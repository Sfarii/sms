<?php

namespace SMS\EstablishmentBundle\Controller;

use SMS\EstablishmentBundle\Entity\Division;
use SMS\Classes\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use SMS\EstablishmentBundle\Form\DivisionType;

/**
 * Division controller.
 *
 * @Route("division")
 */
class DivisionController extends BaseController
{
    /**
     * Lists all division entities.
     *
     * @Route("/", name="division_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $pagination = $this->getEntityManager()->getEntityBy(Division::class , $request);

        return $this->render('division/index.html.twig', array(
            'divisions' => $pagination,
            'search' => $request->query->get($this->getParameter('search_field'), null)
        ));
    }

    /**
     * Creates a new division entity.
     *
     * @Route("/new", name="division_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $division = new Division();
        $form = $this->createForm(DivisionType::class, $division)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->insert($division);

            return $this->redirectToRoute('division_index');
        }

        return $this->render('division/new.html.twig', array(
            'division' => $division,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a division entity.
     *
     * @Route("/show/{id}", name="division_show")
     * @Method("GET")
     */
    public function showAction(Division $division)
    {
        $deleteForm = $this->createDeleteForm($division);

        return $this->render('division/show.html.twig', array(
            'division' => $division,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a division entity.
     *
     * @Route("/actions", name="division_actions")
     * @Method("Post")
     */
    public function multipleActionsAction(Request $request)
    {
        $actions = $request->request->get($this->getParameter('index_actions') , null);
        $keys = $request->request->get($this->getParameter('index_keys') , null);
        
        if(!is_null($keys) && $actions === $this->getParameter('index_delete')){
            $this->getEntityManager()->deleteAll(Division::class ,$keys);
            $this->flashSuccessMsg('delete actions');
        }else{
            $this->flashErrorMsg('no entity selected');
        }

        return $this->redirectToRoute('division_index');
    }

    /**
     * Displays a form to edit an existing division entity.
     *
     * @Route("/{id}/edit", name="division_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Division $division)
    {
        $deleteForm = $this->createDeleteForm($division);
        $editForm = $this->createForm(DivisionType::class, $division);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEntityManager()->update($division);
            return $this->redirectToRoute('division_edit', array('id' => $division->getId()));
        }

        return $this->render('division/edit.html.twig', array(
            'division' => $division,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a division entity.
     *
     * @Route("/delete", name="division_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($division);
            $em->flush();
        }

        return $this->redirectToRoute('division_index');
    }

    /**
     * Creates a form to delete a division entity.
     *
     * @param Division $division The division entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Division $division)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('division_delete', array('id' => $division->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
