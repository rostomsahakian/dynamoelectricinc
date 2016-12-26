<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ShoppingCart
 *
 * @author rostom
 */
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");

class ShoppingCart {

    private $_mysqli;
    private $_db;
    public $flag = 0;
    public $message;
    public $alert_class;
    public $provider;
    public $mode;
    public $api_id;
    public $transaction_key;
    public $card_types = array();
    public $wc;
    public $world_c;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function ShoppingCartManager() {
        if (isset($_REQUEST['v_account'])) {

            if (!array_key_exists("mode", $_REQUEST)) {
                $_REQUEST['mode'] = NULL;
            }
            $this->WriteToJsonInfo($_REQUEST);
        }
        if (isset($_REQUEST['defaults'])) {
            $this->SaveDefualtSettings($_REQUEST);
        }
        ?>
        <div class="panel-heading">
            <h5><strong><i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;Shopping Cart Manager </strong></h5>
        </div>
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5><b><i class="fa fa-gears"></i>&nbsp;Payment specific</b></h5>
                        </div>
                        <div class="panel-body">
                            <form method="post">
                                <div class="form-group">
                                    <label>Payment System:</label>
                                    <?php
                                    $selected = '';
                                    $providers = array(
                                        "1" => "Authorized.net",
                                        "2" => "PayPal"
                                    );
                                    ?>
                                    <select name="provider" class="form-control">
                                        <option value="--" >Select</option>
                                        <?php
                                        foreach ($providers as $provider) {
                                            if (isset($_REQUEST['provider'])) {
                                                if ($_REQUEST['provider'] == $provider) {
                                                    $selected = 'selected="selected"';
                                                } else {
                                                    $selected = '';
                                                }
                                            }
                                            ?>
                                            <option value="<?= $provider ?>" <?= $selected ?>><?= $provider ?></option>

                                            <?php
                                        }
                                        ?>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Mode:</label>
                                    <table class="table">
                                        <?php
                                        $checked = "";
                                        $checked_s = "";
                                        $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : NULL;
                                        if (isset($_REQUEST['mode']) && $mode == "live") {

                                            $checked = 'checked="checked"';
                                        } else {

                                            $checked = "";
                                        }
                                        if (isset($_REQUEST['mode']) && $mode == "sandbox") {
                                            $checked_s = 'checked="checked"';
                                        } else {
                                            $checked_s = "";
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="radio" name="mode" value="live"  <?= $checked ?>/>
                                            </td>
                                            <td >
                                                <span >Live</span>
                                            </td>
                                            <td>
                                                <input type="radio" name="mode" value="sandbox"  <?= $checked_s ?>/>
                                            </td>
                                            <td >
                                                <span >test/Sandbox</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <label>Transaction Mode</label>
                                    <select name="trans_mode" class="form-control">
                                        <?php
                                        $select_authandCap = '';
                                        $select_authOly = '';
                                        if (isset($_REQUEST['trans_mode'])) {
                                            if ($_REQUEST['trans_mode'] == "authorizeOnly") {

                                                $select_authOly = 'selected="selected"';
                                            } else if ($_REQUEST['trans_mode'] == "authorizeAndCapture") {
                                                $select_authandCap = 'selected="selected"';
                                            }
                                        }
                                        ?>
                                        <option value="--">Select</option>
                                        <option value="authorizeOnly" <?= $select_authOly ?>>Authorize Only</option>
                                        <option value="authorizeAndCapture" <?= $select_authandCap ?>>Authorize And Capture</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>API Login ID:</label>
                                    <input type="text" name="api_id" value="<?= isset($_REQUEST['api_id']) ? $_REQUEST['api_id'] : '' ?>" class="form-control input-sm"/>
                                </div>
                                <div class="form-group">
                                    <label>Merchant Transaction Key:</label>
                                    <input type="text" name="march_key" value="<?= isset($_REQUEST['march_key']) ? $_REQUEST['march_key'] : '' ?>" class="form-control input-sm"/>
                                </div>
                                <div class="form-group">
                                    <label>Allowed Card types:</label>
                                    <table class="table">
                                        <tr>
                                            <td>
                                                <?php
                                                $visa_select = '';
                                                $amex_select = '';
                                                $mc_select = '';
                                                $mc_select = '';
                                                if (isset($_REQUEST['visa']) && $_REQUEST['visa'] == "visa") {
                                                    $visa_select = 'checked="checked"';
                                                } else {
                                                    $visa_select = '';
                                                }
                                                ?>
                                                <input type="checkbox" name="visa" value="visa"  <?= $visa_select ?>/>
                                            </td>
                                            <td >visa</td>
                                            <td>
                                                <?php
                                                if (isset($_REQUEST['mastercard']) && $_REQUEST['mastercard'] == "mastercard") {
                                                    $mc_select = 'checked="checked"';
                                                } else {
                                                    $mc_select = '';
                                                }
                                                ?>
                                                <input type="checkbox" name="mastercard" value="mastercard"  <?= $mc_select ?>/>
                                            </td>
                                            <td >mastercard</td>
                                            <td>
                                                <?php
                                                if (isset($_REQUEST['discover']) && $_REQUEST['discover'] == "discover") {
                                                    $discover_select = 'checked="checked"';
                                                } else {
                                                    $mc_select = '';
                                                }
                                                ?>
                                                <input type="checkbox" name="discover" value="discover"  <?= $discover_select ?>/>
                                            </td>
                                            <td >discover</td>
                                            <td>
                                                <?php
                                                if (isset($_REQUEST['amex'])) {
                                                    if ($_REQUEST['amex'] == "amex") {
                                                        $amex_select = 'checked="checked"';
                                                    } else {
                                                        $amex_select = '';
                                                    }
                                                }
                                                ?>
                                                <input type="checkbox" name="amex" value="amex"  <?= $amex_select ?>/>
                                            </td>
                                            <td >amex</td>
                                        </tr>
                                    </table>
                                    <div class="form-group">
                                        <input type="submit" value="validate account" name="v_account" class="btn btn-success btn-xs"/>
                                    </div>
                                    <?php
                                    if ($this->flag == 1) {

                                        echo '<span  ' . $this->alert_class . '>' . $this->message . '</span>';
                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5><b><i class="fa fa-money"></i>&nbsp;General Settings</b></h5>
                        </div>
                        <form method="post">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>Default Country:</label>
                                    <select name="d_country" class="form-control">
                                        <option value="--">Select</option>
                                        <?php
                                        $selected_country = '';
                                        $this->GetWorldCountries();
                                        foreach ($this->world_c as $countries) {
                                            if (isset($_REQUEST['d_country'])) {
                                                if ($_REQUEST['d_country'] == $countries['name']) {
                                                    $selected_country = 'selected="selected"';
                                                } else {
                                                    $selected_country = '';
                                                }
                                            }
                                            ?>
                                            <option value="<?= $countries['name'] ?>" <?= $selected_country ?>><?= $countries['name'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Default Currency</label>
                                    <select name="d_currency" class="form-control">
                                        <option value="--">Select</option>
                                        <?php
                                        $selected_curr = '';
                                        $this->GetWorldCurrencies();
                                        foreach ($this->wc as $world_curr) {
                                            if (isset($_REQUEST['d_currency'])) {
                                                if ($_REQUEST['d_currency'] == $world_curr['code']) {
                                                    $selected_curr = 'selected="selected"';
                                                } else {
                                                    $selected_curr = '';
                                                }
                                            }
                                            ?>
                                            <option value="<?= $world_curr['code'] ?>" <?= $selected_curr ?>><?= $world_curr['name'] . "(" . $world_curr['code'] . ")" ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Ships From:</label>
                                    <input type="text" name="ships_from" value="<?= isset($_REQUEST['ships_from']) ? $_REQUEST['ships_from'] : '' ?>" class="form-control input-sm"/>
                                </div>
                                <div class="form-group">
                                    <label>Tax Rate:</label>
                                    <input type="text" name="tax_rate" value="<?= isset($_REQUEST['tax_rate']) ? $_REQUEST['tax_rate'] : '' ?>" class="form-control input-sm" style="width: 100px;"/>
                                </div>
                                <div class="form-group">
                                    <label>Minimum Purchase:</label>
                                    <input type="text" name="min_purchase" value="<?= isset($_REQUEST['min_purchase']) ? $_REQUEST['min_purchase'] : '' ?>" class="form-control input-sm" style="width: 100px;"/>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="defaults" value="Save" class="btn btn-success btn-sm"/>

                                </div>
                                <?php
                                if ($this->flag == 2) {

                                    echo '<span  ' . $this->alert_class . '>' . $this->message . '</span>';
                                }
                                ?>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5><i class="fa fa-database"></i>&nbsp;Current Settings</h5>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <h5>Accepted Cards type Codes:</h5>
                                    <ol>
                                        <li>Visa</li>
                                        <li>Mastercard</li>
                                        <li>Discover</li>
                                        <li>American Express</li>
                                    </ol>
                                </div>
                                <?php
                                $types = array();
                                $cart_op_details = $this->GetJSONData();
                                ?>
                                <table class="table table-condensed">
                                    <tr>
                                        <th>Provider</th>
                                        <th>Mode</th>
                                        <th>API ID</th>
                                        <th>T KEY</th>
                                        <th>Accepted Cards</th>
                                    </tr>
                                    <?php
                                    if ($cart_op_details != NULL) {
                                        ?>
                                        <tr>
                                            <td><?= $cart_op_details['provider'] ?></td>
                                            <td><?= $cart_op_details['mode'] ?></td>
                                            <td><?= $cart_op_details['api_id'] ?></td>
                                            <td><?= $cart_op_details['march_key'] ?></td>

                                            <?php
                                            $master_card = array();
                                            if (array_key_exists("visa", $cart_op_details)) {
                                                $cart_op_details['visa'] = 1;
                                                array_push($types, $cart_op_details['visa']);
                                            }
                                            if (array_key_exists("mastercard", $cart_op_details)) {
                                                $cart_op_details['mastercard'] = 2;
                                                array_push($types, $cart_op_details['mastercard']);
                                            }
                                            if (array_key_exists("discover", $cart_op_details)) {
                                                $cart_op_details['discover'] = 3;
                                                array_push($types, $cart_op_details['discover']);
                                            }
                                            if (array_key_exists("amex", $cart_op_details)) {
                                                $cart_op_details['amex'] = 4;
                                                array_push($types, $cart_op_details['amex']);
                                            }

                                            $accepted_card_type = implode(",", $types);
                                            ?>
                                            <td><?= $accepted_card_type; ?></td>



                                        </tr>
                                    </table>
                                    <?php
                                }
                                ?>
                                <table class="table table-condensed">
                                    <tr>
                                        <th>Default Country</th>
                                        <th>Default Currency</th>
                                        <th>Ships From</th>
                                        <th>Tax</th>
                                    </tr>
                                    <tr>
                                        <?php
                                        $defaults = $this->GetJSONDefaultData();
                                        ?>
                                        <td><?= $defaults['d_country'] ?></td>
                                        <td><?= $defaults['d_currency'] ?></td>
                                        <td><?= $defaults['ships_from'] ?></td>
                                        <td><?= $defaults['tax_rate'] ?></td>
                                    </tr>
                                </table>
                                <table class="table table-condensed">
                                    <tr>
                                        <th>Minimum Purchase</th> 
                                    </tr>
                                    <tr>
                                        <?php
                                        if (array_key_exists("min_purchase", $defaults)) {
                                            if (array_key_exists("min_purchase", $defaults) && $defaults['min_purchase'] == "") {
                                                $defaults['min_purchase'] = "n/a";
                                            }
                                            ?>
                                            <td><?= $defaults['min_purchase'] ?></td>
                                            <?php
                                        }
                                        ?>

                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function WriteToJsonInfo($data) {
        // Common setup for API credentials

        if ($data['provider'] == "--") {

            $this->flag = 1;
            $this->message = "Please select a payment option provider.";
            $this->alert_class = "style='color:#D92736;'";
        } else if ($data['mode'] == NULL) {
            $this->flag = 1;
            $this->message = "Please select a mode that you would like to be on(live or sandbox).";
            $this->alert_class = "style='color:#D92736;'";
        } else if ($data['trans_mode'] == "--") {
            $this->flag = 1;
            $this->message = "Please select your transaction mode (authorize payment only or authorize and capture payment).";
            $this->alert_class = "style='color:#D92736;'";
        } else if (empty($data['api_id']) && empty($data['march_key'])) {
            $this->flag = 1;
            $this->message = "Please provide The API ID and Merchant Transaction Key.";
            $this->alert_class = "style='color:#D92736;'";
        } else if (empty($data['api_id']) || empty($data['march_key'])) {
            $this->flag = 1;
            $this->message = "Either the API ID or Merchant Transaction Key is missing.";
            $this->alert_class = "style='color:#D92736;'";
        } else if (!array_key_exists("visa", $data) && !array_key_exists("mastercard", $data) && !array_key_exists("discover", $data) && !array_key_exists("amex", $data)) {
            $this->flag = 1;
            $this->message = "Please select at least one card type.";
            $this->alert_class = "style='color:#D92736;'";
        } else {



            $url = ($data['mode'] == "live") ? 'https://api.authorize.net/xml/v1/request.api' : 'https://apitest.authorize.net/xml/v1/request.api';
            $api_id = $data['api_id'];
            $trans_key = $data['march_key'];
            $input_xml = '<authenticateTestRequest xmlns = "AnetApi/xml/v1/schema/AnetApiSchema.xsd">
        <merchantAuthentication>
        <name>' . $api_id . '</name>
        <transactionKey>' . $trans_key . '</transactionKey>
        </merchantAuthentication>
        </authenticateTestRequest >';


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input_xml);
            $result = curl_exec($ch);
            curl_close($ch);

            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOWARNING);


            if ($xml->messages[0]->message[0]->code == "I00001") {
                $this->flag = 1;
                $this->message = "Authentication was successfull";
                $this->alert_class = "style='color:#449D44;'";



                $d = $this;
                $d->provider = $data['provider'];
                $d->mode = $data['mode'];
                $d->api_id = $data['api_id'];
                $d->transaction_key = $data['march_key'];



                $fp = fopen(ABSOLUTH_ROOT . 'public_html/ShoppingCart/constants/settings.json', 'w');
                fwrite($fp, json_encode($data));
                fclose($fp);
            } else {

                $this->flag = 1;
                $this->message = "Authentication faild. Please try again.";
                $this->alert_class = "style='color:#D92736;'";
            }
        }
    }

    public function GetJSONData() {

        $str = file_get_contents(ABSOLUTH_ROOT . 'public_html/ShoppingCart/constants/settings.json');
        $json = json_decode($str, true); // decode the JSON into an associative array
        return $json;
    }

    public function GetWorldCurrencies() {
        $sql = "SELECT DISTINCT `name`, `code` FROM `world_currencies` ORDER BY `name` ASC";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->wc[] = $row;
        }
        return $this->wc;
    }

    public function GetWorldCountries() {
        $sql = "SELECT  * FROM `countries` ORDER BY `name` ASC";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->world_c[] = $row;
        }
        return $this->world_c;
    }

    public function SaveDefualtSettings($data) {

        if ($data['d_country'] == "--" && $data['d_currency'] == "--" && empty($data['ships_from'])) {
            $this->flag = 2;
            $this->message = "Please select defualt country, currency, shipped from location and tax rate.";
            $this->alert_class = "style='color:#D92736;'";
        } else if ($data['d_country'] == "--" || $data['d_currency'] == "--" || empty($data['ships_from'])) {
            $this->flag = 2;
            $this->message = "One or more fields are either not selected or not entered.";
            $this->alert_class = "style='color:#D92736;'";
        } else if ($data['d_country'] == "United States" && $data['tax_rate'] == "") {
            $this->flag = 2;
            $this->message = "For United States please enter the tax rate.";
            $this->alert_class = "style='color:#D92736;'";
        } else {
            $fp = fopen(ABSOLUTH_ROOT . 'public_html/ShoppingCart/constants/defaults.json', 'w');
            fwrite($fp, json_encode($data));
            fclose($fp);
            $this->flag = 2;
            $this->message = "Defaults saved.";
            $this->alert_class = "style='color:#449D44;'";
        }
    }

    public function GetJSONDefaultData() {

        $str = file_get_contents(ABSOLUTH_ROOT . 'public_html/ShoppingCart/constants/defaults.json');
        $json = json_decode($str, true); // decode the JSON into an associative array
        return $json;
    }

}
