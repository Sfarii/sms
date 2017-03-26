<?php

namespace SMS\UserBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * BaseUserInterface
 *
 * @author Rami Sfari <rami2sfari@gmail.com>
 * @copyright Copyright (c) 2016, SNS
 */

interface UserRepositoryInterface extends UserLoaderInterface
{
	/**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username);

    /**
     * load users by email
     *
     * @param string $username
     * @return BaseUser or Null
     */
    public function findUserByEmail($email);

    /**
     * load users by email
     *
     * @param string $username
     * @return BaseUser or Null
     */
    public function findUserByUsername($username);

    /**
     * load users by the confirmation token
     *
     * @param string $token
     * @return BaseUser or Null
     */
    public function findUserByToken($token);

    /**
     * load users by the acctivation token
     *
     * @param string $token
     * @return BaseUser or Null
     */
    public function findUserByActivationToken($token);
}