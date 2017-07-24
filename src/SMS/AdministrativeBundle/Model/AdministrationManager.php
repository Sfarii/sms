<?php

namespace SMS\AdministrativeBundle\Model;

use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Services
 */

class AdministrationManager
{
    /**
    * @var EntityManager
    */
    private $_em;

    /**
    * @var $translator
    *
    */
    private $_translator;

    /**
     * @var array
     */
    private $_days;

    /**
     * @var String Class Names
     */
    private $studentClass;
    private $sessionClass;
    private $scheduleClass;
    private $courseClass;
    private $sectionClass;
    private $professorClass;
    private $attendanceStudentClass;
    private $attendanceProfessorClass;
    private $attendanceProfessorStatus;
    private $attendanceStudentStatus;

    /**
    * @param Doctrine\ORM\EntityManager $em
    * @param int $limitPerPage
    */
    public function __construct(EntityManager $em , DataCollectorTranslator $_translator)
    {
        $this->_em = $em;
        $this->_translator = $_translator;
    }
    /**
    * @param array $attendanceStudentStatus
    */
    public function setAttendanceStudentStatus($attendanceStudentStatus)
    {
      $this->attendanceStudentStatus = $attendanceStudentStatus;
    }

    /**
    * @param array $attendanceProfessorStatus
    */
    public function setAttendanceProfessorStatus($attendanceProfessorStatus)
    {
      $this->attendanceProfessorStatus = $attendanceProfessorStatus;
    }

