Blocking Component
==================

[![Latest Version](https://img.shields.io/github/release/brainbits/blocking.svg?style=flat-square)](https://github.com/brainbits/blocking/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/brainbits/blocking/master.svg?style=flat-square)](https://travis-ci.org/brainbits/blocking)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/brainbits/blocking.svg?style=flat-square)](https://scrutinizer-ci.com/g/brainbits/blocking/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/brainbits/blocking.svg?style=flat-square)](https://scrutinizer-ci.com/g/brainbits/blocking)
[![Insight](https://img.shields.io/sensiolabs/i/bc35527c-11d8-45a1-a482-18d376cfe382.svg)](https://insight.sensiolabs.com/projects/bc35527c-11d8-45a1-a482-18d376cfe382)
[![Total Downloads](https://img.shields.io/packagist/dt/brainbits/blocking.svg?style=flat-square)](https://packagist.org/packages/brainbits/blocking)

The Blocking Component provides methods to manage content based blocking.

    <?php

    use Brainbits\Blocking\Blocker;
    use Brainbits\Blocking\Adapter\FilesystemAdapter;
    use Brainbits\Blocking\Owner\SessionOwner;

    $adapter   = new FilesystemAdapter('/where/to/store/blocks' /* path to directory on filesystem */);
    $owner     = new SessionOwner($session /* symfony session */);
    $validator = new ExpiredValidator(300 /* block will expire after 300 seconds */);

    $blocker = new Blocker($adapter, $owner, $validator);

    $identifier = new Identifer('myContent', 123);

    $block = $blocker->block($identifier);
    $result = $blocker->unblock($identifier);
    $result = $blocker->isBlocked($identifier);
    $block = $blocker->getBlock($identifier);

Blocking Adapters
-----------------
Blocking adapters are used to store the block information.

A file based blocking adapter is provided.
It writes block-files to the filesystem, based on the blocking identifier.

Blocking Identifiers
--------------------
Blocking identifiers are used to identify the content that is being blocked.

A general purpose blocking identifier is provided, that uses content type and
content id to create an identifier string.

Blocking Owners
---------------
Blocking owners are used to identify the user that created the block.

A symfony session based owner class is provided.

Blocking Validators
-------------------
Blocking validators test wether or not an existing block is still valid.

A validator that checks a block via last modification time is provided.
