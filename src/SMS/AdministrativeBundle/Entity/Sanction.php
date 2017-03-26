<?php

namespace SMS\AdministrativeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sanction
 *
 * @ORM\Table(name="sanction")
 * @ORM\Entity(repositoryClass="SMS\AdministrativeBundle\Repository\SanctionRepository")
 */
class Sanction
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cause", type="text")
     */
    private $cause;

    /**
     * Many Sanctions have One Student.
     * @ORM\ManyToOne(targetEntity="SMS\UserBundle\Entity\Student")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $student;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Sanction
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set cause
     *
     * @param string $cause
     * @return Sanction
     */
    public function setCause($cause)
    {
        $this->cause = $cause;

        return $this;
    }

    /**
     * Get cause
     *
     * @return string 
     */
    public function getCause()
    {
        return $this->cause;
    }
}
