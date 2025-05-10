<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{

   public function index(Request $request)
    {
        $posts = Post::where('status', 'published')->get();
        return response()->json($posts);
    }

    public function show($id)
    {
        $post = Post::with('comments')->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post);
    }

     public function filterByCategory(Request $request)
    {
        if ($request->has('category')) {
            $posts = Post::whereHas('categories', function ($query) use ($request) {
                $query->where('name', $request->category);
            })->get();

            return response()->json($posts);
        }

        return response()->json(['message' => 'Category parameter is required'], 400);
    }


    public function store(Request $request)
    {
        try {
            // Logic to create a new post
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'in:draft,published'
            ]);

            $path = $request->hasFile('featured_image')
                ? $request->file('featured_image')->store('posts', 'public')
                : null;
            $post = Post::create([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'featured_image' => $path,
                'status' => $validatedData['status'] ?? 'draft',
                'user_id' => auth()->id()
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
                'status' => 'sometimes|in:draft,published'
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
