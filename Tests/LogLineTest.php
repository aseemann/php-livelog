<?php
/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */

namespace ASeemann\PhpLiveLog\Tests;

use ASeemann\PhpLiveLog\LogLine;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LogLineTest extends TestCase
{
    public function testLogLine()
    {
        $line = new LogLine(
            'test',
            'test',
            'wi293u8',
            '/test.php',
            LogLevel::DEBUG,
            'test',
            [
                'a' => 'b'
            ]
        );

        $this->assertJson($line->getLine());
    }
}
