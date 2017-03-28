<?php

require_once 'Gearman/Worker.php';
require_once 'Gearman/Client.php';
require_once 'Test.php';

/**
 * @var Test $t
 */
$t = new \Deep\Gearman\Client(new Test());
echo $t->ping('hello');
