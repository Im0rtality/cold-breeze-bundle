<?php

namespace Im0rtality\ColdBreezeBundle\Tests\Helper;

use Im0rtality\ColdBreezeBundle\Helper\Statistics;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Order\Model\Order;

class StatisticsTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Statistics|\PHPUnit_Framework_MockObject_MockObject */
    protected $helper;

    public function setUp()
    {
        $this->helper = $this->getMockBuilder('Im0rtality\ColdBreezeBundle\Helper\Statistics')
            ->setMethods(['getUsers'])
            ->getMock();
    }

    /**
     * @param $orders
     * @return OrderRepository
     */
    private function getOrdersRepoMock($orders)
    {
        $manager = $this->getMockBuilder('Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findBetweenDates'])
            ->getMock();

        $manager->expects($this->once())->method('findBetweenDates')->will($this->returnValue($orders));

        return $manager;
    }

    /**
     * @param $total
     * @return Order
     */
    public function buildOrder($total)
    {
        $order = $this->getMockBuilder('\stdClass')->setMethods(['getTotal'])->getMock();
        $order->expects($this->once())->method('getTotal')->will($this->returnValue($total));
        return $order;
    }

    public function getTestGetStatisticsData()
    {
        $out   = [];
        $out[] = [
            [$this->buildOrder(10), $this->buildOrder(20)],
            [''],
            [
                'activeUsers'          => 1,
                'orders'               => 2,
                'revenue'              => 30,
                'averageOrderValue'    => 15.0,
                'ordersPerActiveUser'  => 2.0,
                'revenuePerActiveUser' => 30.0,
            ]
        ];

        return $out;
    }

    /**
     * @dataProvider getTestGetStatisticsData
     */
    public function testGetStatistics($orders, $users, $expected)
    {
        $this->helper->setOrderRepository($this->getOrdersRepoMock($orders));
        $this->helper->expects($this->once())->method('getUsers')->will($this->returnValue($users));

        $this->assertEquals($expected, $this->helper->getStatisticalData());
    }
}
