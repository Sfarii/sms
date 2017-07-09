<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\Delivery;
use SMS\StoreBundle\Form\DeliveryType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Delivery controller.
 *
 * @Route("delivery")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class DeliveryController extends BaseController
{
    /**
     * Lists all delivery entities.
     *
     * @Route("/", name="delivery_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:delivery:index.html.twig")
     */
    public function indexAction()
    {
        $deliveries = $this->getDeliveryEntityManager();
        $deliveries->buildDatatable();

        return array('deliveries' => $deliveries);
    }
  /**
     * Lists all delivery entities.
     *
     * @Route("/results", name="delivery_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $deliveries = $this->getDeliveryEntityManager();
        $deliveries->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($deliveries);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('delivery.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new delivery entity.
     *
     * @Route("/new", name="delivery_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:delivery:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $delivery = new Delivery();
        $form = $this->createForm(DeliveryType::class, $delivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($delivery , $this->getUser());
            $this->flashSuccessMsg('delivery.add.success');
            return $this->redirectToRoute('delivery_index');
        }

        return array(
            'delivery' => $delivery,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a delivery entity.
     *
     * @Route("/{id}", name="delivery_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Delivery $delivery)
    {
        $deleteForm = $this->createDeleteForm($delivery);

        return $this->render('SMSStoreBundle:delivery:show.html.twig', array(
            'delivery' => $delivery,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing delivery entity.
     *
     * @Route("/{id}/edit", name="delivery_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:delivery:edit.html.twig")
     */
    public function editAction(Request $request, Delivery $delivery)
    {
        $editForm = $this->createForm(DeliveryType::class, $delivery)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($delivery);
            $this->flashSuccessMsg('delivery.edit.success');
            return $this->redirectToRoute('delivery_index');
        }

        return array(
            'delivery' => $delivery,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="delivery_bulk_delete")
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
                $this->getEntityManager()->deleteAll(delivery::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('delivery.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('delivery.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a delivery entity.
     *
     * @Route("/{id}", name="delivery_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Delivery $delivery)
    {
        $form = $this->createDeleteForm($delivery)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($delivery);
            $this->flashSuccessMsg('delivery.delete.one.success');
        }

        return $this->redirectToRoute('delivery_index');
    }

    /**
     * Creates a form to delete a delivery entity.
     *
     * @param Delivery $delivery The delivery entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Delivery $delivery)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delivery_delete', array('id' => $delivery->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get delivery Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getDeliveryEntityManager()
    {
        if (!$this->has('sms.datatable.delivery')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.delivery');
    }}
