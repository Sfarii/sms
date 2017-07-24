<?php

namespace API\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
    * @var Symfony\Bundle\FrameworkBundle\Routing\Router
    */
    private $router;

    /**
    * @param Symfony\Bundle\FrameworkBundle\Routing\Router $router
    */
    public function __construct(Router $router)
    {
        $this->router = $router;

    }

    /**
    * @param Symfony\Component\HttpFoundation\Request $request
    * @return Symfony\Component\HttpFoundation\RedirectResponse
    */
    public function onLogoutSuccess(Request $request)
    {
        $request->getSession()->invalidate();
        $uri = $this->router->generate('login');

        $request->headers->addCacheControlDirective('no-cache', true);
        $request->headers->addCacheControlDirective('max-age', 0);
        $request->headers->addCacheControlDirective('must-revalidate', true);
        $request->headers->addCacheControlDirective('no-store', true);
        return new RedirectResponse($uri);
    }
}
