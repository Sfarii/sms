<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Schedule;
use SMS\StudyPlanBundle\Entity\Session;
use SMS\StudyPlanBundle\Form\ScheduleType;
use SMS\StudyPlanBundle\Form\ScheduleStudentFilterType;
use SMS\StudyPlanBundle\Form\ScheduleProfessorFilterType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Schedule controller.
 *
 * @Route("schedule")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class ScheduleController extends BaseController
{
    /**
     * Lists all schedule entities.
     *
     * @Route("/", name="schedule_index")
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:schedule:index.html.twig")
     */
    public function indexAction()
    {
        $schedules = $this->getScheduleEntityManager();
        $schedules->buildDatatable();

        return array('schedules' => $schedules);
    }

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
            $userSpace = $this->getUserSapaceManager();
            $division = $form->get('division')->getData();
            $section = $form->get('section')->getData();
            $result = $userSpace->getSchedule($section,$division);
            $result['form'] = $form->createView();
            return $result;
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
            $userSpace = $this->getUserSapaceManager();
            $division = $form->get('division')->getData();
            $professor = $form->get('professor')->getData();
            $result = $userSpace->getScheduleByProfessor($professor,$division);
            $result['form'] = $form->createView();
            return $result;
        }

        return array('form' => $form->createView());
    }

    /**
     * Lists all schedule entities.
     *
     * @Route("/results", name="schedule_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $schedules = $this->getScheduleEntityManager();
        $schedules->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($schedules);

        return $query->getResponse();
    }
    /**
     * Creates a new schedule entity.
     *
     * @Route("/new", name="schedule_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:schedule:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $schedule = new Schedule();
        $form = $this->createForm(ScheduleType::class, $schedule, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($schedule , $this->getUser());
            $this->flashSuccessMsg('schedule.add.success');
            return $this->redirectToRoute('schedule_index');
        }

        return array(
            'schedule' => $schedule,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a schedule entity.
     *
     * @Route("/{id}", name="schedule_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Schedule $schedule)
    {
        $deleteForm = $this->createDeleteForm($schedule);

        return $this->render('SMSStudyPlanBundle:schedule:show.html.twig', array(
            'schedule' => $schedule,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing schedule entity.
     *
     * @Route("/{id}/edit", name="schedule_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:schedule:edit.html.twig")
     */
    public function editAction(Request $request, Schedule $schedule)
    {
        $editForm = $this->createForm(ScheduleType::class, $schedule, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($schedule);
            $this->flashSuccessMsg('schedule.edit.success');
            return $this->redirectToRoute('schedule_index');
        }

        return array(
            'schedule' => $schedule,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="schedule_bulk_delete")
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
                $this->getEntityManager()->deleteAll(schedule::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('schedule.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('schedule.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a schedule entity.
     *
     * @Route("/{id}", name="schedule_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Schedule $schedule)
    {
        $form = $this->createDeleteForm($schedule)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($schedule);
            $this->flashSuccessMsg('schedule.delete.one.success');
        }

        return $this->redirectToRoute('schedule_index');
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
     * Get schedule Entity Manager Service.
     *
     * @return API\Services\EntityManager
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
