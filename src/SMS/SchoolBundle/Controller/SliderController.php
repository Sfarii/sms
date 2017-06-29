<?php

namespace SMS\SchoolBundle\Controller;

use SMS\SchoolBundle\Entity\Slider;
use SMS\SchoolBundle\Form\SliderType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Slider controller.
 *
 * @Route("slider")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\SchoolBundle\Controller
 *
 */
class SliderController extends BaseController
{
    /**
     * Lists all slider entities.
     *
     * @Route("/", name="slider_index")
     * @Method("GET")
     * @Template("SMSSchoolBundle:slider:index.html.twig")
     */
    public function indexAction()
    {
        $sliders = $this->getSliderEntityManager();
        $sliders->buildDatatable();

        return array('sliders' => $sliders);
    } /**
     * Lists all slider entities.
     *
     * @Route("/results", name="slider_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $sliders = $this->getSliderEntityManager();
        $sliders->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($sliders);

        return $query->getResponse();
    }
    /**
     * Creates a new slider entity.
     *
     * @Route("/new", name="slider_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:slider:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $slider = new Slider();
        $form = $this->createForm(SliderType::class, $slider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $this->getEntityManager()->insert($slider , $this->getUser());
            $this->flashSuccessMsg('slider.add.success');
            return $this->redirectToRoute('slider_index');
        }

        return array(
            'slider' => $slider,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a slider entity.
     *
     * @Route("/{id}", name="slider_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction(Slider $slider)
    {
        $deleteForm = $this->createDeleteForm($slider);

        return $this->render('SMSSchoolBundle:slider:show.html.twig', array(
            'slider' => $slider,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing slider entity.
     *
     * @Route("/{id}/edit", name="slider_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSSchoolBundle:slider:edit.html.twig")
     */
    public function editAction(Request $request, Slider $slider)
    {
        $editForm = $this->createForm(SliderType::class, $slider)->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid() && $editForm->get('save')->isClicked()) {
            $this->getEntityManager()->update($slider);
            $this->flashSuccessMsg('slider.edit.success');
            return $this->redirectToRoute('slider_index');
        }

        return array(
            'slider' => $slider,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete", name="slider_bulk_delete")
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
                $this->getEntityManager()->deleteAll(slider::class ,$choices);
            } catch (\Exception $e) {
                return new Response($this->get('translator')->trans('slider.delete.fail'), 200);
            }


            return new Response($this->get('translator')->trans('slider.delete.success'), 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * Deletes a slider entity.
     *
     * @Route("/{id}", name="slider_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Slider $slider)
    {
        $form = $this->createDeleteForm($slider)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->delete($slider);
            $this->flashSuccessMsg('slider.delete.one.success');
        }

        return $this->redirectToRoute('slider_index');
    }

    /**
     * Creates a form to delete a slider entity.
     *
     * @param Slider $slider The slider entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Slider $slider)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('slider_delete', array('id' => $slider->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Get slider Entity Manager Service.
     *
     * @return SMS\Classes\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getSliderEntityManager()
    {
        if (!$this->has('sms.datatable.slider')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.slider');
    }}
