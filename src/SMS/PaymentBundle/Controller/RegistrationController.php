<?php

namespace SMS\PaymentBundle\Controller;

use SMS\PaymentBundle\Entity\Registration;
use SMS\PaymentBundle\Form\RegistrationType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registration controller.
 *
 * @Route("registration")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\PaymentBundle\Controller
 *
 */
class RegistrationController extends BaseController
{
    /**
     * Lists all registration entities.
     *
     * @Route("/", name="registration_index")
     * @Method("GET")
     * @Template("SMSPaymentBundle:registration:index.html.twig")
     */
    public function indexAction()
    {
        $registrations = $this->getRegistrationEntityManager();
        $registrations->buildDatatable();

        return array('registrations' => $registrations);
    }

    /**
     * Lists all registration entities.
     *
     * @Route("/results", name="registration_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $registrations = $this->getRegistrationEntityManager();
        $registrations->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($registrations);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('registration.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }
    /**
     * Creates a new registration entity.
     *
     * @Route("/new", name="registration_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:registration:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $registration = new Registration();
        $form = $this->createForm(RegistrationType::class, $registration, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($registration , $this->getUser());
            $this->flashSuccessMsg('registration.add.success');
            return $this->redirectToRoute('registration_index');
        }

        return array(
            'registration' => $registration,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a registration entity.
     *
     * @Route("/{id}", name="registration_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Registration $registration)
    {
        $deleteForm = $this->createDeleteForm($registration);

        return $this->render('SMSPaymentBundle:registration:show.html.twig', array(
            'registration' => $registration,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing registration entity.
     *
     * @Route("/{id}/edit", name="registration_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:registration:edit.html.twig")
     */
    public function editAction(Request $request, Registration $registration)
    {
        $editForm = $this->createForm(RegistrationType::class, $registration, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($registration);
            $this->flashSuccessMsg('registration.edit.success');
            return $this->redirectToRoute('registration_index');
        }

        return array(
            'registration' => $registration,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="registration_bulk_delete")
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
                $this->getEntityManager()->deleteAll(registration::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('registration.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('registration.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a registration entity.
     *
     * @Route("/{id}", name="registration_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Registration $registration)
    {
        $form = $this->createDeleteForm($registration)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($registration);
            $this->flashSuccessMsg('registration.delete.one.success');
        }

        return $this->redirectToRoute('registration_index');
    }

    /**
     * Creates a form to delete a registration entity.
     *
     * @param Registration $registration The registration entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Registration $registration)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registration_delete', array('id' => $registration->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
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
    }}
