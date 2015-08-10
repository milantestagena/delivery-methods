<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);
include_once 'classes/DeliveryMethods.php';

$data = json_decode(file_get_contents("php://input"), TRUE);

//validate
$dm = new DeliveryMethods();
if($dm->validate($data)){
    $dm->save($data);
}
exit($dm->getDeliveryMethods());


