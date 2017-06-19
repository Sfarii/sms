<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\StudentParent;
use SMS\UserBundle\Form\StudentParentType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
     * @Template("SMSUserBundle:studentparent:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $studentParent = $this->getStudentParentEntityManager();
        $studentParent->buildDatatable();

        return array('studentParents' => $studentParent);
    }

    /**
     * Lists all studentParent entities.
     *
     * @Route("/results", name="studentparent_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $studentParent = $this->getStudentParentEntityManager();
        $studentParent->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($studentParent);

        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('parent.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }


    /**
     * Creates a new studentParent entity.
     *
     * @Route("/new", name="studentparent_new")
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:studentparent:new.html.twig")
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

        return  array(
            'studentParent' => $studentParent,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a studentParent entity.
     *
     * @Route("/{id}", name="studentparent_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSUserBundle:studentparent:show.html.twig")
     */
    public function showAction(StudentParent $studentParent)
    {
        $deleteForm = $this->createDeleteForm($studentParent);

        return array(
            'studentParent' => $studentParent,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing studentParent entity.
     *
     * @Route("/{id}/edit", name="studentparent_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:studentparent:edit.html.twig")
     */
    public function editAction(Request $request, StudentParent $studentParent)
    {

        $editForm = $this->createForm(StudentParentType::class, $studentParent)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($studentParent);
            $this->flashSuccessMsg('studentParent.edit.success');
            if ($studentParent->getId() !== $this->getUser()->getId()){
              return $this->redirectToRoute('studentparent_index');
            }
        }

        return array(
            'user' => $studentParent,
            'form' => $editForm->createView(),
        );
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

    /**
     * Get studentParent Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getStudentParentEntityManager()
    {
        if (!$this->has('sms.datatable.student_parent')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.student_parent');
    }
}
