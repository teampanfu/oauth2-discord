# Discord Provider for OAuth 2.0 Client

[![Total Downloads](https://img.shields.io/packagist/dt/teampanfu/oauth2-discord?style=flat-square)](https://packagist.org/packages/teampanfu/oauth2-discord)
[![Latest Version](https://img.shields.io/packagist/v/teampanfu/oauth2-discord?style=flat-square)](https://packagist.org/packages/teampanfu/oauth2-discord)
[![Software License](https://img.shields.io/packagist/l/teampanfu/oauth2-discord?style=flat-square)](LICENSE.md)

This package provides Discord OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

Use [Composer](https://getcomposer.org/) to install the package in your application.

```bash
composer require teampanfu/oauth2-discord
```

## Usage

### Authorization Code Flow

```php
require __DIR__.'/vendor/autoload.php';

use Panfu\OAuth2\Client\Provider\Discord;

session_start();

$provider = new Discord([
    'clientId'     => '{discord-client-id}',
    'clientSecret' => '{discord-client-secret}',
    'redirectUri'  => 'http://localhost/callback',
]);

if (! isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token, you can look up a user's profile data
    try {

        // We got an access token, let's now get the user details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s#%s!', $user->getUsername(), $user->getDiscriminator());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Error: '.$e->getMessage());

    }

}
```

### Available user details

- `getAvatarHash()` - the user's avatar hash.
- `getDiscriminator()` - the user's 4-digit discord-tag.
- `getEmail()` - the user's email.
- `getFlags()` - the [flags](https://discord.com/developers/docs/resources/user#user-object-user-flags) on a user's account.
- `getId()` - the user's id.
- `getLocale()` - the user's chosen language option.
- `getTwoFactorEnabled()` - whether the user has two factor enabled on their account.
- `getPremiumType()` - the [type](https://discord.com/developers/docs/resources/user#user-object-premium-types) of Nitro subscription on a user's account.
- `getUsername()` - the user's username, not unique across the platform.
- `getVerified()` - whether the email on this account has been verified.

## Contributing

Thank you for considering contributing to this Discord OAuth 2.0 Provider! If you've found a bug or have a feature suggestion, please open a new issue.

## License

This Discord OAuth 2.0 Provider is open-sourced software licensed under the [MIT license](LICENSE.md).
