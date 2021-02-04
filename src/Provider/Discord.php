<?php

namespace Panfu\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Discord extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    private $url = 'https://discord.com/api';

    /**
     * {@inheritdoc}
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->url.'/oauth2/authorize';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->url.'/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->url.'/users/@me';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultScopes()
    {
        return ['identify'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * {@inheritdoc}
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            $message = $response->getReasonPhrase();
            $code = $response->getStatusCode();

            if (isset($data['error'], $data['error_description'])) {
                $message = $data['error_description'];
            }

            throw new IdentityProviderException($message, $code, $data);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new DiscordResourceOwner($response);
    }
}
