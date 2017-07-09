<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\ProductType;
use SMS\StoreBundle\Form\ProductTypeType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Producttype controller.
 *
 * @Route("producttype")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class ProductTypeController extends BaseController
{
    /**
     * Lists all productType entities.
     *
     * @Route("/", name="producttype_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:producttype:index.html.twig")
     */
    public function indexAction()
    {
        $productTypes = $this->getProductTypeEntityManager();
        $productTypes->buildDatatable();

        return array('productTypes' => $productTypes);
    }
  /**
     * Lists all productType entities.
     *
     * @Route("/results", name="producttype_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $productTypes = $this->getProductTypeEntityManager();
        $productTypes->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($productTypes);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('product_type.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new productType entity.
     *
     * @Route("/new", name="producttype_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:producttype:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $productType = new Producttype();
        $form = $this->createForm(ProductTypeType::class, $productType, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($productType , $this->getUser());
            $this->flashSuccessMsg('productType.add.success');
            return $this->redirectToRoute('producttype_index');
        }

        return array(
            'productType' => $productType,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing productType entity.
     *
     * @Route("/{id}/edit", name="producttype_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:producttype:edit.html.twig")
     */
    public function editAction(Request $request, ProductType $productType)
    {
        $editForm = $this->createForm(ProductTypeType::class, $productType, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($productType);
            $this->flashSuccessMsg('productType.edit.success');
            return $this->redirectToRoute('producttype_index');
        }

        return array(
            'productType' => $productType,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="producttype_bulk_delete")
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
                $this->getEntityManager()->deleteAll(productType::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('productType.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('productType.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Get productType Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getProductTypeEntityManager()
    {
        if (!$this->has('sms.datatable.productType')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.productType');
    }}
