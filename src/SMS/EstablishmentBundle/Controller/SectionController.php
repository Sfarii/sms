<?php

namespace SMS\EstablishmentBundle\Controller;

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
 * @Route("section")
 * @Security("has_role('ROLE_ADMIN')")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\EstablishmentBundle\Controller
 *
 */
class SectionController extends BaseController
{
    /**
     * Lists all section entities.
     *
     * @Route("/", name="section_index")
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
     * @Route("/results", name="section_results" )
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
     * Creates a new section entity.
     *
     * @Route("/new", name="section_new")
     * @Method({"GET", "POST"})
     * @Template("SMSEstablishmentBundle:section:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section , array(
            'establishment' => $this->getUser()->getEstablishment()
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($section , $this->getUser());
            $this->flashSuccessMsg('section.add.success');
            return $this->redirectToRoute('section_index');
        }

        return  array(
            'section' => $section,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing section entity.
     *
     * @Route("/{id}/edit", name="section_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSEstablishmentBundle:section:edit.html.twig")
     */
    public function editAction(Request $request, Section $section)
    {

        $editForm = $this->createForm(SectionType::class, $section , array('establishment' => $this->getUser()->getEstablishment()))
                          ->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($section);
            $this->flashSuccessMsg('section.edit.success');
            return $this->redirectToRoute('section_index');
        }

        return  array(
            'section' => $section,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="section_bulk_delete")
     * @Method("POST")
     *
     * @return Response
     */
    public function bulkDeleteAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            try {
                $this->getEntityManager()->deleteAll(Section::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('section.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('section.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
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
        if (!$this->has('sms.datatable.section')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sms.datatable.section');
    }
}
