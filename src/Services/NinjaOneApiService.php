<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NinjaOneApiService
{
    private HttpClientInterface $httpClient;
    private string $ninjaUrl;
    private ?string $accessToken = null;

    public function __construct(HttpClientInterface $httpClient, string $ninjaUrl = 'https://eu.ninjarmm.com')
    {
        $this->httpClient = $httpClient;
        $this->ninjaUrl = $ninjaUrl;
    }

    public function authenticate(string $clientId, string $clientSecret): void
    {
        $response = $this->httpClient->request('POST', "{$this->ninjaUrl}/ws/oauth/token", [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'monitoring offline_access control management',
            ],
        ]);

        $data = $response->toArray();

        if (!isset($data['access_token'])) {
            throw new \RuntimeException('Failed to retrieve access token.');
        }

        $this->accessToken = $data['access_token'];
    }

    public function getOrganizations(): array
    {
        if (!$this->accessToken) {
            throw new \RuntimeException('You must authenticate first.');
        }

        $response = $this->httpClient->request('GET', "{$this->ninjaUrl}/v2/organizations-detailed", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }
}