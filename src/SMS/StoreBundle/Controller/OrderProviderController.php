<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\OrderProvider;
use SMS\StoreBundle\Entity\ProductType;
use SMS\StoreBundle\Form\OrderProviderType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * OrderProvider controller.
 *
 * @Route("orderprovider")
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
     * Creates a new orderProvider entity.
     *
     * @Route("/new", name="orderprovider_new", options={"expose"=true})
     * @Method({"GET" , "POST"})
     * @Template("SMSStoreBundle:orderprovider:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $orderProvider = new OrderProvider();
        $form = $this->createForm(OrderProviderType::class, $orderProvider, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getStoreManager()->addOrderProvider($orderProvider , $this->getUser(), $form->getExtraData());
            $this->flashSuccessMsg('orderProvider.add.success');
            return $this->redirectToRoute('orderprovider_index');
        }

        return array(
            'productTypes' => $this->getDoctrine()->getRepository(ProductType::class)->findByEstablishment($this->getUser()->getEstablishment()),
            'orderProvider' => $orderProvider,
            'form' => $form->createView(),
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
        $deleteForm = $this->createDeleteForm($orderProvider);

        return $this->render('SMSStoreBundle:orderprovider:show.html.twig', array(
            'orderProvider' => $orderProvider,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing orderProvider entity.
     *
     * @Route("/{id}/edit", name="orderprovider_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:orderprovider:edit.html.twig")
     */
    public function editAction(Request $request, OrderProvider $orderProvider)
    {
        $editForm = $this->createForm(OrderProviderType::class, $orderProvider, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($orderProvider);
            $this->flashSuccessMsg('orderProvider.edit.success');
            return $this->redirectToRoute('orderprovider_index');
        }

        return array(
            'orderProvider' => $orderProvider,
            'form' => $editForm->createView(),
        );
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
     * Deletes a orderProvider entity.
     *
     * @Route("/{id}", name="orderprovider_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrderProvider $orderProvider)
    {
        $form = $this->createDeleteForm($orderProvider)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($orderProvider);
            $this->flashSuccessMsg('orderProvider.delete.one.success');
        }

        return $this->redirectToRoute('orderprovider_index');
    }

    /**
     * Creates a form to delete a orderProvider entity.
     *
     * @param OrderProvider $orderProvider The orderProvider entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrderProvider $orderProvider)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orderprovider_delete', array('id' => $orderProvider->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
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
    }}
