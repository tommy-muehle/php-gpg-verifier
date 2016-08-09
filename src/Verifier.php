<?php

namespace TM\GPG\Verification;

use TM\GPG\Verification\Utility\ExecutableFinder;
use TM\GPG\Verification\Helper\Executor;
use TM\GPG\Verification\Helper\Verificator;
use TM\GPG\Verification\Model\Executable;
use TM\GPG\Verification\Model\File;

/**
 * @package TM\GPG\Verification
 */
class Verifier
{
    /**
     * @var Executor
     */
    private $executor;

    /**
     * @param string $executable
     */
    public function __construct($executable = null)
    {
        if (true === is_string($executable)) {
            $executable = new Executable($executable);
        }

        if (!$executable instanceof Executable) {
            $executable = ExecutableFinder::find();
        }

        $this->executor = new Executor($executable);
    }

    /**
     * @param string $file
     * @param string $signatureFile
     */
    public function verify($signatureFile, $file)
    {
        $verificator = new Verificator($this->executor);
        $verificator->verify(new File($signatureFile), new File($file));
    }
}
