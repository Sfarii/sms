<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\EstablishmentBundle\Entity\Section;
use SMS\EstablishmentBundle\Form\SectionType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Section controller.
 *
 * @Route("section_manager")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class SectionManager extends BaseController
{
    /**
     * Lists all section entities.
     *
     * @Route("/", name="section_manager_index")
     * @Method("GET")
     * @Template("SMSEstablishmentBundle:section:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $section = $this->getSectionEntityManager();
        $section->buildDatatable();

        return array('section' => $section);
    }

    /**
     * @Route("/results", name="section_manager_results" )
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $section = $this->getSectionEntityManager();
        $section->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($section);

        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('section.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * get section Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getSectionEntityManager()
    {
        if (!$this->has('sms.datatable.section_manager')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sms.datatable.section_manager');
    }
}
