<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book a Service') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ selectedId: null, selectedName: '' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            {{-- Technician listing --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Verified Technicians') }}</h3>

                @if ($technicians->isEmpty())
                    <p class="text-sm text-gray-500">
                        {{ __('No verified technicians are available yet. Please check back soon.') }}
                    </p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($technicians as $technician)
                            <div class="border border-gray-200 rounded-lg p-4"
                                :class="selectedId === {{ $technician->id }} ? 'border-indigo-500 ring-1 ring-indigo-500' : ''">
                                <p class="font-semibold text-gray-800">{{ $technician->user->name }}</p>
                                <p class="text-sm text-indigo-600">{{ $technician->specialty }}</p>
                                @if ($technician->bio)
                                    <p class="text-sm text-gray-600 mt-1">{{ $technician->bio }}</p>
                                @endif

                                <button type="button"
                                    @click="selectedId = {{ $technician->id }}; selectedName = '{{ addslashes($technician->user->name) }}'; $nextTick(() => document.getElementById('booking-form').scrollIntoView({ behavior: 'smooth', block: 'center' }))"
                                    class="mt-3 px-3 py-1.5 bg-gray-800 text-white text-xs font-semibold rounded-md hover:bg-gray-700">
                                    {{ __('Request this technician') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Booking request form --}}
            <div id="booking-form" class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-1">{{ __('Request Details') }}</h3>
                <p class="text-sm text-gray-500 mb-4" x-show="selectedName" x-cloak>
                    {{ __('Booking with:') }} <span class="font-semibold" x-text="selectedName"></span>
                </p>
                <p class="text-sm text-gray-400 mb-4" x-show="!selectedName">
                    {{ __('Pick a technician above first.') }}
                </p>

                <form method="POST" action="{{ route('bookings.store') }}">
                    @csrf
                    <input type="hidden" name="technician_id" x-model="selectedId">

                    <div>
                        <x-input-label for="service_type" :value="__('Service type')" />
                        <x-text-input id="service_type" name="service_type" type="text" class="block mt-1 w-full"
                            value="{{ old('service_type') }}" placeholder="e.g. Leaking faucet repair" required />
                        <x-input-error :messages="$errors->get('service_type')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Describe the problem (optional)')" />
                        <textarea id="description" name="description" rows="3"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="address" :value="__('Service address')" />
                        <x-text-input id="address" name="address" type="text" class="block mt-1 w-full"
                            value="{{ old('address') }}" required />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="scheduled_at" :value="__('Preferred date and time')" />
                        <x-text-input id="scheduled_at" name="scheduled_at" type="datetime-local" class="block mt-1 w-full"
                            value="{{ old('scheduled_at') }}" required />
                        <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
                    </div>

                    <x-input-error :messages="$errors->get('technician_id')" class="mt-2" />

                    <div class="mt-6">
                        <x-primary-button :disabled="false">
                            {{ __('Send request') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
