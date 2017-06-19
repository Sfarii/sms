<?php

namespace SMS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SMS\UserBundle\Entity\UserInterface;
use API\BaseController\BaseController;
use SMS\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SMS
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
     * @Route("/setting" , name="user_setting")
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
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="user_bulk_delete")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkDeleteAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            try {
                $this->getEntityManager()->deleteAll(User::class, $choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('user.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('user.delete.success'), 200);
        }

        return new Response('Bad Request', 500);
    }

    /**
     * Bulk delete action.
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

            try {
                $this->getEntityManager()->ActivateAll(User::class, $choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('user.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('user.delete.success'), 200);
        }

        return new Response('Bad Request', 500);
    }
}
