<?php

namespace SMS\PaymentBundle\Controller;

use SMS\PaymentBundle\Entity\RegistrationType;
use SMS\PaymentBundle\Form\RegistrationTypeType;
use SMS\PaymentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registrationtype controller.
 *
 * @Route("registrationtype")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\PaymentBundle\Controller
 *
 */
class RegistrationTypeController extends BaseController
{
    /**
     * Lists all registrationType entities.
     *
     * @Route("/", name="registrationtype_index")
     * @Method("GET")
     * @Template("SMSPaymentBundle:registrationtype:index.html.twig")
     */
    public function indexAction()
    {
        $registrationTypes = $this->getRegistrationTypeEntityManager();
        $registrationTypes->buildDatatable();

        return array('registrationTypes' => $registrationTypes);
    } /**
     * Lists all registrationType entities.
     *
     * @Route("/results", name="registrationtype_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $registrationTypes = $this->getRegistrationTypeEntityManager();
        $registrationTypes->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($registrationTypes);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('registration_type.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }
    /**
     * Creates a new registrationType entity.
     *
     * @Route("/new", name="registrationtype_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:registrationtype:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $registrationType = new Registrationtype();
        $form = $this->createForm(RegistrationTypeType::class, $registrationType, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($registrationType , $this->getUser());
            $this->flashSuccessMsg('registrationType.add.success');
            return $this->redirectToRoute('registrationtype_index');
        }

        return array(
            'registrationType' => $registrationType,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a registrationType entity.
     *
     * @Route("/{id}", name="registrationtype_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(RegistrationType $registrationType)
    {
        $deleteForm = $this->createDeleteForm($registrationType);

        return $this->render('SMSPaymentBundle:registrationtype:show.html.twig', array(
            'registrationType' => $registrationType,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing registrationType entity.
     *
     * @Route("/{id}/edit", name="registrationtype_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:registrationtype:edit.html.twig")
     */
    public function editAction(Request $request, RegistrationType $registrationType)
    {
        $editForm = $this->createForm(RegistrationTypeType::class, $registrationType, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($registrationType);
            $this->flashSuccessMsg('registrationType.edit.success');
            return $this->redirectToRoute('registrationtype_index');
        }

        return array(
            'registrationType' => $registrationType,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="registrationtype_bulk_delete")
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
                $this->getEntityManager()->deleteAll(registrationType::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('registrationType.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('registrationType.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a registrationType entity.
     *
     * @Route("/{id}", name="registrationtype_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, RegistrationType $registrationType)
    {
        $form = $this->createDeleteForm($registrationType)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($registrationType);
            $this->flashSuccessMsg('registrationType.delete.one.success');
        }

        return $this->redirectToRoute('registrationtype_index');
    }

    /**
     * Creates a form to delete a registrationType entity.
     *
     * @param RegistrationType $registrationType The registrationType entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RegistrationType $registrationType)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registrationtype_delete', array('id' => $registrationType->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get registrationType Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getRegistrationTypeEntityManager()
    {
        if (!$this->has('sms.datatable.registrationType')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.registrationType');
    }}
