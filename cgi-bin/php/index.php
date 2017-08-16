<?php

require_once('./Classes/class.AutoIndex.php');
require_once('./Configuration/conf.AutoIndex.php');

// always wanted to use ai in a project ;-)
$ai = new AutoIndex($conf);
#var_dump($ai);
$ai->getRenderedPage();