<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\Purchase;
use SMS\StoreBundle\Entity\OrderLine;
use SMS\StoreBundle\Entity\PurchaseLine;
use SMS\StoreBundle\Entity\StoreOrder;
use SMS\StoreBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * PurchaseProvider controller.
 *
 * @Route("purchase_line")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class PurchaseLineController extends BaseController
{
    /**
     * Lists all provider entities.
     *
     * @Route("/purchase_results/{id}", name="purchase_line_results")
     * @Method("GET")
     * @return Response
     */
    public function indexPurchaseLineResultAction(Purchase $purchase)
    {
        $linePurchase = $this->getPurchaseLineEntityManager();
        $linePurchase->buildDatatable(array('id' => $purchase->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($linePurchase);
        $user = $this->getUser();
        $function = function($qb) use ($purchase)
        {
            $qb->join('purchase_line.purchase', 'purchase')
                ->andWhere('purchase.id = :purchase')
                ->setParameter('purchase', $purchase->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/purchase_bulk/delete", name="purchase_line_bulk_delete", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkPurchaseDeleteAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            try {
                $this->getEntityManager()->deletePurchaseLine($choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('purchase_items.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('purchase_items.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Bulk purshase Items actions.
     *
     * @param Request $request
     *
     * @Route("/bulk/crud", name="bulk_session_crud", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkCrudInSessionAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $token = $request->request->get('token');
            $quantity = intval($request->request->get('quantity'));
            $price = floatval($request->request->get('price'));
            $action = $request->request->get('action');
            $product = intval($request->request->get('product'));
            $data = $request->request->get('data');

            if (!$this->isCsrfTokenValid('purshase', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            switch ($action) {
              case 'new_edit':
                $session = $request->getSession();
                $purshaseItem = $session->get('_purshase', array());
                if ($quantity >= 1 ){
                  $purshaseItem = $this->getEntityManager()->updatePurchaseLineInSession($purshaseItem , $quantity , $price , $product);
                  $session->set('_purshase', $purshaseItem);
                  return new Response(json_encode(array('msg' => $this->get('translator')->trans('purchase_items.edit.success') )), 200);
                }
                break;
              case 'delete':
                $session = $request->getSession();
                $purshaseItem = $session->get('_purshase', array());
                $purshaseItem = $this->getEntityManager()->deletePurchaseLineInSession($purshaseItem , $data);
                $session->set('_purshase', $purshaseItem);
                return new Response(json_encode(array('msg' => $this->get('translator')->trans('purchase_items.delete.success') , 'num' => count($purshaseItem))), 200);
                break;

              default:
                return new Response('Bad Request', 400);
                break;
            }
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Creates a show purchase line entity.
     *
     * @Route("/line/show", name="purchase_line_session_show" , options={"expose"=true})
     * @Method("GET")
     * @Template("SMSStoreBundle:purchase:purchase_line_session.html.twig")
     */
    public function showPurchaseLineSessionAction(Request $request)
    {
          $purshaseItems = $request->getSession()->get('_purshase', array());
          $totalPrice = array_sum(array_map(function ($value){return $value['price'] * $value['quantity'];}, $purshaseItems));
          $totalQuantity = array_sum(array_map(function ($value){return $value['quantity'];}, $purshaseItems));

        return array(  'result' => $purshaseItems , 'totalPrice' => $totalPrice , 'totalQuantity' => $totalQuantity);
    }

    /**
     * Creates a show purchase line entity.
     *
     * @Route("/line/show/{id}", name="purchase_line_db_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSStoreBundle:purchase:purchase_line_db.html.twig")
     */
    public function showPurchaseLineDBAction(Request $request , Purchase $purchase)
    {
        $purshaseItems = $purchase->getPurchaseLines();
        $totalPrice = array_sum(array_map(function ($value){return $value->getPrice() * $value->getQuantity();}, $purshaseItems->toArray()));
        $totalQuantity = array_sum(array_map(function ($value){return $value->getQuantity();}, $purshaseItems->toArray()));

        return array(  'result' => $purshaseItems , 'totalPrice' => $totalPrice , 'totalQuantity' => $totalQuantity);
    }

    /**
     * Bulk purshase Items actions.
     *
     * @param Request $request
     *
     * @Route("/bulk/crud/{id}", name="bulk_db_crud", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkCrudInDBAction(Request $request, Purchase $purchase)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $token = $request->request->get('token');
            $quantity = intval($request->request->get('quantity'));
            $price = floatval($request->request->get('price'));
            $action = $request->request->get('action');
            $product = intval($request->request->get('product'));
            $data = $request->request->get('data');

            if (!$this->isCsrfTokenValid('purshase', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            switch ($action) {
              case 'new_edit':
                if ($quantity >= 1 ){
                  if ($this->getEntityManager()->updatePurchaseLine($purchase , $quantity , $price , $product)) {
                    $msg = $this->get('translator')->trans('purchase_items.edit.success');
                  }else{
                    $msg = $this->get('translator')->trans('purchase_items.new.success');
                  }
                  return new Response(json_encode(array('msg' => $msg )), 200);
                }
                break;
              case 'delete':
                $this->getEntityManager()->deleteAll(PurchaseLine::class ,$data);
                return new Response(json_encode(array('msg' => $this->get('translator')->trans('purchase_items.delete.success') , 'num' => count($purshaseItem))), 200);
                break;

              default:
                return new Response('Bad Request', 400);
                break;
            }
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Get orderLine Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getPurchaseLineEntityManager()
    {
        if (!$this->has('sms.datatable.purchase_line')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.purchase_line');
    }
}
