<?php


require __DIR__ . '/vendor/autoload.php';

use JscorpTech\IYB\IYB;

$iyb = new Iyb();

print_r($iyb->create_transaction(1000));