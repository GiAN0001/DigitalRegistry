<x-app-layout>
    <div class="sub-content">
        <div class="flex flex-wrap items-center gap-2 mt-4">
            <x-search-bar 
                name="search"
                placeholder="Search by Name or Request ID" 
                class="w-full md:flex-1"
                value="{{ $search ?? '' }}"
            />
            <x-select-date 
                class="w-full md:flex-1"
                :value="$date ?? ''"
            />
            <div class="w-full md:flex-1">
                <x-dynamic-filter
                    model="App\Models\DocumentType"
                    column="name"
                    title="Filter by Document Type"
                />
            </div>
            <x-button
                x-data
                x-on:click.prevent="$dispatch('open-modal', 'new-request')"
            >
                <x-slot name="icon">
                    <x-lucide-plus class="w-4 h-4" />
                </x-slot>
                Add New Request
            </x-button>
        </div>
        <!-- Tabs -->
        <div class="flex mt-4 mb-6 border-b-2 border-blue-600">
            <button class="tab-button text-blue-600 font-semibold text-sm p-3 border-b-2 border-blue-600" data-tab="document-request">
                Document Request
            </button>
            <button class="tab-button text-slate-600 font-semibold text-sm p-3" data-tab="request-history">
                Request History
            </button>
        </div>

        <!-- Tab Content: Document Request -->
        <div id="document-request-content" class="tab-content">
            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-3 mb-6">
                <button class="filter-btn px-6 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm" data-filter="all">All</button>
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="for-fulfillment">For Fulfillment</button>
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="for-signature">For Signature</button>
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="for-release">For Release</button>
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="released">Released</button>
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="cancelled">Cancelled</button>
            </div>

            
            <!-- Kanban Cards with Horizontal Scroll -->
            <div class="overflow-x-auto pb-4" id="kanban-view">
                <div class="kanban-grid inline-grid grid-flow-col auto-cols-[minmax(358px,1fr)] gap-4 overflow-visible max-w-[1507px]">
                    <!-- For Fulfillment Column (Gray) -->
                    <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-gray-500 overflow-visible min-w-[358px]" data-status="for-fulfillment">
                        <div>
                            <h3 class="text-xl font-bold pl-8 pt-4 pb-2">For Fulfillment <span class="text-slate-400 text-base font-medium">({{ $forFulfillmentRequests->count() }})</span></h3>
                        </div>
                        <div class="pl-8 pr-8 pb-8 space-y-4 overflow-visible {{ $forFulfillmentRequests->count() > 2 ? 'max-h-[683px] overflow-y-auto custom-scrollbar' : '' }}">
                            @forelse($forFulfillmentRequests as $request)
                                <x-kanban-card
                                    :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                    :name="$request->resident_name"
                                    :document-type="$request->documentType->name"
                                    :fee="$request->fee"
                                    :staff="$request->createdBy->name ?? 'N/A'"
                                    :date="$request->created_at->format('d.m.Y')"
                                    status="For Fulfillment"
                                    :date-color="$request->date_color"
                                    :text-color="$request->text_color"
                                />
                            @empty
                                <p class="text-center text-gray-500 py-8">No requests for fulfillment</p>
                            @endforelse
                        </div>
                    </div>

                   <!-- For Signature Column (Blue) -->
                    <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-blue-500 overflow-visible min-w-[358px]" data-status="for-signature">
                        <div>
                            <h3 class="text-xl font-bold pl-8 pt-4 pb-2">For Signature <span class="text-slate-400 text-base font-medium">({{ $forSignatureRequests->count() }})</span></h3>
                        </div>
                        <div class="pl-8 pr-8 pb-8 space-y-4 overflow-visible {{ $forSignatureRequests->count() > 2 ? 'max-h-[683px] overflow-y-auto custom-scrollbar' : '' }}">
                            @forelse($forSignatureRequests as $request)
                                <x-kanban-card 
                                    :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                    :name="$request->resident_name"
                                    :document-type="$request->documentType->name"
                                    :fee="$request->fee"
                                    :staff="$request->transferredSignatureBy->name ?? 'N/A'"
                                    :date="$request->for_signature_at ? $request->for_signature_at->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                    status="For Signature"
                                    :date-color="$request->date_color"
                                    :text-color="$request->text_color"
                                    :transferred-by="$request->transferredSignatureBy->name ?? 'N/A'"
                                />
                            @empty
                                <p class="text-center text-gray-500 py-8">No requests for signature</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- For Release Column (Orange) -->
                    <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-orange-500 overflow-visible min-w-[358px]" data-status="for-release">
                        <div>
                            <h3 class="text-xl font-bold pl-8 pt-4 pb-2">For Release <span class="text-slate-400 text-base font-medium">({{ $forReleaseRequests->count() }})</span></h3>
                        </div>
                        <div class="pl-8 pr-8 pb-8 space-y-4 overflow-visible {{ $forReleaseRequests->count() > 2 ? 'max-h-[683px] overflow-y-auto custom-scrollbar' : '' }}">
                            @forelse($forReleaseRequests as $request)
                                <x-kanban-card 
                                    :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                    :name="$request->resident_name"
                                    :document-type="$request->documentType->name"
                                    :fee="$request->fee"
                                    :staff="$request->transferredForReleasedBy->name ?? 'N/A'"
                                    :date="$request->for_release_at ? $request->for_release_at->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                    status="For Release"
                                    :date-color="$request->date_color"
                                    :text-color="$request->text_color"
                                    :transferred-by="$request->transferredForReleasedBy->name ?? 'N/A'"
                                />
                            @empty
                                <p class="text-center text-gray-500 py-8">No requests for release</p>
                            @endforelse
                        </div>
                    </div>

                   <!-- Released Column (Green) -->
                    <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-green-500 overflow-visible min-w-[358px]" data-status="released">
                        <div>
                            <h3 class="text-xl font-bold pl-8 pt-4 pb-3">Released <span class="text-slate-400 text-base font-medium">({{ $releasedRequests->count() }})</span></h3>
                        </div>
                        <div class="pl-8 pr-8 pb-8 space-y-4 overflow-visible {{ $releasedRequests->count() > 2 ? 'max-h-[683px] overflow-y-auto custom-scrollbar' : '' }}">
                            @forelse($releasedRequests as $request)
                                <x-kanban-card 
                                    :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                    :name="$request->resident_name"
                                    :document-type="$request->documentType->name"
                                    :fee="$request->fee"
                                    :staff="$request->releasedBy->name ?? 'N/A'"
                                    :date="$request->date_of_release ? $request->date_of_release->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                    status="Released"
                                    :date-color="$request->date_color"
                                    :text-color="$request->text_color"
                                    :released-by="$request->releasedBy->name ?? 'N/A'"
                                />
                            @empty
                                <p class="text-center text-gray-500 py-8">No released requests</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Cancelled Column (Red) -->
                    <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-red-500 overflow-visible min-w-[358px]" data-status="cancelled">
                        <div>
                            <h3 class="text-xl font-bold pl-8 pt-4 pb-2">Cancelled <span class="text-slate-400 text-base font-medium">({{ $cancelledRequests->count() }})</span></h3>
                        </div>
                        <div class="pl-8 pr-8 pb-8 space-y-4 overflow-visible {{ $cancelledRequests->count() > 2 ? 'max-h-[683px] overflow-y-auto custom-scrollbar' : '' }}">
                            @forelse($cancelledRequests as $request)
                            <x-kanban-card 
                                    :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                    :name="$request->resident_name"
                                    :document-type="$request->documentType->name"
                                    :fee="$request->fee"
                                    :staff="$request->cancelledBy->name ?? 'N/A'"
                                    :date="$request->date_of_cancellation ? $request->date_of_cancellation->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                    status="Cancelled"
                                    :date-color="$request->date_color"
                                    :text-color="$request->text_color"
                                    :rejected-by="$request->cancelledBy->name ?? 'N/A'"
                                />
                            @empty
                                <p class="text-center text-gray-500 py-8">No cancelled requests</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- List View (Hidden by default, shown when filtering) -->
            <div class="hidden" id="list-view">
                <!-- For Fulfillment List -->
                <div class="list-section bg-white rounded-2xl border-t-4 border-gray-500 p-8 shadow-md mb-6 hidden" data-status="for-fulfillment">
                    <h3 class="text-xl font-bold mb-6">For Fulfillment <span class="text-slate-400 text-base font-medium">({{ $forFulfillmentRequests->count() }})</span></h3>
                    <div class="space-y-4">
                        @forelse($forFulfillmentRequests as $request)
                            <x-kanban-list-card
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->createdBy->name ?? 'N/A'"
                                :date="$request->created_at->format('d.m.Y')"
                                status="For Fulfillment"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                            />
                        @empty
                            <p class="text-center text-gray-500 py-8">No requests for fulfillment</p>
                        @endforelse
                    </div>
                </div>

                <!-- For Signature List -->
                <div class="list-section bg-white rounded-2xl border-t-4 border-blue-500 p-8 shadow-md mb-6 hidden" data-status="for-signature">
                    <h3 class="text-xl font-bold mb-6">For Signature <span class="text-slate-400 text-base font-medium">({{ $forSignatureRequests->count() }})</span></h3>
                    <div class="space-y-4">
                        @forelse($forSignatureRequests as $request)
                            <x-kanban-list-card
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->transferredSignatureBy->name ?? 'N/A'"
                                :date="$request->for_signature_at ? $request->for_signature_at->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                status="For Signature"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                            />
                        @empty
                            <p class="text-center text-gray-500 py-8">No requests for signature</p>
                        @endforelse
                    </div>
                </div>

                <!-- For Release List -->
                <div class="list-section bg-white rounded-2xl border-t-4 border-orange-500 p-8 shadow-md mb-6 hidden" data-status="for-release">
                    <h3 class="text-xl font-bold mb-6">For Release <span class="text-slate-400 text-base font-medium">({{ $forReleaseRequests->count() }})</span></h3>
                    <div class="space-y-4">
                        @forelse($forReleaseRequests as $request)
                            <x-kanban-list-card
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->transferredForReleasedBy->name ?? 'N/A'"
                                :date="$request->for_release_at ? $request->for_release_at->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                status="For Release"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                            />
                        @empty
                            <p class="text-center text-gray-500 py-8">No requests for release</p>
                        @endforelse
                    </div>
                </div>

                <!-- Released List -->
                <div class="list-section bg-white rounded-2xl border-t-4 border-green-500 p-8 shadow-md mb-6 hidden" data-status="released">
                    <h3 class="text-xl font-bold mb-6">Released <span class="text-slate-400 text-base font-medium">({{ $releasedRequests->count() }})</span></h3>
                    <div class="space-y-4">
                        @forelse($releasedRequests as $request)
                            <x-kanban-list-card
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->releasedBy->name ?? 'N/A'"
                                :date="$request->date_of_release ? $request->date_of_release->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                status="Released"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                                :released-by="$request->releasedBy->name ?? 'N/A'"
                            />
                        @empty
                            <p class="text-center text-gray-500 py-8">No released requests</p>
                        @endforelse
                    </div>
                </div>

                <!-- Cancelled List -->
                <div class="list-section bg-white rounded-2xl border-t-4 border-red-500 p-8 shadow-md mb-6 hidden" data-status="cancelled">
                    <h3 class="text-xl font-bold mb-6">Cancelled <span class="text-slate-400 text-base font-medium">({{ $cancelledRequests->count() }})</span></h3>
                    <div class="space-y-4">
                        @forelse($cancelledRequests as $request)
                            <x-kanban-list-card
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->cancelledBy->name ?? 'N/A'"
                                :date="$request->date_of_cancellation ? $request->date_of_cancellation->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                status="Cancelled"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                                :rejected-by="$request->cancelledBy->name ?? 'N/A'"
                            />
                        @empty
                            <p class="text-center text-gray-500 py-8">No cancelled requests</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Request History -->
        <div id="request-history-content" class="tab-content hidden">
            <div class="p-6 bg-white shadow-md rounded-lg">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Request History</h2>
                
                <div class="overflow-x-auto mt-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-l-lg">Request ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider rounded-r-lg">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($historyRequests as $request)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $request->resident_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->created_at->format('F d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'Released' => 'bg-green-200 text-green-800',
                                                'Cancelled' => 'bg-red-200 text-red-700',
                                            ];
                                            $statusClass = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $request->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a 
                                            href="#"
                                            x-data
                                            x-on:click.prevent="
                                                const requestId = parseInt('{{ $request->id }}');
                                                fetch(`/document-requests/${requestId}`, {
                                                    headers: {
                                                        'Accept': 'application/json',
                                                        'X-Requested-With': 'XMLHttpRequest'
                                                    }
                                                })
                                                    .then(response => {
                                                        if (!response.ok) throw new Error('Failed to fetch');
                                                        return response.json();
                                                    })
                                                    .then(data => {
                                                        $dispatch('open-modal', 'view-request');
                                                        setTimeout(() => {
                                                            window.dispatchEvent(new CustomEvent('load-request', { detail: data }));
                                                        }, 100);
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        alert('Failed to load request details');
                                                    })
                                            "
                                            class="text-indigo-600 hover:text-blue-700"
                                        >
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No request history found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $historyRequests->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
    .kanban-column {
        transition: all 0.3s ease;
    }

    /* When filtered to single column, expand to full width */
    .kanban-grid.filtered-single {
        display: grid !important;
        grid-template-columns: 1fr !important;
        width: 100%;
        max-width: 1507px;
    }

    .kanban-grid.filtered-single .kanban-column {
        width: 100% !important;
        max-width: 1507px;
        min-width: 100% !important;
    }

    /* Horizontal scrollbar styles */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: rgba(136, 136, 136, 0.5);
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Vertical scrollbar styles */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(136, 136, 136, 0.3);
        border-radius: 10px;
        transition: background 0.3s ease;
    }

    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background: rgba(136, 136, 136, 0.6);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: rgba(136, 136, 136, 0.3) transparent;
    }

    .custom-scrollbar:hover {
        scrollbar-color: rgba(136, 136, 136, 0.6) transparent;
    }
    </style>

    <script>
    // Tab switching
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.add('text-slate-600');
            });
            this.classList.remove('text-slate-600');
            this.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabName + '-content').classList.remove('hidden');
        });
    });

    // Status filter with view switching
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            const kanbanView = document.getElementById('kanban-view');
            const listView = document.getElementById('list-view');
            const kanbanGrid = document.querySelector('.kanban-grid');
            
            // Update button states
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-blue-600', 'border', 'border-blue-600');
            });
            this.classList.remove('text-blue-600', 'border', 'border-blue-600');
            this.classList.add('bg-blue-600', 'text-white');
            
            if (filter === 'all') {
                // Show kanban view, hide list view
                kanbanView.classList.remove('hidden');
                listView.classList.add('hidden');
                kanbanGrid.classList.remove('filtered-single');
                
                // Show all kanban columns
                document.querySelectorAll('.kanban-column').forEach(column => {
                    column.classList.remove('hidden');
                });
            } else {
                // Hide kanban view, show list view
                kanbanView.classList.add('hidden');
                listView.classList.remove('hidden');
                
                // Hide all list sections first
                document.querySelectorAll('.list-section').forEach(section => {
                    section.classList.add('hidden');
                });
                
                // Show only the selected list section
                document.querySelectorAll('.list-section').forEach(section => {
                    if (section.dataset.status === filter) {
                        section.classList.remove('hidden');
                    }
                });
            }
        });
    });

    const dateInput = document.querySelector('input[name="date"]');
    if (dateInput) {
        dateInput.addEventListener('change', debounce(function() {
            const params = new URLSearchParams(window.location.search);
            const dateValue = dateInput.value;
            if (dateValue) {
                params.set('date', dateValue);
            } else {
                params.delete('date');
            }
            window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        }, 300));
    }
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const searchInput = document.querySelector('input[name="search"]');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const searchValue = searchInput.value;
            const params = new URLSearchParams(window.location.search);
            
            if (searchValue) {
                params.set('search', searchValue);
            } else {
                params.delete('search');
            }
            
            window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        }, 300));
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    document.addEventListener('click', function(event) {
        const allMenus = document.querySelectorAll('.dropdown-menu');
        allMenus.forEach(menu => {
            menu.classList.add('hidden');
        });
    });

    document.querySelectorAll('.custom-scrollbar').forEach(container => {
        container.addEventListener('scroll', function() {
            const allMenus = document.querySelectorAll('.dropdown-menu');
            allMenus.forEach(menu => {
                menu.classList.add('hidden');
            });
        });
    });
    </script>

    @include('transaction.modal.new-request')
    @include('transaction.modal.view-request')
    @include('transaction.modal.edit-request')
    @include('transaction.modal.signed')
    @include('transaction.modal.for-released')
    @include('transaction.modal.released')
    @include('transaction.modal.cancelled')
</x-app-layout>