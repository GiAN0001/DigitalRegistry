<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\DocumentPurpose;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;


class DocumentController extends Controller
{
    public function document(): View
    {
        $baseQuery = DocumentRequest::withNames()
            ->with(['documentType', 'purpose'])
            ->orderBy('document_requests.id', 'asc');

        // Make sure status values match exactly (case-sensitive)
        $pendingRequests = (clone $baseQuery)->where('document_requests.status', 'Pending')->get();
        $signedRequests = (clone $baseQuery)->where('document_requests.status', 'Signed')->get();
        $releasedRequests = (clone $baseQuery)->where('document_requests.status', 'Released')->get();
        $cancelledRequests = (clone $baseQuery)->where('document_requests.status', 'Cancelled')->get();

        $documentTypes = DocumentType::all();
        $documentPurposes = DocumentPurpose::all();

        return view('transaction.document', compact(
            'pendingRequests',
            'signedRequests',
            'releasedRequests',
            'cancelledRequests',
            'documentTypes',
            'documentPurposes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'document_type_id' => 'required|exists:document_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_no' => 'required|string|max:30',
            'address' => 'required|string',
            'years_of_stay' => 'nullable|integer|min:0|max:100',
            'months_of_stay' => 'nullable|integer|min:0|max:11',
            'purpose_id' => 'required|exists:document_purposes,id',
            'other_purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        if (empty($request->years_of_stay) && empty($request->months_of_stay)) {
            return back()
                ->withErrors(['years_of_stay' => 'Please enter either years or months of stay.'])
                ->withInput();
        }

        $years = $request->years_of_stay ?? 0;
        $months = $request->months_of_stay ?? 0;

        if ($months >= 12) {
            $years += floor($months / 12);
            $months = $months % 12;
        }

        DocumentRequest::create([
            'resident_id' => $request->resident_id,
            'document_type_id' => $request->document_type_id,
            'purpose_id' => $request->purpose_id,
            'other_purpose' => $request->other_purpose,
            'years_of_stay' => $years,
            'months_of_stay' => $months,
            'created_by_user_id' => auth()->id(),
            'status' => 'Pending',
            'remarks' => $request->remarks,
            'fee' => 0.00,
        ]);

        return redirect()->back()->with('success', 'Document request created successfully!');
    }

    public function sign(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|integer',
                'date_signed' => 'required|date_format:Y-m-d H:i:s',
            ]);

            $documentRequest = DocumentRequest::find($validated['request_id']);
            
            if (!$documentRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found'
                ], 422);
            }

            $documentRequest->update([
                'status' => 'Signed',
                'date_signed' => $validated['date_signed'],
                'released_by_user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document signed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function release(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|exists:document_requests,id',
                'date_released' => 'required|date',
            ]);

            $documentRequest = DocumentRequest::find($validated['request_id']);
            $documentRequest->update([
                'status' => 'Released',
                'date_of_release' => $validated['date_released'],
                'released_by_user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document released successfully!',
                'data' => $documentRequest
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|exists:document_requests,id',
                'remarks' => 'required|string',
                'date_of_cancel' => 'required|date',
            ]);

            DocumentRequest::find($validated['request_id'])->update([
                'status' => 'Cancelled',
                'remarks' => $validated['remarks'],
                'date_of_cancel' => $validated['date_of_cancel'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document cancelled successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
