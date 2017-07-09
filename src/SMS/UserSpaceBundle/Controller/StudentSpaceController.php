<?php

namespace SMS\UserSpaceBundle\Controller;

use API\BaseController\BaseController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SMS\StudyPlanBundle\Entity\Note;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\StudyPlanBundle\Entity\TypeExam;
use SMS\StudyPlanBundle\Entity\Exam;
use SMS\UserBundle\Entity\Student;
use Symfony\Component\HttpFoundation\JsonResponse;
use SMS\UserSpaceBundle\Form\DivisionListType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Student Space Controller controller.
 *
 * @Route("student_space")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package Space\StudentBundle\Controller
 */
class StudentSpaceController extends Controller
{
    /**
     * Lists all schedule by Student entities.
     *
     * @Route("/schedule", name="schedule_student_space")
     * @Method({"GET", "POST"})
     * @Template("SMSUserSpaceBundle:studentspace/schedule:index.html.twig")
     */
    public function scheduleAction(Request $request)
    {
        $form = $this->createForm(DivisionListType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('send')->isClicked()) {
            $result = $this->getUserSapaceManager()->getSchedule($this->getUser()->getSection(), $form->get('division')->getData());
            $result['form'] = $form->createView();
            return $result;
        }

        return array('form' => $form->createView());
    }

    /**
     * Lists all note by Student entities.
     *
     * @Route("/note", name="note_student_space")
     * @Method({"GET", "POST"})
     * @Template("SMSUserSpaceBundle:studentspace/note:index.html.twig")
     */
    public function noteAction(Request $request)
    {
        $form = $this->createForm(DivisionListType::class)->handleRequest($request);
        $division = $form->get('division')->getData();
        if ($form->isSubmitted() && !is_null($division) && $form->isValid() && $form->get('send')->isClicked()) {
            $userSpace = $this->getUserSapaceManager();
            $result = $userSpace->getNotes($this->getUser(), $division);
            $result['form'] = $form->createView();
            return $result;
        }

        return array('form' => $form->createView());
    }

    /**
     * Lists all Exam Date by Student entities.
     *
     * @Route("/exam", name="exam_date_student_space")
     * @Method("GET")
     * @Template("SMSUserSpaceBundle:studentspace/exam:index.html.twig")
     */
    public function examDateAction(Request $request)
    {
    }

    /**
     * Lists of Student attendance  entities.
     *
     * @Route("/attendance", name="attendance_student_space")
     * @Method({"GET", "POST"})
     * @Template("SMSUserSpaceBundle:studentspace/attendance:index.html.twig")
     */
    public function attendanceAction(Request $request)
    {
        $form = $this->createForm(DivisionListType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('send')->isClicked()) {
            //$result = $this->getUserSapaceManager()->getAttendanceOfStudent($this->getUser(), $form->get('division')->getData());
            $result = $this->getUserSapaceManager()->getAttendanceOfStudentByCourses($this->getUser() ,$form->get('division')->getData());
            $result['form'] = $form->createView();
            return $result;
        }
        return array('form' => $form->createView());
    }

    /**
     * Lists all Exam Date by Student entities.
     *
     * @Route("/json_exam", name="exam_date_json_student_space" , options={"expose"=true})
     * @Method("GET")
     */
    public function examDateJSONAction(Request $request)
    {
        $startDate = new \DateTime(date('Y-m-d', $request->query->get('start')));
        $endDate = new \DateTime(date('Y-m-d', $request->query->get('end')));

        $examDate = $this->getDoctrine()
                        ->getRepository(Exam::class)
                        ->findByStartDateAndEndDateBySection($startDate, $endDate, $this->getUser()->getSection());


        $response = new JsonResponse();
        $response->setData($examDate);
        return $response;
    }

    /**
     * Lists all sanction entities.
     *
     * @Route("/", name="sanction_student_index")
     * @Method("GET")
     * @Template("SMSUserSpaceBundle:studentspace/sanction:index.html.twig")
     */
    public function indexAction()
    {
        $sanctions = $this->getSanctionEntityManager();
        $sanctions->buildDatatable();

        return array('sanctions' => $sanctions);
    }

    /**
     * Lists all sanction entities.
     *
     * @Route("/results", name="sanction_student_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $sanctions = $this->getSanctionEntityManager();
        $sanctions->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($sanctions);
        $student = $this->getUser();
        $function = function($qb) use (&$student)
        {
            $qb->andWhere("student.id = :p");
            $qb->setParameter('p', $student->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Get sanction Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getSanctionEntityManager()
    {
        if (!$this->has('sms.datatable.sanction_student')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.sanction_student');
    }
}
