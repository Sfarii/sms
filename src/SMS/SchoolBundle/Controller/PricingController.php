<?php

namespace SMS\SchoolBundle\Controller;

use SMS\SchoolBundle\Entity\Pricing;
use SMS\SchoolBundle\Form\PricingType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Pricing controller.
 *
 * @Route("pricing")
 * @Security("has_role('ROLE_MANAGER')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\SchoolBundle\Controller
 *
 */
class PricingController extends BaseController
{
    /**
     * Lists all pricing entities.
     *
     * @Route("/", name="pricing_index")
     * @Method("GET")
     * @Template("SMSSchoolBundle:pricing:index.html.twig")
     */
    public function indexAction()
    {
        $pricings = $this->getPricingEntityManager();
        $pricings->buildDatatable();

        return array('pricings' => $pricings);
    } /**
     * Lists all pricing entities.
     *
     * @Route("/results", name="pricing_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $pricings = $this->getPricingEntityManager();
        $pricings->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($pricings);

        return $query->getResponse();
    }
    /**
     * Creates a new pricing entity.
     *
     * @Route("/new", name="pricing_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:pricing:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $pricing = new Pricing();
        $form = $this->createForm(PricingType::class, $pricing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($pricing , $this->getUser());
            $this->flashSuccessMsg('pricing.add.success');
            return $this->redirectToRoute('pricing_index');
        }

        return array(
            'pricing' => $pricing,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a pricing entity.
     *
     * @Route("/{id}", name="pricing_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Pricing $pricing)
    {
        $deleteForm = $this->createDeleteForm($pricing);

        return $this->render('SMSSchoolBundle:pricing:show.html.twig', array(
            'pricing' => $pricing,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pricing entity.
     *
     * @Route("/{id}/edit", name="pricing_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:pricing:edit.html.twig")
     */
    public function editAction(Request $request, Pricing $pricing)
    {
        $editForm = $this->createForm(PricingType::class, $pricing)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($pricing);
            $this->flashSuccessMsg('pricing.edit.success');
            return $this->redirectToRoute('pricing_index');
        }

        return array(
            'pricing' => $pricing,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="pricing_bulk_delete")
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
                $this->getEntityManager()->deleteAll(pricing::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('pricing.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('pricing.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a pricing entity.
     *
     * @Route("/{id}", name="pricing_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Pricing $pricing)
    {
        $form = $this->createDeleteForm($pricing)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($pricing);
            $this->flashSuccessMsg('pricing.delete.one.success');
        }

        return $this->redirectToRoute('pricing_index');
    }

    /**
     * Creates a form to delete a pricing entity.
     *
     * @param Pricing $pricing The pricing entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pricing $pricing)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pricing_delete', array('id' => $pricing->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get pricing Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getPricingEntityManager()
    {
        if (!$this->has('sms.datatable.pricing')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.pricing');
    }}
