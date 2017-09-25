<?php

namespace SMS\StoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Services
 */
class ShopEntityManager
{
    const IN_STOCK = 'in_stock';
    const OUT_OF_STOCK = 'out_of_stock';

    const DEFAULT_ORDER_STATE = false ;

    /**
    * @var Doctrine\ORM\EntityManager
    */
    private $_em;

    /**
    * @var Symfony\Component\Translation\DataCollectorTranslator
    */
    private $_translator;

    /**
    * @var \Mailer
    */
    private $_mailer;

    /**
    * @var String
    */
    private $_orderUserClassName;
    private $_orderProviderClassName;
    private $_purchaseClassName;
    private $_orderLineClassName;
    private $_purchaseLineClassName;
    private $_storeOrderLineClassName;
    private $_productClassName;

    /**
    * @param Doctrine\ORM\EntityManager $em
    */
    public function __construct(EntityManager $em, DataCollectorTranslator $_translator)
    {
        $this->_em = $em;
        $this->_translator = $_translator;
    }

    /**
    * @param String $orderUserClassName
    */
    public function setOrderUserClassName($orderUserClassName)
    {
      $this->_orderUserClassName = $orderUserClassName;
    }

    /**
    * @param String $storeOrderLineClassName
    */
    public function setStoreOrderLineClassName($storeOrderLineClassName)
    {
      $this->_storeOrderLineClassName = $storeOrderLineClassName;
    }

    /**
    * @param String $orderProviderClassName
    */
    public function setOrderProviderClassName($orderProviderClassName)
    {
      $this->_orderProviderClassName = $orderProviderClassName;
    }

    /**
    * @param String $purchaseClassName
    */
    public function setPurchaseClassName($purchaseClassName)
    {
      $this->_purchaseClassName = $purchaseClassName;
    }

    /**
    * @param String $purchaseLineClassName
    */
    public function setPurchaseLineClassName($purchaseLineClassName)
    {
      $this->_purchaseLineClassName = $purchaseLineClassName;
    }

    /**
    * @param String $purchaseOrderLineClassName
    */
    public function setOrderLineClassName($orderLineClassName)
    {
      $this->_orderLineClassName = $orderLineClassName;
    }

    /**
    * @param String $productClassName
    */
    public function setProductClassName($productClassName)
    {
      $this->_productClassName = $productClassName;
    }

    /**
    * @param String $mailer
    */
    public function setMailer($mailer)
    {
        $this->_mailer = $mailer;
    }

    /**
    * insert entity in the database
    * @param Object $object
    * @param User $user
    */
    public function insert($object, $user = null)
    {
        if (!is_null($user)){
          $object->setAuthor($user);
        }
        $this->_em->persist($object);
        $this->_em->flush($object);
    }

    /**
    * insert Product in the database
    * @param SMS/StoreBundle/Entity/Product $product
    * @param User $user
    */
    public function addProduct($product, $user = null)
    {
      if (!is_null($user)){
        $product->setAuthor($user);
      }
      $product->setStock(0);
      $product->setSku($this->getSKUValue());
      $product->setStatus(self::OUT_OF_STOCK);
      $this->_em->persist($product);
      $this->_em->flush($product);
      //$this->_mailer->sendPaymentEmail($user , $product);

      return true;
    }

    /**
    * @param SMS/StoreBundle/Entity/Purchase $purchase
    * @param array $result
    * @param SMS/UserBundle/Entity/User $user
    */
    public function newPurchase($purchase , $result , $user = null)
    {
      $this->_em->beginTransaction();
      $purchase->setReference($this->getUniquePurchaseReferenceValue())->setAuthor($user);
      foreach ($result as $line) {
          $product = $this->_em->getRepository($this->_productClassName)->findOneBy(array('id' => $line['productID']));
          $orderLine = new $this->_purchaseLineClassName ();
          $orderLine->setPrice($line['price']);
          $orderLine->setQuantity($line['quantity']);
          $orderLine->setProduct($product);
          $orderLine->setPurchase($purchase);
          $purchase->addPurchaseLine($orderLine);
          if ($purchase->getState()) {
            $line->setState(true);
            $line->getProduct()->setStock($line->getProduct()->getStock() + $line->getQuantity());
          }else{
            $line->setState(false);
          }
          $this->_em->persist($orderLine);
      }

      $this->_em->persist($purchase);
      $this->_em->flush();
      $this->_em->commit();
    }

