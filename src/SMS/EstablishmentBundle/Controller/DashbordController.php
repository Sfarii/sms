<?php

namespace SMS\EstablishmentBundle\Controller;

use SMS\EstablishmentBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
      return array(
        
        'info' => $this->getEntityManager()->establishmentInfo($this->getUser()->getEstablishment())
      );
    }
}
