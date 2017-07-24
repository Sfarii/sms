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
use SMS\UserBundle\Entity\Professor;
use SMS\AdministrativeBundle\Form\AttendanceFilterType;
use SMS\EstablishmentBundle\Entity\Division;

/**
 * Attendanceprofessor controller.
 *
 * @Route("attendanceprofessor")
 * @Security("has_role('ROLE_ADMIN')")
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
     * @Route("/schedule_professor/{id}", name="attendanceprofessor_index")
     * @Method({"GET", "POST"})
     * @Template("SMSAdministrativeBundle:attendanceprofessor:professor.html.twig")
     */
    public function scheduleProfessorAction(Request $request,Professor $professor)
    {
      $form = $this->createForm(AttendanceFilterType::class, null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $result = $this->getEntityManager()->getScheduleByProfessor($professor,$form->get('division')->getData(),$form->get('date')->getData(), $this->getUser()->getEstablishment());
          $result['form'] = $form->createView();
          return array('result' => $result , 'form' => $form->createView() , 'professor' => $professor);
      }

      return array('form' => $form->createView()  , 'professor' => $professor);
    }

    /**
     * Lists all attendanceProfessor entities.
     *
     * @Route("/", name="attendance_professor_new")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:attendanceprofessor:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(SearchType::class,null, array('method' => 'GET'))->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $this->getEntityManager()->getAllProfessors($form , $this->getUser()->getEstablishment()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            12/*limit per page*/
        );
        $sort = $request->query->get('sort', 'empty');
        if ($sort == "empty"){
          $pagination->setParam('sort', 'professor.firstName');
          $pagination->setParam('direction', 'asc');
        }
        // parameters to template
        return array('pagination' => $pagination , 'form' => $form->createView());
    }

    /**
     * Finds and displays a attendanceProfessor entity.
     *
     * @Route("/show/{id}", name="attendance_professor_show")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:attendanceprofessor:show.html.twig")
     */
    public function showAction(Professor $professor)
    {
        $attendanceProfessors = $this->getAttendanceProfessorEntityManager();
        $attendanceProfessors->buildDatatable(array('id' => $professor->getId()));

        return array('professor' => $professor, 'echarts' => $this->getEntityManager()->getStatsProfessor($professor), 'attendanceProfessors' => $attendanceProfessors);
    }

    /**
     * Lists all attendance Professor entities.
     *
     * @Route("/results/{id}", name="attendanceprofessor_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction(Professor $professor)
    {
        $attendanceProfessors = $this->getAttendanceProfessorEntityManager();
        $attendanceProfessors->buildDatatable(array('id' => $professor->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($attendanceProfessors);
        $function = function($qb) use ($professor)
        {
            $qb->join('course.division', 'division')
                ->join('attendance_professor.professor', 'professor')
                ->andWhere('professor.id = :professor')
        				->setParameter('professor', $professor->getId());
        };

        $query->addWhereAll($function);
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
     * @Route("/new/attendance", name="attendance_professor_add", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();
        if ($isAjax) {
            $data = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('attendance_professor', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }
            if ($this->getEntityManager()->addProfessorAttendance($data, $this->getUser())){
                return new Response($this->get('translator')->trans('attendanceprofessor.add.success'), 200);
            }else{
              return new Response($this->get('translator')->trans('attendanceprofessor.check.data'), 200);
            }
        }

        return new Response('Bad Request', 400);
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
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/update/{status}", name="attendance_professor_bulk_update")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkDeleteAction(Request $request, $status)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }
            $this->getEntityManager()->updateAll(Attendanceprofessor::class ,$choices, $status);
            return new Response($this->get('translator')->trans('attendanceprofessor.edit.success'), 200);
        }

        return new Response('Bad Request', 400);
    }


}
