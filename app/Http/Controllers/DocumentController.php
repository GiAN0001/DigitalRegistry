<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function document() : View
    {
        // Eager load all relationships
        $pendingRequests = DocumentRequest::with(['resident', 'documentType', 'createdByUser', 'releasedByUser'])
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $signedRequests = DocumentRequest::with(['resident', 'documentType', 'createdByUser', 'releasedByUser'])
            ->where('status', 'Signed')
            ->orderBy('created_at', 'desc')
            ->get();

        $releasedRequests = DocumentRequest::with(['resident', 'documentType', 'createdByUser', 'releasedByUser'])
            ->where('status', 'Released')
            ->orderBy('created_at', 'desc')
            ->get();

        $cancelledRequests = DocumentRequest::with(['resident', 'documentType', 'createdByUser', 'releasedByUser'])
            ->where('status', 'Cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transaction.document', compact(
            'pendingRequests',
            'signedRequests',
            'releasedRequests',
            'cancelledRequests'
        ));
    }
}