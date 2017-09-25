<?php

namespace SMS\EstablishmentBundle\Services;

use Doctrine\ORM\EntityManager;
/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 */

class EstablishmentEntityManager
{
    /**
    * @var Doctrine\ORM\EntityManager
    */
    private $_em;

    /**
    * @var Symfony\Component\Translation\DataCollectorTranslator
    */
    private $_translator;

    /**
     * @var String Class Names
     */
    private $studentClass;
    private $courseClass;
    private $sectionClass;
    private $professorClass;

    /**
    * @param Doctrine\ORM\EntityManager $em
    * @param Symfony\Component\Translation\DataCollectorTranslator $translator
    */
    public function __construct(EntityManager $em , $translator)
    {
        $this->_em = $em;
        $this->_translator = $translator;
    }

    /**
    * @param String $courseClass
    */
    public function setCourseClass($courseClass)
    {
      $this->courseClass = $courseClass;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setProfessorClass($professorClass)
    {
      $this->professorClass = $professorClass;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setSectionClass($sectionClass)
    {
      $this->sectionClass = $sectionClass;
    }

    /**
    * @param String $attendanceStudentClass
    */
    public function setStudentClass($studentClass)
    {
      $this->studentClass = $studentClass;
    }

    /**
    * insert entity in the database
    * @param Object $object
    * @param User $user
    */
    public function insert($object, $user = null)
    {
        if (!is_null($user)){
          $object->setUser($user);
        }
        $this->_em->persist($object);
        $this->_em->flush($object);
    }

    /**
    * update entity in the database
    * @param Object $object
    */
    public function update($object)
    {
        $this->_em->flush($object);
    }

    /**
    * delete one entity from the database
    * @param Object $object
    */
    public function delete($object)
    {
        $this->_em->remove($object);
        $this->_em->flush();
    }

    /**
    * delete multiple entity from the database
    * @param Object $object
    */
    public function deleteAll($className, $choices = array())
    {
        $repository = $this->_em->getRepository($className);

        foreach ($choices as $choice) {
            $object = $repository->find($choice['value']);


            try {
                if ($object) {
                    $this->_em->remove($object);
                }
            } catch (Exception $e) {
                throw new Exception("Error this Entity has child ", 1);
            }
        }

        $this->_em->flush();
    }

    /**
    * establishment Info
    * @param Object $establishment
    */
    public function establishmentInfo($establishment)
    {
      $students = $this->_em->getRepository($this->studentClass)->findStatsByEstablishment($establishment);
      $professors = $this->_em->getRepository($this->professorClass)->findStatsByEstablishment($establishment);

      $statsStudents = array_map(function ($value){$value['name'] = $this->_translator->trans($value['name']);return $value;}, $students);
      $statsProfessors = array_map(function ($value){$value['name'] = $this->_translator->trans($value['name']);return $value;}, $professors);

      $courses = $this->_em->getRepository($this->courseClass)->totalSessionsByEstablishment($establishment);

      return array('statsStudents' => $statsStudents, 'statsProfessors' => $statsProfessors, 'students' => array_sum (array_column($students , 'value')),'professors' => array_sum (array_column($professors , 'value')) , 'courses' => $courses);
    }
}
