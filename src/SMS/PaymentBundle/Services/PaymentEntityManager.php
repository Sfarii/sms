<?php

namespace SMS\PaymentBundle\Services;

use Doctrine\ORM\EntityManager;
use SMS\PaymentBundle\Entity\Registration;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\PaymentBundle\Services
 */
class PaymentEntityManager
{
    /**
    * @var Doctrine\ORM\EntityManager
    */
    private $_em;

    /**
    * @var \Mailer
    */
    private $_mailer;

    /**
    * @param Doctrine\ORM\EntityManager $em
    */
    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
    * @param String $mailer
    */
    public function setMailer($mailer)
    {
        $this->_mailer = $mailer;
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

    public function addPayment($payment, $user = null)
    {
      if (!is_null($user)){
        $payment->setUser($user);
      }
      if ($payment->getPaymentType()->getPrice() < $payment->getPrice()) {
        return false;
      }

      $payment->setCredit($payment->getPaymentType()->getPrice() - $payment->getPrice());
      $this->_em->persist($payment);
      $this->_em->flush($payment);

      $this->_mailer->sendPaymentEmail($user , $payment);

      return true;
    }

    /**
    * add multiple entity to the database
    *
    * @param String $className
    * @param array $data
    */
    public function newRegistration($className , $data = array())
    {
      $repository = $this->_em->getRepository($className);
      $registrationRepository = $this->_em->getRepository(Registration::class);

      $this->_em->beginTransaction();
      foreach ($data['students'] as $choice) {
          $student = $repository->find($choice['value']);
          $registration = $registrationRepository->findOneBy(array('establishment' => $data['user']->getEstablishment() ,'student' => $student , 'paymentType' => $data['paymentType'] ));

          if (!empty($registration)){
            $registration->setRegistered(true)
                          ->addMonths($data['months']);
            $this->_em->flush($registration);
            continue;
          }
          $registration = new Registration();
          $registration->setStudent($student)
                        ->setPaymentType($data['paymentType'])
                        ->setEstablishment($data['user']->getEstablishment())
                        ->setMonths($data['months'])
                        ->setStudent($student)
                        ->setUser($data['user']);
          $this->_em->persist($registration);
      }
      $this->_em->flush();
      $this->_em->commit();
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
    *
    * @param String $className
    * @param array $choices
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
    * insert note in the database
    * @param Form $form
    * @param User $user
    */
    public function addNote(Exam $exam, $user = null)
    {
        if (is_null($user)) {
            throw new Exception("Error No User Found", 1);
        }
        $establishment = $user->getEstablishment()->getId();
        $found = $this
                    ->_em
                    ->getRepository(Note::class)
                    ->findByExam($exam);
        if (is_null($found)) {
          $this->_em->beginTransaction();
          foreach ($exam->getCourse()->getGrade()->getSections() as $key => $section) {

            $students = $this
                            ->_em
                            ->getRepository(Student::class)
                            ->findBySectionAndEstablishment($section->getId() , $establishment);

          foreach ($students as $student) {
              $note = new Note();
              $note->setExam($exam);
              $note->setUser($user);
              $note->setStudent($student);
              $this->_em->persist($note);
              $this->_em->flush();
              $this->_em->detach($note);
          }
        }
        $this->_em->commit();
      }

    }

    /**
    * insert student attendance in the database
    * @param Form $form
    * @param User $user
    */
    public function addStudentAttendance($form,User $user = null)
    {
        if (is_null($user)) {
            throw new Exception("Error No User Found", 1);
        }

        $establishment = $user->getEstablishment()->getId();

        $section = $form->get('section')->getData();
        $date = $form->get('date')->getData();
        $session = $form->get('session')->getData();

        $found = $this
                    ->_em
                    ->getRepository(AttendanceStudent::class)
                    ->findByDateAndSessionAndSection($date, $session, $section);
        if (!empty($found["attendance_ids"])) {
            return array_map('intval', explode(",", $found["attendance_ids"]));
        }
        $students = $this->_em
                            ->getRepository(Student::class)
                            ->findBySectionAndEstablishment($section->getId() , $establishment);

        $attendance_ids = array();
        $this->_em->beginTransaction();
        foreach ($students as $student) {
            $attendance = new AttendanceStudent();
            $attendance->setSession($session);
            $attendance->setDate($date);
            $attendance->setStudent($student);
            $attendance->setUser($user);
            $attendance->setStatus($form->get("status")->getData());
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
    public function addProfessorAttendance($attendance, $user = null)
    {
        if (is_null($user)) {
            throw new Exception("Error No User Found", 1);
        }

        $found = $this
                    ->_em
                    ->getRepository(AttendanceProfessor::class)
                    ->findByDateAndSessionAndUser($attendance);

        if (!is_null($found)) {
            if ($found->getStatus() !== $attendance->getStatus()) {
                $found->setStatus($attendance->getStatus());
            }
            $this->update($found);
        } else {
            $this->insert($attendance, $user);
        }
    }

    /**
     * @param String $sessionName
     * @return void
     */
    public function setSessionName($sessionName)
    {
        $this->_sessionName = $sessionName;
    }
}
