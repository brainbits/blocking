<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * (c) 2012-2013 brainbits GmbH (http://www.brainbits.net)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

error_reporting(-1);
date_default_timezone_set('Europe/Berlin');

if (file_exists('../vendor/autoload.php'))
{
    require_once '../vendor/autoload.php';
}
else
{
    require_once '../../../autoload.php';
}

