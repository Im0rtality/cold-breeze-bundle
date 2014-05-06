<?php

namespace Im0rtality\ColdBreezeBundle\Helper;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Order\Model\Order;

class Statistics
{
    /** @var OrderRepository */
    protected $orderRepository;
    /** @var UserRepository */
    protected $userRepository;

    /**
     * @return array
     */
    public function getStatisticalData()
    {
        $endDate   = new \DateTime();
        $startDate = new \DateTime('-30 days');

        $orders = $this->orderRepository->findBetweenDates($startDate, $endDate);
        $users = $this->getUsers($startDate);

        $revenue = function ($sum, $value) {
            /** @var $value Order */
            return $sum + $value->getTotal();
        };

        $stats                         = [];
        $stats['activeUsers']          = count($users);
        $stats['orders']               = count($orders);
        $stats['revenue']              = array_reduce($orders, $revenue, 0);
        $stats['averageOrderValue']    = round($stats['revenue'] / $stats['orders'], 2);
        $stats['ordersPerActiveUser']  = round($stats['orders'] / $stats['activeUsers'], 2);
        $stats['revenuePerActiveUser'] = round($stats['revenue'] / $stats['activeUsers'], 2);

        return $stats;
    }

    /**
     * @param OrderRepository $orderRepository
     */
    public function setOrderRepository($orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param UserRepository $userRepository
     * @codeCoverageIgnore
     */
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param $startDate
     * @return array
     * @codeCoverageIgnore
     */
    protected function getUsers($startDate)
    {
        $users = $this->userRepository
            ->createQueryBuilder('u')
            ->where('u.lastLogin >= :lastLogin')
            ->setParameter('lastLogin', $startDate)
            ->getQuery()
            ->getResult();
        return $users;
    }
}
