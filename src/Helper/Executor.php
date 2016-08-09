<?php

namespace TM\GPG\Verification\Helper;

use TM\GPG\Verification\Exception\ExecutableException;
use TM\GPG\Verification\Model\Executable;

/**
 * @package TM\GPG\Verification\Helper
 */
class Executor
{
    /**
     * @var Executable
     */
    private $executable;

    /**
     * @param Executable $executable
     */
    public function __construct(Executable $executable)
    {
        $this->executable = $executable;
    }

    /**
     * @return bool
     */
    public function canRun()
    {
        return function_exists('exec');
    }

    /**
     * @param array $arguments
     *
     * @return string
     * @throws ExecutableException
     */
    public function run(array $arguments)
    {
        if (false === $this->canRun()) {
            throw new ExecutableException('Function "exec" not available!');
        }

        if (false === $this->executable->isExist()) {
            throw new ExecutableException('Executable not exist!');
        }

        if (false === $this->executable->isExecutable()) {
            throw new ExecutableException('Executable not executable!');
        }

        $command = sprintf(
            '%s %s 2>&1',
            $this->executable->getRealPath(),
            implode(' ', $arguments)
        );

        /* @var $output array */
        @exec($command, $output, $returnCode);

        if (0 !== $returnCode) {
            return '';
        }

        return implode(PHP_EOL, $output);
    }
}
