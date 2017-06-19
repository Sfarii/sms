<?php

namespace SMS\UserBundle\Repository;

use SMS\UserBundle\Entity\User;
use SMS\UserBundle\Entity\Student;
use Doctrine\ORM\EntityRepository;
use SMS\UserBundle\Repository\BaseUserRepositoryInterface;

/**
 * BaseUserRepository
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SNS
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.usernameCanonical = :username OR u.emailCanonical = :email')
            ->setParameter('username', mb_convert_case($username, MB_CASE_LOWER , "UTF-8"))
            ->setParameter('email', mb_convert_case($username, MB_CASE_LOWER , "UTF-8"))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string[] $criteria format: array('user' => <user_id>, 'name' => <name>)
     */
    public function findByUniqueCriteria(array $criteria)
    {
        // would use findOneBy() but Symfony expects a Countable object
        return $this->_em->getRepository(User::class)->findBy($criteria);
    }

    public function getAllStudents()
    {
      return $this->_em->createQueryBuilder()
                  ->select('count(u.id)')
                  ->from(Student::class,'s')
                  ->getQuery()
                  ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->where('u.emailCanonical = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.usernameCanonical = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByToken($token)
    {
    	return $this->createQueryBuilder('u')
            ->where('u.confirmationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByActivationToken($token)
    {
        return $this->createQueryBuilder('u')
            ->where('u.activationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
