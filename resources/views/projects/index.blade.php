@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Projects</h1>
        <a href="{{ route('projects.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            New Project
        </a>
    </div>

    @forelse($projects as $project)
        <div class="bg-white rounded shadow mb-4 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold">
                        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:underline">
                            {{ $project->name }}
                        </a>
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">{{ Str::limit($project->description, 100) }}</p>
                </div>
                <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full whitespace-nowrap">
                    {{ $project->issues_count }} {{ Str::plural('issue', $project->issues_count) }}
                </span>
            </div>

            @if($project->deadline)
                <div class="mt-3 text-xs text-gray-500">
                    Deadline: {{ $project->deadline->format('M d, Y') }}
                </div>
            @endif
        </div>
    @empty
        <p class="text-gray-500">No projects yet.</p>
    @endforelse

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
@endsection
