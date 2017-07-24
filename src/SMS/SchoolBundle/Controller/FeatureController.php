<?php

namespace SMS\SchoolBundle\Controller;

use SMS\SchoolBundle\Entity\Feature;
use SMS\SchoolBundle\Form\FeatureType;
use SMS\SchoolBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Feature controller.
 *
 * @Route("feature")
 * @Security("has_role('ROLE_MANAGER')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\SchoolBundle\Controller
 *
 */
class FeatureController extends BaseController
{
    /**
     * Lists all feature entities.
     *
     * @Route("/", name="feature_index")
     * @Method("GET")
     * @Template("SMSSchoolBundle:feature:index.html.twig")
     */
    public function indexAction()
    {
        $features = $this->getFeatureEntityManager();
        $features->buildDatatable();

        return array('features' => $features);
    } /**
     * Lists all feature entities.
     *
     * @Route("/results", name="feature_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $features = $this->getFeatureEntityManager();
        $features->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($features);

        return $query->getResponse();
    }
    /**
     * Creates a new feature entity.
     *
     * @Route("/new", name="feature_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:feature:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $feature = new Feature();
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($feature , $this->getUser());
            $this->flashSuccessMsg('feature.add.success');
            return $this->redirectToRoute('feature_index');
        }

        return array(
            'feature' => $feature,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a feature entity.
     *
     * @Route("/{id}", name="feature_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Feature $feature)
    {
        $deleteForm = $this->createDeleteForm($feature);

        return $this->render('SMSSchoolBundle:feature:show.html.twig', array(
            'feature' => $feature,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing feature entity.
     *
     * @Route("/{id}/edit", name="feature_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:feature:edit.html.twig")
     */
    public function editAction(Request $request, Feature $feature)
    {
        $editForm = $this->createForm(FeatureType::class, $feature)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($feature);
            $this->flashSuccessMsg('feature.edit.success');
            return $this->redirectToRoute('feature_index');
        }

        return array(
            'feature' => $feature,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="feature_bulk_delete")
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
                $this->getEntityManager()->deleteAll(feature::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('feature.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('feature.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a feature entity.
     *
     * @Route("/{id}", name="feature_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Feature $feature)
    {
        $form = $this->createDeleteForm($feature)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($feature);
            $this->flashSuccessMsg('feature.delete.one.success');
        }

        return $this->redirectToRoute('feature_index');
    }

    /**
     * Creates a form to delete a feature entity.
     *
     * @param Feature $feature The feature entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Feature $feature)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('feature_delete', array('id' => $feature->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get feature Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getFeatureEntityManager()
    {
        if (!$this->has('sms.datatable.feature')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.feature');
    }}
