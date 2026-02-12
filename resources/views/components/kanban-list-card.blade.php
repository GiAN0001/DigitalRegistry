@props([
    'requestId' => '',
    'name' => '',
    'documentType' => '',
    'fee' => '0.00',
    'staff' => '',
    'date' => '',
    'status' => 'pending',
    'dateColor' => 'bg-gray-200',
    'textColor' => 'text-gray-700',
    'releasedBy' => null,
    'rejectedBy' => null,
    'reason' => null,
])

<div class="hidden bg-gray-200 bg-yellow-200 bg-green-200 bg-red-200 bg-blue-200 bg-orange-200 text-gray-700 text-amber-800 text-green-800 text-red-700 text-blue-800 text-orange-800"></div>

<div class="bg-slate-50 rounded-xl p-6 shadow-md border border-gray-100">
    <div class="flex justify-between items-start gap-6 mb-2">
        <!-- Left Section: Request Info -->
        <div class="flex-1">
            <p class="text-xs font-semibold text-slate-500 mb-1">Request ID: {{ $requestId }}</p>
            <p class="font-bold text-base mb-8">{{ $name }}</p>
            
            <!-- Staff and Date Info -->
            <p class="text-sm mb-2"><span class="font-semibold">Staff:</span> {{ $staff }}</p>
            <div class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-2 rounded-lg {{ $dateColor }} {{ $textColor }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $date }}
            </div>
        </div>
        
        <!-- Center-Left Section: Document Details -->
        <div class="flex-1 text-left">
            <p class="text-sm mb-2"><span class="font-semibold">Document Type:</span> {{ $documentType }}</p>
            <p class="text-sm mb-6"><span class="font-semibold">Fee:</span> ₱ {{ number_format($fee, 2) }}</p>
            
            @if($releasedBy)
                <p class="text-sm mb-3"><span class="font-semibold">Released By:</span> {{ $releasedBy }}</p>
            @endif
            
            @if($rejectedBy)
                <p class="text-sm mb-3"><span class="font-semibold">Cancelled By:</span> {{ $rejectedBy }}</p>
            @endif
            
            <p 
                class="text-xs font-semibold text-slate-500 cursor-pointer hover:text-blue-600 transition-colors mt-2" 
                x-data
                @click="
                    const requestId = parseInt('{{ $requestId }}');
                    console.log('Fetching request ID:', requestId);
                    fetch(`/document-requests/${requestId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) throw new Error('Failed to fetch, status: ' + response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Loaded request:', data);
                            $dispatch('open-modal', 'view-request');
                            setTimeout(() => {
                                window.dispatchEvent(new CustomEvent('load-request', { detail: data }));
                            }, 100);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load request details: ' + error.message);
                        })
                "
            >
                View Details
            </p>
        </div>
        
        <!-- Actions Menu -->
        @if(!in_array(strtolower($status), ['released', 'cancelled']))
            <div x-data="{ open: false, posX: 0, posY: 0 }">
                <button 
                    @click="
                        const rect = $el.getBoundingClientRect();
                        posX = rect.left - 200;
                        posY = rect.top;
                        open = !open;
                    " 
                    class="text-gray-800 text-2xl font-bold hover:text-gray-600 flex-shrink-0"
                >⋮</button>

                <!-- Dropdown Menu -->
                <div 
                    class="fixed bg-white rounded-xl shadow-2xl border border-gray-200 w-48 py-1"
                    x-show="open" 
                    @click.outside="open = false" 
                    x-transition
                    :style="`left: ${posX}px; top: ${posY}px; z-index: 9999;`"
                >
                    
                    @if(strtolower($status) === 'for fulfillment')
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-signed-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">For Signature</button>
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="
                            open = false;
                            fetch(`/document-requests/${parseInt('{{ $requestId }}', 10)}`, {
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                            })
                            .then(r => r.json())
                            .then(data => {
                                $dispatch('open-modal', 'edit-request');
                                setTimeout(() => { window.dispatchEvent(new CustomEvent('load-edit-request', { detail: data })); }, 100);
                            })
                            .catch(e => alert('Failed to load request: ' + e.message));
                        ">Edit</button>
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-cancelled-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">Cancel</button>
                    @endif

                    @if(strtolower($status) === 'for signature')
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-release-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">For Release</button>
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-cancelled-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">Cancel</button>
                    @endif

                    @if(strtolower($status) === 'for release')
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-released-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">Release</button>
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-cancelled-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">Cancel</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>