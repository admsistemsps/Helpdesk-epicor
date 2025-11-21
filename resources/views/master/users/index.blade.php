<x-app-layout>
    <!-- Breadcrumb -->
    <x-breadcrumb />

    @if (session('success'))
    <div class="alert alert-success mb-3">
        {{ session('success') }}
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-1 gap-3">
                <div class="flex">
                    <h2 class="h4 fw-bold mb-3">Akun Pengguna</h2>
                </div>
                <div class="flex mb-3 text-end">
                    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-plus me-2"></i> Tambah User
                    </a>
                </div>
            </div>

            <div class="dt-top-controls flex justify-between items-center mb-3"></div>
            <div class="table-responsive">
                <table id="usersTable" class="datatable table table-bordered table-striped align-middle">
                    <thead class="usersTable" id="usersTable" style="color: indigo-400">
                        <tr>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Departemen</th>
                            <th>Site</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->position)
                                <span class="badge bg-primary">
                                    {{ $user->position->name ?? '' }}
                                </span>
                                @else
                                <span class="badge bg-secondary">No Position</span>
                                @endif
                            </td>
                            <td>{{ $user->department->code ?? '-' }}</td>
                            <td>{{ $user->site->name ?? '-' }}</td>
                            <td>
                                @if ($user->status == 'active')
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>{{ $user->role->name ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Tidak ada data user</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
    @endpush
</x-app-layout>