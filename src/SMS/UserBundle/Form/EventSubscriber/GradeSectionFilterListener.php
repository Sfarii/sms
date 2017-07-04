<?php

namespace SMS\UserBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class GradeSectionFilterListener
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Form\EventSubscriber
 */
class GradeSectionFilterListener implements EventSubscriberInterface
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
    protected $em;

    /**
     * @var String Class Names
     */
    protected $studentClass;
    protected $gradeClass;
    protected $sectionClass;

    /**
     * @var Establishment
     */
    protected $establishment;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em , $gradeClass , $sectionClass ,$establishment)
    {
        $this->em = $em;
        $this->establishment = $establishment;
        $this->gradeClass = $gradeClass;
        $this->sectionClass = $sectionClass;
      }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::PRE_SET_DATA => 'onPreSetData'
            );
    }

    /**
     * Set form field
     *
     * @param FormInterface $form
     * @param Grade $grade
     * @return Void
     */
    public function addElements(FormInterface $form, $grade) {
        $form->remove('save');
        $establishment = $this->establishment ;
        // Add the grade element
        $form->add('grade' , EntityType::class , array(
                    'data'          => $grade,
                    'class'         => $this->gradeClass,
                    'property'      => 'gradeName',
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('grade')
                                  ->join('grade.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'placeholder'   => 'filter.field.select_grade',
                    'mapped'        => false,
                    'label'         => 'filter.field.grade',
                    'attr'          => [ 'class'=> 'gradeField'])
        )->add('section' , EntityType::class , array(
                    'class'         => $this->sectionClass,
                    'property'      => 'sectionName',
                    'query_builder' => function (EntityRepository $er) use ($establishment , $grade) {
                        return $er->createQueryBuilder('section')
                                  ->join('section.grade', 'grade')
                                  ->andWhere('grade.id = :grade')
                                  ->setParameter('grade', $grade)
                                  ->join('section.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'placeholder'   => 'filter.field.select_section',
                    'label'         => 'filter.field.section',
                    'attr'          => [ 'class'=> 'sectionField']
                    )
                );
    }

    public function removeElement($form)
    {
      $form->remove('save');
      $form->add('save', SubmitType::class, array(
          'validation_groups' => "SimpleRegistration",
          'label' => 'md-fab'
      ));
      $form->remove('grade');
      $form->remove('section');

    }

    /**
     * {@inheritdoc}
     */
    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        if (array_key_exists('studentType', $data) && $data['studentType'] == true) {
          // Note that the data is not yet hydrated into the entity.
          $grade = array_key_exists('grade', $data) ? $this->em->getRepository($this->gradeClass)->find($data['grade']) : null;
          $this->addElements($form, $grade);
          unset($data['save']);
          if ($this->_action == Self::EDIT){
            $form->add('save', SubmitType::class, array(
                'validation_groups' => "InternEdit",
                'label' => 'md-fab'
            ));
          }else
          if (array_key_exists('show_username_password', $data) && $this->_action == Self::ADD) {
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
        }else{
          $this->removeElement($form);
        }

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
              // We might have an empty data (when we insert a new data, for instance)
              $grade = $data->getSection() ? $data->getSection()->getGrade() : null;
              $this->addElements($form, $grade);
            }
        }
    }

}
