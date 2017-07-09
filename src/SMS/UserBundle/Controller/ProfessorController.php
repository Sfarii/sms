<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\Professor;
use SMS\UserBundle\Form\ProfessorType;
use SMS\UserBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Professor controller.
 *
 * @Route("professor")
 * @Security("has_role('ROLE_ADMIN')")
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
     * @Template("SMSUserBundle:professor:index.html.twig")
     */
    public function indexAction()
    {
        $professor = $this->getProfessorEntityManager();
        $professor->buildDatatable();

        return array('professors' => $professor);
    }

    /**
     * Lists all professor entities.
     *
     * @Route("/results", name="professor_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $professor = $this->getProfessorEntityManager();
        $professor->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($professor);

        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('professor.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
                ->andWhere('professor.id != :userId')
                ->setParameter('userId', $user->getId())
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new professor entity.
     *
     * @Route("/new", name="professor_new")
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:professor:new.html.twig")
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

        return  array(
            'professor' => $professor,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a professor entity.
     *
     * @Route("/{id}", name="professor_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSUserBundle:professor:show.html.twig")
     */
    public function showAction(Professor $professor)
    {
        $deleteForm = $this->createDeleteForm($professor);

        return array(
            'professor' => $professor,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing professor entity.
     *
     * @Route("/{id}/edit", name="professor_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:professor:edit.html.twig")
     */
    public function editAction(Request $request, Professor $professor)
    {

        $editForm = $this->createForm(ProfessorType::class, $professor)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getUserEntityManager()->editUser($professor);
            $this->flashSuccessMsg('professor.edit.success');
            if ($professor->getId() !== $this->getUser()->getId()){
              return $this->redirectToRoute('professor_index');
            }
        }

        return array(
            'user' => $professor,
            'form' => $editForm->createView(),
        );
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

    /**
     * Get professor Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getProfessorEntityManager()
    {
        if (!$this->has('sms.datatable.professor')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.professor');
    }
}
