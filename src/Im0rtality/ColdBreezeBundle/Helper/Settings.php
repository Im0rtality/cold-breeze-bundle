<?php

namespace Im0rtality\ColdBreezeBundle\Helper;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManager;

class Settings
{
    protected $imagineFilters;
    /**
     * @var CacheManager
     */
    protected $imagineCacheManager;
    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * @param SettingsManager $settingsManager
     */
    public function setSettingsManager($settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

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
    public function setImagineCacheManager(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function getImagineSettings()
    {
        $settings['filters'] = array_keys($this->imagineFilters);
        $settings['baseUrl'] = str_replace(
            [$settings['filters'][0], 'foo'],
            ['', ''],
            $this->imagineCacheManager->generateUrl('foo', $settings['filters'][0])
        );
        $settings['baseUrl'] = preg_replace(['/^[\/]{0,1}app.*.php\//','/\/*$/'], ['',''], $settings['baseUrl']);
        return $settings;
    }

    public function getCurrency()
    {
        $settings = $this->settingsManager->loadSettings('general');

        return $settings['currency'];
    }
}
