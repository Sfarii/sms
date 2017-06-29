<?php

namespace SMS\StudyPlanBundle\Controller;

use SMS\StudyPlanBundle\Entity\Note;
use SMS\StudyPlanBundle\Entity\Exam;
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
     * @Route("/new/{id}", name="note_new", options={"expose"=true})
     * @Method({"GET", "POST"})
     * @Template("SMSStudyPlanBundle:note:new.html.twig")
     */
    public function newAction(Request $request , Exam $exam)
    {
        $notes = $this->getEntityManager()->addNote($exam , $this->getUser());
        $this->getRequest()->getSession()->set("exam" , $exam->getId());
        return $this->redirectToRoute('note_index' , array("id" => $exam->getId()));
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

        if (!$session->has("exam")){
            return $this->redirectToRoute('exam_index');
        }

        $exam = $session->get("exam");
        $notes = $this->getDataTableNoteEntityManager();
        $notes->buildDatatable();

        $query = $this->getDataTableQuery()->getQueryFrom($notes);


        $function = function($qb) use ($exam)
        {
            $qb->join('note.exam', 'exam');
            $qb->andWhere("exam.id = :id ");
            $qb->setParameter('id', $exam);
        };

        $query->addWhereAll($function);
        return $query->getResponse();
    }

    /**
     * add/Edit Student note .
     *
     * @Route("/add_edit_notes/{id}" , name="note_index")
     * @Method("GET")
     * @Template("SMSStudyPlanBundle:note:index.html.twig")
     */
    public function indexAction(Exam $exam)
    {
        $notes = $this->getDataTableNoteEntityManager();
        $notes->buildDatatable();

        $session = $this->getRequest()->getSession();

        if (!$session->has("exam")){
            throw $this->createNotFoundException('Object Not Found');
        }


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