    /**
    * @param String $courseClass
    */
    public function setCourseClass($courseClass)
    {
      $this->courseClass = $courseClass;
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
    * @param array $days
    */
    public function setDays(array $days)
    {
        $this->_days = $days;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setAttendanceStudent($attendanceStudentClass)
    {
      $this->attendanceStudentClass = $attendanceStudentClass;
    }

    /**
    * @param String $attendanceProfessorClass
    */
    public function setAttendanceProfessor($attendanceProfessorClass)
    {
      $this->attendanceProfessorClass = $attendanceProfessorClass;
    }

    /**
    * get Registred professor
    *
    * @param SMS\AdministrativeBundle\Form\SearchType $form
    * @param SMS\EstablishmentBundle\Entity\Establishment $establishment
    */
    public function getAllProfessors($form , $establishment)
    {
      $query = $this->_em->getRepository($this->professorClass)->findAllByEstablishment($establishment);
      if ($form->isSubmitted()) {
          if (!empty($form->get('textField')->getData())){
            $query->andWhere('professor.firstName like :search OR professor.phone LIKE :search OR professor.lastName LIKE :search OR professor.email LIKE :search')
                  ->setParameter('search', '%'.$form->get('textField')->getData().'%');
          }
      }
      return $query->getQuery();

    }

    /**
    * get Registred professor
    *
    * @param SMS\AdministrativeBundle\Form\SearchType $form
    * @param SMS\EstablishmentBundle\Entity\Establishment $establishment
    */
    public function getAllSections($form , $establishment)
    {
      $query = $this->_em->getRepository($this->sectionClass)->findAllByEstablishment($establishment);
      if ($form->isSubmitted()) {
          if (!empty($form->get('textField')->getData())){
            $query->andWhere('section.sectionName like :search OR grade.gradeName LIKE :search')
                  ->setParameter('search', '%'.$form->get('textField')->getData().'%');
          }
      }
      return $query->getQuery();

    }

    /**
    * get all attendances students
    *
    * @param SMS\AdministrativeBundle\Form\SearchType $form
    * @param SMS\EstablishmentBundle\Entity\Section $section
    */
    public function getAllSectionAttendances($form ,$section)
    {
      $query = $this->_em->getRepository($this->attendanceStudentClass)->findBySection($section);
      if ($form->isSubmitted()) {
          if (!empty($form->get('startDate')->getData()) && !empty($form->get('endDate')->getData()) && $form->get('startDate')->getData() < $form->get('endDate')->getData()){
            $query->andWhere('attendance.date BETWEEN :startDate AND :endDate')
                  ->setParameter('startDate', $form->get('startDate')->getData()->format('Y-m-d'))
                  ->setParameter('endDate', $form->get('endDate')->getData()->format('Y-m-d'));
          }
          if (!$form->get('course')->getData()->isEmpty()){
            $query->andWhere('course in (:courses)')
                  ->setParameter('courses', $form->get('course')->getData());
          }
          if (!$form->get('session')->getData()->isEmpty()){
            $query->andWhere('session in (:session)')
                  ->setParameter('session', $form->get('session')->getData());
          }
      }
      return $query->getQuery();
    }

    /**
    * get all attendances students
    *
    * @param SMS\AdministrativeBundle\Form\SearchType $form
    * @param SMS\EstablishmentBundle\Entity\Section $section
    */
    public function getAllSectionStats($form ,$section)
    {
      $queryStats = $this->_em->getRepository($this->attendanceStudentClass)->findStatsBySection($section);
      if ($form->isSubmitted()) {
          if (!empty($form->get('startDate')->getData()) && !empty($form->get('endDate')->getData())){
            $queryStats->andWhere('attendance.date BETWEEN :startDate AND :endDate')
                  ->setParameter('startDate', $form->get('startDate')->getData()->format('Y-m-d'))
                  ->setParameter('endDate', $form->get('endDate')->getData()->format('Y-m-d'));
          }
          if (!$form->get('course')->getData()->isEmpty()){
            $queryStats->andWhere('course in (:courses)')
                  ->setParameter('courses', $form->get('course')->getData());
          }
          if (!$form->get('session')->getData()->isEmpty()){
            $queryStats->andWhere('session in (:session)')
                  ->setParameter('session', $form->get('session')->getData());
          }
      }
      return $this->echartsPieFormat($queryStats->getQuery()->getResult());
    }

    public function echartsPieFormat( $resultattendance)
    {
      $status = array();
      foreach ($resultattendance as $value) {
          $status[] = array('name' => $this->_translator->trans($this->attendanceStudentStatus[$value['name']]) , 'value' => $value['value']);
      }
      return $status;
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
        $this->_em->beginTransaction();
        foreach ($choices as $choice) {
            $object = $repository->find($choice['value']);


            try {
                if ($object) {
                    $this->_em->remove($object);
                }
            } catch (Exception $e) {
                throw new Exception("Error this Entity has child ", 1);
            }
        }
        $this->_em->flush();
        $this->_em->commit();
    }

    /**
    * update multiple entity from the database
    * @param String $className
    * @param array $choices
    * @param String $status
    */
    public function updateAll($className, $choices = array(),$status)
    {
        $repository = $this->_em->getRepository($className);
        $this->_em->beginTransaction();
        foreach ($choices as $choice) {
            $object = $repository->find($choice['value']);
            $object->setStatus($status);
        }
        $this->_em->flush();
        $this->_em->commit();
    }

    /**
    * @param professor $professor
    * @param Division $division
    *
    * @return array
    */
    public function getScheduleByProfessor($professor ,$division, $date , $establishment)
    {
        // get All Sessions
        $sessions = $this->_em->getRepository($this->sessionClass)->findAllByStartTime($establishment->getId());
        // get schedule by the professor
        $schedules = $this->_em->getRepository($this->scheduleClass)->findByProfessor($professor,$division , $establishment);

        $result = array();
        $resultSession = $this->getAllScheduleByDay($schedules,$date->format('l'),$sessions);
        // push the result into the array
        array_push($result,array("day" => $this->_days[$date->format('l')] ,"sessions" =>$resultSession));
        return array("schedules" => $result , "sessions" => $sessions);
    }

    /**
    * @param Student $section
    * @param Division $division
    * @return array
    */
    public function getSchedule($section ,$division, $date , $establishment)
    {
        // get All the sessions
        $sessions = $this->_em->getRepository($this->sessionClass)->findAllByStartTime($establishment->getId());
        // get schedule by the section of the student
        $schedules = $this->_em->getRepository($this->scheduleClass)->findBySectionAndDivisionAndEstablishmentAndDay($section,$division , $establishment,$date->format('l'));
        //die(var_dump($schedules))
        $result = array();
        $resultSession = $this->getAllScheduleByDay($schedules,$date->format('l'),$sessions);
        // push the result into the array
        array_push($result,array("day" => $this->_days[$date->format('l')] ,"sessions" =>$resultSession));
        return array("schedules" => $result , "sessions" => $sessions);
    }

    public function validateStudentAttendance($data , $user = null)
    {
      if (is_null($user) || is_null($data["division"]) || is_null($data["course"]) || is_null($data["date"]) || is_null($data["session"])  || is_null($data["section"]) || empty($data["status"])) {
          return false;
      }
      $date = new \DateTime($data["date"]);
      $sessionObject = $this->_em->getRepository($this->sessionClass)->find($data["session"]);
      $sectionObject = $this->_em->getRepository($this->sectionClass)->find($data["section"]);
      $courseObject = $this->_em->getRepository($this->courseClass)->find($data["course"]);
      // Get the day from the date object
      $day = mb_convert_case(date("l", strtotime($data["date"])), MB_CASE_LOWER, "UTF-8");
      // Fetch the session from specified date
      $sessions = $this->_em->getRepository($this->sessionClass)->findBySectionAndDateAndDivision($data["section"],$day, $data["division"] , $user->getEstablishment()->getId());
      $result = array_filter($sessions, function ($value) use ( $sessionObject) {return strcasecmp($sessionObject->getId(),$value->getId()) == 0 ;});
      if (empty($result) || is_null( $sessionObject)  || is_null($sectionObject) || is_null($courseObject) ) {
          return false;
      }
      return true;
    }

    public function getStatsProfessor($professor)
    {
        $data = $this->_em->getRepository($this->attendanceProfessorClass)->findStatsByProfessor($professor);
        $months = array_unique(array_map(function ($value){return $value['month'];}, $data));
        $stats = array();
        foreach ($months as $month) {
          foreach ($this->attendanceProfessorStatus as $key => $value) {
              $result = array_filter($data , function ($value) use ($key , $month) {return strcasecmp($value['name'],$key) == 0 && strcasecmp($value['month'],$month) == 0;});
              $stats[$month][$key] = intval(reset($result)['value']);
          }
          $stats[$month]['month'] = $this->_translator->trans($month);
        }
        return $stats;
    }

    /**
    * insert student attendance in the database
    * @param Form $form
    * @param User $user
    */
    public function addStudentAttendance($data , $user = null)
    {
        $sessionObject = $this->_em->getRepository($this->sessionClass)->find($data["session"]);
        $sectionObject = $this->_em->getRepository($this->sectionClass)->find($data["section"]);
        $courseObject = $this->_em->getRepository($this->courseClass)->find($data["course"]);

        if (is_null($user)  || is_null($sessionObject)  || is_null($sectionObject)) {
            throw new Exception("Error send Data ", 1);
        }

        $date = new \DateTime($data["date"]);
        $establishment = $user->getEstablishment()->getId();

        $found = $this->_em->getRepository($this->attendanceStudentClass)->findByDateAndSessionAndSection($date, $sessionObject->getId(), $sectionObject->getId());
        $students = $this->_em->getRepository($this->studentClass)->findBySectionAndEstablishment($sectionObject->getId() , $establishment);
        if (!is_null($found) && $found['countStudents'] == count($students) ) {
            return false;
        }
        $students = $this->_em
                            ->getRepository($this->studentClass)
                            ->findBySectionAndEstablishment($sectionObject->getId() , $establishment);

        $this->_em->beginTransaction();
        foreach ($students as $student) {
            $found = $this->_em->getRepository($this->attendanceStudentClass)->findOneBy(array('date' => $date , 'session' => $sessionObject->getId() , 'student' =>  $student->getId()));
            if (!empty($found)){
              continue;
            }
            $attendance = new $this->attendanceStudentClass ();
            $attendance->setSession($sessionObject);
            $attendance->setDate($date);
            $attendance->setStudent($student);
            $attendance->setCourse($courseObject);
            $attendance->setUser($user);
            $attendance->setStatus($data["status"]);
            $this->_em->persist($attendance);
        }
        $this->_em->flush();
        $this->_em->commit();
        return true;
    }

    /**
    * insert Attendance Professor in the database
    * @param AttendanceProfessor $attendance
    * @param User $user
    */
    public function addProfessorAttendance($data , $user = null)
    {
        if (is_null($user) || is_null($data["date"]) || is_null($data["course"]) || is_null($data["session"]) || is_null($data["professor"])  || is_null($data["division"]) || empty($data["status"])) {
            return false;
        }
        $sessionObject = $this->_em->getRepository($this->sessionClass)->find($data["session"]);
        $professorObject = $this->_em->getRepository($this->professorClass)->find($data["professor"]);
        $courseObject = $this->_em->getRepository($this->courseClass)->find($data["course"]);
        $date = new \DateTime($data["date"]);

        if (is_null($date) || is_null($sessionObject)  || is_null($professorObject)) {
            return false;
        }
        $found = $this->_em->getRepository($this->attendanceProfessorClass)->findByDateAndSessionAndUser($professorObject , $date , $sessionObject);

        if (!is_null($found)) {
            if ($found->getStatus() !== $data["status"]) {
                $found->setStatus($data["status"]);
            }
            $this->update($found);
        } else {
            $attendance = new $this->attendanceProfessorClass ();
            $attendance->setSession($sessionObject);
            $attendance->setDate($date);
            $attendance->setProfessor($professorObject);
            $attendance->setUser($user);
            $attendance->setCourse($courseObject);
            $attendance->setStatus($data["status"]);
            $this->insert($attendance, $user);
        }
        return true;
    }

    public function getAllScheduleByDay($schedules, $day,$sessions)
    {
        $resultSession = array();
        foreach ($sessions as $session) {
            $resultSchedule = array();
            $schedule = array_filter($schedules, function ($value) use ($day, $session) {
              return strcasecmp($value['day'],$day) == 0 && in_array($session->getId(),explode(", ", $value['sessionIDS']));
            });
            // test if the selected session existe in the schedule
            if (!empty($schedule)){

                $resultSchedule["colspan"] = 1;
                $resultSchedule["empty"] = false;
                $resultSchedule["sessionID"] = $session->getId();
                $resultSchedule["courseName"] = reset($schedule)["courseName"];
                $resultSchedule["courseID"] = reset($schedule)["courseID"];
                $resultSchedule["sectionID"] = reset($schedule)["sectionID"];
                $resultSchedule["sectionName"] = reset($schedule)["sectionName"];
                $resultSchedule["coefficient"] = reset($schedule)["coefficient"];
                $resultSchedule["professor"] = reset($schedule)["name"];
            }else{
                $resultSchedule["empty"] = true;
            }
            // push the result into the array
            array_push($resultSession, $resultSchedule);
        }
        return $resultSession;
    }

}
