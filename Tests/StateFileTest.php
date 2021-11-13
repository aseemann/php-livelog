<?php
/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

namespace ASeemann\PhpLiveLog\Tests;

use ASeemann\PhpLiveLog\Cookie;
use ASeemann\PhpLiveLog\StateFile;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class StateFileTest extends TestCase
{
    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var Uuid
     */
    private $uuid;

    public function setUp(): void
    {
        parent::setUp();

        $this->uuid = Uuid::uuid4();

        $mockObject = $this->getMockBuilder(Cookie::class)
                           ->onlyMethods(['setCookie'])
                           ->getMock();

        $mockObject->setUuid($this->uuid);

        $_COOKIE[Cookie::COOKIE_NAME] = $this->uuid->toString();

        $mockObject->method('setCookie')
                   ->willReturnSelf();

        $this->cookie = $mockObject;
    }

    public function testStateFileWrapper(): void
    {
        $stateFile = new StateFile($this->cookie);

        $this->assertSame(0, $stateFile->getLine());

        $stateFile->setLine(20);

        $this->assertSame(20, $stateFile->getLine());
    }

    public function tearDown(): void
    {
        parent::tearDown();

        shell_exec('rm -f /tmp/phpLiveLog*.state');
    }
}
