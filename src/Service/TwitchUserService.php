<?php


namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use TwitchApi\Exceptions\TwitchApiException;

/**
 * Class TwitchUserService
 * @package App\Service\Twitch
 */
class TwitchUserService
{
    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * @var string
     */
    private string $twitchClientId;

    /**
     * TwitchUserService constructor.
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $twitchClientId
     */
    public function setTwitchClientId(string $twitchClientId): void
    {
        $this->twitchClientId = $twitchClientId;
    }


    /**
     * @param $token
     * @return array
     * @throws \JsonException
     * @throws TransportExceptionInterface
     * @throws TwitchApiException
     */
    public function fetchUserByBearerToken($token): array
    {
        $url = 'https://api.twitch.tv/helix/users';
        $headers = array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.twitchtv.v5+json',
            'Client-ID' => $this->twitchClientId
        );

        $request = $this->httpClient->request('GET', $url, [
            'headers' => $headers
        ]);
        try {
            $result = $request->getContent();
        } catch (ClientExceptionInterface | TransportExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            throw new TwitchApiException('Error while trying to get bearer token', $e->getCode());
        }

        if ($request->getStatusCode() !== 200) {
            throw new BadCredentialsException('authentication error');
        }

        $twitchUser = json_decode($result, true, 512, JSON_THROW_ON_ERROR)['data'][0];

        return [
            'twitchId' => $twitchUser['id'],
            'twitchLogin' => $twitchUser['login'],
            'twitchDisplayname' => $twitchUser['display_name'],
            'twitchEmail' => $twitchUser['email'],
            'twitchProfileUrl' => $twitchUser['profile_image_url']
        ];

    }

}