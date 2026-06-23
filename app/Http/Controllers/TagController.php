<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;

class TagController extends Controller
{

    public function index()
    {
        $tags = Tag::withCount('issues')->orderBy('name')->get();
        return view('tags.index', compact('tags'));
    }


    public function store(StoreTagRequest $request)
    {
        Tag::create($request->validated());
        return redirect()->route('tags.index')
                         ->with('success', 'Tag created');
    }


    public function destroy(Tag $tag)
    {
        $tag ->delete();
        return redirect() ->route('tags.index')
                          ->with('success', 'Tag deleted.');
    }
}
