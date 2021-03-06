<?php
/**
 * Created by PhpStorm.
 * User: brunopaz
 * Date: 2018-12-26
 * Time: 22:50
 */

namespace Gateway\API;

include_once "autoload.php";

use Exception as Exception;


try {
    $credential = new Credential("{{mechantID}}", "{{mechantKEY}}",
        Environment::SANDBOX);
    $gateway    = new Gateway($credential);

    ### CREATE A NEW TRANSACTION
    $transaction = new Transaction();


    $customer = new Customer();
    $customer->setCustomerIdentity("11111");
    $customer->setCpf("94127918012");
    //$customer->setCnpj("18303116000165")
    $customer->setEmail("teste@teste.com");
    $customer->setName("teste");

    $card = new Card();
    $card->setBrand(Brand::VISA);
    $card->setCardHolder("Bruno paz");
    $card->setCardNumber("2223000148400010");
    $card->setCardSecurityCode("123");
    $card->setCardExpirationDate("202001");

    $token = new Tokenization($credential, $card, $customer);



    // Set ORDER
    $transaction->Order()
        ->setReference("ss")
        ->setTotalAmount(150000);


    $split = [
        [
            "recipient"             => "d71c944b96a43b39c2b38fd6353b6a2",
            "liable"                => "true",
            "charge_processing_fee" => "true",
            "percentage"            => 10,
            "amount"                => 10,
        ],
        [
            "recipient"             => "4c5be5014c104e6580d8d512e10f749b",
            "liable"                => "false",
            "charge_processing_fee" => "false",
            "percentage"            => 20,
            "amount"                => 20,
        ],
    ];


    // Set PAYMENT

    $transaction->Payment()
        ->setAcquirer(Acquirers::KESTERPAY)
        ->setMethod(Methods::CREDIT_CARD_INTEREST_BY_MERCHANT)
        ->setCurrency(Currency::BRAZIL_BRAZILIAN_REAL_BRL)
        ->setCountry("BRA")
        ->setNumberOfPayments(1)
        ->setSoftDescriptor("Bruno paz")
        ->Split($split)
        ->setTokenCard($token->getTokenCard());


    /* // SET CUSTOMER
     $transaction->setCustomer()
         ->setCustomerIdentity("999999999")
         ->setName("Bruno")
         ->setCpf("94127918012")
         ->setCnpj("18303116000165")
         ->setEmail("brunopaz@test.com");*/

    $transaction->setCustomer($customer);

    // SET FRAUD DATA OBJECT
    $transaction->FraudData()
        ->setName("Bruno Paz")
        ->setDocument("94127918012")
        ->setEmail("brunopaz@g.com")
        ->setAddress("Rua test")
        ->setAddress2("Apartamento 23")
        ->setAddressNumber("300")
        ->setPostalCode("08742350")
        ->setCity("S??o Paulo")
        ->setState("SP")
        ->setCountry("BRASIL")
        ->setPhonePrefix("11")
        ->setPhoneNumber("99999-9999")
        ->setDevice("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36")
        ->setCostumerIP("192.168.0.1")
        ->setItems([
            ["productName" => "Iphone X", "quantity" => 1, "price" => "20.00"],
            [
                "productName" => "Iphone XL",
                "quantity"    => 12,
                "price"       => "1220.00",
            ],
        ]);

    //print_r($transaction->toJSON());
    //exit;
    // Set URL RETURN
    $transaction->setUrlReturn("http://127.0.0.1:8989/return.php");

    // PROCESS - ACTION
    //$response = $gateway->sale($transaction);
    $response = $gateway->authorize($transaction);
    // REDIRECT IF NECESSARY (Debit uses)


    if ($response->isRedirect()) {
        $response->redirect();
    }

    // RESULTED
    if ($response->isAuthorized()) { // Action Authorized
        print "<br>RESULTED: ".$response->getStatus();
    } else { // Action Unauthorized
        print "<br>RESULTED:".$response->getStatus();
    }

    // CAPTURE
    if ($response->canCapture()) {
        $response = $gateway->Capture($response->getTransactionID(),
            100000);
        print "<br>CAPTURED: ".$response->getStatus();
    }


    // CANCELL
    if ($response->canCancel()) {
        $response = $gateway->Cancel($response->getTransactionID(), 50000);
        print "<br>CANCELED: ".$response->getStatus();
    }

    // REPORT
    $response = $gateway->Report($response->getTransactionID());
    print "<br>REPORTING: ".$response->getStatus();

} catch (Exception $e) {
    print_r($e->getMessage());
}

