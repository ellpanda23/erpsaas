<x-filament::page>
    <form wire:submit.prevent="getQuote">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                <div class="flex">
                    <x-heroicon-o-sparkles class="w-5 h-5 mr-2" />
                    <span wire:loading.remove>{{ __('Get Quote') }}</span>
                    <span wire:loading>{{ __('Getting quote...') }}</span>
                </div>
            </x-filament::button>
        </div>
    </form>

    {{-- RESULTS SECTION --}}
    @if (!empty($rates))
        <div class="mt-8">
            {{-- Encabezado de la sección, opcional pero recomendado --}}
            <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                {{ __('Resultados de la Cotización') }} (ID: {{ $quotationId }})
            </h2>

            {{-- Contenedor principal de la tabla con estilos de Filament --}}
            <div
                class="mt-4 overflow-hidden bg-white shadow-sm fi-ta-ctn rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <table class="w-full divide-y divide-gray-200 table-auto fi-ta-table text-start dark:divide-white/5">
                    {{-- Encabezado de la tabla --}}
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ __('Carrier') }}
                                </span>
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ __('Service') }}
                                </span>
                            </th>
                            <th
                                class="fi-ta-header-cell px-3 py-3.5 text-center sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ __('Estimated days') }}
                                </span>
                            </th>
                            <th
                                class="fi-ta-header-cell px-3 py-3.5 text-end sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ __('Total price') }}
                                </span>
                            </th>
                        </tr>
                    </thead>

                    {{-- Cuerpo de la tabla --}}
                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                        @foreach ($rates as $rate)
                            <tr class="transition duration-75 fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="p-0 fi-ta-cell sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <div class="px-3 py-4">
                                        <div class="text-sm font-medium text-gray-950 dark:text-white">
                                            {{ $rate['provider_display_name'] }}
                                        </div>
                                    </div>
                                </td>
                                <td class="p-0 fi-ta-cell sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <div class="px-3 py-4">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $rate['provider_service_name'] }}
                                        </div>
                                    </div>
                                </td>
                                <td class="p-0 text-center fi-ta-cell sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <div class="px-3 py-4">
                                        {{ $rate['days'] }}
                                    </div>
                                </td>
                                <td class="p-0 fi-ta-cell text-end sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <div class="px-3 py-4 text-sm font-semibold text-primary-600 dark:text-primary-400">
                                        ${{ number_format((float) $rate['total'], 2) }} {{ $rate['currency_code'] }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- Bloque que se muestra si no hay resultados --}}
        <div class="grid max-w-lg px-6 py-12 mx-auto text-center fi-ta-empty-state justify-items-center">
            <div class="p-3 mb-4 bg-gray-100 rounded-full fi-ta-empty-state-icon-ctn dark:bg-gray-500/20">
                <x-heroicon-o-x-mark class="w-6 h-6 text-gray-500 fi-ta-empty-state-icon dark:text-gray-400" />
            </div>
            <h4 class="text-base font-semibold leading-6 fi-ta-empty-state-heading text-gray-950 dark:text-white">
                {{ __('No results found') }}
            </h4>
            <p class="text-sm text-gray-500 fi-ta-empty-state-description dark:text-gray-400">
                {{ __('No available rates were found for the provided addresses and packages.') }}
            </p>
        </div>
    @endif
</x-filament::page>
