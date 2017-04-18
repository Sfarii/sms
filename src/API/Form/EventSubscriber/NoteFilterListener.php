<?php

namespace API\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use SMS\StudyPlanBundle\Entity\Exam;
use SMS\EstablishmentBundle\Entity\Grade;
use SMS\EstablishmentBundle\Entity\Division;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\UserBundle\Entity\Student;
use Symfony\Component\Validator\Constraints\NotBlank;

class NoteFilterListener implements EventSubscriberInterface
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
    public function addElements(FormInterface $form, Grade $grade = null , Course $course = null , Section $section = null , $by_student = false , Division $division = null) {
        // Remove the submit button, we will place this at the end of the form later
        $submit = $form->get('save');
        $form->remove('save');
        
        // Add the grade element
        $form->add('grade' , EntityType::class , array(
                    'data'          => $grade,
                    'class'         => Grade::class,
                    'property'      => 'gradeName',
                    'placeholder'   => 'course.field.grade',
                    'label'         => 'course.field.grade',
                    'attr'          => [ 'class'=> 'gradeField'])
        );
        
        // Section are empty, unless we actually supplied a grade
        $sections = array();
        $courses = array();
        if ($grade) {
            // Fetch the section from specified grade
            $sections = $this->em->getRepository(Section::class)->findByGrade($grade);
        }

        // Add the Section element
        $form->add('section' , EntityType::class , array(
                    'class'         => Section::class,
                    'property'      => 'sectionName',
                    'placeholder'   => 'filter.field.section',
                    'label'         => 'filter.field.section',
                    'choices'       => $sections,
                    'constraints'   => [new NotBlank()],
                    'attr'          => [ 'class'=> 'sectionField']
                    )
                );
        $students = array();
        if ($section) {
            // Fetch the student from specified section
            $students = $this->em->getRepository(Student::class)->findBySection($section);
        }
        if ($by_student) {
            
            // Add the student element
            $form->add('student' , EntityType::class , array(
                    'class'         => Student::class,
                    'placeholder'   => 'filter.field.student',
                    'label'         => 'filter.field.student',
                    'choices'       => $students,
                    'multiple'      => true,
                    'empty_data'    => false,
                    'attr'          => [ 'class'=> 'studentField']
                    )
                );
        }
        // Add the grade element
        $form->add('division' , EntityType::class , array(
                    'data'       => $division,
                    'class'         => Division::class,
                    'property'      => 'divisionName',
                    'placeholder'   => 'filter.field.division',
                    'mapped'        => false,
                    'label'         => 'filter.field.division',
                    'attr'          => [ 'class'=> 'divisionField'])
        );
        $courses = array();
        if ($grade && $division) {
            // Fetch the course from specified grade
            $courses = $this->em->getRepository(Course::class)->findByGradeAndDivision($grade , $division);
        }
        // Add the Course element
        $form->add('course' , EntityType::class , array(
                    'class' => Course::class,
                    'property' => 'courseName',
                    'choices'       => $courses,
                    'placeholder'   => 'filter.field.course',
                    'label'         => 'filter.field.course',
                    'constraints'   => [new NotBlank()],
                    'attr'          => [ 'class'=> 'courseField'])
                );
        
        // Section are empty, unless we actually supplied a Course
        $exams = array();
        if ($course) {
            // Fetch the exam from specified examType
            $exams = $this->em->getRepository(Exam::class)->findByCourse($course);
        }
        // Add the Exam element
        $form->add('exam' , EntityType::class , array(
                    'class' => Exam::class,
                    'property' => 'examName',
                    'choices'       => $exams,
                    'placeholder'=> 'filter.field.exam',
                    'label' => 'filter.field.exam',
                    'constraints'   => [new NotBlank()],
                    'attr'          => [ 'class'=> 'examField'])
                );
        // Add submit button again, this time, it's back at the end of the form
        $form->add($submit);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        // Note that the data is not yet hydrated into the entity.

        $grade = $this->em->getRepository(grade::class)->find($data['grade']);
        $course = $this->em->getRepository(Course::class)->find($data['course']);
        $section = $this->em->getRepository(Section::class)->find($data['section']);
        $division = $this->em->getRepository(Division::class)->find($data['division']);

        $by_student = false;
        if (array_key_exists('by_student', $data)) {
            $by_student = true;
        }
        $this->addElements($form, $grade , $course , $section , $by_student , $division);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();
        $this->addElements($form);
    }

}
