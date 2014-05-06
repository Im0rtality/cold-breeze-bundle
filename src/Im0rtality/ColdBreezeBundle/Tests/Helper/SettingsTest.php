<?php

namespace src\Im0rtality\ColdBreezeBundle\Tests\Helper;

use Im0rtality\ColdBreezeBundle\Helper\Settings;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManager;

class SettingsTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Settings */
    protected $helper;

    public function setUp()
    {
        $this->helper = new Settings();
    }

    /**
     * @param $settings
     * @return SettingsManager
     */
    private function getSettingsManagerMock($settings)
    {
        $manager = $this->getMockBuilder('Sylius\Bundle\SettingsBundle\Manager\SettingsManager')
            ->disableOriginalConstructor()
            ->setMethods(['loadSettings'])
            ->getMock();

        $manager->expects($this->once())->method('loadSettings')->with('general')->will(
            $this->returnValue($settings)
        );

        return $manager;
    }

    /**
     * @param string $baseUrl
     * @return CacheManager
     */
    private function getImagineCacheManagerMock($baseUrl)
    {
        $manager = $this->getMockBuilder('Liip\ImagineBundle\Imagine\Cache\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods(['generateUrl'])
            ->getMock();

        $manager->expects($this->once())->method('generateUrl')->will($this->returnValue($baseUrl));

        return $manager;
    }

    public function testGetCurrency()
    {
        $this->helper->setSettingsManager($this->getSettingsManagerMock(['currency' => 'foo']));

        $this->assertEquals('foo', $this->helper->getCurrency());
    }

    public function getTestGetImagineSettingsData() {
        $out = [];
        $out[] = ['cache', 'cache'];
        $out[] = ['app.php/cache', 'cache'];
        $out[] = ['app_prod.php/cache', 'cache'];
        $out[] = ['app_prod.php/imagine/cache', 'imagine/cache'];
        return $out;
    }
    /**
     * @dataProvider getTestGetImagineSettingsData
     */
    public function testGetImagineSettings($baseUrl, $expectedUrl)
    {
        $this->helper->setImagineCacheManager($this->getImagineCacheManagerMock($baseUrl));
        $this->helper->setImagineFilters(['foo' => 'this does not matter', 'bar' => 'neither does this']);

        $this->assertEquals(['filters' => ['foo', 'bar'], 'baseUrl' => $expectedUrl], $this->helper->getImagineSettings());
    }

}
