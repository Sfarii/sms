<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Exam;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\StudyPlanBundle\Form\ExamType;
use SMS\StudyPlanBundle\Form\SearchType;
use SMS\StudyPlanBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\StudyPlanBundle\Form\ExamFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\EstablishmentBundle\Entity\Division;
use SMS\StudyPlanBundle\Entity\TypeExam;
use SMS\UserBundle\Entity\Student;

/**
 * Exam controller.
 *
 * @Route("exam")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class ExamController extends BaseController
{
    /**
     * Lists all exam entities.
     *
     * @Route("/", name="exam_index")
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:exam:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(ExamFilterType::class, null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->getEntityManager()->getExams($form->get('section')->getData(),$form->get('division')->getData(), $this->getUser()->getEstablishment());
            $examForm = $this->createForm(ExamType::class, new Exam(), array('division' => $form->get('division')->getData() ,'section' => $form->get('section')->getData(),'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
            return array('form' => $form->createView() , 'result' => $result , 'examForm' => $examForm->createView());
        }
        return array('form' => $form->createView());
    }

    /**
     * Finds and displays a exam entity.
     *
     * @Route("/update/{id}", name="exam_update")
     * @Method("GET")
     */
    public function updateAction(Exam $exam)
    {
      $this->getEntityManager()->addNote($exam , $this->getUser());
      $this->flashSuccessMsg('exam.add.success');
      return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * Creates a new exam entity.
     *
     * @Route("/new/{id_section}/{id_division}", name="exam_new")
     * @ParamConverter("division", class="SMSEstablishmentBundle:Division", options={"id" = "id_division"})
     * @ParamConverter("section", class="SMSEstablishmentBundle:Section", options={"id" = "id_section"})
     * @Method("POST")
     */
    public function newAction(Request $request, Division $division,Section $section)
    {
        $exam = new Exam();
        $form = $this->createForm(ExamType::class, $exam, array('division' => $division ,'section' => $section,'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->insert($exam , $this->getUser());
            $this->getEntityManager()->addNote($exam , $this->getUser());
            return new Response(json_encode(array('success' => $this->get('translator')->trans('exam.add.success'))), 200);
        }
        return new Response(json_encode(array('error' => $this->getErrorMessages($form))), 200);
    }

    /**
     * Finds and displays a exam entity.
     *
     * @Route("/show/{id}", name="exam_show")
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:exam:show.html.twig")
     */
    public function showAction(Exam $exam)
    {
        $notes = $this->getDataTableNoteEntityManager();
        $notes->buildDatatable(array('id' => $exam->getId()));
        return array(
            'notes' => $notes,
            'exam' => $exam
        );
    }

    /**
     * Lists all marks entities.
     *
     * @Route("/results/{id}", name="note_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction(Exam $exam)
    {
        $notes = $this->getDataTableNoteEntityManager();
        $notes->buildDatatable(array('id' => $exam->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($notes);

        $function = function($qb) use ($exam)
        {
            $qb->join('note.exam', 'exam');
            $qb->andWhere("exam.id = :id ");
            $qb->setParameter('id', $exam->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * Displays a form to edit an existing exam entity.
     *
     * @Route("/{id_exam}/{id_division}/edit", name="exam_edit", options={"expose"=true})
     * @ParamConverter("division", class="SMSEstablishmentBundle:Division", options={"id" = "id_division"})
     * @ParamConverter("exam", class="SMSStudyPlanBundle:Exam", options={"id" = "id_exam"})
     * @Method("POST")
     */
    public function editAction(Request $request, Exam $exam , Division $division)
    {
      $editForm = $this->createForm(ExamType::class, $exam, array('division' => $division ,'section' => $exam->getSection(),'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEntityManager()->update($exam);
            return new Response(json_encode(array('success' => $this->get('translator')->trans('exam.edit.success'))), 200);
        }
        return new Response(json_encode(array('error' => $this->getErrorMessages($editForm))), 200);
    }

    /**
     * Deletes a exam entity.
     *
     * @Route("delete/{id}", name="exam_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Exam $exam)
    {
        $token = $request->query->get('token');
        if (!$this->isCsrfTokenValid('exam_delete', $token)) {
            return new Response($this->get('translator')->trans('exam.delete.fail'), 200);
        }else{
          $this->getEntityManager()->delete($exam);
          return new Response($this->get('translator')->trans('exam.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Lists all students entities.
     *
     * @Route("/students", name="exam_students_index")
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:exam:students.html.twig")
     */
    public function StudentsExamAction(Request $request)
    {
      $form = $this->createForm(SearchType::class,null, array('method' => 'GET','establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

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
     * Lists all note by Student entities.
     *
     * @Route("/mark/{id}", name="mark_student_index")
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:exam:mark.html.twig")
     */
    public function noteAction(Request $request , Student $student)
    {
        $divisions = $this->getDoctrine()->getRepository(Division::class)->findBy(array('establishment' => $this->getUser()->getEstablishment()));
        $divisionId = $request->query->get('division', reset($divisions)->getId());
        $division = array_filter($divisions, function($value) use ($divisionId) { return strcasecmp($divisionId,$value->getId()) == 0 ; });
        $division = reset($division);
        if (!is_null($division)) {
            return array('divisionID' => $divisionId , 'student'=> $student , 'divisions' => $divisions , 'result' => $this->getEntityManager()->getNotes($student, $division));
        }

        return $this->redirectToRoute('exam_students_index');
    }

    /**
     * Get Service.
     *
     * @throws \NotFoundException
     */
    protected function getDataTableNoteEntityManager()
    {
        if (!$this->has('sms.datatable.note')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.note');
    }
}
