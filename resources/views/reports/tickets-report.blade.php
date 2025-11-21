<x-app-layout>
    <div class="container mt-4">
        <x-breadcrumb />

        <!-- FILTERS -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="filterForm" class="row g-2 align-items-end">
                    <!-- Created At Range -->
                    <div class="col-md-3">
                        <label class="form-label">Created Date Range</label>
                        <input type="text" id="created_range" name="created_range" class="form-control" placeholder="Pilih range tanggal dibuat">
                    </div>

                    <!-- Due Date Range -->
                    <div class="col-md-3">
                        <label class="form-label">Due Date Range</label>
                        <input type="text" id="due_range" name="due_range" class="form-control" placeholder="Pilih range due date">
                    </div>

                    <!-- Status -->
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select id="status" name="status" class="form-select select2-report" multiple>
                            <option value="Draft">Draft</option>
                            <option value="Submitted">Submitted</option>
                            <option value="Progress">Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Canceled">Canceled</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div class="col-md-3">
                        <label class="form-label">Prioritas</label>
                        <select id="priority" name="priority" class="form-select select2-report" multiple>
                            <option value="1">Urgent</option>
                            <option value="2">High</option>
                            <option value="3">Medium</option>
                            <option value="4">Low</option>
                        </select>
                    </div>

                    <!-- Menu / Category -->
                    <div class="col-md-3">
                        <label class="form-label">Menu / Category</label>
                        <select id="menu_id" name="menu_id" class="form-select select2-report">
                            <option value="">-- Semua Menu --</option>
                            @foreach($menus as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub-Menu -->
                    <div class="col-md-3">
                        <label class="form-label">Sub-Menu</label>
                        <select id="sub_menu_id" name="sub_menu_id" class="form-select select2-report">
                            <option value="">-- Semua Sub-Menu --</option>
                            @foreach($subMenus as $sm)
                            <option value="{{ $sm->id }}">{{ $sm->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Requestor -->
                    <div class="col-md-3">
                        <label class="form-label">Requestor</label>
                        <select id="requestor_id" name="requestor_id" class="form-select select2-report">
                            <option value="">-- Semua Requestor --</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PIC / Assigned to -->
                    <div class="col-md-3">
                        <label class="form-label">PIC / Assigned to</label>
                        <select id="assigned_to" name="assigned_to" class="form-select select2-report">
                            <option value="">-- Semua PIC --</option>
                            @foreach($userAssigns as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Division -->
                    <div class="col-md-3">
                        <label class="form-label">Division</label>
                        <select id="division_id" name="division_id" class="form-select select2-report">
                            <option value="">-- Semua Divisi --</option>
                            @foreach($divisions as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Department -->
                    <div class="col-md-3">
                        <label class="form-label">Department</label>
                        <select id="department_id" name="department_id" class="form-select select2-report">
                            <option value="">-- Semua Department --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Site -->
                    <div class="col-md-3">
                        <label class="form-label">Site</label>
                        <select id="site_id" name="site_id" class="form-select select2-report">
                            <option value="">-- Semua Site --</option>
                            @foreach($sites as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-12 text-end mt-2">
                        <button type="button" id="btnReset" class="btn btn-secondary me-2">Reset</button>
                        <button type="button" id="btnFilter" class="btn btn-primary me-2">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="overflow-x: auto;">
                    <table id="reportTable" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nomor Ticket</th>
                                <th>Menu</th>
                                <th>Jenis Perubahan</th>
                                <th>Line</th>
                                <th>Nomor Detail</th>
                                <th>Reason</th>
                                <th>Sebelum</th>
                                <th>Sesudah</th>
                                <th>Prioritas</th>
                                <th>Requestor</th>
                                <th>Divisi</th>
                                <th>Departemen</th>
                                <th>Site</th>
                                <th>PIC</th>
                                <th>tanggal Dibuat</th>
                                <th>Tenggat Waktu</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="19" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css" />
    <style>
        /* Custom styling for DataTables buttons */
        .dt-buttons .btn {
            margin: 0 2px;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .dt-buttons .btn-outline-secondary {
            color: #6c757d;
            border: 1px solid #6c757d;
            background-color: transparent;
        }

        .dt-buttons .btn-outline-secondary:hover {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
        }

        .dt-buttons .btn-outline-success {
            color: #198754;
            border: 1px solid #198754;
            background-color: transparent;
        }

        .dt-buttons .btn-outline-success:hover {
            color: #fff;
            background-color: #198754;
            border-color: #198754;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
        }

        .dt-buttons .btn-outline-danger {
            color: #dc3545;
            border: 1px solid #dc3545;
            background-color: transparent;
        }

        .dt-buttons .btn-outline-danger:hover {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .dt-buttons .btn-outline-primary {
            color: #0d6efd;
            border: 1px solid #0d6efd;
            background-color: transparent;
        }

        .dt-buttons .btn-outline-primary:hover {
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
        }

        .dt-buttons .btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .dt-buttons .dt-button {
            background-image: none !important;
            text-shadow: none !important;
        }

        /* Table cell alignment */
        #reportTable tbody td:nth-child(1) {
            text-align: center !important;
        }

        #reportTable tbody td:nth-child(7),
        #reportTable tbody td:nth-child(8),
        #reportTable tbody td:nth-child(9) {
            text-align: left !important;
        }

        /* Sticky header */
        .dt-top {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 2;
            padding: 8px 0;
        }

        .dt-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .dt-bottom {
            margin-top: 10px;
            background: #fff;
            padding-top: 8px;
            border-top: 1px solid #dee2e6;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        (function() {
            function loadScript(src) {
                return new Promise(function(resolve, reject) {
                    var script = document.createElement('script');
                    script.src = src;
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            }

            function loadLibraries() {
                if (!$.fn.dataTable) {
                    console.error('DataTables not loaded!');
                    return Promise.reject('DataTables required');
                }

                return loadScript('https://cdn.jsdelivr.net/momentjs/latest/moment.min.js')
                    .then(() => loadScript('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js'))
                    .then(() => loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'))
                    .then(() => loadScript('https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js'))
                    .then(() => loadScript('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js'))
                    .then(() => loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js'))
                    .then(() => loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js'))
                    .then(() => loadScript('https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js'))
                    .then(() => loadScript('https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js'));
            }

            function waitForJQuery(callback) {
                if (window.jQuery) {
                    callback();
                } else {
                    setTimeout(function() {
                        waitForJQuery(callback);
                    }, 100);
                }
            }

            waitForJQuery(function() {
                console.log('jQuery loaded, loading additional libraries...');

                loadLibraries().then(function() {
                    console.log('All libraries loaded, initializing components...');
                    initializeReportPage();
                }).catch(function(error) {
                    console.error('Error loading libraries:', error);
                });
            });

            function initializeReportPage() {
                $(document).ready(function() {
                    console.log('Initializing Report Table...');

                    // Initialize Select2
                    try {
                        $('.select2-report').select2({
                            width: '100%',
                            placeholder: 'Pilih...',
                            allowClear: true
                        });
                        console.log('Select2 initialized');
                    } catch (e) {
                        console.error('Select2 initialization error:', e);
                    }

                    // Initialize DateRangePicker
                    try {
                        $('#created_range, #due_range').daterangepicker({
                            autoUpdateInput: false,
                            locale: {
                                cancelLabel: 'Clear',
                                format: 'YYYY-MM-DD'
                            },
                            opens: 'left'
                        });

                        $('#created_range, #due_range').on('apply.daterangepicker', function(ev, picker) {
                            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
                        });

                        $('#created_range, #due_range').on('cancel.daterangepicker', function(ev, picker) {
                            $(this).val('');
                        });

                        console.log('DateRangePicker initialized');
                    } catch (e) {
                        console.error('DateRangePicker initialization error:', e);
                    }

                    // Initialize DataTable
                    var table = null;
                    try {
                        if ($.fn.DataTable.isDataTable('#reportTable')) {
                            $('#reportTable').DataTable().destroy();
                        }

                        table = $('#reportTable').DataTable({
                            processing: true,
                            serverSide: true,
                            scrollX: true,
                            ajax: {
                                url: "{{ route('reports.tickets.data') }}",
                                type: 'GET',
                                data: function(d) {
                                    d.created_range = $('#created_range').val() || '';
                                    d.due_range = $('#due_range').val() || '';

                                    var statusVal = $('#status').val();
                                    d.status = (statusVal && statusVal.length > 0) ? statusVal.join(',') : '';

                                    var priorityVal = $('#priority').val();
                                    d.priority = (priorityVal && priorityVal.length > 0) ? priorityVal.join(',') : '';

                                    d.assigned_to = $('#assigned_to').val() || '';
                                    d.requestor_id = $('#requestor_id').val() || '';
                                    d.menu_id = $('#menu_id').val() || '';
                                    d.sub_menu_id = $('#sub_menu_id').val() || '';
                                    d.division_id = $('#division_id').val() || '';
                                    d.department_id = $('#department_id').val() || '';
                                    d.site_id = $('#site_id').val() || '';

                                    console.log('Request params:', d);
                                },
                                error: function(xhr, error, thrown) {
                                    console.error('DataTable Ajax Error:', error);
                                    console.error('Response:', xhr.responseText);
                                    console.error('Status:', xhr.status);
                                },
                                dataSrc: function(json) {
                                    console.log('DataTable response:', json);
                                    return json.data;
                                }
                            },
                            columns: [{
                                    data: 'DT_RowIndex',
                                    name: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false,
                                    className: 'text-center'
                                },
                                {
                                    data: 'nomor_fuhd',
                                    name: 'nomor_fuhd'
                                },
                                {
                                    data: 'menu_name',
                                    name: 'menu_name'
                                },
                                {
                                    data: 'sub_menu_name',
                                    name: 'sub_menu_name'
                                },
                                {
                                    data: 'ticket_line',
                                    name: 'ticket_line'
                                },
                                {
                                    data: 'nomor_detail',
                                    name: 'nomor_detail'
                                },
                                {
                                    data: 'reason',
                                    name: 'reason'
                                },
                                {
                                    data: 'desc_before',
                                    name: 'desc_before'
                                },
                                {
                                    data: 'desc_after',
                                    name: 'desc_after'
                                },
                                {
                                    data: 'priority_label',
                                    name: 'priority_label'
                                },
                                {
                                    data: 'requestor_name',
                                    name: 'requestor_name'
                                },
                                {
                                    data: 'division_name',
                                    name: 'division_name'
                                },
                                {
                                    data: 'department_name',
                                    name: 'department_name'
                                },
                                {
                                    data: 'site_name',
                                    name: 'site_name'
                                },
                                {
                                    data: 'pic_name',
                                    name: 'pic_name'
                                },
                                {
                                    data: 'created_at',
                                    name: 'created_at'
                                },
                                {
                                    data: 'due_date',
                                    name: 'due_date'
                                },
                                {
                                    data: 'completed_date',
                                    name: 'completed_date'
                                },
                                {
                                    data: 'status',
                                    name: 'status'
                                }
                            ],
                            order: [
                                [15, 'desc']
                            ],
                            lengthMenu: [
                                [10, 25, 50, 100],
                                [10, 25, 50, 100]
                            ],
                            pageLength: 10,
                            dom: 'Blfrtip',
                            buttons: [{
                                    extend: 'copy',
                                    text: '<i class="fas fa-copy"></i> Copy',
                                    className: 'btn btn-sm btn-outline-secondary',
                                    title: 'Report_BA_EPICOR_' + new Date().toISOString().split('T')[0]
                                },
                                {
                                    extend: 'excel',
                                    text: '<i class="fas fa-file-excel"></i> Excel',
                                    className: 'btn btn-sm btn-outline-success',
                                    filename: 'Report_BA_EPICOR_' + new Date().toISOString().split('T')[0],
                                    title: 'Report Ubah Hapus Database Epicor',
                                    exportOptions: {
                                        columns: ':visible'
                                    }
                                },
                                {
                                    extend: 'csv',
                                    text: '<i class="fas fa-file-csv"></i> CSV',
                                    className: 'btn btn-sm btn-outline-success',
                                    filename: 'Report_BA_EPICOR_' + new Date().toISOString().split('T')[0],
                                    exportOptions: {
                                        columns: ':visible'
                                    }
                                },
                                {
                                    extend: 'pdf',
                                    text: '<i class="fas fa-file-pdf"></i> PDF',
                                    className: 'btn btn-sm btn-outline-danger',
                                    filename: 'Report_BA_EPICOR_' + new Date().toISOString().split('T')[0],
                                    title: 'Report Ubah Hapus Database Epicor',
                                    orientation: 'landscape',
                                    pageSize: 'A4',
                                    exportOptions: {
                                        columns: ':visible'
                                    }
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="fas fa-print"></i> Print',
                                    className: 'btn btn-sm btn-outline-primary',
                                    title: 'Report Ubah Hapus Database Epicor',
                                    exportOptions: {
                                        columns: ':visible'
                                    }
                                }
                            ],
                            language: {
                                processing: "Memproses data...",
                                lengthMenu: "Tampilkan _MENU_ data",
                                zeroRecords: "Data tidak ditemukan",
                                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                                infoEmpty: "Tidak ada data",
                                infoFiltered: "(disaring dari _MAX_ total data)",
                                search: "Cari:",
                                paginate: {
                                    first: "Awal",
                                    previous: "←",
                                    next: "→",
                                    last: "Akhir"
                                }
                            },
                            drawCallback: function() {
                                console.log('Table drawn');
                            }
                        });

                        console.log('DataTable initialized successfully');
                    } catch (e) {
                        console.error('DataTable initialization error:', e);
                    }

                    // Filter button
                    $('#btnFilter').on('click', function() {
                        console.log('Filter clicked');
                        if (table) {
                            table.ajax.reload();
                        }
                    });

                    // Reset button
                    $('#btnReset').on('click', function() {
                        console.log('Reset clicked');
                        $('#filterForm')[0].reset();
                        $('.select2-report').val(null).trigger('change');
                        $('#created_range, #due_range').val('');
                        if (table) {
                            table.ajax.reload();
                        }
                    });
                });
            }
        })();
    </script>
    @endpush
</x-app-layout>