    /**
    * @param array $purshaseItem
    * @param integer $quantity
    * @param float $price
    * @param SMS/StoreBundle/Entity/Product $product
    */
    public function updatePurchaseLineInSession($purshaseItem , $quantity , $price , $product)
    {
      $product = $this->_em->getRepository($this->_productClassName)->findOneBy(array('id' => $product));
      if (array_key_exists($product->getId(),$purshaseItem)) {
        $purshaseItem[$product->getId()]['quantity'] = $quantity >= 1 ? $quantity : $purshaseItem[$product->getId()]['quantity'] ;
        $purshaseItem[$product->getId()]['price'] = $price >= 1 ?  $price : $purshaseItem[$product->getId()]['price'] ;
      }else{
        $purshaseItem[$product->getId()] = array('productName' => $product->getProductName() , 'productID' => $product->getId() , 'quantity' => $quantity , 'price' => $price);
      }
      return $purshaseItem;
    }

    /**
    * @param array $purshaseItem
    * @param array $data
    */
    public function deletePurchaseLineInSession($purshaseItem , $data)
    {
      foreach ($data as $id) {
        if (isset($purshaseItem[$id])){
          unset($purshaseItem[$id]);
        }
      }
      return $purshaseItem;
    }

    /**
    * @param SMS/StoreBundle/Entity/Order $order
    * @param integer $quantity
    * @param SMS/StoreBundle/Entity/Product $productId
    */
    public function updateProviderOrderLine($order , $quantity , $productId)
    {
      $line = array_filter($order->getOrderLines()->toArray() , function ($value) use ($productId) {return strcasecmp($value->getProduct()->getId(),$productId) == 0;});
      $product = $this->_em->getRepository($this->_productClassName)->findOneBy(array('id' => $productId));

        if (empty($line)){
          $line = new $this->_storeOrderLineClassName ();
          $line->setPrice($product->getPrice());
          $line->setQuantity($quantity);
          $line->setProduct($product);
          $line->setOrders($order);
          $order->addOrderLine($line);
          $this->_em->persist($line);
          $this->_em->flush();
          return true;
        }
        $line = reset($line);
        $line->setQuantity($quantity >= 1 ? $quantity : $line->getQuantity());
        $this->_em->flush();
        return false;
    }

    /**
    * @param SMS/StoreBundle/Entity/Order $order
    * @param integer $quantity
    * @param SMS/StoreBundle/Entity/Product $productId
    */
    public function updateOrderLine($order , $quantity , $productId)
    {
      $line = array_filter($order->getOrderLines()->toArray() , function ($value) use ($productId) {return strcasecmp($value->getProduct()->getId(),$productId) == 0;});
      $product = $this->_em->getRepository($this->_productClassName)->findOneBy(array('id' => $productId));

        if (empty($line)){
          if ($product->getStock() < $quantity) {
            return false;
          }
          $line = new $this->_orderLineClassName ();
          $line->setPrice($product->getPrice());
          $line->setQuantity($quantity);
          $line->setProduct($product);
          $line->setOrders($order);
          if ($order->getState()) {
            $line->setState(true);
            $line->getProduct()->setStock($line->getProduct()->getStock() - $line->getQuantity());
          }else{
            $line->setState(false);
          }
          $order->addOrderLine($line);
          $this->_em->persist($line);
          $this->_em->flush();
          return true;
        }
        $line = reset($line);
        if (($product->getStock() + $line->getQuantity()) < $quantity) {
          return false;
        }
        $newStock = ($product->getStock() + $line->getQuantity()) - $quantity;
        $line->setQuantity($quantity >= 1 ? $quantity : $line->getQuantity());
        if ($line->getState()) {
          $product->setStock($newStock);
        }
        $this->_em->flush();
        return false;
    }

    /**
    * @param SMS/StoreBundle/Entity/Purchase $purchase
    */
    public function updateUserOrder($order)
    {
      $this->_em->beginTransaction();
      $state = true;
      if ($order->getState()) {
        foreach ($order->getOrderLines() as $line) {
          if (!$line->getState() && $line->getProduct()->getStock() < $line->getQuantity()){
            $state = false;
          }
        }

        if ($state) {
          foreach ($order->getOrderLines() as $line) {
            if (!$line->getState()){
              $line->setState($order->getState());
              $line->getProduct()->setStock($line->getProduct()->getStock() - $line->getQuantity());
            }
          }
        }else{
          $order->setState($state);
        }
      }
      if (!$order->getState()) {
        foreach ($order->getOrderLines() as $line) {
          if ($line->getState()){
            $line->setState($order->getState());
            $line->getProduct()->setStock($line->getProduct()->getStock() + $line->getQuantity());
          }
        }
      }
      $this->_em->flush();
      $this->_em->commit();

      return $state;
    }



