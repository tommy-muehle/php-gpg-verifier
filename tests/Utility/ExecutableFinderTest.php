<?php

namespace TM\GPG\Verification\Tests\Utility;

use phpmock\phpunit\PHPMock;
use TM\GPG\Verification\Exception\NoExecutableFoundException;
use TM\GPG\Verification\Utility\ExecutableFinder;

/**
 * @package TM\GPG\Verification\Tests\Utility
 */
class ExecutableFinderTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    public function testCanFindABinary()
    {
        $path = $this
            ->getFunctionMock('TM\GPG\Verification\Utility', 'getenv')
            ->expects($this->once())
            ->willReturn('/usr/local/bin:/usr/bin:/bin');

        $fileExists = $this
            ->getFunctionMock('TM\GPG\Verification\Utility', 'file_exists')
            ->expects($this->any())
            ->willReturnCallback(function($path) {
                if ('/usr/bin/gpg' !== $path) {
                    return false;
                }

                return true;
            });

        $executable = ExecutableFinder::find();
        $this->assertEquals('/usr/bin/gpg', $executable->getPathname());
    }

    public function testUnAccessiblePathEnvVariableThrowsException()
    {
        $this->setExpectedException(NoExecutableFoundException::class);

        $path = $this
            ->getFunctionMock('TM\GPG\Verification\Utility', 'getenv')
            ->expects($this->once())
            ->willReturn(false);

        ExecutableFinder::find();
    }

    public function testNoBinaryInPathEnvVariableThrowsException()
    {
        $this->setExpectedException(NoExecutableFoundException::class);

        $path = $this
            ->getFunctionMock('TM\GPG\Verification\Utility', 'getenv')
            ->expects($this->once())
            ->willReturn('/usr/local/bin:/usr/bin:/bin');

        $fileExists = $this
            ->getFunctionMock('TM\GPG\Verification\Utility', 'file_exists')
            ->expects($this->exactly(3))
            ->willReturnCallback(function($executablePath) { return false; });

        ExecutableFinder::find();
    }
}
