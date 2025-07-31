<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);
        
        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);
        
        return back()->with('success', 'Comment added successfully.');
    }
    
    public function update(Request $request, Comment $comment): RedirectResponse
    {
        // Check if the user owns the comment
        if (!$comment->canBeEditedBy($request->user())) {
            return back()->with('error', 'You can only edit your own comments.');
        }
        
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $comment->update([
            'content' => $request->content,
            'edited_at' => now(),
        ]);
        
        return back()->with('success', 'Comment updated successfully.');
    }
    
    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        // Check if the user owns the comment
        if (!$comment->canBeDeletedBy($request->user())) {
            return back()->with('error', 'You can only delete your own comments.');
        }
        
        $comment->delete();
        
        return back()->with('success', 'Comment deleted successfully.');
    }
}
