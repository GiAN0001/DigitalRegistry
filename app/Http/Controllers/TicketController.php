<?php

namespace App\Http\Controllers; // file created by gian

use App\Models\Ticket;
use App\Enums\LogAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TicketController extends Controller
{
    
    public function index(): View
    {
        $user = \Auth::user();
        $isAdmin = $user->hasRole('admin');

        // 1. FORENSIC AGGREGATION: Calculate counts based on role
        $baseQuery = Ticket::query();
        if (!$isAdmin) {
            $baseQuery->where('user_id', $user->id);
        }

        $totalTickets = (clone $baseQuery)->count();
        $pendingTickets = (clone $baseQuery)->where('status', 'Pending')->count();
        $inProgressTickets = (clone $baseQuery)->where('status', 'In Progress')->count();
        $completedTickets = (clone $baseQuery)->where('status', 'Completed')->count();
        $cancelledTickets = (clone $baseQuery)->where('status', 'Cancelled')->count();

        // 2. Main Table Query
        $query = Ticket::with('user');
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        $tickets = $query->orderByRaw("FIELD(status, 'Pending', 'In Progress', 'Completed', 'Cancelled')")
            ->orderByRaw("FIELD(priority, 'High', 'Medium', 'Low')")
            ->latest('date_created')
            ->get();

        return view('tickets.index', [
            'tickets'           => $tickets,
            'totalTickets'      => $totalTickets,
            'pendingTickets'    => $pendingTickets,
            'inProgressTickets' => $inProgressTickets,
            'completedTickets'  => $completedTickets,
            'cancelledTickets'  => $cancelledTickets,
        ]);
    }

    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_type' => 'required|string',
            'description' => 'required|string',
            'priority'    => 'required|in:Low,Medium,High',
        ]);

   
        Ticket::create([
            'user_id'         => Auth::id(),
            'ticket_type'     => $validated['ticket_type'],
            'description'     => $validated['description'],
            'priority'        => $validated['priority'],
            'status'          => 'Pending',
            'is_seen_by_user' => 1, 
            'date_created'    => now(), 
        ]);

        return back()->with('success', 'Ticket submitted. Admins have been notified.');
    }

    public function markAsSeen(Ticket $ticket)
    {
        if (auth()->id() === $ticket->user_id) {
            $ticket->update(['is_seen_by_user' => 1]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }

    public function start(Ticket $ticket)
    {
        if (!auth()->user()->hasRole('admin')) abort(403);

        $ticket->update(['status' => 'In Progress']);
        return back()->with('success', 'Working on ticket: ' . $ticket->subject);
    }

    
    public function show(Ticket $ticket): View
    {
        
        if (Auth::user()->hasRole('admin') && $ticket->status === 'Pending') {
            $ticket->update(['status' => 'In Progress']);
        }

      
        if (Auth::id() === $ticket->user_id) {
            $ticket->update(['is_seen_by_user' => 1]);
        }

        return view('tickets.show', compact('ticket'));
    }

   
    public function resolve(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->hasRole('admin')) abort(403);

        $validator = \Validator::make($request->all(), [
            'resolution_notes' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
        return back()
            ->withErrors($validator, 'details') 
            ->withInput()
            ->with('error_ticket_id', $ticket->id);
        }

        $ticket->update([
            'status' => 'Completed',
            'resolution_notes' => $request->resolution_notes,
            'resolved_by' => auth()->id(),
            'date_done' => now(),
            'is_seen_by_user' => 0 
        ]);

        return back()->with('success', 'Ticket resolved successfully.');
    }


    public function cancel(Request $request, Ticket $ticket)
    {
        if (auth()->id() !== $ticket->user_id) abort(403);
        
        $validator = \Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|min:5'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'details') 
                ->withInput()
                ->with('error_ticket_id', $ticket->id);
        }

        $userName = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $ticket->update([
            'status' => 'Cancelled',
            'resolution_notes' => "{$userName} cancelled the ticket: " . $request->cancellation_reason,
            'date_done' => now(),
        ]);

        return back()->with('success', 'Ticket cancelled.');
    }
}