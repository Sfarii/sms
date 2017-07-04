<?php

namespace SMS\UserBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use SMS\UserBundle\Entity\StudentParent;

/**
 * Class UsersListener
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Form\EventSubscriber
 */
class StudentListener implements EventSubscriberInterface
{
  const ADD = 'ADD';
  const EDIT = 'EDIT';
  /**
   * @var String
   */
  private $_action ;
  /**
   * @var EntityManager
   */
  protected $establishment;

  /**
   * Constructor
   *
   * @param Establishment $establishment
   */
  function __construct($establishment )
  {
      $this->establishment = $establishment;
  }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::PRE_SET_DATA => 'onPreSetData'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();
        if (!$data || null === $data->getId()) {
            $this->_action = self::ADD ;
        }else{
            $this->_action = self::EDIT ;
            if ($data->getStudentType() == true) {
              $this->addElements($form);
            }
            $form->add('save', SubmitType::class, array(
                'validation_groups' => "InternEdit",
                'label' => 'md-fab'
            ));
        }

    }

    public function addElements($form)
    {
      $establishment = $this->establishment;
      $form
          ->add('studentParent' , EntityType::class , array(
              'class' => StudentParent::class,
              'query_builder' => function (EntityRepository $er) use ($establishment) {
                  return $er->createQueryBuilder('studentParent')
                            ->join('studentParent.establishment', 'establishment')
                            ->andWhere('establishment.id = :establishment')
                            ->setParameter('establishment', $establishment->getId());
              },
              'label' => 'student.field.studentParent',
              'placeholder' => 'student.field.select_studentParent')
          );
    }

    public function removeElement($form)
    {
      $form->remove('studentParent');
      $form->remove('save');
      $form->add('save', SubmitType::class, array(
          'validation_groups' => "SimpleRegistration",
          'label' => 'md-fab'
      ));
    }

    public function onPreSubmit(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();
        if (array_key_exists('studentType', $user) && $user['studentType'] == true) {
            unset($user['save']);
            $this->addElements($form);
            if ($this->_action == Self::EDIT){
              $form->add('save', SubmitType::class, array(
                  'validation_groups' => "InternEdit",
                  'label' => 'md-fab'
              ));
            }else
            if (array_key_exists('show_username_password', $user) && $this->_action == Self::ADD) {
              $form->add('save', SubmitType::class, array(
                  'validation_groups' => "InternRegistration",
                  'label' => 'md-fab'
              ));
            }else {
              $form->add('save', SubmitType::class, array(
                  'validation_groups' => "Intern",
                  'label' => 'md-fab'
              ));
            }
        } else {
            $this->removeElement($form);
        }
    }
}
