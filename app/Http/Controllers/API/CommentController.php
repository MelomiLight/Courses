<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Course;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    private CommentService $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function store(CommentStoreRequest $request): JsonResponse
    {
        try {
            $comment = $this->service->store($request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => __('messages.create.success', ['attribute' => 'comment']),
            'data' => new CommentResource($comment),
        ]);
    }

    public function index(Course $course): AnonymousResourceCollection
    {
        $comments = Comment::where('course_id', $course->id)->get();

        return CommentResource::collection($comments);
    }

    public function update(CommentUpdateRequest $request, Comment $comment): JsonResponse
    {
        try {
            $this->service->update($request, $comment);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


        return response()->json([
            'message' => __('messages.update.success', ['attribute' => 'comment']),
            'data' => new CommentResource($comment),
        ]);
    }

    public function delete(Comment $comment): JsonResponse
    {
        try {
            $this->service->delete($comment);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => __('messages.delete.success', ['attribute' => 'comment'])]);
    }
}
