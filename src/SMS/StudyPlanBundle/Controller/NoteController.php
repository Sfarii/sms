<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Note;
use SMS\StudyPlanBundle\Form\NoteType;
use API\BaseController\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Note controller.
 *
 * @Route("note")
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\StudyPlanBundle\Controller
 *
 */
class NoteController extends BaseController
{
    /**
     * Creates a new note entity.
     *
     * @Route("/new", name="note_index", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("smsstudyplanbundle/note/new.html.twig")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(NoteType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked()) {
            $notes = $this->getEntityManager()->addNote($form , $this->getUser());
            // set the array of IDS in the session
            $session = $this->getRequest()->getSession();
            $session->set("notes", $notes);
            
            return $this->forward('SMSStudyPlanBundle:Note:index');
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Lists all professor entities.
     *
     * @Route("/results", name="note_results")
     * @Method("GET")
     * @return Response
     */
    public function indexResultsAction()
    {
        $session = $this->getRequest()->getSession();
        
        if (!$session->has("notes")){
            throw $this->createNotFoundException('Object Not Found');
        }

        $notes = $this->getDataTableNoteEntityManager();
        $notes->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($notes);

        
        $function = function($qb) use ($session)
        {
            $qb->andWhere("note.id IN (:p)");
            $qb->setParameter('p', $session->get("notes"));
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }

    /**
     * clear session.
     *
     * @Route("/clear", name="clear_results" , options={"expose"=true})
     * @Method("DELETE")
     * @return Response
     */
    public function clearAction()
    {
        $session = $this->getRequest()->getSession();
        
        if ($session->has("notes")){
            $session->clear();
        }

        return $this->redirectToRoute('note_index');
    }

    /**
     * add/Edit Student note .
     *
     * @Route("/add_edit_notes")
     * @Method("GET")
     * @Template("smsstudyplanbundle/note/index.html.twig")
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        if (!$session->has("notes")){
            return $this->redirectToRoute('note_index');
        }

        $notes = $this->getDataTableNoteEntityManager();
        $notes->buildDatatable();

        return array('notes' => $notes);
    }

    /**
     * Get professor Entity Manager Service.
     *
     * @return API\Services\EntityManager
     *
     * @throws \NotFoundException
     */
    protected function getDataTableNoteEntityManager()
    {
        if (!$this->has('sms.datatable.note')){
           throw $this->createNotFoundException('Service Not Found');
        }

        return $this->get('sms.datatable.note');
    }

}
