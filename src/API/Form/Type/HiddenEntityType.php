<?php

namespace API\Form\Type;


use API\Form\DataTransformer\HiddenEntityTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;

 /**
  * Class HiddenEntityType
  *
  * @author Rami Sfari <rami2sfari@gmail.com>
  * @copyright Copyright (c) 2017, SMS
  * @package API\Form\DataTransformer
  */
class HiddenEntityType extends AbstractType
{
    /**
    * @var ManagerRegistry
    */
    protected $objectManager;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(EntityManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new HiddenEntityTransformer($this->objectManager, $options['class']);
        $builder->addModelTransformer($transformer);
    }
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['class']);
        $resolver->setDefaults([
            'class'      => null,
            'data_class' => null,
            'invalid_message' => 'The entity does not exist.',
            'property'        => 'id',
        ]);
        $resolver->setAllowedTypes('invalid_message', ['null', 'string']);
        $resolver->setAllowedTypes('property', ['null', 'string']);
    }
    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
