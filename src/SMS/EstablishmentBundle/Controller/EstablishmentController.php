<?php

namespace SMS\EstablishmentBundle\Controller;

use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\EstablishmentBundle\Form\EstablishmentType;
use SMS\EstablishmentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Establishment controller.
 *
 * @Route("establishment")
 * @Security("has_role('ROLE_MANAGER')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\SchoolBundle\Controller
 *
 */
class EstablishmentController extends BaseController
{
    /**
     * Lists all establishment entities.
     *
     * @Route("/", name="establishment_index")
     * @Method("GET")
     * @Template("SMSEstablishmentBundle:establishment:index.html.twig")
     */
    public function indexAction()
    {
        $establishments = $this->getEstablishmentEntityManager();
        $establishments->buildDatatable();

        return array('establishments' => $establishments);
    } /**
     * Lists all establishment entities.
     *
     * @Route("/results", name="establishment_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $establishments = $this->getEstablishmentEntityManager();
        $establishments->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($establishments);

        return $query->getResponse();
    }
    /**
     * Creates a new establishment entity.
     *
     * @Route("/new", name="establishment_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSEstablishmentBundle:establishment:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $establishment = new Establishment();
        $form = $this->createForm(EstablishmentType::class, $establishment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($establishment , $this->getUser());
            $this->flashSuccessMsg('establishment.add.success');
            return $this->redirectToRoute('establishment_index');
        }

        return array(
            'establishment' => $establishment,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a establishment entity.
     *
     * @Route("/{id}", name="establishment_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Establishment $establishment)
    {
        $deleteForm = $this->createDeleteForm($establishment);

        return $this->render('SMSEstablishmentBundle:establishment:show.html.twig', array(
            'establishment' => $establishment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing establishment entity.
     *
     * @Route("/{id}/edit", name="establishment_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSEstablishmentBundle:establishment:edit.html.twig")
     */
    public function editAction(Request $request, Establishment $establishment)
    {
        $editForm = $this->createForm(EstablishmentType::class, $establishment)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($establishment);
            $this->flashSuccessMsg('establishment.edit.success');
            return $this->redirectToRoute('establishment_index');
        }

        return array(
            'establishment' => $establishment,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="establishment_bulk_delete")
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
                $this->getEntityManager()->deleteAll(establishment::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('establishment.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('establishment.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a establishment entity.
     *
     * @Route("/{id}", name="establishment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Establishment $establishment)
    {
        $form = $this->createDeleteForm($establishment)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($establishment);
            $this->flashSuccessMsg('establishment.delete.one.success');
        }

        return $this->redirectToRoute('establishment_index');
    }

    /**
     * Creates a form to delete a establishment entity.
     *
     * @param Establishment $establishment The establishment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Establishment $establishment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('establishment_delete', array('id' => $establishment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get establishment Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getEstablishmentEntityManager()
    {
        if (!$this->has('sms.datatable.establishment')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.establishment');
    }}
