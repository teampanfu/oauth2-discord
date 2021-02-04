<?php

namespace Panfu\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class DiscordResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * @var array
     */
    protected $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * The user's avatar hash.
     *
     * @return string
     */
    public function getAvatarHash()
    {
        return $this->getValueByKey($this->response, 'avatar');
    }

    /**
     * The user's 4-digit discord-tag.
     *
     * @return string
     */
    public function getDiscriminator()
    {
        return $this->getValueByKey($this->response, 'discriminator');
    }

    /**
     * The user's email.
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'email');
    }

    /**
     * The flags on a user's account
     *
     * @see    https://discord.com/developers/docs/resources/user#user-object-user-flags
     * @return string
     */
    public function getFlags()
    {
        return $this->getValueByKey($this->response, 'flags');
    }

    /**
     * The user's id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getValueByKey($this->response, 'id');
    }

    /**
     * The user's chosen language option.
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->getValueByKey($this->response, 'locale');
    }

    /**
     * Whether the user has two factor enabled on their account.
     *
     * @return bool
     */
    public function getTwoFactorEnabled()
    {
        return $this->getValueByKey($this->response, 'mfa_enabled', false);
    }

    /**
     * The type of Nitro subscription on a user's account.
     *
     * @see    https://discord.com/developers/docs/resources/user#user-object-premium-types
     * @return string
     */
    public function getPremiumType()
    {
        return $this->getValueByKey($this->response, 'premium_type');
    }

    /**
     * The user's username, not unique across the platform.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getValueByKey($this->response, 'username');
    }

    /**
     * Whether the email on this account has been verified.
     *
     * @return bool
     */
    public function getVerified()
    {
        return $this->getValueByKey($this->response, 'verified', false);
    }

    /**
     * Returns the raw resource owner response.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
