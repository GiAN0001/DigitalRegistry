<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class LogController extends Controller
{
    public function index(Request $request): View
    {

        $query = Log::with('user');
        

        $selectedMonth = $request->input('date');
        $analyticsQuery = Log::query();

        if ($selectedMonth) {

            $query->whereMonth('date', $selectedMonth);
            $analyticsQuery->whereMonth('date', $selectedMonth)
                        ->whereYear('date', Carbon::now()->year);
        } else {
     
            $analyticsQuery->where('date', '>=', Carbon::now()->startOfMonth());
        }

        $topActions = $analyticsQuery->select('log_type', \DB::raw('count(*) as total'))
            ->groupBy('log_type')
            ->orderBy('total', 'desc')
            ->limit(4)
            ->get();

        $logs = $query->latest('date')->paginate(15)->withQueryString();

        $logStat1 = $topActions->get(0);
        $logStat2 = $topActions->get(1);
        $logStat3 = $topActions->get(2);
        $logStat4 = $topActions->get(3);

        return view('admin.users.logs.index', [
            'logs' => $logs,
            'stat1Title' => $logStat1 ? str_replace('_', ' ', $logStat1->log_type) : 'No Data',
            'stat1Value' => $logStat1->total ?? 0,
            'stat2Title' => $logStat2 ? str_replace('_', ' ', $logStat2->log_type) : 'No Data',
            'stat2Value' => $logStat2->total ?? 0,
            'stat3Title' => $logStat3 ? str_replace('_', ' ', $logStat3->log_type) : 'No Data',
            'stat3Value' => $logStat3->total ?? 0,
            'stat4Title' => $logStat4 ? str_replace('_', ' ', $logStat4->log_type) : 'No Data',
            'stat4Value' => $logStat4->total ?? 0,
        ]);
    }
}