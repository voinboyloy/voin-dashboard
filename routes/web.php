<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard, which will trigger auth
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/weekly-load', [DashboardController::class, 'weeklyLoad'])->name('weekly-load');
    Route::get('/review-log', [DashboardController::class, 'reviewLog'])->name('review-log');
    Route::get('/savings-tracker', [DashboardController::class, 'savingsTracker'])->name('savings-tracker');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    Route::get('/workout-planner', [DashboardController::class, 'workoutPlanner'])->name('workout-planner');
    Route::get('/credentials-vault', [DashboardController::class, 'credentialsVault'])->name('credentials-vault');

    Route::prefix('api')->group(function () {
        Route::post('/workout-plans/exercise', [DashboardController::class, 'addExerciseToPlan']);
        Route::post('/load-sample', [DashboardController::class, 'loadSample']);
        Route::post('/tasks', [DashboardController::class, 'storeTask']);
        Route::patch('/tasks/{task}', [DashboardController::class, 'updateTask']);
        Route::delete('/tasks/{task}', [DashboardController::class, 'destroyTask']);
        Route::patch('/tasks/{task}/toggle', [DashboardController::class, 'toggleTask']);
        Route::post('/tasks/{task}/carry-over', [DashboardController::class, 'carryOverTask']);
        Route::post('/blocks', [DashboardController::class, 'storeBlock']);
        Route::delete('/blocks/{block}', [DashboardController::class, 'destroyBlock']);
        Route::post('/reviews', [DashboardController::class, 'saveReview']);

        Route::post('/transactions', [DashboardController::class, 'storeTransaction']);
        Route::delete('/transactions/{transaction}', [DashboardController::class, 'destroyTransaction']);
        Route::post('/wishlist', [DashboardController::class, 'storeWishlist']);
        Route::patch('/wishlist/{item}/toggle', [DashboardController::class, 'toggleWishlist']);
        Route::delete('/wishlist/{item}', [DashboardController::class, 'destroyWishlist']);

        Route::post('/habits', [DashboardController::class, 'storeHabit']);
        Route::patch('/habits/{habit}/toggle', [DashboardController::class, 'toggleHabit']);
        Route::delete('/habits/{habit}', [DashboardController::class, 'destroyHabit']);

        Route::post('/events', [DashboardController::class, 'storeEvent']);
        Route::delete('/events/{event}', [DashboardController::class, 'destroyEvent']);

        Route::post('/workout-plans/toggle-exercise', [DashboardController::class, 'toggleExercise']);

        Route::post('/notes', [DashboardController::class, 'storeNote']);
        Route::delete('/notes/{note}', [DashboardController::class, 'destroyNote']);
        Route::patch('/user/budget', [DashboardController::class, 'updateBudget']);
        Route::post('/subscriptions', [DashboardController::class, 'storeSubscription']);
        Route::delete('/subscriptions/{subscription}', [DashboardController::class, 'destroySubscription']);
        Route::post('/credentials', [DashboardController::class, 'storeCredential']);
        Route::delete('/credentials/{credential}', [DashboardController::class, 'destroyCredential']);
        Route::get('/analytics/data', [DashboardController::class, 'getAnalyticsData']);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
