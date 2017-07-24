<?php

namespace SMS\SchoolBundle\Controller;

use SMS\SchoolBundle\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class TranslationController extends BaseController
{
    /**
     * @Route("/translate" , name="translate_page")
     * @Method("POST")
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('_locale', $this->getRequest()->request->get('lang_switcher') == "gb" ? "en" : $this->getRequest()->request->get('lang_switcher'));
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
}
