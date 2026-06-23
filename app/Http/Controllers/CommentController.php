<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Http\Requests\StoreCommentRequest;

class CommentController extends Controller
{

    public function index(Issue $issue)
    {
        $comments = $issue->comments()
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Issue $issue)
    {
        $comment = $issue->comments()->create($request->validated());

        return response()->json($comment, 201);
    }
}
