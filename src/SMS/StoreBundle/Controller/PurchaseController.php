<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\Purchase;
use SMS\StoreBundle\Entity\Product;
use SMS\StoreBundle\Form\PurchaseType;
use SMS\StoreBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SMS\StoreBundle\Entity\PurchaseLine;

/**
 * Purchase controller.
 *
 * @Route("purchase")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class PurchaseController extends BaseController
{
    /**
     * Lists all purchase entities.
     *
     * @Route("/", name="purchase_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:purchase:index.html.twig")
     */
    public function indexAction()
    {
        $deliveries = $this->getPurchaseEntityManager();
        $deliveries->buildDatatable();

        return array('deliveries' => $deliveries);
    }

    /**
     * Finds and displays a Purchase entity.
     *
     * @Route("/pdf/{id}", name="purchase_pdf", options={"expose"=true})
     * @Method("GET")
     */
    public function pdfAction(Purchase $purchase)
    {
      $html = $this->renderView('SMSStoreBundle:pdf:purchase.html.twig', array(
          'purchase'  => $purchase
      ));

      return new Response(
          $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'application/pdf',
              'Content-Disposition'   => sprintf('attachment; filename="%s.pdf"' , $purchase->getReference())
          )
      );
    }

    /**
     * Finds and displays a Purchase entity.
     *
     * @Route("/img/{id}", name="purchase_img", options={"expose"=true})
     * @Method("GET")
     */
    public function imgAction(Purchase $purchase)
    {
      $html = $this->renderView('SMSStoreBundle:pdf:purchase.html.twig', array(
          'purchase'  => $purchase
      ));

      return new Response(
          $this->get('knp_snappy.image')->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'image/jpg',
              'Content-Disposition'   => sprintf('attachment; filename="%s.jpg"' , $purchase->getReference())
          )
      );
    }

  /**
     * Lists all purchase entities.
     *
     * @Route("/results", name="purchase_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $deliveries = $this->getPurchaseEntityManager();
        $deliveries->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($deliveries);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('purchase.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new purchase entity.
     *
     * @Route("/new", name="purchase_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:purchase:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $purchase = new Purchase();
        $form = $this->createForm(PurchaseType::class, $purchase, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        $purshaseItems = $request->getSession()->get('_purshase', array());

        if (!empty($purshaseItems) && $form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->newPurchase($purchase ,$purshaseItems , $this->getUser());
            $this->flashSuccessMsg('purchase.add.success');
            return $this->redirectToRoute('purchase_index');
        }



        $products = $this->getProductEntityManager();
        $products->buildDatatable();

        return array(
            'products' => $products,
            'purchase' => $purchase,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a purchase entity.
     *
     * @Route("/{id}", name="purchase_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Purchase $purchase)
    {
        $lineOrder = $this->getOrderLineEntityManager();
        $lineOrder->buildDatatable(array('id' => $purchase->getId()));

        return $this->render('SMSStoreBundle:purchase:show.html.twig', array(
            'purchase' => $purchase,
            'lineOrder' => $lineOrder
        ));
    }

    /**
     * Displays a form to edit an existing purchase entity.
     *
     * @Route("/{id}/edit", name="purchase_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:purchase:edit.html.twig")
     */
    public function editAction(Request $request, Purchase $purchase)
    {
        $editForm = $this->createForm(PurchaseType::class, $purchase, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
          if ($this->getEntityManager()->updatePurchase($purchase)){
            $this->flashSuccessMsg('purchase.edit.success');
          }else{
            $this->flashSuccessMsg('purchase.edit.fail');
          }
          return $this->redirectToRoute('purchase_show' , array('id' => $purchase->getId()));
        }

        $products = $this->getProductEntityManager();
        $products->buildDatatable();

        return array(
            'products' => $products,
            'purchase' => $purchase,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="purchase_bulk_delete")
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
                $this->getEntityManager()->deleteAll(purchase::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('purchase.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('purchase.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Get purchase Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getPurchaseEntityManager()
    {
        if (!$this->has('sms.datatable.purchase')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.purchase');
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

    /**
     * Get orderLine Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getOrderLineEntityManager()
    {
        if (!$this->has('sms.datatable.purchase_line')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.purchase_line');
    }
}
