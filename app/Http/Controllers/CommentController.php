<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $validated['post_id'],
            'content' => $validated['content'],
            'status' => 'pending', // Default status
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $comment->update($validated);
        $comment->save();

        return response()->json([
            'message' => 'Status updated',
            'comment' => $comment
        ]);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
