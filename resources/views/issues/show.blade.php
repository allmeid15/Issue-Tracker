@extends('layouts.app')

@section('title', $issue->title)

@push('meta')
    <meta name="issue-id" content="{{ $issue->id }}">
@endpush

@section('content')
    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <h1 class="text-2xl font-bold text-gray-800">{{ $issue->title }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.issues.edit', [$project, $issue]) }}"
                   class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-300 text-sm">
                    Edit
                </a>
                <a href="{{ route('projects.issues.index', $project) }}"
                   class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-300 text-sm">
                    Back
                </a>
                <form action="{{ route('projects.issues.destroy', [$project, $issue]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this issue?')"
                            class="bg-red-100 text-red-700 px-3 py-1.5 rounded hover:bg-red-200 text-sm">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <p class="text-gray-600 mt-3">{{ $issue->description }}</p>

        <div class="flex items-center gap-3 mt-4">
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
            @if($issue->due_date)
                <span class="text-xs text-gray-500">Due: {{ $issue->due_date->format('M d, Y') }}</span>
            @endif
        </div>

                <div class="mt-4">
            <div class="flex items-center gap-2 mb-2">
                <h3 class="text-sm font-medium text-gray-700">Tags</h3>
                <button id="toggle-tag-form"
                        class="text-xs text-blue-600 hover:underline">
                    + Manage Tags
                </button>
            </div>

            {{-- Tag manage form (hidden by default) --}}
            <div id="tag-form" class="hidden bg-gray-50 rounded p-3 mb-3">
                <select id="tag-select"
                        class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a tag...</option>
                </select>
                <button id="attach-tag"
                        class="bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 text-sm ml-1">
                    Attach
                </button>
            </div>

            <div id="tags-list" class="flex flex-wrap gap-1">
                @forelse($issue->tags as $tag)
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full inline-flex items-center gap-1"
                          data-tag-id="{{ $tag->id }}">
                        {{ $tag->name }}
                        <button class="detach-tag text-gray-400 hover:text-red-600 ml-0.5">&times;</button>
                    </span>
                @empty
                    <span class="text-xs text-gray-400 no-tags">No tags</span>
                @endforelse
            </div>
        </div>


        {{-- Comments section --}}
       <h2 class="text-xl font-semibold text-gray-800 mb-4">Comments</h2>

    <div class="bg-white rounded shadow p-4 mb-4">
        <form id="comment-form" class="space-y-3">
            <div>
                <label for="author_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="author_name" id="author_name"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-red-600 text-sm mt-1 hidden" data-error="author_name"></p>
            </div>
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                <textarea name="body" id="body" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <p class="text-red-600 text-sm mt-1 hidden" data-error="body"></p>
            </div>
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Add Comment
            </button>
        </form>
    </div>

    <div id="comments-list"></div>

    <div id="comments-pagination" class="mt-4 text-center"></div>

    @push('scripts')
    <script>
    window.allTags = @json(\App\Models\Tag::orderBy('name')->get(['id', 'name']));
    </script>
@endpush


@endsection
