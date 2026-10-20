@php
    $type = $field[1];
    $value = $type === 'password' ? '' : old($name, $value ?? null);
    if ($type === 'datetime-local') {
        // ডাটাবেস মান → 15-10-2026 02:30 PM (ফাঁকা হলে এখনকার সময়)
        $value = $value ? datetime_to_view($value) : now()->format('d-m-Y h:i A');
    }
    if ($type === 'date') {
        // ডাটাবেস মান → 15-10-2026 (ফাঁকা হলে আজ, শুধু birth_date ছাড়া)
        $value = $value ? date_to_view($value) : ($name !== 'birth_date' ? now()->format('d-m-Y') : '');
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
    @elseif(in_array($type, ['date', 'datetime-local']))
        <input id="{{ $name }}" name="{{ $name }}" type="text"
            class="{{ $type === 'date' ? 'js-date' : 'js-datetime' }}" value="{{ $value }}" @required($field[2])>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
            value="{{ $value }}" @required($field[2]) @if ($type === 'number') min="0" step="0.01" @endif>

    @endif
</div>
