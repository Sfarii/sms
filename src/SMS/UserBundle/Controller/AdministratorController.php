<?php

namespace SMS\UserBundle\Controller;

use SMS\UserBundle\Entity\Administrator;
use SMS\UserBundle\Entity\Manager;
use SMS\UserBundle\Form\AdministratorType;
use SMS\UserBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Administrator controller.
 *
 * @Route("administrator")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\UserBundle\Controller
 *
 */
class AdministratorController extends BaseController
{
    /**
     * Lists all administrator entities.
     *
     * @Route("/", name="administrator_index")
     * @Method("GET")
     * @Template("SMSUserBundle:administrator:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $administrator = $this->getAdministratorEntityManager();
        $administrator->buildDatatable();

        return array('administrators' => $administrator);
    }

    /**
     * Lists all administrator entities.
     *
     * @Route("/results", name="administrator_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $administrator = $this->getAdministratorEntityManager();
        $administrator->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($administrator);

        if (!$this->getUser() instanceof Manager){
          $user = $this->getUser();
          $function = function($qb) use ($user)
          {
              $qb->join('administrator.establishment', 'establishment')
                  ->andWhere('establishment.id = :establishment')
                  ->andWhere('administrator.id != :userId')
                  ->setParameter('userId', $user->getId())
          				->setParameter('establishment', $user->getEstablishment()->getId());
          };

          $query->addWhereAll($function);
        }

        return $query->getResponse();
    }

    /**
     * Creates a new administrator entity.
     *
     * @Route("/new", name="administrator_new")
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:administrator:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $administrator = new Administrator();
        $form = $this->createForm(AdministratorType::class, $administrator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getUserEntityManager()->addUser($administrator);
            $this->flashSuccessMsg('administrator.add.success');
            return $this->redirectToRoute('administrator_index');
        }

        return  array(
            'administrator' => $administrator,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a administrator entity.
     *
     * @Route("/{id}", name="administrator_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSUserBundle:administrator:show.html.twig")
     */
    public function showAction(Administrator $administrator)
    {
        $deleteForm = $this->createDeleteForm($administrator);

        return array(
            'administrator' => $administrator,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing administrator entity.
     *
     * @Route("/{id}/edit", name="administrator_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSUserBundle:administrator:edit.html.twig")
     */
    public function editAction(Request $request, Administrator $administrator)
    {

        $editForm = $this->createForm(AdministratorType::class, $administrator)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getUserEntityManager()->editUser($administrator);
            $this->flashSuccessMsg('administrator.edit.success');
            if ($administrator->getId() !== $this->getUser()->getId()){
              return $this->redirectToRoute('administrator_index');
            }
        }

        return array(
            'user' => $administrator,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a administrator entity.
     *
     * @Route("/{id}", name="administrator_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Administrator $administrator)
    {
        $form = $this->createDeleteForm($administrator)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($administrator);
            $this->flashSuccessMsg('administrator.delete.one.success');
        }

        return $this->redirectToRoute('administrator_index');
    }

    /**
     * Creates a form to delete a administrator entity.
     *
     * @param Administrator $administrator The administrator entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Administrator $administrator)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('administrator_delete', array('id' => $administrator->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get administrator Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getAdministratorEntityManager()
    {
        if (!$this->has('sms.datatable.administrator')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.administrator');
    }
}
