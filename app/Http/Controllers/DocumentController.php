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
    public function document(Request $request): View
    {
        $search = $request->input('search', '');
        $date = $request->input('date');
        $threeDaysAgo = now()->subDays(3);
        $inProgressStatuses = ['For Fulfillment', 'For Signature', 'For Release'];

        $baseQuery = DocumentRequest::withNames()
    ->with(['documentType', 'purpose', 'resident']);

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('resident', function ($query) use ($search) {
                    $query->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhere('document_requests.id', 'like', '%' . $search . '%');
            });
        }

        if ($date) {
            $baseQuery->whereDate('document_requests.created_at', $date);
        }

        $name = $request->input('name');
        if ($name) {
            $baseQuery->whereHas('documentType', function ($q) use ($name) {
                $q->where('name', $name);
            });
        }

        $allRequests = $baseQuery
            ->orderBy('document_requests.id', 'asc')
            ->get();
           

        // Separate requests: Keep all in-progress requests, only old Released/Cancelled go to history
        $kanbancards = $allRequests->filter(function($request) use ($inProgressStatuses) {
            return in_array($request->status, $inProgressStatuses) 
                || ($request->status === 'Released')
                || ($request->status === 'Cancelled');
        });
        
        // For Fulfillment - all of them
        $forFulfillmentRequests = $kanbancards
            ->where('status', 'For Fulfillment')
            ->values();
        
        // For Signature - all of them
        $forSignatureRequests = $kanbancards
            ->where('status', 'For Signature')
            ->values();
        
        // For Release - all of them
        $forReleaseRequests = $kanbancards
            ->where('status', 'For Release')
            ->values();
        
        // Released - newer than 3 days
        $releasedRequests = $kanbancards
            ->where('status', 'Released')
            ->filter(function($request) use ($threeDaysAgo) {
                return ($request->date_of_release ?? $request->created_at) > $threeDaysAgo;
            })
            ->values();
        
        // Cancelled - newer than 3 days
        $cancelledRequests = $kanbancards
            ->where('status', 'Cancelled')
            ->filter(function($request) use ($threeDaysAgo) {
                return ($request->date_of_cancellation ?? $request->created_at) > $threeDaysAgo;
            })
            ->values();

        // History: Show Released and Cancelled if older than 3 days - paginate at query level
        $historyQuery = DocumentRequest::withNames()
            ->with(['documentType', 'resident'])
            ->where(function($query) use ($threeDaysAgo) {
                $query->where('document_requests.status', 'Released')
                    ->where('document_requests.date_of_release', '<=', $threeDaysAgo)
                    ->orWhere(function($q) use ($threeDaysAgo) {
                        $q->where('document_requests.status', 'Cancelled')
                            ->where('document_requests.date_of_cancel', '<=', $threeDaysAgo);
                    });
            });

        if ($search) {
            $historyQuery->where(function ($q) use ($search) {
                $q->whereHas('resident', function ($query) use ($search) {
                    $query->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                ->orWhere('document_requests.id', 'like', '%' . $search . '%');
            });
        }

        $historyRequests = $historyQuery
            ->orderBy('document_requests.id', 'desc')
            ->paginate(15);

            $documentTypes = DocumentType::all();
            $documentPurposes = DocumentPurpose::all();

        return view('transaction.document', compact(
            'forFulfillmentRequests',
            'forSignatureRequests',
            'forReleaseRequests',
            'releasedRequests',
            'cancelledRequests',
            'historyRequests',
            'documentTypes',
            'documentPurposes',
            'search',
            'date'
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
            'sex' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'civil_status' => 'nullable|string',
            'citizenship' => 'nullable|string',
            'annual_income' => 'nullable|numeric|min:0',
            'years_of_stay' => 'nullable|integer|min:0|max:100',
            'months_of_stay' => 'nullable|integer|min:0|max:11',
            'purpose_id' => 'nullable|exists:document_purposes,id',
            'other_purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        // Get document type to check if it's Cedula
        $documentType = \App\Models\DocumentType::find($request->document_type_id);
        $isCedula = strtolower($documentType->name) === 'cedula';

        // Validation for non-Cedula - years and months required
        if (!$isCedula) {
            if (empty($request->years_of_stay) && empty($request->months_of_stay)) {
                return back()
                    ->withErrors(['years_of_stay' => 'Please enter either years or months of stay.'])
                    ->withInput();
            }

            // Purpose is required for non-Cedula
            if (empty($request->purpose_id)) {
                return back()
                    ->withErrors(['purpose_id' => 'Purpose is required for this document type.'])
                    ->withInput();
            }
        }

        $years = $request->years_of_stay ?? 0;
        $months = $request->months_of_stay ?? 0;

        if ($months >= 12) {
            $years += floor($months / 12);
            $months = $months % 12;
        }

        try {
            DocumentRequest::create([
                'resident_id' => $request->resident_id,
                'document_type_id' => $request->document_type_id,
                'purpose_id' => $request->purpose_id ?? null,
                'other_purpose' => $request->other_purpose ?? null,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                'annual_income' => $request->annual_income ?? null,
                'years_of_stay' => $years,
                'months_of_stay' => $months,
                'created_by_user_id' => auth()->id(),
                'status' => 'For Fulfillment',
                'remarks' => $request->remarks ?? null,
                'fee' => 0.00,
            ]);

            return redirect()->back()->with('success', 'Document request created successfully!');
        } catch (\Exception $e) {
            \Log::error('Document request creation error: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create document request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function transferToSignature(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|integer|exists:document_requests,id',
                'for_signature_at' => 'required|date',
            ]);

            $documentRequest = DocumentRequest::find($validated['request_id']);
            
            if (!$documentRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found'
                ], 404);
            }

            // Check if already signed or later status
            if (in_array($documentRequest->status, ['For Signature', 'For Release', 'Released', 'Cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document has already been processed'
                ], 422);
            }

            // Convert to proper datetime format
            $signatureDate = \Carbon\Carbon::parse($validated['for_signature_at'])->format('Y-m-d H:i:s');

            $documentRequest->update([
                'status' => 'For Signature',
                'for_signature_at' => $signatureDate,
                'transferred_signature_by_user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document transferred to signature successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Transfer to signature error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function transferToRelease(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|integer|exists:document_requests,id',
                'for_release_at' => 'required|date',
            ]);

            $documentRequest = DocumentRequest::find($validated['request_id']);
            
            if (!$documentRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found'
                ], 404);
            }

            // Check if already at later status
            if (in_array($documentRequest->status, ['For Release', 'Released', 'Cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document has already been processed'
                ], 422);
            }

            // Convert to proper datetime format
            $releaseDate = \Carbon\Carbon::parse($validated['for_release_at'])->format('Y-m-d H:i:s');

            $documentRequest->update([
                'status' => 'For Release',
                'for_release_at' => $releaseDate,
                'transferred_for_released_by_user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document transferred for release successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Transfer to release error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function release(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|integer|exists:document_requests,id',
                'date_of_release' => 'required|date',
            ]);

            $documentRequest = DocumentRequest::find($validated['request_id']);
            
            if (!$documentRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found'
                ], 404);
            }

            // Check if already released or cancelled
            if (in_array($documentRequest->status, ['Released', 'Cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document has already been processed'
                ], 422);
            }

            // Convert to proper datetime format
            $releaseDate = \Carbon\Carbon::parse($validated['date_of_release'])->format('Y-m-d H:i:s');

            $documentRequest->update([
                'status' => 'Released',
                'date_of_release' => $releaseDate,
                'released_by_user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document released successfully!',
                'data' => $documentRequest
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Release error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|integer|exists:document_requests,id',
                'remarks' => 'required|string',
                'date_of_cancel' => 'required|date',
            ]);

            $documentRequest = DocumentRequest::find($validated['request_id']);
            
            if (!$documentRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document request not found'
                ], 404);
            }

            // Check if already cancelled or released
            if (in_array($documentRequest->status, ['Released', 'Cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document has already been processed'
                ], 422);
            }

            // Convert to proper datetime format
            $cancelDate = \Carbon\Carbon::parse($validated['date_of_cancel'])->format('Y-m-d H:i:s');

            $documentRequest->update([
                'status' => 'Cancelled',
                'remarks' => $validated['remarks'],
                'date_of_cancel' => $cancelDate,
                'cancelled_by_user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document cancelled successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Cancel error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($requestId)
    {
        try {
            $documentRequest = DocumentRequest::with(['resident.demographic', 'resident.household.areaStreet', 'documentType', 'purpose'])->find($requestId);

            if (!$documentRequest) {
                return response()->json(['error' => 'Request not found'], 404);
            }

            $resident = $documentRequest->resident;
            $demographics = $resident?->demographic;
            
            \Log::info('Resident ID: ' . $resident?->id);
            \Log::info('Demographics: ' . json_encode($demographics));

            $fullName = $resident
                ? trim(($resident->first_name ?? '') . ' ' . ($resident->middle_name ?? '') . ' ' . ($resident->last_name ?? '') . ' ' . ($resident->suffix ?? ''))
                : '';

            $address = $resident?->household?->areaStreet
                ? trim(
                    ($resident->household->house_number ?? '') . ', ' .
                    ($resident->household->areaStreet->street_name ?? '') . ', ' .
                    ($resident->household->areaStreet->purok_name ?? '')
                  )
                : '';

            $getUserName = function($userId) {
                if (!$userId) return null;
                $user = \App\Models\User::find($userId);
                return $user ? trim($user->first_name . ' ' . $user->last_name) : null;
            };

            return response()->json([
                'id' => $documentRequest->id,
                'resident_name' => $fullName,
                'email' => $documentRequest->email ?? '',
                'contact_no' => $documentRequest->contact_no ?? '',
                'address' => $address,
                'document_type' => $documentRequest->documentType->name ?? '',
                'status' => $documentRequest->status,
                'purpose_id' => $documentRequest->purpose_id,
                'purpose_name' => $documentRequest->purpose?->name ?? '',
                'other_purpose' => $documentRequest->other_purpose ?? '',
                'sex' => $demographics?->sex ?? null,
                'birthdate' => $demographics?->birthdate ?? null,
                'civil_status' => $demographics?->civil_status ?? null,
                'citizenship' => $demographics?->nationality ?? null,
                'annual_income' => $documentRequest->annual_income,
                'years_of_stay' => $documentRequest->years_of_stay,
                'months_of_stay' => $documentRequest->months_of_stay,
                'remarks' => $documentRequest->remarks ?? '',
                'fee' => $documentRequest->fee,
                'created_at' => $documentRequest->created_at,
                'for_signature_at' => $documentRequest->for_signature_at,
                'for_release_at' => $documentRequest->for_release_at,
                'date_of_release' => $documentRequest->date_of_release,
                'date_of_cancel' => $documentRequest->date_of_cancel,
                'date_of_edited' => $documentRequest->date_of_edited,
                'created_by' => $getUserName($documentRequest->created_by_user_id),
                'signed_by' => $getUserName($documentRequest->transferred_signature_by_user_id),
                'transferred_for_release_by' => $getUserName($documentRequest->transferred_for_released_by_user_id),
                'released_by' => $getUserName($documentRequest->released_by_user_id),
                'cancelled_by' => $getUserName($documentRequest->cancelled_by_user_id),
                'updated_by' => $getUserName($documentRequest->update_by_user_id),
            ]);
        } catch (\Exception $e) {
            \Log::error('Show request error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load request'], 500);
        }
    }

    public function update(Request $request, $requestId)
    {
        try {
            $documentRequest = DocumentRequest::find($requestId);

            if (!$documentRequest) {
                return response()->json(['success' => false, 'message' => 'Request not found'], 404);
            }

            if (in_array($documentRequest->status, ['Released', 'Cancelled'])) {
                return response()->json(['success' => false, 'message' => 'Cannot edit a completed request'], 422);
            }

            $documentType = $documentRequest->documentType;
            $isCedula = strtolower($documentType->name) === 'cedula';

            $rules = [
                'email' => 'required|email|max:255',
                'contact_no' => 'required|string|max:30',
            ];

            if ($isCedula) {
                $rules['annual_income'] = 'nullable|numeric|min:0';
            } else {
                $rules['years_of_stay'] = 'nullable|integer|min:0|max:100';
                $rules['months_of_stay'] = 'nullable|integer|min:0|max:11';
                $rules['purpose_id'] = 'required|exists:document_purposes,id';
                $rules['other_purpose'] = 'nullable|string';
                $rules['remarks'] = 'nullable|string';
            }

            $validated = $request->validate($rules);

            $updateData = [
                'email' => $validated['email'],
                'contact_no' => $validated['contact_no'],
                'update_by_user_id' => auth()->id(),
                'date_of_edited' => now(),
            ];

            if ($isCedula) {
                $updateData['annual_income'] = $validated['annual_income'] ?? null;
            } else {
                $updateData['years_of_stay'] = $validated['years_of_stay'] ?? 0;
                $updateData['months_of_stay'] = $validated['months_of_stay'] ?? 0;
                $updateData['purpose_id'] = $validated['purpose_id'];
                $updateData['other_purpose'] = $validated['other_purpose'] ?? null;
                $updateData['remarks'] = $validated['remarks'] ?? null;
            }

            $documentRequest->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Document request updated successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}