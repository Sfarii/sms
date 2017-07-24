<?php

namespace SMS\SchoolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\SchoolBundle\Entity\Pricing;
use SMS\SchoolBundle\Entity\Translations\PricingTranslation;

class PricingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
               ->add('price' ,TextType::class , array(
                    'label' => 'pricing.field.price')
                )
              ->add('pricingName', 'sms_translatable_field', array(
                  'field'          => 'pricingName',
                  'property_path'  => 'translations',
                  'widget'         => TextType::class,
                  'personal_translation' => PricingTranslation::class,
              ))
              ->add('unitPrice', 'sms_translatable_field', array(
                  'field'          => 'unitPrice',
                  'label' => 'aboutus.field.text',
                  'property_path'  => 'translations',
                  'widget'         => TextType::class,
                  'personal_translation' => PricingTranslation::class,
              ))
              ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Pricing::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_schoolbundle_pricing';
    }


}
