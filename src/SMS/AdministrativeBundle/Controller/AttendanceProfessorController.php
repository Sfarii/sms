<?php

namespace SMS\AdministrativeBundle\Controller;

use SMS\AdministrativeBundle\Entity\AttendanceProfessor;
use SMS\AdministrativeBundle\Form\ProfessorAttendanceType;
use SMS\AdministrativeBundle\Form\ScheduleStudentFilterType;
use SMS\AdministrativeBundle\Form\ScheduleProfessorFilterType;
use SMS\AdministrativeBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
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
     * Lists all schedule by Professor entities.
     *
     * @Route("/schedule_professor", name="attendanceprofessor_index")
     * @Method({"GET", "POST"})
     * @Template("SMSAdministrativeBundle:attendanceprofessor:professor.html.twig")
     */
    public function scheduleProfessorAction(Request $request)
    {
        $form = $this->createForm(ScheduleProfessorFilterType::class,null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $result = $this->getEntityManager()->getScheduleByProfessor($form->get('professor')->getData(),$form->get('division')->getData(), $this->getUser()->getEstablishment());
            $result['form'] = $form->createView();
            $result['division'] = $form->get('division')->getData();
            $result['professor'] = $form->get('professor')->getData();
            $result['status'] = $this->getParameter("attendance");
            return $result;
        }

        return array('form' => $form->createView());
    }

    /**
     * Lists all attendanceProfessor entities.
     *
     * @Route("/", name="attendance_professor_new")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:attendanceprofessor:index.html.twig")
     */
    public function indexAction()
    {
        $attendanceProfessors = $this->getAttendanceProfessorEntityManager();
        $attendanceProfessors->buildDatatable();

        return array('attendanceProfessors' => $attendanceProfessors);
    }

    /**
     * Lists all attendance Professor entities.
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
     * @Route("/new", name="attendance_prof_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSAdministrativeBundle:attendanceprofessor:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $attendanceProfessor = new Attendanceprofessor();
        $form = $this->createForm(ProfessorAttendanceType::class, $attendanceProfessor, array('establishment' => $this->getUser()->getEstablishment()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {

            $this->flashSuccessMsg('attendanceProfessor.add.success');
        }

        return array(
            'attendanceProfessor' => $attendanceProfessor,
            'form' => $form->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/add", name="attendance_professor_add")
     * @Method("POST")
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
          $data = array("professor" => $request->request->get('attendance_professor', NULL),
                        "session" => $request->request->get('attendance_session', NULL),
                        "date"    => $request->request->get('attendance_date', NULL),
                        "division"=> $request->request->get('attendance_division', NULL),
                        "status"  => $request->request->get('attendance_status', NULL));
          if ($this->getEntityManager()->addProfessorAttendance($data, $this->getUser())){
            $this->flashSuccessMsg('attendanceprofessor.add.success');
          }else{
            $this->flashErrorMsg('attendanceprofessor.check.data');
          }
          return $this->redirectToRoute('attendance_professor_new');
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
      if (!$this->has('sms.datatable.attendance_professor')){
         throw $this->createNotFoundException('Service Not Found');
      }

      return $this->get('sms.datatable.attendance_professor');
    }}
