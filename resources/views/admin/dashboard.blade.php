<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <p class="text-sm text-gray-500">{{ __('Pending technicians') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $pendingTechnicianCount }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <p class="text-sm text-gray-500">{{ __('Approved technicians') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $approvedTechnicianCount }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <p class="text-sm text-gray-500">{{ __('Total customers') }}</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $customerCount }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-2">{{ __('Next step') }}</h3>
                <p class="text-sm text-gray-600 mb-4">
                    {{ __('Review technicians waiting for approval before customers can book them.') }}
                </p>
                <a href="{{ route('admin.technicians.index') }}"
                    class="inline-block px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
                    {{ __('Review technicians') }}
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
