<?php

namespace API\BaseController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package API\Services
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
     * @return API\Services\EntityManager
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
     * @return API\Services\EntityManager
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

    /**
     * get User Space Manager Service.
     *
     * @return API\Services\UserSpaceManager
     *
     * @throws \NotFoundException
     */
    protected function getUserSapaceManager()
    {
        if (!$this->has('sms.user_space')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sms.user_space');
    }

    /**
     * get Store Manager Service.
     *
     * @return API\Services\StoreManager
     *
     * @throws \NotFoundException
     */
    protected function getStoreManager()
    {
        if (!$this->has('sms.store_manager')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sms.store_manager');
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

    /**
     * get datatable query Service.
     * @throws \NotFoundException
     */
    protected function getSerializer()
    {
        if (!$this->has('serializer')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('serializer');
    }
}
