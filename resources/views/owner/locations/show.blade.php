<x-layouts.owner>
<div class="page">

    <div class="max-w-4xl mx-auto">
         <a href="{{ route('owner.listings') }}" class="text-sm text-indigo-700 hover:underline">
                &larr; Back to listings
            </a>
        <div class="flex items-center justify-between mb-6">
            <p class="title text-indigo-900">Listing Details</p>
        </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-200 text-green-600 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="card chart-transition p-0 overflow-hidden max-w-3xl rounded-2xl ">

        {{-- GALLERY — thumbnails swap the image above them, all in place --}}
        @php($gallery = $location->gallery())
        <div @if(count($gallery)) x-data="{ active: '{{ Storage::url($gallery[0]) }}' }" @endif>

            {{-- COVER --}}
            <div class="relative h-64 sm:h-72 bg-gray-100">
                @if(count($gallery))
                    <img :src="active"
                         src="{{ Storage::url($gallery[0]) }}"
                         alt="{{ $location->title }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-50">
                        <span class="text-5xl text-gray-300">
                            <i class="fa-solid fa-camera"></i>
                        </span>
                    </div>
                @endif

                {{-- STATUS BADGE --}}
                @if($location->status->value === 'approved')
                    <span class="badge badge-active">Approved</span>
                @elseif($location->status->value === 'pending')
                    <span class="badge badge-draft">Pending</span>
                @elseif($location->status->value === 'rejected')
                    <span class="badge badge-rejected">Rejected</span>
                @endif
            </div>

            {{-- THUMBNAILS --}}
            @if(count($gallery) > 1)
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 p-3 border-b border-gray-100">
                    @foreach($gallery as $path)
                        @php($url = Storage::url($path))
                        <button type="button"
                            @click="active = '{{ $url }}'"
                            class="rounded-lg overflow-hidden border-2 transition-colors"
                            :class="active === '{{ $url }}' ? 'border-indigo-600' : 'border-transparent hover:border-indigo-300'"
                            aria-label="Show photo {{ $loop->iteration }}">
                            <img src="{{ $url }}"
                                 alt="{{ $location->title }} photo {{ $loop->iteration }}"
                                 class="w-full h-16 object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-indigo-900">{{ $location->title }}</h1>
                <span class="badge-inline badge-active">{{ ucfirst($location->category) }}</span>
            </div>

            <p class="text-sm text-gray-500 mt-1">
                <i class="fa-solid fa-location-dot mr-1"></i>{{ $location->city }}
            </p>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 mb-6">
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Price / hour</dt>
                    <dd class="font-medium text-indigo-900">PKR {{ number_format((float) $location->price_per_hour) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Address</dt>
                    <dd class="font-medium text-indigo-900">{{ $location->address }}</dd>
                </div>
            </dl>

            <div class="mb-6">
                <dt class="text-xs uppercase underline text-indigo-900 mb-1">Description</dt>
                <dd class="text-gray-500 whitespace-pre-line">{{ $location->description }}</dd>
            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-2 btn-transition">
                <a href="{{ route('owner.locations.edit', $location->id) }}"
                   class="action-btn shade text-center flex-1">
                    Edit
                </a>

                <form action="{{ route('owner.locations.destroy', $location->id) }}"
                      method="POST"
                      class="flex-1"
                      onsubmit="return confirm('Are you sure you want to delete this listing?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn btn-transition shade danger w-full">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-layouts.owner>