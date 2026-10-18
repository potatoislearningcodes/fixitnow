<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Request') }}
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $isAssignedTechnician = $user->isTechnician() && $booking->technician_id === $user->technicianProfile?->id;
    @endphp

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="font-medium text-sm text-green-600">{{ session('status') }}</div>
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
                    {{ $booking->statusLabel() }}
                </span>

                <h3 class="mt-3 text-lg font-semibold text-gray-800">{{ $booking->service_type }}</h3>
                <p class="text-sm text-gray-500">
                    {{ $booking->buildingTypeLabel() }} · {{ ucfirst($booking->service_group ?? '—') }}
                </p>

                <dl class="mt-4 space-y-2 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Technician') }}</dt>
                        <dd class="text-gray-800">
                            {{ $booking->technician?->user->name ?? __('Not yet assigned') }}
                        </dd>
                    </div>
                    @if ($booking->rooms)
                        <div>
                            <dt class="text-gray-500">{{ __('Room(s)') }}</dt>
                            <dd class="text-gray-800">{{ $booking->rooms }}</dd>
                        </div>
                    @endif
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

                {{-- Map --}}
                <div class="mt-4">
                    @if ($booking->hasCoordinates())
                        <div id="booking-map" class="w-full rounded-md border border-gray-200" style="height: 250px;"></div>
                        <p class="text-xs text-gray-400 mt-1">{{ __('Approximate location based on the address provided.') }}</p>
                    @else
                        <div class="w-full rounded-md border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-xs text-gray-400" style="height: 100px;">
                            {{ __('Map location not available for this address.') }}
                        </div>
                    @endif
                </div>

                @if ($isAssignedTechnician && in_array($booking->status, ['pending', 'accepted', 'in_progress']))
                    <form method="POST" action="{{ route('bookings.update-status', $booking) }}" class="mt-6 flex gap-2 flex-wrap">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="border-gray-300 rounded-md text-sm">
                            <option value="accepted" @selected($booking->status === 'accepted')>{{ __('Accepted') }}</option>
                            <option value="in_progress" @selected($booking->status === 'in_progress')>{{ __('On-going') }}</option>
                            <option value="completed">{{ __('Repaired') }}</option>
                            <option value="cancelled">{{ __('Cancelled') }}</option>
                        </select>
                        <x-primary-button>{{ __('Update status') }}</x-primary-button>
                    </form>
                @endif
            </div>

            {{-- Payment section: visible once the job is Repaired (completed) --}}
            @if ($booking->status === 'completed' && ($isAssignedTechnician || $user->isAdmin()))
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-1">{{ __('Payment') }}</h3>

                    @if ($booking->payment && $booking->payment->status === 'paid')
                        <p class="text-sm text-green-700 mt-2">
                            {{ __('Paid via') }} {{ $booking->payment->methodLabel() }}
                            @if ($booking->payment->reference_number)
                                — {{ __('Ref:') }} {{ $booking->payment->reference_number }}
                            @endif
                            ({{ __('₱') }}{{ number_format($booking->payment->amount, 2) }})
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mb-4">
                            {{ __('Record how the customer paid for this completed job.') }}
                        </p>
                        <form method="POST" action="{{ route('bookings.record-payment', $booking) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="amount" :value="__('Amount (₱)')" />
                                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0"
                                    class="block mt-1 w-full" value="{{ old('amount', $booking->payment->amount ?? '') }}" required />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label :value="__('Payment method')" />
                                <select name="payment_method" required
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="cash">{{ __('Cash') }}</option>
                                    <option value="gcash">{{ __('GCash') }}</option>
                                    <option value="bank_transfer">{{ __('Online Banking') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="reference_number" :value="__('Reference number (if GCash or Online Banking)')" />
                                <x-text-input id="reference_number" name="reference_number" type="text"
                                    class="block mt-1 w-full" value="{{ old('reference_number') }}" />
                                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                            </div>

                            <x-primary-button>{{ __('Mark as paid') }}</x-primary-button>
                        </form>
                    @endif
                </div>
            @endif

        </div>
    </div>

    @if ($booking->hasCoordinates())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.initFixItNowMap) {
                    window.initFixItNowMap(
                        'booking-map',
                        {{ $booking->lat }},
                        {{ $booking->lng }},
                        @js($booking->address)
                    );
                }
            });
        </script>
    @endif
</x-app-layout>
