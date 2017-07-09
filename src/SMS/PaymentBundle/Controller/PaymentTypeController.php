<?php

namespace SMS\PaymentBundle\Controller;

use SMS\PaymentBundle\Entity\Payment;
use SMS\PaymentBundle\Entity\PaymentType;
use SMS\PaymentBundle\Entity\Registration;
use SMS\PaymentBundle\Form\PaymentTypeType;
use SMS\PaymentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Paymenttype controller.
 *
 * @Route("paymenttype")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\PaymentBundle\Controller
 *
 */
class PaymentTypeController extends BaseController
{
    /**
     * Lists all paymentType entities.
     *
     * @Route("/", name="paymenttype_index")
     * @Method("GET")
     * @Template("SMSPaymentBundle:paymenttype:index.html.twig")
     */
    public function indexAction()
    {
        $paymentTypes = $this->getPaymentTypeEntityManager();
        $paymentTypes->buildDatatable();

        return array('paymentTypes' => $paymentTypes);
    }
    /**
     * Lists all paymentType entities.
     *
     * @Route("/results", name="paymenttype_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $paymentTypes = $this->getPaymentTypeEntityManager();
        $paymentTypes->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($paymentTypes);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('payment_type.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }
    /**
     * Creates a new paymentType entity.
     *
     * @Route("/new", name="paymenttype_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:paymenttype:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $paymentType = new Paymenttype();
        $form = $this->createForm(PaymentTypeType::class, $paymentType, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($paymentType , $this->getUser());
            $this->flashSuccessMsg('paymentType.add.success');
            return $this->redirectToRoute('paymenttype_index');
        }

        return array(
            'paymentType' => $paymentType,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a paymentType entity.
     *
     * @Route("/{id}", name="paymenttype_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:paymenttype:show.html.twig")
     */
    public function showAction(PaymentType $paymentType)
    {
        $paymentRepository = $this->getDoctrine()->getRepository(Payment::class);
        $registrationRepository = $this->getDoctrine()->getRepository(Registration::class);

        return array(
            'paymentType' => $paymentType,
            'paymentsInfo' => $paymentRepository->findByPayment($paymentType) ,
            'studentInfo' => $registrationRepository->findByPayment($paymentType) ,
            'chart' => $paymentRepository->findChartByPayment($paymentType)
        );
    }

    /**
     * Finds and return a paymentType entity.
     *
     * @Route("/json/{id}", name="paymenttype_json_show", options={"expose"=true})
     * @Method("GET")
     */
    public function jsonAction(Request $request,PaymentType $paymentType)
    {
      $response = new JsonResponse();
      $response->setData(array(
        'price' => $paymentType->getPrice()
      ));
      return $response;
    }

    /**
     * Displays a form to edit an existing paymentType entity.
     *
     * @Route("/{id}/edit", name="paymenttype_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:paymenttype:edit.html.twig")
     */
    public function editAction(Request $request, PaymentType $paymentType)
    {
        $editForm = $this->createForm(PaymentTypeType::class, $paymentType, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($paymentType);
            $this->flashSuccessMsg('paymentType.edit.success');
            return $this->redirectToRoute('paymenttype_index');
        }

        return array(
            'paymentType' => $paymentType,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="paymenttype_bulk_delete")
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
                $this->getEntityManager()->deleteAll(paymentType::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('paymentType.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('paymentType.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a paymentType entity.
     *
     * @Route("/{id}", name="paymenttype_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PaymentType $paymentType)
    {
        $form = $this->createDeleteForm($paymentType)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($paymentType);
            $this->flashSuccessMsg('paymentType.delete.one.success');
        }

        return $this->redirectToRoute('paymenttype_index');
    }

    /**
     * Creates a form to delete a paymentType entity.
     *
     * @param PaymentType $paymentType The paymentType entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PaymentType $paymentType)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('paymenttype_delete', array('id' => $paymentType->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get paymentType Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getPaymentTypeEntityManager()
    {
        if (!$this->has('sms.datatable.paymentType')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.paymentType');
    }}
