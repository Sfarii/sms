<?php

namespace SMS\StudyPlanBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\StudyPlanBundle\Entity\Session;


class SessionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sessionName' ,TextType::class , array(
                'label' => 'session.field.sessionName')
            )
            ->add('startTime', TimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'session.field.startTime' ,
                'attr' => [ 'data-uk-timepicker'=> ""],
            ))
            ->add('endTime', TimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'session.field.endTime' ,
                'attr' => [ 'data-uk-timepicker'=> ""],
            ))
            ->add('save', SubmitType::class);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Session::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_studyplanbundle_session';
    }


}
