<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketHead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', now()->year);

        $ticketQuery = TicketHead::query();

        if ($user->role_id == 2) {
            $ticketQuery->where('assigned_to', $user->id);
        }

        $tiketMasuk = (clone $ticketQuery)
            ->select(DB::raw('MONTH(created_date) as bulan'), DB::raw('COUNT(*) as jumlah'))
            ->whereYear('created_date', $tahun)
            ->where('status', '!=', 'Draft')
            ->groupBy(DB::raw('MONTH(created_date)'))
            ->pluck('jumlah', 'bulan')
            ->toArray();

        $tiketSelesai = (clone $ticketQuery)
            ->select(DB::raw('MONTH(closed_date) as bulan'), DB::raw('COUNT(*) as jumlah'))
            ->whereYear('closed_date', $tahun)
            ->where('status', 'Closed')
            ->groupBy(DB::raw('MONTH(closed_date)'))
            ->pluck('jumlah', 'bulan')
            ->toArray();

        // PERBAIKAN: Gunakan created_date atau updated_at, bukan closed_date
        $tiketProgres = (clone $ticketQuery)
            ->select(DB::raw('MONTH(created_date) as bulan'), DB::raw('COUNT(*) as jumlah'))
            ->whereYear('created_date', $tahun)
            ->whereIn('status', ['In Progress', 'Dialihkan', 'Diajukan ke Konsultan', 'Completed', 'Feedback', 'Assigned'])
            ->groupBy(DB::raw('MONTH(created_date)'))
            ->pluck('jumlah', 'bulan')
            ->toArray();

        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $openData   = [];
        $closedData = [];
        $progressData = [];

        for ($i = 1; $i <= 12; $i++) {
            $openData[]   = $tiketMasuk[$i] ?? 0;
            $closedData[] = $tiketSelesai[$i] ?? 0;
            $progressData[] = $tiketProgres[$i] ?? 0;
        }

        if (in_array($user->role_id, [1, 2])) {
            $data = [
                'role' => 'admin',
                'tahun' => $tahun,
                'totalTickets'   => (clone $ticketQuery)->where('status', '!=', 'Draft')->count(),
                'pendingTickets' => (clone $ticketQuery)->whereIn('status', ['In Progress', 'Dialihkan', 'Diajukan ke Konsultan', 'Completed', 'Feedback', 'Assigned'])->count(),
                'closedTickets'  => (clone $ticketQuery)->where('status', 'Closed')->count(),
                'totalUsers'     => User::count(),
                'recentTickets'  => (clone $ticketQuery)->latest()->take(5)->get(),
                'chartData' => [
                    'labels' => $bulan,
                    'open'   => $openData,
                    'closed' => $closedData,
                    'progress' => $progressData  // PERBAIKAN: Ubah key dari 'In Progress' menjadi 'progress'
                ],
            ];

            return view('dashboard.admin', $data);
        } else {
            // Query khusus untuk user berdasarkan requestor_id
            $myTicketsData = TicketHead::where('requestor_id', $user->id)
                ->select(DB::raw('MONTH(created_date) as bulan'), DB::raw('COUNT(*) as jumlah'))
                ->whereYear('created_date', $tahun)
                ->where('status', '!=', 'Draft')
                ->groupBy(DB::raw('MONTH(created_date)'))
                ->pluck('jumlah', 'bulan')
                ->toArray();

            $myTicketsPerMonth = [];
            for ($i = 1; $i <= 12; $i++) {
                $myTicketsPerMonth[] = $myTicketsData[$i] ?? 0;
            }

            $data = [
                'role' => 'user',
                'tahun' => $tahun,
                'totalTickets'      => TicketHead::where('requestor_id', $user->id)->count(),
                'openTickets' => TicketHead::where('requestor_id', $user->id)
                    ->where(function ($query) {
                        $query->where('status', 'Draft')
                            ->orWhere('status', 'Rejected')
                            ->orWhere('status', 'like', '%Menunggu%');
                    })
                    ->count(),
                'inProgressTickets' => TicketHead::where('requestor_id', $user->id)
                    ->whereIn('status', ['In Progress', 'Dialihkan', 'Diajukan ke Konsultan', 'Completed', 'Feedback', 'Assigned'])
                    ->count(),
                'closedTickets'     => TicketHead::where('requestor_id', $user->id)->where('status', 'Closed')->count(),
                'myRecentTickets'   => TicketHead::where('requestor_id', $user->id)->latest()->take(5)->get(),
                'chartData' => [
                    'labels' => $bulan,
                    'myTickets' => $myTicketsPerMonth,
                ],
            ];

            return view('dashboard.user', $data);
        }
    }
}
