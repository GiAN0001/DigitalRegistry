<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function document() : View
    {
        // Using optimized scope with joins (better performance)
        $baseQuery = DocumentRequest::withNames()
            ->with('documentType')
            ->orderBy('document_requests.created_at', 'desc');

        // Get requests by status
        $pendingRequests = (clone $baseQuery)->where('document_requests.status', 'Pending')->get();
        $signedRequests = (clone $baseQuery)->where('document_requests.status', 'Signed')->get();
        $releasedRequests = (clone $baseQuery)->where('document_requests.status', 'Released')->get();
        $cancelledRequests = (clone $baseQuery)->where('document_requests.status', 'Cancelled')->get();

        return view('transaction.document', compact(
            'pendingRequests',
            'signedRequests',
            'releasedRequests',
            'cancelledRequests'
        ));
    }
}