<?php

namespace App\Http\Controllers;

use App\Models\MasterSubMenu;
use Illuminate\Http\Request;

class MasterSubMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:master_menus,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'placeholder' => 'nullable|string|max:255',
        ]);

        MasterSubMenu::create([
            'menu_id' => $request->menu_id,
            'name' => $request->name,
            'description' => $request->description,
            'placeholder' => $request->placeholder,
        ]);

        return redirect()
            ->route('menus.edit', $request->menu_id)
            ->with('success', 'Sub Menu berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show(MasterSubMenu $subMenu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterSubMenu $subMenu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterSubMenu $subMenu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'placeholder' => 'nullable|string|max:255',
        ]);

        $subMenu->update([
            'name' => $request->name,
            'description' => $request->description,
            'placeholder' => $request->placeholder,
        ]);

        return redirect()
            ->route('menus.edit', $subMenu->menu_id)
            ->with('success', 'Sub Menu berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $submenu = MasterSubMenu::find($id);

        if (!$submenu) {
            return response()->json(['error' => 'Sub Menu tidak ditemukan'], 404);
        }

        $submenu->delete();

        return redirect()
            ->route('menus.edit', $submenu->menu_id)
            ->with('success', 'Sub Menu berhasil dihapus.');
    }
}
