<?php

namespace SMS\AdministrativeBundle\Controller;

use SMS\AdministrativeBundle\Entity\AttendanceProfessor;
use SMS\AdministrativeBundle\Form\ProfessorAttendanceType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attendanceprofessor controller.
 *
 * @Route("attendanceprofessor")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\AdministrativeBundle\Controller
 *
 */
class AttendanceProfessorController extends BaseController
{
    /**
     * Lists all attendanceProfessor entities.
     *
     * @Route("/", name="attendanceprofessor_index")
     * @Method("GET")
     * @Template("smsadministrativebundle/attendanceprofessor/index.html.twig")
     */
    public function indexAction()
    {
        $attendanceProfessors = $this->getAttendanceProfessorEntityManager();
        $attendanceProfessors->buildDatatable();

        return array('attendanceProfessors' => $attendanceProfessors);
    } /**
     * Lists all attendanceProfessor entities.
     *
     * @Route("/results", name="attendanceprofessor_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $attendanceProfessors = $this->getAttendanceProfessorEntityManager();
        $attendanceProfessors->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($attendanceProfessors);

        return $query->getResponse();
    }
    /**
     * Creates a new attendanceProfessor entity.
     *
     * @Route("/new", name="attendance_professor_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("smsadministrativebundle/attendanceprofessor/new.html.twig")
     */
    public function newAction(Request $request)
    {
        $attendanceProfessor = new Attendanceprofessor();
        $form = $this->createForm(ProfessorAttendanceType::class, $attendanceProfessor);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->addProfessorAttendance($attendanceProfessor , $this->getUser());
            $this->flashSuccessMsg('attendanceProfessor.add.success');
        }

        return array(
            'attendanceProfessor' => $attendanceProfessor,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a attendanceProfessor entity.
     *
     * @Route("/{id}", name="attendanceprofessor_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(AttendanceProfessor $attendanceProfessor)
    {
        $deleteForm = $this->createDeleteForm($attendanceProfessor);

        return $this->render('smsadministrativebundle/attendanceprofessor/show.html.twig', array(
            'attendanceProfessor' => $attendanceProfessor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing attendanceProfessor entity.
     *
     * @Route("/{id}/edit", name="attendanceprofessor_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("smsadministrativebundle/attendanceprofessor/edit.html.twig")
     */
    public function editAction(Request $request, AttendanceProfessor $attendanceProfessor)
    {
        $editForm = $this->createForm(AttendanceProfessorType::class, $attendanceProfessor)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($attendanceProfessor);
            $this->flashSuccessMsg('attendanceProfessor.edit.success');
            return $this->redirectToRoute('attendanceprofessor_index');
        }

        return array(
            'attendanceProfessor' => $attendanceProfessor,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="attendanceprofessor_bulk_delete")
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
                $this->getEntityManager()->deleteAll(attendanceProfessor::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('attendanceProfessor.delete.fail'), 200);
            }
            

            return new Response($this->get('translator')->trans('attendanceProfessor.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a attendanceProfessor entity.
     *
     * @Route("/{id}", name="attendanceprofessor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AttendanceProfessor $attendanceProfessor)
    {
        $form = $this->createDeleteForm($attendanceProfessor)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($attendanceProfessor);
            $this->flashSuccessMsg('attendanceProfessor.delete.one.success');
        }

        return $this->redirectToRoute('attendanceprofessor_index');
    }

    /**
     * Creates a form to delete a attendanceProfessor entity.
     *
     * @param AttendanceProfessor $attendanceProfessor The attendanceProfessor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AttendanceProfessor $attendanceProfessor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('attendanceprofessor_delete', array('id' => $attendanceProfessor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get attendanceProfessor Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getAttendanceProfessorEntityManager()
    {
        if (!$this->has('sms.datatable.attendanceProfessor')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.attendanceProfessor');
    }}
