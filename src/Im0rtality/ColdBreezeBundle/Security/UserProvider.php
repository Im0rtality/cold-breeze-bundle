<?php

namespace Im0rtality\ColdBreezeBundle\Security;

use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider extends FOSUBUserProvider implements UserProviderInterface
{
    public function getUsernameForToken($token)
    {
        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        $username = "admin@admin.com";

        return $username;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed.", 403);
    }
}
