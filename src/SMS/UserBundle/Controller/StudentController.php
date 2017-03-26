<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\Student;
use SMS\UserBundle\Form\StudentType;
use SMS\Classes\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

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
     */
    public function indexAction(Request $request)
    {
        $students = $this->getEntityManager()->getEntityBy(Student::class , $request);

        return $this->render('smsuserbundle/student/index.html.twig', array(
            'students' => $students,
            'search' => $request->query->get($this->getParameter('search_field'), null)
        ));
    }

    /**
     * Creates a new student entity.
     *
     * @Route("/new", name="student_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getUserEntityManager()->addUser($student);
            $this->flashSuccessMsg('student.add.success');
            return $this->redirectToRoute('student_index');
        }

        return $this->render('smsuserbundle/student/new.html.twig', array(
            'student' => $student,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a student entity.
     *
     * @Route("/{id}", name="student_show")
     * @Method("GET")
     */
    public function showAction(Student $student)
    {
        $deleteForm = $this->createDeleteForm($student);

        return $this->render('smsuserbundle/student/show.html.twig', array(
            'student' => $student,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing student entity.
     *
     * @Route("/{id}/edit", name="student_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Student $student)
    {

        $editForm = $this->createForm(StudentType::class, $student)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($student);
            $this->flashSuccessMsg('student.edit.success');
            return $this->redirectToRoute('student_index');
        }

        return $this->render('smsuserbundle/student/edit.html.twig', array(
            'student' => $student,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Finds and displays a student entity.
     *
     * @Route("/multiple_actions", name="student_bulk_action")
     * @Method("POST")
     */
    public function multipleActionsAction(Request $request)
    {
        $actions = $request->request->get($this->getParameter('index_actions') , null);
        $keys = $request->request->get($this->getParameter('index_keys') , null);
            
        if(!is_null($keys) && $actions === $this->getParameter('index_delete')){
            $this->getEntityManager()->deleteAll(student::class ,$keys);
            $this->flashSuccessMsg('student.delete.all.success');
        }else{
            $this->flashErrorMsg('student.delete.all.error');
        }

        return $this->redirectToRoute('student_index');
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
}