    /**
    * @param SMS/StoreBundle/Entity/Purchase $purchase
    */
    public function updatePurchase($purchase)
    {
      $this->_em->beginTransaction();
      $state = true;
      if (!$purchase->getState()) {
        foreach ($purchase->getPurchaseLines() as $line) {
          if ($line->getState() && $line->getProduct()->getStock() < $line->getQuantity()){
            $state = false;
          }
        }
        if ($state) {
          foreach ($purchase->getPurchaseLines() as $line) {
            if ($line->getState()){
              $line->setState(false);
              $line->getProduct()->setStock($line->getProduct()->getStock() - $line->getQuantity());
            }
          }
        }else{
          $purchase->getState($state);
        }
      }
      if ($purchase->getState()) {
        foreach ($purchase->getPurchaseLines() as $line) {
          if (!$line->getState()){
            $line->setState(true);
            $line->getProduct()->setStock($line->getProduct()->getStock() + $line->getQuantity());
          }
        }
      }
      $this->_em->flush();
      $this->_em->commit();
      return $state;
    }

    /**
    * @param array $data
    */
    public function deleteOrderLine($data= array())
    {
      $repository = $this->_em->getRepository($this->_orderLineClassName);
      $this->_em->beginTransaction();
      foreach ($data as $choice) {
          $line = $repository->find($choice['value']);
          try {
            if($line->getState()){
                $line->getProduct()->setStock($line->getProduct()->getStock() + $line->getQuantity());
                $this->_em->remove($line);
            }else {
              $this->_em->remove($line);
            }
          } catch (Exception $e) {
              throw new \Exception("Error this Entity has child ", 1);
          }
      }
      $this->_em->flush();
      $this->_em->commit();
    }

    /**
    * @param array $data
    */
    public function deletePurchaseLine($data = array())
    {
      $repository = $this->_em->getRepository($this->_purchaseLineClassName);
      $this->_em->beginTransaction();
      foreach ($data as $choice) {
          $line = $repository->find($choice['value']);

          try {
            if($line->getState()){
              if ($line->getProduct()->getStock() >= $line->getQuantity() ){
                $line->getProduct()->setStock($line->getProduct()->getStock() - $line->getQuantity());
                $this->_em->remove($line);
              }else{
                throw new \Exception("Error Stock", 1);
              }
            }else {
              $this->_em->remove($line);
            }
          } catch (Exception $e) {
              throw new \Exception("Error this Entity has child ", 1);
          }
      }
      $this->_em->flush();
      $this->_em->commit();
    }

    /**
    * @param SMS/StoreBundle/Entity/Purchase $purchase
    * @param integer $quantity
    * @param float $price
    * @param SMS/StoreBundle/Entity/Product $product
    */
    public function updatePurchaseLine($purchase , $quantity , $price , $product)
    {
        $line = array_filter($purchase->getPurchaseLines()->toArray() , function ($value) use ($product) {return strcasecmp($value->getProduct()->getId(),$product) == 0;});
        $product = $this->_em->getRepository($this->_productClassName)->findOneBy(array('id' => $product));
        if (empty($line)){
          $line = new $this->_purchaseLineClassName ();
          $line->setPrice($price);
          $line->setQuantity($quantity);
          $line->setProduct($product);
          $line->setPurchase($purchase);
          if ($purchase->getState()) {
            $line->setState(true);
            $line->getProduct()->setStock($line->getProduct()->getStock() + $line->getQuantity());
          }else{
            $line->setState(false);
          }
          $purchase->addPurchaseLine($line);
          $this->_em->persist($line);
          $this->_em->flush();
          return true;
        }
        $line = reset($line);
        $diff = abs($line->getQuantity() - $quantity);
        $line->setPrice($price >= 1 ?  $price : $line->getPrice());
        $line->setQuantity($quantity >= 1 ? $quantity : $line->getQuantity());
        if ($purchase->getState() && $purchase->getState()) {
          $product->setStock($product->getStock() + $diff);
          $line->setState(true);
        }else {
          $line->setState(false);
        }
        $this->_em->flush();
        return false;
    }

