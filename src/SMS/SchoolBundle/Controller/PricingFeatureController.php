<?php

namespace SMS\SchoolBundle\Controller;

use SMS\SchoolBundle\Entity\PricingFeature;
use SMS\SchoolBundle\Form\PricingFeatureType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pricingfeature controller.
 *
 * @Route("pricingfeature")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\SchoolBundle\Controller
 *
 */
class PricingFeatureController extends BaseController
{
    /**
     * Lists all pricingFeature entities.
     *
     * @Route("/", name="pricingfeature_index")
     * @Method("GET")
     * @Template("SMSSchoolBundle:pricingfeature:index.html.twig")
     */
    public function indexAction()
    {
        $pricingFeatures = $this->getPricingFeatureEntityManager();
        $pricingFeatures->buildDatatable();

        return array('pricingFeatures' => $pricingFeatures);
    } /**
     * Lists all pricingFeature entities.
     *
     * @Route("/results", name="pricingfeature_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $pricingFeatures = $this->getPricingFeatureEntityManager();
        $pricingFeatures->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($pricingFeatures);

        return $query->getResponse();
    }
    /**
     * Creates a new pricingFeature entity.
     *
     * @Route("/new", name="pricingfeature_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:pricingfeature:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $pricingFeature = new Pricingfeature();
        $form = $this->createForm(PricingFeatureType::class, $pricingFeature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($pricingFeature , $this->getUser());
            $this->flashSuccessMsg('pricingFeature.add.success');
            return $this->redirectToRoute('pricingfeature_index');
        }

        return array(
            'pricingFeature' => $pricingFeature,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a pricingFeature entity.
     *
     * @Route("/{id}", name="pricingfeature_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(PricingFeature $pricingFeature)
    {
        $deleteForm = $this->createDeleteForm($pricingFeature);

        return $this->render('SMSSchoolBundle:pricingfeature:show.html.twig', array(
            'pricingFeature' => $pricingFeature,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pricingFeature entity.
     *
     * @Route("/{id}/edit", name="pricingfeature_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:pricingfeature:edit.html.twig")
     */
    public function editAction(Request $request, PricingFeature $pricingFeature)
    {
        $editForm = $this->createForm(PricingFeatureType::class, $pricingFeature)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($pricingFeature);
            $this->flashSuccessMsg('pricingFeature.edit.success');
            return $this->redirectToRoute('pricingfeature_index');
        }

        return array(
            'pricingFeature' => $pricingFeature,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="pricingfeature_bulk_delete")
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
                $this->getEntityManager()->deleteAll(pricingFeature::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('pricingFeature.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('pricingFeature.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a pricingFeature entity.
     *
     * @Route("/{id}", name="pricingfeature_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PricingFeature $pricingFeature)
    {
        $form = $this->createDeleteForm($pricingFeature)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($pricingFeature);
            $this->flashSuccessMsg('pricingFeature.delete.one.success');
        }

        return $this->redirectToRoute('pricingfeature_index');
    }

    /**
     * Creates a form to delete a pricingFeature entity.
     *
     * @param PricingFeature $pricingFeature The pricingFeature entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PricingFeature $pricingFeature)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pricingfeature_delete', array('id' => $pricingFeature->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get pricingFeature Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getPricingFeatureEntityManager()
    {
        if (!$this->has('sms.datatable.pricingFeature')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.pricingFeature');
    }}
