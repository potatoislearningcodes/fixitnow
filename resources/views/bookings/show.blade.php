<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                @php
                    $badge = match ($booking->status) {
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-gray-100 text-gray-600',
                        'accepted', 'in_progress' => 'bg-blue-100 text-blue-800',
                        default => 'bg-yellow-100 text-yellow-800',
                    };
                @endphp
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>

                <h3 class="mt-3 text-lg font-semibold text-gray-800">{{ $booking->service_type }}</h3>

                <dl class="mt-4 space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Technician') }}</dt>
                        <dd class="text-gray-800">{{ $booking->technician?->user->name ?? __('Not assigned') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Address') }}</dt>
                        <dd class="text-gray-800">{{ $booking->address }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Scheduled for') }}</dt>
                        <dd class="text-gray-800">{{ $booking->scheduled_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    @if ($booking->description)
                        <div>
                            <dt class="text-gray-500">{{ __('Notes') }}</dt>
                            <dd class="text-gray-800">{{ $booking->description }}</dd>
                        </div>
                    @endif
                </dl>

                @if (auth()->user()->isTechnician() && in_array($booking->status, ['pending', 'accepted', 'in_progress']))
                    <form method="POST" action="{{ route('bookings.update-status', $booking) }}" class="mt-6 flex gap-2 flex-wrap">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="border-gray-300 rounded-md text-sm">
                            <option value="accepted" @selected($booking->status === 'accepted')>{{ __('Accepted') }}</option>
                            <option value="in_progress" @selected($booking->status === 'in_progress')>{{ __('In progress') }}</option>
                            <option value="completed">{{ __('Completed') }}</option>
                            <option value="cancelled">{{ __('Cancelled') }}</option>
                        </select>
                        <x-primary-button>{{ __('Update status') }}</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
