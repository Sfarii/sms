<?php

namespace SMS\EstablishmentBundle\Controller;

use SMS\EstablishmentBundle\Entity\Grade;
use SMS\EstablishmentBundle\Form\GradeType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Grade controller.
 *
 * @Route("grade")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\EstablishmentBundle\Controller
 *
 */
class GradeController extends BaseController
{
    /**
     * Lists all grade entities.
     *
     * @Route("/", name="grade_index")
     * @Template("smsestablishmentbundle/grade/index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $grade = $this->getGradeEntityManager();
        $grade->buildDatatable();

        return array('grade' => $grade);

    }

    /**
     * @Route("/results", name="grade_results" )
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $grade = $this->getGradeEntityManager();
        $grade->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($grade);

        return $query->getResponse();

    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="grade_bulk_delete")
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
                $this->getEntityManager()->deleteAll(Grade::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('grade.delete.fail'), 200);
            }
            

            return new Response($this->get('translator')->trans('grade.delete.success'), 200);
        }

        return new Response('Bad Request', 500);
    }

    /**
     * Creates a new grade entity.
     *
     * @Route("/new", name="grade_new")
     * @Method({"GET", "POST"})
     * @Template("smsestablishmentbundle/grade/new.html.twig")
     */
    public function newAction(Request $request)
    {
        $grade = new Grade();
        $form = $this->createForm(GradeType::class, $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($grade , $this->getUser());
            $this->flashSuccessMsg('grade.add.success');
            return $this->redirectToRoute('grade_index');
        }

        return array(
                'grade' => $grade,
                'form' => $form->createView(),
            );
    }

    /**
     * Displays a form to edit an existing grade entity.
     *
     * @Route("/{id}/edit", name="grade_edit" , options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("smsestablishmentbundle/grade/edit.html.twig")
     */
    public function editAction(Request $request, Grade $grade)
    {

        $editForm = $this->createForm(GradeType::class, $grade)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($grade);
            $this->flashSuccessMsg('grade.edit.success');
            return $this->redirectToRoute('grade_index');
        }

        return  array(
            'grade' => $grade,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * get grade Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getGradeEntityManager()
    {
        if (!$this->has('sms.datatable.grade')){
           throw $this->createNotFoundException('Service Not Found');
        }
        return $this->get('sms.datatable.grade');
    }
}
