<?php

namespace ASeemann\PhpLiveLog\Tests;


use ASeemann\PhpLiveLog\Constants;
use ASeemann\PhpLiveLog\GarbageCollector;
use PHPUnit\Framework\TestCase;

/**
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 */
class GarbageCollectorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $testFiles = [
            'ueguh' => time() - 3636,
            'dugeozpfrher' => time() - 3600,
            'kjhgdpue' => time() - 10,
            'ksdjhödueh' => time() - 60,
        ];


        foreach ($testFiles as $testFile => $time) {
            $path = sprintf(Constants::LOG_FILE_PATH_PATTERN, $testFile);
            file_put_contents($path, date('d-m-Y H:i:s'));
            touch($path, $time);

            $path = sprintf(Constants::STATE_FILE_PATH_PATTERN, $testFile);
            file_put_contents($path, date('d-m-Y H:i:s'));
            touch($path, $time);
        }
    }

    public function testPurgeOutdatedFiles(): void
    {

        $this->assertCount(8, $this->getFileList());

        $collector = new GarbageCollector();

        $this->assertCount(4, $collector->getDeleteCandidates());

        $collector->removeOutdatedFiles();

        $this->assertCount(4, $this->getFileList());

        $this->removeAllTestFiles();

        $this->assertSame([], $collector->getDeleteCandidates());
        $this->assertCount(0, $collector->getDeleteCandidates());
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->removeAllTestFiles();
    }

    private function removeAllTestFiles(): void
    {
        foreach ($this->getFileList() as $file) {
            unlink($file);
        }
    }

    private function getFileList() : array
    {
        return glob('/tmp/phpLiveLog*');
    }
}
