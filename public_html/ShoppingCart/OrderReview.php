<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderReview
 *
 * @author rostom
 */
class OrderReview {

    private $_mysqli;
    private $_db;
    public $cc_number;
    public $cc_expiration;
    public $cc_amount;
    public $product_info;
    public $product_dtails;
    public $short_state_name;
    public $product_size;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();

        $this->ReturnCarNumber();
        $this->ReturnExpiration();
        $this->ReturnAmount();
    }

    public function Getrequest($data) {
        
    }

    public function ReviewOrderProccess($page_data, $request) {
        ?>

        <div class="col-md-8">
            <div class="panel panel-default rock-check-out-main-div">
                <div class="panel-heading rock-check-out-step-1-p-heading">
                    <h4><strong><i class="fa fa-cc-<?= isset($_REQUEST['cc_type']) ? $_REQUEST['cc_type'] : 'cc' ?>"></i>&nbsp;Review Order and Check out</strong></h4>
                </div>
                <div class="panel-body">
                    <?php
                    if (isset($_REQUEST['review_order'])) {
                        $name = $_REQUEST['guest_name'];
                        $last_name = $_REQUEST['guest_last_name'];
                        $email = $_REQUEST['guest_email'];
                        $address_1 = $_REQUEST['guest_address_1'];
                        $address_2 = $_REQUEST['guest_address_2'];
                        $city = $_REQUEST['guest_city'];
                        $grand_total = $_REQUEST['grand_total'];
                        $totla_pre_tax = $_REQUEST['total_price_checkout'];
                        $tax = $_REQUEST['tax_check_out'];
                        $state = $_REQUEST['guest_state'];
                        $region = $_REQUEST['guest_region'];
                        $zip_code = $_REQUEST['guest_zip'];
                        $country = $_REQUEST['guest_country'];
                        $cc_number = $_REQUEST['cc_number'];
                        $cc_length = $_REQUEST['cc_length'];
                        $cc_is_valid = $_REQUEST['cc_is_valid'];
                        $cc_cvv = $_REQUEST['cc_cvv'];
                        $shipping_instruction = $_REQUEST['instructions'];
                        $cc_type = $_REQUEST['cc_type'];
                        $flag = $_REQUEST['flag'];
                        $exp_card = $_REQUEST['cc_exp_year'] . "-" . $_REQUEST['cc_exp_month'];
                        $masked_card_num = $this->ccMasking($cc_number);
                        $this->cc_number = $cc_number;
                        $this->cc_expiration = $exp_card;
                        $this->cc_amount = $grand_total;
                        ?>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h6><b><i class="fa fa-male" aria-hidden="true"></i>&nbsp;Personal Information</b></h6>
                                    </div>
                                    <div class="panel-body">
                                        <p><b><?= $name . " " . $last_name ?></b></p>
                                        <p><?= $email ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <?php
                                        if (isset($_SESSION['user_log'])) {
                                            $cmd = "user-check-out";
                                        } else {
                                            $cmd = "check-out-as-guest";
                                        }
                                        ?>
                                        <h6><b><i class="fa fa-truck" aria-hidden="true"></i>&nbsp;Shipping Information</b> <a href="see_cart?cmd=<?= $cmd ?>&ssid=<?= md5($_COOKIE['order']) ?>&edit_info=true">change</a></h6>
                                    </div>
                                    <?php
                                    $this->GetStatesCode($state);
                                    foreach ($this->short_state_name as $state_short) {
                                        
                                    }
                                    ?>
                                    <div class="panel-body">
                                        <p><i class="fa fa-cc-<?= isset($_REQUEST['cc_type']) ? $_REQUEST['cc_type'] : 'cc' ?>"></i> Ending in <?= $masked_card_num ?></p>
                                        <p>Expires <?= $exp_card ?></p>
                                        <p><?= $address_1 ?></p>
                                        <p><?= $address_2 ?></p>
                                        <p><?= $city ?>, <?= $state_short['short'] . " " . $zip_code ?></p>                                      
                                        <p><?= $country ?></p>
                                    </div>
                                </div>
                            </div>



                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h6><b><i class="fa fa-cart-plus" aria-hidden="true"></i>&nbsp;Item Details</b> &nbsp; <a href="/see_cart?cmd=cart&ssid=<?= md5($_COOKIE['order']) ?>">change</a></h6>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-responsive table-bordered table-hover">
                                        <tr>
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>Qty</th>
                                            <th>Taxable</th>
                                            <th>Unit Price</th>
                                            <th>Item Total</th>
                                        </tr>
                                        <?php
                                        if ($this->GetPurchasedProducts($_COOKIE['order'])) {
                                            foreach ($this->product_info as $product) {
                                                $this->product_dtails = NULL;
                                                if ($this->GetItemDetails($product['sel_item_id'])) {

                                                    foreach ($this->product_dtails as $product_detail) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $product['id'] ?></td>
                                                            <td><?= $product_detail['item_name'] ?></td>
                                                            <td><?= $product['sel_item_qty'] ?></td>
                                                            <td>
                                                                <?php
                                                                $taxable = "";
                                                                if ($state == "California") {
                                                                    $taxable = "Y";
                                                                } else {
                                                                    $taxable = "N";
                                                                }
                                                                echo $taxable
                                                                ?>
                                                            </td>
                                                            <td>$<?= $product['sel_item_price'] . " (" . DEFAULT_CURRENCY . ")" ?></td>
                                                            <td>$<?= $product['sel_item_price'] * $product['sel_item_qty'] . " (" . DEFAULT_CURRENCY . ")" ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                        ?>

                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 rock-review-order-btn-div">
                            <form method="post">
                                <input type="hidden" name="item_idss" value="<?= $_REQUEST['item_id_0'] ?>"/>
                                <input type="hidden" name="address_1" value="<?= $address_1 ?>"/>
                                <input type="hidden" name="address_2" value="<?= $address_2 ?>"/>
                                <input type="hidden" name="address" value="<?= $address_1 . ", " . $address_2 ?>"/>
                                <input type="hidden" name="city" value="<?= $city ?>"/>
                                <input type="hidden" name="zip_code" value="<?= $zip_code ?>"/>
                                <input type="hidden" name="country" value="<?= $country ?>"/>
                                <input type="hidden" name="customer_ip" value="<?= $_SERVER['REMOTE_ADDR'] ?>"/>
                                <input type="hidden" name="customer_id" value="<?= isset($_SESSION['user_log']) ? $_SESSION['user_id'] : uniqid() ?>"/>
                                <input type="hidden" name="cust_email" value="<?= $email ?>"/>
                                <input type="hidden" name="state" value="<?= $state_short['short'] ?>"/>

                                <input type="hidden" name="taxable" value="<?= $taxable ?>"/>
                                <?php
                                for ($j = 0; $j < count($this->product_info); $j++) {
                                    ?>
                                    <input type="hidden" name="product_name_<?= $j ?>" value="<?= $this->product_info[$j]['sel_item_name'] ?>"/>
                                    <input type="hidden" name="product_id_<?= $j ?>" value="<?= $this->product_info[$j]['id'] ?>"/>
                                    <input type="hidden" name="product_qty_<?= $j ?>" value="<?= $this->product_info[$j]['sel_item_qty'] ?>"/>
                                    <input type="hidden" name="product_price_<?= $j ?>" value="<?= $this->product_info[$j]['sel_item_price'] ?>"/>

                                    <?php
                                }
                                ?>
                                <input type="hidden" name="sells_tax" value="<?= $tax ?>"/>
                                <input type="hidden" name="product_detail_array_size" value="<?= $j ?>"/>
                                <input type="hidden" name="c_name" value="<?= $name ?>"/>
                                <input type="hidden" name="c_last_name" value="<?= $last_name ?>"/>
                                <?php
                                if ($state == "California") {
                                    $t_price = $grand_total;
                                } else {
                                    $t_price = $totla_pre_tax;
                                }
                                ?>
                                <input type="hidden" name="order_id" value="<?= $_COOKIE['order'] ?>"/>
                                <input type="hidden" name="card_type" value="<?= $cc_type ?>"/>
                                <input type="hidden" name="masked_cc" value="<?= $masked_card_num ?>"/>
                                <input type="hidden" name="ship_instruct" value="<?= $shipping_instruction ?>"/>
                                <input type="hidden" name="amount" value="<?= $t_price ?>"/>
                                <input type="hidden" name="cc_expire" value="<?= $exp_card ?>"/>
                                <input type="hidden" name="cc_number" value="<?= $this->cc_number ?>"/>
                                <input type="hidden" name="cvv" value="<?= $cc_cvv ?>"/>
                                <input type="hidden" name="cmd" value="review order"/>
                                <input type="submit" name="sub" value="Check Out" class="btn btn-success btn-lg"/>
                            </form>
                        </div>


                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php ?>

        <?php
    }

    public function ReturnCarNumber() {
        return $this->cc_number;
    }

    public function ReturnExpiration() {
        return $this->cc_expiration;
    }

    public function ReturnAmount() {
        return $this->cc_amount;
    }

    public function ccMasking($number, $maskingCharacter = '*') {
        return substr($number, 0, 0) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
    }

    public function GetPurchasedProducts($data) {

        $sql = "SELECT * FROM `store_orders_items` WHERE order_id = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->product_info[] = $row;
            }
            return $this->product_info;
        }
    }

    public function GetItemDetails($data) {
        $sql = "SELECT * FROM `pages_products` WHERE page_id = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->product_dtails[] = $row;
            }
            return $this->product_dtails;
        }
    }

    public function GetStatesCode($data) {

        $sql = "SELECT `short` FROM `rock_states` WHERE `name` = '" . trim($data) . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->short_state_name[] = $row;
            }
            return $this->short_state_name;
        }
    }

    public function PrintOrderReciept($data, $transactionID, $authID) {
      
        ?>
        <div class="container rock-main-container" >
            <script>
                $(document).ready(function () {
                    $('#do_print').click(function () {
                        $('.printable').print();
                    })
                });
            </script>

            <div class="row">
                <div class="col-md-12 ">
                    <div class="panel panel-default rock-check-out-main-div">
                        <div class="panel-heading rock-check-out-step-1-p-heading">
                            <h4 style="text-transform: uppercase;">Receipt</h4>
                        </div>
                        <div class="panel-body printable">
                            <h2>Thank you for your order!</h2>

                            <p>You may print this receipt page for your records.&nbsp;<a id="do_print"><i class="fa fa-print"></i></a><p>

                            <p id="p-headers">Order Information</p>





                            <table class="table">
                                <tr>
                                    <td>Merchant:</td>
                                    <td><?= CUSTOMER ?></td>

                                </tr>
                                <tr>
                                    <td>Date/Time:</td>
                                    <td><?= date("d-M-y") ?></td>
                                </tr>
                                <tr>
                                    <td>Customer ID:</td>
                                    <td><?= $data['customer_id'] ?></td>
                                </tr>
                            </table>
                            <hr/>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <td><b>Billing Information</b></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['c_name'] . " " . $data['c_last_name'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['address'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['city'] . ", " . $data['state'] . " " . $data['zip_code'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['country'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['cust_email'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <td><b>Shipping Information</b></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['c_name'] . " " . $data['c_last_name'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['address'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['city'] . ", " . $data['state'] . " " . $data['zip_code'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?= $data['country'] ?></td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table">
                                    <tr>
                                        <td><b>Item</b></td>
                                        <td><b>Description</b></td>
                                        <td><b>Qty</b></td>
                                        <td><b>Size</b></td>
                                        <td><b>Taxable</b></td>
                                        <td><b>Unit Price</b></td>
                                        <td><b>Item Total</b></td>
                                    </tr>
                                    <?php
                                    $select_details = "SELECT `id`,`sel_item_size` FROM `store_orders_items` WHERE `order_id` = '" . $data['order_id'] . "'";

                                    $select_details_res = $this->_mysqli->query($select_details);
                                    if ($select_details_res->num_rows > 0) {
                                        while ($row_result = $select_details_res->fetch_array(MYSQLI_ASSOC)) {
                                            $this->product_size[] = $row_result;
                                        }
                                    }

                                    $product_d_size = $_REQUEST['product_detail_array_size'];
                                    for ($i = 0; $i < $product_d_size; $i++) {
                                        $product_name = $_REQUEST['product_name_' . $i];
                                        $product_id = $_REQUEST['product_id_' . $i];
                                        $product_qty = $_REQUEST['product_qty_' . $i];
                                        $product_price = $_REQUEST['product_price_' . $i];
                                        ?>
                                        <tr>
                                            <td><?= $product_id ?></td>
                                            <td><?= $product_name ?></td>
                                            <td><?= $product_qty ?></td>
                                            <td>
                                                <?php
                                                if ($this->product_size[$i]['id'] == $product_id) {
                                                    echo $this->product_size[$i]['sel_item_size'];
//                                                    if (isset($_REQUEST['sub']) && $_REQUEST['sub'] == "Check Out") {
//                                                        $_REQUEST['sel_item_size'] = $this->product_size[$i]['sel_item_size'];
//                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td><?= $data['taxable'] ?></td>
                                            <td>$<?= $product_price ?>&nbsp;(<?= DEFAULT_CURRENCY ?>)</td>
                                            <td>$<?= $product_price * $product_qty ?>&nbsp;(<?= DEFAULT_CURRENCY ?>)</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Tax:</b></td>
                                        <td><b>$<?= number_format($data['sells_tax'], 2, '.', ',') ?>&nbsp;(<?= DEFAULT_CURRENCY ?>)</b></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Total:</b></td>
                                        <td><b>$<?= number_format($data['amount'], 2, '.', ',') ?>&nbsp;(<?= DEFAULT_CURRENCY ?>)</b></td>
                                    </tr>
                                </table>

                            </div>
                            <div class="col-md-12">
                                <p id="p-headers"><?= $data['masked_cc'] ?></p>
                                <table>
                                    <tr>
                                        <td>Date/Time:</td>
                                        <td><?= date('d-M-Y') ?></td>
                                    </tr>
                                    <tr>
                                        <td>Transaction ID:</td>
                                        <td><?= $transactionID ?></td>
                                    </tr>
                                    <tr>
                                        <td>Auth Code:</td>
                                        <td><?= $authID ?></td>
                                    </tr>
                                    <tr>
                                        <td>Payment Method:</td>
                                        <td>&nbsp;<i class="fa fa-cc-<?= $data['card_type'] ?>"></i>&nbsp; ending <?= $data['masked_cc'] ?></td>
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

    public function CardProccessError($e) {
        ?>
        <div class="container rock-main-container" >
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default rock-check-out-main-div">
                        <div class="panel-heading rock-check-out-step-1-p-heading">
                            <h4 style="text-transform: uppercase;">Response Error</h4>
                        </div>
                        <div class="panel-body">
                            <p>Sorry, transaction did not  go through. Please try again.</p>
                            <p>Code:<?= $e ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function UpdateCartCheckOut($data, $transacation_id, $auth_code) {

        $order_date = date("d-M-Y H:i:s");
        $order_name = $data['c_name'] . " " . $data['c_last_name'];
        /*
         * Insert data into store_orders and check_out
         */
        $insert_into_store_orders = "INSERT INTO `store_orders` (order_date, order_name, order_address_1, order_address_2, "
                . "order_city, order_state, order_zip, order_country, order_email, item_total, authorization, transaction_id, shipping_instructions, status) "
                . "VALUES "
                . "("
                . "'" . $order_date . "', "
                . "'" . $order_name . "', "
                . "'" . $data['address_1'] . "', "
                . "'" . $data['address_2'] . "', "
                . "'" . $data['city'] . "', "
                . "'" . $data['state'] . "',"
                . "'" . $data['zip_code'] . "',"
                . "'" . $data['country'] . "',"
                . "'" . $data['cust_email'] . "',"
                . "'" . $data['amount'] . "',"
                . "'" . $auth_code . "', "
                . "'" . $transacation_id . "',"
                . "'".$data['ship_instruct']."', "
                . "'pending'"
                . ")";
        $insert_into_store_orders_res = $this->_mysqli->query($insert_into_store_orders);

        if (isset($_SESSION['user_log'])) {
            $insert_user_p_history = "INSERT INTO `frontend_users_purchase_history` (cust_id, transaction_id, date_of_purchase)"
                    . "VALUES "
                    . "("
                    . "'" . $data['customer_id'] . "',"
                    . "'" . $transacation_id . "', "
                    . "'" . $order_date . "'"
                    . ")";
            $insert_user_p_history_res = $this->_mysqli->query($insert_user_p_history);
        }


        $get_order_data = "SELECT * FROM `store_orders_items` WHERE `order_id` = '" . $data['order_id'] . "'";
        $get_order_data_res = $this->_mysqli->query($get_order_data);
        while ($rows = $get_order_data_res->fetch_array(MYSQLI_ASSOC)) {

            $insert_into_checked_out = "INSERT INTO `checked_out` (order_id, transaction_id, sold_item_id, sold_item_qty, sold_item_size, "
                    . "sold_item_unit_price, sold_item_color, sold_date_time) VALUES "
                    . "("
                    . "'" . $auth_code . "',"
                    . "'" . $transacation_id . "', "
                    . "'" . $rows['sel_item_id'] . "', "
                    . "'" . $rows['sel_item_qty'] . "', "
                    . "'" . $rows['sel_item_size'] . "', "
                    . "'" . $rows['sel_item_price'] . "',"
                    . "'" . $rows['sel_item_color'] . "',"
                    . "'" . $order_date . "'"
                    . ")";
            $insert_into_checked_out_res = $this->_mysqli->query($insert_into_checked_out);
        }
        /*
         * Send Email to customer and retailer
         */
        $customer_rec = "";

        $delete_data_up_on_check_out = "DELETE FROM `store_orders_items` WHERE `order_id` = '" . $data['order_id'] . "'";
        $delete_data_up_on_check_out_result = $this->_mysqli->query($delete_data_up_on_check_out);
    }

    public function SendEmailToCustomers($to, $mail_subject, $message, $from) {
        $subject = $mail_subject;
        $headers = "MIME-Version: 1.0 \r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
        $headers .= "From: $from \r\n";
        $headers .= "Reply-To: $to \r\n";
        mail($to, $subject, $message, $headers);
    }

}
