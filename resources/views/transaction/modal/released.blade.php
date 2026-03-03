<x-modal name="released" maxWidth="max-w-[500px]" focusable>
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
            <x-lucide-circle-alert class="w-20 h-20 sm:w-24 sm:h-24 text-green-600 stroke-[1]"/>
        </div>

        {{-- Confirmation Title --}}
        <h2 class="text-xl font-semibold text-slate-700 text-center mb-2">
            Are you sure?
        </h2>

        {{-- Confirmation Message --}}
        <p class="text-slate-400 text-m font-semibold text-center mb-8 italic">
            Do you want to release this document request?
        </p>

        {{-- Action Buttons --}}
        <div class="sticky bottom-0 bg-white pt-2 flex justify-end gap-3">
            <x-secondary-button x-on:click.prevent="$dispatch('close')">Cancel</x-secondary-button>
            
            <x-primary-button class="ms-3" type="button" @click="releaseDocument()">
                Confirm
            </x-primary-button>
        </div>
    </div>
</x-modal>

<script>
let requestIdForReleased = null;

document.addEventListener('open-released-modal', function(e) {
    requestIdForReleased = e.detail.requestId;
    console.log('Released modal opened with requestId:', requestIdForReleased);
    
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'released' }));
    }, 100);
});

function releaseDocument() {
    if (!requestIdForReleased) return;
    
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
            request_id: requestIdForReleased,
            date_of_release: mysqlDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'released' }));
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('set-success-message', { detail: 'Document successfully released!' }));
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'success-modal' }));
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