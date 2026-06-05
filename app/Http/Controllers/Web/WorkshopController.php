<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WorkshopPhoto;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Workshop', [
            'photos' => WorkshopPhoto::latest()->get()->map(fn ($p) => [
                'id' => $p->id,
                'url' => $p->url,
                'original_name' => $p->original_name,
            ]),
        ]);
    }
}
