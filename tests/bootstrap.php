<?php
/**
 * This file is part of the brainbits blocking package.
 *
 * @copyright 2012-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.brainbits.net/LICENCE     Dummy Licence
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

