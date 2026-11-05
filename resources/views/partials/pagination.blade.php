@if ($paginator->hasPages())
<nav class="pager" aria-label="Pages">
  @if ($paginator->onFirstPage())<span class="btn btn--outline btn--sm" aria-disabled="true">Previous</span>@else<a class="btn btn--outline btn--sm" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>@endif
  <span class="pager__info">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
  @if ($paginator->hasMorePages())<a class="btn btn--outline btn--sm" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>@else<span class="btn btn--outline btn--sm" aria-disabled="true">Next</span>@endif
</nav>
@endif
