<?php

namespace SMS\SchoolBundle\Controller;

use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController
{
    /**
     * @Route("/")
     * @Template("SMSSchoolBundle:default:index.html.twig")
     */

    public function indexAction()
    {
        return array();
    }
}
