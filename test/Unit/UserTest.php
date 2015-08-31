<?php declare(strict_types=1);

namespace CodeCollabTest\Unit\Authentication;

use CodeCollab\Authentication\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers CodeCollab\Authentication\User::__construct
     * @covers CodeCollab\Authentication\User::isLoggedIn
     */
    public function testIsLoggedInNotLoggedIn()
    {
        $session = $this->getMock('CodeCollab\Http\Session\Session');

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
        $session = $this->getMock('CodeCollab\Http\Session\Session');

        $session
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('user'))
            ->willReturn(true)
        ;

        $this->assertTrue((new User($session))->isLoggedIn());
    }
}
