<!-- User Index Show -->
<x-app-layout>
    <!-- Content -->
    <main class="flex-1">
        <x-breadcrumb />

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-1 gap-3">
                    <div class="flex">
                        <h2 class="h4 fw-bold mb-3">site</h2>
                    </div>
                    <div class="flex mb-3 text-end">
                        <a href="{{ route('sites.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createSite">
                            <i class="fa-solid fa-plus me-2"></i> Tambah site
                        </a>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="dt-top-controls flex justify-between items-center mb-3"></div>
                <div class="table-responsive">
                    <div class="overflow-x-auto">
                        <table id="sitesTable" class="datatable table table-bordered table-striped align-middle">
                            <thead id="sitesTable" class="color-indigo-400 font-semibold">
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($masterSites as $site)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $site->code }}</td>
                                    <td>{{ $site->name }}</td>
                                    <td>
                                        {{ $site->address }}
                                    </td>
                                    <td class="flex gap-2 justify-center">
                                        <button
                                            class="btn btn-warning btn-sm text-white text-sm px-3 py-1 rounded-lg"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $site->id }}"
                                            data-code="{{ $site->code }}"
                                            data-name="{{ $site->name }}"
                                            data-address="{{ $site->address }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('sites.destroy', $site->id) }}" method="POST">
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
        <div class="modal fade" id="createSite" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Site</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('sites.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="code" class="form-label">Kode Site</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="Masukkan nama site" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="name" class="form-label">Nama Site</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama site" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="address" class="form-label">Alamat</label>
                                <input type="text" name="address" id="address" class="form-control" placeholder="Masukkan deskripsi site">
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
                        <h5 class="modal-title" id="editModalLabel">Edit Site</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_code" class="form-label">Nama Site</label>
                                <input type="text" name="code" id="edit_code" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama Site</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="edit_address" class="form-label">Deskripsi</label>
                                <input type="text" name="address" id="edit_address" class="form-control" placeholder="Masukkan deskripsi site">
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
                let code = button.getAttribute('data-code');
                let name = button.getAttribute('data-name');
                let address = button.getAttribute('data-address');
                let form = document.getElementById('editForm');
                form.action = '/masters/sites/' + id;

                document.getElementById('edit_code').value = code;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_address').value = address;
            });
        });
    </script>
    @endpush
</x-app-layout>