<div class="flex justify-between items-center gap-6 p-4 bg-white rounded-lg shadow-sm">


    <div class="">
        <p class="text-xs font-medium text-slate-500 tracking-wider mb-1 ">
            {{ $title }}
        </p>
        <p class="text-2xl font-bold text-slate-700">
            {{ number_format($value) }}
        </p>
    </div>

    <div class="{{ $bgColor }} w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-md shadow-slate-200">

        <x-dynamic-component
            :component="'lucide-' . $iconName"
            class="w-6 h-6"
        />

    </div>

</div>
