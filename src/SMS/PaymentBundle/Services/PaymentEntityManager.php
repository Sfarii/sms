<?php

namespace SMS\PaymentBundle\Services;

use Doctrine\ORM\EntityManager;
use SMS\PaymentBundle\Entity\Registration;
use SMS\PaymentBundle\Entity\Payment;

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
    public function getRegistredStudent($className, $form , $establishment)
    {
      $query = $this->_em->getRepository($className)->findAllRegistredStudent($establishment);
      if ($form->isSubmitted()) {
          if (!empty($form->get('textField')->getData())){
            $query->andWhere('student.firstName like :search OR student.phone LIKE :search OR student.lastName LIKE :search OR student.email LIKE :search')
                  ->setParameter('search', '%'.$form->get('textField')->getData().'%');
          }
          if (!$form->get('extern')->getData() && $form->get('intren')->getData()){
            $query->andWhere('student.studentType = TRUE');
          }
          if ($form->get('extern')->getData() && !$form->get('intren')->getData()){
            $query->andWhere('student.studentType = FALSE');
          }
          if ($form->get('paid')->getData() && is_null($form->get('paymentType')->getData())){
            $query->addSelect(sprintf("(SELECT SUM(payment1.credit) FROM %s as payment1 WHERE payment1.student = student ) AS credit1", Payment::class))
                  ->andHaving('credit1 = 0');
          }
          if ($form->get('hasCredit')->getData() && is_null($form->get('paymentType')->getData())){
            $query->addSelect(sprintf("(SELECT SUM(payment2.credit) FROM %s as payment2 WHERE payment2.student = student ) AS credit2", Payment::class))
                  ->andHaving('credit2 > 0');
          }
          if ($form->get('paid')->getData() && !is_null($form->get('paymentType')->getData())){
            $query->addSelect(sprintf("(SELECT SUM(payment3.credit) FROM %s as payment3 WHERE payment3 = payment AND payment3.student = student ) AS credit3", Payment::class))
                  ->andHaving('credit3 = 0');
          }
          if ($form->get('hasCredit')->getData() && !is_null($form->get('paymentType')->getData())){

            $query->addSelect(sprintf("(SELECT SUM(payment4.credit) FROM %s as payment4 WHERE payment4 = payment AND payment4.student = student ) AS credit4", Payment::class))
                  ->andHaving('credit4 > 0');
          }
          if (!is_null($form->get('paymentType')->getData())){
            $query->andHaving('paymentType.id = :paymentType')
                  ->setParameter('paymentType', $form->get('paymentType')->getData()->getId());
          }
          if (!empty($form->get('months')->getData())){
            $query
                ->andWhere('payment.month = :months')
                ->setParameter('months', $form->get('months')->getData());
          }
          if ($form->get('Registred')->getData() && !$form->get('notRegistred')->getData()){
            $query->andWhere('registrations.registered = TRUE');
          }
          if (!$form->get('Registred')->getData() && $form->get('notRegistred')->getData()){
            $query->andWhere('registrations.registered = FALSE');
          }

      }
      return $query->getQuery();

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
              $registration->setRegistered($data['registered']);
              $this->_em->flush($registration);
              continue;
            }
            $registration = new Registration();
            $registration->setStudent($student)
                          ->setPaymentType($data['paymentType'])
                          ->setEstablishment($data['user']->getEstablishment())
                          ->setRegistered($data['registered'])
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
    * @param String $className
    * @param array $choices
    * @param boolean $boolean
    */
    public function registrationAction($choices = array(),$boolean)
    {
        $repository = $this->_em->getRepository(Registration::class);
        $this->_em->beginTransaction();
        foreach ($choices as $choice) {
            $object = $repository->find($choice['value']);
            $object->setRegistered($boolean);

        }
        $this->_em->flush();
        $this->_em->commit();
    }
}
