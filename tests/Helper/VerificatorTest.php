<?php

namespace TM\GPG\Verification\Tests\Helper;

use phpmock\phpunit\PHPMock;
use TM\GPG\Verification\Exception\FailedVerificationException;
use TM\GPG\Verification\Exception\NotExistException;
use TM\GPG\Verification\Helper\Executor;
use TM\GPG\Verification\Helper\Verificator;

/**
 * @package TM\GPG\Verification\Tests\Helper
 */
class VerificatorTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var Verificator
     */
    private $verificator;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->verificator = new Verificator(new Executor);
    }

    public function testNotExistingFileThrowsException()
    {
        $this->setExpectedException(NotExistException::class);
        $this->verificator->verify('file.sig', 'file');
    }

    public function testNotValidSignatureThrowsException()
    {
        $this->setExpectedException(FailedVerificationException::class);

        $exec = $this->getFunctionMock('TM\GPG\Verification\Helper', 'exec');
        $exec->expects($this->once())->willReturnCallback(
            function ($command, &$output, &$returnCode) {
                $this->assertRegexp('/gpg \-\-verify \-\-status-fd 1/', $command);
                $this->assertRegexp('/box\-2\.7\.4\.phar\.sig/', $command);
                $this->assertRegexp('/box\-2\.7\.4\.phar/', $command);

                $output = <<<EOT
gpg: Unterschrift vom Mi 27 Jul 15:59:10 2016 CEST mittels RSA-Schlüssel ID 41515FE8
[GNUPG:] ERRSIG 293D771241515FE8 1 10 00 1469627950 9
[GNUPG:] NO_PUBKEY 293D771241515FE8
gpg: Unterschrift kann nicht geprüft werden: Öffentlicher Schlüssel nicht gefunden
EOT;

                $returnCode = 0;
            }
        );

        $this->verificator->verify(
            __DIR__ . '/../Fixtures/box-2.7.4.phar.sig',
            __DIR__ . '/../Fixtures/box-2.7.4.phar'
        );
    }

    public function testCanVerifyAValidSignature()
    {
        $exec = $this->getFunctionMock('TM\GPG\Verification\Helper', 'exec');
        $exec->expects($this->once())->willReturnCallback(
            function ($command, &$output, &$returnCode) {
                $this->assertRegexp('/gpg \-\-verify \-\-status-fd 1/', $command);
                $this->assertRegexp('/box\-2\.7\.4\.phar\.sig/', $command);
                $this->assertRegexp('/box\-2\.7\.4\.phar/', $command);

                $output = <<<EOT
gpg: Unterschrift vom Mi 27 Jul 15:59:10 2016 CEST mittels RSA-Schlüssel ID 41515FE8
[GNUPG:] SIG_ID m89rvRG+oGqzFADfSM+tHId4PJ4 2016-07-27 1469627950
[GNUPG:] GOODSIG 293D771241515FE8 Kevin G. Herrera <kevin@herrera.io>
gpg: Korrekte Unterschrift von "Kevin G. Herrera <kevin@herrera.io>"
[GNUPG:] VALIDSIG 32E4B74757B1D65234FC389F293D771241515FE8 2016-07-27 1469627950 0 4 0 1 10 00 32E4B74757B1D65234FC389F293D771241515FE8
[GNUPG:] TRUST_UNDEFINED
gpg: WARNUNG: Dieser Schlüssel trägt keine vertrauenswürdige Signatur!
gpg:          Es gibt keinen Hinweis, daß die Signatur wirklich dem vorgeblichen Besitzer gehört.
Haupt-Fingerabdruck  = 32E4 B747 57B1 D652 34FC  389F 293D 7712 4151 5FE8
EOT;

                $returnCode = 0;
            }
        );

        $this->verificator->verify(
            __DIR__ . '/../Fixtures/box-2.7.4.phar.sig',
            __DIR__ . '/../Fixtures/box-2.7.4.phar'
        );
    }
}
