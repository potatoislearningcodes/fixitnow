<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-end">
                <a href="{{ route('bookings.create') }}"
                    class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
                    {{ __('Book another service') }}
                </a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @forelse ($bookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}"
                        class="block p-6 border-b border-gray-100 last:border-b-0 hover:bg-gray-50">
                        <div class="flex items-start justify-between flex-wrap gap-4">
                            <div>
                                @php
                                    $badge = match ($booking->status) {
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-gray-100 text-gray-600',
                                        'accepted', 'in_progress' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ $booking->statusLabel() }}
                                </span>

                                <p class="font-semibold text-gray-800 mt-2">{{ $booking->service_type }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $booking->buildingTypeLabel() }} · {{ ucfirst($booking->service_group ?? '—') }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ __('Technician:') }} {{ $booking->technician?->user->name ?? __('Not yet assigned') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ __('Scheduled:') }} {{ $booking->scheduled_at->format('M d, Y g:i A') }}
                                </p>
                            </div>

                            <span class="text-sm text-indigo-600 shrink-0">{{ __('View →') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500 mb-4">
                            {{ __('You haven\'t booked a service yet.') }}
                        </p>
                        <a href="{{ route('bookings.create') }}"
                            class="inline-block px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
                            {{ __('Book your first service') }}
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
