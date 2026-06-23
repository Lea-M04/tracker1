<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $tags = Tag::query()
            ->withCount('issues')
            ->orderBy('name')
            ->paginate(15);

        return view('tags.index', compact('tags'));
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::query()->create($request->validated());

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag created successfully.');
    }
}
