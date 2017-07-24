<?php

namespace SMS\SchoolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\SchoolBundle\Entity\Feature;
use SMS\SchoolBundle\Entity\Translations\FeatureTranslation;
class FeatureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('icon' ,TextType::class , array(
                  'label' => 'feature.field.icon')
              )
              ->add('title', 'sms_translatable_field', array(
                  'field'          => 'title',
                  'label' => 'feature.field.title',
                  'property_path'  => 'translations',
                  'widget'         => TextType::class,
                  'personal_translation' => FeatureTranslation::class,
              ))
              ->add('text', 'sms_translatable_field', array(
                  'field'          => 'text',
                  'label' => 'feature.field.text',
                  'property_path'  => 'translations',
                  'widget'         => TextareaType::class,
                  'personal_translation' => FeatureTranslation::class,
              ))
              ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Feature::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_schoolbundle_feature';
    }


}
