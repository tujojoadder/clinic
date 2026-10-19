@props(['paginator'])

@php $sizes = [20, 50, 100]; @endphp

<label class="per-page noprint">
    প্রতি পেজে
    <select data-per-page>
        @foreach ($sizes as $size)
            <option value="{{ $size }}" @selected($paginator->perPage() == $size)>{{ $size }}</option>
        @endforeach
    </select>
</label>
