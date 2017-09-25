<?php

namespace SMS\PaymentBundle\Controller;

use SMS\PaymentBundle\Entity\Payment;
use SMS\PaymentBundle\Entity\CatchUpLesson;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\PaymentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SMS\PaymentBundle\Form\CatchUpLessonType;

/**
 * Catchuplesson controller.
 *
 * @Route("catchuplesson")
 */
class CatchUpLessonController extends BaseController
{
    /**
     * Lists all catchUpLesson entities.
     *
     * @Route("/", name="catchuplesson_index")
     * @Method("GET")
     * @Template("SMSPaymentBundle:catchuplesson:index.html.twig")
     */
    public function indexAction()
    {
        $catchUpLessons = $this->getCatchUpLessonEntityManager();
        $catchUpLessons->buildDatatable();

        return array('catchUpLessons' => $catchUpLessons);
    }

    /**
     * Lists all paymentType entities.
     *
     * @Route("/results", name="catchuplesson_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $catchUpLessons = $this->getCatchUpLessonEntityManager();
        $catchUpLessons->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($catchUpLessons);
        $user = $this->getUser();
        $entityClass = CatchUpLesson::class ;
        $function = function($qb) use ($user , $entityClass )
        {
            $qb->join('catch_up_lesson.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * Creates a new catchUpLesson entity.
     *
     * @Route("/new", name="catchuplesson_new")
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:catchuplesson:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $catchUpLesson = new Catchuplesson();
        $form = $this->createForm(CatchUpLessonType::class, $catchUpLesson , array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->addCatchUpLesson($catchUpLesson , $request->getSession()->get('_catchUpLesson_schedule', array()) , $this->getUser());
            $this->flashSuccessMsg('catchuplesson.add.success');
            return $this->redirectToRoute('catchuplesson_show', array('id' => $catchUpLesson->getId()));
        }

        return array(
            'catchUpLesson' => $catchUpLesson,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a catchUpLesson entity.
     *
     * @Route("/{id}", name="catchuplesson_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:catchuplesson:show.html.twig")
     */
    public function showAction(CatchUpLesson $catchUpLesson)
    {
        return array(
            'catchUpLesson' => $catchUpLesson,
            'stats' => $this->getEntityManager()->getStatsByPaymentType($catchUpLesson)
        );
    }

    /**
     * Displays a form to edit an existing catchUpLesson entity.
     *
     * @Route("/{id}/edit", name="catchuplesson_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSPaymentBundle:catchuplesson:edit.html.twig")
     */
    public function editAction(Request $request, CatchUpLesson $catchUpLesson)
    {
        $editForm = $this->createForm(CatchUpLessonType::class, $catchUpLesson , array('establishment' => $this->getUser()->getEstablishment()));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEntityManager()->update($catchUpLesson);
            $this->flashSuccessMsg('catchuplesson.edit.success');
            return $this->redirectToRoute('catchuplesson_edit', array('id' => $catchUpLesson->getId()));
        }

        return array(
            'catchUpLesson' => $catchUpLesson,
            'form' => $editForm->createView()
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="catchuplesson_bulk_delete")
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
                $this->getEntityManager()->deleteAll(CatchUpLesson::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('catchuplesson.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('catchuplesson.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Finds and displays a catchUpLesson entity.
     *
     * @Route("/statistics/{id}", name="catchuplesson_statistics", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:catchuplesson:payment.html.twig")
     */
    public function statisticsAction(Request $request , CatchUpLesson $catchUpLesson )
    {
      $month = $request->query->get('month', 'all');
      $pagination = $this->getPaginator()->paginate(
          $this->getEntityManager()->getRegistredStudentByPaymentType($catchUpLesson,  $month), /* query NOT result */
          $request->query->getInt('page', 1)/*page number*/,
          12/*limit per page*/,
          array('wrap-queries'=>true)
      );
      $sort = $request->query->get('sort', 'empty');
      if ($sort == "empty"){
        $pagination->setParam('sort', 'student.firstName');
        $pagination->setParam('direction', 'asc');
      }
      // parameters to template
      return array('month' => $month , 'pagination' => $pagination , 'paymentType' => $catchUpLesson);
    }

    /**
     * Creates a new registration entity.
     *
     * @Route("/registration/new/{id}", name="catchuplesson_registration_new", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:catchuplesson:registration.html.twig")
     */
    public function registrationAction(Request $request, CatchUpLesson $catchUpLesson)
    {
      $student = $this->getStudentEntityManager();
      $student->buildDatatable();

      return array('students' => $student ,'catchUpLesson' => $catchUpLesson);
    }

    /**
     * Creates a new registration entity.
     *
     * @Route("/registration/bulk/{id}/{registered}" , requirements={"registered": "0|1"}, name="catchuplesson_registration_bulk_new", options={"expose"=true})
     * @Method("POST")
     */
    public function newRegistrationAction(Request $request, CatchUpLesson $catchUpLesson , $registered)
    {
      $isAjax = $request->isXmlHttpRequest();

      if ($isAjax) {
          $choices = $request->request->get('data');
          $token = $request->request->get('token');

          if (!$this->isCsrfTokenValid('multiselect', $token)) {
              throw new AccessDeniedException('The CSRF token is invalid.');
          }

          $this->getEntityManager()->updateCatchUpLessonRegistration($catchUpLesson , $choices , $registered);
          return new Response($this->get('translator')->trans('catchuplesson.registration.success'), 200);
      }

      return new Response('Bad Request', 400);
    }

    /**
     * Get paymentType Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getCatchUpLessonEntityManager()
    {
        if (!$this->has('sms.datatable.catchUp.lesson')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.catchUp.lesson');
    }

    /**
     * Get student Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getStudentEntityManager()
    {
        if (!$this->has('sms.datatable.registration.students')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.registration.students');
    }

    /**
     * Get paginator Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     * @throws \NotFoundException
     */
    protected function getPaginator()
    {
        if (!$this->has('knp_paginator')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('knp_paginator');
    }
}
