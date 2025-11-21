<!-- User Index Show -->
<x-app-layout>
    <!-- Content -->
    <main class="flex-1">
        <x-breadcrumb />

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-1 gap-3">
                    <div class="flex">
                        <h2 class="h4 fw-bold mb-3">Role</h2>
                    </div>
                    <div class="flex mb-3 text-end">
                        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createRole">
                            <i class="fa-solid fa-plus me-2"></i> Tambah Role
                        </a>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="dt-top-controls flex justify-between items-center mb-3"></div>
                <div class="table-responsive">
                    <div class="overflow-x-auto">
                        <table id="rolesTable" class="datatable table table-bordered table-striped align-middle">
                            <thead id="rolesTable" class="color-indigo-400 font-semibold">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        {{ $role->description }}
                                    </td>
                                    <td class="flex gap-2 justify-center">
                                        <button
                                            class="btn btn-warning btn-sm text-white text-sm px-3 py-1 rounded-lg"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $role->id }}"
                                            data-name="{{ $role->name }}"
                                            data-description="{{ $role->description }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
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
            </div>
        </div>

        <!-- Modal Create -->
        <div class="modal fade" id="createRole" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="name" class="form-label">Nama Role</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama role" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="description" class="form-label">Deskripsi</label>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Masukkan deskripsi role">
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
                        <h5 class="modal-title" id="editModalLabel">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama Role</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="edit_description" class="form-label">Deskripsi</label>
                                <input type="text" name="description" id="edit_description" class="form-control" placeholder="Masukkan deskripsi role">
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

    <!-- modal action -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                let button = event.relatedTarget;
                let id = button.getAttribute('data-id');
                let name = button.getAttribute('data-name');
                let description = button.getAttribute('data-description');

                // Set form action
                let form = document.getElementById('editForm');
                form.action = '/masters/roles/' + id;

                // Isi field
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_description').value = description;
            });
        });
    </script>
    @endpush
</x-app-layout>