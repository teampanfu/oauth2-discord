# Discord Provider for OAuth 2.0 Client

[![Latest Version](https://img.shields.io/github/release/teampanfu/oauth2-discord.svg?style=flat-square)](https://github.com/teampanfu/oauth2-discord/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/teampanfu/oauth2-discord.svg?style=flat-square)](https://packagist.org/packages/teampanfu/oauth2-discord)

This package provides Discord OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Requirements

The following versions of PHP are supported.

- PHP 8.0
- PHP 8.1

## Installation

To install, use composer:

```sh
composer require teampanfu/oauth2-discord
```

## Usage

If you don't have a Client ID and Client Secret yet, first [create a new application](https://discord.com/developers/applications) in the Discord Developer Portal.

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

Take a look at the [User Structure](https://discord.com/developers/docs/resources/user#user-object-user-structure) in the documentation to find out what data is available.

You can then call the appropriate method on the resource owner:

```php
$user = $provider->getResourceOwner($token);

$user->getId();
$user->getUsername();
$user->getAvatar();
$user->getMfaEnabled();
$user->getPremiumType();
...
```

### Managing Scopes

When creating the authorization URL, you can specify different scopes.

```php
$options = [
    'scope' => ['identify', 'email', 'guilds.join'], // default is ['identify']
];

$authUrl = $provider->getAuthorizationUrl($options);
```

A list of [all available scopes](https://discord.com/developers/docs/topics/oauth2#shared-resources-oauth2-scopes) can be found in the documentation.

### Refreshing a Token

You can refresh an expired token with a refresh token instead of going through the entire process of obtaining a new token. To do this, simply use the expired token and request a refresh:

```php
$provider = new Discord(...);

if ($existingAccessToken->hasExpired()) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $existingAccessToken->getRefreshToken(),
    ]);

    // Replace the expired token with the new one.
}
```

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

## Contribute

If you find a bug or have a suggestion for a feature, feel free to create a new issue or open a pull request.

We are happy about every contribution!

## License

This package is open-source software licensed under the [MIT License](LICENSE).
