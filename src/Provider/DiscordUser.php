<?php

namespace Panfu\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class DiscordUser implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * @var array
     */
    protected $response;

    /**
     * @param  array  $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * The user's banner color encoded as an integer representation of hexadecimal color code.
     *
     * @return int|null
     */
    public function getAccentColor(): ?int
    {
        return $this->getValueByKey($this->response, 'accent_color');
    }

    /**
     * The user's avatar hash.
     *
     * @link   https://discord.com/developers/docs/reference#image-formatting
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->getValueByKey($this->response, 'avatar');
    }

    /**
     * The user's banner hash.
     *
     * @link   https://discord.com/developers/docs/reference#image-formatting
     *
     * @return string|null
     */
    public function getBanner(): ?string
    {
        return $this->getValueByKey($this->response, 'banner');
    }

    /**
     * Whether the user belongs to an OAuth2 application.
     *
     * @return bool
     */
    public function getBot(): bool
    {
        return $this->getValueByKey($this->response, 'bot');
    }

    /**
     * The user's 4-digit discord-tag.
     *
     * @return string
     */
    public function getDiscriminator(): string
    {
        return $this->getValueByKey($this->response, 'discriminator');
    }

    /**
     * The user's email.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getValueByKey($this->response, 'email');
    }

    /**
     * The flags on a user's account
     *
     * @link   https://discord.com/developers/docs/resources/user#user-object-user-flags
     *
     * @return int
     */
    public function getFlags(): int
    {
        return $this->getValueByKey($this->response, 'flags');
    }

    /**
     * The user's id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->getValueByKey($this->response, 'id');
    }

    /**
     * The user's chosen language option.
     *
     * @link   https://discord.com/developers/docs/reference#locales
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->getValueByKey($this->response, 'locale');
    }

    /**
     * The public flags on a user's account
     *
     * @link   https://discord.com/developers/docs/resources/user#user-object-user-flags
     *
     * @return int
     */
    public function getPublicFlags(): int
    {
        return $this->getValueByKey($this->response, 'public_flags');
    }

    /**
     * The type of Nitro subscription on a user's account.
     *
     * @link   https://discord.com/developers/docs/resources/user#user-object-premium-types
     *
     * @return int
     */
    public function getPremiumType(): int
    {
        return $this->getValueByKey($this->response, 'premium_type');
    }

    /**
     * Wether the user is an Official Discord System user (part of the urgent message system).
     *
     * @return bool
     */
    public function getSystem(): bool
    {
        return $this->getValueByKey($this->response, 'system');
    }

    /**
     * Whether the user has two factor enabled on their account.
     *
     * @return bool
     */
    public function getMfaEnabled(): bool
    {
        return $this->getValueByKey($this->response, 'mfa_enabled');
    }

    /**
     * The user's username, not unique across the platform.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getValueByKey($this->response, 'username');
    }

    /**
     * Whether the email on this account has been verified.
     *
     * @return bool
     */
    public function getVerified(): bool
    {
        return $this->getValueByKey($this->response, 'verified');
    }

    /**
     * Returns the raw resource owner response.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
