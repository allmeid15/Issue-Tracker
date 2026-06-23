<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Issue;
use App\Models\Tag;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $issues = $project ->issues()
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('priority'), function($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when(request('tag'), function($query, $tagId) {
                $query->whereHas('tags', function ($q) use ($tagId) {
                    $q->where('tags.id', $tagId);
                });
            })
            ->with('tags')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        $tags = Tag::orderBy('name')->get();
        return view('issues.index', compact('project', 'issues', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $tags = Tag::orderBy('name')->get();
        return view('issues.create', compact('project', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIssueRequest $request, Project $project)
    {
        $issue = $project->issues()->create($request->validated());

        if($request->validated('tags')) {
            $issue->tags()->sync($request->validated('tags'));
        }

        return redirect()->route('projects.issues.show', [$project, $issue])
                         ->with('success', 'Issue created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Issue $issue)
    {
        $issue->load('tags');
        return view('issues.show', compact('project', 'issue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, Issue $issue)
    {
        $tags = Tag::orderBy('name')->get();
        $issue->load('tags');
        return view('issues.edit', compact('project', 'issue', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIssueRequest $request, Project $project, Issue $issue)
    {
        $issue->update($request->validated());

        if($request->has('tags')) {
            $issue->tags()->sync($request->validated('tags') ?? []);
        }

        return redirect()->route('projects.issues.show', [$project, $issue])
                         ->with('success', 'Issue updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Issue $issue)
    {
        $issue->delete();
        return redirect() ->route('projects.issues.index', $project)
                          ->with('success', 'Issue deleted.');   
    }
}
