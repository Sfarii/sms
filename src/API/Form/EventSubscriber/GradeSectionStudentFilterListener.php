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
use Symfony\Component\Validator\Constraints\NotBlank;

class GradeSectionStudentFilterListener implements EventSubscriberInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em)
    {
        $this->em = $em;
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
    public function addElements(FormInterface $form, Grade $grade = null , Section $section = null) {
        
        // Add the grade element
        $form->add('grade' , EntityType::class , array(
                    'data'          => $grade,
                    'class'         => Grade::class,
                    'property'      => 'gradeName',
                    'placeholder'   => 'filter.field.grade',
                    'mapped'        => false,
                    'constraints'   => [new NotBlank()],
                    'label'         => 'filter.field.grade',
                    'attr'          => [ 'class'=> 'gradeField'])
        );
        
        // Section are empty, unless we actually supplied a grade
        $sections = array();
        if ($grade) {
            // Fetch the section from specified grade
            $sections = $this->em->getRepository(Section::class)->findByGrade($grade);
        }
        $students = array();
        if ($section){
             // Fetch the student from specified grade
            $students = $this->em->getRepository(Student::class)->findBySection($section);
            $sections = $section;
        }

        // Add the Section element
        $form->add('section' , EntityType::class , array(
                    'data'          => $sections,
                    'class'         => Section::class,
                    'property'      => 'sectionName',
                    'placeholder'   => 'filter.field.section',
                    'label'         => 'filter.field.section',
                    'mapped'        => false,
                    'constraints'   => [new NotBlank()],
                    'attr'          => [ 'class'=> 'sectionField']
                    )
                );

        
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
        $this->addElements($form, $grade , $section);
    }

}
