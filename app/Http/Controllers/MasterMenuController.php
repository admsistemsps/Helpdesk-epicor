<?php

namespace App\Http\Controllers;

use App\Models\MasterMenu;
use Illuminate\Http\Request;
use App\Models\MasterRole;
use App\Models\TicketApprovalRule;
use App\Models\MasterSubMenu;
use App\Models\MasterPosition;
use App\Models\MasterDivision;
use App\Models\MasterDepartment;
use Illuminate\Support\Facades\DB;

class MasterMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = MasterMenu::with(['divisions', 'departments', 'subMenus'])->get();
        $subMenus = MasterSubMenu::all();
        $divisions = MasterDivision::all();

        return view('master.menu.index', compact('menus', 'subMenus', 'divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = MasterDivision::all();
        $departments = MasterDepartment::all();

        return view('master.menu.create', compact('divisions', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'division_ids' => 'nullable|array',
            'division_ids.*' => 'exists:master_divisions,id',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:master_departments,id',
            'sub_menus' => 'nullable|array',
            'sub_menus.*.name' => 'required|string|max:255',
            'sub_menus.*.description' => 'nullable|string|max:500',
            'sub_menus.*.placeholder' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $menu = MasterMenu::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Simpan relasi divisions dan departments
            if (!empty($validated['division_ids'])) {
                $menu->divisions()->sync($validated['division_ids']);
            }

            if (!empty($validated['department_ids'])) {
                $menu->departments()->sync($validated['department_ids']);
            }

            // Simpan sub menus
            if (!empty($validated['sub_menus'])) {
                foreach ($validated['sub_menus'] as $subMenuData) {
                    if (!empty($subMenuData['name'])) {
                        MasterSubMenu::create([
                            'menu_id' => $menu->id,
                            'name' => $subMenuData['name'],
                            'description' => $subMenuData['description'] ?? null,
                            'placeholder' => $subMenuData['placeholder'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('menus.index')
                ->with('success', 'Menu berhasil disimpan dengan relasi divisi dan departemen.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan menu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterMenu $menu)
    {
        $menu->load(['divisions', 'departments', 'subMenus', 'approvalRules']);

        return view('master.menu.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterMenu $menu)
    {
        $menu->load(['divisions', 'departments', 'subMenus']);
        $divisions = MasterDivision::all();
        $departments = MasterDepartment::all();

        return view('master.menu.edit', compact('menu', 'divisions', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterMenu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'division_ids' => 'nullable|array',
            'division_ids.*' => 'exists:master_divisions,id',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:master_departments,id',
        ]);

        DB::beginTransaction();

        try {
            $menu->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Update relasi
            $menu->divisions()->sync($validated['division_ids'] ?? []);
            $menu->departments()->sync($validated['department_ids'] ?? []);

            DB::commit();

            return redirect()
                ->route('menus.index')
                ->with('success', 'Menu berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui menu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterMenu $menu)
    {
        DB::beginTransaction();

        try {
            // Hapus relasi
            $menu->divisions()->detach();
            $menu->departments()->detach();

            // Hapus sub menus (cascade delete jika sudah diatur di migration)
            $menu->subMenus()->delete();

            // Hapus approval rules
            $menu->approvalRules()->delete();

            // Hapus menu
            $menu->delete();

            DB::commit();

            return redirect()
                ->route('menus.index')
                ->with('success', 'Menu, sub menu, dan approval berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus menu: ' . $e->getMessage());
        }
    }

    /**
     * Show approval rules setting page
     */
    public function setting($id)
    {
        $menu = MasterMenu::with(['approvalRules.role', 'subMenus'])->findOrFail($id);
        $roles = MasterRole::all();
        $divisions = MasterDivision::all();
        $positions = MasterPosition::orderBy('level')->get();

        return view('master.menu.setting', compact('menu', 'roles', 'divisions', 'positions'));
    }

    /**
     * Save approval rules setup
     */
    public function setup(Request $request, MasterMenu $menu)
    {
        $validated = $request->validate([
            'rules' => 'required|array',
            'rules.*.sub_menu_id' => 'nullable|exists:master_sub_menus,id',
            'rules.*.division_id' => 'nullable|exists:master_divisions,id',
            'rules.*.position_id' => 'required|exists:master_positions,id',
            'rules.*.is_mandatory' => 'boolean',
            'rules.*.is_final' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Ambil semua level posisi
            $positions = MasterPosition::pluck('level', 'id')->toArray();

            // Group rules berdasarkan sub_menu_id
            $groupedBySubmenu = collect($validated['rules'])->groupBy('sub_menu_id');

            foreach ($groupedBySubmenu as $subMenuId => $rules) {
                // Urutkan berdasarkan level posisi
                $sorted = $rules->sortBy(fn($r) => $positions[$r['position_id']] ?? 999);

                // Ambil urutan terakhir sequence di DB untuk submenu ini
                $lastSequence = TicketApprovalRule::where('menu_id', $menu->id)
                    ->where('sub_menu_id', $subMenuId)
                    ->max('sequence');

                // Simpan urutan terakhir per level jika ada
                $existingSequences = [];
                if ($lastSequence) {
                    $parts = explode('.', $lastSequence);
                    if (count($parts) === 2) {
                        [$lastLevel, $lastDecimal] = $parts;
                        $existingSequences[$lastLevel] = (int) $lastDecimal;
                    }
                }

                $counters = []; // counter urutan per level

                foreach ($sorted as $rule) {
                    $level = $positions[$rule['position_id']] ?? 999;

                    // Tentukan nilai awal counter
                    if (!isset($counters[$level])) {
                        $counters[$level] = isset($existingSequences[$level])
                            ? $existingSequences[$level] + 10
                            : 10;
                    } else {
                        $counters[$level] += 10;
                    }

                    // Format angka sequence
                    $sequence = number_format($level + ($counters[$level] / 100), 2, '.', '');

                    // Simpan atau update data rule
                    TicketApprovalRule::updateOrCreate(
                        [
                            'menu_id' => $menu->id,
                            'sub_menu_id' => $subMenuId ?: null,
                            'division_id' => $rule['division_id'] ?? null,
                            'position_id' => $rule['position_id'],
                        ],
                        [
                            'level' => $level,
                            'sequence' => $sequence,
                            'is_mandatory' => $rule['is_mandatory'] ?? false,
                            'is_final' => $rule['is_final'] ?? false,
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()
                ->route('menus.index')
                ->with('success', 'Approval rule berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui approval rule: ' . $e->getMessage());
        }
    }
}
