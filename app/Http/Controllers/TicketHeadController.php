<?php

namespace App\Http\Controllers;

use App\Models\TicketHead;
use Illuminate\Http\Request;
use App\Models\MasterMenu;
use App\Models\User;
use App\Models\MasterSubMenu;
use App\Models\TicketApproval;
use App\Models\TicketDetail;
use App\Models\TicketDetailLog;
use App\Models\TicketLog;
use App\Models\TicketAssign;
use App\Models\TicketPriority;
use App\Models\TicketApprovalRule;
use App\Models\TicketAttachment;
use App\Observers\TicketDetailObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Services\ApprovalService;

class TicketHeadController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $menus = MasterMenu::all();
        $subMenus = MasterSubMenu::all();

        $tickets = TicketHead::with(['menu', 'submenu', 'user.position'])
            ->orderBy('created_at', 'desc');

        // === ROLE ADMIN (lihat semua ticket) ===
        if ($user->role_id == 1) {
            $tickets = $tickets->get();
        }

        // === ROLE TEKNISI (lihat yang assigned + requestor sendiri) ===
        elseif ($user->role_id == 2) {
            $tickets = $tickets->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('requestor_id', $user->id);
            })->get();
        }

        // === ROLE MANAGER/HEAD (lihat ticket bawahan satu departemen + miliknya) ===
        elseif ($user->position && $user->position->level >= 2) {

            $tickets = $tickets->where(function ($q) use ($user) {

                // 1️⃣ Ticket yang menunggu approval dari posisi/departemen user
                $q->where(function ($inner) use ($user) {
                    $inner->where('current_approval_position_id', $user->position->id)
                        ->where(function ($s) use ($user) {
                            $s->whereNull('current_approval_division_id')
                                ->orWhere('current_approval_division_id', $user->division_id);
                        })
                        ->whereNotIn('status', ['Approved', 'Rejected', 'Draft']);
                });

                // 2️⃣ Ticket dari bawahannya (user dalam divisi yang sama, tapi level lebih rendah)
                $q->orWhereHas('user.position', function ($pos) use ($user) {
                    $pos->where('division_id', $user->division_id)
                        ->where('level', '<', $user->position->level);
                });

                // 3️⃣ Ticket milik user sendiri
                $q->orWhere('requestor_id', $user->id);
            })->get();
        }

        // === DEFAULT: hanya ticket sendiri ===
        else {
            $tickets = $tickets->where('requestor_id', $user->id)->get();
        }

        return view('tickets.index', compact('tickets', 'menus', 'user', 'subMenus'));
    }

    public function approverView()
    {
        $user = Auth::user();
        $tickets = TicketHead::with(['menu', 'submenu', 'user.position'])
            ->where(function ($q) use ($user) {
                $q->where('current_approval_position_id', $user->position_id)
                    ->whereNotIn('status', ['Approved', 'Rejected', 'Draft']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.approve-view', compact('tickets', 'user'));
    }

    public function assignView()
    {
        $user = Auth::user();

        if ($user->role_id != 1) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        $tickets = TicketHead::query()
            ->whereIn('status', ['Approved', 'Dialihkan', 'Diajukan ke Konsultan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $ticketsAssign = TicketAssign::query()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('tickets.assign-view', compact('tickets', 'ticketsAssign', 'user'));
    }

    public function workView()
    {
        $user = Auth::user();

        $tickets = TicketHead::with(['menu', 'submenu', 'user.position'])
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id);

                if ($user->role_id == 2 || $user->role_id == 1) {
                    $query->orWhere(function ($subQuery) {
                        $subQuery->whereNull('assigned_to')
                            ->where('status', 'Diajukan ke Konsultan');
                    });
                }
            })
            ->whereIn('status', ['Assigned', 'In Progress', 'Feedback', 'Diajukan ke Konsultan'])
            ->orderByRaw("FIELD(status, 'Assigned', 'In Progress', 'Feedback', 'Diajukan ke Konsultan')")
            ->paginate(10);

        return view('tickets.work-view', compact('tickets', 'user'));
    }

    public function reportTicket()
    {
        $user = Auth::user();

        $tickets = TicketHead::with([
            'menu',
            'submenu',
            'user.position',
            'ticketAssigns.assignedUser',
            'ticketAssigns.priority',
        ])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
                'Feedback',
                'Completed',
                'Closed',
                'Diajukan ke Konsultan'
            ])
            ->orderByRaw("FIELD(status, 'Assigned', 'In Progress', 'Feedback')")
            ->paginate(10);

        return view('reports.tickets-report', compact('tickets', 'user'));
    }


    public function all()
    {
        $user = Auth::user();
        $menus = MasterMenu::all();
        $subMenus = MasterSubMenu::all();

        if ($user->role_id == 1 || $user->role_id == 4) {
            $tickets = TicketHead::with(['menu', 'submenu', 'user.position'])
                ->where('status', '!=', 'Draft')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('tickets.all-data', compact('tickets', 'menus', 'user', 'subMenus'));
    }

    public function create()
    {
        $user = Auth::user();
        if (in_array($user->role_id, [1, 2, 4])) {
            $menus = MasterMenu::all();
        } else if ($user->role_id == 3) {
            if (!is_null($user->division_id)) {
                $menus = MasterMenu::whereHas('divisions', function ($q) use ($user) {
                    $q->where('divisions.id', $user->division_id);
                })
                    ->orWhere(function ($q) {
                        $q->whereNull('division_id')
                            ->whereNull('department_id');
                    })
                    ->get();
            } else {
                $menus = MasterMenu::whereHas('departments', function ($q) use ($user) {
                    $q->where('departments.id', $user->department_id);
                })
                    ->orWhere(function ($q) {
                        $q->whereNull('division_id')
                            ->whereNull('department_id');
                    })
                    ->get();
            }
        } else {
            $menus = collect();
        }
        // dd($menus);
        $subMenus = MasterSubMenu::all();

        return view('tickets.create', compact('menus', 'subMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id'     => 'required|exists:master_menus,id',
            'sub_menu_id' => 'required|exists:master_sub_menus,id',
            'action'      => 'required|in:draft,submit',

            'details' => 'required|array|min:1',
            'details.*.nomor' => 'required|string|max:255',
            'details.*.reason' => 'required|string',
            'details.*.desc_before' => 'required|string',
            'details.*.desc_after' => 'required|string',

            'attachments.*' => 'nullable|file|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $lastNumber = TicketHead::withTrashed()
                ->where('nomor_fuhd', 'like', 'F/UHD/SPS/' . date('ym') . '/%')
                ->selectRaw('MAX(CAST(SUBSTRING(nomor_fuhd, -5) AS UNSIGNED)) as max_number')
                ->value('max_number') ?? 0;

            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            $nomorFuhd = "F/UHD/SPS/" . date('ym/') . $newNumber;

            $user   = auth()->user();
            $action = strtolower($request->input('action'));

            // Create Ticket
            $ticket = TicketHead::create([
                'nomor_fuhd'   => $nomorFuhd,
                'menu_id'      => $request->menu_id,
                'sub_menu_id'  => $request->sub_menu_id,
                'requestor_id' => $user->id,
                'status'       => $action === 'draft' ? 'Draft' : 'Menunggu',
                'current_approval_level' => $user->position->level ?? 1,
                'created_date' => now(),
            ]);

            // Insert detail lines
            $remarks = [];
            foreach ($request->details as $i => $d) {
                $detail = TicketDetail::create([
                    'ticket_head_id' => $ticket->id,
                    'ticket_line'    => $i + 1,
                    'nomor'          => $d['nomor'],
                    'reason'         => $d['reason'],
                    'desc_before'    => $d['desc_before'],
                    'desc_after'     => $d['desc_after'],
                    'created_date'   => now(),
                ]);

                TicketDetailLog::create([
                    'ticket_detail_id' => $detail->id,
                    'ticket_head_id'   => $ticket->id,
                    'ticket_line'      => $detail->ticket_line,
                    'nomor'            => $detail->nomor,
                    'reason'           => $detail->reason,
                    'desc_before'      => $detail->desc_before,
                    'desc_after'       => $detail->desc_after,
                    'action'           => 'Created',
                    'logged_at'        => now(),
                    'logged_by'        => $user->id,
                ]);

                $remarks[] = "Line {$detail->ticket_line}: nomor [{$detail->nomor}] dengan alasan {$detail->reason} [$detail->desc_before => $detail->desc_after]";
            }

            TicketLog::create([
                'ticket_head_id' => $ticket->id,
                'user_id'        => $user->id,
                'action'         => 'Ticket Created',
                'remark'         => "Ticket baru dibuat oleh {$user->name} : \n" . implode("\n", $remarks),
            ]);

            // Attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('attachments', 'public');
                        \App\Models\TicketAttachment::create([
                            'ticket_id' => $ticket->id,
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                        ]);
                    }
                }
            }

            DB::commit();

            // Jika draft, selesai
            if ($action === 'draft') {
                return redirect()->route('tickets.index')->with('success', 'Draft ticket berhasil disimpan.');
            }

            // Submit untuk approval
            return $this->submitTicket($ticket);
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Error saat menyimpan ticket: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan ticket. ' . $e->getMessage()]);
        }
    }

    public function approve(Request $request, TicketHead $ticket)
    {
        $user = Auth::user();

        TicketApproval::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'division_id' => $user->division_id,
            'position_id' => $user->position_id,
            'level'       => $user->position->level,
            'action'      => 'Approved',
            'approved_at' => now(),
            'note'        => $request->note ?? null,
        ]);

        if (in_array($user->position->level, [9, 99])) {
            $ticket->update([
                'status' => 'Approved',
                'current_approval_value' => null,
                'current_approval_position_id' => null,
                'current_approval_division_id' => null,
                'current_approval_level' => null,
            ]);
            return back()->with('success', 'Final approved by JM Finance');
        }

        $next = \App\Services\ApprovalService::getNextApprover($ticket, $ticket->current_approval_value ?? $user->position->level);

        if ($next['user']) {
            $nextPosition = \App\Models\MasterPosition::find($next['position_id']);

            $ticket->update([
                'status' => $nextPosition
                    ? 'Menunggu Approval ' . $nextPosition->name
                    : 'Menunggu Approval Level ' . number_format($next['value'], 2),
                'current_approval_value' => $next['value'],
                'current_approval_position_id' => $next['position_id'],
                'current_approval_division_id' => $next['division_id'],
                'current_approval_level' => floor($next['value']),
            ]);
        } else {
            $ticket->update([
                'status' => 'Approved',
                'current_approval_value' => null,
                'current_approval_position_id' => null,
                'current_approval_division_id' => null,
                'current_approval_level' => null,
            ]);
        }

        return back()->with('success', 'Ticket berhasil diapprove.');
    }

    public function reject(Request $request, TicketHead $ticket)
    {
        $user = Auth::user();

        TicketApproval::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'division_id' => $user->division_id,
            'position_id' => $user->position_id,
            'level'       => $user->position->level,
            'action'      => 'Rejected',
            'approved_at' => now(),
        ]);

        $ticket->comments()->create([
            'user_id' => $user->id,
            'action'  => 'Rejected',
            'comment' => $request->comment,
        ]);

        $ticket->update([
            'status'                      => 'Rejected',
            'current_approval_value'      => null,
            'current_approval_position_id' => null,
            'current_approval_division_id' => null,
            'current_approval_level'      => null,
        ]);

        return redirect()->route('tickets.index')->with('error', 'Ticket ditolak oleh ' . $user->name);
    }

    public function show(TicketHead $ticket)
    {
        $menus = MasterMenu::all();
        $subMenus = MasterSubMenu::all();
        $user = Auth::user();

        $details = \App\Models\TicketDetail::query()
            ->where('ticket_head_id', $ticket->id)
            ->select('*')
            ->whereIn('id', function ($q) use ($ticket) {
                $q->selectRaw('MAX(id)')
                    ->from('ticket_details')
                    ->where('ticket_head_id', $ticket->id)
                    ->groupBy('ticket_line');
            })
            ->orderBy('ticket_line')
            ->get();

        $totalLines = $details->count();
        $firstDetail = $details->first();

        logger($details->toArray());

        $duplicates = $ticket->details
            ->groupBy(fn($d) => $d->ticket_line)
            ->filter(fn($g) => $g->count() > 1);

        if ($duplicates->isNotEmpty()) {
            logger('DUPLICATE FOUND >>>');
            foreach ($duplicates as $line => $items) {
                logger([
                    'ticket_line' => $line,
                    'ids' => $items->pluck('id'),
                    'head_ids' => $items->pluck('ticket_head_id'),
                ]);
            }
        }


        return view('tickets.show', compact(
            'ticket',
            'menus',
            'user',
            'subMenus',
            'firstDetail',
            'totalLines',
        ))->with('details', $details->values()->toArray());
    }

    public function edit(TicketHead $ticket)
    {
        //
    }

    public function update(Request $request, TicketHead $ticket)
    {
        $request->validate([
            'menu_id'     => 'required|exists:master_menus,id',
            'sub_menu_id' => 'required|exists:master_sub_menus,id',
            'action'      => 'required|in:draft,submit',
            'attachment'  => 'nullable|file',
            'details'     => 'nullable|array',
            'details.*.nomor' => 'nullable|string',
            'details.*.reason' => 'nullable|string',
            'details.*.desc_before' => 'nullable|string',
            'details.*.desc_after' => 'nullable|string',
        ]);

        $action = strtolower($request->action);
        $user   = auth()->user();

        DB::beginTransaction();

        try {
            TicketDetailObserver::$suppressLogging = true;

            // Update header
            $ticketChanges = [];
            if ($ticket->menu_id != $request->menu_id) {
                $ticketChanges['menu_id'] = $request->menu_id;
            }
            if ($ticket->sub_menu_id != $request->sub_menu_id) {
                $ticketChanges['sub_menu_id'] = $request->sub_menu_id;
            }
            if (!empty($ticketChanges)) {
                $ticket->update($ticketChanges);
            }

            // Simpan detail lama di log
            $oldDetails = TicketDetail::where('ticket_head_id', $ticket->id)->get();
            foreach ($oldDetails as $old) {
                TicketDetailLog::create([
                    'ticket_detail_id' => $old->id,
                    'ticket_head_id'   => $old->ticket_head_id,
                    'ticket_line'      => $old->ticket_line,
                    'nomor'            => $old->nomor,
                    'reason'           => $old->reason,
                    'desc_before'      => $old->desc_before,
                    'desc_after'       => $old->desc_after,
                    'logged_at'        => now(),
                    'logged_by'        => $user->id,
                    'action'           => 'MOVED',
                ]);
            }

            TicketDetailObserver::$suppressLogging = false;
            TicketDetail::where('ticket_head_id', $ticket->id)->forceDelete();

            // Insert detail baru
            $newDetails = collect($request->input('details', []));
            $lineNumber = 1;
            foreach ($newDetails as $item) {
                $hasContent = !empty($item['nomor']) || !empty($item['desc_before']) || !empty($item['desc_after']) || !empty($item['reason']);
                if (!$hasContent) continue;

                TicketDetail::create([
                    'ticket_head_id' => $ticket->id,
                    'ticket_line'    => $lineNumber++,
                    'nomor'          => $item['nomor'] ?? '',
                    'reason'         => $item['reason'] ?? '',
                    'desc_before'    => $item['desc_before'] ?? '',
                    'desc_after'     => $item['desc_after'] ?? '',
                    'comment'        => null,
                    'created_date'   => now()->toDateString(),
                ]);
            }

            DB::commit(); // ✅ perubahan tersimpan dulu

            // Jika draft, selesai
            if ($action === 'draft') {
                return back()->with('success', 'Perubahan disimpan sebagai draft.');
            }

            // Submit untuk approval, cek minimal 1 detail
            $detailCount = TicketDetail::where('ticket_head_id', $ticket->id)->count();
            if ($detailCount < 1) {
                return back()->with('error', 'Ticket harus memiliki minimal 1 detail line sebelum diajukan untuk approval.');
            }

            return $this->submitTicket($ticket);
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Ticket update error: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Terjadi kesalahan saat menyimpan. ' . $e->getMessage());
        }
    }

    private function submitTicket(TicketHead $ticket)
    {
        $user = auth()->user();

        // Catat approval submit
        TicketApproval::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'division_id' => $user->division_id,
            'position_id' => $user->position_id,
            'level'       => $user->position->level ?? 1,
            'action'      => 'Submitted',
            'approved_at' => now(),
        ]);

        // Jika posisi JM Finance, langsung approved
        if (in_array($user->position->level, [9, 99])) {
            $ticket->update([
                'status' => 'Approved',
                'current_approval_value' => null,
                'current_approval_position_id' => null,
                'current_approval_division_id' => null,
                'current_approval_level' => null,
            ]);

            TicketApproval::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => $user->id,
                'division_id' => $user->division_id,
                'position_id' => $user->position_id,
                'level'       => $user->position->level ?? 1,
                'action'      => 'Auto Approved (JM Finance)',
                'approved_at' => now(),
            ]);

            return back()->with('success', 'Ticket otomatis disetujui oleh JM Finance.');
        }

        // Lanjut ke approver berikutnya
        $next = \App\Services\ApprovalService::getNextApprover($ticket);
        if (!empty($next['user'])) {
            $nextPosition = \App\Models\MasterPosition::find($next['position_id']);
            $ticket->update([
                'status' => $nextPosition
                    ? 'Menunggu Approval ' . $nextPosition->name
                    : 'Menunggu Approval Level ' . number_format($next['value'], 2),
                'current_approval_value'       => $next['value'],
                'current_approval_position_id' => $next['position_id'],
                'current_approval_division_id' => $next['division_id'],
                'current_approval_level'       => floor($next['value']),
            ]);
        } else {
            $ticket->update([
                'status' => 'Approved',
                'current_approval_value' => null,
                'current_approval_position_id' => null,
                'current_approval_division_id' => null,
                'current_approval_level' => null,
            ]);
        }

        return back()->with('success', 'Ticket berhasil diajukan untuk approval.');
    }

    private function compareDetailsHybrid($oldDetails, $newDetails): string
    {
        $remarks = [];

        $oldMap = $oldDetails->keyBy('ticket_line'); // pakai line sebagai acuan
        $newMap = collect($newDetails)->keyBy(function ($item, $key) {
            return $key + 1; // line baru akan disesuaikan urutan
        });

        // Cek line yang dihapus
        foreach ($oldMap as $line => $old) {
            if (!isset($newMap[$line])) {
                $remarks[] = "Line {$line} (nomor='{$old->nomor}') dihapus";
            }
        }

        // Cek line baru / diubah
        foreach ($newMap as $line => $new) {
            $old = $oldMap[$line] ?? null;

            if ($old) {
                $changes = [];
                foreach (['nomor', 'reason', 'desc_before', 'desc_after'] as $field) {
                    if (($old[$field] ?? null) !== ($new[$field] ?? null)) {
                        $changes[] = "$field: '{$old[$field]}' → '{$new[$field]}'";
                    }
                }

                if (!empty($changes)) {
                    $remarks[] = "Line {$line} (nomor='{$old->nomor}') diubah (" . implode(', ', $changes) . ")";
                }
            } else {
                $remarks[] = "Line baru ditambahkan (nomor='{$new['nomor']}')";
            }
        }

        return implode("; ", $remarks);
    }

    public function destroy(TicketHead $ticket)
    {
        //
    }

    public function getSubMenus($menu_id)
    {
        $subMenus = MasterSubMenu::where('menu_id', $menu_id)->get();
        return response()->json($subMenus);
    }

    public function assignForm(TicketHead $ticket)
    {
        // Hanya super admin yang bisa assign
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Akses ditolak.');
        }

        // Hanya tampilkan user dengan role admin sistem
        $admins = User::where('role_id', 2)->get();
        $priorities = TicketPriority::all();

        return view('tickets.assign', compact('ticket', 'admins', 'priorities'));
    }

    public function assignStore(Request $request, TicketHead $ticket)
    {
        $request->validate([
            'assigned_to' => 'nullable',
            'priority_id' => 'required|exists:ticket_priorities,id',
            'comment' => 'nullable|string|max:500',
        ]);

        $priority = \App\Models\TicketPriority::findOrFail($request->priority_id);
        $dueDateTime = now()->addHours($priority->sla_hours);
        $dueDate = $dueDateTime->format('Y-m-d');
        $dueTime = $dueDateTime->format('H:i:s');

        if ($request->assigned_to == 'CONSULTANT') {
            $ticket->update([
                'assigned_to' => null,
                'status' => 'Diajukan ke Konsultan',
            ]);

            TicketAssign::create([
                'ticket_head_id' => $ticket->id,
                'priority_id' => $request->priority_id,
                'action' => 'Sent to Consultant',
                'comment' => $request->comment,
                'assigned_by' => auth()->id(),
                'assigned_to' => null,
                'is_consultant' => 1,
                'assigned_date' => now(),
                'started_date' => now(),
                'due_date' => $dueDate,
                'due_time' => $dueTime,
            ]);

            return redirect()->route('tickets.assigner')
                ->with('success', '✅ Ticket telah diajukan ke konsultan Prismatech untuk penanganan lebih lanjut.');
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'Assigned',
        ]);

        TicketAssign::create([
            'ticket_head_id' => $ticket->id,
            'priority_id' => $request->priority_id,
            'action' => 'Assigned',
            'comment' => $request->comment,
            'assigned_by' => auth()->id(),
            'assigned_to' => $request->assigned_to,
            'is_consultant' => false,
            'assigned_date' => now(),
            'due_date' => $dueDate,
            'due_time' => $dueTime,
        ]);

        $user = User::find($request->assigned_to);

        return redirect()->route('tickets.assigner')
            ->with('success', '✅ Ticket berhasil diassign ke ' . ($user ? $user->name : 'Admin Sistem') . ' dengan prioritas ' . $priority->name);
    }


    public function uploadAttachment(Request $request, TicketHead $ticket)
    {
        $request->validate([
            'attachments.*' => 'required|file|max:5120',
        ]);

        $attachments = [];

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store('attachments', 'public');
            $attachment = \App\Models\TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
            ]);
            $attachments[] = [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'url' => asset('storage/' . $attachment->file_path),
            ];
        }

        return redirect()->back()->with('success', 'Lampiran berhasil diupload');
    }

    public function deleteAttachment(TicketHead $ticket, TicketAttachment $attachment)
    {
        if ($attachment->ticket_id != $ticket->id) {
            return response()->json(['success' => false], 403);
        }

        \Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return redirect()->back()->with('success', 'Lampiran berhasil dihapus');
    }

    public function complete(Request $request, TicketHead $ticket)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);
        $ticket->comments()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Completed',
            'comment' => $request->comment,
        ]);

        $ticket->ticketAssigns()->update([
            'completed_date' => now(),
        ]);

        $ticket->update([
            'status' => 'Completed',
        ]);

        return redirect()->back()->with('success', 'Ticket berhasil diselesaikan dengan komentar: ' . $request->comment);
    }

    public function feedback(Request $request, TicketHead $ticket)
    {
        $request->validate([
            'feedback_comment' => 'required|string|max:500',
        ]);
        $ticket->comments()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Feedback',
            'comment' => $request->feedback_comment,
        ]);

        $ticket->update([
            'status' => 'Feedback'
        ]);

        return response()->json(['success' => true]);
    }

    public function start(Request $request, TicketHead $ticket)
    {
        $ticket->comments()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Progress',
            'comment' => 'Sedang Dikerjakan',
        ]);

        $ticket->ticketAssigns()->update([

            'started_date' => now(),
        ]);

        $ticket->update([
            'status' => 'In Progress',
        ]);

        return redirect()->back()->with('success', 'Ticket sedang dikerjakan.');
    }

    public function close(TicketHead $ticket)
    {
        $ticket->comments()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Closed',
            'comment' => 'Ticket telah ditutup.',
        ]);

        $ticket->update([
            'status' => 'Closed',
            'closed_date' => now(),
        ]);

        return response()->json(['message' => 'Ticket telah ditutup.']);
    }


    public function throw(Request $request, TicketHead $ticket)
    {
        $ticket->comments()->create([
            'user_id' => auth()->user()->id,
            'action' => 'Dialihkan',
            'comment' => $request->comment,
        ]);

        $ticket->update([
            'status' => 'Dialihkan',
        ]);

        return redirect()->back()->with('success', 'Ticket berhasil dialihkan dengan komentar: ' . $request->comment);
    }
}
