<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GuestCheckOut
 *
 * @author rostom
 */
class GuestCheckOut {

    private $_mysqli;
    private $_db;
    public $cart;
    public $total = 0;
    public $tax = 0;
    public $grand_total = 0;
    public $states;
    public $country;
    public $flag = 1;
    public $messages = array();
    public $alert_class = "";
    public $order_review;
    public $product_info;
    public $user_desc;
    public $user_email;
    public $user_address;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->order_review = new OrderReview();
        $this->OrderCalculations();
    }

    public function CheckOutAsGuest($page_data, $req) {
        
        if (isset($_REQUEST['review_order'])) {
            $this->CheckFormValidation($_REQUEST);
            $this->returnFlag();
        }
        if (isset($_SESSION['user_log'])) {

            $this->GetAllFrontEnduserData($_SESSION['user_id']);
            $this->GetUserEmail($_SESSION['user_id']);
            $this->GetUserAddress($_SESSION['user_id']);

            foreach ($this->user_address as $address) {
                
            }

            foreach ($this->user_desc as $user_data) {
                
            }

            foreach ($this->user_email as $user_email) {
                
            }
        }
        ?>

        <div class="container rock-main-container" >

            <h4 style="text-transform: uppercase;"><?= $page_data['page_name']; ?></h4>
            <div class="row">
                <div class="col-md-8 <?= $this->alert_class ?>" >
                    <?php
                    if ($this->flag == 2) {
                        ?>

                        <div class="alert alert-warning  alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                            <ul>
                                <?php
                                foreach ($this->messages as $m) {
                                    ?>
                                    <li><?= $m['1'] ?></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>

                        <?php
                    }
                    ?>
                </div>
                <?php
                if ($this->returnFlag() == 0) {
                    $this->order_review->ReviewOrderProccess($page_data, $_REQUEST);
                }
                ?>

                <div class="col-md-8" id="guest-checkout-form">

                    <div class="panel panel-default rock-check-out-main-div">
                        <div class="panel-heading rock-check-out-step-1-p-heading">
                            <h4><strong><i class="fa fa-info"></i>&nbsp;Guest Check out</strong></h4>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <p>Personal Information</p>
                                <form method="post">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>First Name<span style="color: #C62326;">*</span>:</label>
                                            <input type="text" name="guest_name" class="form-control input-sm" value="<?= isset($_REQUEST['guest_name']) ? $_REQUEST['guest_name'] : isset($_SESSION['user_log']) ? $user_data['f_name'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Last Name<span style="color: #C62326;">*</span>:</label>
                                            <input type="text" name="guest_last_name" class="form-control input-sm" value="<?= isset($_REQUEST['guest_last_name']) ? $_REQUEST['guest_last_name'] : isset($_SESSION['user_log']) ? $user_data['l_name'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Email<span style="color: #C62326;">*</span>:</label>
                                            <input type="email" name="guest_email" class="form-control input-sm" value="<?= isset($_REQUEST['guest_email']) ? $_REQUEST['guest_email'] : isset($_SESSION['user_log']) ? $user_email['email'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Address 1<span style="color: #C62326;">*</span>:</label>
                                            <input type="text" name="guest_address_1" class="form-control input-sm" value="<?= isset($_REQUEST['guest_address_1']) ? $_REQUEST['guest_address_1'] : isset($_SESSION['user_log']) ? $address['address_1'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Address 2(Optional):</label>
                                            <input type="text" name="guest_address_2" class="form-control input-sm" value="<?= isset($_REQUEST['guest_address_2']) ? $_REQUEST['guest_address_2'] : isset($_SESSION['user_log']) ? $address['address_2'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>City<span style="color: #C62326;">*</span>:</label>
                                            <input type="text" name="guest_city" class="form-control input-sm" value="<?= isset($_REQUEST['guest_city']) ? $_REQUEST['guest_city'] : isset($_SESSION['user_log']) ? $address['city'] : '' ?>"/>
                                        </div>
                                        <div class="form-group" >
                                            <input type="hidden" name="grand_total" id="grand_total" value="<?= $this->ReturngrandTotal() ?>"/>
                                            <input type="hidden" name="total_price_checkout" id="total_check_out" value="<?= $this->ReturnTotal() ?>"/>
                                            <input type="hidden" name="tax_check_out" id="tax_checkout" value="<?= $this->ReturnTax() ?>"/>
                                            <label>Select Shipping State (US/Canada Customers<span style="color: #C62326;">*</span>):</label>
                                            <select name="guest_state" class="form-control states">
                                                <option value="--">--</option>
                                                <?php
                                                $this->GetAllTheStates();
                                                foreach ($this->states as $state) {
                                                    $selected_s = "";

                                                    if (isset($_SESSION['user_log']) && $address['state'] == $state['name'] && !isset($_REQUEST['guest_state'])) {
                                                        $selected_s = 'selected="selected"';
                                                    } else {
                                                        $selected_state = isset($_REQUEST['guest_state']) ? $_REQUEST['guest_state'] : '';
                                                        if (isset($_REQUEST['guest_state'])) {
                                                            if ($selected_state == $state['name']) {
                                                                $selected_s = 'selected="selected"';
                                                            } else {
                                                                $selected_s = '';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <option value="<?= $state['name'] ?>" <?= $selected_s ?>><?= $state['name'] ?></option> 
                                                    <?php
                                                }
                                                ?>
                                            </select>

                                        </div>
                                        <div class="form-group">
                                            <label>Enter Region/ Province (International Customers<span style="color: #C62326;">*</span>):</label>
                                            <input type="text" name="guest_region" class="form-control input-sm" value="<?= isset($_REQUEST['guest_region']) ? $_REQUEST['guest_region'] : isset($_SESSION['user_log']) ? $address['province'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Zip code / Postal Code<span style="color: #C62326;">*</span>:</label>
                                            <input type="text" name="guest_zip" class="form-control input-sm" value="<?= isset($_REQUEST['guest_zip']) ? $_REQUEST['guest_zip'] : isset($_SESSION['user_log']) ? $address['zip_code'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Country<span style="color: #C62326;">*</span></label>
                                            <select name="guest_country" class="form-control">
                                                <option value="--">--</option>
                                                <?php
                                                $this->GetAllTheCountries();
                                                foreach ($this->country as $country) {
                                                    $selected = "";
                                                    if (isset($_SESSION['user_log']) && $address['country'] == $country['name'] && !isset($_REQUEST['guest_country'])) {
                                                        $selected = 'selected="selected"';
                                                    } else {
                                                        if (DEFAULT_COUNTRY == $country['name'] && !isset($_REQUEST['guest_country'])) {
                                                            $selected = 'selected="selected"';
                                                        } else {
                                                            $selected_country = isset($_REQUEST['guest_country']) ? $_REQUEST['guest_country'] : '';
                                                            if (isset($_REQUEST['guest_country'])) {
                                                                if ($selected_country == $country['name']) {
                                                                    $selected = 'selected="selected"';
                                                                } else {
                                                                    $selected = '';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <option value="<?= $country['name'] ?>" <?= $selected ?>><?= $country['name'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">

                                            <label>Credit Card Number<span style="color: #C62326;">*</span>:</label>
                                            <img id="type"  class="rock-cc-validator-image"/>
                                            <input id="cc_number" name="cc_number" placeholder="1234 5678 9000 0000" class="form-control input-sm" maxlength="19" value="<?= isset($_REQUEST['cc_number']) ? $_REQUEST['cc_number'] : '' ?>"/>

                                            <input type="hidden" name="cc_length" id="cc_length" value=""/>
                                            <input type="hidden" name="cc_luhn" id="cc_luhn" value=""/>
                                            <input type="hidden" name="cc_is_valid" id="cc_is_valid" value=""/>

                                        </div>
                                        <div class="form-group">
                                            <label>Expiry date<span style="color: #C62326;">*</span>:</label>
                                            <input type="text" name="cc_exp_year" id="expire" placeholder="yyyy" class="form-control input-sm" maxlength="4" value="<?= isset($_REQUEST['cc_exp_year']) ? $_REQUEST['cc_exp_year'] : '' ?>"/>
                                            <span id="slashes">/</span>
                                            <input type="text" name="cc_exp_month" id="expire_month" placeholder="mm" class="form-control input-sm" maxlength="2" value="<?= isset($_REQUEST['cc_exp_month']) ? $_REQUEST['cc_exp_month'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>CVV<span style="color: #C62326;">*</span>:</label>
                                            <input type="text" name="cc_cvv" id="cvv" placeholder="123" class="form-control input-sm" maxlength="4" value="<?= isset($_REQUEST['cc_cvv']) ? $_REQUEST['cc_cvv'] : '' ?>"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Shipping Instructions:</label>
                                            <textarea name="instructions" id="instructions" class="form-control input-sm"><?= isset($_REQUEST['instructions']) ? $_REQUEST['instructions'] : '' ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <?php
                                            if ($this->returnFlag() == 1 || $this->returnFlag() == 2) {
                                                $cmd = "Check out as a guest";
                                            } else if ($this->returnFlag() == 0) {
                                                $cmd = "review order";
                                            }

                                            ?>

                                            <input type="hidden" name="default_currency" id="d_currency" value="<?= DEFAULT_CURRENCY ?>"/>
                                            <input type="hidden" name="h_state" id="h_state" value="<?= DEFAULT_SHIPPED_FROM ?>"/>
                                            <input type="hidden" name="cc_type" value="" id="cc_type"/>
                                            <input type="hidden" name="hidden_state" value="<?= isset($_REQUEST['guest_state']) ? $_REQUEST['guest_state'] : '' ?>"/>
                                            <input type="hidden" name="flag" value="<?= $this->returnFlag() ?>" id="flag"/>
                                            <input type="hidden" name="cmd" value="check-out-as-guest"/>
                                            <input type="hidden" name="array_size" value="<?= isset($_REQUEST['array_size'])?$_REQUEST['array_size']:'' ?>"/>
                                            <input type="hidden" name="a_size" value="<?= isset($_REQUEST['a_size'])?$_REQUEST['a_size']:'' ?>"/>
                                            <input type="submit" name="review_order" value="Review Order" id="sub" class="btn btn-default btn-lg"/>
                                        </div>
                                    </div>
                                    <?php
                                    if (isset($_GET['edit_info']) && $_GET['edit_info'] == "true") {

                                        $_SESSION['guest_name'] = isset($_REQUEST['guest_name']) ? $_REQUEST['guest_name'] : isset($_SESSION['user_log']) ? $user_data['f_name'] : '';
                                    }
                                    ?>
                                </form>

                            </div>


                        </div>
                    </div>
                </div>             
                <!--Order details-->
                <?php
                $this->OrderDetails();
                ?>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                var flag = $('#flag').val();
                if (flag === "0" || flag === 0) {
                    $('#guest-checkout-form').hide();
                }
                console.log($('#sub').val());
                //                $('#sub').click(function () {
                //                    console.log($('#sub').val());
                //                    $('#guest-checkout-form').hide();
                //                });
            });

        </script>
        <?php
    }

    public function GetAllTheStates() {
        $sql = "SELECT * FROM `rock_states` ORDER BY name ASC";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->states[] = $row;
        }
        return $this->states;
    }

    public function GetAllTheCountries() {
        $sql = "SELECT * FROM `countries` ORDER BY name ASC";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->country[] = $row;
        }
        return $this->country;
    }

    public function CheckFormValidation($data) {


        if (empty($data['guest_name']) && empty($data['guest_last_name']) && empty($data['guest_email']) && empty($data['guest_address_1']) && empty($data['guest_city']) && empty($data['guest_address_1']) && empty($data['guest_zip']) && $data['guest_country'] == "--" &&
                empty($data['cc_number']) && empty($data['cc_exp_year']) && empty($data['cc_exp_month']) && empty($data['cc_cvv'])) {
            $this->flag = 2;
            $message = array("1" => "All required fields are empty");
            array_push($this->messages, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }
        if (empty($data['guest_name']) || empty($data['guest_last_name']) || empty($data['guest_email']) || empty($data['guest_address_1']) || empty($data['guest_city']) || empty($data['guest_address_1']) || empty($data['guest_zip']) || $data['guest_country'] == "--" ||
                empty($data['cc_number']) || empty($data['cc_exp_year']) || empty($data['cc_exp_month']) || empty($data['cc_cvv'])) {

            $this->flag = 2;
            $message = array("1" => "One or more of the required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }
        if (!filter_var($data['guest_email'], FILTER_VALIDATE_EMAIL)) {
            $this->flag = 2;
            $message = array("1" => "You have entered an invalid email address.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }
        if ($data['guest_country'] == "Canada" || $data['guest_country'] == "United States") {
            if ($data['guest_state'] == "--") {
                $this->flag = 2;
                $message = array("1" => "Please select your state or province.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-check-out-alert-warning";
            }
        }
        if (($data['guest_country'] != "Canada" && $data['guest_country'] != "United States")) {
            if (empty($data['guest_region'])) {
                $this->flag = 2;
                $message = array("1" => "Please enter your region or province.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-check-out-alert-warning";
            } else if ($data['guest_state'] != "--") {
                $this->flag = 2;
                $message = array("1" => "State selection is only for United States and Canada.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-check-out-alert-warning";
            }
        }

        if ($data['cc_is_valid'] == "false" || $data['cc_is_valid'] === false) {
            $this->flag = 2;
            $message = array("1" => "Invalid credit card information is provided. Please check the card number and enter again.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }

        if ($this->flag == 1) {

            $message = array("1" => "We are preparing your check out data.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-check-out-alert-success";
            $this->flag = 0;
            $this->returnFlag();
        }
    }

    public function returnFlag() {
        return $this->flag;
    }

    public function OrderDetails() {
        ?>
        <!--Check out detail-->
        <script>
            $(document).ready(function () {
                var state1 = $('select.states').find(':selected').val();

                var total_val1 = $('#total_check_out').val();
                var total1 = parseFloat(Math.round(total_val1 * 100) / 100).toFixed(2);
                var grand_total_val1 = $('#grand_total').val();
                var grand_total1 = parseFloat(Math.round(grand_total_val1 * 100) / 100).toFixed(2);
                console.log(grand_total1);
                console.log(total1);
                var default_state = $('#h_state').val();
                var d_currency = $('#d_currency').val();
                if (state1 !== default_state) {

                    $('#tax_void').html("$ 0.00");
                    $('#tax_void').css("text-decoration", "line-through");
                    $('#tax').css("text-decoration", "line-through");
                    $('#grand_total_val').html("$" + total1 + " " + d_currency)
                } else {

                    var tax_val1 = $("#tax_checkout").val();
                    var tax1 = parseFloat(Math.round(tax_val1 * 100) / 100).toFixed(2);
                    $('#tax_void').html("$" + tax1 + " " + d_currency);
                    $('#tax_void').css("text-decoration", "none");
                    $('#tax').css("text-decoration", "none");
                    $('#grand_total_val').html("$" + grand_total1 + " " + d_currency);
                }


                $('select.states').change(function () {
                    var state = $('select.states').find(':selected').val();

                    var total_val = $('#total_check_out').val();
                    var total = parseFloat(Math.round(total_val * 100) / 100).toFixed(2);
                    var grand_total_val = $('#grand_total').val();
                    var grand_total = parseFloat(Math.round(grand_total_val * 100) / 100).toFixed(2);
                    console.log(grand_total);
                    console.log(total);
                    if (state !== default_state) {

                        $('#tax_void').html("$ 0.00");
                        $('#tax_void').css("text-decoration", "line-through");
                        $('#tax').css("text-decoration", "line-through");
                        $('#grand_total_val').html("$" + total + " " + d_currency)
                    } else {

                        var tax_val = $("#tax_checkout").val();
                        var tax = parseFloat(Math.round(tax_val * 100) / 100).toFixed(2);
                        $('#tax_void').html("$" + tax + " " + d_currency);
                        $('#tax_void').css("text-decoration", "none");
                        $('#tax').css("text-decoration", "none");
                        $('#grand_total_val').html("$" + grand_total + " " + d_currency);
                    }
                });
            });
        </script>
        <div class="col-md-4">
            <div class="panel panel-default ">
                <div class="panel-heading rock-cart-panel">
                    <h4>Cart Details</h4>
                </div>
                <div class="panel-body rock-cart-total">          
                    <ul class="list-group">
                        <li class="list-group-item"><b>Subtotal:</b>&Tab;<span style="float: right">$<?= number_format($this->total, 2, ".", ""); ?>&nbsp;<?= DEFAULT_CURRENCY ?></span></li>
                        <li class="list-group-item"><a href="#" data-toggle="tooltip" data-placement="right"  id="tax" title="<?= DEFAULT_SHIPPED_FROM ?> State Tax applies only if purchasing within The State of <?= DEFAULT_SHIPPED_FROM ?>."><b>Tax**:</b>&Tab;@<?= DEFAULT_TAX_RATE ?> </a><span style="float: right" id="tax_void">$<?= number_format($this->tax, 2, ".", ""); ?>&nbsp;<?= DEFAULT_CURRENCY ?></span></li>
                        <li class="list-group-item"><b>Shipping:</b>&Tab;<span style="float: right">$0.00</span></li>
                        <li class="list-group-item"><b>Estimated Total:</b>&Tab;<span style="float: right" id="grand_total_val">$<?= number_format($this->grand_total, 2, ".", ""); ?>&nbsp;<?= DEFAULT_CURRENCY ?></span></li>
                    </ul>
                    <script>

                        $(document).ready(function () {
                            $("#tax").hover(function () {
                                $('#tax').tooltip('show');
                            });
                        });
                    </script>

                    <div class="form-group rock-cart-button-div">

                        <form method="post" name="continue">

                            <button class="btn btn-default btn-sm" name="c_shop"  formaction="" type="submit"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i>&nbsp;Continue Shopping</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function OrderCalculations() {
        $this->cart = new ShoppingCartFunc();
        $total_array = array();
        $this->cart->GetSubTotalOnCart($_COOKIE['order']);
        if ($this->cart->get_vals != NULL) {
            foreach ($this->cart->get_vals as $cart_sub_totals) {
                $unit_price = $cart_sub_totals['sel_item_price'];
                $quantity = $cart_sub_totals['sel_item_qty'];

                $item_sub_total = $unit_price * $quantity;
                array_push($total_array, $item_sub_total);
            }
            $total = 0.00;
            for ($t = 0; $t < count($total_array); $t++) {
                $total = $total + $total_array[$t];
            }
            $tax = $total * DEFAULT_TAX_RATE;
            $grand_total = $total + $tax;
        } else {
            $total = 0.00;
            $tax = 0.00;
            $grand_total = 0.00;
        }

        $this->total = $total;
        $this->tax = $tax;
        $this->grand_total = $grand_total;
    }

    public function ReturnTotal() {
        return $this->total;
    }

    public function ReturnTax() {
        return $this->tax;
    }

    public function ReturngrandTotal() {
        return $this->grand_total;
    }

    public function GetAllFrontEnduserData($data) {
        //desc
        $sql = "SELECT * FROM `frontend_users_desc` WHERE `cust_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->user_desc[] = $row;
            }
            return $this->user_desc;
        }
    }

    public function GetUserEmail($data) {
        //Email
        $get_email = "SELECT `email` FROM `frontend_users_email` WHERE `cust_id` = '" . $data . "'";
        $result_email = $this->_mysqli->query($get_email);
        if ($result_email->num_rows > 0) {
            while ($row_email = $result_email->fetch_array(MYSQLI_ASSOC)) {
                $this->user_email[] = $row_email;
            }
            return $this->user_email;
        }
    }

    public function GetUserAddress($data) {
        //address
        $get_address = "SELECT * FROM `frontend_users_address` WHERE `cust_id` = '" . $data . "'";
        $result_address = $this->_mysqli->query($get_address);
        if ($result_address->num_rows > 0) {
            while ($row_address = $result_address->fetch_array(MYSQLI_ASSOC)) {
                $this->user_address[] = $row_address;
            }
            return $this->user_address;
        } else {
            $row_address = array();
            $row_address ['address_1'] = "";
            $row_address ['address_2'] = "";
            $row_address ['city'] = "";
            $row_address ['zip_code'] = "";
            $row_address ['state'] = "";
            $row_address ['province'] = "";
            $row_address ['coutry'] = "";

            $this->user_address[] = $row_address;
            return $this->user_address;
        }
    }

}
