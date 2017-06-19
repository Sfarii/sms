<?php

namespace API\Services;

use SMS\StudyPlanBundle\Entity\Schedule;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\StudyPlanBundle\Entity\Session;
use SMS\StudyPlanBundle\Entity\TypeExam;
use SMS\StudyPlanBundle\Entity\Note;
use SMS\AdministrativeBundle\Entity\AttendanceStudent;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Services
 */

class UserSpaceManager
{
  /**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $_em;

    /**
     * @var array
     */
    private $_days;

	/**
	 * @param Doctrine\ORM\EntityManager $em
	 */
	public function __construct($em)
    {
        $this->_em = $em;
    }

    /**
    * @param array $days
    */
    public function setDays(array $days)
    {
        $this->_days = $days;
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
            $schedule = $this->getOneScheduleByDay($schedules,$day,$session);
            // test if the selected session existe in the schedule
            if (!is_null($schedule)){
                $next = $nextIterator->current();
                $nextSchedule = !is_null($next) ? $this->getOneScheduleByDay($schedules,$day,$next) : null;
                if (!is_null($nextSchedule) && strcasecmp($nextSchedule["courseName"], $schedule["courseName"]) == 0){

                    $colspan += 1;
                    $nextIterator->next();
                    continue;
                }
                $resultSchedule["colspan"] = $colspan;
                $resultSchedule["empty"] = false;
                $resultSchedule["courseName"] = $schedule["courseName"];
                $resultSchedule["coefficient"] = $schedule["coefficient"];
                $resultSchedule["professor"] = $schedule["name"];

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
    * @return array
    */
    public function getSchedule($section ,$division)
    {
        // get All the sessions
        $sessions = $this->_em->getRepository(Session::class)->findAllByStartTime();
        // get schedule by the section of the student
        $schedules = $this->_em->getRepository(Schedule::class)->findBySection($section,$division);

        $result = array();
        foreach ($this->_days as $key => $day) {
            $resultSession = $this->getAllScheduleByDay($schedules,$key,$sessions);

            // push the result into the array
            array_push($result,array("day" => $day ,"sessions" =>$resultSession));
        }
        return array("schedules" => $result , "sessions" => $sessions);
    }

    /**
    * @param professor $professor
    * @param Division $division
    *
    * @return array
    */
    public function getScheduleByProfessor($professor ,$division)
    {
        // get All Sessions
        $sessions = $this->_em->getRepository(Session::class)->findAllByStartTime();
        // get schedule by the professor
        $schedules = $this->_em->getRepository(Schedule::class)->findByProfessor($professor,$division);

        $result = array();
        foreach ($this->_days as $key => $day) {
            $resultSession = $this->getAllScheduleByDay($schedules,$key,$sessions);
            // push the result into the array
            array_push($result,array("day" => $day ,"sessions" =>$resultSession));
        }
        return array("schedules" => $result , "sessions" => $sessions);
    }

    public function getOneScheduleByDay( $schedules,$day,$session)
    {

        foreach ($schedules as $value) {
            $sessions = explode(", ", $value['sessionIDS']);
            if (strcasecmp($value['day'],$day) == 0 && in_array($session->getId(),$sessions)){
                return $value;

            }
        }
        return null;
    }

    /**
    * @param Student $student
    * @param String  $CourseClassName
    * @param String  $noteClassName
    * @param String  $typeExamClassName
    *
    * @return array
    */
    public function getNotes($student ,$division){
        // get All the type Exams
        $typeExams = $this->_em->getRepository(TypeExam::class)->getTypeExamAll();
        // get All the Course by the garde of the student
        $courses = $this->_em->getRepository(Course::class)
                                ->findByGradeAndDivision($student->getSection()->getGrade()->getId(),$division->getId());
        $marks = $this->_em->getRepository(Course::class)
                          ->findByStudent($student,$division);
        // init the result value
        $result = array();
        // fetch All the Course
        foreach ($courses as $course) {
            $resultTypeExam = array();
            // fetch All the type Exams
            foreach ($typeExams as $typeExam) {
                $resultMark = array_filter($marks, function($value) use ($course, &$typeExam) { return strcasecmp($value['courseName'],$course->getCourseName()) == 0 && strcmp($typeExam['typeExamName'],$value['typeExamName']) == 0;});

                // push the result into the array
                array_push($resultTypeExam, array( "exams" => $resultMark ));
            }
            // push the result into the array
            array_push($result,array("courseName" => $course->getCourseName() , "coefficient" => $course->getCoefficient() ,"typeExams" => $resultTypeExam));
        }
        //die(var_dump($result));
        return array("marks" => $result , "typeExams" => $typeExams);
    }

    /**
    * @param Student $student
    * @param String $scheduleClassName
    * @param String $sessionClassName
    *
    * @return array
    */
    public function getAttendanceOfStudent($student ,$division)
    {
        // get All the sessions
        $sessions = $this->_em->getRepository(Session::class)->findAllByStartTime();
        // get attendance by the section of the student
        $attendanceRepository = $this->_em->getRepository(AttendanceStudent::class);
        $attendance = $attendanceRepository->findByStudent($student, $division->getStartDate() , $division->getEndDate());
        $result = array();
        foreach ($this->_days as $key => $day) {
            $resultSession = $this->getAllAttendanceByDay($attendance,$key,$sessions);
            // push the result into the array
            array_push($result,array("day" => $day ,"sessions" =>$resultSession));
        }
        $attendanceStats = $attendanceRepository->findStatsByStudent($student, $division->getStartDate() , $division->getEndDate());

        return array("attendances" => $result , "sessions" => $sessions , "stats" => $this->echartsPieFormat( $attendanceStats));
    }

    public function echartsPieFormat( $resultattendance)
    {
      $status = array();
      foreach ($resultattendance as $value) {
          $status[] = array('name' => $value['name'] , 'value' => $value['value']);
      }
      return $status;
    }

    public function getAllAttendanceByDay($attendances,$day,$sessions)
    {
      $resultSession = array();
      foreach ($sessions as $session) {
          $resultattendance = array_filter($attendances, function($value) use ($day, &$session) { return strcasecmp($value['day'],$day) == 0 && strcmp($session->getId(),$value['sessionId']) == 0;});
          // test if the selected session existe in the attendance
          if (!empty($resultattendance)){
              $resultattendance["status"] =$this->echartsPieFormat( $resultattendance);
              $resultattendance["empty"] = false;
          }else{
              $resultattendance["empty"] = true;
          }
          // push the result into the array
          array_push($resultSession, (array) $resultattendance);
      }
      return $resultSession;
    }

    /**
    * @param Student $student
    * @param String $scheduleClassName
    * @param String $sessionClassName
    *
    * @return array
    */
    public function getAttendanceOfStudentByCourses($student ,$division)
    {
      // get All the Course by the garde of the student
      $courses = $this->_em->getRepository(Course::class)
                              ->findByGradeAndDivision($student->getSection()->getGrade()->getId(),$division->getId());
        // get attendance by the section of the student
        $attendanceRepository = $this->_em->getRepository(AttendanceStudent::class);
        $attendances = $attendanceRepository->findByStudentGroupByCourse($student, $division);
        $result = array();
        foreach ($courses as $course) {
          $resultattendance = array();
          $attendance = array_filter($attendances, function($value) use (&$course) { return strcasecmp($value['courseName'],$course->getCourseName()) == 0; });
          // test if the selected session existe in the attendance
          $resultattendance["courseName"] = $course->getCourseName();
          if (!empty($this->echartsPieFormat( $attendance))){
              $resultattendance["status"] =$this->echartsPieFormat( $attendance);
              $resultattendance["empty"] = false;
          }else{
              $resultattendance["empty"] = true;
          }
            // push the result into the array $course
            array_push($result,$resultattendance );
        }
        // get Absence by Student
        $attendanceStats = $attendanceRepository->findStatsByStudent($student, $division);
        // get Absence By Section
        $attendanceSectionStats = $attendanceRepository->findStatsBySection($student->getSection(), $division);

        return array("attendances" => $result , "stats" => $this->echartsPieFormat( $attendanceStats) , "sectionStats" => $this->echartsPieFormat( $attendanceSectionStats));
    }
}
