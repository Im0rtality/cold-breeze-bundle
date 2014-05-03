<?php

namespace Im0rtality\ColdBreezeBundle\Security;

use Doctrine\ORM\EntityManager;
use Im0rtality\ColdBreezeBundle\Entity\Token;
use Sylius\Component\Core\Model\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TokenManager
{
    /** @var  EntityManager */
    protected $em;

    /**
     * @return string
     */
    public function generateToken()
    {
        return md5(uniqid('', true));
    }

    public function updateTokenForUser(User $user, $token)
    {
        $repo = $this->em->getRepository('Im0rtality\ColdBreezeBundle\Entity\Token');

        /** @var Token $entry */
        $entry = $repo->findOneBy(['user' => $user]);
        if ($entry) {
            $entry->setValue($token);
            $entry->setShown(false);
        } else {
            $entry = new Token();
            $entry->setValue($token);
            $entry->setUser($user);
            $entry->setShown(false);
        }

        $this->em->persist($entry);
        $this->em->flush();
    }

    public function retrieveTokenForUser(User $user)
    {
        $repo = $this->em->getRepository('Im0rtality\ColdBreezeBundle\Entity\Token');

        /** @var Token $token */
        $token = $repo->findOneBy(['user' => $user]);
        if (!$token) {
            throw new NotFoundHttpException();
        }
        if ($token->isShown()) {
            throw new AccessDeniedHttpException();
        }

        $token->setShown(true);

        $this->em->persist($token);
        $this->em->flush();

        return $token->getValue();
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
    }
}
