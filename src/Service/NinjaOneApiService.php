<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class NinjaOneApiService
 * @package App\Service
 *
 * Cette classe est responsable de l'interaction avec l'API NinjaOne.
 * Elle gère l'authentification et fournit des méthodes pour récupérer les organisations.
 */

class NinjaOneApiService
{
    private HttpClientInterface $httpClient;
    private RequestStack $requestStack;
    private string $ninjaOneApiUrl;
    private string $ninjaOneGrantType;
    private string $ninjaOneClientId;
    private string $ninjaOneClientSecret;
    private string $ninjaOneScope;
    private ?string $accessToken = null;
    private ?string $refreshToken = null;

    public function __construct(HttpClientInterface $httpClient, RequestStack $requestStack, string $ninjaOneApiUrl, string $ninjaOneGrantType, string $ninjaOneClientId, string $ninjaOneClientSecret, string $ninjaOneScope)
    {
        $this->httpClient = $httpClient;
        $this->requestStack = $requestStack;
        $this->ninjaOneApiUrl = $ninjaOneApiUrl;
        $this->ninjaOneGrantType = $ninjaOneGrantType;
        $this->ninjaOneClientId = $ninjaOneClientId;
        $this->ninjaOneClientSecret = $ninjaOneClientSecret;
        $this->ninjaOneScope = $ninjaOneScope;
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    // Authentification avec l'API NinjaOne
    public function authenticate(): void
    {
        $response = $this->httpClient->request('POST', "{$this->ninjaOneApiUrl}/ws/oauth/token", [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'grant_type' => $this->ninjaOneGrantType,
                'client_id' => $this->ninjaOneClientId,
                'client_secret' => $this->ninjaOneClientSecret,
                'scope' => $this->ninjaOneScope
            ],
        ]);

        $data = $response->toArray();

        if (!isset($data['access_token'])) {
            throw new \RuntimeException('Failed to retrieve access token.');
        }

        $this->accessToken = $data['access_token'];
        $this->refreshToken = $data['refresh_token'];

        $this->getSession()->set('ninja_access_token', $data['access_token']);
        $this->getSession()->set('ninja_refresh_token', $data['refresh_token']);
        $this->getSession()->set('ninja_token_expiry', time() + 3600);
    }

    // Vérification de la validité des tokens
    public function hasValidTokens(): bool
    {
        $expiry = $this->getSession()->get('ninja_token_expiry');
        return $expiry !== null && $expiry > time();
    }

    // Récuperation des tickets
    public function getTickets(): array
    {
        if (!$this->hasValidTokens()) {
            $this->authenticate();
        }

        $this->accessToken = $this->getSession()->get('ninja_access_token');

        $response = $this->httpClient->request('POST', "{$this->ninjaOneApiUrl}/v2/ticketing/trigger/board/2/run", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'pageSize' => 10000,
                'lastCursorId' => 0
            ]
        ]);

        return $response->toArray();
    }

    // Récuperation des Patches Échoués
    public function getPatchesFailed(): array
    {
        if (!$this->hasValidTokens()) {
            $this->authenticate();
        }

        $this->accessToken = $this->getSession()->get('ninja_access_token');

        $response = $this->httpClient->request('GET', "{$this->ninjaOneApiUrl}/v2/queries/os-patch-installs?status=FAILED", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }


    // Récuperation des Logiciels Rejetés
    public function getSoftwaresRejected(): array
    {
        if (!$this->hasValidTokens()) {
            $this->authenticate();
        }

        $this->accessToken = $this->getSession()->get('ninja_access_token');

        $response = $this->httpClient->request('GET', "{$this->ninjaOneApiUrl}/v2/queries/software-patches?status=REJECTED", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }


    // Récuperation des Alertes Installés
    public function getAlerts(): array
    {
        if (!$this->hasValidTokens()) {
            $this->authenticate();
        }

        $this->accessToken = $this->getSession()->get('ninja_access_token');

        $response = $this->httpClient->request('GET', "{$this->ninjaOneApiUrl}/v2/alerts", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }

    // Récuperation des Systèmes d'Exploitation
    public function getOperatingSystems(): array
    {
        if (!$this->hasValidTokens()) {
            $this->authenticate();
        }

        $this->accessToken = $this->getSession()->get('ninja_access_token');

        $response = $this->httpClient->request('GET', "{$this->ninjaOneApiUrl}/v2/queries/operating-systems", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }

    // Récuperation des Antivirus
    public function getAntivirus(): array
    {
        if (!$this->hasValidTokens()) {
            $this->authenticate();
        }

        $this->accessToken = $this->getSession()->get('ninja_access_token');

        $response = $this->httpClient->request('GET', "{$this->ninjaOneApiUrl}/v2/queries/antivirus-threats", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);
        return $response->toArray();
    }

    // Récuperation des état de santé des appareils
    public function getDeviceHealths(): array
    {
        if (!$this->hasValidTokens()) {
            $this->authenticate();
        }

        $this->accessToken = $this->getSession()->get('ninja_access_token');

        $response = $this->httpClient->request('GET', "{$this->ninjaOneApiUrl}/v2/queries/device-health", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }
}
