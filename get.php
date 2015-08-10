<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);
include_once 'classes/DeliveryMethods.php';

$dm = new DeliveryMethods();

exit($dm->getDeliveryMethods());
