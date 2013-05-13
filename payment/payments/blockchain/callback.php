<?php

define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
require_once ABS_PATH . 'oc-load.php';

$res = Blockchain::processPayment();
if($res==PAYMENT_COMPLETED) {
    echo '*ok*';
} else {
    echo '*failed*';
}

?>