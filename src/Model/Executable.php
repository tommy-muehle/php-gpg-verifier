<?php

namespace TM\GPG\Verification\Model;

/**
 * @package TM\GPG\Verification
 */
class Executable extends \SplFileInfo
{
    /**
     * @return bool
     */
    public function isExist()
    {
        if (true === $this->isFile() || true === $this->isLink()) {
            return true;
        }

        return false;
    }
}
