<?php declare(strict_types=1);
/**
 * Smple user authentication class
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

use CodeCollab\Http\Session\Session;

/**
 * Smple user authentication class
 *
 * @category   CodeCollab
 * @package    Authentication
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class User implements Authentication
{
    /**
     * @var int The cost used to hash passwords
     */
    const PASSWORD_COST = 14;

    /**
     * @var \CodeCollab\Http\Session\Session The session object
     */
    protected $session;

    /**
     * Creates instance
     *
     * @param \CodeCollab\Http\Session\Session $session The session object
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Checks whether the user is logged in
     *
     * @return bool True when the user is logged in
     */
    public function isLoggedIn(): bool
    {
        return $this->session->exists('user');
    }

    /**
     * Logs a user in
     *
     * @param string $password The password
     * @param string $hash     The hash of the user´s password
     * @param array  $user     The user data
     *
     * @return bool True when the user successfully logged in
     */
    public function logIn(string $password, string $hash, array $user): bool
    {
        if (!$user || !password_verify($password, $hash)) {
            return false;
        }

        $this->session->set('user', $user);

        return true;
    }

    /**
     * Logs a user in using the remember me cookie
     *
     * @param array $user The user data
     *
     * @return bool True when the user successfully logged in
     */
    public function logInRememberMe(array $user): bool
    {
        if (!$user) {
            return false;
        }

        $this->session->set('user', $user);

        return true;
    }

    /**
     * Checks whether the user's password needs to be rehashed
     *
     * @param string $hash The hash of the user´s password
     *
     * @return bool True when the password needs to be rehashed
     */
    public function needsRehash(string $hash): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return password_needs_rehash(
            $hash,
            PASSWORD_DEFAULT,
            ['cost' => self::PASSWORD_COST]
        );
    }

    /**
     * Rehashes the password of the user
     *
     * @param string $password The password to rehash
     *
     * @return string The hashed password
     */
    public function rehash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => self::PASSWORD_COST]);
    }

    /**
     * Logs the user out
     */
    public function logOut()
    {
        $this->session->destroy();
    }

    /**
     * Checks whether the current user is an administrator
     *
     * @return bool True when the user is an administrator
     */
    public function isAdmin(): bool
    {
        return $this->isLoggedIn()
            && isset($this->session->get('user')['admin'])
            && $this->session->get('user')['admin'] === true;
    }

    /**
     * Magic getter
     *
     * Gets a property of the user
     *
     * @return mixed The property of the user if exists or a placeholder otherwise
     */
    public function __get($key)
    {
        $user = $this->session->get('user');

        if (array_key_exists($key, $user)) {
            return $user[$key];
        }

        return '{{' . $key . '}}';
    }
}
