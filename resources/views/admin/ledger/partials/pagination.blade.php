{{-- Links already carry the full query (withQueryString + appends), so the
     AJAX layer can fetch them directly. The id keeps the fragment addressable
     across outerHTML swaps. --}}
<div id="ledger-pagination" class="border-t border-indigo-100 px-4 py-3">
    {{ $transactions->appends(request()->query())->links() }}
</div>
