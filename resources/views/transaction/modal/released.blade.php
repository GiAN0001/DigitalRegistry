<x-modal name="released" maxWidth="max-w-[500px]" focusable>
    <div class="p-8 sm:p-12">
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
            Do you want to release this document request?
        </p>

        <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-6">
                <button type="button" class="py-3 text-gray-600 hover:text-gray-800" @click="$dispatch('close')">Cancel</button>
                <button type="button" @click="releaseDocument()" class="px-6 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow-md">
                    Confirm
                </button>
            </div>
    </div>
</x-modal>

<script>
let requestIdToRelease = null;

document.addEventListener('open-released-modal', function(e) {
    console.log('Opening released modal with:', e.detail);
    requestIdToRelease = e.detail.requestId;
    
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'released' }));
    }, 100);
});

function releaseDocument() {
    if (!requestIdToRelease) return;
    
    const now = new Date();
    const mysqlDate = now.getFullYear() + '-' + 
                      String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                      String(now.getDate()).padStart(2, '0') + ' ' +
                      String(now.getHours()).padStart(2, '0') + ':' +
                      String(now.getMinutes()).padStart(2, '0') + ':' +
                      String(now.getSeconds()).padStart(2, '0');
    
    fetch('{{ route("document.release") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            request_id: requestIdToRelease,
            date_released: mysqlDate
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Document released:', data);
        if (data.success) {
            // Close the release modal
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'released' }));
            // Open the success modal
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'success' }));
                // Reload page after success modal is shown
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }, 300);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error releasing document:', error);
        alert('Error releasing document: ' + error.message);
    });
}
</script>