    /**
    * @param SMS\StoreBundle\Form\OrderType $form
    * @param array $result
    * @param SMS/UserBundle/Entity/User $user
    */
    public function addOrder($form , $result , $user = null)
    {
      $userOrders = array_merge( $form->get('student')->getData()->toArray() , $form->get('professor')->getData()->toArray());
      $providerOrders = $form->get('provider')->getData()->toArray();

      $this->_em->beginTransaction();
      foreach ($userOrders as $userOrder) {
        $orderUser = new $this->_orderUserClassName ();
        $orderUser->setUserOrder($userOrder)->setOrderDate(new \DateTime('now'))->setReference($this->getUniqueOrderUserReferenceValue())->setEstablishment($user->getEstablishment())->setState(self::DEFAULT_ORDER_STATE)->setAuthor($user);
        foreach ($result as $line) {
          $orderLine = new $this->_orderLineClassName ();
          $orderLine->setPrice($line->getPrice());
          $orderLine->setQuantity($line->getQuantity());
          $orderLine->setProduct($line);
          $orderLine->setState(false);
          $orderLine->setOrders($orderUser);
          $orderUser->addOrderLine($orderLine);
          $this->_em->persist($orderLine);
        }
        $this->_em->persist($orderUser);
        $this->_em->flush();
      }

      foreach ($providerOrders as $userOrder) {

        $orderProvider = new $this->_orderProviderClassName ();
        $orderProvider->setProvider($userOrder)->setReference($this->getUniqueOrderProviderReferenceValue())->setEstablishment($user->getEstablishment())->setAuthor($user);
        foreach ($result as $line) {
          $orderLine = new $this->_storeOrderLineClassName ();
          $orderLine->setPrice($line->getPrice());
          $orderLine->setQuantity($line->getQuantity());
          $orderLine->setProduct($line);
          $orderLine->setOrders($orderProvider);
          $orderProvider->addOrderLine($orderLine);
          $this->_em->persist($orderLine);
        }
        $this->_em->persist($orderProvider);
        $this->_em->flush();
      }

      $this->_em->commit();
    }

    /**
    * get Order User reference
    * @param string $reference
    */
    public function getUniqueOrderUserReferenceValue()
    {
      do {
          $reference = mb_convert_case(bin2hex(random_bytes(10)), MB_CASE_UPPER, "UTF-8");
          if (is_null($this->_em->getRepository($this->_orderUserClassName)->findOneBy(array('reference' => $reference )))) {
              return $reference ;
          }
      } while (true);
    }

    /**
    * get Order Provider reference
    * @param string $reference
    */
    public function getUniqueOrderProviderReferenceValue()
    {
      do {
          $reference = mb_convert_case(bin2hex(random_bytes(10)), MB_CASE_UPPER, "UTF-8");
          if (is_null($this->_em->getRepository($this->_orderProviderClassName)->findOneBy(array('reference' => $reference )))) {
              return $reference ;
          }
      } while (true);
    }

    /**
    * get Purchase reference
    * @param string $reference
    */
    public function getUniquePurchaseReferenceValue()
    {
      do {
          $reference = mb_convert_case(bin2hex(random_bytes(10)), MB_CASE_UPPER, "UTF-8");
          if (is_null($this->_em->getRepository($this->_purchaseClassName)->findOneBy(array('reference' => $reference )))) {
              return $reference ;
          }
      } while (true);
    }

    /**
    * Unique SKU Value
    */
    public function getSKUValue()
    {
      do {
          $value = mb_convert_case(bin2hex(random_bytes(10)), MB_CASE_UPPER, "UTF-8");
          if (is_null($this->_em->getRepository($this->_productClassName)->findOneBy(array('sku' => $value )))) {
              return $value ;
          }
      } while (true);
    }

    /**
    * update entity in the database
    * @param Object $product
    */
    public function updateProduct($product)
    {
        $this->_em->flush($product);
    }

