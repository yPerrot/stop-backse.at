<?php

namespace App\Security;

use App\Entity\User;
use Depotwarehouse\OAuth2\Client\Twitch\Entity\TwitchUser;
use Depotwarehouse\OAuth2\Client\Twitch\Provider\Twitch;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use TwitchApi\TwitchApi;

class TwitchAuthenticator extends AbstractGuardAuthenticator
{

    use TargetPathTrait;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var RouterInterface $router
     */
    private RouterInterface $router;

    /**
     * @var TwitchApi $twitchApi
     */
    private TwitchApi $twitchApi;

    private Twitch $provider;

    /**
     * TwitchAuthenticator constructor.
     * @param EntityManagerInterface $em
     * @param RouterInterface $router
     * @param TwitchApi $twitchApi
     * @param Twitch $provider
     */
    public function __construct(
        EntityManagerInterface $em,
        RouterInterface $router,
        TwitchApi $twitchApi,
        Twitch $provider
    )
    {
        $this->em = $em;
        $this->router = $router;
        $this->twitchApi = $twitchApi;
        $this->provider = $provider;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        if ($request->attributes->get('_route') === 'twitch_redirect' && $request->isMethod('GET')) {
            return $request->query->has('code');
        }

        return false;
    }

    public function getCredentials(Request $request): ?array
    {
        return $request->query->has('code') && $request->query->has('state') ? [
            'code' => $request->query->get('code'),
            'loginToken' => $request->query->get('state')
        ] : null;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {

        try {
            $token = $this->provider->getAccessToken("authorization_code", [
                'code' => $credentials['code']
            ]);
        } catch (IdentityProviderException $e) {
            throw new BadCredentialsException($e->getMessage(), $e->getCode(), $e);
        }

        /** @var TwitchUser $twitchUser */
        $twitchUser = $this->provider->getResourceOwner($token);

        $user = $this->em->getRepository(User::class)
            ->findOneBy([
                'twitchId' => $twitchUser->getId()
            ]);

        if (!$user) {
            $user = new User();
        }

        $user
            ->setTwitchId($twitchUser->getId())
            ->setUsername($twitchUser->getUsername())
            ->setDisplayedUsername($twitchUser->getDisplayName())
            ->setAvatar($twitchUser->getLogo())
            ->setTwitchToken($token);

        $this->em->persist($user);
        $this->em->flush();

        return $user;

    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('but_the_caster_asked'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('but_the_caster_asked'));
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        $this->provider->scopes = '';
        return new RedirectResponse($this->provider->getAuthorizationUrl([
            'force_verify' => 'true'
        ]));
    }

    public function supportsRememberMe(): bool
    {
        return true;
    }
}
