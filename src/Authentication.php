<?php declare(strict_types=1);
/**
 * Interface for authentication classes
 *
 * PHP version 7.0
 *
 * @category   CodeCollab
 * @package    Authentication
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 Pieter Hordijk <https://github.com/PeeHaa>
 * @license    See the LICENSE file
 * @version    1.0.0
 */
namespace CodeCollab\Authentication;

/**
 * Interface for authentication classes
 *
 * @category   CodeCollab
 * @package    Authentication
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
interface Authentication
{
    /**
     * Checks whether the user is logged in
     *
     * @return bool True when the user is logged in
     */
    public function isLoggedIn(): bool;

    /**
     * Logs the user out
     */
    public function logOut();

    /**
     * Checks whether the current user is an administrator
     *
     * @return bool True when the user is an administrator
     */
    public function isAdmin(): bool;

    /**
     * Magic getter
     *
     * Gets a property of the user
     *
     * @return mixed The property of the user if exists or a placeholder otherwise
     */
    public function __get($key);
}
