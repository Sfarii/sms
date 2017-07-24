<?php

namespace SMS\SchoolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\SchoolBundle\Entity\SchoolTestimonial;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use SMS\SchoolBundle\Entity\Translations\SchoolTestimonialTranslation;

class SchoolTestimonialType extends AbstractType
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
              ->add('title', 'sms_translatable_field', array(
                  'field'          => 'title',
                  'property_path'  => 'translations',
                  'widget'         => TextType::class,
                  'personal_translation' => SchoolTestimonialTranslation::class,
              ))
              ->add('subtitle', 'sms_translatable_field', array(
                  'field'          => 'subtitle',
                  'property_path'  => 'translations',
                  'widget'         => TextType::class,
                  'personal_translation' => SchoolTestimonialTranslation::class,
              ))
              ->add('text', 'sms_translatable_field', array(
                  'field'          => 'text',
                  'label' => 'aboutus.field.text',
                  'property_path'  => 'translations',
                  'widget'         => TextareaType::class,
                  'personal_translation' => SchoolTestimonialTranslation::class,
              ))
              ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SchoolTestimonial::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_schoolbundle_schooltestimonial';
    }


}
