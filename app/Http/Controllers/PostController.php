<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all()->sortByDesc(function ($post) {
            return $post->score;
        })->values();

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        //$this->authorize('admin');

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/posts'), $imageName);
            $imageUrl = url('uploads/posts/' . $imageName);
        }

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $imageUrl,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Post created successfully.',
            'post' => $post
        ], 201);
    }

    public function update(Request $request, $id)
    {
        //$this->authorize('admin');

        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image_url) {
                $oldPath = public_path(parse_url($post->image_url, PHP_URL_PATH));
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/posts'), $imageName);
            $post->image_url = url('uploads/posts/' . $imageName);
        }

        $post->update($request->only(['title', 'description']));

        return response()->json([
            'message' => 'Post updated successfully.',
            'post' => $post
        ]);
    }

    public function destroy($id)
    {
        // $this->authorize('admin');

        $post = Post::findOrFail($id);

        if ($post->image_url) {
            $oldPath = public_path(parse_url($post->image_url, PHP_URL_PATH));
            if (file_exists($oldPath)) unlink($oldPath);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.'
        ]);
    }

    public function like($id)
    {
        $post = Post::findOrFail($id);

        PostLike::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => Auth::id()],
            ['is_like' => true]
        );

        $post->likes_count = PostLike::where('post_id', $post->id)->where('is_like', true)->count();
        $post->save();

        return response()->json([
            'message' => 'You liked this post.',
            'likes_count' => $post->likes_count
        ]);
    }

    public function dislike($id)
    {
        $post = Post::findOrFail($id);

        PostLike::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => Auth::id()],
            ['is_like' => false]
        );

        $post->likes_count = PostLike::where('post_id', $post->id)->where('is_like', true)->count();
        $post->save();

        return response()->json([
            'message' => 'You disliked this post.',
            'likes_count' => $post->likes_count
        ]);
    }
}
