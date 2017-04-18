<?php

namespace SMS\AdministrativeBundle\Controller;

use SMS\AdministrativeBundle\Entity\AttendanceStudent;
use SMS\AdministrativeBundle\Form\StudentAttendanceType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attendance Student controller.
 *
 * @Route("attendance")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\AdministrativeBundle\Controller
 *
 */
class AttendanceStudentController extends BaseController
{
    /**
     * Lists all attendance entities.
     *
     * @Route("/", name="attendance_index")
     * @Method("GET")
     * @Template("smsadministrativebundle/attendance/index.html.twig")
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        
        if (!$session->has("attendance_student")){
            throw $this->createNotFoundException('Object Not Found');
        }

        $attendances = $this->getAttendanceEntityManager();
        $attendances->buildDatatable();

        return array('attendances' => $attendances);
    }

     /**
     * Lists all attendance entities.
     *
     * @Route("/results", name="attendance_results")
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
     * Creates a new attendance entity.
     *
     * @Route("/new_attendance_student", name="attendance_student_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("smsadministrativebundle/attendance/new.html.twig")
     */
    public function newAttendanceStudentAction(Request $request)
    {
        $form = $this->createForm(StudentAttendanceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $ids = $this->getEntityManager()->addStudentAttendance($form , $this->getUser());
            // set the array of IDS in the session
            $session = $this->getRequest()->getSession();
            $session->set("attendance_student", $ids);
            
            return $this->forward('SMSAdministrativeBundle:AttendanceStudent:index');
        }

        return array('form' => $form->createView());
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
        if (!$this->has('sms.datatable.attendance')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.attendance');
    }
}
