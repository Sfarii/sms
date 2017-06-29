<?php

namespace SMS\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class Role Type
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Form\Type
 */
class RoleType extends AbstractType
{
    private $_roleHierarchy;

    public function __construct(array $roleHierarchy)
    {
        $this->_roleHierarchy = $roleHierarchy;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //die(var_dump($this->getExistingRoles()));
        $resolver->setDefaults(array(
            'choices'       => $this->getExistingRoles(),
        ));
    }

    /**
     * @return array
     */
    private function getExistingRoles()
    {
        $theRoles = array();
        $roles = array_keys($this->_roleHierarchy);
        foreach ($roles as $role) {
            $theRoles[$role] = $role;
        }
        return $theRoles;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