    /**
    * Store Filter
    *
    * @param SMS\StoreBundle\Form\SearchType $form
    * @param SMS\EstablishmentBundle\Entity\Establishment $establishment
    */
    public function getAllActiveProduct($form , $establishment)
    {
      $query = $this->_em->getRepository($this->_productClassName)->findAllActiveProduct($establishment);
      if ($form->isSubmitted()) {
          if (!empty($form->get('textField')->getData())){
            $query->andWhere('product.productName like :search OR product.sku LIKE :search OR product.description LIKE :search')
                  ->setParameter('search', '%'.$form->get('textField')->getData().'%');
          }
          if (!empty($form->get('price')->getData())){
            $price = explode("-", $form->get('price')->getData());
            $query->andWhere('product.price BETWEEN :minPrice and :maxPrice')
                  ->setParameter('minPrice', $price[0])
                  ->setParameter('maxPrice', trim($price[1]));

          }
          if (!empty($form->get('stock')->getData())){
            $stock = explode("-", $form->get('stock')->getData());
            $query->andWhere('product.stock BETWEEN :minStock and :maxStock')
                  ->setParameter('minStock', $stock[0])
                  ->setParameter('maxStock', trim($stock[1]));
          }
          if (!$form->get('productType')->getData()->isEmpty()){
            $query->andWhere('product.productType IN (:productType)')
                  ->setParameter('productType', $form->get('productType')->getData()->toArray());
          }


      }
      return $query->getQuery();

    }

    /**
    * update entity in the database
    * @param Object $object
    */
    public function update($object)
    {
        $this->_em->flush($object);
    }

    /**
    * delete one entity from the database
    * @param Object $object
    */
    public function delete($object)
    {
        $this->_em->remove($object);
        $this->_em->flush();
    }

    /**
    * delete multiple entity from the database
    *
    * @param String $className
    * @param array $choices
    */
    public function deleteAll($className, $choices = array())
    {
        $repository = $this->_em->getRepository($className);
        $this->_em->beginTransaction();
        foreach ($choices as $choice) {
            $object = $repository->find($choice['value']);

            try {
                if (is_object($object)) {
                    $this->_em->remove($object);
                }
            } catch (Exception $e) {
                throw new Exception("Error this Entity has child ", 1);
            }
        }
        $this->_em->flush();
        $this->_em->commit();
    }

    public function shopStatistics($establishment)
    {
      $purshaseDataByMonth = $this->_em->getRepository($this->_purchaseClassName)->findPurchasePriceByMonth($establishment);
      $purshaseChartByMonth = [];
      $orderDataByMonth = $this->_em->getRepository($this->_orderUserClassName)->findOrderPriceByMonth($establishment);
      $orderChartByMonth = [];
      $allKeys = array_unique(array_merge(array_column($orderDataByMonth, 'month') ,array_column($purshaseDataByMonth, 'month') ));
      sort($allKeys);
      foreach ($allKeys as $key) {
        if (in_array($key, array_column($purshaseDataByMonth, 'month'))) {
          $purshaseChartByMonth[$key]['price'] = $purshaseDataByMonth[array_search($key, array_column($purshaseDataByMonth, 'month'))]['price'];
        }else{
          $purshaseChartByMonth[$key]['price'] = 0;
        }
        if (in_array($key, array_column($orderDataByMonth, 'month'))) {
          $orderChartByMonth[$key]['price'] = $orderDataByMonth[array_search($key, array_column($orderDataByMonth, 'month'))]['price'];
        }else{
          $orderChartByMonth[$key]['price'] = 0;
        }

      }
      $purshaseDataByDay = array_map(function ($value){ $value['month'] = $value['month']->format('Y-m-d'); return $value;}, $this->_em->getRepository($this->_purchaseClassName)->findPurchasePrice($establishment));
      $orderDataByDay =  array_map(function ($value){ $value['month'] = $value['month']->format('Y-m-d'); return $value;}, $this->_em->getRepository($this->_orderUserClassName)->findOrderPrice($establishment));
      $orderChartByDay = [];
      $purshaseChartByDay = [];
      $result = array_unique(array_merge(array_column($orderDataByDay, 'month')  ,array_column($purshaseDataByDay, 'month')  ));
      usort($result, function ($a, $b) {return strtotime($a) - strtotime($b);});
      foreach ($result as $key) {
        if (in_array($key, array_column($purshaseDataByDay, 'month'))) {
          $purshaseChartByDay[$key]['price'] = $purshaseDataByDay[array_search($key, array_column($purshaseDataByDay, 'month'))]['price'];
        }else{
          $purshaseChartByDay[$key]['price'] = 0;
        }
        if (in_array($key, array_column($orderDataByDay, 'month'))) {
          $orderChartByDay[$key]['price'] = $orderDataByDay[array_search($key, array_column($orderDataByDay, 'month'))]['price'];
        }else{
          $orderChartByDay[$key]['price'] = 0;
        }
      }

      return array('purchase_chart_day' => array_column($purshaseChartByDay, 'price') ,'order_chart_day' => array_column($orderChartByDay, 'price') ,'all_days' => array_values( $result) , 'total_order' => array_sum ( array_column($orderChartByMonth, 'price')) , 'total_purshase' => array_sum ( array_column($purshaseChartByMonth, 'price') ) , 'all_month' => array_values(array_map(function ($value){return $this->_translator->trans($value);}, $allKeys)) , 'order_chart' => array_column($orderChartByMonth, 'price') , 'purchase_chart' => array_column($purshaseChartByMonth, 'price'));
    }

