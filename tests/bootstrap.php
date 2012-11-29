<?php
 
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

