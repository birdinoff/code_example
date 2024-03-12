<?php

declare(strict_types=1);

namespace App\Services\Hrm\HttpClient;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

abstract class Client
{
    private ?string $token = null;

    protected function request(): PendingRequest
    {
        return Http::withToken($this->getToken())
            ->retry(
                2,
                0,
                function (Exception $exception, PendingRequest $request) {
                    if (!$exception instanceof RequestException
                        || $exception->response->status() !== 401
                    ) {
                        return false;
                    }

                    $request->withToken($this->refreshToken());
                }
            );
    }

    protected function getToken(): string
    {
        if ($this->token === null) {
            $this->token = $this->refreshToken();
        }

        return $this->token;
    }

    protected function refreshToken(): string
    {
        /** @var string $authUrl */
        $authUrl = config('services.hrm.auth_url');
        $authResponse = Http::asForm()
            ->post(
                $authUrl,
                [
                    'grant_type' => 'password',
                    'client_id' => config('services.hrm.client_id'),
                    'username' => config('services.hrm.api_username'),
                    'password' => config('services.hrm.api_password'),
                ]
            );
        /** @var string $accessToken */
        $accessToken = $authResponse->json('access_token');

        $this->token = $accessToken;

        return $accessToken;
    }
}
