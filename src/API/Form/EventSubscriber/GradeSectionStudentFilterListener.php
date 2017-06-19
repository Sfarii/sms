<?php

namespace API\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use SMS\EstablishmentBundle\Entity\Grade;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\UserBundle\Entity\Student;
use Doctrine\ORM\EntityRepository;
use SMS\EstablishmentBundle\Entity\Establishment;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class GradeSectionStudentFilterListener
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Form\EventSubscriber
 */
class GradeSectionStudentFilterListener implements EventSubscriberInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Establishment
     */
    protected $establishment;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em , Establishment $establishment )
    {
        $this->em = $em;
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
     * Set form field
     *
     * @param FormInterface $form
     * @param Grade $grade
     * @return Void
     */
    public function addElements(FormInterface $form, Grade $grade = null , Section $section = null , $type = "add") {

        $establishment = $this->establishment ;
        // Add the grade element
        $form->add('grade' , EntityType::class , array(
                    'data'          => $grade,
                    'class'         => Grade::class,
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('grade')
                                  ->join('grade.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'property'      => 'gradeName',
                    'placeholder'   => 'course.field.grade',
                    'mapped'        => false,
                    'constraints'   => [new NotBlank()],
                    'label'         => 'course.field.grade',
                    'attr'          => [ 'class'=> 'gradeField'])
        );
          // Add the Section element
        $form->add('section' , EntityType::class , array(
                    'data'       => $section,
                    'class'         => Section::class,
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
                    'placeholder'   => 'schedule.field.section',
                    'label'         => 'schedule.field.section',
                    'constraints'   => [new NotBlank()],
                    'mapped'        => false,
                    'attr'          => [ 'class'=> 'sectionField']
                    )
                );
        $students = array();
        if ($section){
             // Fetch the student from specified grade
            $students = $this->em->getRepository(Student::class)->findBySectionAndEstablishment($section , $establishment);

        }
        // Add the Student element
        $form->add('student' , EntityType::class , array(
                    'class'         => Student::class,
                    'choices'       => $students,
                    'placeholder'   => 'filter.field.student',
                    'label'         => 'filter.field.student',
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
        $grade = $this->em->getRepository(Grade::class)->find($data['grade']);
        $section = $this->em->getRepository(Section::class)->find($data['section']);
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
