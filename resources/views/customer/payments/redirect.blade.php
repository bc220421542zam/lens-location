<x-layouts.customer>
<div class="flex flex-col items-center justify-center py-20">
    <p class="text-indigo-900 font-medium mb-4">Redirecting to JazzCash...</p>
    <form id="jazzcash-form" method="POST" action="{{ $url }}">
        @foreach($data as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>
</div>
<script>document.getElementById('jazzcash-form').submit();</script>
</x-layouts.customer>