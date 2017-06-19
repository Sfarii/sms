<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\OrderUser;
use SMS\StoreBundle\Form\OrderUserType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Orderuser controller.
 *
 * @Route("orderuser")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class OrderUserController extends BaseController
{
    /**
     * Lists all orderUser entities.
     *
     * @Route("/", name="orderuser_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:orderuser:index.html.twig")
     */
    public function indexAction()
    {
        $orderUsers = $this->getOrderUserEntityManager();
        $orderUsers->buildDatatable();

        return array('orderUsers' => $orderUsers);
    }
  /**
     * Lists all orderUser entities.
     *
     * @Route("/results", name="orderuser_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $orderUsers = $this->getOrderUserEntityManager();
        $orderUsers->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($orderUsers);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('order_user.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new orderUser entity.
     *
     * @Route("/new", name="orderuser_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:orderuser:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $orderUser = new Orderuser();
        $form = $this->createForm(OrderUserType::class, $orderUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($orderUser , $this->getUser());
            $this->flashSuccessMsg('orderUser.add.success');
            return $this->redirectToRoute('orderuser_index');
        }

        return array(
            'orderUser' => $orderUser,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a orderUser entity.
     *
     * @Route("/{id}", name="orderuser_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(OrderUser $orderUser)
    {
        $deleteForm = $this->createDeleteForm($orderUser);

        return $this->render('SMSStoreBundle:orderuser:show.html.twig', array(
            'orderUser' => $orderUser,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing orderUser entity.
     *
     * @Route("/{id}/edit", name="orderuser_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:orderuser:edit.html.twig")
     */
    public function editAction(Request $request, OrderUser $orderUser)
    {
        $editForm = $this->createForm(OrderUserType::class, $orderUser)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($orderUser);
            $this->flashSuccessMsg('orderUser.edit.success');
            return $this->redirectToRoute('orderuser_index');
        }

        return array(
            'orderUser' => $orderUser,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="orderuser_bulk_delete")
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
                $this->getEntityManager()->deleteAll(orderUser::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('orderUser.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('orderUser.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a orderUser entity.
     *
     * @Route("/{id}", name="orderuser_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrderUser $orderUser)
    {
        $form = $this->createDeleteForm($orderUser)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($orderUser);
            $this->flashSuccessMsg('orderUser.delete.one.success');
        }

        return $this->redirectToRoute('orderuser_index');
    }

    /**
     * Creates a form to delete a orderUser entity.
     *
     * @param OrderUser $orderUser The orderUser entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrderUser $orderUser)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orderuser_delete', array('id' => $orderUser->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get orderUser Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getOrderUserEntityManager()
    {
        if (!$this->has('sms.datatable.orderUser')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.orderUser');
    }}
