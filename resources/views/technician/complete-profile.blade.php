<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Technician Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Verification status badge --}}
                @php
                    $status = $technician->verification_status ?? 'pending';
                    $badge = match ($status) {
                        'approved' => 'bg-green-100 text-green-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        default => 'bg-yellow-100 text-yellow-800',
                    };
                @endphp
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                    {{ ucfirst($status) }}
                </span>

                @if ($status === 'rejected' && $technician->admin_notes)
                    <p class="mt-2 text-sm text-red-700">
                        <strong>{{ __('Admin notes:') }}</strong> {{ $technician->admin_notes }}
                    </p>
                @endif

                <form method="POST" action="{{ route('technician.profile.update') }}" enctype="multipart/form-data" class="mt-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="specialty" :value="__('Specialty')" />
                        <x-text-input id="specialty" name="specialty" type="text" class="block mt-1 w-full"
                            value="{{ old('specialty', $technician->specialty) }}"
                            placeholder="e.g. Plumbing, Electrical, Aircon repair" required />
                        <x-input-error :messages="$errors->get('specialty')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="bio" :value="__('Short bio')" />
                        <textarea id="bio" name="bio" rows="4"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('bio', $technician->bio) }}</textarea>
                        <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="document" :value="__('Verification document (ID or certificate)')" />
                        <input id="document" name="document" type="file" accept=".pdf,.jpg,.jpeg,.png"
                            class="block mt-1 w-full text-sm text-gray-600" />
                        <x-input-error :messages="$errors->get('document')" class="mt-2" />

                        @if ($technician->document_path)
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('A document is already on file. Uploading a new one will replace it.') }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-6">
                        <x-primary-button>{{ __('Submit for review') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
