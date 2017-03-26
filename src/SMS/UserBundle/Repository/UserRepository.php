<?php

namespace SMS\UserBundle\Repository;

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