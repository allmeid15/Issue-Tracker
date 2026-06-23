<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\User;

class IssueUserController extends Controller
{
    public function store(Issue $issue)
    {
        $validated = request()->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $issue->users()->syncWithoutDetaching([$validated['user_id']]);

        return response()->json($issue->users);
    }

    public function destroy(Issue $issue, User $user)
    {
        $issue->users()->detach($user->id);

        return response()->json($issue->users);
    }
}
