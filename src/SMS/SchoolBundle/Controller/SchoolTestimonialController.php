<?php

namespace SMS\SchoolBundle\Controller;

use SMS\SchoolBundle\Entity\SchoolTestimonial;
use SMS\SchoolBundle\Form\SchoolTestimonialType;
use SMS\SchoolBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Schooltestimonial controller.
 *
 * @Route("schooltestimonial")
 * @Security("has_role('ROLE_MANAGER')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\SchoolBundle\Controller
 *
 */
class SchoolTestimonialController extends BaseController
{
    /**
     * Lists all schoolTestimonial entities.
     *
     * @Route("/", name="schooltestimonial_index")
     * @Method("GET")
     * @Template("SMSSchoolBundle:schooltestimonial:index.html.twig")
     */
    public function indexAction()
    {
        $schoolTestimonials = $this->getSchoolTestimonialEntityManager();
        $schoolTestimonials->buildDatatable();

        return array('schoolTestimonials' => $schoolTestimonials);
    } /**
     * Lists all schoolTestimonial entities.
     *
     * @Route("/results", name="schooltestimonial_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $schoolTestimonials = $this->getSchoolTestimonialEntityManager();
        $schoolTestimonials->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($schoolTestimonials);

        return $query->getResponse();
    }
    /**
     * Creates a new schoolTestimonial entity.
     *
     * @Route("/new", name="schooltestimonial_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:schooltestimonial:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $schoolTestimonial = new Schooltestimonial();
        $form = $this->createForm(SchoolTestimonialType::class, $schoolTestimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($schoolTestimonial , $this->getUser());
            $this->flashSuccessMsg('schoolTestimonial.add.success');
            return $this->redirectToRoute('schooltestimonial_index');
        }

        return array(
            'schoolTestimonial' => $schoolTestimonial,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a schoolTestimonial entity.
     *
     * @Route("/{id}", name="schooltestimonial_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(SchoolTestimonial $schoolTestimonial)
    {
        $deleteForm = $this->createDeleteForm($schoolTestimonial);

        return $this->render('SMSSchoolBundle:schooltestimonial:show.html.twig', array(
            'schoolTestimonial' => $schoolTestimonial,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing schoolTestimonial entity.
     *
     * @Route("/{id}/edit", name="schooltestimonial_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:schooltestimonial:edit.html.twig")
     */
    public function editAction(Request $request, SchoolTestimonial $schoolTestimonial)
    {
        $editForm = $this->createForm(SchoolTestimonialType::class, $schoolTestimonial)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($schoolTestimonial);
            $this->flashSuccessMsg('schoolTestimonial.edit.success');
            return $this->redirectToRoute('schooltestimonial_index');
        }

        return array(
            'schoolTestimonial' => $schoolTestimonial,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="schooltestimonial_bulk_delete")
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
                $this->getEntityManager()->deleteAll(schoolTestimonial::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('schoolTestimonial.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('schoolTestimonial.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a schoolTestimonial entity.
     *
     * @Route("/{id}", name="schooltestimonial_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SchoolTestimonial $schoolTestimonial)
    {
        $form = $this->createDeleteForm($schoolTestimonial)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($schoolTestimonial);
            $this->flashSuccessMsg('schoolTestimonial.delete.one.success');
        }

        return $this->redirectToRoute('schooltestimonial_index');
    }

    /**
     * Creates a form to delete a schoolTestimonial entity.
     *
     * @param SchoolTestimonial $schoolTestimonial The schoolTestimonial entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SchoolTestimonial $schoolTestimonial)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('schooltestimonial_delete', array('id' => $schoolTestimonial->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get schoolTestimonial Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getSchoolTestimonialEntityManager()
    {
        if (!$this->has('sms.datatable.schoolTestimonial')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.schoolTestimonial');
    }}
