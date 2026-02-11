<x-modal name="for-released" maxWidth="max-w-[500px]" focusable>
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
            <x-lucide-circle-alert class="w-20 h-20 sm:w-24 sm:h-24 text-orange-600 stroke-[1]"/>
        </div>

        {{-- Confirmation Title --}}
        <h2 class="text-xl font-semibold text-slate-700 text-center mb-2">
            Are you sure?
        </h2>

        {{-- Confirmation Message --}}
        <p class="text-slate-400 text-m font-semibold text-center mb-8 italic">
            Do you want to transfer this document request for release?
        </p>

        <input type="hidden" id="request_id" value="" />

        {{-- Action Buttons --}}
        <div class="sticky bottom-0 bg-white pt-2 flex justify-end gap-3">
            <x-secondary-button x-on:click.prevent="$dispatch('close')">Cancel</x-secondary-button>
            
            <x-primary-button class="ms-3" type="button" @click="transferForRelease()">
                Confirm
            </x-primary-button>
        </div>
    </div>
</x-modal>

<script>
let requestIdForRelease = null;

document.addEventListener('open-release-modal', function(e) {
    requestIdForRelease = e.detail.requestId;
    console.log('For Release modal opened with requestId:', requestIdForRelease);
    
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'for-released' }));
    }, 100);
});

function transferForRelease() {
    if (!requestIdForRelease) return;
    
    const now = new Date();
    const mysqlDate = now.getFullYear() + '-' + 
                      String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                      String(now.getDate()).padStart(2, '0') + ' ' +
                      String(now.getHours()).padStart(2, '0') + ':' +
                      String(now.getMinutes()).padStart(2, '0') + ':' +
                      String(now.getSeconds()).padStart(2, '0');
    
    console.log('Sending data:', {
        request_id: requestIdForRelease,
        for_release_at: mysqlDate
    });
    
    fetch('{{ route("document.transfer-for-release") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            request_id: requestIdForRelease,
            for_release_at: mysqlDate
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Document transferred for release:', data);
        if (data.success) {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'for-released' }));
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
        console.error('Error transferring document:', error);
        alert('Error transferring document: ' + error.message);
    });
}
</script>