<?php

namespace SMS\StudyPlanBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Services
 */

class StudyPlanManager
{
    /**
    * @var EntityManager
    */
    private $_em;

    /**
     * @var array
     */
    private $_days;

    /**
     * @var String Class Names
     */
    protected $studentClass;
    protected $sessionClass;
    protected $scheduleClass;
    protected $sectionClass;
    protected $gradeClass;
    protected $professorClass;
    protected $examTypeClass;
    protected $courseClass;
    protected $examClass;
    protected $noteClass;

    /**
    * @param Doctrine\ORM\EntityManager $em
    * @param int $limitPerPage
    */
    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
    * @param String $noteClass
    */
    public function setNoteClass($noteClass)
    {
      $this->noteClass = $noteClass;
    }

    /**
    * @param String $gradeClass
    */
    public function setGradeClass($gradeClass)
    {
      $this->gradeClass = $gradeClass;
    }

    /**
    * @param String $examTypeClass
    */
    public function setExamTypeClass($examTypeClass)
    {
      $this->examTypeClass = $examTypeClass;
    }

    /**
    * @param String $courseClass
    */
    public function setCourseClass($courseClass)
    {
      $this->courseClass = $courseClass;
    }

    /**
    * @param String $examClass
    */
    public function setExamClass($examClass)
    {
      $this->examClass = $examClass;
    }

