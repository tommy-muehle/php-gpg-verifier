<?php

namespace TM\GPG\Verification;

use TM\GPG\Verification\Helper\Executor;
use TM\GPG\Verification\Helper\Verificator;

/**
 * @package TM\GPG\Verification
 */
class Verifier
{
    /**
     * @var Verificator
     */
    private $verificator;

    /**
     * @param string $executable
     */
    public function __construct($executable = '/usr/bin/gpg')
    {
        $this->verificator = new Verificator(new Executor($executable));
    }

    /**
     * @param string $file
     * @param string $signatureFile
     */
    public function verify($signatureFile, $file)
    {
        $this->verificator->verify($signatureFile, $file);
    }
}
