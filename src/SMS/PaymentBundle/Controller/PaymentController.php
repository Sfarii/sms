<?php

namespace SMS\PaymentBundle\Controller;

use SMS\PaymentBundle\Entity\Payment;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SMS\PaymentBundle\Entity\PaymentType;
use SMS\UserBundle\Entity\Student;
use SMS\PaymentBundle\Form\PaymentType as PaymentTypeForm;
use SMS\PaymentBundle\Form\AjaxPaymentType;
use SMS\PaymentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SMS\PaymentBundle\Form\SearchType;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sg\DatatablesBundle\Datatable\Data\DatatableQuery;

/**
 * Payment controller.
 *
 * @Route("payment")
 * @Security("has_role('ROLE_ADMIN')")
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
            $this->getEntityManager()->getRegistredStudent($form , $this->getUser()->getEstablishment()), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            9/*limit per page*/,
            array('wrap-queries'=>true)
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
              'Content-Disposition'   => sprintf('attachment; filename="%s.pdf"' , $payment->getStudent())
          )
      );
    }

    /**
     * Creates a ajax new payment entity.
     *
     * @Route("/ajax/new/{id}/{paymentType_id}", name="ajax_payment_new")
     * @ParamConverter("paymentType", class="SMSPaymentBundle:PaymentType", options={"id" = "paymentType_id"})
     * @Method("POST")
     */
    public function newAjaxAction(Student $student , PaymentType $paymentType , Request $request)
    {
      $isAjax = $request->isXmlHttpRequest();

      if ($isAjax) {
          $token = $request->request->get('token');
          $month = $request->request->get('month');
          $price = $request->request->get('price');
          $action = $request->request->get('action');

          if (!$this->isCsrfTokenValid('payment', $token) || !in_array($month, range(1, 12)) || !in_array($action , array('info' , 'new'))) {
              throw new AccessDeniedException('The CSRF token OR The Action OR Month is invalid.');
          }
          if (0 === strcasecmp($action , 'info')) {
            $result = $this->getEntityManager()->getRegistredStudentByStudent($paymentType , $month, $student);
            return new Response(json_encode(array('result' => $result)), 200);
          }else{
            if ($this->getEntityManager()->newPayment($paymentType , $price , $month, $student , $this->getUser())){
              return new Response($this->get('translator')->trans('payment.new.success'), 200);
            }else{
              return new Response($this->get('translator')->trans('payment.new.fail'), 200);
            }
          }
          throw new \LogicException('Unreachable code');
      }

      return new Response('Bad Request', 400);
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
        $form = $this->createForm(PaymentTypeForm::class, $payment, array('student' => $student ))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            if ($this->getEntityManager()->addPayment($payment , $this->getUser())){
              $this->flashSuccessMsg('payment.add.success');
              $form = $this->createForm(PaymentTypeForm::class, $payment, array('student' => $student ));
            }else {
              $form->get('price')->addError(new FormError($this->get('translator')->trans('payment.add.fail')));
            }

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
     * @Route("/student/{id}", name="user_payment_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:payment:show.html.twig")
     */
    public function showAction(Student $student)
    {
        $payments = $this->getPaymentEntityManager();
        $payments->buildDatatable(array('id' => $student->getId()));
        $paymentsStats = $this->getEntityManager()->getStatsByStudent($student);
        return array(
            'student' => $student,
            'payments' => $payments,
            'stats' => $paymentsStats
        );
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
     * @Route("staistics", name="payment_staistics")
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:payment:staistics.html.twig")
     */
    public function staisticsAction(Request $request)
    {
      $paymentsStats = $this->getEntityManager()->getPaymentStats($this->getUser()->getEstablishment());

      return array(
        'payment' => $paymentsStats
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
}
