<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\Student;
use SMS\UserBundle\Form\StudentType;
use SMS\UserBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Student controller.
 *
 * @Route("student")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Controller
 *
 */
class StudentController extends BaseController
{
    /**
     * Lists all student entities.
     *
     * @Route("/", name="student_index")
     * @Method("GET")
     * @Template("SMSUserBundle:student:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $student = $this->getStudentEntityManager();
        $student->buildDatatable();

        return array('students' => $student);
    }

    /**
     * Lists all student entities.
     *
     * @Route("/results", name="student_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $student = $this->getStudentEntityManager();
        $student->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($student);

        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('student.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
                ->andWhere('student.id != :userId')
                ->setParameter('userId', $user->getId())
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }


    /**
     * Creates a new student entity.
     *
     * @Route("/new", name="student_new")
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:student:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student, array(
            'establishment' => $this->getUser()->getEstablishment()
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getUserEntityManager()->addUser($student);
            $this->flashSuccessMsg('student.add.success');
            return $this->redirectToRoute('student_index');
        }

        return array(
            'student' => $student,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a student entity.
     *
     * @Route("/{id}", name="student_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSUserBundle:student:show.html.twig")
     */
    public function showAction(Student $student)
    {
        $deleteForm = $this->createDeleteForm($student);

        return array(
            'student' => $student,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing student entity.
     *
     * @Route("/{id}/edit", name="student_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:student:edit.html.twig")
     */
    public function editAction(Request $request, Student $student)
    {

        $editForm = $this->createForm(StudentType::class, $student, array(
                              'establishment' => $this->getUser()->getEstablishment()
                          ))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getUserEntityManager()->editUser($student);
            $this->flashSuccessMsg('student.edit.success');
            if ($student->getId() !== $this->getUser()->getId()){
              return $this->redirectToRoute('student_index');
            }
        }

        return array(
            'user' => $student,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a student entity.
     *
     * @Route("/{id}", name="student_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Student $student)
    {
        $form = $this->createDeleteForm($student)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($student);
            $this->flashSuccessMsg('student.delete.one.success');
        }

        return $this->redirectToRoute('student_index');
    }

    /**
     * Creates a form to delete a student entity.
     *
     * @param Student $student The student entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Student $student)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('student_delete', array('id' => $student->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get student Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getStudentEntityManager()
    {
        if (!$this->has('sms.datatable.student')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.student');
    }
}
