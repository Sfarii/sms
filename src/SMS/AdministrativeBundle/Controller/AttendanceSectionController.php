<?php

namespace SMS\AdministrativeBundle\Controller;

use SMS\AdministrativeBundle\Entity\AttendanceStudent;
use SMS\AdministrativeBundle\Entity\AttendanceSection;
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
 * @Route("attendance_section")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\AdministrativeBundle\Controller
 *
 */
class AttendanceSectionController extends BaseController
{
    /**
     * Lists all schedule by Student entities.
     *
     * @Route("/index/{id}", name="attendance_section_index")
     * @Method({"GET", "POST"})
     * @Template("SMSAdministrativeBundle:attendancesection:student.html.twig")
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
     * @Route("/", name="attendance_section_new")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:attendancesection:attendance.html.twig")
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
     * @Route("/show/{id}", name="attendance_section_show")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:attendancesection:show.html.twig")
     */
    public function showAction(Section $section, Request $request)
    {
      $attendanceSections = $this->getAttendanceSectionEntityManager();
      $attendanceSections->buildDatatable(array('id' => $section->getId()));

      // parameters to template
      return array('attendanceSections' => $attendanceSections ,'section' => $section , 'echarts' => $this->getEntityManager()->getAllSectionStats($section));
    }

    /**
     * Lists all attendance section entities.
     *
     * @Route("/sections/results/{id}", name="attendance_section_results")
     * @Method("GET")
     * @return Response
     */
    public function indexAttendanceSectionsResultsAction(Section $section)
    {
        $attendanceSections = $this->getAttendanceSectionEntityManager();
        $attendanceSections->buildDatatable(array('id' => $section->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($attendanceSections);
        $function = function($qb) use ($section)
        {
            $qb->join('course.division', 'division')
                ->join('attendance_section.section', 'section')
                ->andWhere('section.id = :section')
        				->setParameter('section', $section->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * Creates a new attendance entity.
     *
     * @Route("/new_attendance_section", name="attendance_student_add", options={"expose"=true})
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
          $id = $this->getEntityManager()->addStudentAttendance($data, $this->getUser());
          $this->flashSuccessMsg('attendancestudent.add.success');
          return $this->redirectToRoute('attendance_index' ,array('id'=> $id));
        }
        $this->flashErrorMsg('attendancestudent.check.data');
        return $this->redirectToRoute('attendance_section_index' , array('id' => $data["section"]));
    }

    /**
     * Lists all attendance entities.
     *
     * @Route("/info/{id}", name="attendance_index", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:attendancesection:info.html.twig")
     */
    public function indexAction(AttendanceSection $attendanceSection)
    {
        $attendances = $this->getAttendanceEntityManager();
        $attendances->buildDatatable(array('id'=> $attendanceSection->getId()));

        return array('attendances' => $attendances , 'attendance_date' => $attendanceSection->getDate() , 'section' => $attendanceSection->getSection());
    }

     /**
     * Lists all attendance entities.
     *
     * @Route("/attendance_section/results/{id}", name="attendance_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction(AttendanceSection $attendanceSection)
    {
        $attendances = $this->getAttendanceEntityManager();
        $attendances->buildDatatable(array('id'=> $attendanceSection->getId() ));

        $query = $this->getDataTableQuery()->getQueryFrom($attendances);

        $function = function($qb) use ($attendanceSection)
        {
            $qb
            ->join('attendance_student.attendanceSection', 'attendance_section')
            ->andWhere('attendance_section.id = :attendance_section')
            ->setParameter('attendance_section', $attendanceSection->getId());
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
        if (!$this->has('sms.datatable.attendance_student')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.attendance_student');
    }

    /**
     * Get attendanceProfessor Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getAttendanceSectionEntityManager()
    {
      if (!$this->has('sms.datatable.attendance_section')){
         throw $this->createNotFoundException('Service Not Found');
      }

      return $this->get('sms.datatable.attendance_section');
    }
}
