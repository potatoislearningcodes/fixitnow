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

            {{-- Technician listing (optional pick) --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-1">{{ __('Verified Technicians') }}</h3>
                <p class="text-sm text-gray-500 mb-4">
                    {{ __('Pick one now, or skip this and let an admin assign an available technician for you.') }}
                </p>

                @if ($technicians->isEmpty())
                    <p class="text-sm text-gray-500">
                        {{ __('No verified technicians are available yet. You can still submit a request — an admin will assign one.') }}
                    </p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($technicians as $technician)
                            @php $available = $technician->isAvailable(); @endphp
                            <div class="border border-gray-200 rounded-lg p-4"
                                :class="selectedId === {{ $technician->id }} ? 'border-indigo-500 ring-1 ring-indigo-500' : ''">
                                <div class="flex items-start justify-between">
                                    <p class="font-semibold text-gray-800">{{ $technician->user->name }}</p>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $available ? __('Available') : __('Busy') }}
                                    </span>
                                </div>
                                <p class="text-sm text-indigo-600">{{ $technician->specialty }}</p>
                                @if ($technician->bio)
                                    <p class="text-sm text-gray-600 mt-1">{{ $technician->bio }}</p>
                                @endif

                                <button type="button" @if (! $available) disabled @endif
                                    @click="selectedId = {{ $technician->id }}; selectedName = '{{ addslashes($technician->user->name) }}'; $nextTick(() => document.getElementById('booking-form').scrollIntoView({ behavior: 'smooth', block: 'center' }))"
                                    class="mt-3 px-3 py-1.5 text-white text-xs font-semibold rounded-md {{ $available ? 'bg-gray-800 hover:bg-gray-700' : 'bg-gray-300 cursor-not-allowed' }}">
                                    {{ $available ? __('Request this technician') : __('Currently busy') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <button type="button" x-show="selectedId" @click="selectedId = null; selectedName = ''"
                    class="mt-4 text-xs text-gray-500 underline">
                    {{ __('Clear selection — let admin assign instead') }}
                </button>
            </div>

            {{-- Booking request form --}}
            <div id="booking-form" class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-1">{{ __('Request Details') }}</h3>
                <p class="text-sm text-gray-500 mb-4" x-show="selectedName" x-cloak>
                    {{ __('Booking with:') }} <span class="font-semibold" x-text="selectedName"></span>
                </p>
                <p class="text-sm text-gray-400 mb-4" x-show="!selectedName">
                    {{ __('No technician selected — an admin will assign one after you submit.') }}
                </p>

                <form method="POST" action="{{ route('bookings.store') }}">
                    @csrf
                    <input type="hidden" name="technician_id" x-model="selectedId">

                    <div>
                        <x-input-label :value="__('Type of building')" />
                        <select name="building_type" required
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                            <option value="">{{ __('Select one') }}</option>
                            <option value="residential" @selected(old('building_type') === 'residential')>{{ __('Residential (House, Condo unit)') }}</option>
                            <option value="institutional_business" @selected(old('building_type') === 'institutional_business')>{{ __('Institutional / Business (Office, Company, School)') }}</option>
                            <option value="hospitality" @selected(old('building_type') === 'hospitality')>{{ __('Hospitality (Hotel, Apartment)') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('building_type')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label :value="__('Service group')" />
                        <div class="mt-2 flex gap-6">
                            <label class="flex items-center">
                                <input type="radio" name="service_group" value="repair"
                                    {{ old('service_group', 'repair') === 'repair' ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-700">{{ __('Repair — something is broken') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="service_group" value="maintenance"
                                    {{ old('service_group') === 'maintenance' ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-700">{{ __('Maintenance — regular check-up') }}</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('service_group')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="service_type" :value="__('Specific service')" />
                        <x-text-input id="service_type" name="service_type" type="text" class="block mt-1 w-full"
                            value="{{ old('service_type') }}" placeholder="e.g. Leaking faucet repair, Aircon cleaning" required />
                        <x-input-error :messages="$errors->get('service_type')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="rooms" :value="__('Room(s) needing service (optional)')" />
                        <x-text-input id="rooms" name="rooms" type="text" class="block mt-1 w-full"
                            value="{{ old('rooms') }}" placeholder="e.g. Kitchen, Bathroom, Room 101" />
                        <x-input-error :messages="$errors->get('rooms')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Additional details (optional)')" />
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

                    <div class="mt-6">
                        <x-primary-button>{{ __('Send request') }}</x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
