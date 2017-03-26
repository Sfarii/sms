<?php

namespace SMS\Classes\Services;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2017, SMS
 * @package SMS\Classes\Services
 */

class EntityManager 
{
    /**
	* @var Doctrine\ORM\EntityManager
	*/
	private $_em;

	/**
	* @var Knp\Component\Pager\Paginator
	*/
	private $_knpPaginator;

	/**
	* @var int
	*/
	private $_limitPerPage;

	/**
	* @var String
	*/
	private $_searchField;

	/**
	* @param Doctrine\ORM\EntityManager $em
    * @param Knp\Component\Pager\Paginator $knpPaginator
    * @param int $limitPerPage
	*/
	public function __construct($em , $knpPaginator  )
    {
        $this->_em = $em;
        $this->_knpPaginator = $knpPaginator;
    }

    /**
    * @param Request $request
    * @param String $className
    */
    public function getEntityBy($className , $request)
    {
    	$searchField = $request->query->get($this->_searchField, null);
    	if (is_null($searchField)) {
    		$result = $this->_em->getRepository($className)->findAll();
    	}else{
    		$result = $this->_em->getRepository($className)->findByAnything($searchField);
    	}
    	
        return $this->_knpPaginator->paginate(
            $result, /* query NOT result */
            $request->query->getInt('page', 1) /*page number*/,
            $this->_limitPerPage /*limit per page*/
        );
    }

    /**
    * insert entity in the database
    * @param Object $object
    */
    public function insert($object)
    {
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
    public function deleteAll($className ,$ids = array())
    {
    	foreach ($ids as $id) {
    		$object = $this->_em->getRepository($className)->find($id);
    		if ($object){
    			$this->delete($object);
    		}
    	}
    }

    /**
    * Fix the Pagnaitor limit per page 
    * @param int $limitPerPage
	*/
    public function setPaginatorLimitPerPage($limitPerPage)
    {
        $this->_limitPerPage = $limitPerPage;
    }

    /**
    * get search field name the same in the twig
    * @param String $searchField
	*/
    public function setSearchField($searchField)
    {
        $this->_searchField = $searchField;
    }

}
