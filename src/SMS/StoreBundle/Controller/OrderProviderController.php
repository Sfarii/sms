<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\OrderProvider;
use SMS\StoreBundle\Entity\ProductType;
use SMS\StoreBundle\Form\OrderProviderType;
use SMS\StoreBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * OrderProvider controller.
 *
 * @Route("orderprovider")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class OrderProviderController extends BaseController
{
    /**
     * Lists all orderProvider entities.
     *
     * @Route("/", name="orderprovider_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:orderprovider:index.html.twig")
     */
    public function indexAction()
    {
        $orderProviders = $this->getOrderProviderEntityManager();
        $orderProviders->buildDatatable();

        return array('orderProviders' => $orderProviders);
    }
  /**
     * Lists all orderProvider entities.
     *
     * @Route("/results", name="orderprovider_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $orderProviders = $this->getOrderProviderEntityManager();
        $orderProviders->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($orderProviders);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('order_provider.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Finds and displays a order_provider entity.
     *
     * @Route("/pdf/{id}", name="order_provider_pdf", options={"expose"=true})
     * @Method("GET")
     */
    public function pdfAction(OrderProvider $orderProvider)
    {
      $html = $this->renderView('SMSStoreBundle:pdf:order_provider.html.twig', array(
          'order_provider'  => $orderProvider
      ));

      return new Response(
          $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'application/pdf',
              'Content-Disposition'   => sprintf('attachment; filename="%s.pdf"' , $orderProvider->getReference())
          )
      );
    }

    /**
     * Finds and displays a order_provider entity.
     *
     * @Route("/img/{id}", name="order_provider_img", options={"expose"=true})
     * @Method("GET")
     */
    public function imgAction(OrderProvider $orderProvider)
    {
      $html = $this->renderView('SMSStoreBundle:pdf:order_provider.html.twig', array(
          'order_provider'  => $orderProvider
      ));

      return new Response(
          $this->get('knp_snappy.image')->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'image/jpg',
              'Content-Disposition'   => sprintf('attachment; filename="%s.jpg"' , $orderProvider->getReference())
          )
      );
    }

    /**
     * Finds and displays a orderProvider entity.
     *
     * @Route("/{id}", name="orderprovider_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(OrderProvider $orderProvider)
    {
        $lineOrder = $this->getOrderLineEntityManager();
        $lineOrder->buildDatatable(array('id' => $orderProvider->getId()));

        return $this->render('SMSStoreBundle:orderprovider:show.html.twig', array(
            'orderProvider' => $orderProvider,
            'lineOrder' => $lineOrder
        ));
    }

    /**
     * edit and displays a orderProvider entity.
     *
     * @Route("edit/{id}", name="orderprovider_edit", options={"expose"=true})
     * @Method({ "GET" , "POST"})
     */
    public function editAction(Request $request, OrderProvider $orderProvider)
    {
        $editForm = $this->createForm(OrderProviderType::class, $orderProvider, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($orderProvider);
            $this->flashSuccessMsg('orderProvider.edit.success');
            return $this->redirectToRoute('orderprovider_show' , array('id' => $orderProvider->getId()));
        }

        $products = $this->getProductEntityManager();
        $products->buildDatatable();

        return $this->render('SMSStoreBundle:orderprovider:edit.html.twig', array(
            'orderProvider' => $orderProvider,
            'products' => $products,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="orderprovider_bulk_delete")
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
                $this->getEntityManager()->deleteAll(orderProvider::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('orderProvider.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('orderProvider.delete.success'), 200);
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
    protected function getOrderLineEntityManager()
    {
        if (!$this->has('sms.datatable.store_order_line')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.store_order_line');
    }

    /**
     * Get orderProvider Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getOrderProviderEntityManager()
    {
        if (!$this->has('sms.datatable.orderProvider')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.orderProvider');
    }

    /**
     * Get product Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getProductEntityManager()
    {
        if (!$this->has('sms.datatable.form.product')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.form.product');
    }
}
