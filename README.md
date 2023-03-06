# Discord Provider for OAuth 2.0 Client

[![Latest Version](https://img.shields.io/github/release/teampanfu/oauth2-discord.svg?style=flat-square)](https://github.com/teampanfu/oauth2-discord/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/teampanfu/oauth2-discord.svg?style=flat-square)](https://packagist.org/packages/teampanfu/oauth2-discord)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This package provides Discord OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use [Composer](https://getcomposer.org):

```sh
composer require teampanfu/oauth2-discord
```

## Usage

The first step in implementing OAuth2 is [registering a developer application](https://discord.com/developers/applications) and retrieving your client ID and client secret.

### Authorization Code Flow

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Panfu\OAuth2\Client\Provider\Discord;

session_start();

$provider = new Discord([
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_CLIENT_SECRET',
    'redirectUri' => 'http://localhost/callback-url',
]);

if (!empty($_GET['error'])) {
    // Got an error, probably user denied access
    exit('Got error: '.htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));
} else if (empty($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;
} else if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    // State is invalid, possible CSRF attack in progress
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {
    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);

    // Now that you have a token, you can retrieve a user's data
    try {
        $user = $provider->getResourceOwner($token);

        // Depending on which scope you use, you now have access to the user data
        printf('Hello %s#%s!', $user->getUsername(), $user->getDiscriminator());
    } catch (Exception $e) {
        // Failed to get user data
        exit('Something went wrong:'.$e->getMessage());
    }
}
```

### Retrieving User Data

When using the `getResourceOwner()` method to obtain the user node, it will be returned as a `DiscordUser` entity.

```php
$user = $provider->getResourceOwner($token);

$id = $user->getId();
var_dump($id);
# string(17) "80351110224678912"

$username = $user->getUsername();
var_dump($username);
# string(5) "Nelly"

$discriminator = $user->getDiscriminator();
var_dump($discriminator);
# string(4) "1337"

$avatar = $user->getAvatar();
var_dump($avatar);
# string(32) "8342729096ea3675442027381ff50dfe"

$isBot = $user->getBot();
var_dump($isBot);
# boolean false

$isSystem = $user->getSystem();
var_dump($isSystem);
# boolean false

$isMfaEnabled = $user->getMfaEnabled();
var_dump($isMfaEnabled);
# boolean true

$banner = $user->getBanner();
var_dump($banner);
# string(32) "06c16474723fe537c283b8efa61a30c8"

$accentColor = $user->getAccentColor();
var_dump($accentColor);
# int 16711680

$locale = $user->getLocale();
var_dump($locale);
# string(5) "en-GB"

$verified = $user->getVerified();
var_dump($verified);
# boolean true

$email = $user->getEmail();
var_dump($email);
# string(17) "nelly@discord.com"

$flags = $user->getFlags();
var_dump($flags);
# int 64

$premiumType = $user->getPremiumType();
var_dump($premiumType);
# int 1

$publicFlags = $user->getPublicFlags();
var_dump($publicFlags);
# int 64
```

You can also get all the data from the user node as a plain-old PHP array with `toArray()`.

```php
$userData = $user->toArray();
```

### Managing Scopes

When creating the authorization URL, you can specify different scopes.

```php
$options = [
    'scope' => ['identify', 'email', 'guilds.join'],
];

$authUrl = $provider->getAuthorizationUrl($options);
```

A list of [all available scopes](https://discord.com/developers/docs/topics/oauth2#shared-resources-oauth2-scopes) can be found in the Discord API documentation.

### Client Credentials Grant

Discord provides a client credentials flow for bot developers to get their own bearer tokens for testing purposes. This returns an access token for the bot owner:

```php
$provider = new Discord(...);

try {
    $accessToken = $provider->getAccessToken('client_credentials');
} catch (Exception $e) {
    exit('Something went wrong: '.$e->getMessage());
}
```

### Bot Authorization

To authorize a bot, specify the `bot` scope and set permissions appropriately:

```php
$provider = new Discord(...);

$options = [
    'scope' => ['bot'],
    'permissions' => 1,
];

$authUrl = $provider->getAuthorizationUrl($options);

header('Location: '.$authUrl);
```

## Testing

```sh
$ ./vendor/bin/phpunit
```

## Contribute

If you find a bug or have a suggestion for a feature, feel free to create a new issue or open a pull request.

We are happy about every contribution!

## License

This package is open-source software licensed under the [MIT License](LICENSE).
