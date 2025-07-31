<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PostController extends Controller
{
    // Display paginated list of posts
    public function index(Request $request)
    {
        $query = Post::with('user:id,name')
            ->withCount(['likes', 'comments'])
            ->when(auth()->check(), fn ($q) =>
                $q->withLikedStatus(auth()->id())
            );

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) =>
                      $uq->where('name', 'like', "%{$search}%")
                  );
            });
        }

        $posts = $query->latest()->paginate(5);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
            'search' => $search
        ]);
    }

    // Show a single post (detail view)
    public function show(Post $post)
    {
        $start = microtime(true);

        $post = Cache::remember("post_{$post->id}", 60, function () use ($post) {
            return $post->load([
                'user:id,name',
                'comments' => fn ($q) =>
                    $q->whereNull('parent_id')
                      ->with(['user:id,name', 'replies.user:id,name']),
            ])->loadCount(['likes', 'comments']);
        });

        // Add dynamic like status (not cached because it's per-user)
        $post->is_liked_by_user = auth()->check()
            ? $post->likes()->where('user_id', auth()->id())->exists()
            : false;

        // Add total count of main comments + replies
        $post->comments_count = $post->comments->count() +
            $post->comments->flatMap->replies->count();

        \Log::info("Post load time: " . number_format(microtime(true) - $start, 3) . "s");

        return Inertia::render('Posts/Show', [
            'post' => $post
        ]);
    }

    // Create view
    public function create()
    {
        return Inertia::render('Posts/Create');
    }

    // Store a new post
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
            'images'  => 'nullable|array|max:10',
            'images.*'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
        ]);

        $data = $request->only('title', 'content');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        if ($request->hasFile('images')) {
            $data['images'] = array_map(
                fn ($img) => $img->store('posts', 'public'),
                $request->file('images')
            );
        }

        $request->user()->posts()->create($data);

        return redirect()->route('posts.index');
    }

    // Edit view
    public function edit(Post $post)
    {
        return Inertia::render('Posts/Edit', [
            'post' => $post
        ]);
    }

    // Update an existing post
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
            'images'  => 'nullable|array|max:10',
            'images.*'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
        ]);

        $data = $request->only('title', 'content');

        if ($request->hasFile('image')) {
            if ($post->image) {
                \Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        if ($request->hasFile('images')) {
            if ($post->images) {
                foreach ($post->images as $img) {
                    \Storage::disk('public')->delete($img);
                }
            }

            $data['images'] = array_map(
                fn ($img) => $img->store('posts', 'public'),
                $request->file('images')
            );
        }

        $post->update($data);

        return redirect()->route('posts.index');
    }

    // Delete post
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index');
    }

    // Optional API endpoint (for dynamic frontend use)
    public function apiIndex(Request $request)
    {
        $posts = Post::with('user:id,name')
            ->latest()
            ->paginate(5);

        return response()->json([
            'posts' => $posts
        ]);
    }
}
