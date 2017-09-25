<?php

namespace SMS\AdministrativeBundle\Controller;

use SMS\AdministrativeBundle\Entity\AttendanceProfessor;
use SMS\AdministrativeBundle\Form\ProfessorAttendanceType;
use SMS\AdministrativeBundle\Form\ScheduleStudentFilterType;
use SMS\AdministrativeBundle\Form\ScheduleProfessorFilterType;
use SMS\AdministrativeBundle\Form\SearchType;
use SMS\AdministrativeBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\UserBundle\Entity\Student;
use SMS\AdministrativeBundle\Form\AttendanceFilterType;
use SMS\EstablishmentBundle\Entity\Division;

/**
 * Attendance Student controller.
 *
 * @Route("attendance_student")
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
     * Lists all attendanceProfessor entities.
     *
     * @Route("/", name="attendance_student_new")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:attendancestudent:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(SearchType::class,null, array('method' => 'GET'))->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $this->getEntityManager()->getAllStudents($form , $this->getUser()->getEstablishment()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            12/*limit per page*/
        );
        $sort = $request->query->get('sort', 'empty');
        if ($sort == "empty"){
          $pagination->setParam('sort', 'student.firstName');
          $pagination->setParam('direction', 'asc');
        }
        // parameters to template
        return array('pagination' => $pagination , 'form' => $form->createView());
    }

    /**
     * Finds and displays a attendanceProfessor entity.
     *
     * @Route("/show/{id}", name="attendance_student_show")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:attendancestudent:show.html.twig")
     */
    public function showAction(Student $student)
    {
        $attendanceStudent = $this->getAttendanceStudentEntityManager();
        $attendanceStudent->buildDatatable(array('id' => $student->getId()));

        return array('student' => $student, 'echarts' => $this->getEntityManager()->getStatsStudent($student), 'attendanceStudent' => $attendanceStudent);
    }

    /**
     * Lists all attendance Professor entities.
     *
     * @Route("/results/{id}", name="attendance_student_info_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction(Student $student)
    {
        $attendanceStudent = $this->getAttendanceStudentEntityManager();
        $attendanceStudent->buildDatatable(array('id' => $student->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($attendanceStudent);
        $function = function($qb) use ($student)
        {
            $qb->join('attendance_student.student', 'student')
                ->andWhere('student.id = :student')
        				->setParameter('student', $student->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * Get attendanceProfessor Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getAttendanceStudentEntityManager()
    {
      if (!$this->has('sms.datatable.student_attendance')){
         throw $this->createNotFoundException('Service Not Found');
      }

      return $this->get('sms.datatable.student_attendance');
    }


}
