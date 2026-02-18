<x-app-layout>
    
    <div>
        @livewire('admin.tupad-management')

        @if(session('success'))
            <x-success-modal name="action-success" :show="true">
                {{ session('success') }}
            </x-success-modal>
        @endif
    </div>
    @push('scripts')
    <script>  
        window.onload = function() {
            const url = new URL(window.location.href);
            if (url.searchParams.has('search')) {
                url.searchParams.delete('search');
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
        };
    </script>
    @endpush
</x-app-layout>