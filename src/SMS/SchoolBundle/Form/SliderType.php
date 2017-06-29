<?php

namespace SMS\SchoolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\SchoolBundle\Entity\Slider;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SliderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('imageFile',  VichImageType::class, array(
                      'allow_delete' => true, // not mandatory, default is true
                      'download_link' => false, // not mandatory, default is true
                      'label' => false )
                  )
              ->add('title' ,TextType::class , array(
                  'label' => 'slider.field.title')
              )
              ->add('subtitle' ,TextType::class , array(
                  'label' => 'slider.field.subtitle')
              )
              ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Slider::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_schoolbundle_slider';
    }


}
