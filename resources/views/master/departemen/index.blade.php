<!-- Departemen Index Show -->
<x-app-layout>
    <!-- Content -->
    <main class="flex-1">
        <x-breadcrumb />

        <div class="bg-white rounded-xl shadow-md px-4 py-2">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Departemen</h2>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Tambah Departemen -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDept">
                        + Tambah Departemen
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="dt-top-controls flex justify-between items-center mb-2"></div>

            <!-- Scroll hanya di bagian tabel -->
            <div class="table-responsive">
                <table id="departmentsTable" class="datatable table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="col-code">code</th>
                            <th class="col-name">Name</th>
                            <th class="col-desc">Description</th>
                            <th class="col-action text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($departments as $department)
                        <tr>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $department->code }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $department->name }}</td>
                            <td class="px-2 py-2 whitespace-nowrap">{{ $department->description }}</td>
                            <td class="px-2 py-2 whitespace-nowrap flex gap-2 justify-center">
                                <button
                                    class="btn btn-warning btn-sm text-white px-3 py-1 rounded-lg"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-code="{{ $department->code }}"
                                    data-id="{{ $department->id }}"
                                    data-name="{{ $department->name }}"
                                    data-description="{{ $department->description }}">
                                    Edit
                                </button>

                                <form action="{{ route('departments.destroy', $department->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-700 transition delete-btn">
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
        <div class="modal fade" id="createDept" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Departemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('departments.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="Masukkan code departemen optional">
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Departemen</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama departemen" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Masukkan deskripsi departemen">
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
                        <h5 class="modal-title" id="editModalLabel">Edit Departemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_code" class="form-label">Kode Departemen</label>
                                <input type="text" name="code" id="edit_code" class="form-control">
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama Departemen</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Deskripsi</label>
                                <input type="text" name="description" id="edit_description" class="form-control">
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
    <script src="//unpkg.com/alpinejs" defer></script>

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
                let code = button.getAttribute('data-code');
                let id = button.getAttribute('data-id');
                let name = button.getAttribute('data-name');
                let description = button.getAttribute('data-description');

                // Set form action
                let form = document.getElementById('editForm');
                form.action = '/departments/' + id;

                // Isi field
                document.getElementById('edit_code').value = code;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_description').value = description;
            });
        });
    </script>
    @endpush

</x-app-layout>