<?php

namespace SMS\PaymentBundle\Controller;

use SMS\PaymentBundle\Entity\Payment;
use SMS\PaymentBundle\Entity\Registration;
use SMS\UserBundle\Entity\Student;
use SMS\PaymentBundle\Form\PaymentType;
use SMS\PaymentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SMS\PaymentBundle\Form\SearchType;

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
    public function indexAction(Request $request)
    {
        $form = $this->createForm(SearchType::class,null, array('method' => 'GET', 'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $this->getEntityManager()->getRegistredStudent(Student::class , $form), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/
        );
        $sort = $request->query->get('sort', 'empty');
        if ($sort == "empty"){
          $pagination->setParam('sort', 'student.firstName');
          $pagination->setParam('direction', 'asc');
        }
        // parameters to template
        return array('pagination' => $pagination , 'form' => $form->createView());
    }

    /**
     * Finds and displays a payment entity.
     *
     * @Route("/pdf/{id}", name="payment_pdf", options={"expose"=true})
     * @Method("GET")
     */
    public function pdfAction(Payment $payment)
    {
      $html = $this->renderView('SMSPaymentBundle:pdf:payment.html.twig', array(
          'payment'  => $payment
      ));

      return new Response(
          $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'application/pdf',
              'Content-Disposition'   => sprintf('attachment; filename="%.pdf"' , $payment->getStudent())
          )
      );
    }

    /**
     * Creates a new payment entity.
     *
     * @Route("/new/{id}", name="payment_new")
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:payment:new.html.twig")
     */
    public function newAction(Student $student , Request $request)
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment, array('student' => $student , 'establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            if ($this->getEntityManager()->addPayment($payment , $this->getUser())){
              $this->flashSuccessMsg('payment.add.success');
              return $this->redirectToRoute('user_payment_show', array('id' => $student->getId()));
            }
            $this->flashErrorMsg('payment.add.error');
        }

        return array(
            'payment' => $payment,
            'student' => $student,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a registration entity.
     *
     * @Route("/payment/user/{id}", name="user_payment_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Student $student)
    {
        $payments = $this->getPaymentEntityManager();
        $payments->buildDatatable(array('id' => $student->getId()));

        $registration = $this->getRegistrationEntityManager();
        $registration->buildDatatable(array('id' => $student->getId()));

        return $this->render('SMSPaymentBundle:student:show.html.twig', array(
            'student' => $student,
            'payments' => $payments,
            'registration' => $registration
        ));
    }

    /**
     * Lists all payment entities.
     *
     * @Route("/results/payment/{id}", name="payment_results")
     * @Method("GET")
     * @return Response
     */
    public function indexPaymentResultsAction(Student $student)
    {
        $payments = $this->getPaymentEntityManager();
        $payments->buildDatatable(array('id' => $student->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($payments);
        $function = function($qb) use ( $student)
        {
            $qb->join('payment.student', 'student')
                ->andWhere('student.id = :student')
                ->setParameter('student', $student->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
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
        $editForm = $this->createForm(PaymentType::class, $payment, array('student' => $payment->getStudent() ,'establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($payment);
            $this->flashSuccessMsg('payment.edit.success');
            return $this->redirectToRoute('user_payment_show', array('id' => $payment->getStudent()->getId()));
        }

        return array(
            'payment' => $payment,
            'student' => $payment->getStudent(),
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
    }

    /**
     * Get paginator Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     * @throws \NotFoundException
     */
    protected function getPaginator()
    {
        if (!$this->has('knp_paginator')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('knp_paginator');
    }

    /**
     * Get registration Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getRegistrationEntityManager()
    {
        if (!$this->has('sms.datatable.registration')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.registration');
    }
}
