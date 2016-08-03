<?php

namespace TM\GPG\Verification\Exception;

/**
 * @package TM\GPG\Verification\Exception
 */
final class FailedVerificationException extends VerificationException
{
    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        $message = <<<EOF
Verification failed! Please run the command 
$ gpg --verify --status-fd 1 {file.sig} {file}
to get more details. In most cases you need to add the public key of the file author.
So please take a look at the documentation on 
> https://www.gnupg.org/gph/en/manual/book1.html
EOF;

        parent::__construct($message, $code, $previous);
    }
}
