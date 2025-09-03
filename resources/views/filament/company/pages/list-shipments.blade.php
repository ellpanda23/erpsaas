<x-filament-panels::page wire:init="loadShipments">
    {{-- @php
        // 1. RESPUESTA COMPLETA DE LA API (tal como la pegaste)
        $apiResponse = [
            'data' => [
                [
                    'id' => 'ac307298-74a2-4f77-a7fc-9ed08ea12596',
                    'type' => 'shipment',
                    'attributes' => [
                        'id' => 'ac307298-74a2-4f77-a7fc-9ed08ea12596',
                        'carrier_name' => 'fedex',
                        'workflow_status' => 'success',
                        'payment_status' => 'paid',
                        'total' => '256.0',
                        'carrier_id' => '27eb9ac7-856d-4ece-b455-4991db6d8f1e',
                        'source' => 'order_wizard',
                        'service_id' => '533745bc-33e9-4673-a494-2523c3cc73f1',
                        'created_at' => '2025-08-28T12:42:16.278-06:00',
                        'updated_at' => '2025-08-31T21:51:13.265-06:00',
                        'master_tracking_number' => '883940623986',
                    ],
                    'relationships' => [
                        'packages' => [
                            'data' => [['id' => 'b0cd9204-1ec4-4b07-be98-398196826889', 'type' => 'package']],
                        ],
                        'address_from' => [
                            'data' => ['id' => '3104f70a-0191-46e3-bba7-899cd9c9925a', 'type' => 'address'],
                        ],
                        'address_to' => [
                            'data' => ['id' => 'd008b1f7-6b37-4c2f-9142-43f8ab0ead47', 'type' => 'address'],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => 'b0cd9204-1ec4-4b07-be98-398196826889',
                    'type' => 'package',
                    'attributes' => [
                        'id' => 'b0cd9204-1ec4-4b07-be98-398196826889',
                        'package_type' => '4G',
                        'weight' => '14.0',
                        'length' => '49.0',
                        'width' => '39.0',
                        'height' => '33.0',
                        'consignment_note' => '53102901',
                        'tracking_status' => 'in_transit',
                        'created_at' => '2025-08-28T12:42:16.392-06:00',
                        'updated_at' => '2025-08-31T21:51:13.219-06:00',
                        'tracking_url_provider' => 'https://www.fedex.com/fedextrack/?trknbr=883940623986',
                        'tracking_number' => '883940623986',
                        'label_url' =>
                            'https://pro.skydropx.com/cloud/storage/blobs/proxy/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaEpJaWswTnpZMVpUWTFOQzAwWldFMkxUUmtZalF0T1ROak9TMHpORGhsWlRZM05URmxNamdHT2daRlZBPT0iLCJleHAiOm51bGwsInB1ciI6ImJsb2JfaWQifX0=--df0b0728b816759b82e5b4bc59e47cfc6535c4c7/label_b0cd9204-1ec4-4b07-be98-398196826889.pdf',
                    ],
                ],
                [
                    'id' => '3104f70a-0191-46e3-bba7-899cd9c9925a',
                    'type' => 'address',
                    'attributes' => [
                        'id' => '3104f70a-0191-46e3-bba7-899cd9c9925a',
                        'area_level1' => 'Guanajuato',
                        'area_level2' => 'Moroleón',
                        'name' => 'Israel Isaac Orozco Gonzalez',
                        'postal_code' => '38994',
                        'country_code' => 'MX',
                        'address_type' => 'from',
                        'street1' => 'P.º del Haya 435',
                        'company' => '',
                        'phone' => '4451757187',
                        'email' => 'isack0g23cs@gmail.com',
                        'reference' => 'Malla de acero',
                        'area_level3' => 'Rinconadas del Bosque',
                        'apartment_number' => '',
                    ],
                ],
                [
                    'id' => 'd008b1f7-6b37-4c2f-9142-43f8ab0ead47',
                    'type' => 'address',
                    'attributes' => [
                        'id' => 'd008b1f7-6b37-4c2f-9142-43f8ab0ead47',
                        'area_level1' => 'Quintana Roo',
                        'area_level2' => 'Benito Juárez',
                        'name' => 'Rafael García Rico',
                        'postal_code' => '77539',
                        'country_code' => 'MX',
                        'address_type' => 'to',
                        'street1' => 'C. Cisne 35',
                        'company' => '',
                        'phone' => '9982026363',
                        'email' => 'noemirafael1983@gmail.com',
                        'reference' => 'Edificio',
                        'area_level3' => 'Paseo Kusamil',
                        'apartment_number' => 'F',
                    ],
                ],
            ],
        ];

        // 2. LÓGICA PARA PROCESAR Y UNIR LOS DATOS
        $includedData = collect($apiResponse['included'])->keyBy('id');
        $shipmentsForView = collect($apiResponse['data'])
            ->map(function ($shipment) use ($includedData) {
                // Unir Dirección de Origen
                $addressFromId = $shipment['relationships']['address_from']['data']['id'] ?? null;
                if ($addressFromId && $includedData->has($addressFromId)) {
                    $shipment['address_from_details'] = $includedData->get($addressFromId)['attributes'];
                }
                // Unir Dirección de Destino
                $addressToId = $shipment['relationships']['address_to']['data']['id'] ?? null;
                if ($addressToId && $includedData->has($addressToId)) {
                    $shipment['address_to_details'] = $includedData->get($addressToId)['attributes'];
                }
                // Unir Paquetes (tomamos el primero para este ejemplo)
                $packageId = $shipment['relationships']['packages']['data'][0]['id'] ?? null;
                if ($packageId && $includedData->has($packageId)) {
                    $shipment['package_details'] = $includedData->get($packageId)['attributes'];
                }
                return $shipment;
            })
            ->all();
    @endphp --}}

    <div x-data="{ isVisible: @entangle('isViewingDetails') }">
        {{-- Indicador de Carga (se mostrará hasta que conectes los datos reales) --}}
        @if ($isLoading)
            <div class="flex items-center justify-center w-full py-16">
                <div class="flex flex-col items-center space-y-4">
                    <x-filament::loading-indicator class="w-10 h-10" />
                    <span class="text-lg font-medium text-gray-500 dark:text-gray-400">Cargando...</span>
                </div>
            </div>
        @else
            {{-- Contenido de los envíos --}}
            <div class="space-y-4">
                @forelse ($shipments as $shipment)
                    <div
                        class="p-4 bg-white shadow-sm fi-section rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                        <div
                            class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-white/10">
                            <div class="flex items-center space-x-4">
                                <x-heroicon-o-archive-box class="w-6 h-6 text-gray-400 dark:text-gray-500" />
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ \Carbon\Carbon::parse($shipment['attributes']['created_at'])->translatedFormat('d/M/Y h:i a') }}</span>
                                    <span class="mx-1">&middot;</span>
                                    <span>Por: {{ $shipment['address_from_details']['name'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div>
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-md',
                                    'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' =>
                                        $shipment['package_details']['tracking_status'] === 'delivered',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400' =>
                                        $shipment['package_details']['tracking_status'] === 'in_transit',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400' =>
                                        $shipment['package_details']['tracking_status'] === 'pending',
                                ])>
                                    {{ $shipment['package_details']['tracking_status'] }}
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-6 py-4 md:grid-cols-6">
                            <div class="md:col-span-2">
                                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Paquetería</p>
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 text-white bg-purple-600 rounded-md">
                                        <x-heroicon-s-building-storefront class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 capitalize dark:text-white">
                                            {{ $shipment['attributes']['carrier_name'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Express Saver</p>
                                    </div>
                                </div>
                            </div>
                            <div x-data="{ copied: false }" class="md:col-span-1">
                                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Núm. de rastreo</p>
                                <div class="flex items-center space-x-2">
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $shipment['attributes']['master_tracking_number'] }}</p>
                                    <button
                                        x-on:click="navigator.clipboard.writeText('{{ $shipment['attributes']['master_tracking_number'] }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        title="Copiar">
                                        <x-heroicon-s-check-circle x-show="copied" class="w-5 h-5 text-green-500" />
                                        <x-heroicon-o-clipboard-document x-show="!copied"
                                            class="w-5 h-5 text-gray-400 hover:text-gray-600" />
                                    </button>
                                </div>
                            </div>
                            <div class="md:col-span-1">
                                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Destino</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $shipment['address_to_details']['name'] ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $shipment['address_to_details']['postal_code'] ?? '' }} -
                                    {{ $shipment['address_to_details']['area_level3'] ?? '' }}</p>
                            </div>
                            <div class="text-left md:col-span-1 md:text-right">
                                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Precio</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    ${{ number_format((float) $shipment['attributes']['total'], 2) }}</p>
                            </div>
                            <div class="flex items-center justify-start space-x-2 md:col-span-1 md:justify-end">
                                <a href="{{ $shipment['package_details']['label_url'] ?? '#' }}" target="_blank"
                                    class="p-2 text-white rounded-lg bg-primary-600 hover:bg-primary-500">
                                    <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                                </a>
                                <button wire:click="viewDetails({{ json_encode($shipment) }})"
                                    class="p-2 text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700"
                                    title="Ver detalles"><x-heroicon-o-eye class="w-5 h-5" /></button>
                                <button
                                    class="p-2 text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700"><x-heroicon-o-ellipsis-horizontal
                                        class="w-5 h-5" /></button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        No se encontraron envíos.
                    </div>
                @endforelse
            </div>
        @endif

        <div x-show="isVisible" x-cloak @keydown.escape.window="isVisible = false" class="relative z-50">
            <div x-show="isVisible" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/50"></div>
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10 pointer-events-none">
                        <div x-show="isVisible" @click.away="isVisible = false"
                            x-transition:enter="transform transition ease-in-out duration-300"
                            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition ease-in-out duration-300"
                            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                            class="w-screen max-w-2xl pointer-events-auto">
                            <div class="flex flex-col h-full shadow-xl bg-gray-50 dark:bg-gray-800/50">
                                @if ($selectedShipment)
                                    <div
                                        class="px-4 py-4 bg-white border-b sm:px-6 dark:bg-gray-800 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Detalles
                                                del envío</h2>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ $selectedShipment['package_details']['tracking_url_provider'] ?? '#' }}"
                                                    target="_blank"
                                                    class="inline-flex items-center px-3 py-1 text-sm font-medium border rounded-md text-primary-600 border-primary-600 hover:bg-primary-50">
                                                    Rastrear en {{ $selectedShipment['attributes']['carrier_name'] }}
                                                    <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 ml-1" />
                                                </a>
                                                <button @click="isVisible = false"><x-heroicon-o-x-mark
                                                        class="w-6 h-6 text-gray-500 hover:text-gray-800" /></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 px-4 py-6 overflow-y-auto sm:px-6">
                                        <div class="space-y-4">
                                            <div class="p-4 bg-white rounded-lg shadow fi-section dark:bg-gray-800">
                                                <div class="flex items-center mb-4 space-x-2">
                                                    <x-heroicon-o-cube-transparent class="w-6 h-6 text-primary-500" />
                                                    <h3 class="font-semibold text-gray-700 text-md dark:text-gray-300">
                                                        Estatus del envío</h3>
                                                </div>
                                                <div
                                                    class="p-4 text-center text-gray-500 bg-gray-100 border border-dashed rounded-lg dark:bg-gray-700">
                                                    Área de la línea de tiempo de estatus (diseño estático)</div>
                                            </div>
                                            <div class="p-4 bg-white rounded-lg shadow fi-section dark:bg-gray-800">
                                                <div class="space-y-3">
                                                    <div class="flex items-center justify-between"><span
                                                            class="text-sm text-gray-500">Paquetería:</span><span
                                                            class="font-semibold text-gray-800 capitalize dark:text-gray-200">{{ $selectedShipment['attributes']['carrier_name'] }}
                                                            Express Saver</span></div>
                                                    <div class="flex items-center justify-between"><span
                                                            class="text-sm text-gray-500">Número de
                                                            rastreo:</span><span
                                                            class="font-semibold text-primary-600">#{{ $selectedShipment['attributes']['master_tracking_number'] }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between"><span
                                                            class="text-sm text-gray-500">Entrega estimada:</span><span
                                                            class="font-semibold text-gray-800 dark:text-gray-200">6
                                                            días</span></div>
                                                    <div class="flex items-center justify-between"><span
                                                            class="text-sm text-gray-500">Creado por:</span><span
                                                            class="font-semibold text-gray-800 dark:text-gray-200">{{ $selectedShipment['address_from_details']['name'] ?? 'N/A' }}</span>
                                                    </div>
                                                    <div
                                                        class="flex items-center justify-between pt-3 mt-3 border-t dark:border-gray-700">
                                                        <span class="font-semibold text-md">Monto total:</span><span
                                                            class="text-lg font-bold">${{ number_format((float) $selectedShipment['attributes']['total'], 2) }}
                                                            MXN</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="p-4 space-y-2 bg-white rounded-lg shadow fi-section dark:bg-gray-800">
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="font-semibold">Agrega SOS Protección</h4>
                                                    <x-heroicon-o-shield-check class="w-5 h-5 text-gray-400" />
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">El contenido del
                                                    paquete no está protegido. Estás a tiempo de <a href="#"
                                                        class="font-medium text-primary-600 dark:text-primary-400 hover:underline">agregar
                                                        SOS Protección</a> a este envío.</p>
                                            </div>
                                            <div x-data="{ open: true }"
                                                class="p-4 bg-white rounded-lg shadow fi-section dark:bg-gray-800">
                                                <button @click="open = !open"
                                                    class="flex items-center justify-between w-full">
                                                    <div class="flex items-center">
                                                        <x-heroicon-o-building-office-2
                                                            class="w-5 h-5 mr-2 text-primary-600 dark:text-primary-400" />
                                                        <h4 class="font-semibold">Dirección de origen</h4>
                                                    </div>
                                                    <x-heroicon-s-chevron-up class="w-5 h-5 transition-transform"
                                                        x-bind:class="{ 'rotate-180': !open }" />
                                                </button>
                                                <div x-show="open" x-transition class="mt-4 space-y-2">
                                                    <p
                                                        class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                                                        <x-heroicon-o-user
                                                            class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>{{ $selectedShipment['address_from_details']['name'] ?? 'N/A' }}</span>
                                                    </p>
                                                    <p
                                                        class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                                                        <x-heroicon-o-map-pin
                                                            class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>{{ $selectedShipment['address_from_details']['street1'] ?? '' }},
                                                            {{ $selectedShipment['address_from_details']['area_level3'] ?? '' }}
                                                            | CP
                                                            {{ $selectedShipment['address_from_details']['postal_code'] ?? '' }}
                                                            |
                                                            {{ $selectedShipment['address_from_details']['area_level2'] ?? '' }},
                                                            {{ $selectedShipment['address_from_details']['area_level1'] ?? '' }}</span>
                                                    </p>
                                                    <div class="flex items-center">
                                                        <p
                                                            class="flex items-start justify-center mr-4 text-sm text-gray-600 dark:text-gray-300">
                                                            <x-heroicon-o-device-phone-mobile
                                                                class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>{{ $selectedShipment['address_from_details']['phone'] ?? 'N/A' }}</span>
                                                        </p>
                                                        <p
                                                            class="flex items-start justify-center text-sm text-gray-600 dark:text-gray-300">
                                                            <x-heroicon-o-envelope
                                                                class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>{{ $selectedShipment['address_from_details']['email'] ?? 'N/A' }}</span>
                                                        </p>
                                                    </div>
                                                    @if (!empty($selectedShipment['address_from_details']['reference']))
                                                        <p
                                                            class="flex items-start pt-2 mt-2 text-sm text-gray-500 border-t dark:border-gray-700">
                                                            <x-heroicon-o-chat-bubble-left-ellipsis
                                                                class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>Referencia:
                                                                {{ $selectedShipment['address_from_details']['reference'] }}</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div x-data="{ open: true }"
                                                class="p-4 bg-white rounded-lg shadow fi-section dark:bg-gray-800">
                                                <button @click="open = !open"
                                                    class="flex items-center justify-between w-full">
                                                    <div class="flex items-center">
                                                        <x-heroicon-o-home
                                                            class="w-5 h-5 mr-2 text-primary-600 dark:text-primary-400" />
                                                        <h4 class="font-semibold">Dirección de destino</h4>
                                                    </div>
                                                    <x-heroicon-s-chevron-up class="w-5 h-5 transition-transform"
                                                        x-bind:class="{ 'rotate-180': !open }" />
                                                </button>
                                                <div x-show="open" x-transition class="mt-4 space-y-2">
                                                    <p
                                                        class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                                                        <x-heroicon-o-user
                                                            class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>{{ $selectedShipment['address_to_details']['name'] ?? 'N/A' }}</span>
                                                    </p>
                                                    <p
                                                        class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                                                        <x-heroicon-o-map-pin
                                                            class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>{{ $selectedShipment['address_to_details']['street1'] ?? '' }},
                                                            {{ $selectedShipment['address_to_details']['area_level3'] ?? '' }}
                                                            | CP
                                                            {{ $selectedShipment['address_to_details']['postal_code'] ?? '' }}
                                                            |
                                                            {{ $selectedShipment['address_to_details']['area_level2'] ?? '' }},
                                                            {{ $selectedShipment['address_to_details']['area_level1'] ?? '' }}</span>
                                                    </p>
                                                    <p
                                                        class="flex items-start text-sm text-gray-600 dark:text-gray-300">
                                                        <x-heroicon-o-device-phone-mobile
                                                            class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>{{ $selectedShipment['address_to_details']['phone'] ?? 'N/A' }}</span>
                                                    </p>
                                                    @if (!empty($selectedShipment['address_to_details']['reference']))
                                                        <p
                                                            class="flex items-start pt-2 mt-2 text-sm text-gray-500 border-t dark:border-gray-700">
                                                            <x-heroicon-o-chat-bubble-left-ellipsis
                                                                class="w-4 h-4 mt-1 mr-2 shrink-0" /><span>Referencia:
                                                                {{ $selectedShipment['address_to_details']['reference'] }}</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if (!empty($selectedShipment['package_details']))
                                                <div x-data="{ open: true }"
                                                    class="p-4 bg-white rounded-lg shadow fi-section dark:bg-gray-800">
                                                    <button @click="open = !open"
                                                        class="flex items-center justify-between w-full">
                                                        <div class="flex items-center mr-2">
                                                            <x-heroicon-o-cube
                                                                class="w-5 h-5 mr-2 text-primary-600 dark:text-primary-400" />
                                                            <h4 class="font-semibold">Paquete</h4>
                                                        </div>
                                                        <x-heroicon-s-chevron-up class="w-5 h-5 transition-transform"
                                                            x-bind:class="{ 'rotate-180': !open }" />
                                                    </button>
                                                    <div x-show="open" x-transition class="mt-4 space-y-3">


                                                        <div class="flex items-center gap-4">
                                                            <span
                                                                class="flex px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-300 rounded dark:bg-gray-700 dark:text-gray-200">
                                                                <x-heroicon-o-cube-transparent class="w-4 h-4 mr-2" />
                                                                {{ $selectedShipment['package_details']['length'] ?? 0 }}
                                                                x
                                                                {{ $selectedShipment['package_details']['width'] ?? 0 }}
                                                                x
                                                                {{ $selectedShipment['package_details']['height'] ?? 0 }}
                                                                cm
                                                            </span>
                                                            <span
                                                                class="flex px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-300 rounded dark:bg-gray-700 dark:text-gray-200">
                                                                <x-heroicon-o-rectangle-stack class="w-4 h-4 mr-2" />
                                                                {{ $selectedShipment['package_details']['weight'] ?? 0 }}
                                                                kg
                                                            </span>
                                                        </div>


                                                        {{-- <div class="flex items-center justify-between"><span
                                                                class="text-sm text-gray-500">Peso:</span><span
                                                                class="font-semibold text-gray-800 dark:text-gray-200">
                                                                kg</span></div> --}}
                                                        <div class="pt-3 mt-3 border-t dark:border-gray-700">
                                                            <a href="{{ $selectedShipment['package_details']['label_url'] ?? '#' }}"
                                                                target="_blank"
                                                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-semibold text-white rounded-lg bg-primary-600 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                                <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                                                                Descargar Guía (PDF)
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
