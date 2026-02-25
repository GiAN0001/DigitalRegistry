@props(['disabled' => false, 'value' => ''])

<div class="relative inline-block w-auto h-10">
    <input 
        type="date" 
        name="date" {{-- <-- Added by Cath --}}
        {{ $disabled ? 'disabled' : '' }} 
        {!! $attributes->merge(['class' => 'block w-full h-10 pl-10 pr-4 py-2 text-sm font-normal text-slate-500 bg-slate-50 border-none rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-slate-200 date-input']) !!}
        value="{{ $value }}"
        onchange="this.classList.toggle('has-value', this.value !== '')"
        onclick="this.showPicker()"
    >
    <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none flex items-center gap-2">
        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="date-placeholder-text text-slate-400 text-sm">Select Date</span>
    </div>
</div>

<style>
    /* Hide placeholder text when date has value or is focused */
    .date-input.has-value ~ div .date-placeholder-text,
    .date-input:focus ~ div .date-placeholder-text {
        display: none;
    }
    
    /* Hide the text when no date is selected */
    .date-input:not(.has-value):not(:focus) {
        color: transparent;
    }
    
    /* Show text color when date is selected */
    .date-input.has-value {
        color: inherit;
    }
    
    /* Hide the default calendar icon */
    .date-input::-webkit-calendar-picker-indicator {
        display: none;
    }
    .date-input::-webkit-inner-spin-button,
    .date-input::-webkit-clear-button {
        display: none;
    }
    
    /* Hide the date format placeholder text and prevent selection */
    .date-input::-webkit-datetime-edit-text,
    .date-input::-webkit-datetime-edit-month-field,
    .date-input::-webkit-datetime-edit-day-field,
    .date-input::-webkit-datetime-edit-year-field {
        color: transparent;
        background: transparent;
    }
    
    /* Prevent selection highlight on date parts */
    .date-input::-webkit-datetime-edit-text:focus,
    .date-input::-webkit-datetime-edit-month-field:focus,
    .date-input::-webkit-datetime-edit-day-field:focus,
    .date-input::-webkit-datetime-edit-year-field:focus {
        background: transparent;
        color: transparent;
    }
    
    /* Show the date parts only when it has a value */
    .date-input.has-value::-webkit-datetime-edit-text,
    .date-input.has-value::-webkit-datetime-edit-month-field,
    .date-input.has-value::-webkit-datetime-edit-day-field,
    .date-input.has-value::-webkit-datetime-edit-year-field {
        color: inherit;
    }
</style>