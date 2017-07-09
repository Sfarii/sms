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
use SMS\UserSpaceBundle\Form\DivisionListType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * professor Space Controller controller.
 *
 * @Route("professor_space")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package Space\professorBundle\Controller
 */
class ProfessorSpaceController extends Controller
{
    /**
     * Lists all schedule by professor entities.
     *
     * @Route("/schedule", name="schedule_professor_space")
     * @Method({"GET", "POST"})
     * @Template("SMSUserSpaceBundle:professorspace/schedule:index.html.twig")
     */
    public function scheduleAction(Request $request)
    {
      $form = $this->createForm(DivisionListType::class)->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid() && $form->get('send')->isClicked()) {
          $result = $this->getUserSapaceManager()->getScheduleByProfessor($this->getUser(),$form->get('division')->getData());
          $result['form'] = $form->createView();
          return $result;
      }

      return array('form' => $form->createView());
    }

    /**
     * Lists all Exam Date by professor entities.
     *
     * @Route("/exam", name="exam_date_professor_space")
     * @Method("GET")
     * @Template("SMSUserSpaceBundle:professorspace/exam:index.html.twig")
     */
    public function examDateAction(Request $request)
    {}

    /**
     * Lists of professor attendance  entities.
     *
     * @Route("/attendance", name="attendance_professor_space")
     * @Method({"GET", "POST"})
     * @Template("SMSUserSpaceBundle:professorspace/attendance:index.html.twig")
     */
    public function attendanceAction(Request $request)
    {
        $form = $this->createForm(DivisionListType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('send')->isClicked()) {
            //$result = $this->getUserSapaceManager()->getAttendanceOfprofessor($form->get('student')->getData(), $form->get('division')->getData());
            /*$result = $this->getUserSapaceManager()->getAttendanceOfStudentByCourses( ,$form->get('division')->getData());
            */$result['form'] = $form->createView();
            return $result;
        }
        return array('form' => $form->createView());
    }

    /**
     * Lists all Exam Date by professor entities.
     *
     * @Route("/json_exam", name="exam_date_json_professor_space" , options={"expose"=true})
     * @Method("GET")
     */
    public function examDateJSONAction(Request $request)
    {
        $startDate = new \DateTime(date('Y-m-d', $request->query->get('start')));
        $endDate = new \DateTime(date('Y-m-d', $request->query->get('end')));
        $examDate = $this->getDoctrine()
                        ->getRepository(Exam::class)
                        ->findByStartDateAndEndDateByAuthor($startDate, $endDate, $this->getUser());

        $response = new JsonResponse();
        $response->setData($examDate);
        return $response;
    }

    /**
     * Lists all sanction entities.
     *
     * @Route("/", name="sanction_professor_index")
     * @Method("GET")
     * @Template("SMSUserSpaceBundle:professorspace/sanction:index.html.twig")
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
     * @Route("/results", name="sanction_professor_results")
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
        if (!$this->has('sms.datatable.sanction_professor')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.sanction_professor');
    }
}
