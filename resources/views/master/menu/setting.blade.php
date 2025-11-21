<x-app-layout>
    <main class="flex-1 p-6">
        <x-breadcrumb />

        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Setting Menu Approval</h2>
        </div>

        <form action="{{ route('menus.setup', $menu->id) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            <!-- Menu Info -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Menu</label>
                <input type="text" value="{{ $menu->name }}" readonly
                    class="w-full py-2 px-3 rounded bg-gray-200 border-0 text-gray-700">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <input type="text" value="{{ $menu->description }}" readonly
                    class="w-full py-2 px-3 rounded bg-gray-200 border-0 text-gray-700">
            </div>

            <!-- Approval Rule -->
            <div x-data="approvalHandler({{ $menu->approvalRules->map(fn($r) => [
                'id' => $r->id,
                'level' => $r->level,
                'role_name' => $r->role->name ?? '-',
                'sub_menu_id' => $r->sub_menu_id,
                'sub_menu_name' => $r->subMenu->name ?? '-',
                'division_id' => $r->division_id,
                'division_name' => $r->division->name ?? '-',
                'position_id' => $r->position_id,
                'position_name' => $r->position->name ?? '-',
                'is_mandatory' => $r->is_mandatory,
                'is_final' => $r->is_final,
            ])->toJson() }})" class="mb-6">

                <h3 class="font-semibold text-lg mb-2">Approval Rule (Opsional)</h3>

                <table class="min-w-full text-sm mb-4 border border-gray-300">
                    <thead class="bg-purple-800 text-white">
                        <tr>
                            <th class="px-3 py-2">Sub Menu</th>
                            <th class="px-3 py-2">Level</th>
                            <th class="px-3 py-2">Posisi</th>
                            <th class="px-3 py-2">Divisi</th>
                            <th class="px-3 py-2">Mandatory</th>
                            <th class="px-3 py-2">Final</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(rule, index) in rules" :key="index">
                            <tr class="border-t">
                                <td class="px-3 py-2" x-text="rule.sub_menu_name"></td>
                                <td class="px-3 py-2 text-center" x-text="rule.level"></td>
                                <td class="px-3 py-2" x-text="rule.position_name"></td>
                                <td class="px-3 py-2" x-text="rule.division_name"></td>
                                <td class="px-3 py-2 text-center" x-text="rule.is_mandatory ? '✓' : '-'"></td>
                                <td class="px-3 py-2 text-center" x-text="rule.is_final ? '✓' : '-'"></td>
                                <td class="px-3 py-2 text-center">
                                    <form :action="'/approval-rules/' + rule.id" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:underline delete-btn">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Hidden inputs untuk dikirim ke backend -->
                <template x-for="(rule, index) in rules" :key="'hidden-' + index">
                    <div>
                        <input type="hidden" :name="'rules['+index+'][level]'" :value="rule.level">
                        <input type="hidden" :name="'rules['+index+'][sub_menu_id]'" :value="rule.sub_menu_id">
                        <input type="hidden" :name="'rules['+index+'][division_id]'" :value="rule.division_id">
                        <input type="hidden" :name="'rules['+index+'][position_id]'" :value="rule.position_id">
                        <input type="hidden" :name="'rules['+index+'][is_mandatory]'" :value="rule.is_mandatory ? 1 : 0">
                        <input type="hidden" :name="'rules['+index+'][is_final]'" :value="rule.is_final ? 1 : 0">
                    </div>
                </template>

                <button type="button" class="bg-purple-800 text-white px-3 py-1 rounded hover:bg-purple-700"
                    @click="openModal()">+ Tambah Approval Level</button>

                <!-- Modal -->
                <div x-show="showModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white p-6 rounded shadow w-[28rem]">
                        <h2 class="text-lg font-semibold mb-4">Tambah Approval Level</h2>

                        <!-- Sub Menu -->
                        <div class="mb-3">
                            <label class="block text-sm font-bold mb-1">Sub Menu</label>
                            <select x-model="newSubMenuId" class="border rounded w-full px-3 py-2">
                                <option value="">-- Pilih Sub Menu --</option>
                                @foreach($menu->subMenus as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Division -->
                        <div class="mb-3">
                            <label class="block text-sm font-bold mb-1">Divisi</label>
                            <select x-model="newDivisionId" class="border rounded w-full px-3 py-2">
                                <option value="">-- Pilih Divisi --</option>
                                @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Position -->
                        <div class="mb-3">
                            <label class="block text-sm font-bold mb-1">Posisi / Jabatan</label>
                            <select x-model="newPositionId" class="border rounded w-full px-3 py-2">
                                <option value="">-- Pilih Posisi --</option>
                                @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" data-level="{{ $pos->level }}">{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Flags -->
                        <div class="flex items-center gap-3 mb-4">
                            <label><input type="checkbox" x-model="newIsMandatory"> Mandatory</label>
                            <label><input type="checkbox" x-model="newIsFinal"> Final</label>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" class="bg-red-600 text-white px-4 py-2 rounded"
                                @click="closeModal()">Batal</button>
                            <button type="button" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"
                                @click="addRule()">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                Update
            </button>
        </form>

        <!-- Alpine Script -->
        <script>
            function approvalHandler(initialRules = []) {
                return {
                    rules: initialRules,
                    showModal: false,
                    newLevel: '',
                    newSubMenuId: '',
                    newDivisionId: '',
                    newPositionId: '',
                    newIsMandatory: false,
                    newIsFinal: false,

                    openModal() {
                        this.showModal = true;
                        this.newLevel = '';
                        this.newSubMenuId = '';
                        this.newDivisionId = '';
                        this.newPositionId = '';
                        this.newIsMandatory = false;
                        this.newIsFinal = false;
                    },
                    closeModal() {
                        this.showModal = false;
                    },
                    addRule() {
                        if (this.newPositionId) {
                            const getText = (selector, value) =>
                                document.querySelector(`${selector} option[value='${value}']`)?.textContent || '-';

                            const getLevel = (selector, value) =>
                                document.querySelector(`${selector} option[value='${value}']`)?.dataset.level || '';

                            this.rules.push({
                                level: getLevel('select[x-model=newPositionId]', this.newPositionId),
                                sub_menu_id: this.newSubMenuId,
                                sub_menu_name: getText('select[x-model=newSubMenuId]', this.newSubMenuId),
                                division_id: this.newDivisionId,
                                division_name: getText('select[x-model=newDivisionId]', this.newDivisionId),
                                position_id: this.newPositionId,
                                position_name: getText('select[x-model=newPositionId]', this.newPositionId),
                                is_mandatory: this.newIsMandatory,
                                is_final: this.newIsFinal
                            });

                            this.closeModal();
                        } else {
                            alert('Posisi wajib dipilih!');
                        }
                    },
                    removeRule(index) {
                        const rule = this.rules[index];
                        confirmAction({
                            title: 'Hapus Approval Rule?',
                            text: 'Data ini tidak dapat dikembalikan.',
                            confirmButtonText: 'Ya, hapus!',
                            confirmColor: '#dc2626'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`/approval-rules/${rule.id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        }
                                    })
                                    .then(res => {
                                        if (res.ok) {
                                            this.rules.splice(index, 1);
                                            successAlert('Approval rule berhasil dihapus!');
                                        } else {
                                            errorAlert('Gagal menghapus rule.');
                                        }
                                    });
                            }
                        });
                    }
                };
            }
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-btn')) {
                    e.preventDefault();
                    const form = e.target.closest('form');

                    confirmAction({
                        title: 'Hapus Approval Rule?',
                        text: 'Data yang dihapus tidak dapat dikembalikan.',
                        confirmButtonText: 'Ya, hapus!',
                        confirmColor: '#dc2626'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        </script>
    </main>
</x-app-layout>