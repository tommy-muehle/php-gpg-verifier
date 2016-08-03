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
    public function testNotExistingFileThrowsException()
    {
        $this->setExpectedException(
            NotExistException::class,
            'One given file are not exist!'
        );

        $verificator = $this
            ->getMockBuilder(Verificator::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['verify'])
            ->getMock();

        /* @var $verificator Verificator */
        $verificator->verify(new \SplFileInfo('foo'), new \SplFileInfo('bar'));
    }

    public function testNotValidSignatureThrowsException()
    {
        $this->setExpectedException(
            FailedVerificationException::class
        );

        $executor = $this
            ->getMockBuilder(Executor::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();

        $executor
            ->method('run')
            ->willReturn(<<<EOT
gpg: Unterschrift vom Mi 27 Jul 15:59:10 2016 CEST mittels RSA-Schlüssel ID 41515FE8
[GNUPG:] ERRSIG 293D771241515FE8 1 10 00 1469627950 9
[GNUPG:] NO_PUBKEY 293D771241515FE8
gpg: Unterschrift kann nicht geprüft werden: Öffentlicher Schlüssel nicht gefunden
EOT
            );

        $signature = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['isFile'])
            ->getMock();

        $signature
            ->method('isFile')
            ->willReturn(true);

        $file = clone $signature;

        $verificator = $this
            ->getMockBuilder(Verificator::class)
            ->setConstructorArgs([$executor])
            ->setMethodsExcept(['verify'])
            ->getMock();

        /* @var $verificator Verificator */
        $verificator->verify($signature, $file);
    }

    public function testCanVerifyAValidSignature()
    {
        $executor = $this
            ->getMockBuilder(Executor::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock();

        $executor
            ->method('run')
            ->willReturn(<<<EOT
gpg: Unterschrift vom Mi 27 Jul 15:59:10 2016 CEST mittels RSA-Schlüssel ID 41515FE8
[GNUPG:] SIG_ID m89rvRG+oGqzFADfSM+tHId4PJ4 2016-07-27 1469627950
[GNUPG:] GOODSIG 293D771241515FE8 Kevin G. Herrera <kevin@herrera.io>
gpg: Korrekte Unterschrift von "Kevin G. Herrera <kevin@herrera.io>"
[GNUPG:] VALIDSIG 32E4B74757B1D65234FC389F293D771241515FE8 2016-07-27 1469627950 0 4 0 1 10 00 32E4B74757B1D65234FC389F293D771241515FE8
[GNUPG:] TRUST_UNDEFINED
gpg: WARNUNG: Dieser Schlüssel trägt keine vertrauenswürdige Signatur!
gpg:          Es gibt keinen Hinweis, daß die Signatur wirklich dem vorgeblichen Besitzer gehört.
Haupt-Fingerabdruck  = 32E4 B747 57B1 D652 34FC  389F 293D 7712 4151 5FE8
EOT
            );

        $signature = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['isFile'])
            ->getMock();

        $signature
            ->method('isFile')
            ->willReturn(true);

        $file = clone $signature;

        $verificator = $this
            ->getMockBuilder(Verificator::class)
            ->setConstructorArgs([$executor])
            ->setMethodsExcept(['verify'])
            ->getMock();

        /* @var $verificator Verificator */
        $verificator->verify($signature, $file);
    }
}
