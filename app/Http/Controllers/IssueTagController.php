<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Tag;

class IssueTagController extends Controller
{
    public function store(Issue $issue)
    {
        $validated = request()->validate([
            'tag_id' => 'required|exists:tags,id',
        ]);

        $issue->tags()->syncWithoutDetaching([$validated['tag_id']]);

        return response()->json($issue->tags);
    }

    public function destroy(Issue $issue, Tag $tag)
    {
        $issue->tags()->detach($tag->id);

        return response()->json($issue->tags);
    }
}
