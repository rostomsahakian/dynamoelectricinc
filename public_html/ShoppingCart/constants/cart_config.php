<?php

require_once ABSOLUTH_ROOT . 'vendor/autoload.php';

$str = file_get_contents(ABSOLUTH_ROOT . 'public_html/ShoppingCart/constants/settings.json');
$settings = json_decode($str, true); // decode the JSON into an associative array
define("MERCHANT_LOGIN_ID", $settings['api_id']);
define("MERCHANT_TRANSACTION_KEY", $settings['march_key']);
if ($settings['mode'] == "sandbox") {
    define("DEFAULT_MODE", true);
} else {
    define("DEFAULT_MODE", false);
}

if ($settings['trans_mode'] == "authorizeOnly") {

    define("DEFAULT_TRANS_MODE", "authorizeOnly");
} else {
    define("DEFAULT_TRANS_MODE", "authorizeAndCapture");
}

define("RESPONSE_OK", 1);


$def = file_get_contents(ABSOLUTH_ROOT . 'public_html/ShoppingCart/constants/defaults.json');
$defaults = json_decode($def, true); // decode the JSON into an associative array
define("DEFAULT_COUNTRY", $defaults['d_country']);
define("DEFAULT_CURRENCY", $defaults['d_currency']);
define("DEFAULT_SHIPPED_FROM", $defaults['ships_from']);
define("DEFAULT_TAX_RATE", $defaults['tax_rate']);
define("MINIMUM_PURCHASE", $defaults['min_purchase']);
