<?php

namespace SMS\PaymentBundle\Controller;

use SMS\PaymentBundle\Entity\Payment;
use SMS\PaymentBundle\Form\PaymentType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Payment controller.
 *
 * @Route("payment")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\PaymentBundle\Controller
 *
 */
class PaymentController extends BaseController
{
    /**
     * Lists all payment entities.
     *
     * @Route("/", name="payment_index")
     * @Method("GET")
     * @Template("SMSPaymentBundle:payment:index.html.twig")
     */
    public function indexAction()
    {
        $payments = $this->getPaymentEntityManager();
        $payments->buildDatatable();

        return array('payments' => $payments);
    }

    /**
     * Lists all payment entities.
     *
     * @Route("/results", name="payment_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $payments = $this->getPaymentEntityManager();
        $payments->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($payments);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('payment.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }
    /**
     * Creates a new payment entity.
     *
     * @Route("/new", name="payment_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:payment:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment, array( 'establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            if ($this->getEntityManager()->addPayment($payment , $this->getUser())){
              $this->flashSuccessMsg('payment.add.success');
              return $this->redirectToRoute('payment_index');
            }
            $this->flashErrorMsg('payment.add.error');
        }

        return array(
            'payment' => $payment,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a payment entity.
     *
     * @Route("/{id}", name="payment_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Payment $payment)
    {
        $deleteForm = $this->createDeleteForm($payment);

        return $this->render('SMSPaymentBundle:payment:show.html.twig', array(
            'payment' => $payment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing payment entity.
     *
     * @Route("/{id}/edit", name="payment_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:payment:edit.html.twig")
     */
    public function editAction(Request $request, Payment $payment)
    {
        $editForm = $this->createForm(PaymentType::class, $payment, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($payment);
            $this->flashSuccessMsg('payment.edit.success');
            return $this->redirectToRoute('payment_index');
        }

        return array(
            'payment' => $payment,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="payment_bulk_delete")
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
                $this->getEntityManager()->deleteAll(payment::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('payment.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('payment.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a payment entity.
     *
     * @Route("/{id}", name="payment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Payment $payment)
    {
        $form = $this->createDeleteForm($payment)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($payment);
            $this->flashSuccessMsg('payment.delete.one.success');
        }

        return $this->redirectToRoute('payment_index');
    }

    /**
     * Creates a form to delete a payment entity.
     *
     * @param Payment $payment The payment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Payment $payment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('payment_delete', array('id' => $payment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get payment Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getPaymentEntityManager()
    {
        if (!$this->has('sms.datatable.payment')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.payment');
    }}
