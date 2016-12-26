<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CheckOut
 *
 * @author rostom
 */
class CheckOut {

    private $_mysqli;
    private $_db;
    public $cart;
    public $total = 0;
    public $tax = 0;
    public $grand_total = 0;
    public $states;
    public $country;
    public $flag = 0;
    public $messages = array();
    public $alert_class = "";

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
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

    public function CheckOptionForm($data) {
        /*
         * once the customer clicks the check out
         * 1. check if the customer is logged in and we have their shipping information
         *      .If we do then show the credit card information
         *      .if we dont then show the entire form with billing address
         * 2. if user is not logged in then show two options 
         *      .Login or sign up
         *      .check out as guest
         */
        /*
         * Part 1 (TBC) Checking session or cookies
         */


        /*
         * For now lets do part two of this section
         */
        if ($_REQUEST['cmd'] == "pre_check_out") {
            $this->SigninOrSignupOrAsGuest($data);
        } else if ($_REQUEST['guest'] == "Check out as a guest") {

            $this->CheckOutAsGuest($data);
        }
    }

    public function SigninOrSignupOrAsGuest($page_data) {
        if (isset($_REQUEST['user_login'])) {
            $this->DoLoginFrontEndUser($_REQUEST);
        }
     
        ?>
        <div class="container rock-main-container">

            <h4 style="text-transform: uppercase;"><?= $page_data['page_name']; ?></h4>
            <div class="row">
                <div class="col-md-8 <?= $this->alert_class ?>">

                    <?php
                    if ($this->flag == 1) {
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
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading rock-check-out-step-1-p-heading">
                            <h4><strong>Check out Options</strong></h4>
                        </div>

                        <div class="panel-body">
                            <form method="post">

                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5><i class="fa fa-user"></i>&nbsp;<b>Sign in</b></h5>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>email:</label>
                                                <input type="email" name="user_email" class="form-control input-sm" placeholder="Enter your email"/>
                                            </div>
                                            <div class="form-group">
                                                <label>password:</label>
                                                <input type="password" name="user_password" class="form-control input-sm" placeholder="password"/>
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="cmd" value="pre_check_out"/>
                                                <input type="submit" name="user_login" value="login" class="btn btn-success btn-sm"/>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </form>
                            <div class="col-md-1 rock-or-divider">
                                <span>or</span>
                            </div>
                            <div class="col-md-5">
                                <form method="post">
                                    <div class="form-group rock-sign-in-guest-div">
                                        <?php
                                        for ($r = 0; $r < $_REQUEST['a_size']; $r++) {
                                            ?>
                                            <input type="hidden" name="item_size_check_<?= $r ?>" value="<?= $_REQUEST['item_size_' . $r] ?>"/>
                                            <?php
                                        }
                                        ?>
                                        <input type="hidden" name="array_size" value="<?= $_REQUEST['a_size'] ?>"/>
                                        <input type="hidden" name="default_currency" id="d_currency" value="<?= DEFAULT_CURRENCY ?>"/>
                                        <input type="hidden" name="h_state" id="h_state" value="<?= DEFAULT_SHIPPED_FROM ?>"/>
                                        <input type="hidden" name="flag" value="1"/>
                                        <input type="hidden" name="cmd" value="check-out-as-guest"/>
                                        <input type="submit" name="guest" value="Check out as a guest" class="btn btn-primary"/>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
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
                    });
                </script>
                <!--Check out detail-->
                <div class="col-md-4">
                    <div class="panel panel-default ">
                        <div class="panel-heading rock-cart-panel">
                            <h4>Cart Details</h4>
                        </div>
                        <div class="panel-body rock-cart-total">          
                            <ul class="list-group">
                                <li class="list-group-item"><b>Subtotal:</b>&Tab;<span style="float: right">$<?= number_format($this->total, 2, ".", ""); ?>&nbsp;<?= DEFAULT_CURRENCY ?></span></li>
                                <li class="list-group-item"><a href="#" data-toggle="tooltip" data-placement="right"  id="tax" title="<?= DEFAULT_SHIPPED_FROM ?> State Tax applies only if purchasing within The State of <?= DEFAULT_SHIPPED_FROM ?>."><b>Tax**:</b>&Tab;@<?= DEFAULT_TAX_RATE ?> </a><span style="float: right">$<?= number_format($this->tax, 2, ".", ""); ?>&nbsp;<?= DEFAULT_CURRENCY ?></span></li>
                                <li class="list-group-item"><b>Shipping:</b>&Tab;<span style="float: right">$0.00</span></li>
                                <li class="list-group-item"><b>Estimated Total:</b>&Tab;<span style="float: right">$<?= number_format($this->grand_total, 2, ".", ""); ?>&nbsp;<?= DEFAULT_CURRENCY ?></span></li>


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

            </div>
        </div>
        <?php
    }

    public function DoLoginFrontEndUser($data) {


        if (empty($data['user_email']) && empty($data['user_password'])) {
            $this->flag = 1;
            $message = array("1" => "All required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (empty($data['user_email']) || empty($data['user_password'])) {
            $this->flag = 1;
            $message = array("1" => "One or more required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else {

            $sql = "SELECT `cust_id` FROM `frontend_users_email` WHERE `email` = '" . $data['user_email'] . "' AND `password` ='" . md5($data['user_password']) . "'";
            $result = $this->_mysqli->query($sql);
            $num_rows = $result->num_rows;
            if ($num_rows > 0) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                    $get_account_stat = "SELECT `status` FROM `frontend_users_status` WHERE `cust_id` = '" . $row['cust_id'] . "' AND `status` = '1' ";
                    $get_account_stat_res = $this->_mysqli->query($get_account_stat);
                    $status = $get_account_stat_res->num_rows;
                    if ($status > 0) {
                        $_SESSION['timeout'] = time();
                        $_SESSION['user_log'] = uniqid();
                        $user_session = $_SESSION['user_log'];
                        $insert_session = "UPDATE `frontend_users_status` SET `last_session` = '" . $user_session . "' WHERE `cust_id` = '" . $row['cust_id'] . "'";
                        $insert_session_result = $this->_mysqli->query($insert_session);
                        $_SESSION['user_id'] = $row['cust_id'];

                        header('Location: /account?cmd=user-check-out&s=' . $user_session);
                    } else {
                        $this->flag = 1;
                        $message = array("1" => "Account is not active.");
                        array_push($this->messages, $message);
                        $this->alert_class = "rock-warning-message";
                    }
                }
            } else {
                $this->flag = 1;
                $message = array("1" => "Authentication failed. You entered an incorrect username or password.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-warning-message";
            }
        }
    }

}
