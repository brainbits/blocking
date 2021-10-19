Blocking Component
==================

[![Latest Version](https://img.shields.io/github/release/brainbits/blocking.svg?style=flat-square)](https://github.com/brainbits/blocking/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/brainbits/blocking.svg?style=flat-square)](https://packagist.org/packages/brainbits/blocking)
[![Tests](https://github.com/brainbits/blocking/actions/workflows/test.yml/badge.svg)](https://github.com/brainbits/blocking/actions)

The Blocking Component provides methods to manage content based blocking.

```php
<?php

use Brainbits\Blocking\Blocker;
use Brainbits\Blocking\Identity\Identity;
use Brainbits\Blocking\Owner\SymfonySessionOwnerFactory;
use Brainbits\Blocking\Storage\FilesystemStorage;
use Brainbits\Blocking\Validator\ExpiredValidator;

$storage = new FilesystemStorage('/where/to/store/blocks' /* path to directory on filesystem */);
$ownerFactory = new SymfonySessionOwnerFactory($session /* symfony session */);
$validator = new ExpiredValidator(300 /* block will expire after 300 seconds */);

$blocker = new Blocker($storage, $ownerFactory, $validator);

$identity = new Identity('my_content_123');

$block = $blocker->block($identity);
$result = $blocker->unblock($identity);
$result = $blocker->isBlocked($identity);
$block = $blocker->getBlock($identity);
```

Blocking Storage
----------------
The blocking storage is used to store the block information.

A file based blocking storage is provided.
It writes block-files to the filesystem, based on the blocking identifier.

Blocking Identity
-----------------
The blocking identity is used to identify the content that is being blocked.

A general purpose blocking identify is provided, that uses a string as an identifier.

Blocking Owner
--------------
The blocking owner is used to identify the user that created the block.

A symfony session based owner class is provided.

Blocking Validator
------------------
The blocking validator is used to test wether or not an existing block is still valid.

A validator that checks a block via last modification time is provided.

