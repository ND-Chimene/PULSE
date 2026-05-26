<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GraphMailService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $tenantId,
        private string $clientId,
        private string $clientSecret,
        private string $userEmail,
    ) {}

    private function getAccessToken(): string
    {
        $response = $this->httpClient->request('POST',
            "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ]
            ]
        );

        $data = $response->toArray();

        return $data['access_token'];
    }

    public function send(string $to, string $subject, string $html): void
    {
        $token = $this->getAccessToken();

        $this->httpClient->request(
            'POST',
            "https://graph.microsoft.com/v1.0/users/{$this->userEmail}/sendMail",
            [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'subject' => $subject,
                        'body' => [
                            'contentType' => 'HTML',
                            'content' => $html,
                        ],
                        'toRecipients' => [
                            [
                                'emailAddress' => [
                                    'address' => $to,
                                ]
                            ]
                        ],
                    ],
                    'saveToSentItems' => true,
                ]
            ]
        );
    }
}