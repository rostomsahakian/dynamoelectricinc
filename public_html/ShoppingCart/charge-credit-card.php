<?php

//require '../vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

$cc_detail = new OrderReview();
$cc_number = $_REQUEST['cc_number'];
$cc_expiration = $_REQUEST['cc_expire'];
$cc_charge_amount = $_REQUEST['amount'];
$customer_name = $_REQUEST['c_name'];
$customer_last_name = $_REQUEST['c_last_name'];
$cc_cvv = $_REQUEST['cvv'];
// Common setup for API credentials  
$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
$merchantAuthentication->setName("64ZLY3cpK");
$merchantAuthentication->setTransactionKey("45jBsu6K73eT9wqH");
$refId = 'ref' . time();

// Create the payment data for a credit card
$creditCard = new AnetAPI\CreditCardType();
$creditCard->setCardNumber($cc_number);
$creditCard->setExpirationDate($cc_expiration);
$creditCard->setCardCode($cc_cvv);
$paymentOne = new AnetAPI\PaymentType();
$paymentOne->setCreditCard($creditCard);

// Create a transaction
$transactionRequestType = new AnetAPI\TransactionRequestType();
$transactionRequestType->setTransactionType("authCaptureTransaction");
$transactionRequestType->setAmount($cc_charge_amount);
$transactionRequestType->setPayment($paymentOne);
$request = new AnetAPI\CreateTransactionRequest();
$request->setMerchantAuthentication($merchantAuthentication);
$request->setRefId($refId);
$request->setTransactionRequest($transactionRequestType);
$controller = new AnetController\CreateTransactionController($request);
$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

if ($response != null) {
    $tresponse = $response->getTransactionResponse();
    if (($tresponse != null) && ($tresponse->getResponseCode() == "1")) {
        echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
        echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
        echo "<br/>";
    } else {
        echo "Charge Credit Card ERROR :  Invalid response\n";
        echo "<br/>error";
        var_dump($tresponse->getAuthCode());
    }
} else {
    echo "Charge Credit Card Null response returned";
    echo "<br/>error";
    var_dump($tresponse->getAuthCode());
}

