<?php

namespace Panfu\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Panfu\OAuth2\Client\Provider\Exception\DiscordIdentityProviderException;
use Psr\Http\Message\ResponseInterface;

/**
 * @method DiscordUser getResourceOwner(AccessToken $token)
 */
class Discord extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * The endpoint to the Discord API.
     *
     * @var string
     */
    protected const BASE_API_URL = 'https://discord.com/api';

    /**
     * {@inheritdoc}
     */
    public function getBaseAuthorizationUrl(): string
    {
        return self::BASE_API_URL.'/oauth2/authorize';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return self::BASE_API_URL.'/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return self::BASE_API_URL.'/users/@me';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultScopes(): array
    {
        return ['identify'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getScopeSeparator(): string
    {
        return ' ';
    }

    /**
     * {@inheritdoc}
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw DiscordIdentityProviderException::clientException($response, $data);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createResourceOwner(array $response, AccessToken $token): DiscordUser
    {
        return new DiscordUser($response);
    }
}
