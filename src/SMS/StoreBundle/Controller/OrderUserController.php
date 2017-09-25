<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\OrderUser;
use SMS\StoreBundle\Form\OrderUserType;
use SMS\StoreBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\StoreBundle\Entity\Product;

/**
 * Orderuser controller.
 *
 * @Route("orderuser")
 * @Security("has_role('ROLE_ADMIN')")
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
     * Finds and displays a orderUser entity.
     *
     * @Route("/{id}", name="orderuser_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(OrderUser $orderUser)
    {
        $lineOrder = $this->getOrderLineEntityManager();

        $lineOrder->buildDatatable(array('id' => $orderUser->getId()));
        return $this->render('SMSStoreBundle:orderuser:show.html.twig', array(
            'orderUser' => $orderUser,
            'lineOrder' => $lineOrder
        ));
    }

    /**
     * Finds and displays a order provider entity.
     *
     * @Route("/pdf/{id}", name="order_user_pdf", options={"expose"=true})
     * @Method("GET")
     */
    public function pdfAction(OrderUser $orderUser)
    {
      $html = $this->renderView('SMSStoreBundle:pdf:order_user.html.twig', array(
          'order_user'  => $orderUser
      ));

      return new Response(
          $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'application/pdf',
              'Content-Disposition'   => sprintf('attachment; filename="%s.pdf"' , $orderUser->getReference())
          )
      );
    }

    /**
     * Finds and displays a order user entity.
     *
     * @Route("/img/{id}", name="order_user_img", options={"expose"=true})
     * @Method("GET")
     */
    public function imgAction(OrderUser $orderUser)
    {
      $html = $this->renderView('SMSStoreBundle:pdf:order_user.html.twig', array(
          'order_user'  => $orderUser
      ));

      return new Response(
          $this->get('knp_snappy.image')->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'image/jpg',
              'Content-Disposition'   => sprintf('attachment; filename="%s.jpg"' , $orderUser->getReference())
          )
      );
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
        $editForm = $this->createForm(OrderUserType::class, $orderUser, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            if ($this->getEntityManager()->updateUserOrder($orderUser)){
              $this->flashSuccessMsg('orderUser.edit.success');
            }else {
              $this->flashErrorMsg('orderUser.edit.fail');
            }
            return $this->redirectToRoute('orderuser_show' , array('id' => $orderUser->getId()));
        }

        $products = $this->getProductEntityManager();
        $products->buildDatatable();

        return array(
            'products' => $products,
            'orderUser' => $orderUser,
            'form' => $editForm->createView(),
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
    }

    /**
     * Get orderLine Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getOrderLineEntityManager()
    {
        if (!$this->has('sms.datatable.order_line')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.order_line');
    }

    /**
     * Get product Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getProductEntityManager()
    {
        if (!$this->has('sms.datatable.form.product')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.form.product');
    }
}
