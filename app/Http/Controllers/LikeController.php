<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LikeController extends Controller
{
    public function toggle(Request $request, Post $post): RedirectResponse
    {
        $user = $request->user();
        
        $existingLike = Like::where('user_id', $user->id)
                           ->where('post_id', $post->id)
                           ->first();
        
        if ($existingLike) {
            // Unlike the post
            $existingLike->delete();
            $message = 'Post unliked successfully.';
        } else {
            // Like the post
            Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $message = 'Post liked successfully.';
        }
        
        return back()->with('success', $message);
    }
}
