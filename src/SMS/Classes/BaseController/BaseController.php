<?php

namespace SMS\Classes\BaseController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\Classes\Services
 */

class BaseController extends Controller
{
	/**
     * Adds a flash success message to the current session.
     *
     * @param string $message The message
     */
    protected function flashSuccessMsg($message)
    {
        $this->addFlash($this->getParameter('flash_msg_success') , $this->get('translator')->trans($message));
    }

    /**
     * Adds a flash error message to the current session.
     *
     * @param string $message The message
     */
    protected function flashErrorMsg($message)
    {
        $this->addFlash($this->getParameter('flash_msg_error') , $this->get('translator')->trans($message));
    }

    /**
     * get Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getEntityManager()
    {
        if (!$this->has('sms.entity_manager')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sms.entity_manager');
    }

    /**
     * get user Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getUserEntityManager()
    {
        if (!$this->has('sms.user.entity_manager')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sms.user.entity_manager');
    }
}
