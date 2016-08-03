<?php

namespace TM\GPG\Verification\Helper;

use TM\GPG\Verification\Exception\ExecutableException;

/**
 * @package TM\GPG\Verification\Helper
 */
class Executor
{
    /**
     * @var \SplFileInfo
     */
    private $executable;

    /**
     * @param \SplFileInfo $executable
     */
    public function __construct(\SplFileInfo $executable)
    {
        $this->executable = $executable;
    }

    /**
     * @return bool
     */
    public function isExist()
    {
        if (true === $this->executable->isFile() || true === $this->executable->isLink()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isExecutable()
    {
        return $this->executable->isExecutable();
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

        if (false === $this->isExist()) {
            throw new ExecutableException('Executable not exist!');
        }

        if (false === $this->isExecutable()) {
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
