<select {{ $attributes->merge(['class' => '
    w-full h-10
    text-sm text-gray-700
    border border-gray-300 rounded-md shadow-sm
    focus:border-blue-700 focus:ring-blue-700 focus:ring-1
    placeholder:text-gray-400
']) }}>
    
    <option value="" disabled selected>{{ $placeholder }}</option>

    @foreach($options as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach

</select>