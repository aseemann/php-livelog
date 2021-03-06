<?php
/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

namespace ASeemann\PhpLiveLog\Tests;

use ASeemann\PhpLiveLog\Cookie;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\Validator;
use Ramsey\Uuid\Uuid;

/**
 * Class CookieTest
 *
 * @package ASeemann\PhpLiveLog\Tests
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */
class CookieTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $_SERVER['HTTP_HOST'] = 'local.dev.test';
    }

    public function testHasCookie(): void
    {
        $cookie = new Cookie();

        $this->assertFalse($cookie->hasCookie());

        $_COOKIE[Cookie::COOKIE_NAME] = 'test';

        $this->assertTrue($cookie->hasCookie());
    }

    public function testGetCookie(): void
    {
        $cookie = new Cookie();

        $uuid = Uuid::uuid4();
        $cookie->setUuid($uuid);

        $_COOKIE[Cookie::COOKIE_NAME] = $uuid->toString();

        $this->assertTrue($cookie->isCookieValid());
        $this->assertTrue($cookie->hasCookie());
        $this->assertSame($cookie->getCookie(), $uuid->toString());
    }

    public function testCreateCookieOnGet(): void
    {
        $mockObject = $this->getMockBuilder(Cookie::class)
            ->onlyMethods(['setCookie'])
            ->getMock();

        $_COOKIE[Cookie::COOKIE_NAME] = 'Test';

        $mockObject->method('setCookie')
            ->willReturnSelf();

        $this->assertIsString($mockObject->getCookie());
        $this->assertSame('Test', $mockObject->getCookie());
    }

}
