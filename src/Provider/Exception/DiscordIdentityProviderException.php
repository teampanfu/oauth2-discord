<?php

namespace Panfu\OAuth2\Client\Provider\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class DiscordIdentityProviderException extends IdentityProviderException
{
    /**
     * Creates client exception from response.
     */
    public static function clientException(ResponseInterface $response, array $data): DiscordIdentityProviderException
    {
        $message = isset($data['message']) ? $data['message'] : json_encode($data);
        $code = $response->getStatusCode();
        $body = (string) $response->getBody();

        return new static($message, $code, $body);
    }
}
