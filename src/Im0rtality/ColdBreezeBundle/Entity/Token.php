<?php

namespace Im0rtality\ColdBreezeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\User;

class Token
{
    /** @var  integer */
    protected $id;
    /** @var  string */
    protected $value;
    /** @var  User */
    protected $user;
    /** @var  boolean */
    protected $shown = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public function isShown()
    {
        return $this->shown;
    }

    /**
     * @param boolean $shown
     */
    public function setShown($shown)
    {
        $this->shown = $shown;
    }
}
