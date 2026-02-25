<x-modal name="success-message" maxWidth="max-w-sm" focusable>
    <div class="p-8 text-center">
        <div class="mb-4">
            <x-lucide-check-circle class="w-12 h-12 text-green-500 mx-auto"/>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Success!</h2>
        <x-primary-button @click="$dispatch('close'); window.location.reload();">OK</x-primary-button>
    </div>
</x-modal>