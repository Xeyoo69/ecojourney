@if ($paginator->hasPages())
    @php
        $window = 1;
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        $pages = [];
        for ($i = 1; $i <= $last; $i++) {
            if ($i === 1 || $i === $last || abs($i - $current) <= $window) {
                $pages[] = $i;
            }
        }

        $segments = [];
        foreach ($pages as $page) {
            $previous = end($segments);
            if ($previous !== null && $page === $previous + 1) {
                if ($page - $previous > 1) {
                    $segments[] = '…';
                }
                $segments[] = $page;
            } else {
                if ($segments !== [] && end($segments) !== '…' && $page !== 1) {
                    $segments[] = '…';
                }
                $segments[] = $page;
            }
        }
    @endphp

    <nav class="flex flex-wrap items-center justify-between gap-4 pt-6" aria-label="Pagination">
        <p class="text-xs font-semibold uppercase tracking-wider text-[color:var(--muted)]">
            @if ($paginator->total() > 0)
                Showing {{ number_format($paginator->firstItem()) }}–{{ number_format($paginator->lastItem()) }}
                of {{ number_format($paginator->total()) }}
            @endif
        </p>

        <div class="flex flex-wrap items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="pagination-button" aria-disabled="true">&lsaquo; Prev</span>
            @else
                <a class="pagination-button" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo; Prev</a>
            @endif

            @foreach ($segments as $segment)
                @if ($segment === '…')
                    <span class="pagination-button" aria-disabled="true">&hellip;</span>
                @elseif ($segment === $current)
                    <span class="pagination-button pagination-button-active" aria-current="page">{{ $segment }}</span>
                @else
                    <a class="pagination-button" href="{{ $paginator->url($segment) }}">{{ $segment }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="pagination-button" href="{{ $paginator->nextPageUrl() }}" rel="next">Next &rsaquo;</a>
            @else
                <span class="pagination-button" aria-disabled="true">Next &rsaquo;</span>
            @endif
        </div>
    </nav>
@endif
