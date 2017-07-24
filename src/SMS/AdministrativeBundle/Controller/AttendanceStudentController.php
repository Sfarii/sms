<?php

namespace SMS\AdministrativeBundle\Controller;

use SMS\AdministrativeBundle\Entity\AttendanceStudent;
use SMS\AdministrativeBundle\Form\AttendanceFilterType;
use SMS\AdministrativeBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\AdministrativeBundle\Form\SearchType;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\AdministrativeBundle\Form\AttendanceSectionFilterType;
use SMS\StudyPlanBundle\Entity\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Attendance Student controller.
 *
 * @Route("attendancestudent")
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
     * @Route("/attendance_student/{id}", name="attendance_student_index")
     * @Method({"GET", "POST"})
     * @Template("SMSAdministrativeBundle:studentattendance:student.html.twig")
     */
    public function attendanceStudentAction(Section $section , Request $request)
    {
        $form = $this->createForm(AttendanceFilterType::class, null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->getEntityManager()->getSchedule($section,$form->get('division')->getData(),$form->get('date')->getData(), $this->getUser()->getEstablishment());
            $result['form'] = $form->createView();
            return array('result' => $result , 'form' => $form->createView() , 'section' => $section);
        }

        return array('form' => $form->createView() , 'section' => $section);
    }

    /**
     * Lists all attendanceProfessor entities.
     *
     * @Route("/", name="attendance_student_new")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:studentattendance:attendance.html.twig")
     */
    public function indexStudentAction(Request $request)
    {
        $form = $this->createForm(SearchType::class,null, array('method' => 'GET'))->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $this->getEntityManager()->getAllSections($form , $this->getUser()->getEstablishment()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            12/*limit per page*/
        );
        $sort = $request->query->get('sort', 'empty');
        if ($sort == "empty"){
          $pagination->setParam('sort', 'section.sectionName');
          $pagination->setParam('direction', 'asc');
        }
        // parameters to template
        return array('pagination' => $pagination , 'form' => $form->createView());
    }

    /**
     * Finds and displays a section entity.
     *
     * @Route("/show/{id}", name="attendance_student_show")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:studentattendance:show.html.twig")
     */
    public function showAction(Section $section, Request $request)
    {
        $form = $this->createForm(AttendanceSectionFilterType::class,null, array('method' => 'GET' , 'grade' => $section->getGrade() , 'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $this->getEntityManager()->getAllSectionAttendances($form , $section), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            12/*limit per page*/
        );
        $sort = $request->query->get('sort', 'empty');
        if ($sort == "empty"){
          $pagination->setParam('sort', 'section.sectionName');
          $pagination->setParam('direction', 'asc');
        }
        // parameters to template
        return array('pagination' => $pagination , 'stats' => $this->getEntityManager()->getAllSectionStats($form ,$section) , 'form' => $form->createView() , 'section' => $section);
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
                      "course"    => $request->request->get('attendance_course', NULL),
                      "division"=> $request->request->get('attendance_division', NULL),
                      "status"  => $request->request->get('attendance_status', NULL));
        if ($this->getEntityManager()->validateStudentAttendance($data , $this->getUser())){
          $ids = $this->getEntityManager()->addStudentAttendance($data, $this->getUser());
          // set the array of IDS in the session
          $session = $this->getRequest()->getSession();
          $session->set("attendance_student", $ids);
          $session->set("attendance_date", $data["date"]);
          $this->flashSuccessMsg('attendancestudent.add.success');
          return $this->redirectToRoute('attendance_index' ,array('id_section'=> $data["section"] ,'date' => $data["date"] ,'id_session' => $data["session"]));
        }
        $this->flashErrorMsg('attendancestudent.check.data');
        return $this->redirectToRoute('attendance_student_index' , array('id' => $data["section"]));
    }

    /**
     * Lists all attendance entities.
     *
     * @Route("/attendance_section/{id_section}/{date}/{id_session}", name="attendance_index")
     * @ParamConverter("date", options={"format": "Y-m-d"})
     * @ParamConverter("session", class="SMSStudyPlanBundle:Session", options={"id" = "id_session"})
     * @ParamConverter("section", class="SMSEstablishmentBundle:Section", options={"id" = "id_section"})
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:studentattendance:index.html.twig")
     */
    public function indexAction(Section $section ,\DateTime $date ,Session $session)
    {
        $attendances = $this->getAttendanceEntityManager();
        $attendances->buildDatatable(array('id_section'=> $section->getId() ,'date' => $date->format('Y-m-d') ,'id_session' => $session->getId()));

        return array('attendances' => $attendances , 'attendance_date' => $date , 'section' => $section);
    }

     /**
     * Lists all attendance entities.
     *
     * @Route("/attendance_section/results/{id_section}/{date}/{id_session}", name="attendance_results")
     * @ParamConverter("date", options={"format": "Y-m-d"})
     * @ParamConverter("session", class="SMSStudyPlanBundle:Session", options={"id" = "id_session"})
     * @ParamConverter("section", class="SMSEstablishmentBundle:Section", options={"id" = "id_section"})
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction(Section $section ,\DateTime $date ,Session $session)
    {
        $attendances = $this->getAttendanceEntityManager();
        $attendances->buildDatatable(array('id_section'=> $section->getId() ,'date' => $date->format('Y-m-d') ,'id_session' => $session->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($attendances);

        $function = function($qb) use ($session, $date ,$section )
        {
            $qb
            ->join('attendance_student.session', 'session')
            ->join('student.section', 'section')
            ->andWhere("attendance_student.date = :date")
            ->andWhere('session.id = :session')
            ->andWhere('section.id = :section')
            ->setParameter('date', $date, \Doctrine\DBAL\Types\Type::DATE)
            ->setParameter('section', $section)
            ->setParameter('session', $session);
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
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/update/{status}", name="attendance_student_bulk_update")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkUpdateAction(Request $request, $status)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }
            $this->getEntityManager()->updateAll(AttendanceStudent::class ,$choices, $status);
            return new Response($this->get('translator')->trans('attendancestudent.edit.success'), 200);
        }

        return new Response('Bad Request', 400);
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
