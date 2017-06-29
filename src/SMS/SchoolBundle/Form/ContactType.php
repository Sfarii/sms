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
use SMS\SchoolBundle\Entity\Contact;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('name' ,TextType::class , array(
                  'label' => 'contact.field.name')
              )
              ->add('email' ,TextType::class , array(
                  'label' => 'contact.field.email')
              )
              ->add('phone' ,TextType::class , array(
                  'label' => 'contact.field.phone')
              )
              ->add('message' ,TextareaType::class , array(
                  'label' => 'contact.field.message',
                  'attr'          => [ 'class'=> 'materialize-textarea'])
              );

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Contact::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_schoolbundle_contact';
    }


}
