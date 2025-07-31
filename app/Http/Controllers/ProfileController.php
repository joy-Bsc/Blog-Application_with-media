<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();
        
        // Add posts count
        $user->posts_count = $user->posts()->count();
        $user->published_posts_count = $user->posts()->count(); // Assuming all posts are published for now
        
        return Inertia::render('Profile/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image && \Storage::disk('public')->exists($user->profile_image)) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            
            // Store new image
            $imagePath = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image'] = $imagePath;
        }
        
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display a public user profile.
     */
    public function publicShow(Request $request, $userId): Response
    {
        $user = \App\Models\User::findOrFail($userId);
        
        // Get user's public posts with likes and comments count
        $posts = $user->posts()
            ->withCount(['likes', 'comments'])
            ->with(['user'])
            ->latest()
            ->get()
            ->map(function ($post) {
                $post->is_liked_by_user = false; // Default for non-authenticated users
                
                if (auth()->check()) {
                    $post->is_liked_by_user = $post->likes()
                        ->where('user_id', auth()->id())
                        ->exists();
                }
                
                return $post;
            });
        
        // Get user stats
        $user->posts_count = $user->posts()->count();
        $user->total_likes = $user->posts()->withCount('likes')->get()->sum('likes_count');
        
        return Inertia::render('Profile/PublicShow', [
            'profileUser' => $user,
            'posts' => $posts,
            'isOwnProfile' => auth()->check() && auth()->id() === (int)$userId,
        ]);
    }
}
