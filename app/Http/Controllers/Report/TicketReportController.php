<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketHead;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketReportController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::orderBy('name')->get();
        $userAssigns = \App\Models\User::orderBy('name')->where('role_id', 2)->get();
        $menus = \App\Models\MasterMenu::orderBy('name')->get();
        $subMenus = \App\Models\MasterSubMenu::orderBy('name')->get();
        $divisions = \App\Models\MasterDivision::orderBy('name')->get();
        $departments = \App\Models\MasterDepartment::orderBy('name')->get();
        $sites = \App\Models\MasterSite::orderBy('name')->get();

        return view('reports.tickets-report', compact('users', 'menus', 'subMenus', 'divisions', 'departments', 'sites', 'userAssigns'));
    }

    public function data(Request $request)
    {
        $query = TicketHead::with([
            'menu',
            'subMenu',
            'details',
            'ticketAssigns.assignedUser',
            'ticketAssigns.priority',
            'user.division',
            'user.department',
            'user.site'
        ]);

        // Apply filters
        if ($request->filled('created_range')) {
            $range = explode(' to ', $request->created_range);
            if (count($range) === 2) {
                [$start, $end] = $range;
                $query->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
            }
        }

        if ($request->filled('due_range')) {
            $range = explode(' to ', $request->due_range);
            if (count($range) === 2) {
                [$start, $end] = $range;
                $query->whereHas('ticketAssigns', function ($q) use ($start, $end) {
                    $q->whereBetween('due_date', [$start, $end]);
                });
            }
        }

        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : explode(',', $request->status);
            $statuses = array_filter($statuses);
            if (!empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        }

        if ($request->filled('priority')) {
            $priorities = is_array($request->priority) ? $request->priority : explode(',', $request->priority);
            $priorities = array_filter($priorities);
            if (!empty($priorities)) {
                $query->whereHas('ticketAssigns', function ($q) use ($priorities) {
                    $q->whereIn('priority_id', $priorities);
                });
            }
        }

        if ($request->filled('assigned_to')) {
            $query->whereHas('ticketAssigns', function ($q) use ($request) {
                $q->where('assigned_to', $request->assigned_to);
            });
        }

        if ($request->filled('requestor_id')) {
            $query->where('requestor_id', $request->requestor_id);
        }

        if ($request->filled('menu_id')) {
            $query->where('menu_id', $request->menu_id);
        }

        if ($request->filled('sub_menu_id')) {
            $query->where('sub_menu_id', $request->sub_menu_id);
        }

        if ($request->filled('division_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('site_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('site_id', $request->site_id);
            });
        }

        // Handle search
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_fuhd', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('menu', function ($m) use ($search) {
                        $m->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('subMenu', function ($sm) use ($search) {
                        $sm->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('details', function ($d) use ($search) {
                        $d->where('reason', 'like', "%{$search}%")
                            ->orWhere('nomor', 'like', "%{$search}%");
                    });
            });
        }

        // Get tickets
        $tickets = $query->orderBy('created_at', 'desc')->get();

        // Expand tickets to rows (one row per detail)
        $expandedData = [];
        $rowNumber = 1;

        foreach ($tickets as $ticket) {
            $latestAssign = $ticket->ticketAssigns->last();

            // Base data for this ticket
            $baseData = [
                'nomor_fuhd' => $ticket->nomor_fuhd ?? '-',
                'menu_name' => $ticket->menu->name ?? '-',
                'sub_menu_name' => $ticket->subMenu->name ?? '-',
                'priority_label' => $latestAssign && $latestAssign->priority ? $latestAssign->priority->name : '-',
                'requestor_name' => $ticket->user->name ?? '-',
                'division_name' => $ticket->user && $ticket->user->division ? $ticket->user->division->name : '-',
                'department_name' => $ticket->user && $ticket->user->department ? $ticket->user->department->name : '-',
                'site_name' => $ticket->user && $ticket->user->site ? $ticket->user->site->name : '-',
                'pic_name' => $latestAssign && $latestAssign->assignedUser ? $latestAssign->assignedUser->name : '-',
                'created_at' => $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '-',
                'due_date' => $latestAssign && $latestAssign->due_date
                    ? \Carbon\Carbon::parse($latestAssign->due_date)->format('Y-m-d') . ($latestAssign->due_time ? ' ' . \Carbon\Carbon::parse($latestAssign->due_time)->format('H:i') : '')
                    : '-',
                'completed_date' => $ticket->completed_date ? \Carbon\Carbon::parse($ticket->completed_date)->format('Y-m-d H:i') : '-',
                'status' => $ticket->status ?? '-',
            ];

            // If ticket has details, create one row per detail
            if ($ticket->details && $ticket->details->count() > 0) {
                foreach ($ticket->details as $detail) {
                    $expandedData[] = array_merge([
                        'DT_RowIndex' => $rowNumber++,
                        'ticket_line' => $detail->ticket_line ?? '-',
                        'nomor_detail' => $detail->nomor ?? '-',
                        'reason' => $detail->reason ?? '-',
                        'desc_before' => $detail->desc_before ?? '-',
                        'desc_after' => $detail->desc_after ?? '-',
                    ], $baseData);
                }
            } else {
                // No details, show one row with empty detail fields
                $expandedData[] = array_merge([
                    'DT_RowIndex' => $rowNumber++,
                    'ticket_line' => '-',
                    'nomor_detail' => '-',
                    'reason' => '-',
                    'desc_before' => '-',
                    'desc_after' => '-',
                ], $baseData);
            }
        }

        // Apply pagination to expanded data
        $recordsTotal = count($expandedData);
        $recordsFiltered = $recordsTotal;

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $paginatedData = array_slice($expandedData, $start, $length);

        // Re-number the rows for current page
        foreach ($paginatedData as $index => &$row) {
            $row['DT_RowIndex'] = $start + $index + 1;
        }

        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $paginatedData,
        ]);
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $query = TicketHead::with([
            'menu',
            'subMenu',
            'details',
            'ticketAssigns.assignedUser',
            'ticketAssigns.priority',
            'user.division',
            'user.department',
            'user.site'
        ]);

        // Apply same filters
        if ($request->filled('created_range')) {
            $range = explode(' to ', $request->created_range);
            if (count($range) === 2) {
                [$start, $end] = $range;
                $query->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
            }
        }

        if ($request->filled('due_range')) {
            $range = explode(' to ', $request->due_range);
            if (count($range) === 2) {
                [$start, $end] = $range;
                $query->whereHas('ticketAssigns', function ($q) use ($start, $end) {
                    $q->whereBetween('due_date', [$start, $end]);
                });
            }
        }

        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : explode(',', $request->status);
            $statuses = array_filter($statuses);
            if (!empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        }

        if ($request->filled('priority')) {
            $priorities = is_array($request->priority) ? $request->priority : explode(',', $request->priority);
            $priorities = array_filter($priorities);
            if (!empty($priorities)) {
                $query->whereHas('ticketAssigns', function ($q) use ($priorities) {
                    $q->whereIn('priority_id', $priorities);
                });
            }
        }

        if ($request->filled('assigned_to')) {
            $query->whereHas('ticketAssigns', function ($q) use ($request) {
                $q->where('assigned_to', $request->assigned_to);
            });
        }

        if ($request->filled('requestor_id')) {
            $query->where('requestor_id', $request->requestor_id);
        }

        if ($request->filled('menu_id')) {
            $query->where('menu_id', $request->menu_id);
        }

        if ($request->filled('sub_menu_id')) {
            $query->where('sub_menu_id', $request->sub_menu_id);
        }

        if ($request->filled('division_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('master_site_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('master_site_id', $request->site_id);
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            $filename = 'Report_BA_EPICOR_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($tickets) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Header
                fputcsv($handle, [
                    'Nomor Ticket',
                    'Menu',
                    'Sub-Menu',
                    'Line',
                    'Nomor Detail',
                    'Reason',
                    'Sebelum',
                    'Sesudah',
                    'Priority',
                    'Requestor',
                    'Division',
                    'Department',
                    'Site',
                    'PIC',
                    'Created At',
                    'Due Date',
                    'Completed Date',
                    'Status'
                ]);

                foreach ($tickets as $ticket) {
                    $latestAssign = $ticket->ticketAssigns->last();

                    if ($ticket->details && $ticket->details->count() > 0) {
                        foreach ($ticket->details as $detail) {
                            fputcsv($handle, [
                                $ticket->nomor_fuhd ?? '',
                                $ticket->menu->name ?? '',
                                $ticket->subMenu->name ?? '',
                                $detail->ticket_line ?? '',
                                $detail->nomor ?? '',
                                $detail->reason ?? '',
                                $detail->desc_before ?? '',
                                $detail->desc_after ?? '',
                                $latestAssign && $latestAssign->priority ? $latestAssign->priority->name : '',
                                $ticket->user->name ?? '',
                                $ticket->user && $ticket->user->division ? $ticket->user->division->name : '',
                                $ticket->user && $ticket->user->department ? $ticket->user->department->name : '',
                                $ticket->user && $ticket->user->site ? $ticket->user->site->name : '',
                                $latestAssign && $latestAssign->assignedUser ? $latestAssign->assignedUser->name : '',
                                $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '',
                                $latestAssign && $latestAssign->due_date ? $latestAssign->due_date : '',
                                $ticket->completed_date ? \Carbon\Carbon::parse($ticket->completed_date)->format('Y-m-d H:i') : '',
                                $ticket->status ?? '',
                            ]);
                        }
                    } else {
                        fputcsv($handle, [
                            $ticket->nomor_fuhd ?? '',
                            $ticket->menu->name ?? '',
                            $ticket->subMenu->name ?? '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $latestAssign && $latestAssign->priority ? $latestAssign->priority->name : '',
                            $ticket->user->name ?? '',
                            $ticket->user && $ticket->user->division ? $ticket->user->division->name : '',
                            $ticket->user && $ticket->user->department ? $ticket->user->department->name : '',
                            $ticket->user && $ticket->user->site ? $ticket->user->site->name : '',
                            $latestAssign && $latestAssign->assignedUser ? $latestAssign->assignedUser->name : '',
                            $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '',
                            $latestAssign && $latestAssign->due_date ? $latestAssign->due_date : '',
                            $ticket->completed_date ? \Carbon\Carbon::parse($ticket->completed_date)->format('Y-m-d H:i') : '',
                            $ticket->status ?? '',
                        ]);
                    }
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('warning', 'Format Excel belum tersedia. Silakan gunakan CSV.');
    }
}
