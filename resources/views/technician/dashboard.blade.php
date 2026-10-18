<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Jobs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            {{-- Availability banner --}}
            <div class="bg-white shadow-sm rounded-lg p-4 flex items-center justify-between">
                <p class="text-sm text-gray-600">{{ __('Your current status:') }}</p>
                @if ($technician->isAvailable())
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        {{ __('Available') }}
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                        {{ __('Busy — on an active job') }}
                    </span>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @forelse ($bookings as $booking)
                    <div class="p-6 border-b border-gray-100 last:border-b-0">
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
                                <p class="text-sm text-gray-600 mt-1">{{ __('Customer:') }} {{ $booking->customer->name }}</p>
                                <p class="text-sm text-gray-600">{{ __('Address:') }} {{ $booking->address }}</p>
                                @if ($booking->rooms)
                                    <p class="text-sm text-gray-600">{{ __('Rooms:') }} {{ $booking->rooms }}</p>
                                @endif
                                <p class="text-sm text-gray-600">
                                    {{ __('Scheduled:') }} {{ $booking->scheduled_at->format('M d, Y g:i A') }}
                                </p>
                            </div>

                            <div class="flex flex-col items-end gap-2 shrink-0">
                                <a href="{{ route('bookings.show', $booking) }}"
                                    class="text-sm text-indigo-600 underline">
                                    {{ __('View full details') }}
                                </a>

                                @if (in_array($booking->status, ['pending', 'accepted', 'in_progress']))
                                    <form method="POST" action="{{ route('bookings.update-status', $booking) }}" class="flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="border-gray-300 rounded-md text-sm">
                                            <option value="accepted" @selected($booking->status === 'accepted')>{{ __('Accepted') }}</option>
                                            <option value="in_progress" @selected($booking->status === 'in_progress')>{{ __('On-going') }}</option>
                                            <option value="completed">{{ __('Repaired') }}</option>
                                            <option value="cancelled">{{ __('Cancelled') }}</option>
                                        </select>
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-gray-800 text-white text-xs font-semibold rounded-md hover:bg-gray-700">
                                            {{ __('Update') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-gray-500">
                        {{ __('No jobs assigned to you yet. An admin will assign one, or a customer may request you directly.') }}
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
