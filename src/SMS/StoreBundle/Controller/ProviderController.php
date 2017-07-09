<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\Provider;
use SMS\StoreBundle\Form\ProviderType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * Provider controller.
 *
 * @Route("provider")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class ProviderController extends BaseController
{
    /**
     * Lists all provider entities.
     *
     * @Route("/", name="provider_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:provider:index.html.twig")
     */
    public function indexAction()
    {
        $providers = $this->getProviderEntityManager();
        $providers->buildDatatable();

        return array('providers' => $providers);
    }
  /**
     * Lists all provider entities.
     *
     * @Route("/results", name="provider_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $providers = $this->getProviderEntityManager();
        $providers->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($providers);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('provider.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new provider entity.
     *
     * @Route("/new", name="provider_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:provider:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $provider = new Provider();
        $form = $this->createForm(ProviderType::class, $provider, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($provider , $this->getUser());
            $this->flashSuccessMsg('provider.add.success');
            return $this->redirectToRoute('provider_index');
        }

        return array(
            'provider' => $provider,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a provider entity.
     *
     * @Route("/{id}", name="provider_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Provider $provider)
    {
        $deleteForm = $this->createDeleteForm($provider);

        return $this->render('SMSStoreBundle:provider:show.html.twig', array(
            'provider' => $provider,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing provider entity.
     *
     * @Route("/{id}/edit", name="provider_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:provider:edit.html.twig")
     */
    public function editAction(Request $request, Provider $provider)
    {
        $editForm = $this->createForm(ProviderType::class, $provider, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($provider);
            $this->flashSuccessMsg('provider.edit.success');
            return $this->redirectToRoute('provider_index');
        }

        return array(
            'provider' => $provider,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="provider_bulk_delete")
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
                $this->getEntityManager()->deleteAll(provider::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('provider.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('provider.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a provider entity.
     *
     * @Route("/{id}", name="provider_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Provider $provider)
    {
        $form = $this->createDeleteForm($provider)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($provider);
            $this->flashSuccessMsg('provider.delete.one.success');
        }

        return $this->redirectToRoute('provider_index');
    }

    /**
     * Creates a form to delete a provider entity.
     *
     * @param Provider $provider The provider entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Provider $provider)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('provider_delete', array('id' => $provider->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get provider Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getProviderEntityManager()
    {
        if (!$this->has('sms.datatable.provider')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.provider');
    }}
