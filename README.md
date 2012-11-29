Blocking Component
==================
The Locking Component provides methods to manager content based blocking.

```php
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
```

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