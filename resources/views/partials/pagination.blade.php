{{-- Pagination Partial Component --}}
{{-- Usage: @include('partials.pagination', ['paginator' => $items, 'perPage' => $perPage ?? 10]) --}}

@php
    $perPage = $perPage ?? request('per_page', 10);
@endphp

<div class="pagination-wrapper">
    <div class="per-page-selector">
        <label>Show:</label>
        <select onchange="updatePerPage(this.value)">
            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
        </select>
    </div>
    <div class="custom-pagination">
        @if($paginator->onFirstPage())
            <span class="page-btn disabled">&lsaquo; Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">&lsaquo; Prev</a>
        @endif
        
        <span class="page-info">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
        
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-btn next">Next &rsaquo;</a>
        @else
            <span class="page-btn disabled next">Next &rsaquo;</span>
        @endif
    </div>
</div>

@once
<script>
function updatePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
</script>
@endonce
