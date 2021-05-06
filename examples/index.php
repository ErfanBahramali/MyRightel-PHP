<?php


/**
 * https://github.com/ErfanBahramali/MyRightel-PHP
 */

use MyRightelPHP\MyRightel;

set_time_limit(0);
require_once __DIR__ . '/vendor/autoload.php';


$MyRightel = new MyRightel(9220000000, 'Password');

$Inventory = $MyRightel->getInventory();
print_r($Inventory);

$gift = $MyRightel->getFridayGift();
print_r($gift);