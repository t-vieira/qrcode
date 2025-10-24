<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of folders
     */
    public function index()
    {
        $folders = auth()->user()->folders()
            ->withCount('qrCodes')
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('folders.index', compact('folders'));
    }

    /**
     * Show the form for creating a new folder
     */
    public function create()
    {
        $parentFolders = auth()->user()->folders()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('folders.create', compact('parentFolders'));
    }

    /**
     * Store a newly created folder
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        // Check if parent folder belongs to user
        if ($request->parent_id) {
            $parentFolder = auth()->user()->folders()->find($request->parent_id);
            if (!$parentFolder) {
                return back()->withErrors(['parent_id' => 'Pasta pai não encontrada.']);
            }
        }

        $folder = auth()->user()->folders()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('folders.index')
            ->with('success', 'Pasta criada com sucesso!');
    }

    /**
     * Display the specified folder
     */
    public function show(Folder $folder)
    {
        $this->authorize('view', $folder);

        $qrCodes = $folder->qrCodes()
            ->with('scans')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $subfolders = $folder->children()
            ->withCount('qrCodes')
            ->orderBy('name')
            ->get();

        return view('folders.show', compact('folder', 'qrCodes', 'subfolders'));
    }

    /**
     * Show the form for editing the specified folder
     */
    public function edit(Folder $folder)
    {
        $this->authorize('update', $folder);

        $parentFolders = auth()->user()->folders()
            ->where('id', '!=', $folder->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('folders.edit', compact('folder', 'parentFolders'));
    }

    /**
     * Update the specified folder
     */
    public function update(Request $request, Folder $folder)
    {
        $this->authorize('update', $folder);

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:folders,id',
                Rule::notIn([$folder->id])
            ],
        ]);

        // Check if parent folder belongs to user and is not a child of current folder
        if ($request->parent_id) {
            $parentFolder = auth()->user()->folders()->find($request->parent_id);
            if (!$parentFolder) {
                return back()->withErrors(['parent_id' => 'Pasta pai não encontrada.']);
            }

            // Prevent circular reference
            if ($this->wouldCreateCircularReference($folder, $parentFolder)) {
                return back()->withErrors(['parent_id' => 'Não é possível mover a pasta para dentro de si mesma.']);
            }
        }

        $folder->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('folders.index')
            ->with('success', 'Pasta atualizada com sucesso!');
    }

    /**
     * Remove the specified folder
     */
    public function destroy(Folder $folder)
    {
        $this->authorize('delete', $folder);

        // Move QR codes to parent folder or root
        $newParentId = $folder->parent_id;
        $folder->qrCodes()->update(['folder_id' => $newParentId]);

        // Move subfolders to parent folder
        $folder->children()->update(['parent_id' => $newParentId]);

        $folder->delete();

        return redirect()->route('folders.index')
            ->with('success', 'Pasta excluída com sucesso!');
    }

    /**
     * Get folders for AJAX requests (for dropdowns, etc.)
     */
    public function getFolders(Request $request)
    {
        $folders = auth()->user()->folders()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($folders);
    }

    /**
     * Check if moving folder would create circular reference
     */
    private function wouldCreateCircularReference(Folder $folder, Folder $newParent): bool
    {
        $current = $newParent;
        while ($current) {
            if ($current->id === $folder->id) {
                return true;
            }
            $current = $current->parent;
        }
        return false;
    }
}
