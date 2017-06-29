<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\Invoice;
use SMS\StoreBundle\Form\InvoiceType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 * @Route("invoice")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class InvoiceController extends BaseController
{
    /**
     * Lists all invoice entities.
     *
     * @Route("/", name="invoice_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:invoice:index.html.twig")
     */
    public function indexAction()
    {
        $invoices = $this->getInvoiceEntityManager();
        $invoices->buildDatatable();

        return array('invoices' => $invoices);
    }
  /**
     * Lists all invoice entities.
     *
     * @Route("/results", name="invoice_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $invoices = $this->getInvoiceEntityManager();
        $invoices->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($invoices);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('invoices.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new invoice entity.
     *
     * @Route("/new", name="invoice_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:invoice:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($invoice , $this->getUser());
            $this->flashSuccessMsg('invoice.add.success');
            return $this->redirectToRoute('invoice_index');
        }

        return array(
            'invoice' => $invoice,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a invoice entity.
     *
     * @Route("/{id}", name="invoice_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Invoice $invoice)
    {
        $deleteForm = $this->createDeleteForm($invoice);

        return $this->render('SMSStoreBundle:invoice:show.html.twig', array(
            'invoice' => $invoice,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing invoice entity.
     *
     * @Route("/{id}/edit", name="invoice_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:invoice:edit.html.twig")
     */
    public function editAction(Request $request, Invoice $invoice)
    {
        $editForm = $this->createForm(InvoiceType::class, $invoice)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($invoice);
            $this->flashSuccessMsg('invoice.edit.success');
            return $this->redirectToRoute('invoice_index');
        }

        return array(
            'invoice' => $invoice,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="invoice_bulk_delete")
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
                $this->getEntityManager()->deleteAll(invoice::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('invoice.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('invoice.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a invoice entity.
     *
     * @Route("/{id}", name="invoice_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Invoice $invoice)
    {
        $form = $this->createDeleteForm($invoice)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($invoice);
            $this->flashSuccessMsg('invoice.delete.one.success');
        }

        return $this->redirectToRoute('invoice_index');
    }

    /**
     * Creates a form to delete a invoice entity.
     *
     * @param Invoice $invoice The invoice entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Invoice $invoice)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('invoice_delete', array('id' => $invoice->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get invoice Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getInvoiceEntityManager()
    {
        if (!$this->has('sms.datatable.invoice')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.invoice');
    }}
