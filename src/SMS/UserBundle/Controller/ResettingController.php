<?php

namespace SMS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use API\BaseController\BaseController;
use SMS\UserBundle\Entity\User;
use SMS\UserBundle\Form\ResettingPasswordFormType;
use SMS\UserBundle\Form\ResettingFormType;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 */
class ResettingController extends BaseController
{

    /**
     * @Route("/resetting" , name="resetting")
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:user/resetting:resetting.html.twig")
     */
    public function resettingAction(Request $request)
    {
    	// registration form
	    $form = $this->createForm(ResettingFormType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        	$email = $form->get('email')->getData();

        	if (!$this->getUserEntityManager()->validEmail($email)){
        		$form->get('email')->addError(new FormError($this->get('translator')->trans('reset.email.not_found')));
        	}else{
        		$this->getUserEntityManager()->resettingPassword($email,$this->getParameter("tokenLifetime"));
                return $this->render(
                    'SMSUserBundle:user/resetting:index.html.twig',
                    array(
                        "tokenLifetime" => ($this->getParameter("tokenLifetime")/3600)
                    )
                );
        	}
        }

        return array("form" => $form->createView());
    }

    /**
     * @Route("/resetting:{token}" , name="resetting_password")
     * @ParamConverter("user", class="SMSUserBundle:User", options={
     *    "repository_method" = "findUserByToken",
     *    "map_method_signature" = true
     * })
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:user/resetting:resetting.html.twig")
     *
     */
    public function resettingPasswordAction(User $user, Request $request)
    {
        // registration form
        $form = $this->createForm(ResettingPasswordFormType::class , $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUserEntityManager()->resettingNewPassword($user , $this->getParameter("tokenLifetime"));
            return $this->redirectToRoute('login');
        }

        return array("form" => $form->createView());
    }
}
