<?php

require_once 'src/Deep/Gearman/RPC.php';
require_once 'src/Deep/Gearman/Client.php';
require_once 'Test.php';

/**
 * @var Test $t
 */
$t = new \Deep\Gearman\Client(new Test());
echo $t->ping('hello');
