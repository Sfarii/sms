<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\Administrator;
use SMS\UserBundle\Form\AdministratorType;
use SMS\Classes\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Administrator controller.
 *
 * @Route("administrator")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Controller
 *
 */
class AdministratorController extends BaseController
{
    /**
     * Lists all administrator entities.
     *
     * @Route("/", name="administrator_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $administrators = $this->getEntityManager()->getEntityBy(Administrator::class , $request);

        return $this->render('smsuserbundle/administrator/index.html.twig', array(
            'administrators' => $administrators,
            'search' => $request->query->get($this->getParameter('search_field'), null)
        ));
    }

    /**
     * Creates a new administrator entity.
     *
     * @Route("/new", name="administrator_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $administrator = new Administrator();
        $form = $this->createForm(AdministratorType::class, $administrator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getUserEntityManager()->addUser($administrator);
            $this->flashSuccessMsg('administrator.add.success');
            return $this->redirectToRoute('administrator_index');
        }

        return $this->render('smsuserbundle/administrator/new.html.twig', array(
            'administrator' => $administrator,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a administrator entity.
     *
     * @Route("/{id}", name="administrator_show")
     * @Method("GET")
     */
    public function showAction(Administrator $administrator)
    {
        $deleteForm = $this->createDeleteForm($administrator);

        return $this->render('smsuserbundle/administrator/show.html.twig', array(
            'administrator' => $administrator,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing administrator entity.
     *
     * @Route("/{id}/edit", name="administrator_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Administrator $administrator)
    {

        $editForm = $this->createForm(AdministratorType::class, $administrator)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($administrator);
            $this->flashSuccessMsg('administrator.edit.success');
            return $this->redirectToRoute('administrator_index');
        }

        return $this->render('smsuserbundle/administrator/edit.html.twig', array(
            'administrator' => $administrator,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Finds and displays a administrator entity.
     *
     * @Route("/multiple_actions", name="administrator_bulk_action")
     * @Method("POST")
     */
    public function multipleActionsAction(Request $request)
    {
        $actions = $request->request->get($this->getParameter('index_actions') , null);
        $keys = $request->request->get($this->getParameter('index_keys') , null);
            
        if(!is_null($keys) && $actions === $this->getParameter('index_delete')){
            $this->getEntityManager()->deleteAll(administrator::class ,$keys);
            $this->flashSuccessMsg('administrator.delete.all.success');
        }else{
            $this->flashErrorMsg('administrator.delete.all.error');
        }

        return $this->redirectToRoute('administrator_index');
    }

    /**
     * Deletes a administrator entity.
     *
     * @Route("/{id}", name="administrator_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Administrator $administrator)
    {
        $form = $this->createDeleteForm($administrator)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($administrator);
            $this->flashSuccessMsg('administrator.delete.one.success');
        }

        return $this->redirectToRoute('administrator_index');
    }

    /**
     * Creates a form to delete a administrator entity.
     *
     * @param Administrator $administrator The administrator entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Administrator $administrator)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('administrator_delete', array('id' => $administrator->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
