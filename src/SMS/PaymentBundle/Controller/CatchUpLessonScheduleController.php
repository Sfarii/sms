<?php

namespace SMS\PaymentBundle\Controller;

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
 * Catchuplesson Schedule controller.
 *
 * @Route("catchuplessonschedule")
 */
class CatchUpLessonScheduleController extends BaseController
{
    /**
     * Creates a show Catchuplesson Schedule entity.
     *
     * @Route("/schedule/show", name="catchUpLesson_session_schedule_show" , options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:catchuplessonschedule:schedule.html.twig")
     */
    public function showCatchUpLessonScheduleSessionAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
          $catchUpLessonSchedule = $request->getSession()->get('_catchUpLesson_schedule', array());
          return array(  'result' => $catchUpLessonSchedule);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Creates a show Catchuplesson Schedule  entity.
     *
     * @Route("/schedule/show/{id}", name="catchUpLesson_db_schedule_show" , options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:catchuplessonschedule:schedule_db.html.twig")
     */
    public function showCatchUpLessonScheduleDBAction(Request $request,  CatchUpLesson $catchUpLesson)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
          $catchUpLessonSchedule =  $catchUpLesson->getSchedules();
          return array('result' => $catchUpLessonSchedule);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Bulk catchUpLesson schedule actions.
     *
     * @param Request $request
     *
     * @Route("/bulk/crud", name="catchUpLesson_bulk_session_schedule_crud", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkCrudInSessionAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $token = $request->request->get('token');
            $action = $request->request->get('action');
            $data = $request->request->get('data');

            if (!$this->isCsrfTokenValid('catchUpLesson', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            switch ($action) {
              case 'new_edit':
                $session = $request->getSession();
                $catchUpLessonSchedule = $session->get('_catchUpLesson_schedule', array());
                $session->set('_catchUpLesson_schedule', $this->getEntityManager()->updateCatchUpLessonScheduleLineSession($catchUpLessonSchedule , $data) );
                return new Response($this->get('translator')->trans('catchUpLesson_schedule.new.success'), 200);
                break;
              case 'delete':
                $session = $request->getSession();
                $catchUpLessonSchedule = $session->get('_catchUpLesson_schedule', array());
                $session->set('_catchUpLesson_schedule', $this->getEntityManager()->deleteCatchUpLessonScheduleLineSession($catchUpLessonSchedule , $data));
                return new Response($this->get('translator')->trans('catchUpLesson_schedule.delete.success'), 200);
                break;

              default:
                return new Response('Bad Request', 400);
                break;
            }
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Bulk catchUpLesson schedule actions.
     *
     * @param Request $request
     *
     * @Route("/bulk/crud/{id}", name="catchUpLesson_bulk_db_schedule_crud", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkCrudInDBAction(Request $request,  CatchUpLesson $catchUpLesson)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $token = $request->request->get('token');
            $action = $request->request->get('action');
            $data = $request->request->get('data');

            if (!$this->isCsrfTokenValid('catchUpLesson', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            switch ($action) {
              case 'new_edit':
                $this->getEntityManager()->updateCatchUpLessonScheduleLineDB($catchUpLesson , $data);
                return new Response($this->get('translator')->trans('catchUpLesson_schedule.new.success'), 200);
                break;
              case 'delete':
                $this->getEntityManager()->deleteCatchUpLessonScheduleLineDB($catchUpLesson , $data);
                return new Response($this->get('translator')->trans('catchUpLesson_schedule.delete.success'), 200);
                break;

              default:
                return new Response('Bad Request', 400);
                break;
            }
        }

        return new Response('Bad Request', 400);
    }
}
