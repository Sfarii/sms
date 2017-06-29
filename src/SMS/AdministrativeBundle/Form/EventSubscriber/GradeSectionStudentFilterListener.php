<?php

namespace SMS\AdministrativeBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class GradeSectionStudentFilterListener
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\AdministrativeBundle\Form\EventSubscriber
 */
class GradeSectionStudentFilterListener implements EventSubscriberInterface
{

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
    function __construct(EntityManager $em , $studentClass , $gradeClass , $sectionClass ,$establishment)
    {
        $this->em = $em;
        $this->establishment = $establishment;
        $this->gradeClass = $gradeClass;
        $this->sectionClass = $sectionClass;
        $this->studentClass = $studentClass;
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
    public function addElements(FormInterface $form, $grade = null , $section = null , $type = "add") {

        $establishment = $this->establishment ;
        // Add the grade element
        $form->add('grade' , EntityType::class , array(
                    'data'          => $grade,
                    'class'         => $this->gradeClass,
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('grade')
                                  ->join('grade.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'property'      => 'gradeName',
                    'placeholder'   => 'sanction.field.grade',
                    'mapped'        => false,
                    'constraints'   => [new NotBlank()],
                    'label'         => 'sanction.field.grade',
                    'attr'          => [ 'class'=> 'gradeField'])
        );
          // Add the Section element
        $form->add('section' , EntityType::class , array(
                    'data'       => $section,
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
                    'placeholder'   => 'sanction.field.section',
                    'label'         => 'sanction.field.section',
                    'constraints'   => [new NotBlank()],
                    'mapped'        => false,
                    'attr'          => [ 'class'=> 'sectionField']
                    )
                );
        // Add the Student element
        $form->add('student' , EntityType::class , array(
                    'class'         => $this->studentClass,
                    'query_builder' => function (EntityRepository $er) use ($establishment , $section) {
                        return $er->createQueryBuilder('student')
                                  ->join('student.section', 'section')
                                  ->andWhere('section.id = :section')
                                  ->setParameter('section', $section)
                                  ->join('student.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'placeholder'   => 'sanction.field.student',
                    'label'         => 'sanction.field.student',
                    'attr'          => [ 'class'=> 'studentField'])
                );
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        // Note that the data is not yet hydrated into the entity.
        $grade = $this->em->getRepository($this->gradeClass)->find($data['grade']);
        $section = $this->em->getRepository($this->sectionClass)->find($data['section']);
        $this->addElements($form, $grade , $section);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();
        // We might have an empty data (when we insert a new data, for instance)
        $grade = $data->getStudent() ? $data->getStudent()->getSection()->getGrade() : null;

        $section = $data->getStudent() ? $data->getStudent()->getSection() : null;
        $this->addElements($form, $grade , $section , "edit");
    }

}
