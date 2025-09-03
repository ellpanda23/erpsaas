<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;

class SkydropxService
{
    protected string $baseUri;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUri = config('services.skydropx.base_uri');
        $this->clientId = config('services.skydropx.client_id');
        $this->clientSecret = config('services.skydropx.client_secret');
    }

    /**
     * Obtiene el token de acceso. Lo busca en caché y si no existe, lo solicita a la API.
     */
    protected function getAccessToken(): ?string
    {
        return Cache::remember('skydropx_access_token', now()->addHour(), function () {
            $response = Http::asForm()->post($this->baseUri . 'oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful() && isset($response->json()['access_token'])) {
                // La documentación no especifica 'expires_in', así que lo cacheamos por 1 hora.
                // Si lo especificara, usaríamos: now()->addSeconds($response->json()['expires_in'] - 60)
                return $response->json()['access_token'];
            }

            Log::error('Fallo al obtener el token de Skydropx', $response->json());
            return null;
        });
    }

    /**
     * Prepara una petición HTTP con el token de autorización.
     */
    protected function client(): PendingRequest
    {
        $token = $this->getAccessToken();

        if (!$token) {
            throw new \Exception('No se pudo obtener el token de acceso de Skydropx.');
        }

        return Http::baseUrl($this->baseUri)
            ->withToken($token) // Esto añade el header 'Authorization: Bearer ...'
            ->acceptJson()
            ->timeout(30);
    }

    /**
     * Paso A: Crea una cotización para obtener tarifas.
     */
    public function cotizarEnvio(array $data): ?array
    {
        // El cuerpo de la petición debe estar anidado dentro de "quotation"
        $response = $this->client()->post('quotations', ['quotation' => $data]);

        return $this->handleResponse($response, 'Error al cotizar envío');
    }

    /**
     * Paso B: Obtiene los detalles y tarifas de una cotización existente.
     */
    public function obtenerTarifasDeCotizacion(string $quotationId): ?array
    {
        $response = $this->client()->get("quotations/{$quotationId}");

        return $this->handleResponse($response, "Error al obtener tarifas para la cotización {$quotationId}");
    }


    /**
     * Paso C: Crea la guía de envío final usando un rate_id.
     */
    public function crearGuia(array $data): ?array
    {
        // El cuerpo de la petición debe estar anidado dentro de "shipment"
        $response = $this->client()->post('shipments', ['shipment' => $data]);

        return $this->handleResponse($response, 'Error al crear la guía');
    }


    /**
     * Obtiene una lista paginada de órdenes.
     *
     * @param int $page El número de página a solicitar.
     * @param int $perPage El número de resultados por página.
     * @return array|null
     */
    public function obtenerOrdenes(int $page = 1, int $perPage = 15): ?array
    {
        // Usamos el endpoint correcto de la documentación: 'orders'
        $response = $this->client()->get('orders', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return $this->handleResponse($response, 'Error al obtener la lista de órdenes');
    }

    /**
     * Obtiene una lista paginada de envíos (shipments).
     *
     * @param int $page El número de página a solicitar.
     * @param int $perPage El número de resultados por página.
     * @return array|null
     */
    public function obtenerEnvios(int $page = 1, int $perPage = 10): ?array
    {
        // Usamos el endpoint correcto de la documentación: 'shipments'
        $response = $this->client()->get('shipments', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return $this->handleResponse($response, 'Error al obtener la lista de envíos');
    }

    /**
     * Maneja la respuesta de la API y los errores.
     */
    private function handleResponse($response, string $errorMessage): ?array
    {
        if ($response->successful()) {
            return $response->json();
        }

        Log::error($errorMessage, [
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return null;
    }
}
