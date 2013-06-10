<?php

    Braintree_Configuration::environment('sandbox');
    Braintree_Configuration::merchantId('6sydfqcpfbzmcj7w');
    Braintree_Configuration::publicKey('bytsnfpmh8f2v637');
    Braintree_Configuration::privateKey('88396fd311a6e1016a903bf08660065f');

    $result = Braintree_Transaction::sale(array(
        'amount' => '100.00',
        'creditCard' => array(
            'number' => Params::getParam('braintree_number'),
            'cvv' => Params::getParam('braintree_cvv'),
            'expirationMonth' => Params::getParam('braintree_month'),
            'expirationYear' => Params::getParam('braintree_year')
        ),
        'options' => array(
            'submitForSettlement' => true
        )
    ));

    if ($result->success) {
        echo "<h1>Success! Transaction ID: " . $result->transaction->id . "</h1>";
    } else {
        echo "<h1>Error: " . $result->message . "</h1>";
    }

?>