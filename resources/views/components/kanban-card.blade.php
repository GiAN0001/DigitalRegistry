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
    'reason' => null
])

<div class="hidden bg-gray-200 bg-yellow-200 bg-green-200 bg-red-200 text-gray-700 text-amber-800 text-green-800 text-red-700"></div>

<div class="bg-slate-50 rounded-xl p-8 shadow-md">
    <div class="flex justify-between items-start mb-8 relative z-10">
        <div>
            <p class="text-xs font-semibold text-slate-500">Request ID: {{ $requestId }}</p>
            <p class="font-bold text-base">{{ $name }}</p>
        </div>
        
        @if(!in_array(strtolower($status), ['released', 'cancelled']))
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-800 text-2xl font-bold mt-1 hover:text-gray-600 relative z-20">⋮</button>

                <!-- Dropdown Menu -->
                <div class="fixed bg-white rounded-xl shadow-xl border border-gray-200 w-36 py-1 z-[99999]" x-show="open" @click.outside="open = false" x-transition>
                    
                    @if(strtolower($status) === 'pending')
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false">Edit</button>
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-signed-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">Sign</button>
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-cancelled-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">Cancel</button>
                    @endif

                    @if(strtolower($status) === 'signed')
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-released-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">Release</button>
                        <button type="button" class="w-full text-left px-4 py-3 hover:bg-gray-100 text-xs font-medium" @click="open = false; document.dispatchEvent(new CustomEvent('open-cancelled-modal', { detail: { requestId: parseInt('{{ $requestId }}', 10) } }))">Cancel</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <div class="text-sm space-y-1 mb-8">
        <p class="text-sm"><span class="font-semibold">Document Type:</span> {{ $documentType }}</p>
        <p class="text-sm"><span class="font-semibold">Fee:</span> ₱ {{ number_format($fee, 2) }}</p>
        
        @if($releasedBy)
            <p class="text-sm"><span class="font-semibold">Released By:</span> {{ $releasedBy }}</p>
        @endif
        
        @if($rejectedBy)
            <p class="text-sm"><span class="font-semibold">Rejected By:</span> {{ $rejectedBy }}</p>
        @endif
        
        @if($reason)
            <p class="text-sm"><span class="font-semibold">Reason:</span> {{ $reason }}</p>
        @endif
        
        <p class="text-xs font-semibold text-slate-500 cursor-pointer hover:text-blue-600">View Details</p>
    </div>
    
    <div>
        <p class="font-semibold text-sm mb-2">Staff: {{ $staff }}</p>
        <div class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-2 rounded-lg {{ $dateColor }} {{ $textColor }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $date }}
        </div>
    </div>
</div>