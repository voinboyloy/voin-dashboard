<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\WishlistItem;
use App\Models\Habit;
use Illuminate\Http\Request;
use App\Services\NotionSyncService;

class FinanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)->orderBy('date', 'desc')->get();
        $income = $transactions->where('type', 'income')->sum('amount');
        $expenses = $transactions->where('type', 'expense')->sum('amount');
        $savings = $income - $expenses;

        $wishlist = WishlistItem::where('user_id', $user->id)->orderBy('is_bought')->get();
        $subscriptions = $user->subscriptions;

        return view('savings-tracker', compact('user', 'transactions', 'income', 'expenses', 'savings', 'wishlist', 'subscriptions'));
    }

    public function storeTransaction(Request $request)
    {
        $user = auth()->user();
        $tx = Transaction::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'type' => $request->type,
                'amount' => $request->amount,
                'category' => $request->category,
                'description' => $request->description,
                'date' => $request->date ?? now()->toDateString(),
            ]
        );

        app(NotionSyncService::class)->syncTransaction($tx);

        return response()->json($tx);
    }

    public function destroyTransaction(Transaction $transaction)
    {
        app(NotionSyncService::class)->deleteEntity($transaction);
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }

    public function updateBudget(Request $request)
    {
        $user = auth()->user();
        $user->update(['monthly_budget' => $request->monthly_budget]);
        return response()->json(['message' => 'Budget updated']);
    }

    public function storeSubscription(Request $request)
    {
        $user = auth()->user();
        $sub = Subscription::updateOrCreate(
            ['id' => $request->id, 'user_id' => $user->id],
            [
                'title' => $request->title,
                'amount' => $request->amount,
                'billing_cycle' => $request->billing_cycle,
                'next_billing_date' => $request->next_billing_date,
            ]
        );

        app(NotionSyncService::class)->syncSubscription($sub);

        return response()->json($sub);
    }

    public function destroySubscription(Subscription $subscription)
    {
        app(NotionSyncService::class)->deleteEntity($subscription);
        $subscription->delete();
        return response()->json(['message' => 'Subscription deleted']);
    }

    public function getAnalyticsData()
    {
        $user = auth()->user();

        // Savings Chart Data
        $txs = Transaction::where('user_id', $user->id)->orderBy('date')->get();
        $savingsLabels = $txs->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray();
        $incomeData = $txs->where('type', 'income')->pluck('amount')->toArray();
        $expenseData = $txs->where('type', 'expense')->pluck('amount')->toArray();

        // Habit Streaks Data
        $habits = Habit::with('completions')->where('user_id', $user->id)->get();
        $streakData = $habits->map(fn($h) => [
            'name' => $h->title,
            'streak' => $h->calculateStreak()
        ]);

        return response()->json([
            'savings' => ['labels' => $savingsLabels, 'income' => $incomeData, 'expenses' => $expenseData],
            'streaks' => $streakData
        ]);
    }
}
