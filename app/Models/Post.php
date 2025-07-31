<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'images',
        'user_id',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->with('user');
    }

    // Only top-level comments (parent_id is null)
    public function topLevelComments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    // Accessors (used only when needed)
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute(): int
    {
        return $this->comments()->count(); // includes replies if comments relationship is full
    }

    public function getAllCommentsCount(): int
    {
        $mainIds = $this->comments()->whereNull('parent_id')->pluck('id');

        $replyCount = Comment::whereIn('parent_id', $mainIds)->count();

        return $mainIds->count() + $replyCount;
    }

    // Utility: check if a post is liked by given user
    public function isLikedBy($user): bool
    {
        if (!$user) return false;

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    // Scope to include whether the current user liked the post
    public function scopeWithLikedStatus(Builder $query, $userId): Builder
    {
        return $query->withCount([
            'likes as is_liked_by_user' => fn ($q) =>
                $q->where('user_id', $userId),
        ]);
    }
}
