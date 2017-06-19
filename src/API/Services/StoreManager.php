<?php

namespace API\Services;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Services
 */

class StoreManager
{
    /**
    * @var Doctrine\ORM\EntityManager
    */
    private $_em;

    /**
    * @param Doctrine\ORM\EntityManager $em
    */
    public function __construct( $em)
    {
        $this->_em = $em;
    }

    public function addOrderProvider($orderProvider , $user , $extraData = array())
    {
      $orderProvider->setUser($user);
      $this->_em->persist($orderProvider);
      $this->_em->flush($orderProvider);
      if (!empty($extraData)){

      }
    }

    /**
    * delete multiple entity from the database
    * @param Object $object
    */
    public function deleteAll($className, $choices = array())
    {
        $repository = $this->_em->getRepository($className);

        foreach ($choices as $choice) {
            $object = $repository->find($choice['value']);


            try {
                if ($object) {
                    $this->_em->remove($object);
                }
            } catch (Exception $e) {
                throw new Exception("Error this Entity has child ", 1);
            }
        }

        $this->_em->flush();
    }

}
