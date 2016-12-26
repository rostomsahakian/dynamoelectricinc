<?php

//require 'vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

function authorizeCreditCard($amount) {
    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName('64ZLY3cpK');
    $merchantAuthentication->setTransactionKey("45jBsu6K73eT9wqH");
    $refId = 'ref' . time();

    $cc_detail = new OrderReview();
    $cc_number = $_REQUEST['cc_number'];
    $cc_expiration = $_REQUEST['cc_expire'];
    $cc_charge_amount = $_REQUEST['amount'];
    $customer_name = $_REQUEST['c_name'];
    $customer_last_name = $_REQUEST['c_last_name'];
    $cc_cvv = $_REQUEST['cvv'];


    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($cc_number);
    $creditCard->setExpirationDate($cc_expiration);
    $creditCard->setCardCode($cc_cvv);
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);

    //create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authOnlyTransaction");
    $transactionRequestType->setAmount($amount);
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
            echo " AUTH CODE : " . $tresponse->getAuthCode() . "\n";
            echo " TRANS ID  : " . $tresponse->getTransId() . "\n";
        } else {
            echo "ERROR : " . $tresponse->getResponseCode() . "\n";
        }
    } else {
        echo "No response returned";
    }
    return $response;
    echo "<br/>";
var_dump( $tresponse->getResponseCode());
}

if (!defined('DONT_RUN_SAMPLES'))
    $cc_charge_amount = $_REQUEST['amount'];

authorizeCreditCard($cc_charge_amount);
?>