<?php

namespace Im0rtality\ColdBreezeBundle;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    /** @var  bool */
    protected $debug;

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->getRequest()->headers->contains('Accept', 'application/json')) {
            return;
        }
        // You get the exception object from the received event
        $exception = FlattenException::create($event->getException());
        $response  = [
            'code'             => $exception->getStatusCode(),
            'message'          => $exception->getMessage(),
            'developerMessage' => $exception->getClass(),
        ];
        if ($this->debug) {
            $debug = [
                'trace' => array_map(
                    function ($entry) {
                        return sprintf('%s:%d', $entry['file'], $entry['line']);
                    },
                    $exception->getTrace()
                )
            ];
        } else {
            $debug = [];
        }

        $event->setResponse(new JsonResponse($response + $debug, $exception->getStatusCode()));
    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
} 
