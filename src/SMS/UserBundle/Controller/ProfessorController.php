<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\Professor;
use SMS\UserBundle\Form\ProfessorType;
use SMS\Classes\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Professor controller.
 *
 * @Route("professor")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Controller
 *
 */
class ProfessorController extends BaseController
{
    /**
     * Lists all professor entities.
     *
     * @Route("/", name="professor_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $professors = $this->getEntityManager()->getEntityBy(Professor::class , $request);

        return $this->render('smsuserbundle/professor/index.html.twig', array(
            'professors' => $professors,
            'search' => $request->query->get($this->getParameter('search_field'), null)
        ));
    }

    /**
     * Creates a new professor entity.
     *
     * @Route("/new", name="professor_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $professor = new Professor();
        $form = $this->createForm(ProfessorType::class, $professor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getUserEntityManager()->addUser($professor);
            $this->flashSuccessMsg('professor.add.success');
            return $this->redirectToRoute('professor_index');
        }

        return $this->render('smsuserbundle/professor/new.html.twig', array(
            'professor' => $professor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a professor entity.
     *
     * @Route("/{id}", name="professor_show")
     * @Method("GET")
     */
    public function showAction(Professor $professor)
    {
        $deleteForm = $this->createDeleteForm($professor);

        return $this->render('smsuserbundle/professor/show.html.twig', array(
            'professor' => $professor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing professor entity.
     *
     * @Route("/{id}/edit", name="professor_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Professor $professor)
    {

        $editForm = $this->createForm(ProfessorType::class, $professor)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($professor);
            $this->flashSuccessMsg('professor.edit.success');
            return $this->redirectToRoute('professor_index');
        }

        return $this->render('smsuserbundle/professor/edit.html.twig', array(
            'professor' => $professor,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Finds and displays a professor entity.
     *
     * @Route("/multiple_actions", name="professor_bulk_action")
     * @Method("POST")
     */
    public function multipleActionsAction(Request $request)
    {
        $actions = $request->request->get($this->getParameter('index_actions') , null);
        $keys = $request->request->get($this->getParameter('index_keys') , null);
            
        if(!is_null($keys) && $actions === $this->getParameter('index_delete')){
            $this->getEntityManager()->deleteAll(professor::class ,$keys);
            $this->flashSuccessMsg('professor.delete.all.success');
        }else{
            $this->flashErrorMsg('professor.delete.all.error');
        }

        return $this->redirectToRoute('professor_index');
    }

    /**
     * Deletes a professor entity.
     *
     * @Route("/{id}", name="professor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Professor $professor)
    {
        $form = $this->createDeleteForm($professor)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($professor);
            $this->flashSuccessMsg('professor.delete.one.success');
        }

        return $this->redirectToRoute('professor_index');
    }

    /**
     * Creates a form to delete a professor entity.
     *
     * @param Professor $professor The professor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Professor $professor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('professor_delete', array('id' => $professor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
