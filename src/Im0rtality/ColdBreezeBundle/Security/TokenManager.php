<?php

namespace Im0rtality\ColdBreezeBundle\Security;

use Doctrine\ORM\EntityManager;
use Im0rtality\ColdBreezeBundle\Entity\Token;
use Sylius\Component\Core\Model\User;

class TokenManager
{
    /** @var  EntityManager */
    protected $em;

    /**
     * @return string
     */
    public function generateToken()
    {
        return md5(uniqid());
    }

    public function updateTokenForUser(User $user, $token)
    {
        $repo = $this->em->getRepository('Im0rtality\ColdBreezeBundle\Entity\Token');

        /** @var Token $entry */
        $entry = $repo->findOneBy(['user' => $user]);
        if ($entry) {
            $entry->setValue($token);
        } else {
            $entry = new Token();
            $entry->setValue($token);
            $entry->setUser($user);
        }

        $this->em->persist($entry);
        $this->em->flush();
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
    }
}
