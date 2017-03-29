<?php

require_once 'src/Deep/Gearman/RPC.php';
require_once 'src/Deep/Gearman/Worker.php';
require_once 'Test.php';

$t = new \Deep\Gearman\Worker(new Test(), ['localhost']);
$t->run();
