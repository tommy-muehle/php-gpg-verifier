# php-gpg-verifier

[![Latest Stable Version](https://poser.pugx.org/tm/gpg-verifier/v/stable)](https://packagist.org/packages/tm/gpg-verifier)
[![Total Downloads](https://poser.pugx.org/tm/gpg-verifier/downloads)](https://packagist.org/packages/tm/gpg-verifier)
[![Build Status](https://travis-ci.org/tommy-muehle/php-gpg-verifier.svg?branch=master)](https://travis-ci.org/tommy-muehle/php-gpg-verifier)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg?style=flat-square)](https://php.net/)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/tommy-muehle/php-gpg-verifier/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/tommy-muehle/php-gpg-verifier.svg)](https://github.com/php-gpg-verifier/issues)

Simple library to verify a file with gpg signature. 
Look at the [documentation](https://www.gnupg.org/gph/en/manual/x135.html) for more information.

## Requirements

Except PHP and composer you need an accessible [GPG](https://www.gnupg.org) binary.

## Install

```
composer require tm/gpg-verifier ^1.0
```

## Basic usage

```
use TM\GPG\Verification\Verifier;

$verifier = new Verifier;
$verifier->verify('my-file.sig', 'my-file');
```

### Advanced usage

```
use TM\GPG\Verification\Verifier;
use TM\GPG\Verification\Exception\FailedVerificationException;
use TM\GPG\Verification\Exception\ExecutableException;
use TM\GPG\Verification\Exception\NotExistException;

$verifier = new Verifier('/path/to/gpg');

try {
    $verifier->verify('my-file.sig', 'my-file');
} catch(FailedVerificationException $exception) {
    // verification failed
} catch(NotExistException $exception) {
    // missing signature- or source-file
} catch(ExecutableException $exception) {
    // something with the executable is wrong
}

```

## Contributing

Please refer to [CONTRIBUTING.md](CONTRIBUTING.md) for information on how to contribute.
