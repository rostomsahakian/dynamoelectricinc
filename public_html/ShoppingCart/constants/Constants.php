<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Constants
 *
 * @author rostom
 */
class Constants {
    
    public $vals;

    //merchant credentials
//    const MERCHANT_LOGIN_ID = ;
//    const MERCHANT_TRANSACTION_KEY = "45jBsu6K73eT9wqH";
//    const RESPONSE_OK = "1";
     
     
    //Recurring Billing
//	const SUBSCRIPTION_ID_GET = "2930242";
//	//Transaction Reporting
//	const TRANS_ID = "2238968786";

    public function GetJsonData() {
        $str = file_get_contents(ABSOLUTH_ROOT . 'public_html/ShoppingCart/constants/defaults.json');
        $json = json_decode($str, true); // decode the JSON into an associative array
       
    }
    public function ReturnPvals(){
        return $this->vals;
    }

}
