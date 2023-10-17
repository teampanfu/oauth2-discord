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
     * Create a new Discord user.
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * The user's banner color encoded as an integer representation of hexadecimal color code.
     */
    public function getAccentColor(): ?int
    {
        return $this->getValueByKey($this->response, 'accent_color');
    }

    /**
     * The user's avatar hash.
     *
     * @link https://discord.com/developers/docs/reference#image-formatting
     */
    public function getAvatar(): ?string
    {
        return $this->getValueByKey($this->response, 'avatar');
    }

    /**
     * The user's avatar decoration hash.
     *
     * @link https://discord.com/developers/docs/reference#image-formatting
     */
    public function getAvatarDecoration(): ?string
    {
        return $this->getValueByKey($this->response, 'avatar_decoration');
    }

    /**
     * The user's banner hash.
     *
     * @link https://discord.com/developers/docs/reference#image-formatting
     */
    public function getBanner(): ?string
    {
        return $this->getValueByKey($this->response, 'banner');
    }

    /**
     * Whether the user belongs to an OAuth2 application.
     */
    public function getBot(): bool
    {
        return $this->getValueByKey($this->response, 'bot');
    }

    /**
     * The user's 4-digit discord-tag.
     */
    public function getDiscriminator(): string
    {
        return $this->getValueByKey($this->response, 'discriminator');
    }

    /**
     * The user's email.
     */
    public function getEmail(): ?string
    {
        return $this->getValueByKey($this->response, 'email');
    }

    /**
     * The flags on a user's account
     *
     * @link https://discord.com/developers/docs/resources/user#user-object-user-flags
     */
    public function getFlags(): int
    {
        return $this->getValueByKey($this->response, 'flags');
    }

    /**
     * The user's display name, if it is set. For bots, this is the application name.
     */
    public function getGlobalName(): ?string
    {
        return $this->getValueByKey($this->response, 'global_name');
    }

    /**
     * The user's id.
     */
    public function getId(): string
    {
        return $this->getValueByKey($this->response, 'id');
    }

    /**
     * The user's chosen language option.
     *
     * @link https://discord.com/developers/docs/reference#locales
     */
    public function getLocale(): string
    {
        return $this->getValueByKey($this->response, 'locale');
    }

    /**
     * The public flags on a user's account
     *
     * @link https://discord.com/developers/docs/resources/user#user-object-user-flags
     */
    public function getPublicFlags(): int
    {
        return $this->getValueByKey($this->response, 'public_flags');
    }

    /**
     * The type of Nitro subscription on a user's account.
     *
     * @link https://discord.com/developers/docs/resources/user#user-object-premium-types
     */
    public function getPremiumType(): int
    {
        return $this->getValueByKey($this->response, 'premium_type');
    }

    /**
     * Wether the user is an Official Discord System user (part of the urgent message system).
     */
    public function getSystem(): bool
    {
        return $this->getValueByKey($this->response, 'system');
    }

    /**
     * Whether the user has two factor enabled on their account.
     */
    public function getMfaEnabled(): bool
    {
        return $this->getValueByKey($this->response, 'mfa_enabled');
    }

    /**
     * The user's username, not unique across the platform.
     */
    public function getUsername(): string
    {
        return $this->getValueByKey($this->response, 'username');
    }

    /**
     * Whether the email on this account has been verified.
     */
    public function getVerified(): bool
    {
        return $this->getValueByKey($this->response, 'verified');
    }

    /**
     * Returns the raw resource owner response.
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
