<?php

namespace SMS\StoreBundle\Controller;

use SMS\StoreBundle\Entity\Product;
use SMS\StoreBundle\Form\ProductType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Product controller.
 *
 * @Route("product")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StoreBundle\Controller
 *
 */
class ProductController extends BaseController
{
    /**
     * Lists all product entities.
     *
     * @Route("/", name="product_index")
     * @Method("GET")
     * @Template("SMSStoreBundle:product:index.html.twig")
     */
    public function indexAction()
    {
        $products = $this->getProductEntityManager();
        $products->buildDatatable();

        return array('products' => $products);
    }
  /**
     * Lists all product entities.
     *
     * @Route("/results", name="product_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $products = $this->getProductEntityManager();
        $products->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($products);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('product.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * Creates a new product entity.
     *
     * @Route("/new", name="product_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:product:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product , array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($product , $this->getUser());
            $this->flashSuccessMsg('product.add.success');
            return $this->redirectToRoute('product_index');
        }

        return array(
            'product' => $product,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/{id}", name="product_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        return $this->render('SMSStoreBundle:product:show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/{id}/edit", name="product_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStoreBundle:product:edit.html.twig")
     */
    public function editAction(Request $request, Product $product)
    {
        $editForm = $this->createForm(ProductType::class, $product, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($product);
            $this->flashSuccessMsg('product.edit.success');
            return $this->redirectToRoute('product_index');
        }

        return array(
            'product' => $product,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="product_bulk_delete")
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
                $this->getEntityManager()->deleteAll(product::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('product.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('product.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a product entity.
     *
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($product);
            $this->flashSuccessMsg('product.delete.one.success');
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
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
        if (!$this->has('sms.datatable.product')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.product');
    }}
