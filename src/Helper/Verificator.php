<?php

namespace TM\GPG\Verification\Helper;

use TM\GPG\Verification\Exception\FailedVerificationException;
use TM\GPG\Verification\Exception\NotExistException;

/**
 * @package TM\GPG\Verification\Helper
 */
class Verificator
{
    /**
     * @var Executor
     */
    private $executor;

    /**
     * @param Executor $executor
     */
    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param \SplFileInfo $signature
     * @param \SplFileInfo $file
     *
     * @throws FailedVerificationException
     * @throws NotExistException
     */
    public function verify(\SplFileInfo $signature, \SplFileInfo $file)
    {
        if (false === $signature->isFile() || false === $file->isFile()) {
            throw new NotExistException('One given file are not exist!');
        }

        $result = $this->executor->run([
            '--verify',
            '--status-fd 1',
            $signature->getRealPath(),
            $file->getRealPath(),
        ]);

        if (false === (bool) strpos($result, 'GOODSIG')) {
            throw new FailedVerificationException;
        }
    }
}
