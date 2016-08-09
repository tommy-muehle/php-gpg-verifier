<?php

namespace TM\GPG\Verification\Tests;

use TM\GPG\Verification\Model\Executable;
use TM\GPG\Verification\Verifier;

/**
 * @package TM\GPG\Verification\Tests
 */
class VerifierTest extends \PHPUnit_Framework_TestCase
{
    public function testCanSetExplicitBinary()
    {
        $executable = $this->getExecutable(new Verifier('/usr/custom/bin/gpg'));
        $this->assertEquals('/usr/custom/bin/gpg', $executable->getPathname());
    }

    /**
     * @param Verifier $verifier
     *
     * @return Executable
     */
    private function getExecutable(Verifier $verifier)
    {
        $executor = call_user_func(function() use ($verifier) {
            $reflection = new \ReflectionClass($verifier);

            $property = $reflection->getProperty('executor');
            $property->setAccessible(true);

            return $property->getValue($verifier);
        });

        $executable = call_user_func(function () use ($executor) {
            $reflection = new \ReflectionClass($executor);

            $property = $reflection->getProperty('executable');
            $property->setAccessible(true);

            return $property->getValue($executor);
        });

        return $executable;
    }
}
