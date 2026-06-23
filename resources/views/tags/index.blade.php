@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tags</h1>

    {{-- Inline create form --}}
    <form action="{{ route('tags.store') }}" method="POST"
          class="bg-white rounded shadow p-4 mb-6 flex items-end gap-4 flex-wrap">
        @csrf

        <div>
            <label for="name" class="block text-xs font-medium text-gray-500 mb-1">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="color" class="block text-xs font-medium text-gray-500 mb-1">Color</label>
            <input type="color" name="color" id="color" value="{{ old('color', '#000000') }}"
                   class="h-[38px] w-16 border border-gray-300 rounded cursor-pointer">
            @error('color') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
            Create Tag
        </button>
    </form>

    {{-- Tag list --}}
    @forelse($tags as $tag)
        <div class="bg-white rounded shadow mb-3 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($tag->color)
                    <span class="w-4 h-4 rounded-full inline-block" style="background-color: {{ $tag->color }};"></span>
                @endif
                <span class="font-medium text-gray-800">{{ $tag->name }}</span>
                <span class="text-xs text-gray-500">{{ $tag->issues_count }} {{ Str::plural('issue', $tag->issues_count) }}</span>
            </div>

            <form action="{{ route('tags.destroy', $tag) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Delete this tag?')"
                        class="bg-red-100 text-red-700 px-3 py-1.5 rounded hover:bg-red-200 text-sm">
                    Delete
                </button>
            </form>
        </div>
    @empty
        <p class="text-gray-500">No tags yet.</p>
    @endforelse
@endsection
