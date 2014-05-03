<?php

namespace Im0rtality\ColdBreezeBundle\Security;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

class Authenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    const TOKEN_NAME_PUBLIC = "~coldbreeze.public.access~";

    /** @var UserProvider */
    protected $userProvider;
    /** @var  HttpUtils */
    protected $httpUtils;

    public function __construct(UserProvider $userProvider, HttpUtils $httpUtils)
    {
        $this->userProvider = $userProvider;
        $this->httpUtils    = $httpUtils;
    }

    /**
     * @param Request $request
     * @param         $providerKey
     * @return PreAuthenticatedToken|UsernamePasswordToken
     * @throws \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function createToken(Request $request, $providerKey)
    {
        $checkRequestPath = function ($targetUrl) use ($request) {
            return $this->httpUtils->checkRequestPath($request, $targetUrl);
        };

        $truthy = function ($value) {
            return !empty($value);
        };

        // set the only URL where we should look for auth information
        // and only return the token if we're at that URL
        $publicUrls = [
            '/coldbreeze/token',
            '/coldbreeze/version',
        ];

        if ((new ArrayCollection($publicUrls))->map($checkRequestPath)->filter($truthy)->count() == 1) {
            return new PreAuthenticatedToken(self::TOKEN_NAME_PUBLIC, null, $providerKey);
        }

        if (!$request->headers->has('Token')) {
            throw new BadCredentialsException('No token found');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $request->headers->get('Token'),
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if ($token->getUser() == self::TOKEN_NAME_PUBLIC) {
            return $token;
        }
        $token    = $token->getCredentials();
        $username = $this->userProvider->getUsernameForToken($token);

        if (!$username) {
            throw new AuthenticationException(
                sprintf('Token "%s" does not exist.', $token)
            );
        }

        $user = $this->userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken($user, $token, $providerKey, $user->getRoles());
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return ($token instanceof PreAuthenticatedToken) && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw new AccessDeniedHttpException("Authentication failed", $exception);
    }
}
