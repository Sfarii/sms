<?php

namespace SMS\SchoolBundle\Controller;

use SMS\SchoolBundle\Entity\Contact;
use SMS\SchoolBundle\Form\ContactType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Contact controller.
 *
 * @Route("contact")
 * @Security("has_role('ROLE_MANAGER')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\SchoolBundle\Controller
 *
 */
class ContactController extends BaseController
{
    /**
     * Lists all contact entities.
     *
     * @Route("/", name="contact_index")
     * @Method("GET")
     * @Template("SMSSchoolBundle:contact:index.html.twig")
     */
    public function indexAction()
    {
        $contacts = $this->getContactEntityManager();
        $contacts->buildDatatable();

        return array('contacts' => $contacts);
    }
  /**
     * Lists all contact entities.
     *
     * @Route("/results", name="contact_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $contacts = $this->getContactEntityManager();
        $contacts->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($contacts);

        return $query->getResponse();
    }

    /**
     * Finds and displays a contact entity.
     *
     * @Route("/{id}", name="contact_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Contact $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);

        return $this->render('SMSSchoolBundle:contact:show.html.twig', array(
            'contact' => $contact,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing contact entity.
     *
     * @Route("/{id}/edit", name="contact_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:contact:edit.html.twig")
     */
    public function editAction(Request $request, Contact $contact)
    {
        $editForm = $this->createForm(ContactType::class, $contact)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($contact);
            $this->flashSuccessMsg('contact.edit.success');
            return $this->redirectToRoute('contact_index');
        }

        return array(
            'contact' => $contact,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="contact_bulk_delete")
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
                $this->getEntityManager()->deleteAll(contact::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('contact.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('contact.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a contact entity.
     *
     * @Route("/{id}", name="contact_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Contact $contact)
    {
        $form = $this->createDeleteForm($contact)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($contact);
            $this->flashSuccessMsg('contact.delete.one.success');
        }

        return $this->redirectToRoute('contact_index');
    }

    /**
     * Creates a form to delete a contact entity.
     *
     * @param Contact $contact The contact entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Contact $contact)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contact_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get contact Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getContactEntityManager()
    {
        if (!$this->has('sms.datatable.contact')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.contact');
    }}
