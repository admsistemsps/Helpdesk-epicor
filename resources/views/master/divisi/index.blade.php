<!-- Departemen Index Show -->
<x-app-layout>
    <!-- Content -->
    <main class="flex-1">
        <x-breadcrumb />
        <div class="bg-white rounded-xl shadow-md px-4 py-2">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-3">
                <h2 class="text-2xl font-semibold text-gray-800">Divisi</h2>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Tambah Divisi -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDivision">
                        + Tambah Divisi
                    </button>
                </div>
            </div>
            <!-- Table -->
            <div class="dt-top-controls flex justify-between items-center mb-3"></div>
            <div class="table-responsive">
                <table id="divisionsTable" class="datatable table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th class="col-code">code</th>
                            <th class="col-name">Name Divisi</th>
                            <th class="col-desc">Departemen</th>
                            <th class="col-action text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($divisions as $division)
                        <tr>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $division->code }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $division->name }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $division->department->name }} - {{ $division->department->description}}</td>
                            <td class="px-2 py-2 whitespace-nowrap flex gap-2 justify-center">
                                <button
                                    class="btn btn-warning btn-sm text-white text-sm px-3 py-1 rounded-lg"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="{{ $division->id }}"
                                    data-code="{{ $division->code }}"
                                    data-name="{{ $division->name }}"
                                    data-dept="{{ $division->department->id }}">
                                    Edit
                                </button>

                                <form action="{{ route('divisions.destroy', $division->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 text-white text-sm px-3 py-1 rounded-lg hover:bg-red-700 transition delete-btn">
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
        <div class="modal fade" id="createDivision" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Divisi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('divisions.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Divisi</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="Masukkan Kode divisi" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Divisi</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama divisi" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Departemen</label>
                                <select name="department_id" id="department_id" class="form-select" required>
                                    <option value="" disabled selected>Pilih Departemen</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->code }} - {{ $department->name}}</option>
                                    @endforeach
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
                        <h5 class="modal-title" id="editModalLabel">Edit Divisi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_code" class="form-label">Nama Divisi</label>
                                <input type="text" name="code" id="edit_code" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama Divisi</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_department_id" class="form-label">Departemen</label>
                                <select name="department_id" id="edit_department_id" class="form-select" required>
                                    <option value="" disabled selected>Pilih Departemen</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                let button = event.relatedTarget;
                let id = button.getAttribute('data-id');
                let code = button.getAttribute('data-code');
                let name = button.getAttribute('data-name');
                let dept = button.getAttribute('data-dept');

                // Set form action
                let form = document.getElementById('editForm');
                form.action = '/divisions/' + id;

                // Isi field
                document.getElementById('edit_code').value = code;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_department_id').value = dept;
            });
        });
    </script>
    @endpush
</x-app-layout>