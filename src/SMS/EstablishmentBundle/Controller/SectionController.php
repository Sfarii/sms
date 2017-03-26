<?php

namespace SMS\EstablishmentBundle\Controller;

use SMS\EstablishmentBundle\Entity\Section;
use SMS\EstablishmentBundle\Form\SectionType;
use SMS\Classes\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Section controller.
 *
 * @Route("section")
 */
class SectionController extends BaseController
{
    /**
     * Lists all section entities.
     *
     * @Route("/", name="section_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $sections = $this->getEntityManager()->getEntityBy(Section::class , $request);

        return $this->render('section/index.html.twig', array(
            'sections' => $sections,
            'search' => $request->query->get($this->getParameter('search_field'), null)
        ));
    }

    /**
     * Creates a new section entity.
     *
     * @Route("/new", name="section_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->insert($section);
            $this->flashSuccessMsg('section.add.success');
            return $this->redirectToRoute('section_index');
        }

        return $this->render('section/new.html.twig', array(
            'section' => $section,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a section entity.
     *
     * @Route("/{id}", name="section_show")
     * @Method("GET")
     */
    public function showAction(Section $section)
    {
        $deleteForm = $this->createDeleteForm($section);

        return $this->render('section/show.html.twig', array(
            'section' => $section,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing section entity.
     *
     * @Route("/{id}/edit", name="section_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Section $section)
    {

        $editForm = $this->createForm(SectionType::class, $section)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEntityManager()->update($section);
            $this->flashSuccessMsg('section.edit.success');
            return $this->redirectToRoute('section_index');
        }

        return $this->render('section/edit.html.twig', array(
            'section' => $section,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Finds and displays a section entity.
     *
     * @Route("/multiple_actions", name="section_actions")
     * @Method("POST")
     */
    public function multipleActionsAction(Request $request)
    {
        $actions = $request->request->get($this->getParameter('index_actions') , null);
        $keys = $request->request->get($this->getParameter('index_keys') , null);
            
        if(!is_null($keys) && $actions === $this->getParameter('index_delete')){
            $this->getEntityManager()->deleteAll(section::class ,$keys);
            $this->flashSuccessMsg('section.delete.all.success');
        }else{
            $this->flashErrorMsg('section.delete.all.error');
        }

        return $this->redirectToRoute('division_index');
    }

    /**
     * Deletes a section entity.
     *
     * @Route("/{id}", name="section_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Section $section)
    {
        $form = $this->createDeleteForm($section)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($section);
            $this->flashSuccessMsg('section.delete.one.success');
        }

        return $this->redirectToRoute('section_index');
    }

    /**
     * Creates a form to delete a section entity.
     *
     * @param Section $section The section entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Section $section)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('section_delete', array('id' => $section->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
