<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Exam;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\StudyPlanBundle\Form\ExamType;
use SMS\StudyPlanBundle\Form\ExamEditType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exam controller.
 *
 * @Route("exam")
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
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:exam:index.html.twig")
     */
    public function indexAction()
    {
        $exams = $this->getExamEntityManager();
        $exams->buildDatatable();

        return array('exams' => $exams);
    } /**
     * Lists all exam entities.
     *
     * @Route("/results", name="exam_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $exams = $this->getExamEntityManager();
        $exams->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($exams);

        return $query->getResponse();
    }
    /**
     * Creates a new exam entity.
     *
     * @Route("/new/{id}", name="exam_new", options={"expose"=true} )
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:exam:new.html.twig")
     */
    public function newAction(Request $request, Course $course)
    {
        $exam = new Exam();
        $form = $this->createForm(ExamType::class, $exam, array(
                            'establishment' => $this->getUser()->getEstablishment(),
                            'course' => $course
                            ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($exam , $this->getUser());
            $this->flashSuccessMsg('exam.add.success');
            return $this->redirectToRoute('exam_index');
        }

        return array(
            'exam' => $exam,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a exam entity.
     *
     * @Route("/{id}", name="exam_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Exam $exam)
    {
        $deleteForm = $this->createDeleteForm($exam);

        return $this->render('SMSStudyPlanBundle:exam:show.html.twig', array(
            'exam' => $exam,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing exam entity.
     *
     * @Route("/{id}/edit", name="exam_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:exam:edit.html.twig")
     */
    public function editAction(Request $request, Exam $exam)
    {
        $editForm = $this->createForm(ExamType::class, $exam, array(
                            'establishment' => $this->getUser()->getEstablishment(),
                            'course' => $exam->getCourse()
                            ))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($exam);
            $this->flashSuccessMsg('exam.edit.success');
            return $this->redirectToRoute('exam_index');
        }

        return array(
            'exam' => $exam,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="exam_bulk_delete")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkDeleteAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            try {
                $this->getEntityManager()->deleteAll(exam::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('exam.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('exam.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a exam entity.
     *
     * @Route("/{id}", name="exam_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Exam $exam)
    {
        $form = $this->createDeleteForm($exam)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($exam);
            $this->flashSuccessMsg('exam.delete.one.success');
        }

        return $this->redirectToRoute('exam_index');
    }

    /**
     * Creates a form to delete a exam entity.
     *
     * @param Exam $exam The exam entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Exam $exam)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('exam_delete', array('id' => $exam->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get exam Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getExamEntityManager()
    {
        if (!$this->has('sms.datatable.exam')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.exam');
    }}
