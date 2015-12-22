Blocking Component
==================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/bc35527c-11d8-45a1-a482-18d376cfe382/mini.png)](https://insight.sensiolabs.com/projects/bc35527c-11d8-45a1-a482-18d376cfe382)
[![Build Status](https://travis-ci.org/brainbits/blocking.svg?branch=master)](https://travis-ci.org/brainbits/blocking)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brainbits/blocking/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brainbits/blocking/?branch=master)
[![Scrutinizer Code Coverage](https://scrutinizer-ci.com/g/brainbits/blocking/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/brainbits/blocking/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/brainbits/blocking/v/stable.svg)](https://packagist.org/packages/brainbits/blocking)
[![Total Downloads](https://poser.pugx.org/brainbits/blocking/downloads.svg)](https://packagist.org/packages/brainbits/blocking)
[![Dependency Status](https://www.versioneye.com/php/brainbits:blocking/master/badge.svg)](https://www.versioneye.com/php/brainbits:blocking/master)

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
