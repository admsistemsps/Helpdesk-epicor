<!-- Posisi Index Show -->
<x-app-layout>
    <!-- Content -->
    <main class="flex-1">
        <x-breadcrumb />

        <div class="bg-white rounded-xl shadow-md px-4 py-2 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-3">
                <h2 class="text-2xl font-semibold text-gray-800">Posisi Jabatan</h2>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Tambah Posisi -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPosition">
                        + Tambah Posisi Jabatan
                    </button>
                </div>
            </div>
            <!-- Table -->
            <div class="dt-top-controls flex justify-between items-center mb-3"></div>
            <div class="table-responsive">
                <table id="positionsTable" class="datatable table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th class="col-name">Nama Posisi</th>
                            <th class="text-center">Deskripsi</th>
                            <th class="col-dept">Departemen</th>
                            <th class="col-div">Divisi</th>
                            <th class="col-role">Jabatan</th>
                            <th class="col-level text-center">Level</th>
                            <th class="col-action text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($positions as $position)
                        <tr>
                            <td>{{ $position->name }}</td>
                            <td class="text-center">{{ $position->description }}</td>
                            <td>{{ $position->department->name ?? '-' }}</td>
                            <td>{{ $position->division->name ?? '-' }}</td>
                            <td>{{ $position->jabatan}}</td>
                            <td>{{ $position->level }}</td>
                            <td class="flex gap-2 justify-center">
                                <button
                                    class="btn btn-warning btn-sm text-white"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="{{ $position->id }}"
                                    data-name="{{ $position->name }}"
                                    data-jabatan="{{ $position->jabatan }}"
                                    data-department="{{ $position->department_id }}"
                                    data-div="{{ $position->division_id }}">
                                    Edit
                                </button>

                                <form action="{{ route('positions.destroy', $position->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-danger btn-sm text-white text-white delete-btn">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Create -->
        <div class="modal fade" id="createPosition" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Posisi Jabatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('positions.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Posisi</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama posisi" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Masukkan nama posisi">
                            </div>
                            <div class="mb-3">
                                <label for="create_department_id" class="form-label">Departemen</label>
                                <select name="department_id" id="create_department_id" class="form-select">
                                    <option value="">- Tidak Ada Departemen -</option>
                                    @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="create_division_id" class="form-label">Divisi</label>
                                <select name="division_id" id="create_division_id" class="form-select">
                                    <option value="">- Tidak Ada Divisi -</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <select name="jabatan" id="jabatan" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jabatan</option>
                                    <option value="Staff/Admin">Staff/Admin</option>
                                    <option value="Koordinator">Koordinator</option>
                                    <option value="Supervisor">Supervisor</option>
                                    <option value="Junior Manajer">Junior Manajer</option>
                                    <option value="Manajer">Manajer</option>
                                    <option value="Junior Manajer FAC">Junior Manajer FAC</option>
                                    <option value="Manajer FAC">Manajer FAC</option>
                                    <option value="Direktur">Direktur</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Posisi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama Posisi</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Deskripsi</label>
                                <input type="text" name="description" id="edit_description" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="edit_department_id" class="form-label">Departemen</label>
                                <select name="department_id" id="edit_department_id" class="form-select">
                                    <option value="">- Tidak Ada Departemen -</option>
                                    @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_division_id" class="form-label">Divisi</label>
                                <select name="division_id" id="edit_division_id" class="form-select">
                                    <option value="">- Tidak Ada Divisi -</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_jabatan" class="form-label">Jabatan</label>
                                <select name="jabatan" id="edit_jabatan" class="form-select" required>
                                    <option value="" disabled selected>Pilih Jabatan</option>
                                    <option value="Staff/Admin">Staff/Admin</option>
                                    <option value="Koordinator">Koordinator</option>
                                    <option value="Supervisor">Supervisor</option>
                                    <option value="Junior Manajer">Junior Manajer</option>
                                    <option value="Manajer">Manajer</option>
                                    <option value="Junior Manajer FAC">Junior Manajer FAC</option>
                                    <option value="Manajer FAC">Manajer FAC</option>
                                    <option value="Direktur">Direktur</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let divisions = @json($divisions);

            // ============ CREATE ============
            let createDept = document.getElementById("create_department_id");
            let createDiv = document.getElementById("create_division_id");

            createDept.addEventListener("change", function() {
                let deptId = this.value;
                createDiv.innerHTML = '<option value="">- Tidak Ada Divisi -</option>';

                if (deptId) {
                    let filtered = divisions.filter(div => div.department_id == deptId);
                    filtered.forEach(function(div) {
                        let opt = document.createElement("option");
                        opt.value = div.id;
                        opt.textContent = div.name;
                        createDiv.appendChild(opt);
                    });
                }
            });

            // ============ EDIT ============
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                let button = event.relatedTarget;
                let id = button.getAttribute('data-id');
                let name = button.getAttribute('data-name');
                let description = button.getAttribute('data-description');
                let jabatan = button.getAttribute('data-jabatan');
                let dept = button.getAttribute('data-department');
                let div = button.getAttribute('data-div');

                // Set form action dengan route yang benar
                let form = document.getElementById('editForm');
                form.action = '/masters/positions/' + id;

                // Isi field
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_description').value = description || '';
                document.getElementById('edit_jabatan').value = jabatan;

                // Set Departemen
                let deptSelect = document.getElementById('edit_department_id');
                let divSelect = document.getElementById('edit_division_id');

                deptSelect.value = dept ?? "";

                // Reset divisi
                divSelect.innerHTML = '<option value="">- Tidak Ada Divisi -</option>';

                if (dept) {
                    let filtered = divisions.filter(div => div.department_id == dept);
                    filtered.forEach(function(d) {
                        let opt = document.createElement("option");
                        opt.value = d.id;
                        opt.textContent = d.name;
                        divSelect.appendChild(opt);
                    });
                }

                // Set divisi
                divSelect.value = div ?? "";
            });
        });
    </script>
    <script>
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-btn')) {
                e.preventDefault();
                const form = e.target.closest('form');

                confirmAction({
                    title: 'Hapus data?',
                    text: 'Data yang dihapus tidak dapat dikembalikan.',
                    confirmButtonText: 'Ya, hapus!',
                    confirmColor: '#dc2626'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            }
        });
    </script>
    @endpush
</x-app-layout>