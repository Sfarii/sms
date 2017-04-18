<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Session;
use SMS\StudyPlanBundle\Form\SessionType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Session controller.
 *
 * @Route("session")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class SessionController extends BaseController
{
    /**
     * Lists all session entities.
     *
     * @Route("/", name="session_index")
     * @Method("GET")
     * @Template("smsstudyplanbundle/session/index.html.twig")
     */
    public function indexAction()
    {
        $sessions = $this->getSessionEntityManager();
        $sessions->buildDatatable();

        return array('sessions' => $sessions);
    } /**
     * Lists all session entities.
     *
     * @Route("/results", name="session_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $sessions = $this->getSessionEntityManager();
        $sessions->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($sessions);

        return $query->getResponse();
    }
    /**
     * Creates a new session entity.
     *
     * @Route("/new", name="session_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("smsstudyplanbundle/session/new.html.twig")
     */
    public function newAction(Request $request)
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($session , $this->getUser());
            $this->flashSuccessMsg('session.add.success');
            return $this->redirectToRoute('session_index');
        }

        return array(
            'session' => $session,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a session entity.
     *
     * @Route("/{id}", name="session_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Session $session)
    {
        $deleteForm = $this->createDeleteForm($session);

        return $this->render('smsstudyplanbundle/session/show.html.twig', array(
            'session' => $session,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing session entity.
     *
     * @Route("/{id}/edit", name="session_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("smsstudyplanbundle/session/edit.html.twig")
     */
    public function editAction(Request $request, Session $session)
    {
        $editForm = $this->createForm(SessionType::class, $session)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($session);
            $this->flashSuccessMsg('session.edit.success');
            return $this->redirectToRoute('session_index');
        }

        return array(
            'session' => $session,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="session_bulk_delete")
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
                $this->getEntityManager()->deleteAll(session::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('session.delete.fail'), 200);
            }
            

            return new Response($this->get('translator')->trans('session.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a session entity.
     *
     * @Route("/{id}", name="session_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Session $session)
    {
        $form = $this->createDeleteForm($session)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($session);
            $this->flashSuccessMsg('session.delete.one.success');
        }

        return $this->redirectToRoute('session_index');
    }

    /**
     * Creates a form to delete a session entity.
     *
     * @param Session $session The session entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Session $session)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('session_delete', array('id' => $session->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get session Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getSessionEntityManager()
    {
        if (!$this->has('sms.datatable.session')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.session');
    }}
