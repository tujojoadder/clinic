@php
    $type = $field[1];
    $value = $type === 'password' ? '' : old($name, $value ?? null);
    if ($type === 'datetime-local') {
        $value = $value ? \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
    }
    if ($type === 'date' && !$value && $name !== 'birth_date') {
        $value = now()->format('Y-m-d');
    }
    if ($type === 'time' && $value) {
        $value = substr($value, 0, 5);
    }
@endphp
<div>
    <label for="{{ $name }}">{{ $field[0] }}{{ $field[2] ? ' *' : '' }}</label>

    @if (in_array($type, ['select', 'fk']))
        @php $options = $type === 'select' ? $field[3] : \App\Support\Registry::options($field[3]); @endphp
        <select id="{{ $name }}" name="{{ $name }}" @required($field[2])>

            @if (!$field[2])
                <option value="">নির্বাচন করুন</option>
            @endif

            @foreach ($options as $key => $label)
                <option value="{{ $key }}" @selected((string) $value === (string) $key)>{{ $label }}</option>
            @endforeach
        </select>
    @elseif($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" @required($field[2])>{{ $value }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            value="{{ $value }}" @required($field[2]) @if ($type === 'number') min="0" step="0.01" @endif>

    @endif
</div>
