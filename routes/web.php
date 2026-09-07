<?php

use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [IdeaController::class, 'index'])->name('home');

// Dashboard (requires auth)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Idea routes (index is public, rest requires auth for create/edit/delete)
Route::get('/ideas', [IdeaController::class, 'index'])->name('ideas.index');

// Create must be before {idea} wildcard route
Route::middleware('auth')->group(function () {
    Route::get('/ideas/create', [IdeaController::class, 'create'])->name('ideas.create');
});

Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('ideas.show');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Ideas CRUD (store, edit, update, destroy)
    Route::post('/ideas', [IdeaController::class, 'store'])->name('ideas.store');
    Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit'])->name('ideas.edit');
    Route::put('/ideas/{idea}', [IdeaController::class, 'update'])->name('ideas.update');
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('ideas.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Voting
    Route::post('/ideas/{idea}/vote', [VoteController::class, 'store'])->name('ideas.vote');

    // Comments
    Route::post('/ideas/{idea}/comments', [CommentController::class, 'store'])->name('ideas.comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Team positions & applications
    Route::post('/ideas/{idea}/positions', [TeamController::class, 'storePosition'])->name('ideas.positions.store');
    Route::post('/positions/{position}/apply', [TeamController::class, 'apply'])->name('positions.apply');
    Route::patch('/applications/{application}', [TeamController::class, 'updateApplication'])->name('applications.update');

    // Admin routes
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/workflow', [WorkflowController::class, 'index'])->name('workflow.index');
        Route::post('/workflow', [WorkflowController::class, 'store'])->name('workflow.store');
        Route::put('/workflow/{stage}', [WorkflowController::class, 'update'])->name('workflow.update');
        Route::delete('/workflow/{stage}', [WorkflowController::class, 'destroy'])->name('workflow.destroy');
        Route::post('/workflow/advance/{idea}', [WorkflowController::class, 'advanceIdea'])->name('workflow.advance');
    });
});

require __DIR__.'/auth.php';
