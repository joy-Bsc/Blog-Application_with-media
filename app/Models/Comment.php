<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'content',
        'edited_at'
    ];

    protected $casts = [
        'edited_at' => 'datetime',
    ];

    protected $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('replies');
    }

    public function isEdited(): bool
    {
        return !is_null($this->edited_at);
    }

    public function canBeEditedBy($user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeDeletedBy($user): bool
    {
        // Comment author can delete their own comment
        if ($this->user_id === $user->id) {
            return true;
        }
        
        // Post creator can delete any comment on their post
        return $this->post->user_id === $user->id;
    }
}
