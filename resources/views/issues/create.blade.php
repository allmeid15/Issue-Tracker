@extends('layouts.app')

@section('title', 'New Issue')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">New Issue for {{ $project->name }}</h1>

        <form action="{{ route('projects.issues.store', $project) }}" method="POST"
              class="bg-white rounded shadow p-6 space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(['open', 'in_progress', 'closed'] as $status)
                            <option value="{{ $status }}" @selected(old('status') === $status)>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select name="priority" id="priority"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(['low', 'medium', 'high'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority') === $priority)>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('due_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                <div class="flex flex-wrap gap-3">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-1.5 text-sm text-gray-700">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   class="rounded border-gray-300"
                                   @checked(is_array(old('tags')) && in_array($tag->id, old('tags')))>
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
                @error('tags') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 text-sm">
                Create Issue
            </button>
        </form>
    </div>
@endsection
