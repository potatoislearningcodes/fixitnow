<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Technician Verification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @forelse ($pending as $technician)
                    <div class="p-6 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-start justify-between flex-wrap gap-4">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $technician->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $technician->user->email }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>{{ __('Specialty:') }}</strong> {{ $technician->specialty ?? __('Not provided yet') }}
                                </p>
                                @if ($technician->bio)
                                    <p class="text-sm text-gray-600 mt-1">{{ $technician->bio }}</p>
                                @endif
                                @if ($technician->document_path)
                                    <a href="{{ Storage::url($technician->document_path) }}" target="_blank"
                                        class="text-sm text-indigo-600 underline mt-1 inline-block">
                                        {{ __('View submitted document') }}
                                    </a>
                                @else
                                    <p class="text-sm text-red-600 mt-1">{{ __('No document uploaded yet') }}</p>
                                @endif
                            </div>

                            <div class="flex flex-col gap-2 shrink-0">
                                <form method="POST" action="{{ route('admin.technicians.approve', $technician) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                                        {{ __('Approve') }}
                                    </button>
                                </form>

                                <details class="text-sm">
                                    <summary class="cursor-pointer text-red-600 font-semibold">{{ __('Reject') }}</summary>
                                    <form method="POST" action="{{ route('admin.technicians.reject', $technician) }}" class="mt-2 space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="admin_notes" rows="2" placeholder="{{ __('Reason (optional)') }}"
                                            class="border-gray-300 rounded-md text-sm w-full"></textarea>
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700">
                                            {{ __('Confirm reject') }}
                                        </button>
                                    </form>
                                </details>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-gray-500">{{ __('No technicians waiting for review.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
