<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{

   public function index(Request $request)
    {
        $posts = Post::where('status', 'published')->with('user')->get();
        return response()->json($posts);
    }

    public function show($id)
    {
          $post = Post::with('user', 'comments.user')->findOrFail($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post);
    }



    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'in:draft,published',
                'categories' => 'nullable|array',
                'tags' => 'nullable|array',
            ]);

            $path = $request->hasFile('featured_image')
                ? $request->file('featured_image')->store('posts', 'public')
                : null;
            $post = Post::create([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'featured_image' => $path,
                'status' => $validatedData['status'] ?? 'draft',
                'categories' => $validated['categories'] ?? [],
                'tags' => $validated['tags'] ?? [],
                 'user_id' => $request->user()->id,
            ]);
            return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'sometimes|in:draft,published',
                'categories' => 'nullable|array',
                'tags' => 'nullable|array',
            ]);


            if ($request->hasFile('featured_image')) {
                $path = $request->file('featured_image')->store('posts', 'public');
                $validatedData['featured_image'] = $path;
            }
            $post = Post::findOrFail($id);

            $post->update([
                'title' => $validatedData['title'] ?? $post->title,
                'content' => $validatedData['content'] ?? $post->content,
                'status' => $validatedData['status'] ?? $post->status,
                'featured_image' => $validatedData['featured_image'] ?? $post->featured_image,
                'categories' => $validatedData['categories'] ?? $post->categories,
                'tags' => $validatedData['tags'] ?? $post->tags,
            ]);

            return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            // Logic to delete a post
            $post = Post::findOrFail($id);
            $post->delete();

            return response()->json(['message' => 'Post deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
