<?php

namespace API\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use SMS\EstablishmentBundle\Entity\Grade;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\StudyPlanBundle\Entity\Session;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use SMS\EstablishmentBundle\Entity\Division;

class StudentAttendanceFilterListener implements EventSubscriberInterface
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
    public function addElements(FormInterface $form, Grade $grade = null , Section $section = null , $date = null,Division $division = null) {
        // Remove the submit button, we will place this at the end of the form later
        $submit = $form->get('save');
        $form->remove('save');
        // Add the division element
        $form->add('division' , EntityType::class , array(
                    'data'       => $division,
                    'class'         => Division::class,
                    'property'      => 'divisionName',
                    'placeholder'   => 'filter.field.division',
                    'mapped'        => false,
                    'label'         => 'filter.field.division',
                    'attr'          => [ 'class'=> 'divisionField'])
        );
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
            $repo = $this->em->getRepository(Section::class);
            $sections = $repo->findByGrade($grade);
        }
        // Add the Section element
        $form->add('section' , EntityType::class , array(
                    'class'         => Section::class,
                    'property'      => 'sectionName',
                    'placeholder'   => 'filter.field.section',
                    'label'         => 'filter.field.section',
                    'choices'       => $sections,
                    'mapped'        => false,
                    'constraints'   => [new NotBlank()],
                    'attr'          => [ 'class'=> 'sectionField']
                    )
                );

        // Add the date element
        $form->add('date' ,DateType::class , array(
                'label' => 'attendance.field.date',
                'widget' => 'single_text',
                'html5' => false,
                'constraints'   => [new NotBlank() , new Date()],
                'attr' => [ 'class'=> 'dateField' , 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"])
            );
        
        // Session are empty, unless we actually supplied a date
        $sessions = array();
        if ($date && $section && $division) {
            // Get the day from the date object
            $timestamp = strtotime($date);
            $day = mb_convert_case(date("l", $timestamp), MB_CASE_LOWER , "UTF-8");
            // Fetch the session from specified date
            $repo = $this->em->getRepository(Session::class);
            $sessions = $repo->findBySectionAndDateAndDivision($section,$day,$division);
        }
        // Add the Session element
        $form->add('session' , EntityType::class , array(
                    'class'         => Session::class,
                    'property'      => 'sessionName',
                    'placeholder'   => 'filter.field.session',
                    'label'         => 'filter.field.session',
                    'choices'       => $sessions,
                    'mapped'        => false,
                    'empty_data'    => false,
                    'attr'          => [ 'class'=> 'sessionField']
                    )
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
        $grade = $this->em->getRepository(Grade::class)->find($data['grade']);
        $section = $this->em->getRepository(Section::class)->find($data['section']);
        $division = $this->em->getRepository(Division::class)->find($data['division']);
        $this->addElements($form, $grade, $section, $data['date'], $division);
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
