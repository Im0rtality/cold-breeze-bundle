<?php

namespace Im0rtality\ColdBreezeBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Authenticator implements SimplePreAuthenticatorInterface
{
    protected $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function createToken(Request $request, $providerKey)
    {
        if (!$request->query->has('token')) {
            throw new BadCredentialsException('No token found');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $request->query->get('token'),
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $token   = $token->getCredentials();
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
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}
