<x-app-layout>
    <div class="sub-content">
        <div class="flex flex-wrap items-center gap-2 mt-[42px]">
            <x-search-bar placeholder="Search by Name or Transaction ID" class="w-full md:flex-1" />  
            <x-select-date class="w-full md:flex-1" />
        </div>

        <!-- Tabs -->
        <div class="flex mt-8 mb-6 border-b-2 border-gray-200">
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
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="pending">Pending</button>
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="signed">Signed</button>
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="released">Released</button>
                <button class="filter-btn px-6 py-2 text-blue-600 border border-blue-600 rounded-lg font-medium text-sm" data-filter="cancelled">Cancelled</button>
            </div>

            <!-- Kanban Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Pending Column -->
                <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-gray-500" data-status="pending">
                    <div>
                        <h3 class="text-xl font-bold pl-8 pt-4 pb-4">Pending <span class="text-slate-400 text-base font-medium">({{ $pendingRequests->count() }})</span></h3>
                    </div>
                    <div class="pl-8 pr-8 pb-8 space-y-8 max-h-[800px] overflow-y-scroll overflow-x-visible">
                        @forelse($pendingRequests as $request)
                            <x-kanban-card
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->staff_name"
                                :date="$request->created_at->format('d.m.Y')"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                            />
                        @empty
                            <p class="text-center text-gray-500 py-8">No pending requests</p>
                        @endforelse
                    </div>
                </div>

                <!-- Signed Column -->
                <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-amber-500" data-status="signed">
                    <div>
                        <h3 class="text-xl font-bold pl-8 pt-4 pb-4">Signed <span class="text-slate-400 text-base font-medium">({{ $signedRequests->count() }})</span></h3>
                    </div>
                    <div class="pl-8 pr-8 pb-8 space-y-8 max-h-[800px] overflow-y-scroll overflow-x-visible">
                        @forelse($signedRequests as $request)
                            <x-kanban-card 
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->staff_name"
                                :date="$request->created_at->format('d.m.Y')"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                            />
                        @empty
                            <p class="text-center text-gray-500 py-8">No signed requests</p>
                        @endforelse
                    </div>
                </div>

                <!-- Released Column -->
                <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-green-500" data-status="released">
                    <div>
                        <h3 class="text-xl font-bold pl-8 pt-4 pb-4">Released <span class="text-slate-400 text-base font-medium">({{ $releasedRequests->count() }})</span></h3>
                    </div>
                    <div class="pl-8 pr-8 pb-8 space-y-8 max-h-[800px] overflow-y-scroll overflow-x-visible">
                        @forelse($releasedRequests as $request)
                            <x-kanban-card 
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->staff_name"
                                :date="$request->date_of_release ? $request->date_of_release->format('d.m.Y') : $request->created_at->format('d.m.Y')"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                                :released-by="$request->releasedByUser?->name"
                            />
                        @empty
                            <p class="text-center text-gray-500 py-8">No released requests</p>
                        @endforelse
                    </div>
                </div>

                <!-- Cancelled Column -->
                <div class="kanban-column bg-white rounded-2xl shadow-sm border-t-4 border-red-500" data-status="cancelled">
                    <div>
                        <h3 class="text-xl font-bold pl-8 pt-4 pb-4">Cancelled <span class="text-slate-400 text-base font-medium">({{ $cancelledRequests->count() }})</span></h3>
                    </div>
                    <div class="pl-8 pr-8 pb-8 space-y-8 max-h-[800px] overflow-y-scroll overflow-x-visible">
                        @forelse($cancelledRequests as $request)
                            <x-kanban-card 
                                :request-id="str_pad($request->id, 3, '0', STR_PAD_LEFT)"
                                :name="$request->resident_name"
                                :document-type="$request->documentType->name"
                                :fee="$request->fee"
                                :staff="$request->staff_name"
                                :date="$request->created_at->format('d.m.Y')"
                                :date-color="$request->date_color"
                                :text-color="$request->text_color"
                                :rejected-by="$request->releasedByUser?->name"
                                :reason="$request->remarks"
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
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h3 class="text-xl font-bold mb-4">Request History</h3>
                <p class="text-gray-500">History content coming soon...</p>
            </div>
        </div>
    </div>

    <script>
    // Tab switching
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Update button styles
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.add('text-slate-600');
            });
            this.classList.remove('text-slate-600');
            this.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            
            // Show/hide tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabName + '-content').classList.remove('hidden');
        });
    });

    // Status filter
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update button styles
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-blue-600', 'border', 'border-blue-600');
            });
            this.classList.remove('text-blue-600', 'border', 'border-blue-600');
            this.classList.add('bg-blue-600', 'text-white');
            
            // Show/hide columns
            document.querySelectorAll('.kanban-column').forEach(column => {
                if (filter === 'all') {
                    column.classList.remove('hidden');
                } else {
                    if (column.dataset.status === filter) {
                        column.classList.remove('hidden');
                    } else {
                        column.classList.add('hidden');
                    }
                }
            });
        });
    });

    // Dropdown menu toggle
    function toggleMenu(event, button) {
        event.stopPropagation();
        
        const menu = button.nextElementSibling;
        const allMenus = document.querySelectorAll('.dropdown-menu');
        
        allMenus.forEach(m => {
            if (m !== menu) {
                m.classList.add('hidden');
            }
        });
        
        if (menu.classList.contains('hidden')) {
            const rect = button.getBoundingClientRect();
            menu.style.position = 'fixed';
            menu.style.top = (rect.bottom + 2) + 'px';
            menu.style.left = rect.left + 'px';
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    }

    // Close dropdowns on click outside
    document.addEventListener('click', function(event) {
        const allMenus = document.querySelectorAll('.dropdown-menu');
        allMenus.forEach(menu => {
            menu.classList.add('hidden');
        });
    });

    // Close dropdowns on scroll
    document.querySelectorAll('.overflow-y-scroll').forEach(container => {
        container.addEventListener('scroll', function() {
            const allMenus = document.querySelectorAll('.dropdown-menu');
            allMenus.forEach(menu => {
                menu.classList.add('hidden');
            });
        });
    });
    </script>
</x-app-layout>