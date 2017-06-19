<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\TypeExam;
use SMS\StudyPlanBundle\Form\TypeExamType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Typeexam controller.
 *
 * @Route("typeexam")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class TypeExamController extends BaseController
{
    /**
     * Lists all typeExam entities.
     *
     * @Route("/", name="typeexam_index")
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:typeexam:index.html.twig")
     */
    public function indexAction()
    {
        $typeExams = $this->getTypeExamEntityManager();
        $typeExams->buildDatatable();

        return array('typeExams' => $typeExams);
    } /**
     * Lists all typeExam entities.
     *
     * @Route("/results", name="typeexam_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $typeExams = $this->getTypeExamEntityManager();
        $typeExams->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($typeExams);

        $user = $this->getUser();
        $function = function($qb) use ($user)
        {
            $qb->join('type_exam.establishment', 'establishment')
                ->andWhere('establishment.id = :establishment')
        				->setParameter('establishment', $user->getEstablishment()->getId());
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }
    /**
     * Creates a new typeExam entity.
     *
     * @Route("/new", name="typeexam_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:typeexam:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $typeExam = new Typeexam();
        $form = $this->createForm(TypeExamType::class, $typeExam, array('establishment' => $this->getUser()->getEstablishment()));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($typeExam , $this->getUser());
            $this->flashSuccessMsg('typeExam.add.success');
            return $this->redirectToRoute('typeexam_index');
        }

        return array(
            'typeExam' => $typeExam,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a typeExam entity.
     *
     * @Route("/{id}", name="typeexam_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(TypeExam $typeExam)
    {
        $deleteForm = $this->createDeleteForm($typeExam);

        return $this->render('SMSStudyPlanBundle:typeexam:show.html.twig', array(
            'typeExam' => $typeExam,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing typeExam entity.
     *
     * @Route("/{id}/edit", name="typeexam_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:typeexam:edit.html.twig")
     */
    public function editAction(Request $request, TypeExam $typeExam)
    {
        $editForm = $this->createForm(TypeExamType::class, $typeExam, array('establishment' => $this->getUser()->getEstablishment()))->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($typeExam);
            $this->flashSuccessMsg('typeExam.edit.success');
            return $this->redirectToRoute('typeexam_index');
        }

        return array(
            'typeExam' => $typeExam,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="typeexam_bulk_delete")
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
                $this->getEntityManager()->deleteAll(typeExam::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('typeExam.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('typeExam.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a typeExam entity.
     *
     * @Route("/{id}", name="typeexam_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, TypeExam $typeExam)
    {
        $form = $this->createDeleteForm($typeExam)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($typeExam);
            $this->flashSuccessMsg('typeExam.delete.one.success');
        }

        return $this->redirectToRoute('typeexam_index');
    }

    /**
     * Creates a form to delete a typeExam entity.
     *
     * @param TypeExam $typeExam The typeExam entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TypeExam $typeExam)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('typeexam_delete', array('id' => $typeExam->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get typeExam Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getTypeExamEntityManager()
    {
        if (!$this->has('sms.datatable.type_exam')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.type_exam');
    }}
