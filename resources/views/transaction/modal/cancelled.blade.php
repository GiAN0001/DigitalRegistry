<x-modal name="cancelled" maxWidth="max-w-[500px]" focusable class="flex items-center justify-center">
    <div class="p-8 sm:p-8">
        {{-- Close Button (X) --}}
        <div class="flex justify-end mb-4">
            <button
                type="button"
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition-colors"
            >
                <x-lucide-x class="w-6 h-6"/>
            </button>
        </div>

        {{-- Warning Icon --}}
        <div class="flex justify-center mb-4">
            <x-lucide-circle-alert class="w-20 h-20 sm:w-24 sm:h-24 text-red-600 stroke-[1]"/>
        </div>

        {{-- Confirmation Title --}}
        <h2 class="text-xl font-semibold text-slate-700 text-center mb-2">
            Are you sure?
        </h2>

        {{-- Confirmation Message --}}
        <p class="text-slate-400 text-m font-semibold text-center mb-8 italic">
            Do you want to cancel this document request?
        </p>

        {{-- Reason Label --}}
        <div class="mb-2">
            <label class="text-base font-semibold text-gray-900">
                Reason <span class="text-red-500">*</span>
            </label>
        </div>

        {{-- Textarea --}}
        <textarea 
            id="cancellation_reason" 
            name="cancellation_reason" 
            rows="4" 
            class="w-full px-4 py-3 text-sm text-gray-700 border border-gray-300 rounded-lg focus:border-blue-700 focus:ring-blue-700 focus:ring-1 placeholder:text-gray-400 mb-6" 
            placeholder="Enter reason for cancellation"
            autocomplete="off"></textarea>

        {{-- Action Buttons --}}
        <div class="sticky bottom-0 bg-white pt-2 flex justify-end gap-3">
            <x-secondary-button @click="$dispatch('close')">Cancel</x-secondary-button>
            
            <x-primary-button class="ms-3" type="button" @click="cancelDocument()">
                Confirm
            </x-primary-button>
        </div>
    </div>
</x-modal>

<script>
let requestIdToCancel = null;

document.addEventListener('open-cancelled-modal', function(e) {
    console.log('Opening cancelled modal with:', e.detail);
    requestIdToCancel = e.detail.requestId;
    
    // Clear the textarea
    document.getElementById('cancellation_reason').value = '';
    
    // Use the modal opening mechanism
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'cancelled' }));
    }, 100);
});

function cancelDocument() {
    if (!requestIdToCancel) {
        alert('No request ID found');
        return;
    }
    
    const reason = document.getElementById('cancellation_reason').value;
    if (!reason.trim()) {
        alert('Please enter a reason for cancellation');
        return;
    }
    
    const now = new Date();
    const mysqlDate = now.getFullYear() + '-' + 
                      String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                      String(now.getDate()).padStart(2, '0') + ' ' +
                      String(now.getHours()).padStart(2, '0') + ':' +
                      String(now.getMinutes()).padStart(2, '0') + ':' +
                      String(now.getSeconds()).padStart(2, '0');
    
    fetch('{{ route("document.cancel") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            request_id: requestIdToCancel,
            remarks: reason,
            date_of_cancel: mysqlDate
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Document cancelled:', data);
        if (data.success) {
            // Close modal
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'cancelled' }));
            
            // Reload page without alert
            setTimeout(() => {
                window.location.reload();
            }, 300);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error cancelling document:', error);
        alert('Error cancelling document: ' + error.message);
    });
}
</script>