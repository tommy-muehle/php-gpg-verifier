<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TM\GPG\Verification\Verifier;
use TM\GPG\Verification\Exception\VerificationException;

/**
 * @param string $filename
 * @param string $content
 *
 * @return string
 */
function create_temporary_file($filename, $content)
{
    $file = sys_get_temp_dir() . $filename;
    file_put_contents($file, $content);

    return $file;
}

$file = create_temporary_file(
    'box.phar',
    file_get_contents('https://github.com/box-project/box2/releases/download/2.7.4/box-2.7.4.phar')
);

$signature = create_temporary_file(
    'box.phar.sig',
    file_get_contents('https://github.com/box-project/box2/releases/download/2.7.4/box-2.7.4.phar.sig')
);

try {
    $verifier = new Verifier('/usr/local/bin/gpg');
    $verifier->verify($signature, $file);
    echo 'Everything ok.';
} catch (VerificationException $exception) {
    echo $exception->getMessage();
}
