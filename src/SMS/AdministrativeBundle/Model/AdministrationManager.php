<?php

namespace SMS\AdministrativeBundle\Model;

use Doctrine\ORM\EntityManager;
use Exception;
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
    protected $professorClass;
    protected $attendanceStudentClass;
    protected $attendanceProfessorClass;

    /**
    * @param Doctrine\ORM\EntityManager $em
    * @param int $limitPerPage
    */
    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
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
            } catch (Exception $e) {
                throw new Exception("Error this Entity has child ", 1);
            }
        }

        $this->_em->flush();
    }

    /**
    * @param professor $professor
    * @param Division $division
    *
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
    * @param Student $section
    * @param Division $division
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
            array_push($result,array("day" => $day ,"sessions" =>$resultSession));
        }
        return array("schedules" => $result , "sessions" => $sessions);
    }

    public function validateStudentAttendance($data , $user = null)
    {
      if (is_null($user) || is_null($data["division"]) || is_null($data["date"]) || is_null($data["session"])  || is_null($data["section"]) || empty($data["status"])) {
          return false;
      }
      $date = new \DateTime($data["date"]);
      $sessionObject = $this->_em->getRepository($this->sessionClass)->find($data["session"]);
      $sectionObject = $this->_em->getRepository($this->sectionClass)->find($data["section"]);
      // Get the day from the date object
      $day = mb_convert_case(date("l", strtotime($data["date"])), MB_CASE_LOWER, "UTF-8");
      // Fetch the session from specified date
      $sessions = $this->_em->getRepository($this->sessionClass)->findBySectionAndDateAndDivision($data["section"],$day, $data["division"] , $user->getEstablishment()->getId());
      $result = array_filter($sessions, function ($value) use ( $sessionObject) {return strcasecmp($sessionObject->getId(),$value->getId()) == 0 ;});
      if (empty($result) || is_null( $sessionObject)  || is_null($sectionObject) ) {
          return false;
      }
      return true;
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
        if (is_null($user)  || is_null($sessionObject)  || is_null($sectionObject)) {
            throw new Exception("Error send Data ", 1);
        }

        $date = new \DateTime($data["date"]);
        $establishment = $user->getEstablishment()->getId();

        $found = $this
                    ->_em
                    ->getRepository($this->attendanceStudentClass)
                    ->findByDateAndSessionAndSection($date, $sessionObject->getId(), $sectionObject->getId());

        if (!empty($found["attendance_ids"])) {
            return array_map('intval', explode(",", $found["attendance_ids"]));
        }
        $students = $this->_em
                            ->getRepository($this->studentClass)
                            ->findBySectionAndEstablishment($sectionObject->getId() , $establishment);

        $attendance_ids = array();
        $this->_em->beginTransaction();
        foreach ($students as $student) {
            $attendance = new $this->attendanceStudentClass ();
            $attendance->setSession($sessionObject);
            $attendance->setDate($date);
            $attendance->setStudent($student);
            $attendance->setUser($user);
            $attendance->setStatus($data["status"]);
            $this->_em->persist($attendance);
            $this->_em->flush();
            $attendance_ids[] = $attendance->getId();
            $this->_em->detach($attendance);
        }
        $this->_em->commit();
        return $attendance_ids;
    }

    /**
    * insert Attendance Professor in the database
    * @param AttendanceProfessor $attendance
    * @param User $user
    */
    public function addProfessorAttendance($data , $user = null)
    {
        if (is_null($user) || is_null($data["date"]) || is_null($data["session"]) || is_null($data["professor"])  || is_null($data["division"]) || empty($data["status"])) {
            return false;
        }
        $sessionObject = $this->_em->getRepository($this->sessionClass)->find($data["session"]);
        $professorObject = $this->_em->getRepository($this->professorClass)->find($data["professor"]);
        // Get the day from the date object
        $day = mb_convert_case(date("l", strtotime($data["date"])), MB_CASE_LOWER, "UTF-8");
        // Fetch the session from specified date
        $sessions = $this->_em->getRepository($this->sessionClass)->findByProfessorAndDateAndDivision($data["professor"],$day, $data["division"] , $user->getEstablishment()->getId());
        $result = array_filter($sessions, function ($value) use ( $sessionObject) {return strcasecmp($sessionObject->getId(),$value->getId()) == 0 ;});

        $date = new \DateTime($data["date"]);
        if (empty($result) || is_null($sessionObject)  || is_null($professorObject)) {
            return false;
        }

        $found = $this
                    ->_em
                    ->getRepository($this->attendanceProfessorClass)
                    ->findByDateAndSessionAndUser($professorObject , $date , $sessionObject);

        if (!is_null($found)) {
            if ($found->getStatus() !== $attendance->getStatus()) {
                $found->setStatus($data["status"]);
            }
            $this->update($found);
        } else {
            $attendance = new $this->attendanceProfessorClass ();
            $attendance->setSession($sessionObject);
            $attendance->setDate($date);
            $attendance->setProfessor($professorObject);
            $attendance->setUser($user);
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
