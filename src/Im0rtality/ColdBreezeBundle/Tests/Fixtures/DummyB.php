<?php

namespace Im0rtality\ColdBreezeBundle\Tests\Fixtures;

class DummyB extends Dummy
{
    protected $baz = 3;
    protected $qux = 4;

    /**
     * @return mixed
     */
    public function getBaz()
    {
        return $this->baz;
    }

    /**
     * @return mixed
     */
    public function isQux()
    {
        return $this->qux;
    }
}
