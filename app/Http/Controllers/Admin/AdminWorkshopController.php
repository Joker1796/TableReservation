<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkshopController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/workshop/Index', [
            'photos' => WorkshopPhoto::latest()->get()->map(fn ($p) => [
                'id' => $p->id,
                'url' => $p->url,
                'original_name' => $p->original_name,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['image', 'max:5120'],
        ]);

        foreach ($request->file('photos') as $file) {
            $filename = basename(Storage::disk('public')->putFile('workshop', $file));

            WorkshopPhoto::create([
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        return redirect()->route('admin.workshop.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $photo = WorkshopPhoto::findOrFail($id);
        Storage::disk('public')->delete('workshop/'.$photo->filename);
        $photo->delete();

        return redirect()->route('admin.workshop.index');
    }
}
