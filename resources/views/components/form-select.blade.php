<select {{ $attributes->merge(['class' => '
    w-full h-10
    text-sm text-gray-700
    border border-gray-300 rounded-md shadow-sm
    focus:border-blue-700 focus:ring-blue-700 focus:ring-1
    placeholder:text-gray-400
']) }}>
    
    <option value="" disabled @if(is_null($selected) || $selected === '') selected @endif>{{ $placeholder }}</option>

    @foreach($options as $value => $label)
        <option value="{{ $value }}" @if($selected !== null && $selected !== '' && $value == $selected) selected @endif>{{ $label }}</option>
    @endforeach

</select>