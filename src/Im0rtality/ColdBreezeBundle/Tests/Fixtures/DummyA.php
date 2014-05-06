<?php

namespace Im0rtality\ColdBreezeBundle\Tests\Fixtures;

class DummyA extends Dummy{
    protected $foo;
    protected $bar;

    /**
     * @return mixed
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * @return mixed
     */
    public function getFoo()
    {
        return $this->foo;
    }
}
