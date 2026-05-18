<x-layouts.admin>

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">
    <h1 class="text-xl font-semibold text-indigo-900 dark:text-indigo-100 mb-6">All Notifications</h1>

    @forelse ($notifications as $n)
        <div class="flex items-start gap-4 p-4 mb-3 rounded-xl border
                    {{ $n->read_at ? 'bg-white dark:bg-indigo-900 border-indigo-100 dark:border-indigo-800'
                                   : 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800' }}">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
                    {{ $n->data['title'] }}
                </p>
                <p class="text-xs text-indigo-500 dark:text-indigo-400 mt-0.5">
                    {{ $n->data['body'] }}
                </p>
                <p class="text-[11px] text-indigo-400 mt-1">
                    {{ $n->created_at->diffForHumans() }}
                </p>
            </div>
            @unless($n->read_at)
                <span class="mt-1.5 w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
            @endunless
        </div>
    @empty
        <p class="text-center text-indigo-400 py-12">No notifications yet.</p>
    @endforelse

    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
</x-layouts.admin>