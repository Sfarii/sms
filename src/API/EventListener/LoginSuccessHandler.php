<?php
namespace API\EventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 */
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
    * @var Symfony\Bundle\FrameworkBundle\Routing\Router
    */
    private $router;

    /**
    * @var Symfony\Component\Security\Core\SecurityContext
    */
    private $security;

    /**
    * @param Symfony\Bundle\FrameworkBundle\Routing\Router $router
    * @param Symfony\Component\Security\Core\SecurityContext $security
    */
    public function __construct(Router $router, SecurityContext $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
    * @param Symfony\Bundle\FrameworkBundle\Routing\Router $router
    * @param Symfony\Component\Security\Core\SecurityContext $security
    *
    * @return Symfony\Component\HttpFoundation\RedirectResponse
    */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // Default target for unknown roles. Everyone else go there.
        $url = 'user_profile';
        
        $response = new RedirectResponse($this->router->generate($url));

        return $response;
    }
}
