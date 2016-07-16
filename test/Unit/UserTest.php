<?php declare(strict_types=1);

namespace CodeCollabTest\Unit\Authentication;

use CodeCollab\Authentication\User;
use CodeCollab\Authentication\Authentication;
use CodeCollab\Http\Session\Session;

class UserTest extends \PHPUnit_Framework_TestCase
{
    protected $loginCredentials = [];

    public function setUp()
    {
        $this->loginCredentials = [
            'password'  => 'password',
            'hash'      => '$2y$14$O3M/sAF6.woKclbHMK0yp.lxJ5x9FGCE090iJusBktDPNrxh5ZVbW',
            'userArray' => [
                'username' => 'PeeHaa',
            ],
        ];
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     */
    public function testImplementsCorrectInterface()
    {
        $this->assertInstanceOf(Authentication::class, new User($this->createMock(Session::class)));
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::isLoggedIn
     */
    public function testIsLoggedInNotLoggedIn()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('user'))
            ->willReturn(false)
        ;

        $this->assertFalse((new User($session))->isLoggedIn());
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::isLoggedIn
     */
    public function testIsLoggedInLoggedIn()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('user'))
            ->willReturn(true)
        ;

        $this->assertTrue((new User($session))->isLoggedIn());
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::logIn
     */
    public function testLogInFailesWhenUserArrayIsEmpty()
    {
        $session = $this->createMock(Session::class);

        $this->assertFalse(
            (new User($session))->logIn(
                $this->loginCredentials['password'],
                $this->loginCredentials['hash'],
                []
            )
        );
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::logIn
     */
    public function testLogInFailesWhenPasswordDoesntMatch()
    {
        $session = $this->createMock(Session::class);

        $this->assertFalse(
            (new User($session))->logIn(
                'wrongpassword',
                $this->loginCredentials['hash'],
                $this->loginCredentials['userArray']
            )
        );
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::logIn
     */
    public function testLogInSucceeds()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo('user'), $this->equalTo($this->loginCredentials['userArray']))
        ;

        $this->assertTrue(
            (new User($session))->logIn(
                'password',
                $this->loginCredentials['hash'],
                $this->loginCredentials['userArray']
            )
        );
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::logInRememberMe
     */
    public function testLogInRememberMeFailsOnEmptyUserArray()
    {
        $session = $this->createMock(Session::class);

        $this->assertFalse(
            (new User($session))->logInRememberMe([])
        );
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::logInRememberMe
     */
    public function testLogInRememberMeSucceeds()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo('user'), $this->equalTo($this->loginCredentials['userArray']))
        ;

        $this->assertTrue(
            (new User($session))->logInRememberMe($this->loginCredentials['userArray'])
        );
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::needsRehash
     */
    public function testNeedsRehashDoesntNeedRehashWhenNotLoggedIn()
    {
        $session = $this->createMock(Session::class);

        $this->assertFalse(
            (new User($session))->needsRehash($this->loginCredentials['hash'])
        );
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::needsRehash
     */
    public function testNeedsRehashDoesntNeedRehashWhenHashIsAlreadyUpToDate()
    {
        $user = new User($this->createMock(Session::class));

        $this->assertTrue(
            $user->logIn(
                'password',
                $this->loginCredentials['hash'],
                $this->loginCredentials['userArray']
            )
        );

        $this->assertFalse($user->needsRehash($this->loginCredentials['hash']));
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::needsRehash
     */
    public function testNeedsRehashDoesNeedRehash()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('user'))
            ->willReturn(true)
        ;

        $user = new User($session);

        $this->assertTrue(
            $user->logIn(
                'password',
                $this->loginCredentials['hash'],
                $this->loginCredentials['userArray']
            )
        );

        $this->assertTrue($user->needsRehash('$2y$13$d1dLbDd4MkvSd0hk/57BqedPb7NtF6I/68Dz8bpe0VzDArQJN9KCq'));
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::rehash
     */
    public function testRehash()
    {
        $user = new User($this->createMock(Session::class));

        $this->assertRegExp('/^\$2y\$14\$(.*)$/', $user->rehash('password'));
        $this->assertSame(60, strlen($user->rehash('password')));
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::logOut
     */
    public function testLogOut()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('destroy')
        ;

        (new User($session))->logOut();
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::isLoggedIn
     * @covers CodeCollab\Authentication\User::isAdmin
     */
    public function testIsAdminNotAdminBecauseNotLoggedIn()
    {
        $this->assertFalse((new User($this->createMock(Session::class)))->isAdmin());
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::isLoggedIn
     * @covers CodeCollab\Authentication\User::isAdmin
     */
    public function testIsAdminNotAdminKeyDoesntExistInUserArray()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('user'))
            ->willReturn(true)
        ;

        $session
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('user'))
            ->willReturn([])
        ;

        $this->assertFalse((new User($session))->isAdmin());
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::isLoggedIn
     * @covers CodeCollab\Authentication\User::isAdmin
     */
    public function testIsAdminNotAdminValueIsFalse()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('user'))
            ->willReturn(true)
        ;

        $session
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo('user'))
            ->willReturn(['admin' => false])
        ;

        $this->assertFalse((new User($session))->isAdmin());
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::isLoggedIn
     * @covers CodeCollab\Authentication\User::isAdmin
     */
    public function testIsAdminAdmin()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('user'))
            ->willReturn(true)
        ;

        $session
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->equalTo('user'))
            ->willReturn(['admin' => true])
        ;

        $this->assertTrue((new User($session))->isAdmin());
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::__get
     */
    public function testGetReturnsPlaceholderOnNonExistentKey()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('user'))
            ->willReturn([])
        ;

        $this->assertSame('{{foobar}}', (new User($session))->foobar);
    }

    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::__get
     */
    public function testGetReturnsValueOnExistingKey()
    {
        $session = $this->createMock(Session::class);

        $session
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('user'))
            ->willReturn(['username' => 'PeeHaa'])
        ;

        $this->assertSame('PeeHaa', (new User($session))->username);
    }
}
