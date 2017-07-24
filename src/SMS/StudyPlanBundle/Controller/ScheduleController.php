<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Schedule;
use SMS\StudyPlanBundle\Entity\Session;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\EstablishmentBundle\Entity\Division;
use SMS\StudyPlanBundle\Form\ScheduleType;
use SMS\StudyPlanBundle\Form\ScheduleStudentFilterType;
use SMS\StudyPlanBundle\Form\ScheduleProfessorFilterType;
use SMS\StudyPlanBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Schedule controller.
 *
 * @Route("schedule")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class ScheduleController extends BaseController
{
  /**
   * Lists all schedule by Student entities.
   *
   * @Route("/schedule_student", name="schedule_student_index")
   * @Method({"GET", "POST"})
   * @Template("SMSStudyPlanBundle:schedule:student.html.twig")
   */
  public function scheduleStudentAction(Request $request)
  {
      $form = $this->createForm(ScheduleStudentFilterType::class, null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
          $result = $this->getEntityManager()->getSchedule($form->get('section')->getData(),$form->get('division')->getData(), $this->getUser()->getEstablishment());
          $formSchedule = $this->createForm(ScheduleType::class, new Schedule(), array('section' => $form->get('section')->getData() , 'division' => $form->get('division')->getData() , 'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
          return array('form' => $form->createView() , 'result' => $result , 'formSchedule' => $formSchedule->createView());
      }
      return array('form' => $form->createView());
  }

    /**
     * Lists all schedule by Professor entities.
     *
     * @Route("/schedule_professor", name="schedule_professor_index")
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:schedule:professor.html.twig")
     */
    public function scheduleProfessorAction(Request $request)
    {
        $form = $this->createForm(ScheduleProfessorFilterType::class,null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $result = $this->getEntityManager()->getScheduleByProfessor($form->get('professor')->getData(),$form->get('division')->getData(), $this->getUser()->getEstablishment());
            return array('form' => $form->createView() , 'result' => $result);
        }

        return array('form' => $form->createView());
    }

    /**
     * Creates a new schedule entity.
     *
     * @Route("/new/{id_section}/{id_division}", name="schedule_new")
     * @ParamConverter("division", class="SMSEstablishmentBundle:Division", options={"id" = "id_division"})
     * @ParamConverter("section", class="SMSEstablishmentBundle:Section", options={"id" = "id_section"})
     * @Method("POST")
     */
    public function newAction(Request $request,Division $division,Section $section)
    {
        $schedule = new Schedule();
        $form = $this->createForm(ScheduleType::class, $schedule, array('section' => $section , 'division' => $division , 'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->insert($schedule , $this->getUser());
            return new Response(json_encode(array('success' => $this->get('translator')->trans('schedule.add.success'))), 200);
        }
        return new Response(json_encode(array('error' => $this->getErrorMessages($form))), 200);
    }

    /**
     * Displays a form to edit an existing schedule entity.
     *
     * @Route("/{id_schedule}/{id_division}/edit", name="schedule_edit", options={"expose"=true})
     * @ParamConverter("division", class="SMSEstablishmentBundle:Division", options={"id" = "id_division"})
     * @ParamConverter("schedule", class="SMSStudyPlanBundle:Schedule", options={"id" = "id_schedule"})
     * @Method("POST")
     */
    public function editAction(Request $request, Schedule $schedule,Division $division)
    {
        $editForm = $this->createForm(ScheduleType::class, $schedule, array('division' => $division,'section' => $schedule->getSection() ,'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEntityManager()->update($schedule);
            return new Response(json_encode(array('success' => $this->get('translator')->trans('schedule.edit.success'))), 200);
        }
        return new Response(json_encode(array('error' => $this->getErrorMessages($editForm))), 200);
    }

    /**
     * Deletes a schedule entity.
     *
     * @Route("/delete/{id}", name="schedule_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Schedule $schedule)
    {
        $token = $request->query->get('token');
        if (!$this->isCsrfTokenValid('schedule_delete', $token)) {
            return new Response($this->get('translator')->trans('schedule.delete.fail'), 200);
        }else{
          $this->getEntityManager()->delete($schedule);
          return new Response($this->get('translator')->trans('schedule.delete.success'), 200);
        }
        return new Response('Bad Request', 400);
    }

    /**
     * Creates a form to delete a schedule entity.
     *
     * @param Schedule $schedule The schedule entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Schedule $schedule)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('schedule_delete', array('id' => $schedule->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get Service.
     *
     * @throws \NotFoundException
     */
    protected function getScheduleEntityManager()
    {
        if (!$this->has('sms.datatable.schedule')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.schedule');
    }}
