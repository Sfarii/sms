<?php

namespace API\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MonthType extends AbstractType
{
    private $_month;

    public function __construct(array $month)
    {
        $this->_month = $month;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->_month,
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
