<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\Manager;
use SMS\UserBundle\Form\ManagerType;
use SMS\UserBundle\Form\EditManagerType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manager controller.
 *
 * @Route("manager")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Controller
 *
 */
class ManagerController extends BaseController
{
    /**
     * Lists all manager entities.
     *
     * @Route("/", name="manager_index")
     * @Method("GET")
     * @Template("SMSUserBundle:manager:index.html.twig")
     */
    public function indexAction()
    {
        $managers = $this->getManagerEntityManager();
        $managers->buildDatatable();

        return array('managers' => $managers);
    } /**
     * Lists all manager entities.
     *
     * @Route("/results", name="manager_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $managers = $this->getManagerEntityManager();
        $managers->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($managers);

        return $query->getResponse();
    }
    /**
     * Creates a new manager entity.
     *
     * @Route("/new", name="manager_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:manager:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $manager = new Manager();
        $form = $this->createForm(ManagerType::class, $manager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getUserEntityManager()->addUser($manager );
            $this->flashSuccessMsg('manager.add.success');
            return $this->redirectToRoute('manager_index');
        }

        return array(
            'manager' => $manager,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a manager entity.
     *
     * @Route("/{id}", name="manager_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Manager $manager)
    {
        $deleteForm = $this->createDeleteForm($manager);

        return $this->render('SMSUserBundle:manager:show.html.twig', array(
            'manager' => $manager,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing manager entity.
     *
     * @Route("/{id}/edit", name="manager_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:manager:edit.html.twig")
     */
    public function editAction(Request $request, Manager $manager)
    {
        $editForm = $this->createForm(EditManagerType::class, $manager)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($manager);
            $this->flashSuccessMsg('manager.edit.success');
            if ($manager->getId() !== $this->getUser()->getId()){
              return $this->redirectToRoute('manager_index');
            }
        }

        return array(
            'manager' => $manager,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="manager_bulk_delete")
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
                $this->getEntityManager()->deleteAll(manager::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('manager.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('manager.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a manager entity.
     *
     * @Route("/{id}", name="manager_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Manager $manager)
    {
        $form = $this->createDeleteForm($manager)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($manager);
            $this->flashSuccessMsg('manager.delete.one.success');
        }

        return $this->redirectToRoute('manager_index');
    }

    /**
     * Creates a form to delete a manager entity.
     *
     * @param Manager $manager The manager entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Manager $manager)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manager_delete', array('id' => $manager->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get manager Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getManagerEntityManager()
    {
        if (!$this->has('sms.datatable.manager')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.manager');
    }}
