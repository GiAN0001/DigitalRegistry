<x-modal name="cancelled" maxWidth="max-w-[500px]" focusable>
    <div class="p-8">
        {{-- Header with Title and Close Button --}}
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-4xl font-bold text-gray-900">Cancelled</h2>
            <button
                type="button"
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition-colors"
            >
                <x-lucide-x class="w-7 h-7"/>
            </button>
        </div>

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
            rows="6" 
            class="w-full px-4 py-3 text-base text-gray-700 rounded-2xl focus:border-blue-600 focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder:text-gray-400" 
            placeholder="Reason for Rejecting"
            autocomplete="off"></textarea>

        <input type="hidden" id="request_id" value="" />

        {{-- Action Buttons --}}
        <div class="flex justify-between items-center mt-6">
            <button type="button" class="py-3 text-gray-600 hover:text-gray-800" @click="$dispatch('close')">Cancel</button>
            <button type="button" @click="cancelDocument()" class="px-6 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition shadow-md">
                Confirm
            </button>
        </div>
    </div>
</x-modal>

<script>
let requestIdToCancel = null;

document.addEventListener('open-cancelled-modal', function(e) {
    requestIdToCancel = e.detail.requestId;
    document.getElementById('request_id').value = e.detail.requestId || '';
    document.getElementById('cancellation_reason').value = '';
    
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'cancelled' }));
    }, 100);
});

function cancelDocument() {
    if (!requestIdToCancel) return;
    
    const reason = document.getElementById('cancellation_reason').value;
    if (!reason.trim()) {
        alert('Please enter a reason for cancellation');
        return;
    }
    
    // Convert to MySQL datetime format (YYYY-MM-DD HH:MM:SS)
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
        if (data.success) {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'cancelled' }));
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'success' }));
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }, 300);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error cancelling document: ' + error.message);
    });
}
</script>