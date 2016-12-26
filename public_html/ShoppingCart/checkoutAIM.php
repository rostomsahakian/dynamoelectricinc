<?php

define("AUTHORIZENET_API_LOGIN_ID", MERCHANT_LOGIN_ID);
define("AUTHORIZENET_TRANSACTION_KEY", MERCHANT_TRANSACTION_KEY);
define('AUTHORIZENET_SANDBOX', DEFAULT_MODE); //Change this to true for sandbox mode

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

$cc_number = $_REQUEST['cc_number'];
$cc_expiration = $_REQUEST['cc_expire'];
$cc_charge_amount = $_REQUEST['amount'];
$customer_name = $_REQUEST['c_name'];
$customer_last_name = $_REQUEST['c_last_name'];
$cc_cvv = $_REQUEST['cvv'];
$address = $_REQUEST['address'];
$city = $_REQUEST['city'];
$zip = $_REQUEST['zip_code'];
$country = $_REQUEST['country'];
$customer_ip = $_REQUEST['customer_ip'];
$customer_id = $_REQUEST['customer_id'];
$customer_email = $_REQUEST['cust_email'];
$state = $_REQUEST['state'];
$taxable = $_REQUEST['taxable'];
$shipping_instruct = $_REQUEST['ship_instruct'];
$billto = new AuthorizeNetAIM();



$product_d_size = $_REQUEST['product_detail_array_size'];
for ($i = 0; $i < $product_d_size; $i++) {
    $product_name = $_REQUEST['product_name_' . $i];
    $product_id = $_REQUEST['product_id_' . $i];
    $product_qty = $_REQUEST['product_qty_' . $i];
    $product_price = $_REQUEST['product_price_' . $i];

    $billto->addLineItem(
            $product_id, // Item Id
            $product_name, // Item Name
            PRODUCT_TYPE, // Description
            $product_qty, // Item Quantity
            $product_price, // Item Unit Price
            $taxable // Taxable
    );
}
if ($shipping_instruct != "") {
    $billto->setCustomField("Shipping Instructions", $shipping_instruct);
}
// Create the Bill To info

$billing_info = (object) array();
$billing_info->first_name = $customer_name;
$billing_info->last_name = $customer_last_name;
$billing_info->address = $address;
$billing_info->city = $city;
$billing_info->state = $state;
$billing_info->zip = $zip;
$billing_info->country = $country;
$billing_info->email = $customer_email;
$billing_info->cust_id = $customer_id;
$billing_info->customer_ip = $customer_ip;
$billing_info->description = "Sales";

/*
 * ship to info
 */
$shipping_info = (object) array();
$shipping_info->ship_to_first_name = $customer_name;
$shipping_info->ship_to_last_name = $customer_last_name;
$shipping_info->ship_to_address = $address;
$shipping_info->ship_to_city = $city;
$shipping_info->ship_to_state = $state;
$shipping_info->ship_to_zip = $zip;
$shipping_info->ship_to_country = $country;

/*
 * Card info
 */
$billto->amount = $cc_charge_amount;
$billto->card_num = $cc_number;
$billto->exp_date = $cc_expiration;
$billto->card_code = $cc_cvv;


$billto->setFields($billing_info);
$billto->setFields($shipping_info);



/*
 * IF authorized only the use $billto->authorizeOnly() function
 * Else use the $billto->authorizeAndCapture() function
 * *******************************************************************************
 */

if (defined('DEFAULT_TRANS_MODE') && DEFAULT_TRANS_MODE == "authorizeAndCapture") {
    $response = $billto->authorizeAndCapture();
} else {
    $response = $billto->authorizeOnly();
}
//********************************************************************************

/*
 * Response
 */
$transaction_id = $response->transaction_id;
$auth_code = $response->authorization_code;


$printReciept = new OrderReview();

if ($response->response_code == "1" || $response->response_code == 1) {
    $printReciept->PrintOrderReciept($_REQUEST, $response->transaction_id, $response->authorization_code);
    $printReciept->UpdateCartCheckOut($_REQUEST, $response->transaction_id, $response->authorization_code);
} else {
    unset($_REQUEST['cc_number']);
    $printReciept->CardProccessError($response->response_code);
}


