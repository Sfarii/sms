<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\Product;
use SMS\StoreBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\StoreBundle\Form\SearchType;
use SMS\StoreBundle\Form\OrderType;

/**
 * Provider controller.
 *
 * @Route("shop")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class ShopController extends BaseController
{
    /**
     * shop statistics.
     *
     * @Route("/statistics", name="shop_statistics_index")
     * @Method({"GET" , "POST"})
     * @Template("SMSStoreBundle:shop:statistics.html.twig")
     */
    public function shopStatisticsAction(Request $request)
    {
        $establishment = $this->getUser()->getEstablishment();


        // parameters to template
        return $this->getEntityManager()->shopStatistics($establishment);
    }
    /**
     * Lists all product entities.
     *
     * @Route("/", name="shop_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:shop:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(SearchType::class,null, array('method' => 'GET', 'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $this->getEntityManager()->getAllActiveProduct($form , $this->getUser()->getEstablishment()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/,
            array('wrap-queries'=>true)
        );
        $sort = $request->query->get('sort', 'empty');
        if ($sort == "empty"){
          $pagination->setParam('sort', 'product.productName');
          $pagination->setParam('direction', 'asc');
        }
        // parameters to template
        return array('pagination' => $pagination , 'form' => $form->createView());
    }

    /**
     * Lists all product entities.
     *
     * @Route("/cart", name="cart_index")
     * @Method({"GET" , "POST"})
     * @Template("SMSStoreBundle:shop:cart.html.twig")
     */
    public function cartAction(Request $request)
    {
        $cart = $request->getSession()->get('_cart', array());
        $result = array_map(function ($product) use ($cart){$product->setQuantity($cart[$product->getId()]);return $product;}, $this->getDoctrine()->getRepository(Product::class)->findAllProductByIds(array_keys($cart)));
        $totalPrice = array_sum(array_map(function ($product){return $product->getPrice() * $product->getQuantity();}, $result));
        $totalQuantity = array_sum(array_map(function ($product){return $product->getQuantity();}, $result));
        // form
        $form = $this->createForm(OrderType::class, null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->addOrder($form , $result , $this->getUser());
            return new Response($this->get('translator')->trans('order.new.success'), 200);
        }
        // parameters to template
        return array('result' => $result ,'form' => $form->createView() , 'totalPrice' => $totalPrice , 'totalQuantity' => $totalQuantity);
    }

    /**
     * Bulk New Cart action.
     *
     * @param Request $request
     *
     * @Route("/bulk/new/{id}", name="store_bulk_cart_new", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkNewCartAction(Request $request , Product $product)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $token = $request->request->get('token');
            $qte = intval($request->request->get('qte'));

            if (!$this->isCsrfTokenValid('storeCart', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            $session = $request->getSession();
            $cart = $session->get('_cart', array());
            if ($qte >= 1 && $qte <= $product->getStock()){
              if (array_key_exists($product->getId(),$cart)) {
                $msg = $this->get('translator')->trans('cart.edit.success');
              }else{
                $msg = $this->get('translator')->trans('cart.new.success');
              }
              $cart[$product->getId()] = $qte;
              $session->set('_cart', $cart);
              return new Response(json_encode(array('msg' => $msg , 'num' => count($cart))), 200);
            }
            return new Response(json_encode(array('msg' => $this->get('translator')->trans('cart.new.fail') , 'num' => count($cart))), 200);


        }

        return new Response('Bad Request', 400);
    }

    /**
     * Bulk New Cart action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="store_bulk_cart_delete", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkDeleteCartAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $token = $request->request->get('token');
            $data = $request->request->get('data');

            if (!$this->isCsrfTokenValid('storeCart', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            $session = $request->getSession();
            $cart = $session->get('_cart', array());
            foreach ($data as $id) {
              if (isset($cart[$id])){
                unset($cart[$id]);
              }
            }
            $session->set('_cart', $cart);
            return new Response(json_encode(array('msg' => $this->get('translator')->trans('cart.delete.success') , 'num' => count($cart))), 200);


        }

        return new Response('Bad Request', 400);
    }

}
