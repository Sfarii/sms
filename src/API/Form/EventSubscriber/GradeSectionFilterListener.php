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
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;
use SMS\EstablishmentBundle\Entity\Establishment;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GradeSectionFilterListener
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Form\EventSubscriber
 */
class GradeSectionFilterListener implements EventSubscriberInterface
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
    public function addElements(FormInterface $form, Grade $grade = null ) {
        // Remove the submit button, we will place this at the end of the form later
        $submit = $form->get('save');
        $form->remove('save');
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

        // Section are empty, unless we actually supplied a grade
        $sections = array();
        if ($grade) {
            // Fetch the section from specified grade
            $repo = $this->em->getRepository(Section::class);
            $sections = $repo->findByGrade($grade, array('name' => 'asc'));
        }
        // Add the Section element
        $form->add('section' , EntityType::class , array(
                    'class'         => Section::class,
                    'property'      => 'sectionName',
                    'placeholder'   => 'schedule.field.section',
                    'label'         => 'schedule.field.section',
                    'constraints'   => [new NotBlank()],
                    'choices'       => $sections,
                    'attr'          => [ 'class'=> 'sectionField']
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
        $this->addElements($form, $grade);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();
        // We might have an empty data (when we insert a new data, for instance)
        $grade = null;
        if (!is_null($data)){
            $grade = $data->getSection() ? $data->getSection()->getGrade() : null;
        }
        $this->addElements($form, $grade);
    }

}
