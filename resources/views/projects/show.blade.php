@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $project->name }}</h1>
                <p class="text-gray-600 mt-2">{{ $project->description }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.edit', $project) }}"
                   class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-300 text-sm">
                    Edit
                </a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this project?')"
                            class="bg-red-100 text-red-700 px-3 py-1.5 rounded hover:bg-red-200 text-sm">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="flex gap-4 mt-4 text-sm text-gray-500">
            @if($project->start_date)
                <span>Started: {{ $project->start_date->format('M d, Y') }}</span>
            @endif
            @if($project->deadline)
                <span>Deadline: {{ $project->deadline->format('M d, Y') }}</span>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Issues</h2>
        <a href="{{ route('projects.issues.create', $project) }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            New Issue
        </a>
    </div>

    @forelse($project->issues as $issue)
        <div class="bg-white rounded shadow mb-3 p-4 flex items-center justify-between">
            <a href="{{ route('projects.issues.show', [$project, $issue]) }}" class="text-blue-600 hover:underline font-medium">
                {{ $issue->title }}
            </a>
            <div class="flex items-center gap-2">
                <span class="text-xs px-2 py-1 rounded-full
                    @if($issue->status === 'open') bg-yellow-100 text-yellow-800
                    @elseif($issue->status === 'in_progress') bg-blue-100 text-blue-800
                    @else bg-green-100 text-green-800 @endif">
                    {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                </span>
                <span class="text-xs px-2 py-1 rounded-full
                    @if($issue->priority === 'high') bg-red-100 text-red-800
                    @elseif($issue->priority === 'medium') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($issue->priority) }}
                </span>
            </div>
        </div>
    @empty
        <p class="text-gray-500">No issues yet.</p>
    @endforelse
@endsection
