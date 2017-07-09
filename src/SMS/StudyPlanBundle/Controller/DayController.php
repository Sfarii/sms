<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Day;
use SMS\StudyPlanBundle\Form\DayType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Day controller.
 *
 * @Route("day")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class DayController extends BaseController
{
    /**
     * Lists all day entities.
     *
     * @Route("/", name="day_index")
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:day/index.html.twig")
     */
    public function indexAction()
    {
        $days = $this->getDayEntityManager();
        $days->buildDatatable();

        return array('days' => $days);
    } /**
     * Lists all day entities.
     *
     * @Route("/results", name="day_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $days = $this->getDayEntityManager();
        $days->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($days);

        return $query->getResponse();
    }
    /**
     * Creates a new day entity.
     *
     * @Route("/new", name="day_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:day/new.html.twig")
     */
    public function newAction(Request $request)
    {
        $day = new Day();
        $form = $this->createForm(DayType::class, $day);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($day , $this->getUser());
            $this->flashSuccessMsg('day.add.success');
            return $this->redirectToRoute('day_index');
        }

        return array(
            'day' => $day,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing day entity.
     *
     * @Route("/{id}/edit", name="day_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:day/edit.html.twig")
     */
    public function editAction(Request $request, Day $day)
    {
        $editForm = $this->createForm(DayType::class, $day)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($day);
            $this->flashSuccessMsg('day.edit.success');
            return $this->redirectToRoute('day_index');
        }

        return array(
            'day' => $day,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="day_bulk_delete")
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
                $this->getEntityManager()->deleteAll(day::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('day.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('day.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a day entity.
     *
     * @Route("/{id}", name="day_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Day $day)
    {
        $form = $this->createDeleteForm($day)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($day);
            $this->flashSuccessMsg('day.delete.one.success');
        }

        return $this->redirectToRoute('day_index');
    }

    /**
     * Creates a form to delete a day entity.
     *
     * @param Day $day The day entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Day $day)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('day_delete', array('id' => $day->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get day Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getDayEntityManager()
    {
        if (!$this->has('sms.datatable.day')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.day');
    }}
