<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home/Posts listing page
Route::get('/', [PostController::class, 'index'])->name('posts.index')->middleware('sleep');

// API route for pagination (no sleep for API)
Route::get('/api/posts', [PostController::class, 'apiIndex'])->name('posts.api');

Route::get('/home', function () {
    return Inertia::render('Home');
})->middleware('sleep');

Route::get('/dashboard', function () {
    $userPosts = auth()->user()->posts()
        ->with('user')
        ->withCount(['likes', 'comments'])
        ->latest()
        ->get();
    
    return Inertia::render('Dashboard', [
        'posts' => $userPosts
    ]);
})->middleware(['auth', 'verified', 'sleep'])->name('dashboard');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show')->middleware('sleep');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('sleep');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile', [ProfileController::class, 'update']); // Allow POST for file uploads
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Only logged-in users can create/edit/delete posts
Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create')->middleware('sleep');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store')->middleware('sleep');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit')->middleware('sleep');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update')->middleware('sleep');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy')->middleware('sleep');
    
    // Like and Comment routes (NO SLEEP - instant response)
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Public post routes (must come AFTER /posts/create to avoid conflicts)
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show')->middleware('sleep');

// Public profile route - for viewing other users' profiles
Route::get('/profile/{user}', [ProfileController::class, 'publicShow'])->name('profile.public')->middleware('sleep');

require __DIR__.'/auth.php';
