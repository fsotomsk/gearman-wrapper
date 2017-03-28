<?php

require_once 'Gearman/Worker.php';
require_once 'Test.php';

$t = new \Deep\Gearman\Worker(new Test(), ['localhost']);
$t->run();
