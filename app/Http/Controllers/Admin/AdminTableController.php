<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Services\TableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminTableController extends Controller
{
    public function index(): Response
    {
        $tables = Table::latest()->paginate(10);

        return Inertia::render('admin/tables/Index', [
            'tables' => $tables,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/tables/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        TableService::create($request);

        return redirect()->route('admin.tables.index');
    }

    public function edit(int $id): Response
    {
        $table = Table::findOrFail($id);

        return Inertia::render('admin/tables/Edit', [
            'table' => $table,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $table = Table::findOrFail($id);
        TableService::update($request, $table);

        return redirect()->route('admin.tables.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $table = Table::findOrFail($id);
        TableService::softDelete($table);

        return redirect()->route('admin.tables.index');
    }
}
