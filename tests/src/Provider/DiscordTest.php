<?php

namespace Panfu\OAuth2\Client\Test\Provider;

use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Tool\QueryBuilderTrait;
use Mockery as m;
use Panfu\OAuth2\Client\Provider\Discord;
use Panfu\OAuth2\Client\Provider\Exception\DiscordIdentityProviderException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class DiscordTest extends TestCase
{
    use QueryBuilderTrait;

    /**
     * @var Discord
     */
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new Discord([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    public function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testScopes(): void
    {
        $scopeSeparator = ' ';
        $options = ['scope' => [uniqid(), uniqid()]];
        $query = ['scope' => implode($scopeSeparator, $options['scope'])];
        $url = $this->provider->getAuthorizationUrl($options);
        $encodedScope = $this->buildQueryString($query);
        $this->assertStringContainsString($encodedScope, $url);
    }

    public function testGetAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertEquals('/api/oauth2/authorize', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl(): void
    {
        $params = [];

        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);

        $this->assertEquals('/api/oauth2/token', $uri['path']);
    }

    public function testGetAccessToken(): void
    {
        $response = m::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')->andReturn('{"access_token": "mock_access_token", "token_type": "Bearer", "scope": "identify"}');
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $client = m::mock(ClientInterface::class);
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertNull($token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    public function testUserData(): void
    {
        $postResponse = m::mock(ResponseInterface::class);
        $postResponse->shouldReceive('getBody')->andReturn('{"access_token":"mock_access_token","token_type":"Bearer","scope":"identify"}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $postResponse->shouldReceive('getStatusCode')->andReturn(200);

        $exampleUser = '{"id":"80351110224678912","username":"Nelly","discriminator":"1337","avatar":"8342729096ea3675442027381ff50dfe","verified":true,"email":"nelly@discord.com","flags":64,"banner":"06c16474723fe537c283b8efa61a30c8","accent_color":16711680,"premium_type":1,"public_flags":64}';

        $userResponse = m::mock(ResponseInterface::class);
        $userResponse->shouldReceive('getBody')->andReturn($exampleUser);
        $userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $userResponse->shouldReceive('getStatusCode')->andReturn(200);

        $client = m::mock(ClientInterface::class);
        $client->shouldReceive('send')->times(2)->andReturn($postResponse, $userResponse);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $user = $this->provider->getResourceOwner($token);

        $this->assertEquals('80351110224678912', $user->getId());
        $this->assertEquals('Nelly', $user->getUsername());
        $this->assertEquals('1337', $user->getDiscriminator());
        $this->assertEquals('8342729096ea3675442027381ff50dfe', $user->getAvatar());
        $this->assertEquals(true, $user->getVerified());
        $this->assertEquals('nelly@discord.com', $user->getEmail());
        $this->assertEquals(64, $user->getFlags());
        $this->assertEquals('06c16474723fe537c283b8efa61a30c8', $user->getBanner());
        $this->assertEquals(16711680, $user->getAccentColor());
        $this->assertEquals(1, $user->getPremiumType());
        $this->assertEquals(64, $user->getPublicFlags());
    }

    public function testExceptionThrownWhenErrorObjectReceived(): void
    {
        $this->expectException(DiscordIdentityProviderException::class);

        $status = mt_rand(400, 600);

        $postResponse = m::mock(ResponseInterface::class);
        $postResponse->shouldReceive('getBody')->andReturn('{"client_id": ["This field is required"]}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'appliction/json']);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);
        $postResponse->shouldReceive('getReasonPhrase');

        $client = m::mock(ClientInterface::class);
        $client->shouldReceive('send')->times(1)->andReturn($postResponse);
        $this->provider->setHttpClient($client);
        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }
}
