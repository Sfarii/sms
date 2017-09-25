<?php

namespace SMS\StoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PurchaseStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                '' => 'store.state.placeholder',
                false => 'store.state.padding',
                true => 'in_stock',
            )
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
