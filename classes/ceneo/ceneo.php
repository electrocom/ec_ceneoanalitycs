<?php
error_reporting(-1);
 ini_set('memory_limit','-1');
ini_set('display_errors', 'On');
require_once ("CeneoPrices.php");

$ceneo=new CeneoPrices(51108772);
print_r($ceneo->getBestPrice());