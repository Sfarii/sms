<?php

namespace SMS\EstablishmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DivisionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name' , null , array('attr' => [ 'form.grid'=> "none"]))
                ->add('startDate' , DateType::class, array(
                    'widget' => 'single_text',

                    // do not render as type="date", to avoid HTML5 date pickers
                    'html5' => false,

                    // add a class that can be selected in JavaScript
                    'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
                ))
                ->add('endDate', DateType::class, array(
                    'widget' => 'single_text',

                    // do not render as type="date", to avoid HTML5 date pickers
                    'html5' => false,

                    // add a class that can be selected in JavaScript
                    'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
                ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SMS\EstablishmentBundle\Entity\Division'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_establishmentbundle_division';
    }


}
