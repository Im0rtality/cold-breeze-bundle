<?php

namespace Im0rtality\ColdBreezeBundle\Helper;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class Settings
{
    protected $imagineFilters;
    /**
     * @var CacheManager
     */
    protected $imagineCacheManager;

    /**
     * @param mixed $imagineFilters
     */
    public function setImagineFilters($imagineFilters)
    {
        $this->imagineFilters = $imagineFilters;
    }

    /**
     * @param CacheManager $imagineCacheManager
     */
    public function setImagineCacheManager($imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function getImagineSettings()
    {
        $settings['filters'] = array_keys($this->imagineFilters);
        $settings['baseUrl'] = str_replace(
            [$settings['filters'][0], 'foo', '//'],
            ['', '', ''],
            $this->imagineCacheManager->generateUrl('foo', $settings['filters'][0])
        );
        return $settings;
    }
} 
