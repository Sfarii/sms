<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\StudentParent;
use SMS\UserBundle\Form\StudentParentType;
use SMS\Classes\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Studentparent controller.
 *
 * @Route("studentparent")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Controller
 *
 */
class StudentParentController extends BaseController
{
    /**
     * Lists all studentParent entities.
     *
     * @Route("/", name="studentparent_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $studentParents = $this->getEntityManager()->getEntityBy(StudentParent::class , $request);

        return $this->render('smsuserbundle/studentparent/index.html.twig', array(
            'studentParents' => $studentParents,
            'search' => $request->query->get($this->getParameter('search_field'), null)
        ));
    }

    /**
     * Creates a new studentParent entity.
     *
     * @Route("/new", name="studentparent_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $studentParent = new Studentparent();
        $form = $this->createForm(StudentParentType::class, $studentParent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getUserEntityManager()->addUser($studentParent);
            $this->flashSuccessMsg('studentParent.add.success');
            return $this->redirectToRoute('studentparent_index');
        }

        return $this->render('smsuserbundle/studentparent/new.html.twig', array(
            'studentParent' => $studentParent,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a studentParent entity.
     *
     * @Route("/{id}", name="studentparent_show")
     * @Method("GET")
     */
    public function showAction(StudentParent $studentParent)
    {
        $deleteForm = $this->createDeleteForm($studentParent);

        return $this->render('smsuserbundle/studentparent/show.html.twig', array(
            'studentParent' => $studentParent,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing studentParent entity.
     *
     * @Route("/{id}/edit", name="studentparent_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, StudentParent $studentParent)
    {

        $editForm = $this->createForm(StudentParentType::class, $studentParent)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($studentParent);
            $this->flashSuccessMsg('studentParent.edit.success');
            return $this->redirectToRoute('studentparent_index');
        }

        return $this->render('smsuserbundle/studentparent/edit.html.twig', array(
            'studentParent' => $studentParent,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Finds and displays a studentParent entity.
     *
     * @Route("/multiple_actions", name="studentparent_bulk_action")
     * @Method("POST")
     */
    public function multipleActionsAction(Request $request)
    {
        $actions = $request->request->get($this->getParameter('index_actions') , null);
        $keys = $request->request->get($this->getParameter('index_keys') , null);
            
        if(!is_null($keys) && $actions === $this->getParameter('index_delete')){
            $this->getEntityManager()->deleteAll(studentParent::class ,$keys);
            $this->flashSuccessMsg('studentParent.delete.all.success');
        }else{
            $this->flashErrorMsg('studentParent.delete.all.error');
        }

        return $this->redirectToRoute('studentparent_index');
    }

    /**
     * Deletes a studentParent entity.
     *
     * @Route("/{id}", name="studentparent_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, StudentParent $studentParent)
    {
        $form = $this->createDeleteForm($studentParent)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($studentParent);
            $this->flashSuccessMsg('studentParent.delete.one.success');
        }

        return $this->redirectToRoute('studentparent_index');
    }

    /**
     * Creates a form to delete a studentParent entity.
     *
     * @param StudentParent $studentParent The studentParent entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StudentParent $studentParent)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('studentparent_delete', array('id' => $studentParent->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
