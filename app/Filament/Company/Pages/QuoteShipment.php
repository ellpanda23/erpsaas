<?php

namespace App\Filament\Pages;

use App\Models\Common\Client;
use App\Models\Company;
use App\Services\SkydropxService;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class QuoteShipment extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $rates = [];
    public ?string $quotationId = null;

    // CAMBIO 1: Agregamos una única propiedad para todo el estado del formulario.
    public ?array $data = [];

    // CAMBIO 2: Eliminamos las propiedades públicas individuales que ahora están dentro de $data.
    // public array $address_from = [];
    // public array $address_to = [];
    // public array $parcels = [];
    // public bool $use_company_address = true;
    // public ?int $client_id = null;
    // public bool $enter_manual_address = false;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static string $view = 'filament.company.pages.quote-shipment';
    protected static string $routePath = 'quote-shipment';

    public static function getNavigationGroup(): ?string
    {
        return __('Logistics');
    }

    public static function getNavigationLabel(): string
    {
        return __('Quote a Shipment');
    }

    public function getTitle(): string
    {
        return __('Shipment Quotation Tool');
    }

    public function mount(): void
    {
        $company = Filament::getTenant();
        $originAddressData = [];
        $hasShippingAddress = false;
        $companyShippingAddress = $company->addresses()->with('state')->first();

        if ($companyShippingAddress) {
            $hasShippingAddress = true;
            $originAddressData = [
                'postal_code' => $companyShippingAddress->postal_code,
                'area_level1' => $companyShippingAddress->state?->name,
                'area_level2' => $companyShippingAddress->city,
                'area_level3' => $companyShippingAddress->neighborhood,
                'country_code' => $companyShippingAddress->country_code,
            ];
        }

        // CAMBIO 3: Llenamos el formulario. Esto ahora poblará automáticamente la propiedad $this->data
        // gracias al método getFormStatePath() que añadiremos.
        $this->form->fill([
            'use_company_address' => $hasShippingAddress,
            'address_from' => $originAddressData,
            'enter_manual_address' => false,
            'address_to' => ['country_code' => 'MX'],
            'parcels' => [['weight' => 1, 'length' => 20, 'width' => 20, 'height' => 15]],
        ]);
    }

    // CAMBIO 4: Implementamos este método para vincular el estado del formulario a la propiedad $data.
    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make(__('Origin'))
                ->schema([
                    Toggle::make('use_company_address')
                        ->label('Utilizar la dirección de envío de la empresa')
                        ->live()
                        ->afterStateUpdated(function (bool $state, Set $set) {
                            if ($state) {
                                $company = Filament::getTenant();
                                $shippingAddress = $company->addresses()->with('state')->first();
                                if ($shippingAddress) {
                                    $set('address_from.postal_code', $shippingAddress->postal_code);
                                    $set('address_from.area_level1', $shippingAddress->state?->name);
                                    $set('address_from.area_level2', $shippingAddress->city);
                                    $set('address_from.area_level3', $shippingAddress->neighborhood);
                                    $set('address_from.country_code', $shippingAddress->country_code);
                                } else {
                                    Notification::make()->title(__('Company Shipping Address Missing'))->body(__('Please set a "shipping" address in your company profile to use this feature.'))->warning()->send();
                                    $set('use_company_address', false);
                                }
                            } else {
                                $set('address_from.postal_code', '');
                                $set('address_from.area_level1', '');
                                $set('address_from.area_level2', '');
                                $set('address_from.area_level3', '');
                                $set('address_from.country_code', 'MX');
                            }
                        }),
                    TextInput::make('address_from.postal_code')->label(__('Postal Code'))->required(fn(Get $get) => !$get('use_company_address'))->disabled(fn(Get $get) => $get('use_company_address')),
                    TextInput::make('address_from.area_level1')->label(__('State'))->required(fn(Get $get) => !$get('use_company_address'))->disabled(fn(Get $get) => $get('use_company_address')),
                    TextInput::make('address_from.area_level2')->label(__('City or Municipality'))->required(fn(Get $get) => !$get('use_company_address'))->disabled(fn(Get $get) => $get('use_company_address')),
                    TextInput::make('address_from.area_level3')->label(__('Neighborhood'))->disabled(fn(Get $get) => $get('use_company_address'))->disabled(fn(Get $get) => $get('use_company_address')),
                    TextInput::make('address_from.country_code')->label(__('Country Code'))->required(fn(Get $get) => !$get('use_company_address'))->disabled(fn(Get $get) => $get('use_company_address')),
                ])->columns(2),

            Section::make(__('Destination'))
                ->description(__('Select a client or enter the address manually.'))
                ->schema([
                    Toggle::make('enter_manual_address')
                        ->label('Ingresar dirección de destino manualmente')
                        ->live()
                        ->afterStateUpdated(function (bool $state, Set $set) {
                            if ($state) {
                                $set('client_id', null);
                                $set('address_to.postal_code', '');
                                $set('address_to.area_level1', '');
                                $set('address_to.area_level2', '');
                                $set('address_to.area_level3', '');
                                $set('address_to.country_code', 'MX');
                            }
                        }),

                    Select::make('client_id')
                        ->label('Cliente')
                        ->placeholder('Busca y selecciona un cliente')
                        ->searchable()
                        ->getSearchResultsUsing(fn(string $search) => Client::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
                        ->getOptionLabelUsing(fn($value): ?string => Client::find($value)?->name)
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (!$state) return;
                            $client = Client::find($state);
                            $shippingAddress = $client?->addresses()->where('type', 'shipping')->with('state')->first();
                            if ($shippingAddress) {
                                $set('address_to.postal_code', $shippingAddress->postal_code);
                                $set('address_to.area_level1', $shippingAddress->state?->name);
                                $set('address_to.area_level2', $shippingAddress->city);
                                $set('address_to.area_level3', $shippingAddress->neighborhood);
                                $set('address_to.country_code', $shippingAddress->country_code);
                            } else {
                                Notification::make()->title('Cliente sin Dirección de Envío')->body('El cliente seleccionado no tiene una dirección de envío configurada.')->warning()->send();
                                $set('address_to.postal_code', '');
                                $set('address_to.area_level1', '');
                                $set('address_to.area_level2', '');
                                $set('address_to.area_level3', '');
                            }
                        })
                        ->disabled(fn(Get $get) => $get('enter_manual_address')),

                    TextInput::make('address_to.postal_code')->label(__('Postal Code'))->required(fn(Get $get) => $get('enter_manual_address'))->disabled(fn(Get $get) => !$get('enter_manual_address')),
                    TextInput::make('address_to.area_level1')->label(__('State'))->required(fn(Get $get) => $get('enter_manual_address'))->disabled(fn(Get $get) => !$get('enter_manual_address')),
                    TextInput::make('address_to.area_level2')->label(__('City or Municipality'))->required(fn(Get $get) => $get('enter_manual_address'))->disabled(fn(Get $get) => !$get('enter_manual_address')),
                    TextInput::make('address_to.area_level3')->label(__('Neighborhood'))->disabled(fn(Get $get) => !$get('enter_manual_address')),
                    TextInput::make('address_to.country_code')->label(__('Country Code'))->default('MX')->required(fn(Get $get) => $get('enter_manual_address'))->disabled(fn(Get $get) => !$get('enter_manual_address')),
                ])->columns(2),

            Section::make(__('Packages'))
                ->description(__('Add one or more packages with their dimensions and weight.'))
                ->schema([
                    Repeater::make('parcels')
                        ->schema([
                            TextInput::make('weight')->label(__('Weight (kg)'))->numeric()->required()->minValue(0.1),
                            TextInput::make('length')->label(__('Length (cm)'))->numeric()->required()->integer(),
                            TextInput::make('width')->label(__('Width (cm)'))->numeric()->required()->integer(),
                            TextInput::make('height')->label(__('Height (cm)'))->numeric()->required()->integer(),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->createItemButtonLabel(__('Add another package')),
                ]),
        ];
    }

    public function getQuote(SkydropxService $skydropxService): void
    {
        $this->rates = [];
        $this->quotationId = null;

        // 1. Obtenemos el estado base de $this->data, que tiene las direcciones correctas.
        $formData = $this->data;

        // 2. ¡AQUÍ ESTÁ LA MAGIA!
        // Corregimos la estructura del array 'parcels'.
        // Usamos array_values() para quitar las claves UUID del Repeater y dejar un array numérico.
        if (!empty($formData['parcels'])) {
            $formData['parcels'] = array_values($formData['parcels']);
        }

        // dd($formData);

        // Verificación para depuración (puedes quitarla después)
        if (empty($formData['address_from']['postal_code']) || empty($formData['address_to']['postal_code'])) {
            Notification::make()->title('Datos Faltantes')->body('El código postal de origen o destino está vacío. Revisa los datos.')->danger()->send();
            return;
        }

        $response = $skydropxService->cotizarEnvio($formData);

        if (!$response || empty($response['id'])) {
            Notification::make()->title(__('Quote Error'))->body(__('Could not connect to the service or the data is incorrect.'))->danger()->send();
            return;
        }
        $this->quotationId = $response['id'];
        // sleep(3);
        $ratesResponse = $skydropxService->obtenerTarifasDeCotizacion($this->quotationId);
        if ($ratesResponse && !empty($ratesResponse['rates'])) {

            $allRates = collect($ratesResponse['rates']);

            $this->rates = $allRates
                ->filter(fn($rate) => ($rate['total'] ?? 0) > 0)
                ->sortBy('total')
                ->toArray();
            Notification::make()->title(__('Quote Successful!'))->body(__('Found :count shipping options.', ['count' => count($this->rates)]))->success()->send();
        } else {
            Notification::make()->title(__('No Results'))->body(__('No rates were found for the provided addresses and packages.'))->warning()->send();
        }
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->url(static::getNavigationUrl())
                ->icon(static::getNavigationIcon())
                ->group(static::getNavigationGroup())
                ->sort(static::getNavigationSort()),
        ];
    }
}
