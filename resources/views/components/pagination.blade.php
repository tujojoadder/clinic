@props(['paginator'])

@if ($paginator->total() > 0)
    <div class="pager noprint">
        <div class="pager-info">
            মোট {{ $paginator->total() }} টির মধ্যে {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
        </div>

        {{ $paginator->withQueryString()->links('pagination::bootstrap-4') }}
    </div>
@endif
