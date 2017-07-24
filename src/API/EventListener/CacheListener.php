<?php

namespace API\EventListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 */
class CacheListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        // Check that the current request is a "MASTER_REQUEST"
        // Ignore any sub-request
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }
        $response = $event->getResponse();
        $response->headers->addCacheControlDirective('no-store', true);
    }
}
