<?php

namespace SMS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use API\BaseController\BaseController;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 */
class SecurityController extends BaseController
{

    /**
     * @Route("/login", name="login")
     * @Method({"GET", "POST"})
     * @Template("smsuserbundle/user/security/login.html.twig")
     */
    public function loginAction(Request $request)
	{
	    $authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    return array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
	    );
	}

	/**
     * @Route("/logout", name="user_logout")
     */
	public function logoutAction(Request $request)
	{
		throw new \LogicException('Unreachable code.');
	}
}
