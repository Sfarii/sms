<?php

namespace SMS\EstablishmentBundle\Controller;

use SMS\EstablishmentBundle\Entity\Division;
use SMS\EstablishmentBundle\Form\DivisionType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Division controller.
 *
 * @Route("division")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\EstablishmentBundle\Controller
 *
 */
class DivisionController extends BaseController
{
    /**
     * Lists all division entities.
     *
     * @Route("/", name="division_index")
     * @Method("GET")
     * @Template("SMSEstablishmentBundle:division:index.html.twig")
     */
    public function indexAction()
    {
        $divisions = $this->getDivisionEntityManager();
        $divisions->buildDatatable();

        return array('divisions' => $divisions);
    }

    /**
     * Lists all division entities.
     *
     * @Route("/results", name="division_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $divisions = $this->getDivisionEntityManager();
        $divisions->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($divisions);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('division.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }
    /**
     * Creates a new division entity.
     *
     * @Route("/new", name="division_new")
     * @Method({"GET", "POST"})
     * @Template("SMSEstablishmentBundle:division:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $division = new Division();
        $form = $this->createForm(DivisionType::class, $division, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($division , $this->getUser());
            $this->flashSuccessMsg('division.add.success');
            return $this->redirectToRoute('division_index');
        }

        return array(
            'division' => $division,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a division entity.
     *
     * @Route("/{id}", name="division_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Division $division)
    {
        $deleteForm = $this->createDeleteForm($division);

        return $this->render('SMSEstablishmentBundle:division:show.html.twig', array(
            'division' => $division,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing division entity.
     *
     * @Route("/{id}/edit", name="division_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSEstablishmentBundle:division:edit.html.twig")
     */
    public function editAction(Request $request, Division $division)
    {
        $editForm = $this->createForm(DivisionType::class, $division, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($division);
            $this->flashSuccessMsg('division.edit.success');
            return $this->redirectToRoute('division_index');
        }

        return array(
            'division' => $division,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="division_bulk_delete")
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
                $this->getEntityManager()->deleteAll(division::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('division.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('division.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a division entity.
     *
     * @Route("/{id}", name="division_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Division $division)
    {
        $form = $this->createDeleteForm($division)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($division);
            $this->flashSuccessMsg('division.delete.one.success');
        }

        return $this->redirectToRoute('division_index');
    }

    /**
     * Creates a form to delete a division entity.
     *
     * @param Division $division The division entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Division $division)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('division_delete', array('id' => $division->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get division Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getDivisionEntityManager()
    {
        if (!$this->has('sms.datatable.division')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.division');
    }
}
