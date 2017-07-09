<?php

namespace SMS\SchoolBundle\Controller;

use SMS\SchoolBundle\Entity\AboutUs;
use SMS\SchoolBundle\Form\AboutUsType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * AboutUs controller.
 *
 * @Route("aboutus")
 * @Security("has_role('ROLE_MANAGER')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\SchoolBundle\Controller
 *
 */
class AboutUsController extends BaseController
{
    /**
     * Lists all aboutUs entities.
     *
     * @Route("/", name="aboutus_index")
     * @Method("GET")
     * @Template("SMSSchoolBundle:aboutus:index.html.twig")
     */
    public function indexAction()
    {
        $aboutuses = $this->getAboutUsEntityManager();
        $aboutuses->buildDatatable();

        return array('aboutuses' => $aboutuses);
    }
  /**
     * Lists all aboutUs entities.
     *
     * @Route("/results", name="aboutus_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $aboutuses = $this->getAboutUsEntityManager();
        $aboutuses->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($aboutuses);

        return $query->getResponse();
    }

    /**
     * Creates a new aboutUs entity.
     *
     * @Route("/new", name="aboutus_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:aboutus:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $aboutUs = new AboutUs();
        $form = $this->createForm(AboutUsType::class, $aboutUs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($aboutUs , $this->getUser());
            $this->flashSuccessMsg('aboutUs.add.success');
            return $this->redirectToRoute('aboutus_index');
        }

        return array(
            'aboutUs' => $aboutUs,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a aboutUs entity.
     *
     * @Route("/{id}", name="aboutus_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(AboutUs $aboutUs)
    {
        $deleteForm = $this->createDeleteForm($aboutUs);

        return $this->render('SMSSchoolBundle:aboutus:show.html.twig', array(
            'aboutUs' => $aboutUs,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing aboutUs entity.
     *
     * @Route("/{id}/edit", name="aboutus_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:aboutus:edit.html.twig")
     */
    public function editAction(Request $request, AboutUs $aboutUs)
    {
        $editForm = $this->createForm(AboutUsType::class, $aboutUs)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($aboutUs);
            $this->flashSuccessMsg('aboutUs.edit.success');
            return $this->redirectToRoute('aboutus_index');
        }

        return array(
            'aboutUs' => $aboutUs,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="aboutus_bulk_delete")
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
                $this->getEntityManager()->deleteAll(aboutUs::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('aboutUs.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('aboutUs.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a aboutUs entity.
     *
     * @Route("/{id}", name="aboutus_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AboutUs $aboutUs)
    {
        $form = $this->createDeleteForm($aboutUs)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($aboutUs);
            $this->flashSuccessMsg('aboutUs.delete.one.success');
        }

        return $this->redirectToRoute('aboutus_index');
    }

    /**
     * Creates a form to delete a aboutUs entity.
     *
     * @param AboutUs $aboutUs The aboutUs entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AboutUs $aboutUs)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('aboutus_delete', array('id' => $aboutUs->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get aboutUs Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getAboutUsEntityManager()
    {
        if (!$this->has('sms.datatable.aboutUs')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.aboutUs');
    }}
