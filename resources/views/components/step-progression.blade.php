@props(['currentStep' => 1])

@php
$steps = [
    ['number' => 1, 'label' => 'Your details'],
    ['number' => 2, 'label' => 'Household'],
    ['number' => 3, 'label' => 'Family'],
    ['number' => 4, 'label' => 'Pets'],
];
@endphp

<div class="w-full max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between">
        @foreach($steps as $index => $step)
            {{-- Step Circle and Label --}}
            <div class="flex flex-col items-center relative z-10">
                {{-- Circle --}}
                <div class="flex items-center justify-center w-16 h-16 rounded-full border-2 transition-all duration-300
                    {{ $currentStep == $step['number'] ? 'border-blue-500 bg-white' : '' }}
                    {{ $currentStep > $step['number'] ? 'border-blue-500 bg-blue-500' : '' }}
                    {{ $currentStep < $step['number'] ? 'border-gray-300 bg-white' : '' }}">
                    
                    @if($currentStep > $step['number'])
                        {{-- Checkmark for completed steps --}}
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        {{-- Step number --}}
                        <span class="text-2xl font-semibold
                            {{ $currentStep == $step['number'] ? 'text-blue-500' : 'text-gray-400' }}">
                            {{ $step['number'] }}
                        </span>
                    @endif
                </div>
                
                {{-- Label --}}
                <span class="mt-3 text-sm font-medium
                    {{ $currentStep >= $step['number'] ? 'text-blue-500' : 'text-gray-400' }}">
                    {{ $step['label'] }}
                </span>
            </div>
            
            {{-- Connecting Line --}}
            @if($index < count($steps) - 1)
                <div class="flex-1 h-1 mx-4 transition-all duration-300 relative" style="top: -24px;">
                    <div class="h-full rounded-full {{ $currentStep > $step['number'] ? 'bg-blue-500' : 'bg-gray-300' }}"></div>
                </div>
            @endif
        @endforeach
    </div>
</div>
