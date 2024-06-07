<?php

namespace App\Services;

use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentService
{
    public function store(CommentStoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            return Comment::create([
                'course_id' => $request->course_id,
                'user_id' => $request->user()->id,
                'comment' => $request->comment,
            ]);
        });
    }

    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        return DB::transaction(function () use ($request, $comment) {
            return $comment->update($request->all());
        });
    }

    public function delete(Comment $comment): void
    {
        DB::transaction(function () use ($comment) {
            $comment->delete();
        });
    }
}
