<?php

namespace API\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use SMS\EstablishmentBundle\Entity\Grade;
use SMS\EstablishmentBundle\Entity\Division;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\StudyPlanBundle\Entity\Course;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class GradeSectionCourseFilterListener
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Form\EventSubscriber
 */
class GradeSectionCourseFilterListener implements EventSubscriberInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityManager
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
    public function addElements(FormInterface $form, Grade $grade = null , Division $division = null) {

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
                    'placeholder'   => 'filter.field.section',
                    'label'         => 'filter.field.section',
                    'attr'          => [ 'class'=> 'sectionField']
                    )
                );
        // Add the grade element
        $form->add('division' , EntityType::class , array(
                    'data'       => $division,
                    'class'         => Division::class,
                    'property'      => 'divisionName',
                    'placeholder'   => 'filter.field.division',
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('division')
                                  ->join('division.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'mapped'        => false,
                    'label'         => 'filter.field.division',
                    'attr'          => [ 'class'=> 'divisionField'])
        );
        // Add the Course element
        $form->add('course' , EntityType::class , array(
                    'class' => Course::class,
                    'property' => 'courseName',
                    'query_builder' => function (EntityRepository $er) use ($establishment, $grade,$division) {
                        return $er->createQueryBuilder('course')
                                  ->join('course.grade', 'grade')
                                  ->join('course.establishment', 'establishment')
                                  ->join('course.division', 'division')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId())
                                  ->andWhere('grade.id = :grade')
                                  ->setParameter('grade', $grade)
                                  ->andWhere('division.id = :division')
                                  ->setParameter('division', $division);
                    },
                    'placeholder'=> 'filter.field.course',
                    'label' => 'filter.field.course',
                    'attr'          => [ 'class'=> 'courseField'])
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
        $division = $this->em->getRepository(Division::class)->find($data['division']);
        $this->addElements($form, $grade , $division);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();
        // We might have an empty data (when we insert a new data, for instance)
        $grade = $data->getSection() ? $data->getSection()->getGrade() : null;
        $division = $data->getCourse() ? $data->getCourse()->getDivision() : null;

        $this->addElements($form, $grade , $division);
    }

}
