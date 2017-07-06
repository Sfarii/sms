<?php

namespace SMS\PaymentBundle\Controller;

use SMS\PaymentBundle\Entity\Registration;
use SMS\PaymentBundle\Entity\Payment;
use SMS\PaymentBundle\Entity\PaymentType;
use SMS\PaymentBundle\Form\RegistrationType;
use SMS\PaymentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SMS\UserBundle\Entity\Student;

/**
 * Registration controller.
 *
 * @Route("registration")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\PaymentBundle\Controller
 *
 */
class RegistrationController extends BaseController
{
    /**
     * Lists all registration entities.
     *
     * @Route("/results/{id}", name="registration_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction(Student $student)
    {
        $registrations = $this->getRegistrationEntityManager();
        $registrations->buildDatatable(array('id' => $student->getId()));

        $query = $this->getDataTableQuery()->getQueryFrom($registrations);
        $function = function($qb) use ( $student)
        {
            $qb->join('registration.student', 'student')
                ->andWhere('student.id = :student')
                ->setParameter('student', $student->getId());
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * Finds and displays a payment entity.
     *
     * @Route("/pdf/{id}", name="registration_pdf", options={"expose"=true})
     * @Method("GET")
     */
    public function pdfAction(Registration $registration)
    {
      $paymentRepository = $this->getDoctrine()->getRepository(Payment::class);
      $html = $this->renderView('SMSPaymentBundle:pdf:registration.html.twig', array(
          'registration'  => $registration,
          'payments' => $paymentRepository->findByRegistration($registration)
      ));

      return new Response(
          $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
          200,
          array(
              'Content-Type'          => 'application/pdf',
              'Content-Disposition'   => sprintf('attachment; filename="%s.pdf"' , $registration->getStudent())
          )
      );
    }

    /**
     * Lists all student entities.
     *
     * @Route("/registration_student", name="registration_student_results")
     * @Method("GET")
     * @return Response
     */
    public function indexRegistrationResultsAction()
    {
        $student = $this->getStudentEntityManager();
        $student->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($student);

        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('student.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
                ->andWhere('student.id != :userId')
                ->setParameter('userId', $user->getId())
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }


    /**
     * Creates a new registration entity.
     *
     * @Route("/new", name="registration_new", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:registration:new.html.twig")
     */
    public function newAction(Request $request)
    {
      // registration form
      $form = $this->createForm(RegistrationType::class, null, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);

      $student = $this->getStudentEntityManager();
      $student->buildDatatable();

      return array('students' => $student , "form" => $form->createView());
    }

    /**
     * Finds and displays a registration entity.
     *
     * @Route("/student/{id}", name="user_registration_show", options={"expose"=true})
     * @Method("GET")
     * @Template("SMSPaymentBundle:registration:show.html.twig")
     */
    public function showAction(Student $student)
    {
        $registration = $this->getRegistrationEntityManager();
        $registration->buildDatatable(array('id' => $student->getId()));
        $registrationRepository = $this->getDoctrine()->getRepository(Registration::class);
        return array(
            'student' => $student,
            'registration' => $registration,
            'price' => $registrationRepository->getPriceByStudent($student),
            'registrationFee' => $registrationRepository->getRegistrationFeeByStudent($student),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/new/{id}", name="registration_bulk_new", options={"expose"=true})
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkNewAction(PaymentType $paymentType, Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $data = array('students' => $request->request->get('data'),
                          'registered' => $request->request->get('registered'),
                          'paymentType' => $paymentType,
                          'user' => $this->getUser());

            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('paymentType', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            if (empty($data['students'])){
              throw new AccessDeniedException('Data is invalid.');
            }

            $this->getEntityManager()->newRegistration(Student::class, $data);

            return new Response($this->get('translator')->trans('registration.add.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Bulk registration action.
     *
     * @param Request $request
     *
     * @Route("/bulk/registred", name="registration_registred")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkRegistredAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            $this->getEntityManager()->registrationAction($choices, true);

            return new Response($this->get('translator')->trans('registration.edit.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Bulk registration action.
     *
     * @param Request $request
     *
     * @Route("/bulk/not/registred", name="registration_not_registred")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkNotRegistredAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            $this->getEntityManager()->registrationAction($choices, false);

            return new Response($this->get('translator')->trans('registration.edit.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Get registration Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getRegistrationEntityManager()
    {
        if (!$this->has('sms.datatable.registration')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.registration');
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
}
