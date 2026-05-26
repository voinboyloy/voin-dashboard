<?php

use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DailyReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimeBlockController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\MonthlyGoalController;
use App\Http\Controllers\JulesController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard, which will trigger auth
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/weekly-load', [DashboardController::class, 'weeklyLoad'])->name('weekly-load');
    Route::get('/review-log', [DashboardController::class, 'reviewLog'])->name('review-log');
    Route::get('/monthly-tasks', [TaskController::class, 'monthlyDashboard'])->name('monthly-tasks');
    Route::get('/savings-tracker', [FinanceController::class, 'index'])->name('savings-tracker');
    Route::get('/calendar', [EventController::class, 'calendar'])->name('calendar');
    Route::get('/workout-planner', [WorkoutController::class, 'index'])->name('workout-planner');
    Route::get('/credentials-vault', [CredentialController::class, 'index'])->name('credentials-vault');
    Route::get('/jules', [JulesController::class, 'index'])->name('jules.index');

    Route::prefix('api')->group(function () {
        Route::post('/load-sample', [DashboardController::class, 'loadSample']);
        
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::patch('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
        Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle']);
        Route::post('/tasks/{task}/carry-over', [TaskController::class, 'carryOver']);
        
        Route::post('/blocks', [TimeBlockController::class, 'store']);
        Route::delete('/blocks/{block}', [TimeBlockController::class, 'destroy']);
        
        Route::post('/reviews', [DailyReviewController::class, 'store']);
        Route::get('/reviews/weekly-summary', [DailyReviewController::class, 'generateWeeklySummary']);

        Route::post('/workout-plans/exercise', [WorkoutController::class, 'addExercise']);
        Route::post('/workout-plans/toggle-exercise', [WorkoutController::class, 'toggleExercise']);

        Route::post('/transactions', [FinanceController::class, 'storeTransaction']);
        Route::delete('/transactions/{transaction}', [FinanceController::class, 'destroyTransaction']);
        Route::patch('/user/budget', [FinanceController::class, 'updateBudget']);
        Route::post('/subscriptions', [FinanceController::class, 'storeSubscription']);
        Route::delete('/subscriptions/{subscription}', [FinanceController::class, 'destroySubscription']);
        Route::get('/analytics/data', [FinanceController::class, 'getAnalyticsData']);
        
        Route::post('/wishlist', [WishlistController::class, 'store']);
        Route::patch('/wishlist/{item}/toggle', [WishlistController::class, 'toggle']);
        Route::delete('/wishlist/{item}', [WishlistController::class, 'destroy']);

        Route::post('/habits', [HabitController::class, 'store']);
        Route::patch('/habits/{habit}/toggle', [HabitController::class, 'toggle']);
        Route::delete('/habits/{habit}', [HabitController::class, 'destroy']);

        Route::post('/events', [EventController::class, 'store']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);

        Route::post('/notes', [NoteController::class, 'store']);
        Route::delete('/notes/{note}', [NoteController::class, 'destroy']);
        
        Route::post('/credentials', [CredentialController::class, 'store']);
        Route::delete('/credentials/{credential}', [CredentialController::class, 'destroy']);

        Route::post('/monthly-goals', [MonthlyGoalController::class, 'store']);
        Route::patch('/monthly-goals/{goal}/toggle', [MonthlyGoalController::class, 'toggle']);
        Route::delete('/monthly-goals/{goal}', [MonthlyGoalController::class, 'destroy']);

        Route::post('/jules/sessions', [JulesController::class, 'createSession']);
        Route::get('/jules/sessions/{id}', [JulesController::class, 'show']);
        Route::post('/jules/sessions/{id}/message', [JulesController::class, 'sendMessage']);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
