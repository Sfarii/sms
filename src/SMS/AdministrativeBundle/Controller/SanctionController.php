<?php

namespace SMS\AdministrativeBundle\Controller;

use SMS\AdministrativeBundle\Entity\Sanction;
use SMS\AdministrativeBundle\Form\SanctionType;
use SMS\AdministrativeBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Sanction controller.
 *
 * @Route("sanction")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\AdministrativeBundle\Controller
 *
 */
class SanctionController extends BaseController
{
    /**
     * Lists all sanction entities.
     *
     * @Route("/", name="sanction_index")
     * @Method("GET")
     * @Template("SMSAdministrativeBundle:sanction:index.html.twig")
     */
    public function indexAction()
    {
        $sanctions = $this->getSanctionEntityManager();
        $sanctions->buildDatatable();

        return array('sanctions' => $sanctions);
    }
    /**
     * Lists all sanction entities.
     *
     * @Route("/results", name="sanction_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $sanctions = $this->getSanctionEntityManager();
        $sanctions->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($sanctions);
        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb
                ->join('student.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }
    /**
     * Creates a new sanction entity.
     *
     * @Route("/new", name="sanction_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSAdministrativeBundle:sanction:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $sanction = new Sanction();
        $form = $this->createForm(SanctionType::class, $sanction, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($sanction , $this->getUser());
            $this->flashSuccessMsg('sanction.add.success');
            return $this->redirectToRoute('sanction_index');
        }

        return array(
            'sanction' => $sanction,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a sanction entity.
     *
     * @Route("/{id}", name="sanction_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Sanction $sanction)
    {
        $deleteForm = $this->createDeleteForm($sanction);

        return $this->render('SMSAdministrativeBundle:sanction:show.html.twig', array(
            'sanction' => $sanction,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sanction entity.
     *
     * @Route("/{id}/edit", name="sanction_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSAdministrativeBundle:sanction/edit.html.twig")
     */
    public function editAction(Request $request, Sanction $sanction)
    {
        $editForm = $this->createForm(SanctionType::class, $sanction, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($sanction);
            $this->flashSuccessMsg('sanction.edit.success');
            return $this->redirectToRoute('sanction_index');
        }

        return array(
            'sanction' => $sanction,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="sanction_bulk_delete")
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
                $this->getEntityManager()->deleteAll(sanction::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('sanction.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('sanction.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a sanction entity.
     *
     * @Route("/{id}", name="sanction_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Sanction $sanction)
    {
        $form = $this->createDeleteForm($sanction)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($sanction);
            $this->flashSuccessMsg('sanction.delete.one.success');
        }

        return $this->redirectToRoute('sanction_index');
    }

    /**
     * Creates a form to delete a sanction entity.
     *
     * @param Sanction $sanction The sanction entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Sanction $sanction)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sanction_delete', array('id' => $sanction->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get sanction Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getSanctionEntityManager()
    {
        if (!$this->has('sms.datatable.sanction')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.sanction');
    }
}
