<?php

//require 'vendor/autoload.php';

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

function createCustomerProfile($email) {

    $cc_detail = new OrderReview();
    $cc_number = $_REQUEST['cc_number'];
    $cc_expiration = $_REQUEST['cc_expire'];
    $cc_charge_amount = $_REQUEST['amount'];
    $customer_name = $_REQUEST['c_name'];
    $customer_last_name = $_REQUEST['c_last_name'];
    $cc_cvv = $_REQUEST['cvv'];






    // Common setup for API credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(\Constants::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(\Constants::MERCHANT_TRANSACTION_KEY);
    $refId = 'ref' . time();
    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($cc_number);
    $creditCard->setExpirationDate($cc_expiration);
    $paymentCreditCard = new AnetAPI\PaymentType();
    $paymentCreditCard->setCreditCard($creditCard);
    // Create the Bill To info
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName($customer_name);
    $billto->setLastName($customer_last_name);
    //$billto->setCompany("Souveniropolis");
    $billto->setAddress("14 Main Street");
    $billto->setCity("Pecan Springs");
    $billto->setState("TX");
    $billto->setZip("44628");
    $billto->setCountry("USA");

    // Create a Customer Profile Request
    //  1. create a Payment Profile
    //  2. create a Customer Profile   
    //  3. Submit a CreateCustomerProfile Request
    //  4. Validate Profiiel ID returned
    $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
    $paymentprofile->setCustomerType('individual');
    $paymentprofile->setBillTo($billto);
    $paymentprofile->setPayment($paymentCreditCard);
    $paymentprofiles[] = $paymentprofile;
    $customerprofile = new AnetAPI\CustomerProfileType();
    $customerprofile->setDescription("Customer 2 Test PHP");
    $customerprofile->setMerchantCustomerId("M_" . $email);
    $customerprofile->setEmail($email);
    $customerprofile->setPaymentProfiles($paymentprofiles);
    $request = new AnetAPI\CreateCustomerProfileRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setProfile($customerprofile);
    $controller = new AnetController\CreateCustomerProfileController($request);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
        echo "Succesfully create customer profile : " . $response->getCustomerProfileId() . "\n";
        $paymentProfiles = $response->getCustomerPaymentProfileIdList();
        echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
    } else {
        echo "ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText() . "\n";
    }
    return $response;
}

if (!defined('DONT_RUN_SAMPLES'))
    createCustomerProfile("rostom.sahakian@gmail.com");
