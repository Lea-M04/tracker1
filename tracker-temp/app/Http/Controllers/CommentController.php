<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Issue $issue): JsonResponse
    {
        $this->authorize('view', $issue);

        $comments = $issue->comments()
            ->latest()
            ->paginate(10);

        return response()->json([
            'comments' => $comments->getCollection()
                ->map(fn (Comment $comment) => $this->formatComment($comment))
                ->values(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'next_page_url' => $comments->nextPageUrl(),
                'prev_page_url' => $comments->previousPageUrl(),
                'total' => $comments->total(),
            ],
        ]);
    }

    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('view', $issue);

        $comment = $issue->comments()->create($request->validated());

        return response()->json([
            'message' => 'Comment created successfully.',
            'comment' => $this->formatComment($comment),
        ], 201);
    }

    private function formatComment(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'author_name' => $comment->author_name,
            'body' => $comment->body,
            'created_at' => $comment->created_at->format('M d, Y H:i'),
            'created_at_human' => $comment->created_at->diffForHumans(),
        ];
    }
}
