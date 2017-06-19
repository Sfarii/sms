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
use SMS\UserBundle\Entity\Professor;
use SMS\StudyPlanBundle\Entity\Session;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use SMS\EstablishmentBundle\Entity\Division;
use Doctrine\ORM\EntityRepository;
use SMS\EstablishmentBundle\Entity\Establishment;

/**
 * Class ProfessorAttendanceFilterListener
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Form\EventSubscriber
 */
class ProfessorAttendanceFilterListener implements EventSubscriberInterface
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
    public function addElements(FormInterface $form, Professor $professor = null , $date = null ,Division $division = null) {
        // Remove the submit button, we will place this at the end of the form later
        $submit = $form->get('save');
        $status = $form->get('status');
        $form->remove('save');
        $form->remove('status');
        $establishment = $this->establishment ;
        // Add the division element
        $form->add('division' , EntityType::class , array(
                    'data'       => $division,
                    'class'         => Division::class,
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('division')
                                  ->join('division.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'property'      => 'divisionName',
                    'placeholder'   => 'filter.field.division',
                    'mapped'        => false,
                    'constraints'   => [new NotBlank()],
                    'label'         => 'filter.field.division',
                    'attr'          => [ 'class'=> 'divisionField'])
        );
        // Add the Professor element
        $form->add('professor' , EntityType::class , array(
                    'class'         => Professor::class,
                    'placeholder'   => 'filter.field.professor',
                    'label'         => 'filter.field.professor',
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('professor')
                                  ->join('professor.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'data'          => $professor,
                    'constraints'   => [new NotBlank()],
                    'attr'          => [ 'class'=> 'professorField']
                    )
                );

        // Add the date element
        $form->add('date' ,DateType::class , array(
                'label'         => 'attendance.field.date',
                'widget'        => 'single_text',
                'html5'         => false,
                'constraints'   => [new NotBlank() , new Date()],
                'attr'          => [ 'class'=> 'dateField' , 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"])
            );

        // Session are empty, unless we actually supplied a date
        $sessions = array();
        if ($date && $professor && $division) {
            // Get the day from the date object
            $timestamp = strtotime($date);
            $day = mb_convert_case(date("l", $timestamp), MB_CASE_LOWER , "UTF-8");
            // Fetch the session from specified date
            $repo = $this->em->getRepository(Session::class);
            $sessions = $repo->findByProfessorAndDateAndDivision( $professor,$day,$division, $establishment);
        }
        // Add the Session element
        $form->add('session' , EntityType::class , array(
                    'class'         => Session::class,
                    'property'      => 'sessionName',
                    'placeholder'   => 'filter.field.session',
                    'label'         => 'filter.field.session',
                    'choices'       => $sessions,
                    'empty_data'    => false,
                    'attr'          => [ 'class'=> 'sessionField']
                    )
                );
        // Add submit button again, this time, it's back at the end of the form
        $form->add($submit);
        $form->add($status);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        // Note that the data is not yet hydrated into the entity.
        $division = $this->em->getRepository(Division::class)->find($data['division']);
        $professor = $this->em->getRepository(Professor::class)->find($data['professor']);
        $this->addElements($form, $professor, $data['date'], $division);
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
