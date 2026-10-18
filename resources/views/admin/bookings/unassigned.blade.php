<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Unassigned Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @forelse ($unassignedBookings as $booking)
                    <div class="p-6 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-start justify-between flex-wrap gap-4">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $booking->service_type }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $booking->buildingTypeLabel() }} · {{ ucfirst($booking->service_group) }}
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

                            <form method="POST" action="{{ route('admin.bookings.assign', $booking) }}" class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="technician_id" required class="border-gray-300 rounded-md text-sm">
                                    <option value="">{{ __('Assign to...') }}</option>
                                    @foreach ($availableTechnicians as $technician)
                                        <option value="{{ $technician->id }}">
                                            {{ $technician->user->name }} ({{ $technician->specialty }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-primary-button>{{ __('Assign') }}</x-primary-button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-gray-500">{{ __('No unassigned bookings right now.') }}</p>
                @endforelse
            </div>

            @if ($availableTechnicians->isEmpty())
                <p class="mt-4 text-sm text-amber-600">
                    {{ __('No technicians are currently available — all approved technicians are busy on another job.') }}
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
