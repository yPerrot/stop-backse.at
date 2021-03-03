<?php


namespace App\Service;

use Depotwarehouse\OAuth2\Client\Twitch\Provider\Twitch;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TwitchAuthenticationService
 * @package App\Service
 */
class TwitchAuthenticationService
{
    public const AUTH_URL = 'https://id.twitch.tv/oauth2/authorize';
    public const TOKEN_URL = 'https://id.twitch.tv/oauth2/token';

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;
    /**
     * @var string
     */
    private string $clientId;
    /**
     * @var string
     */
    private string $clientSecret;
    /**
     * @var string
     */
    private string $redirectUrl;
    /**
     * @var RouterInterface
     */
    private RouterInterface $router;
    /**
     * @var SessionInterface
     */
    private SessionInterface $sessionInterface;

    private Twitch $provider;

    /**
     * TwitchAuthenticationService constructor.
     * @param HttpClientInterface $httpClient
     * @param RouterInterface $router
     * @param SessionInterface $sessionInterface
     * @param Twitch $provider
     */
    public function __construct(HttpClientInterface $httpClient, RouterInterface $router, SessionInterface $sessionInterface, Twitch $provider)
    {
        $this->httpClient = $httpClient;
        $this->router = $router;
        $this->sessionInterface = $sessionInterface;
        $this->provider = $provider;
    }

    /**
     * @param string $redirectUrl
     * @return TwitchAuthenticationService
     */
    public function setRedirectUrl(string $redirectUrl): TwitchAuthenticationService
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     */
    public function setCredentials(string $clientId, string $clientSecret): void
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param array $additionalScope
     * @return string
     * @throws \Exception
     */
    public function authorizeUrl(array $additionalScope = [])
    {
        $this->sessionInterface->set('loginToken', md5(random_int(PHP_INT_MIN, PHP_INT_MAX)));

        $params = http_build_query([
            "client_id" => $this->clientId,
            "redirect_uri" => $this->redirectUrl,
            "response_type" => 'code',
            "scope" => "user:read:email" . implode(" ", $additionalScope),
            "force_verify" => 'true',
            "state" => $this->sessionInterface->get('loginToken')
        ]);

        return self::AUTH_URL . '?' . $params;
    }


}