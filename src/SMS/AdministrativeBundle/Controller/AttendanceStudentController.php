<?php

namespace SMS\AdministrativeBundle\Controller;

use SMS\AdministrativeBundle\Entity\AttendanceStudent;
use SMS\AdministrativeBundle\Form\StudentAttendanceType;
use SMS\AdministrativeBundle\Form\ScheduleStudentFilterType;
use SMS\AdministrativeBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Attendance Student controller.
 *
 * @Route("attendance")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\AdministrativeBundle\Controller
 *
 */
class AttendanceStudentController extends BaseController
{
    /**
     * Lists all schedule by Student entities.
     *
     * @Route("/attendance_student", name="attendance_student_index")
     * @Method({"GET", "POST"})
     * @Template("SMSAdministrativeBundle:studentattendance:student.html.twig")
     */
    public function scheduleStudentAction(Request $request)
    {
        $form = $this->createForm(ScheduleStudentFilterType::class, null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->getEntityManager()->getSchedule($form->get('section')->getData(),$form->get('division')->getData() , $this->getUser()->getEstablishment());
            $result['form'] = $form->createView();
            $result['division'] = $form->get('division')->getData();
            $result['status'] = $this->getParameter("attendance");
            return $result;
        }

        return array('form' => $form->createView());
    }

    /**
     * Lists all attendanceProfessor entities.
     *
     * @Route("/", name="attendance_student_new")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:studentattendance:attendance.html.twig")
     */
    public function indexStudentAction()
    {
        $attendance = $this->getAttendanceStudentEntityManager();
        $attendance->buildDatatable();

        return array('attendance' => $attendance);
    }

    /**
     * Lists all attendance Professor entities.
     *
     * @Route("/results", name="attendancestudent_results")
     * @Method("GET")
     * @return Response
     */
    public function indexStudentResultsAction()
    {
        $attendance = $this->getAttendanceStudentEntityManager();
        $attendance->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($attendance);

        return $query->getResponse();
    }

    /**
     * Creates a new attendance entity.
     *
     * @Route("/new_attendance_student", name="attendance_student_add", options={"expose"=true})
     * @Method("POST")
     */
    public function newAttendanceStudentAction(Request $request)
    {
        $data = array("section" => $request->request->get('attendance_section', NULL),
                      "session" => $request->request->get('attendance_session', NULL),
                      "date"    => $request->request->get('attendance_date', NULL),
                      "division"=> $request->request->get('attendance_division', NULL),
                      "status"  => $request->request->get('attendance_status', NULL));
        if ($this->getEntityManager()->validateStudentAttendance($data , $this->getUser())){
          $ids = $this->getEntityManager()->addStudentAttendance($data, $this->getUser());
          // set the array of IDS in the session
          $session = $this->getRequest()->getSession();
          $session->set("attendance_student", $ids);
          $session->set("attendance_date", $data["date"]);
          $this->flashSuccessMsg('attendancestudent.add.success');
          return $this->redirectToRoute('attendance_index');
        }
        $this->flashErrorMsg('attendancestudent.check.data');
        return $this->redirectToRoute('attendance_student_index');
    }

    /**
     * Lists all attendance entities.
     *
     * @Route("/attendance_section", name="attendance_index")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:studentattendance:index.html.twig")
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        if (!$session->has("attendance_student") || !$session->has("attendance_date")){
            throw $this->createNotFoundException('Object Not Found');
        }

        $attendances = $this->getAttendanceEntityManager();
        $attendances->buildDatatable();

        return array('attendances' => $attendances , 'attendance_date' => new \DateTime($session->get("attendance_date")));
    }

     /**
     * Lists all attendance entities.
     *
     * @Route("/attendance_section/results", name="attendance_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $session = $this->getRequest()->getSession();

        if (!$session->has("attendance_student")){
            throw $this->createNotFoundException('Object Not Found');
        }

        $attendances = $this->getAttendanceEntityManager();
        $attendances->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($attendances);

        $function = function($qb) use ($session)
        {
            $qb->andWhere("attendance_student.id IN (:p)");
            $qb->setParameter('p', $session->get("attendance_student"));
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * Get attendance Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getAttendanceEntityManager()
    {
        if (!$this->has('sms.datatable.attendance_student')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.attendance_student');
    }

    /**
     * Get attendance Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getAttendanceStudentEntityManager()
    {
        if (!$this->has('sms.datatable.attendance')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.attendance');
    }
}
