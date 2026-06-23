@extends('layouts.app')

@section('title', 'Issues - ' . $project->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Issues for {{ $project->name }}</h1>
        <a href="{{ route('projects.issues.create', $project) }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            New Issue
        </a>
    </div>

    <div class="mb-4">
        <input type="text" id="issue-search" placeholder="Search issues by title or description..."
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div id="search-results" class="hidden mt-3"></div>
    </div>
    
    <form action="{{ route('projects.issues.index', $project) }}" method="GET"
          class="bg-white rounded shadow p-4 mb-6 flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status"
                    class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                @foreach(['open', 'in_progress', 'closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Priority</label>
            <select name="priority"
                    class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Priorities</option>
                @foreach(['low', 'medium', 'high'] as $priority)
                    <option value="{{ $priority }}" @selected(request('priority') === $priority)>
                        {{ ucfirst($priority) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tag</label>
            <select name="tag"
                    class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Tags</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(request('tag') == $tag->id)>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Filter
            </button>
            <a href="{{ route('projects.issues.index', $project) }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                Clear
            </a>
        </div>
    </form>

    
    @forelse($issues as $issue)
        <div class="bg-white rounded shadow mb-3 p-4 flex items-center justify-between">
            <div>
                <a href="{{ route('projects.issues.show', [$project, $issue]) }}"
                   class="text-blue-600 hover:underline font-medium">
                    {{ $issue->title }}
                </a>
                <div class="flex gap-1 mt-1">
                    @foreach($issue->tags as $tag)
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
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
        <p class="text-gray-500">No issues found.</p>
    @endforelse

    <div class="mt-4">
        {{ $issues->links() }}
    </div>
@endsection