    public function productStatistics($product)
    {
      $purshaseDataByDay = array_map(function ($value){ $value['month'] = $value['month']->format('Y-m-d'); return $value;}, $this->_em->getRepository($this->_purchaseClassName)->findPurchasePriceByProduct($product));
      $orderDataByDay =  array_map(function ($value){ $value['month'] = $value['month']->format('Y-m-d'); return $value;}, $this->_em->getRepository($this->_orderUserClassName)->findOrderPriceByProduct($product));
      $orderChartByDay = [];
      $purshaseChartByDay = [];
      $result = array_unique(array_merge(array_column($orderDataByDay, 'month')  ,array_column($purshaseDataByDay, 'month')  ));
      usort($result, function ($a, $b) {return strtotime($a) - strtotime($b);});
      foreach ($result as $key) {
        if (in_array($key, array_column($purshaseDataByDay, 'month'))) {
          $purshaseChartByDay[$key]['price'] = $purshaseDataByDay[array_search($key, array_column($purshaseDataByDay, 'month'))]['price'];
        }else{
          $purshaseChartByDay[$key]['price'] = 0;
        }
        if (in_array($key, array_column($orderDataByDay, 'month'))) {
          $orderChartByDay[$key]['price'] = $orderDataByDay[array_search($key, array_column($orderDataByDay, 'month'))]['price'];
        }else{
          $orderChartByDay[$key]['price'] = 0;
        }
      }
      return array('total_order' => array_sum ( array_column($orderDataByDay, 'price')) , 'total_purshase' => array_sum ( array_column($purshaseDataByDay, 'price') ) , 'purchase_chart_day' => array_column($purshaseChartByDay, 'price') ,'order_chart_day' => array_column($orderChartByDay, 'price') ,'all_days' => array_values( $result));
    }

    public function providerStatistics($provider)
    {
      $purshaseDataByDay = array_map(function ($value){ $value['month'] = $value['month']->format('Y-m-d'); return $value;}, $this->_em->getRepository($this->_purchaseClassName)->findPurchasePriceByProvider($provider));
      $orderDataByDay =  array_map(function ($value){ $value['month'] = $value['month']->format('Y-m-d'); return $value;}, $this->_em->getRepository($this->_orderProviderClassName)->findOrderPriceByProvider($provider));
      $orderChartByDay = [];
      $purshaseChartByDay = [];
      $result = array_unique(array_merge(array_column($orderDataByDay, 'month')  ,array_column($purshaseDataByDay, 'month')  ));
      usort($result, function ($a, $b) {return strtotime($a) - strtotime($b);});
      foreach ($result as $key) {
        if (in_array($key, array_column($purshaseDataByDay, 'month'))) {
          $purshaseChartByDay[$key]['price'] = $purshaseDataByDay[array_search($key, array_column($purshaseDataByDay, 'month'))]['price'];
        }else{
          $purshaseChartByDay[$key]['price'] = 0;
        }
        if (in_array($key, array_column($orderDataByDay, 'month'))) {
          $orderChartByDay[$key]['price'] = $orderDataByDay[array_search($key, array_column($orderDataByDay, 'month'))]['price'];
        }else{
          $orderChartByDay[$key]['price'] = 0;
        }
      }
      return array('total_order' => array_sum ( array_column($orderDataByDay, 'price')) , 'total_purshase' => array_sum ( array_column($purshaseDataByDay, 'price') ) , 'purchase_chart_day' => array_column($purshaseChartByDay, 'price') ,'order_chart_day' => array_column($orderChartByDay, 'price') ,'all_days' => array_values( $result));
    }
}
