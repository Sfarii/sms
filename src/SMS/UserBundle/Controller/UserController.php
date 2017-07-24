<?php

namespace SMS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SMS\UserBundle\Entity\UserInterface;
use SMS\UserBundle\BaseController\BaseController;
use SMS\UserBundle\Entity\User;
use SMS\UserBundle\Form\UserResettingPasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SMS
 *
 * @Route("setting")
 * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MANAGER') or has_role('ROLE_STUDENT') or has_role('ROLE_PARENT') or has_role('ROLE_PROFESSOR')")
 */
class UserController extends BaseController
{
    /**
     * @Route("/profile" , name="user_profile")
     * @Method("GET")
     */
    public function profileAction()
    {
        if (!$this->getUser() instanceof User){
          throw new AccessDeniedException('This user does not have access to this section.');
        }
        $className = substr( strtolower(get_class($this->getUser())) , strrpos(get_class($this->getUser()), '\\') + 1);
        return $this->redirectToRoute(sprintf('%s_show' , $className), array('id' =>$this->getUser()->getId()));
    }

    /**
     * @Route("/user" , name="user_setting")
     * @Method("GET")
     * @Template("SMSUserBundle:user/profile/setting.html.twig")
     */
    public function settingAction()
    {
      if (!$this->getUser() instanceof User){
        throw new AccessDeniedException('This user does not have access to this section.');
      }
      $className = substr( strtolower(get_class($this->getUser())) , strrpos(get_class($this->getUser()), '\\') + 1);
      return $this->redirectToRoute(sprintf('%s_edit' , $className), array('id' =>$this->getUser()->getId()));
    }

    /**
     * @Route("/password/{id}" , name="change_password")
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:user/resetting:password.html.twig")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MANAGER') or user.getId() == currentUser.getId()")
     */
    public function resettingPasswordAction(User $currentUser, Request $request)
    {
        // registration form
        $form = $this->createForm(UserResettingPasswordFormType::class , $currentUser)->handleRequest($request);
        $className = substr( strtolower(get_class($this->getUser())) , strrpos(get_class($currentUser), '\\') + 1);
        $route = sprintf('%s_show' , $className);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUserEntityManager()->userResettingNewPassword($currentUser);
            $this->flashSuccessMsg('user.edit.password');
            $className = substr( strtolower(get_class($this->getUser())) , strrpos(get_class($currentUser), '\\') + 1);
            return $this->redirectToRoute($route, array('id' => $currentUser->getId()));
        }

        return array("form" => $form->createView() , 'user' => $currentUser , 'route' => $route);
    }

    /**
     * Bulk deactivate action.
     *
     * @param Request $request
     *
     * @Route("/bulk/deactivate", name="user_bulk_deactivate")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkDesactivateAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            $this->getUserEntityManager()->enabledAll($choices , false);

            return new Response($this->get('translator')->trans('user.deactivate.success'), 200);
        }

        return new Response('Bad Request', 500);
    }

    /**
     * Bulk activate action.
     *
     * @param Request $request
     *
     * @Route("/bulk/activate", name="user_bulk_activate")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkActivateAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            $this->getUserEntityManager()->enabledAll($choices , true);

            return new Response($this->get('translator')->trans('user.activate.success'), 200);
        }

        return new Response('Bad Request', 500);
    }
}
