<?php

namespace TM\GPG\Verification\Tests\Helper;

use phpmock\phpunit\PHPMock;
use TM\GPG\Verification\Exception\ExecutableException;
use TM\GPG\Verification\Helper\Executor;

/**
 * @package TM\GPG\Verification\Tests\Helper
 */
class ExecutorTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    public function testDisabledExecFunctionThrowsException()
    {
        $this->setExpectedException(
            ExecutableException::class,
            'Function "exec" not available!'
        );

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->setMethods(['canRun'])
            ->disableOriginalConstructor()
            ->getMock();

        $executor
            ->method('canRun')
            ->willReturn(false);

        /* @var $executor Executor */
        $executor->run(['--verify', '--status-fd 1', 'file.sig', 'file']);
    }

    public function testNotExecutableExecutableThrowsException()
    {
        $this->setExpectedException(
            ExecutableException::class,
            'Executable not executable!'
        );

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->setMethods(['isExist', 'isExecutable'])
            ->disableOriginalConstructor()
            ->getMock();

        $executor
            ->method('isExist')
            ->willReturn(true);

        $executor
            ->method('isExecutable')
            ->willReturn(false);

        /* @var $executor Executor */
        $executor->run(['--verify', '--status-fd 1', 'file.sig', 'file']);
    }

    public function testNotExistingExecutableThrowsException()
    {
        $this->setExpectedException(
            ExecutableException::class,
            'Executable not exist!'
        );

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->setMethods(['isExist'])
            ->disableOriginalConstructor()
            ->getMock();

        $executor
            ->method('isExist')
            ->willReturn(false);

        /* @var $executor Executor */
        $executor->run(['--verify', '--status-fd 1', 'file.sig', 'file']);
    }

    public function testInoperativeExecReturnsEmptyString()
    {
        $exec = $this->getFunctionMock('TM\GPG\Verification\Helper', 'exec');
        $exec->expects($this->once())->willReturnCallback(
            function ($command, &$output, &$returnCode) {
                $this->assertRegexp('/gpg \-\-verify \-\-status-fd 1 file.sig file/', $command);
                $output = [];
                $returnCode = 1;
            }
        );

        $executable = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->setMethods(['getRealPath'])
            ->disableOriginalConstructor()
            ->getMock();

        $executable
            ->method('getRealPath')
            ->willReturn('/usr/bin/gpg');

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->setMethods(['isExist', 'isExecutable'])
            ->setConstructorArgs([$executable])
            ->getMock();

        $executor
            ->method('isExist')
            ->willReturn(true);

        $executor
            ->method('isExecutable')
            ->willReturn(true);

        /* @var $executor Executor */
        $result = $executor->run(['--verify', '--status-fd 1', 'file.sig', 'file']);

        $this->assertEquals('', $result);
    }

    public function testCanReturnGoodSigOutput()
    {
        $exec = $this->getFunctionMock('TM\GPG\Verification\Helper', 'exec');
        $exec->expects($this->once())->willReturnCallback(
            function ($command, &$output, &$returnCode) {
                $this->assertRegexp('/gpg \-\-verify \-\-status-fd 1 file.sig file/', $command);
                $output = ['GOODSIG'];
                $returnCode = 0;
            }
        );

        $executable = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->setMethods(['getRealPath'])
            ->disableOriginalConstructor()
            ->getMock();

        $executable
            ->method('getRealPath')
            ->willReturn('/usr/bin/gpg');

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->setMethods(['isExist', 'isExecutable', 'canRun'])
            ->setConstructorArgs([$executable])
            ->getMock();

        $executor
            ->method('isExist')
            ->willReturn(true);

        $executor
            ->method('isExecutable')
            ->willReturn(true);

        $executor
            ->method('canRun')
            ->willReturn(true);

        $this->assertNotNull($executor->run(['--verify', '--status-fd 1', 'file.sig', 'file']));
    }
}
