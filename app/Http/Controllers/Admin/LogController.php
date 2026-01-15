<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log; // Changed from Logs to Log
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    /**
     * Display a listing of system logs.
     */
    public function index(Request $request): View
    {
        // Fetch logs with the user who performed the action.
        // We order by 'date' to match your namayandigitalregistry.sql schema.
        $logs = Log::with('user')
            ->latest('date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.logs.index', compact('logs'));
    }
}