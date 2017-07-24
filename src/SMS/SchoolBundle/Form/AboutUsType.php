<?php

namespace SMS\SchoolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\SchoolBundle\Entity\AboutUs;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use SMS\SchoolBundle\Form\FormType\TranslationsType;
use SMS\SchoolBundle\Entity\Translations\AboutUsTranslation;

class AboutUsType extends AbstractType
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
            ->add('icon' ,TextType::class , array(
                'label' => 'aboutus.field.icon')
            )
            ->add('title', 'sms_translatable_field', array(
                'field'          => 'title',
                'property_path'  => 'translations',
                'widget'         => TextType::class,
                'personal_translation' => AboutUsTranslation::class,
            ))
            ->add('text', 'sms_translatable_field', array(
                'field'          => 'text',
                'label' => 'aboutus.field.text',
                'property_path'  => 'translations',
                'widget'         => TextareaType::class,
                'personal_translation' => AboutUsTranslation::class,
            ))

            ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AboutUs::class,
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_schoolbundle_aboutus';
    }


}
