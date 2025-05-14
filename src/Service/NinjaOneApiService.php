<?php

namespace App\Service;

use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class NinjaOneApiService
 * @package App\Service
 *
 * This class is responsible for interacting with the NinjaOne API.
 * It handles authentication and provides methods to retrieve organizations.
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

    public function hasValidTokens(): bool
    {
        $expiry = $this->getSession()->get('ninja_token_expiry');
        return $expiry !== null && $expiry > time();
    }

    public function getOrganizations(): array
    {
        if (!$this->hasValidTokens()) {
            $this->authenticate();
        }

        $this->accessToken = $this->getSession()->get('ninja_access_token');

        $response = $this->httpClient->request('GET', "{$this->ninjaOneApiUrl}/v2/organizations", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }
}
