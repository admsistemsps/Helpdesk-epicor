<!-- User Index Show -->
<x-app-layout>
    <!-- Content -->
    <main class="flex-1">
        <x-breadcrumb />

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-1 gap-3">
                    <div class="flex">
                        <h2 class="h4 fw-bold mb-3">Daftar Menu</h2>
                    </div>
                    <div class="flex mb-3 text-end">
                        <a href="{{ route('menus.create') }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-plus me-2"></i> Tambah Menu
                        </a>
                    </div>
                </div>

                <div class="dt-top-controls flex justify-between items-center mb-3"></div>
                <div class="table-responsive">
                    <table id="menusTable" class="datatable table table-bordered table-striped align-middle">
                        <thead class="menusTable" style="color: indigo-400">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Diperuntukkan</th>
                                <th>Sub Menu</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menus as $menu)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $menu->name }}</td>
                                <td>{{ $menu->description }}</td>
                                <td>
                                    @if ($menu->division_id != null && $menu->department_id != null)
                                    {{ $menu->division->name ?? '-' }} - {{ $menu->department->code ?? '-' }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @foreach ($menu->subMenus as $submenu)
                                    <span class="block rounded bg-secondary text-white text-xs py-1 px-1 mb-2">
                                        {{ $loop->iteration }}. {{ $submenu->name }}<br>
                                    </span>
                                    @endforeach
                                </td>
                                <td class="text-center space-x-2">
                                    <a href="{{ route('menus.setting', $menu->id) }}"
                                        class="btn btn-sm btn-primary">Setting</a>
                                    <a href="{{ route('menus.edit', $menu->id) }}"
                                        class="btn btn-sm btn-warning text-white">
                                        Edit
                                    </a>

                                    <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
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
        $(document).on('click', '.edit-btn', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let description = $(this).data('description');

            // set form action
            $('#editMenuForm').attr('action', '/menus/' + id);
            $('#edit_name').val(name);
            $('#edit_description').val(description);
        });
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