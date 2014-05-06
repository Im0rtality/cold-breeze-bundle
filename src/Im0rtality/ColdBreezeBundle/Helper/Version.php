<?php

namespace Im0rtality\ColdBreezeBundle\Helper;

class Version
{
    protected $version = '0.0.1';

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getVersion()
    {
        return $this->version;
    }
} 
