<?php

namespace TM\GPG\Verification\Utility;

use TM\GPG\Verification\Exception\NoExecutableFoundException;
use TM\GPG\Verification\Model\Executable;

/**
 * @package TM\GPG\Verification
 */
class ExecutableFinder
{
    /**
     * @return Executable
     * @throws NoExecutableFoundException
     */
    public static function find()
    {
        $paths = getenv('PATH');

        if (false === $paths) {
            throw new NoExecutableFoundException;
        }

        foreach (explode(':', $paths) as $path) {
            $filename = sprintf('%s/%s', $path, 'gpg');

            if (false === file_exists($filename)) {
                continue;
            }

            return new Executable($filename);
        }

        throw new NoExecutableFoundException;
    }
}
