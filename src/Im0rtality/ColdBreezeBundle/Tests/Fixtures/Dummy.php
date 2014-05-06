<?php

namespace Im0rtality\ColdBreezeBundle\Tests\Fixtures;

class Dummy
{
    protected $id;

    public function __construct($payload)
    {
        foreach ($payload as $field => $value) {
            $this->$field = $value;
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
