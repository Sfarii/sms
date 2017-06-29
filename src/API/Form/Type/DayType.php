<?php 

namespace API\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DayType extends AbstractType
{
    private $_days;

    public function __construct(array $days)
    {
        $this->_days = $days;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->_days,
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}