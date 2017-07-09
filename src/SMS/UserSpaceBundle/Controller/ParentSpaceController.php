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
use Symfony\Component\HttpFoundation\JsonResponse;
use SMS\UserSpaceBundle\Form\StudentAndDivisionListType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 *
 */

/**
 * Parent Space Controller controller.
 *
 * @Route("parent_space")
 * @Security("has_role('ROLE_ADMIN')")
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package Space\parentBundle\Controller
 */
class ParentSpaceController extends Controller
{
    /**
     * Lists all schedule by parent entities.
     *
     * @Route("/schedule", name="schedule_parent_space")
     * @Method({"GET", "POST"})
     * @Template("SMSUserSpaceBundle:parentspace/schedule:index.html.twig")
     */
    public function scheduleAction(Request $request)
    {
        $form = $this->createForm(StudentAndDivisionListType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('send')->isClicked()) {
            $result = $this->getUserSapaceManager()->getSchedule($form->get('student')->getData()->getSection(), $form->get('division')->getData());
            $result['form'] = $form->createView();
            return $result;
        }

        return array('form' => $form->createView());
    }

    /**
     * Lists all note by parent entities.
     *
     * @Route("/note", name="note_parent_space")
     * @Method({"GET", "POST"})
     * @Template("SMSUserSpaceBundle:parentspace/note:index.html.twig")
     */
    public function noteAction(Request $request)
    {
        $form = $this->createForm(StudentAndDivisionListType::class)->handleRequest($request);
        $division = $form->get('division')->getData();
        if ($form->isSubmitted() && !is_null($division) && $form->isValid() && $form->get('send')->isClicked()) {
            $userSpace = $this->getUserSapaceManager();
            $result = $userSpace->getNotes($form->get('student')->getData(), $division);
            $result['form'] = $form->createView();
            return $result;
        }

        return array('form' => $form->createView());
    }

    /**
     * Lists all Exam Date by parent entities.
     *
     * @Route("/exam", name="exam_date_parent_space")
     * @Method("GET")
     * @Template("SMSUserSpaceBundle:parentspace/exam:index.html.twig")
     */
    public function examDateAction(Request $request)
    {}

    /**
     * Lists of parent attendance  entities.
     *
     * @Route("/attendance", name="attendance_parent_space")
     * @Method({"GET", "POST"})
     * @Template("SMSUserSpaceBundle:parentspace/attendance:index.html.twig")
     */
    public function attendanceAction(Request $request)
    {
        $form = $this->createForm(StudentAndDivisionListType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('send')->isClicked()) {
            //$result = $this->getUserSapaceManager()->getAttendanceOfparent($form->get('student')->getData(), $form->get('division')->getData());
            $result = $this->getUserSapaceManager()->getAttendanceOfStudentByCourses($form->get('student')->getData() ,$form->get('division')->getData());
            $result['form'] = $form->createView();
            return $result;
        }
        return array('form' => $form->createView());
    }

    /**
     * Lists all Exam Date by parent entities.
     *
     * @Route("/json_exam", name="exam_date_json_parent_space" , options={"expose"=true})
     * @Method("GET")
     */
    public function examDateJSONAction(Request $request)
    {
        $startDate = new \DateTime(date('Y-m-d', $request->query->get('start')));
        $endDate = new \DateTime(date('Y-m-d', $request->query->get('end')));
        $examDate = $this->getDoctrine()
                        ->getRepository(Exam::class)
                        ->findByStartDateAndEndDateByStudents($startDate, $endDate, $this->getUser()->getStudents()->toArray());


        $response = new JsonResponse();
        $response->setData($examDate);
        return $response;
    }

    /**
     * Lists all sanction entities.
     *
     * @Route("/", name="sanction_parent_index")
     * @Method("GET")
     * @Template("SMSUserSpaceBundle:parentspace/sanction:index.html.twig")
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
     * @Route("/results", name="sanction_parent_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $sanctions = $this->getSanctionEntityManager();
        $sanctions->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($sanctions);
        $students = $this->getUser()->getStudents()->toArray();
        $function = function($qb) use ($students)
        {
            $qb->andWhere("sanction.student IN (:p)");
            $qb->setParameter('p', $students);
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
        if (!$this->has('sms.datatable.sanction_parent')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.sanction_parent');
    }
}
