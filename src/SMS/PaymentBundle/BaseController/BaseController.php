<?php

namespace SMS\PaymentBundle\BaseController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\PaymentBundle\BaseController
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
     * get user Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getEntityManager()
    {
        if (!$this->has('sms.payment.entity_manager')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sms.payment.entity_manager');
    }

    /**
     * get datatable query Service.
     * @throws \NotFoundException
     */
    protected function getDataTableQuery()
    {
        if (!$this->has('sg_datatables.query')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sg_datatables.query');
    }
}
