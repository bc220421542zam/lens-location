@if (session('success'))
    <div class="mb-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
        Please fix the errors below and try again.
    </div>
@endif