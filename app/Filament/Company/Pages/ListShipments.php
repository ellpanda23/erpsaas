<?php

namespace App\Filament\Company\Pages;

use App\Services\SkydropxService;
use Filament\Pages\Page;

class ListShipments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static string $view = 'filament.company.pages.list-shipments';
    protected static ?string $navigationGroup = 'Logistics';
    protected static ?int $navigationSort = 2;

    // AHORA ESTA ES LA ÚNICA PROPIEDAD QUE LA VISTA NECESITARÁ
    public array $shipments = [];

    public array $paginationMeta = [];
    public bool $isLoading = true;
    public bool $isViewingDetails = false;
    public ?array $selectedShipment = null;

    public function getTitle(): string
    {
        return __('Shipment List');
    }

    public function mount(): void
    {
        $this->isViewingDetails = false;
    }

    public function loadShipments(SkydropxService $skydropxService): void
    {
        $response = $skydropxService->obtenerEnvios();

        // ¡AQUÍ ESTÁ EL CAMBIO!
        // Ahora llamamos a nuestro nuevo método para procesar la respuesta.
        $this->shipments = $this->processApiResponse($response);

        $this->paginationMeta = $response['meta'] ?? [];
        $this->isLoading = false;
    }

    public function viewDetails(array $shipmentData): void
    {
        $this->selectedShipment = $shipmentData;
        $this->isViewingDetails = true;
    }

    public function closeDetailsModal(): void
    {
        $this->isViewingDetails = false;
    }

    /**
     * Procesa la respuesta de la API en formato JSON:API y une los datos
     * de 'included' con los envíos principales de 'data'.
     */
    private function processApiResponse(?array $apiResponse): array
    {
        if (empty($apiResponse) || empty($apiResponse['data'])) {
            return [];
        }

        $includedData = collect($apiResponse['included'] ?? [])->keyBy('id');

        return collect($apiResponse['data'])->map(function ($shipment) use ($includedData) {
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
        })->all();
    }
}
