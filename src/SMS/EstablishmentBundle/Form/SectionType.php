<?php

namespace SMS\EstablishmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\EstablishmentBundle\Entity\Grade;
use Doctrine\ORM\EntityRepository;
use API\Form\Type\HiddenEntityType;


class SectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
            ->add('sectionName' ,TextType::class , array(
                'label' => 'section.field.sectionName')
            )
            ->add('grade' , EntityType::class , array(
                'class'      => Grade::Class,
                'query_builder' => function (EntityRepository $er) use ($establishment) {
                    return $er->createQueryBuilder('grade')
                              ->join('grade.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'property'   => 'gradeName',
                'label'      => 'section.field.grade',
                'placeholder'=> 'section.field.grade')
            )
            ->add('establishment', HiddenEntityType::class, array(
                'class' => Establishment::class,
                'data' =>  $establishment, // Field value by default
                ))
            ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Section::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_establishmentbundle_section';
    }


}
