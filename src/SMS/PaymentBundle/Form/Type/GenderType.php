<?php

namespace SMS\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class Gender Type
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Form\Type
 */
class GenderType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                '' => 'user.field.gender',
                'gender.male' => 'gender.male',
                'gender.female' => 'gender.female',
                'gender.other' => 'gender.other',
            )
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
