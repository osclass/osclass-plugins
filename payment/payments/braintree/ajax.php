<?php

    $status = BraintreePayment::processPayment();
    if ($status==PAYMENT_COMPLETED) {
        echo "<h1>Success! Transaction ID: ".Params::getParam('braintree_transaction_id')."</h1>";
    } else if ($status==PAYMENT_ALREADY_PAID) {
        echo "<h1>Warning! This payment was already paid</h1>";
    } else {
        echo "<h1>Error: " . $result->message . "</h1>";
    }

?>