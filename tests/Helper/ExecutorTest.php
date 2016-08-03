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

    /**
     * @var Executor
     */
    private $executor;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->executor = new Executor('/usr/local/bin/gpg');
    }

    public function testDisabledExecFunctionThrowsException()
    {
        $this->setExpectedException(
            ExecutableException::class,
            'Function "exec" not available!'
        );

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->setMethods(['canRun'])
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
            'Executable not exist or not executable!'
        );

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->setMethods(['isExecutable'])
            ->getMock();

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
            'Executable not exist or not executable!'
        );

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->setMethods(['isExist'])
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
                $output = '';
                $returnCode = 1;
            }
        );

        $this->assertEquals(
            '',
            $this->executor->run(['--verify', '--status-fd 1', 'file.sig', 'file'])
        );
    }

    public function testCanReturnGoodSigOutput()
    {
        $exec = $this->getFunctionMock('TM\GPG\Verification\Helper', 'exec');
        $exec->expects($this->once())->willReturnCallback(
            function ($command, &$output, &$returnCode) {
                $this->assertRegexp('/gpg \-\-verify \-\-status-fd 1 file.sig file/', $command);
                $output = 'GOODSIG';
                $returnCode = 0;
            }
        );

        $this->assertNotNull(
            $this->executor->run(['--verify', '--status-fd 1', 'file.sig', 'file'])
        );
    }
}