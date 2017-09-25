<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Course;
use SMS\StudyPlanBundle\Form\CourseType;
use SMS\StudyPlanBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\StudyPlanBundle\Form\SimpleSearchType;
use SMS\EstablishmentBundle\Entity\Grade;

/**
 * Course controller.
 *
 * @Route("course")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class CourseController extends BaseController
{

    /**
     * Lists all grades entities.
     *
     * @Route("/", name="course_index")
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:course:grade.html.twig")
     */
    public function indexGradeAction(Request $request)
    {
        $form = $this->createForm(SimpleSearchType::class,null, array('method' => 'GET'))->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $this->getEntityManager()->getAllGrades($form , $this->getUser()->getEstablishment()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            12/*limit per page*/
        );
        $sort = $request->query->get('sort', 'empty');
        if ($sort == "empty"){
          $pagination->setParam('sort', 'grade.gradeName');
          $pagination->setParam('direction', 'asc');
        }
        // parameters to template
        return array('pagination' => $pagination , 'form' => $form->createView());
    }

    /**
     * Lists all course entities.
     *
     * @Route("/grade/{id}", name="course_grade_index")
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:course:index.html.twig")
     */
    public function indexAction(Grade $grade)
    {
        $courses = $this->getCourseEntityManager();
        $courses->buildDatatable(array('id' => $grade->getId()));

        return array('courses' => $courses);
    }

     /**
     * Lists all course entities.
     *
     * @Route("/results/{id}", name="course_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction(Grade $grade)
    {
        $courses = $this->getCourseEntityManager();
        $courses->buildDatatable(array('id' => $grade->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($courses);

        $user = $this->getUser();
        $function = function($qb) use ($user , $grade)
        {
            $qb->join('course.establishment', 'establishment')
                ->join('course.grade', 'grade')
                ->andWhere('establishment.id = :establishment')
                ->andWhere('grade.id = :grade')
                ->setParameter('grade', $grade->getId())
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }
    /**
     * Creates a new course entity.
     *
     * @Route("/new", name="course_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:course:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($course , $this->getUser());
            $this->flashSuccessMsg('course.add.success');
            return $this->redirectToRoute('course_new');
        }

        return array(
            'course' => $course,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a course entity.
     *
     * @Route("/{id}", name="course_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Course $course)
    {
        $deleteForm = $this->createDeleteForm($course);

        return $this->render('SMSStudyPlanBundle:course:show.html.twig', array(
            'course' => $course,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing course entity.
     *
     * @Route("/{id}/edit", name="course_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:course:edit.html.twig")
     */
    public function editAction(Request $request, Course $course)
    {
        $editForm = $this->createForm(CourseType::class, $course, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($course);
            $this->flashSuccessMsg('course.edit.success');
            return $this->redirectToRoute('course_index');
        }

        return array(
            'course' => $course,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="course_bulk_delete")
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
                $this->getEntityManager()->deleteAll(course::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('course.delete.fail'), 200);
            }

            return new Response($this->get('translator')->trans('course.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a course entity.
     *
     * @Route("/{id}", name="course_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Course $course)
    {
        $form = $this->createDeleteForm($course)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($course);
            $this->flashSuccessMsg('course.delete.one.success');
        }

        return $this->redirectToRoute('course_index');
    }

    /**
     * Creates a form to delete a course entity.
     *
     * @param Course $course The course entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Course $course)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('course_delete', array('id' => $course->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get Service.
     *
     * @throws \NotFoundException
     */
    protected function getCourseEntityManager()
    {
        if (!$this->has('sms.datatable.course')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.course');
    }}
