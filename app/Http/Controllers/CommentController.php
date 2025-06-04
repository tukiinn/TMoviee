<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        try {
            $movie = Movie::where('slug', $slug)->firstOrFail();
            $userId = Auth::id();
            $comments = $movie->comments()
                ->whereNull('parent_id')
                ->with(['user:id,name,avatar',
                        'replies.user:id,name,avatar',
                        'replies.likes',
                        'likes'])
                ->latest()
                ->paginate(10);

            // Bổ sung số like và trạng thái đã like
            $comments->getCollection()->transform(function ($comment) use ($userId) {
                $comment->likes_count = $comment->likes->count();
                $comment->liked = $userId ? $comment->likes->contains('user_id', $userId) : false;
                // Replies
                $comment->replies->transform(function ($reply) use ($userId) {
                    $reply->likes_count = $reply->likes->count();
                    $reply->liked = $userId ? $reply->likes->contains('user_id', $userId) : false;
                    return $reply;
                });
                return $comment;
            });

            return response()->json($comments);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tải bình luận: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tải bình luận'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $slug)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
                'parent_id' => 'nullable|exists:comments,id'
            ]);

            $movie = Movie::where('slug', $slug)->firstOrFail();
            $comment = $movie->comments()->create([
                'user_id' => Auth::id(),
                'content' => $request->content,
                'is_spoiler' => $request->input('is_spoiler', false),
                'parent_id' => $request->input('parent_id')
            ]);

            $comment->load('user:id,name,avatar');

            return response()->json($comment, 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo bình luận: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tạo bình luận'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        try {
            if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
                return response()->json(['message' => 'Bạn không có quyền xóa bình luận này'], 403);
            }

            $comment->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa bình luận: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa bình luận'], 500);
        }
    }

    public function like(Comment $comment)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }
        if (!$comment->likes()->where('user_id', $user->id)->exists()) {
            $comment->likes()->create(['user_id' => $user->id]);
        }
        return response()->json(['message' => 'Đã like']);
    }

    public function unlike(Comment $comment)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }
        $comment->likes()->where('user_id', $user->id)->delete();
        return response()->json(['message' => 'Đã bỏ like']);
    }
}
