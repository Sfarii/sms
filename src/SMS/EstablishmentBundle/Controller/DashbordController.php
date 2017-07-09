<?php

namespace SMS\EstablishmentBundle\Controller;

use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SMS\PaymentBundle\Entity\Payment;
use SMS\PaymentBundle\Entity\Registration;
/**
 * Division controller.
 *
 * @Route("dashbord")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\EstablishmentBundle\Controller
 *
 */
class DashbordController extends BaseController
{
    /**
     * Lists all division entities.
     *
     * @Route("/", name="dashbord_index")
     * @Method("GET")
     * @Template("SMSEstablishmentBundle:dashbord:index.html.twig")
     */
    public function indexAction()
    {
      $paymentRepository = $this->getDoctrine()->getRepository(Payment::class);
      $registrationRepository = $this->getDoctrine()->getRepository(Registration::class);

      return array(
          'paymentsInfo' => $paymentRepository->getPaymentInfoByEstablishment($this->getUser()->getEstablishment()) ,
          'studentInfo' => $registrationRepository->getRegistredStudent($this->getUser()->getEstablishment()) ,
          'chart' => $paymentRepository->findChartByAll($this->getUser()->getEstablishment())
      );
    }
}
