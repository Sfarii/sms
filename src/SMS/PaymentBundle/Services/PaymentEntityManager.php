<?php

namespace SMS\PaymentBundle\Services;

use Doctrine\ORM\EntityManager;

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
    * @var Symfony\Component\Translation\DataCollectorTranslator
    */
    private $_translator;

    /**
    * @var \Mailer
    */
    private $_mailer;

    /**
    * @var Array
    */
    private $_days;

    /**
    * @var string
    */
    private $_catchUpLessonSechduleClass;
    private $_studentClass;
    private $_paymentClass;

    /**
    * @param Doctrine\ORM\EntityManager $em
    * @param Symfony\Component\Translation\DataCollectorTranslator $_translator
    */
    public function __construct(EntityManager $em, $_translator)
    {
        $this->_em = $em;
        $this->_translator = $_translator;
    }

    /**
    * @param String $mailer
    */
    public function setMailer($mailer)
    {
        $this->_mailer = $mailer;
    }


    /**
    * @param SMS/PaymentBundle/Entity/Payment $paymentClass
    */
    public function setPaymentClass($paymentClass)
    {
        $this->_paymentClass = $paymentClass;
    }

    /**
    * @param SMS/UserBundle/Entity/Student $studentClass
    */
    public function setStudentClass($studentClass)
    {
        $this->_studentClass = $studentClass;
    }

    /**
    * @param Array $days
    */
    public function setDays($days)
    {
        $this->_days = $days;
    }

    /**
    * @param String $catchUpLessonSechdule
    */
    public function setCatchUpLessonSechduleClass($catchUpLessonSechdule)
    {
        $this->_catchUpLessonSechduleClass = $catchUpLessonSechdule;
    }

    /**
    * insert entity in the database
    * @param Object $object
    * @param User $user
    */
    public function insert($object, $user = null)
    {
        if (!is_null($user)){
          $object->setAuthor($user);
        }
        $this->_em->persist($object);
        $this->_em->flush($object);
    }

    /**
    * new Payment with ajax
    * @return bool
    */
    public function newPayment($paymentType , $price , $month, $student , $user)
    {
      $payment = $this->_em->getRepository($this->_paymentClass)->findOneBy(array('month' => $month , 'student' => $student , 'paymentType' => $paymentType ));
      if (is_null($payment)){
        if ($paymentType->getPrice() < $price || $price < 0 ){
          return false;
        }
        $payment = new $this->_paymentClass ();
        $payment->setPrice($price)
        ->setCredit($paymentType->getPrice() - $payment->getPrice())
        ->setMonth($month)
        ->setAuthor($user)
        ->setPaymentType($paymentType)
        ->setReference($this->getUniqueValue())
        ->setStudent($student);
        $this->_em->persist($payment);
        $this->_em->flush($payment);
      }else{
        if ($paymentType->getPrice() < ($price + $payment->getPrice()) || ($price + $payment->getPrice()) < 0 || ($paymentType->getPrice() - ($price + $payment->getPrice()) ) < 0 ){
          return false;
        }
        $payment->setPrice($price + $payment->getPrice())
                ->setCredit($paymentType->getPrice() - $payment->getPrice());
        $this->_em->flush($payment);
      }
      return true;
    }

    /**
    * addPayment with form
    * @return bool
    */
    public function addPayment($newPayment, $user = null)
    {
      $payment = $this->_em->getRepository($this->_paymentClass)->findOneBy(array('month' => $newPayment->getMonth() , 'student' => $newPayment->getStudent() , 'paymentType' => $newPayment->getPaymentType() ));
      if (is_null($payment)){
        if ($newPayment->getPaymentType()->getPrice() < $newPayment->getPrice() || $newPayment->getPrice() < 0 ){
          return false;
        }
        $newPayment->setReference($this->getUniqueValue());
        $newPayment->setCredit($newPayment->getPaymentType()->getPrice() - $newPayment->getPrice());
        $this->_em->persist($newPayment);
        $this->_em->flush($newPayment);
      }else{
        if ($newPayment->getPaymentType()->getPrice() < ($newPayment->getPrice() + $payment->getPrice()) || ($newPayment->getPrice() + $payment->getPrice()) < 0 || ($newPayment->getPaymentType()->getPrice() - ($newPayment->getPrice() + $payment->getPrice())) < 0 ){
          return false;
        }
        $payment->setPrice($newPayment->getPrice() + $payment->getPrice())
                ->setCredit($newPayment->getPaymentType()->getPrice() - $payment->getPrice());
        $this->_em->flush($payment);
      }
      return true;
    }

    /**
    * get reference
    * @return string $reference
    */
    public function getUniqueValue()
    {
      do {
          $reference = mb_convert_case(bin2hex(random_bytes(10)), MB_CASE_UPPER, "UTF-8");
          if (is_null($this->_em->getRepository($this->_paymentClass)->findOneBy(array('reference' => $reference )))) {
              return $reference ;
          }
      } while (true);
    }

    /**
    * update entity in the database
    * @param Object $payment
    */
    public function updatePayment($payment)
    {
        if ($payment->getPaymentType()->getPrice() < $payment->getPrice()) {
          return false;
        }

        $payment->setCredit($payment->getPaymentType()->getPrice() - $payment->getPrice());
        $this->_em->flush($payment);
    }

    /**
    * get Registred Student
    *
    * @param String $className
    * @param SMS\PaymentBundle\Form\SearchType $form
    * @param SMS\EstablishmentBundle\Entity\Establishment $establishment
    */
    public function getRegistredStudent($form , $establishment)
    {
      $query = $this->_em->getRepository($this->_studentClass)->findAllRegistredStudentByEstablishment($establishment);
      if ($form->isSubmitted()) {
          if (!empty($form->get('textField')->getData())){
            $query->andWhere('student.firstName like :search OR student.phone LIKE :search OR student.lastName LIKE :search OR student.email LIKE :search')
                  ->setParameter('search', '%'.$form->get('textField')->getData().'%');
          }
          if (!empty($form->get('birthday')->getData())){
            $date = explode ( " - " , $form->get('birthday')->getData() );
            $query->andWhere('student.birthday BETWEEN :start_date AND :end_date')
                  ->setParameter('start_date', $date[0])
                  ->setParameter('end_date', $date[1]);
          }
          if (!empty($form->get('gender')->getData())){
            $query
                ->andWhere('student.gender = :gender')
                ->setParameter('gender', $form->get('gender')->getData());
          }

          if (strcasecmp ( $form->get('status')->getData() , 'intren' ) == 0) {
            $query->andWhere('student.studentType = TRUE');
          }

          if (strcasecmp ( $form->get('status')->getData() , 'extern' ) == 0) {
            $query->andWhere('student.studentType = FALSE');
          }

      }
      return $query->getQuery();

    }

    /**
    * update CatchUpLesson Schedule before add
    * @param  array $catchUpLessonSchedule
    * @param  array $data
    */
    public function updateCatchUpLessonScheduleLineSession($catchUpLessonSchedule , $data)
    {
        if (!in_array($data['day'] , $this->_days) && !preg_match("/(2[0-3]|[01][1-9]|10):([0-5][0-9])/", $data['startTime']) && !preg_match("/(2[0-3]|[01][1-9]|10):([0-5][0-9])/", $data['endTime'])) {
          return $catchUpLessonSchedule;
        }
        if (\DateTime::createFromFormat('H:i', $data['startTime']) > \DateTime::createFromFormat('H:i', $data['endTime'])) {
          return $catchUpLessonSchedule;
        }
        if ($data['id'] == "" || $data['id'] == 0) {
          $data['id'] = bin2hex(random_bytes(5));
          $catchUpLessonSchedule[$data['id']] = $data;
        }else {
          $catchUpLessonSchedule[$data['id']] = $data;
        }
        return $catchUpLessonSchedule;
    }

    /**
    * delete CatchUpLesson Schedule before add
    * @param  array $catchUpLessonSchedule
    * @param  array $data
    */
    public function deleteCatchUpLessonScheduleLineSession($catchUpLessonSchedule , $data)
    {
      foreach ($data as $value) {
        if (isset($catchUpLessonSchedule[$value['value']])){
          unset($catchUpLessonSchedule[$value['value']]);
        }
      }
      return $catchUpLessonSchedule;
    }

    /**
    * update CatchUpLesson Schedule after add
    * @param  SMS/PaymentBundle/Entity/CatchUpLesson $catchUpLesson
    * @param  array $data
    */
    public function updateCatchUpLessonScheduleLineDB($catchUpLesson , $data)
    {
        if (!in_array($data['day'] , $this->_days) && !preg_match("/(2[0-3]|[01][1-9]|10):([0-5][0-9])/", $data['startTime']) && !preg_match("/(2[0-3]|[01][1-9]|10):([0-5][0-9])/", $data['endTime'])) {
          return $catchUpLessonSchedule;
        }
        if (\DateTime::createFromFormat('H:i', $data['startTime']) > \DateTime::createFromFormat('H:i', $data['endTime'])) {
          return $catchUpLessonSchedule;
        }
        if ($data['id'] == "" || $data['id'] == 0) {
          $catchUpLessonSchedule = new $this->_catchUpLessonSechduleClass ();
          $catchUpLessonSchedule->setDay($data['day']);
          $catchUpLessonSchedule->setEndTime(\DateTime::createFromFormat('H:i', $data['endTime']));
          $catchUpLessonSchedule->setStartTime(\DateTime::createFromFormat('H:i', $data['startTime']));
          $catchUpLesson->addSchedule($catchUpLessonSchedule);
          $catchUpLessonSchedule->setCatchUpLesson($catchUpLesson);
          $this->_em->persist($catchUpLessonSchedule);
          $this->_em->flush();
        }else {
          $catchUpLessonSchedule = $this->_em->getRepository($this->_catchUpLessonSechduleClass)->find($data['id']);
          $catchUpLessonSchedule->setDay($data['day']);
          $catchUpLessonSchedule->setEndTime(\DateTime::createFromFormat('H:i', $data['endTime']));
          $catchUpLessonSchedule->setStartTime(\DateTime::createFromFormat('H:i', $data['startTime']));
          $this->_em->flush();
        }
    }

    /**
    * delete CatchUpLesson Schedule after add
    * @param  SMS/PaymentBundle/Entity/CatchUpLesson $catchUpLesson
    * @param  array $data
    */
    public function deleteCatchUpLessonScheduleLineDB($catchUpLesson , $data)
    {
      $this->_em->beginTransaction();
      $repository = $this->_em->getRepository($this->_catchUpLessonSechduleClass);
      foreach ($data as $value) {
        $catchUpLessonSechdule = $repository->find($value['value']);
        if ($catchUpLesson->getSchedules()->contains($catchUpLessonSechdule)) {
          $catchUpLesson->removeSchedule($catchUpLessonSechdule);
          $this->_em->remove($catchUpLessonSechdule);
        }
      }
      $this->_em->flush();
      $this->_em->commit();
    }

    /**
    * add CatchUpLesson
    * @param  SMS/PaymentBundle/Entity/CatchUpLesson $catchUpLesson
    * @param  array $catchUpLessonSchedules
    * @param  SMS/UserBundle/Entity/User $user
    */
    public function addCatchUpLesson($catchUpLesson , $catchUpLessonSchedules , $user)
    {
        $this->_em->beginTransaction();
        foreach ($catchUpLessonSchedules as $data) {
          $catchUpLessonSchedule = new $this->_catchUpLessonSechduleClass ();
          $catchUpLessonSchedule->setDay($data['day']);
          $catchUpLessonSchedule->setEndTime(\DateTime::createFromFormat('H:i', $data['endTime']));
          $catchUpLessonSchedule->setStartTime(\DateTime::createFromFormat('H:i', $data['startTime']));
          $catchUpLesson->addSchedule($catchUpLessonSchedule);
          $catchUpLessonSchedule->setCatchUpLesson($catchUpLesson);
          $this->_em->persist($catchUpLessonSchedule);
        }
        $catchUpLesson->setAuthor($user);
        $this->_em->persist($catchUpLesson);
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
    * Registration Action
    *
    * @param SMS/PaymentBundle/Entity/PaymentType $paymentType
    * @param array $choices
    */
    public function updatePaymentTypeRegistration($paymentType , $choices , $registered)
    {
      $repository = $this->_em->getRepository($this->_studentClass);
      $this->_em->beginTransaction();
      foreach ($choices as $choice) {
          $student = $repository->find($choice['value']);
          if ($registered){
            if ($paymentType->getStudent()->contains($student)) {
              continue;
            }
            $paymentType->addStudent($student);
          }else {
            if ($paymentType->getStudent()->contains($student)) {
              $paymentType->removeStudent($student);
            }
          }

      }
      $this->_em->flush();
      $this->_em->commit();
    }

    /**
    * Registration Action
    *
    * @param SMS/PaymentBundle/Entity/CatchUpLesson $catchUpLesson
    * @param array $choices
    */
    public function updateCatchUpLessonRegistration($catchUpLesson , $choices = array() , $registred = true)
    {
        $repository = $this->_em->getRepository($this->_studentClass);
        $this->_em->beginTransaction();
        foreach ($choices as $choice) {
            $student = $repository->find($choice['value']);
            if ($registred){
              if ($catchUpLesson->getStudent()->contains($student)) {
                continue;
              }
              $catchUpLesson->addStudent($student);
            }else {
              if ($catchUpLesson->getStudent()->contains($student)) {
                $catchUpLesson->removeStudent($student);
              }
            }

        }
        $this->_em->flush();
        $this->_em->commit();
    }

    /**
    * get All Registred Student By PaymentType
    *
    * @param SMS/PaymentBundle/Entity/CatchUpLesson $catchUpLesson
    * @param Integer $month
    */
    public function getRegistredStudentByPaymentType($catchUpLesson , $month)
    {
        $query = $this->_em->getRepository($this->_studentClass)->findAllRegistredStudent($catchUpLesson);
        if ($month == 'all'){
          $query->addSelect(sprintf("(SELECT SUM(credit_payment.credit) FROM %s as credit_payment WHERE credit_payment.paymentType = registrations AND credit_payment.student = student ) AS credit", $this->_paymentClass));
          $query->addSelect(sprintf("(SELECT SUM(paid_payment.price) FROM %s as paid_payment WHERE paid_payment.paymentType = registrations AND paid_payment.student = student ) AS paid", $this->_paymentClass));
        }else{
          $query->addSelect(sprintf("(SELECT SUM(credit_payment.credit) FROM %s as credit_payment WHERE credit_payment.paymentType = registrations AND credit_payment.student = student AND credit_payment.month = %s ) AS credit", $this->_paymentClass , $month));
          $query->addSelect(sprintf("(SELECT SUM(paid_payment.price) FROM %s as paid_payment WHERE paid_payment.paymentType = registrations AND paid_payment.student = student AND paid_payment.month = %s ) AS paid", $this->_paymentClass , $month));
        }
        return $query;
    }

    /**
    * get All Payment By Student
    *
    * @param SMS/PaymentBundle/Entity/Student $student
    * @return array()
    */
    public function getStatsByStudent($student)
    {
        $studentsStats = $this->_em->getRepository($this->_paymentClass)->findByStudent($student);
        return array('paymentsInfo' => $studentsStats);
    }

    /**
    * get All Payment By PaymentType
    *
    * @param SMS/PaymentBundle/Entity/PaymentType $paymentType
    * @return array()
    */
    public function getStatsByPaymentType($paymentType)
    {
        $paymentsStats = $this->_em->getRepository($this->_paymentClass)->findByPayment($paymentType);
        $paymentsChart = $this->_em->getRepository($this->_paymentClass)->findChartByPayment($paymentType);
        $months = array_map(function ($value){return $this->_translator->trans($value);}, array_column($paymentsChart, 'month'));
        return array('paymentsInfo' => $paymentsStats , 'month' => $months , 'pricePaid' => array_column($paymentsChart, 'price') , 'credit' => array_column($paymentsChart, 'credit'));
    }

    /**
    * get All Payment
    *
    * @param SMS/EstablishmentBundle/Entity/Establishment $establishment
    * @return array()
    */
    public function getPaymentStats($establishment)
    {
      $paymentsInfo = $this->_em->getRepository($this->_paymentClass)->getPaymentInfoByEstablishment($establishment);
      $paymentsResult = $this->_em->getRepository($this->_paymentClass)->findChartByTypePayment($establishment);
      $catchUpLessonResult = $this->_em->getRepository($this->_paymentClass)->findChartByCatchUpLesson($establishment);
      $months = array_map(function ($value){return $this->_translator->trans($value);}, array_column($paymentsResult, 'month'));
      $paymentsChart = array('month' => $months , 'pricePaid' => array_column($paymentsResult, 'price') , 'credit' => array_column($paymentsResult, 'credit'));
      $months = array_map(function ($value){return $this->_translator->trans($value);}, array_column($catchUpLessonResult, 'month'));
      $catchUpLessonChart = array('month' => $months , 'pricePaid' => array_column($catchUpLessonResult, 'price') , 'credit' => array_column($catchUpLessonResult, 'credit'));
      return array('paymentsInfo' => $paymentsInfo , 'paymentsChart' => $paymentsChart , 'catchUpLessonChart' => $catchUpLessonChart);
    }

    /**
    * get All Registred Student By PaymentType
    *
    * @param SMS/PaymentBundle/Entity/PaymentType $paymentType
    * @param Integer $month
    */
    public function getRegistredStudentByStudent($paymentType , $month, $student)
    {
        $query = $this->_em->getRepository($this->_studentClass)->findRegistredStudent($student->getId() , $paymentType->getId());
        $query->select(sprintf("(SELECT SUM(credit_payment.credit) FROM %s as credit_payment WHERE credit_payment.paymentType = registrations AND credit_payment.student = student AND credit_payment.month = %s ) AS credit", $this->_paymentClass , $month));
        $query->addSelect(sprintf("(SELECT SUM(paid_payment.price) FROM %s as paid_payment WHERE paid_payment.paymentType = registrations AND paid_payment.student = student AND paid_payment.month = %s ) AS paid", $this->_paymentClass , $month));
        return $query->getQuery()->getOneOrNullResult();
    }

}
