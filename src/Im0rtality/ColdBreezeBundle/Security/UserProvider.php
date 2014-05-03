<?php

namespace Im0rtality\ColdBreezeBundle\Security;

use Doctrine\ORM\EntityManager;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Im0rtality\ColdBreezeBundle\Entity\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider extends FOSUBUserProvider implements UserProviderInterface
{
    /** @var  EntityManager */
    protected $em;

    public function getUsernameForToken($token)
    {
        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        $tokenRepo = $this->em->getRepository('Im0rtality\ColdBreezeBundle\Entity\Token');

        /** @var Token $token */
        $token = $tokenRepo->findOneBy(['value' => $token]);
        if ($token) {
            return $token->getUser()->getUsernameCanonical();
        } else {
            return null;
        }
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
    }
}
