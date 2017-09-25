<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\OrderLine;
use SMS\StoreBundle\Entity\OrderUser;
use SMS\StoreBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Order Line controller.
 *
 * @Route("order_line")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class OrderLineController extends BaseController
{
    /**
     * Lists all provider entities.
     *
     * @Route("/order/{id}", name="order_line_results")
     * @Method("GET")
     * @return Response
     */
    public function indexOrderLineResultAction(OrderUser $order)
    {
        $lineOrder = $this->getOrderLineEntityManager();
        $lineOrder->buildDatatable(array('id' => $order->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($lineOrder);
        $function = function($qb) use ($order)
        {
            $qb->join('order_line.orders', 'line')
                ->andWhere('line.id = :line')
        				->setParameter('line', $order->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/order/bulk/delete", name="order_line_bulk_delete", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkDeleteAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            try {
                $this->getEntityManager()->deleteOrderLine($choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('order_items.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('order_items.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Creates a show order line entity.
     *
     * @Route("/line/show/{id}", name="order_line_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSStoreBundle:orderuser:order.html.twig")
     */
    public function showOrderUserLineDBAction(Request $request , OrderUser $storeOrder)
    {
        $orderItems = $storeOrder->getOrderLines();
        $totalPrice = array_sum(array_map(function ($value){return $value->getPrice() * $value->getQuantity();}, $orderItems->toArray()));
        $totalQuantity = array_sum(array_map(function ($value){return $value->getQuantity();}, $orderItems->toArray()));

        return array(  'result' => $orderItems , 'totalPrice' => $totalPrice , 'totalQuantity' => $totalQuantity);
    }

    /**
     * Bulk purshase Items actions.
     *
     * @param Request $request
     *
     * @Route("/bulk/crud/{id}", name="bulk_order_db_crud", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkCrudInDBAction(Request $request, OrderUser $storeOrder)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $token = $request->request->get('token');
            $quantity = intval($request->request->get('quantity'));
            $product = intval($request->request->get('product'));

            if (!$this->isCsrfTokenValid('order', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }
            if ($quantity >= 1 ){
              if ($this->getEntityManager()->updateOrderLine($storeOrder , $quantity , $product)) {
                $msg = $this->get('translator')->trans('order_items.new.success');
              }else{
                $msg = $this->get('translator')->trans('order_items.edit.success');
              }
              return new Response(json_encode(array('msg' => $msg )), 200);
        }

      return new Response('Bad Request', 400);
      }
    }

    /**
     * Get orderLine Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getOrderLineEntityManager()
    {
        if (!$this->has('sms.datatable.order_line')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.order_line');
    }
}
