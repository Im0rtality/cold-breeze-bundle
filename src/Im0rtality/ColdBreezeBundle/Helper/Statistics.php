<?php

namespace Im0rtality\ColdBreezeBundle\Helper;

class Statistics
{

    /**
     * @return array
     */
    public function getStatisticalData()
    {
        return [
            'title'                => 'month',
            'averageOrderValue'    => 1,
            'revenue'              => 1,
            'ordersPerActiveUser'  => 1,
            'revenuePerActiveUser' => 1,
            'orders'               => 1,
            'activeUsers'          => 1,
        ];
    }
}