    /**
    * @param array $days
    */
    public function setDays(array $days)
    {
        $this->_days = $days;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setProfessorClass($professorClass)
    {
      $this->professorClass = $professorClass;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setSessionClass($sessionClass)
    {
      $this->sessionClass = $sessionClass;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setSectionClass($sectionClass)
    {
      $this->sectionClass = $sectionClass;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setStudentClass($studentClass)
    {
      $this->studentClass = $studentClass;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setScheduleClass($scheduleClass)
    {
      $this->scheduleClass = $scheduleClass;
    }

    /**
    * insert entity in the database
    * @param Object $object
    * @param User $user
    */
    public function insert($object, $user = null)
    {
        if (!is_null($user)){
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
    public function deleteAll($className, $choices = array())
    {
        $repository = $this->_em->getRepository($className);

        foreach ($choices as $choice) {
            $object = $repository->find($choice['value']);


            try {
                if ($object) {
                    $this->_em->remove($object);
                }
            } catch (\Exception $e) {
                throw new \Exception("Error this Entity has child ", 1);
            }
        }

        $this->_em->flush();
    }

    /**
    * @param professor $professor
    * @param Division $division
    * @param Establishment $establishment
    * @return array
    */
    public function getScheduleByProfessor($professor ,$division , $establishment)
    {
        // get All Sessions
        $sessions = $this->_em->getRepository($this->sessionClass)->findAllByStartTime($establishment->getId());
        // get schedule by the professor
        $schedules = $this->_em->getRepository($this->scheduleClass)->findByProfessor($professor,$division , $establishment);

        $result = array();
        foreach ($this->_days as $key => $day) {
            $resultSession = $this->getAllScheduleByDay($schedules,$key,$sessions);
            // push the result into the array
            array_push($result,array("day" => $day ,"sessions" =>$resultSession));
        }
        return array("schedules" => $result , "sessions" => $sessions);
    }

    /**
    * insert note in the database
    * @param Exam $exam
    * @param User $user
    */
    public function addNote($exam, $user = null)
    {
        if (is_null($user)) {
            throw new \Exception("Error No User Found", 1);
        }
        $establishment = $user->getEstablishment()->getId();
        $this->_em->beginTransaction();
        $students = $this->_em->getRepository($this->studentClass)->findBy(array('section' => $exam->getSection() , 'establishment' => $establishment));
        foreach ($students as $student) {
            $found = $this->_em->getRepository($this->noteClass)->findBy(array('student' => $student , 'exam' => $exam));
            if (!empty($found)) {
              continue;
            }
            $note = new $this->noteClass ();
            $note->setExam($exam);
            $note->setUser($user);
            $note->setStudent($student);
            $this->_em->persist($note);
        }
        $this->_em->flush();
        $this->_em->commit();
    }

    /**
    * @param Student $section
    * @param Division $division
    * @param Establishment $establishment
    * @return array
    */
    public function getSchedule($section ,$division , $establishment)
    {
        // get All the sessions
        $sessions = $this->_em->getRepository($this->sessionClass)->findAllByStartTime($establishment->getId());
        // get schedule by the section of the student
        $schedules = $this->_em->getRepository($this->scheduleClass)->findBySectionAndDivisionAndEstablishment($section,$division , $establishment);
        //die(var_dump($schedules))
        $result = array();
        foreach ($this->_days as $key => $day) {
            $resultSession = $this->getAllScheduleByDay($schedules,$key,$sessions);

            // push the result into the array
            array_push($result,array("day_key"=> $key , "day" => $day ,"sessions" =>$resultSession));
        }
        return array("schedules" => $result , "sessions" => $sessions);
    }

    public function getAllScheduleByDay($schedules, $day,$sessions)
    {
        $colspan = 1;
        $resultSession = array();
        // put the initial pointer to 2nd position
        $nextIterator = new \ArrayIterator($sessions);
        $nextIterator->rewind();
        $nextIterator->next();

        foreach ($sessions as $session) {
            $resultSchedule = array();
            $schedule = array_filter($schedules, function ($value) use ($day, $session) {
              return strcasecmp($value['day'],$day) == 0 && in_array($session->getId(),explode(", ", $value['sessionIDS']));
            });
            // test if the selected session existe in the schedule
            if (!empty($schedule)){
                $next = $nextIterator->current();
                if (!is_null($next)){
                  $nextSchedule = array_filter($schedules, function ($value) use ($day, $next) {
                    return strcasecmp($value['day'],$day) == 0 && in_array($next->getId(),explode(", ", $value['sessionIDS']));
                  });
                  if (!empty($nextSchedule) && strcasecmp(reset($nextSchedule)["scheduleId"], reset($schedule)["scheduleId"]) == 0){
                      $colspan += 1;
                      $nextIterator->next();
                      continue;
                  }
                }
                $resultSchedule["colspan"] = $colspan;
                $resultSchedule["empty"] = false;
                $resultSchedule["courseName"] = reset($schedule)["courseName"];
                $resultSchedule["scheduleId"] = reset($schedule)["scheduleId"];
                $resultSchedule["sectionName"] = reset($schedule)["sectionName"];
                $resultSchedule["courseID"] = reset($schedule)["courseID"];
                $resultSchedule["sessionIDS"] = reset($schedule)["sessionIDS"];
                $resultSchedule["professorID"] = reset($schedule)["professorID"];
                $resultSchedule["professor"] = reset($schedule)["name"];

                $colspan = 1;
            }else{
                $resultSchedule["empty"] = true;
            }
            $nextIterator->next();
            // push the result into the array
            array_push($resultSession, $resultSchedule);
        }
        return $resultSession;
    }

    /**
    * @param Student $section
    * @param Division $division
    * @param Establishment $establishment
    * @return array
    */
    public function getExams($section ,$division , $establishment){
        // get All the type Exams
        $typeExams = $this->_em->getRepository($this->examTypeClass)->findBy(array('establishment' => $establishment));
        // get All the Course by the garde of the student
        $courses = $this->_em->getRepository($this->courseClass)->findBy(array('grade' => $section->getGrade(),'division' => $division , 'establishment' => $establishment));
        $exams = $this->_em->getRepository($this->examClass)->findBySectionAndDivisionAndEstablishment($section, $division ,$establishment);
        // init the result value
        $result = array();
        // fetch All the Course
        foreach ($courses as $course) {
            $resultTypeExam = array();
            // fetch All the type Exams
            foreach ($typeExams as $typeExam) {
                $resultExam = array_filter($exams, function($value) use ($course, &$typeExam) { return strcasecmp($value['courseID'],$course->getId()) == 0 && strcmp($typeExam->getId(),$value['typeExamID']) == 0;});

                // push the result into the array
                array_push($resultTypeExam, array( "exams" => $resultExam , "typeExamID" => $typeExam->getId() ));
            }
            // push the result into the array
            array_push($result,array("courseID" => $course->getId() , "courseName" => $course->getCourseName() , "coefficient" => $course->getCoefficient() ,"typeExams" => $resultTypeExam));
        }
        return array("marks" => $result , "typeExams" => $typeExams);
    }

    /**
    * get Registred student
    *
    * @param SMS\AdministrativeBundle\Form\SearchType $form
    * @param SMS\EstablishmentBundle\Entity\Establishment $establishment
    */
    public function getAllStudents($form , $establishment)
    {
      $query = $this->_em->getRepository($this->studentClass)->findAllByEstablishment($establishment->getId());
      if ($form->isSubmitted()) {
          if (!empty($form->get('textField')->getData())){
            $query->andWhere('student.firstName like :search OR student.phone LIKE :search OR student.lastName LIKE :search OR student.email LIKE :search')
                  ->setParameter('search', '%'.$form->get('textField')->getData().'%');
          }
          if ($form->has('garde') && !is_null($form->get('garde')->getData())){
            $query->join('student.section' , 'section')
                  ->join('section.grade' , 'grade')
                  ->andWhere('grade.id = :grade')
                  ->setParameter('grade', $form->get('grade')->getData()->getId());
          }
          if ($form->has('section') && !is_null($form->get('section')->getData())){
            $query->andWhere('section.id = :section')
                  ->setParameter('section', $form->get('section')->getData()->getId());
          }
      }
      return $query->getQuery();
    }

    /**
    * @param Student $student
    * @param Division  $division
    *
    * @return array
    */
    public function getNotes($student ,$division){
      // get All the type Exams
      $establishment = $student->getEstablishment();
      $section = $student->getSection();
      $typeExams = $this->_em->getRepository($this->examTypeClass)->findBy(array('establishment' => $establishment));
      // get All the Course by the garde of the student
      $courses = $this->_em->getRepository($this->courseClass)->findBy(array('grade' => $section->getGrade(),'division' => $division , 'establishment' => $establishment));
      $exams = $this->_em->getRepository($this->examClass)->findStudentMark($student ,$section, $division ,$establishment);

      // init the result value
      $result = array();
      // fetch All the Course
      foreach ($courses as $course) {
          $resultTypeExam = array();
          // fetch All the type Exams
          foreach ($typeExams as $typeExam) {
              $resultExam = array_filter($exams, function($value) use ($course, &$typeExam) { return strcasecmp($value['courseID'],$course->getId()) == 0 && strcmp($typeExam->getId(),$value['typeExamID']) == 0;});

              // push the result into the array
              array_push($resultTypeExam, array( "exams" => $resultExam , "typeExamID" => $typeExam->getId() ));
          }
          // push the result into the array
          array_push($result,array("courseID" => $course->getId() , "courseName" => $course->getCourseName() , "coefficient" => $course->getCoefficient() ,"typeExams" => $resultTypeExam));
      }
      return array("marks" => $result , "typeExams" => $typeExams);
    }

    /**
    * get Registred section
    *
    * @param SMS\AdministrativeBundle\Form\SearchType $form
    * @param SMS\EstablishmentBundle\Entity\Establishment $establishment
    */
    public function getAllGrades($form , $establishment)
    {
      $query = $this->_em->getRepository($this->gradeClass)->findByEstablishment($establishment);
      if ($form->isSubmitted()) {
          if (!empty($form->get('textField')->getData())){
            $query->andWhere('grade.code like :search OR grade.gradeName LIKE :search')
                  ->setParameter('search', '%'.$form->get('textField')->getData().'%');
          }
      }
      return $query->getQuery();

    }

}
