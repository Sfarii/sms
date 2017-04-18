<?php

namespace API\Services;

use Exception;
use SMS\AdministrativeBundle\Entity\AttendanceStudent;
use SMS\AdministrativeBundle\Entity\AttendanceProfessor;
use SMS\StudyPlanBundle\Entity\Note;
use SMS\UserBundle\Entity\Student;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Services
 */

class EntityManager 
{
    /**
	* @var Doctrine\ORM\EntityManager
	*/
	private $_em;

	/**
	* @param Doctrine\ORM\EntityManager $em
    * @param int $limitPerPage
	*/
	public function __construct($em)
    {
        $this->_em = $em;
    }

    /**
    * insert entity in the database
    * @param Object $object
    * @param User $user
    */
    public function insert($object , $user = null)
    {
        if ( !is_null($user)){
            $object->setUser($user);
        }

    	$this->_em->persist($object);
        $this->_em->flush($object);
    }

    /**
    * update entity in the database
    * @param Object $object
    */
    public function update($object)
    {
    	$this->_em->flush($object);
    }

    /**
    * delete one entity from the database
    * @param Object $object
    */
    public function delete($object)
    {
    	$this->_em->remove($object);
        $this->_em->flush();
    }

    /**
    * delete multiple entity from the database
    * @param Object $object
    */
    public function deleteAll($className ,$choices = array())
    {
        $repository = $this->_em->getRepository($className);

        foreach ($choices as $choice) {

            $object = $repository->find($choice['value']);

            
            try {
                if ($object){
                    $this->_em->remove($object);
                }
            } catch (Exception $e) {
                throw new Exception("Error this Entity has child ", 1);
            }

        }

        $this->_em->flush();
    }

    /**
    * insert note in the database
    * @param Form $form
    * @param User $user
    */
    public function addNote($form , $user = null)
    {
        if (is_null($user)){
            throw new Exception("Error No User Found", 1);
        }

        $section = $form->get('section')->getData();
        $exam = $form->get('exam')->getData();
        if ($form->has('student')){
            $students = $form->get('student')->getData();
        }

        $students = $this
                        ->_em
                        ->getRepository(Student::class)
                        ->findBySection($section->getId());

        $note_ids = array();
        foreach ($students as $student) {
            
            $found = $this
                        ->_em
                        ->getRepository(Note::class)
                        ->findByExamAndStudent($exam,$student);
            
            if (!is_null($found)){
                $note_ids[] = $found->getId();
                continue;
            }
            $note = new Note();
            $note->setExam($exam);
            $note->setUser($user);
            $note->setStudent($student);
            $this->_em->persist($note);
            $this->_em->flush($note);
            $note_ids[] = $note->getId();
        }

        return $note_ids;
        
    }

    /**
    * insert student attendance in the database
    * @param Form $form
    * @param User $user
    */
    public function addStudentAttendance($form , $user = null)
    {
        if (is_null($user)){
            throw new Exception("Error No User Found", 1);
        }

        $section = $form->get('section')->getData();
        $date = $form->get('date')->getData();
        $session = $form->get('session')->getData();
        
        $students = $this->_em
                            ->getRepository(Student::class)
                            ->findBySection($section->getId());

        $attendance_ids = array();
        foreach ($students as $student) {
            $found = $this
                        ->_em
                        ->getRepository(AttendanceStudent::class)
                        ->findByDateAndSessionAndUser($date,$session,$student);
            
            if (!is_null($found)){
                $attendance_ids[] = $found->getId();
                continue;
            }
            
            $attendance = new AttendanceStudent();
            $attendance->setSession($session);
            $attendance->setDate($date);
            $attendance->setStudent($student);
            $attendance->setUser($user);
            $attendance->setStatus(true);
            $this->_em->persist($attendance);
            $this->_em->flush($attendance);
            $attendance_ids[] = $attendance->getId();
        }

        return $attendance_ids;
        
    }

    /**
    * insert Attendance Professor in the database
    * @param AttendanceProfessor $attendance
    * @param User $user
    */
    public function addProfessorAttendance($attendance , $user = null)
    {
        if (is_null($user)){
            throw new Exception("Error No User Found", 1);
        }

        $found = $this
                    ->_em
                    ->getRepository(AttendanceProfessor::class)
                    ->findByDateAndSessionAndUser($attendance);
            
        if (!is_null($found)){
            if ($found->getStatus() !== $attendance->getStatus()){
                $found->setStatus($attendance->getStatus());
            }
            $this->update($found);
        }else{
            $this->insert($attendance , $user);
        }
        
    }

}
