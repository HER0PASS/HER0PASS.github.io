<?php

namespace App\Services;

class TwitchCredentialManager
{
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->clientId = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
    }

    public function getCredentials(): array
    {
        $apiUrl = 'https://id.twitch.tv/oauth2/token';
        $data = http_build_query([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials'
        ]);

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
        ];

        [$response, $httpCode] = $this->getApiResponse($apiUrl, $headers, $data);

        $responseData = json_decode($response, true);

        if ($httpCode !== 200 || !isset($responseData['access_token'])) {
            throw new \RuntimeException('Failed to obtain Twitch access token. HTTP Code: ' . $httpCode);
        }

        return [
            'client_id' => $this->clientId,
            'access_token' => $responseData['access_token'],
        ];
    }

    /**
     * @param string $apiUrl
     * @param array $headers
     * @param string $data
     * @return array
     */
    public function getApiResponse(string $apiUrl, array $headers, string $data): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$response, $httpCode];
    }
}
