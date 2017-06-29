<?php

namespace API\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManager;
use SMS\StoreBundle\Entity\ProductType;
use SMS\StoreBundle\Entity\Product;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;
use SMS\EstablishmentBundle\Entity\Establishment;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GradeSectionFilterListener
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Form\EventSubscriber
 */
class ProductProductTypeFilterListener implements EventSubscriberInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityManager
     */
    protected $establishment;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em , Establishment $establishment )
    {
        $this->em = $em;
        $this->establishment = $establishment;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::PRE_SET_DATA => 'onPreSetData'
            );
    }

    /**
     * Set form field
     *
     * @param FormInterface $form
     * @param productType $productType
     * @return Void
     */
    public function addElements(FormInterface $form, productType $productType = null ) {
        // Remove the submit button, we will place this at the end of the form later
        $submit = $form->get('save');
        $form->remove('save');
        $establishment = $this->establishment ;
        // Add the productType element
        $form->add('productType' , EntityType::class , array(
                    'data'          => $productType,
                    'class'         => ProductType::class,
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('productType')
                                  ->join('productType.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'property'      => 'productTypeName',
                    'placeholder'   => 'filter.field.productType',
                    'mapped'        => false,
                    'constraints'   => [new NotBlank()],
                    'label'         => 'filter.field.productType',
                    'attr'          => [ 'class'=> 'productTypeField'])
        );
        // Add the product element
        $form->add('product' , EntityType::class , array(
                    'class'         => Product::class,
                    'property'      => 'productName',
                    'query_builder' => function (EntityRepository $er) use ($productType , $establishment) {
                        return $er->createQueryBuilder('product')
                                  ->join('product.productType', 'productType')
                                  ->join('product.establishment', 'establishment')
                                  ->andWhere('productType.id = :productType')
                                  ->setParameter('productType', $productType)
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'placeholder'   => 'filter.field.product',
                    'label'         => 'filter.field.product',
                    'constraints'   => [new NotBlank()],
                    'attr'          => [ 'class'=> 'productField']
                    )
                );
        // Add submit button again, this time, it's back at the end of the form
        $form->add($submit);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        // Note that the data is not yet hydrated into the entity.
        $productType = $this->em->getRepository(productType::class)->find($data['productType']);
        $this->addElements($form, $productType);
    }

    /**
     * {@inheritdoc}
     */
    public function onPreSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();
        // We might have an empty data (when we insert a new data, for instance)
        $productType = null;
        if (!is_null($data)){
            $productType = $data->getProduct() ? $data->getProduct()->getProductType() : null;
        }
        $this->addElements($form, $productType);
    }

}
