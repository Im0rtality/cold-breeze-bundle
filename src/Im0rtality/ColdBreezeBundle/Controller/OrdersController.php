<?php

namespace Im0rtality\ColdBreezeBundle\Controller;

use Underscore\Underscore;
use ReflectionObject;
use Sylius\Component\Core\Model\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrdersController extends Controller
{
    public function getAction($id)
    {
        $repo = $this->get('sylius.repository.order');
        /** @var $order Order */
        $order = $repo->find($id);
        if ($order) {
            $reflector = new ReflectionObject($order);
            $nodes     = $reflector->getProperties();

            $serialized = Underscore::from($nodes)
                ->map(
                    function ($node) use ($order) {
                        $getter = sprintf('get%s', ucfirst($node['name']));
                        return $order->{$getter}();
                    }
                )
                ->zip(Underscore::from($nodes)->pick('name')->toArray())
                ->value();

            return new JsonResponse($serialized);
        } else {
            throw new NotFoundHttpException();
        }
    }
} 
