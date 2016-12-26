<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SellsReports
 *
 * @author rostom
 */
class SellsReports {

    private $_mysqli;
    private $_db;
    public $checkded_out_items;
    public $item_detail;
    public $trans_detail;
    public $more_details;
    public $rows;
    public $shipping_info;
    public $flag = 0;
    public $messages = array();
    public $alert_class = "";

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function SellsReportsManager() {
        if (isset($_REQUEST['ship_u'])) {
            $this->UpdateShippingInfo($_REQUEST);
        }
        ?>

        <div class="panel-heading">
            <h5><strong><i class="fa fa-pie-chart" aria-hidden="true"></i>&nbsp;Sales Reports</strong></h5>
        </div>

        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-7 <?= $this->alert_class ?>">
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
            </div>
            <div class="col-md-12">
                <table class="table table-bordered table-hover table-striped">
                    <tr>

                        <th>Transaction Id</th>
                        <th>Trans Date</th>
                        <th>Status</th>
                        <th>Shipper</th>
                        <th>tracking code</th>
                    </tr>
                    <?php
                    $this->shipping_info = NULL;
                    $this->trans_detail = NULL;
                    $this->GetCheckOutDetailes();
                    if ($this->checkded_out_items != NULL) {
                        for ($j = 0; $j < COUNT($this->checkded_out_items); $j++) {
                            $this->GetDataFromStoreOrders($this->checkded_out_items[$j]['transaction_id']);

                            if ($this->GetShippingInfo($this->checkded_out_items[$j]['transaction_id']) != NULL) {
                                ?>
                                <tr>

                                    <td><a href="/public_html/rock_backend/?cmd=reports&ti=<?= $this->checkded_out_items[$j]['transaction_id'] ?>" class="my_popup_open" id="dialog_link_<?= $j ?>"><?= $this->checkded_out_items[$j]['transaction_id'] ?></a></td>
                                    <td><?= $this->trans_detail[$j]['order_date'] ?><input type="hidden" name="" value="<?= COUNT($this->checkded_out_items); ?>" id="find_me"/></td>
                                    <?php
//                               
                                    ?>
                                <form method="get">
                                    <td>
                                        <?php
                                        $stats = array("pending", "shipped", "canceled");
                                        ?>
                                        <select name="status">
                                            <option value="0">--Select--</option>
                                            <?php
                                            $st = isset($_REQUEST['status']) ? $_REQUEST['status'] : $this->shipping_info[$j]['status'];

                                            foreach ($stats as $stat) {
                                                if ($st == $stat && $_REQUEST['trans_i'] == $this->checkded_out_items[$j]['transaction_id']) {
                                                    $selected = 'selected="selected"';
                                                } else if ($this->shipping_info[$j]['status'] == $stat && $this->shipping_info[$j]['transaction_id'] == $this->checkded_out_items[$j]['transaction_id']) {
                                                    $selected = 'selected="selected"';
                                                } else {
                                                    $selected = '';
                                                }
                                                ?>
                                                <option value="<?= $stat ?>" <?= $selected ?>><?= $stat ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                        $shippers = array(
                                            "fed" => "FedEx",
                                            "ups" => "UPS",
                                            "usp" => "USPS",
                                            "dhl" => "DHL",
                                            "aramex" => "Aramex"
                                        );
                                        ?>
                                        <select name="shipper">
                                            <option value="0">--Select--</option>
                                            <?php
                                            $selected_shipper = '';
                                            $shipper_d = isset($_REQUEST['shipper']) ? $_REQUEST['shipper'] : $this->shipping_info[$j]['shipper'];
                                            foreach ($shippers as $shipper) {
                                                if ($shipper_d == $shipper && $_REQUEST['trans_i'] == $this->checkded_out_items[$j]['transaction_id']) {
                                                    $selected_shipper = 'selected="selected"';
                                                } else if ($this->shipping_info[$j]['shipper'] == $shipper && $this->shipping_info[$j]['transaction_id'] == $this->checkded_out_items[$j]['transaction_id']) {
                                                    $selected_shipper = 'selected="selected"';
                                                } else {
                                                    $selected_shipper = '';
                                                }
                                                ?>
                                                <option value="<?= $shipper ?>" <?= $selected_shipper ?>><?= $shipper ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="trans_i" value="<?= $this->checkded_out_items[$j]['transaction_id'] ?>"/>
                                        <input type="hidden" name="cmd" value="reports" />
                                        <?php
                                        $tr_num = "";
                                        if ($this->shipping_info[$j] != NULL) {
                                            $tr_num = isset($_REQUEST['tracking_number']) ? $_REQUEST['tracking_number'] : $this->shipping_info[$j]['tracking_number'];
                                            if (isset($_REQUEST['tracking_number'])) {
                                                if ($_REQUEST['trans_i'] == $this->checkded_out_items[$j]['transaction_id']) {
                                                    $tr_num = $_REQUEST['tracking_number'];
                                                } else {
                                                    $tr_num = $this->shipping_info[$j]['tracking_number'];
                                                }
                                            }
                                        }
                                        ?>
                                        <input type="text" name="tracking_number" width="70%"  value="<?= $tr_num ?>" id="tn" placeholder="23232308891"/>
                                        <input type="submit" class="btn btn-primary btn-xs" value="update" name="ship_u"/>
                                    </td>
                                </form>

                                <?php ?>


                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>

                </table>
            </div>




        </div>
        <!---In Detail-->
        <?php
        if (isset($_REQUEST['ti'])) {
            $this->trans_detail = NULL;
            $this->GetDataFromStoreOrders($_REQUEST['ti']);
            foreach ($this->trans_detail as $trans_detail) {
                ?>
                <input type="hidden" name="order_name" value="<?= $trans_detail['order_name'] ?>" id="order_name"/>
                <input type="hidden" name="order_address_1" value="<?= $trans_detail['order_address_1'] ?>" id="address_1"/>
                <input type="hidden" name="order_address_2" value="<?= $trans_detail['order_address_2'] ?>" id="address_2"/>
                <input type="hidden" name="order_city" value="<?= $trans_detail['order_city'] ?>" id="city"/>
                <input type="hidden" name="order_state" value="<?= $trans_detail['order_state'] ?>" id="state"/>
                <input type="hidden" name="order_zip" value="<?= $trans_detail['order_zip'] ?>" id="zip"/>
                <input type="hidden" name="order_country" value="<?= $trans_detail['order_country'] ?>" id="country"/>
                <input type="hidden" name="order_email" value="<?= $trans_detail['order_email'] ?>" id="email"/>
                <input type="hidden" name="item_total" value="<?= $trans_detail['item_total'] ?>" id="item_total"/>
                <input type="hidden" name="auth" value="<?= $trans_detail['authorization'] ?>" id="auth"/>
                <input type="hidden" name="transaction_id" value="<?= $trans_detail['transaction_id'] ?>" id="transaction"/>
                <input type="hidden" name="shipping_inst" value="<?= $trans_detail['shipping_instructions'] ?>" id="shipping_inst"/>



                <?php
            }
            $this->more_details = NULL;
            $this->GetallCheckoutdetail($_REQUEST['ti']);
            ?>
            <input type="hidden" name="count" value="<?= COUNT($this->more_details) ?>" id="count_each"/>
            <?php
            for ($x = 0; $x < COUNT($this->more_details); $x++) {
                ?>
                <input type="hidden" name="sold_item_id" value="<?= $this->more_details[$x]['sold_item_id'] ?>" id="sold_item_id_<?= $x ?>"/>
                <input type="hidden" name="sold_item_qty" value="<?= $this->more_details[$x]['sold_item_qty'] ?>" id="sold_item_qty_<?= $x ?>"/>
                <input type="hidden" name="sold_item_size" value="<?= $this->more_details[$x]['sold_item_size'] ?>" id="sold_item_size_<?= $x ?>"/>
                <input type="hidden" name="sold_item_unit_price" value="<?= $this->more_details[$x]['sold_item_unit_price'] ?>" id="unit_price_<?= $x ?>"/>
                <input type="hidden" name="sold_item_color" value="<?= $this->more_details[$x]['sold_item_color'] ?>" id="color_<?= $x ?>"/>
                <input type="hidden" name="" value="" id=""/>

                <?php
                $this->item_detail = NULL;
                $this->GetProductDetails($this->more_details[$x]['sold_item_id']);
                foreach ($this->item_detail as $item) {
                    ?>
                    <input type="hidden" name="item_name" value="<?= $item['item_name'] ?>" id="sold_item_name_<?= $x ?>"/>

                    <?php
                }
            }
        }
        ?>
        <!-- Add content to the popup -->
        <div id="dialog">
            <table class="table table-bordered table-hover rock-sell-table">

            </table>
            <table class="table table-bordered table-hover rock-sell-table-info">

            </table>
        </div>
        <script>

            $(document).ready(function () {
                var num = $("#num_trans").val();
                // Initialize the plugin
                var i = $('#count_each').val();
                var c;
                var buyer_name = $("#order_name").val();
                var buyer_email = $("#email").val();
                var buyer_address_1 = $("#address_1").val();
                var buyer_address_2 = $("#address_2").val();
                var city = $("#city").val();
                var state = $("#state").val();
                var zip = $("#zip").val();
                var country = $("#country").val();
                var item_total = $("#item_total").val();
                var authorization = $("#auth").val();
                var shipping_inst = $("#shipping_inst").val();
                for (c = 0; c < i; c++) {
                    var transaction = $('#transaction').val();
                    var size = $("#sold_item_size_" + c).val();
                    var qty = $("#sold_item_qty_" + c).val();
                    var unit_price = $("#unit_price_" + c).val();
                    var color = $("#color_" + c).val();
                    var item_name = $('#sold_item_name_' + c).val();

                    $(".rock-sell-table").append("<tr><th>Item Name:</th><td>" + item_name + "</td></tr>\n\
                <tr><th>Purchase Qty:</th><td>" + qty + "</td></tr>\n\
        <tr><th>Item Size:</th><td>" + size + "</td></tr>\n\
        <tr><th>Unit Price:</th><td>$ " + unit_price + "</td></tr>\n\
        <tr><th>Color:</th><td>" + color + "</td></tr><tr><td style='background-color:#F5F5F5;'></td><td style='background-color:#F5F5F5;'></td></tr>\n\
                ");

                    var q = window.location.search;
                    var get_trans = q.substr(16);
                    if (get_trans === transaction) {


                        $('#dialog').dialog({autoOpen: false})
                        $(document).ready(function () {
                            $("#dialog").dialog({
                                title: 'Transaction# ' + transaction,
                                modal: false,
                                minWidth: 600,
                                top: 75,
                            });
                            $(".rock-sell-table-info").show();
                            $('#dialog').dialog('open');


                        });


                    }
                }

                $(".ui-dialog-titlebar-close").append('<span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text"></span>');
                $(".ui-widget-overlay ui-front").css('z-index', '10000');
                $(".ui-dialog").css('z-index', '10001');
                $(".ui-dialog-titlebar-close").css('width', '24px');
                $(".ui-dialog-titlebar-close").css('height', '24px');
                $(".rock-sell-table-info").hide();
                $(".rock-sell-table-info").append('<tr><th>Authorization Code:</th><td>' + authorization + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>Customer Name:</th><td>' + buyer_name + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>Customer Email:</th><td>' + buyer_email + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>Customer Address 1:</th><td>' + buyer_address_1 + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>Customer Address 2:</th><td>' + buyer_address_2 + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>City:</th><td>' + city + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>State:</th><td>' + state + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>Zip code/postal code:</th><td>' + zip + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>Country:</th><td>' + country + '</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>Sale Total:</th><td>' + item_total + ' (USD)</td></tr>');
                $(".rock-sell-table-info").append('<tr><th>Shipping Instructions:</th><td>' + shipping_inst + '</td></tr>');








            });


        </script>

        <?php
    }

    public function GetCheckOutDetailes() {
        $sql = "SELECT * FROM `store_orders`";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->checkded_out_items[] = $row;
            }
            return $this->checkded_out_items;
        }
    }

    public function GetProductDetails($data) {
        $sql = "SELECT `item_name`, `image_0` FROM  `pages_products` WHERE `page_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->item_detail[] = $row;
            }
            return $this->item_detail;
        }
    }

    public function GetDataFromStoreOrders($data) {

        $sql = "SELECT * FROM `store_orders` WHERE `transaction_id` = '" . $data . "' ";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->trans_detail[] = $row;
            }
            return $this->trans_detail;
        }
    }

    public function GetallCheckoutdetail($data) {
        $sql = "SELECT * FROM `checked_out` WHERE `transaction_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->more_details[] = $row;
            }
            return $this->more_details;
        }
    }

    public function GetNumRows() {
        $sql = "SELECT COUNT(id) as num FROM `checked_out`";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

            $this->rows = $row['num'];
        }
        return $this->rows;
    }

    public function GetShippingInfo($data) {

        $sql = "SELECT * FROM `shippingInfo` WHERE `transaction_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->shipping_info[] = $row;
            }
            return $this->shipping_info;
        } else {
            $this->shipping_info[] = NULL;
            return $this->shipping_info;
        }
    }

    public function UpdateShippingInfo($data) {
        if (empty($data['tracking_number']) && $data['shipper'] == "0" && $data['status'] == "0") {
            $this->flag = 1;
            $message = array("1" => "All required fields are either empty or not selected");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else if (empty($data['tracking_number']) || $data['shipper'] == "0" || $data['status'] == "0") {
            $this->flag = 1;
            $message = array("1" => "One or more required fields are either empty or not selected");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else {
            $date = date('m/d/y');
            $sql = "SELECT * FROM `shippingInfo` WHERE `transaction_id` = '" . $data['trans_i'] . "'";
            $result = $this->_mysqli->query($sql);
            if ($result->num_rows > 0) {
                /*
                 * Update
                 */
                $update = "UPDATE `shippingInfo` SET `status` = '" . $data['status'] . "', `shipper` = '" . $data['shipper'] . "', `tracking_number` = '" . $data['tracking_number'] . "' WHERE `transaction_id` = '" . $data['trans_i'] . "' ";
                $update_res = $this->_mysqli->query($update);
                if ($update_res) {
                    switch ($data['shipper']) {
                        case "FedEx":
                            $website = "https://www.fedex.com/apps/fedextrack/?tracknumbers=" . $data['tracking_number'];
                            break;
                        case "UPS":
                            $website = "https://www.ups.com/";
                            break;
                        case "USPS":
                            $website = "https://tools.usps.com/go/TrackConfirmAction.action?tRef=fullpage&tLc=1&text28777=&tLabels=" . $data['tracking_number'];
                            break;
                        case "Aramex":
                            $website = "https://www.aramex.com/track-results-multiple.aspx?ShipmentNumber=" . $data['tracking_number'];
                            break;
                    }


                    /*
                     * Send email to customer about the information
                     */
                    $this->trans_detail = NULL;
                    $this->GetDataFromStoreOrders($data['trans_i']);
                    foreach ($this->trans_detail as $details) {

                        switch ($data['status']) {
                            case "pending":
                                $part_message = 'Your shipment is being prepared to be shipped via ' . $data['shipper'] . '. Below are the details.<br/>			  <br/>
											  <h4>Track your shipment</h4>
											  <table style="border:1px solid #000" class="shipping_info">
											  		 <tbody>
											  		 		<tr >
											  	  				<td>Tracking Number:</td>
																<td>' . $data['tracking_number'] . '</td>
											  	  			</tr>
															 <tr >
											  	  				<td>Transaction Number:</td>
																<td>' . $data['trans_i'] . '</td>
											  	  			</tr>
															<tr >
											  	  				<td>Shipping Date:</td>
																<td>' . $date . '</td>
											  	  			</tr>
		    												 <tr >
											  	  				<td>Website to track:</td>
																<td><a href="' . $website . '">Go To ' . $data['shipper'] . ' site.</a></td>
											  	  			</tr>
											  		 </tbody>
											  </table> 
											  <br/>
											  <h4>Order Details</h4>
											  <table style="border:1px solid #000" class="shipping_info">
											  		 <tbody>
											  		 		<tr >
											  	  				<td>Shipped to:</td>
																<td>' . $details['order_name'] . '</td>
											  	  			</tr>
															 <tr >
											  	  				<td>Ship to:</td>
																<td>' . $details['order_address_1'] . '&nbsp;' . $details['order_address_2'] . '<br/>
                                                                                                                                ' . $details['order_city'] . ', &nbsp; ' . $details['order_state'] . '&nbsp; ' . $details['order_zip'] . ', &nbsp; ' . $details['order_country'] . '    
                                                                                                                                </td>
											  	  			</tr>
															<tr >
											  	  				<td>Customer Email:</td>
																<td>' . $details['order_email'] . '</td>
											  	  			</tr>
		    												 <tr >
											  	  				<td>Order total:</td>
																<td>' . $details['item_total'] . ' (USD)</td>
											  	  			</tr>
	         												 <tr >
											  	  				<td>Transaction ID#:</td>
																<td>' . $details['transaction_id'] . '</td>
											  	  			</tr>
															<tr >
											  	  				<td>Authorization Code:</td>
																<td>' . $details['authorization'] . '</td>
											  	  			</tr>
											  		 </tbody>
											  </table> ';
                                $this->flag = 1;
                                $message = array("1" => "Your have just updated shipment information to status pending. Your customer will receive an update email in regards to his/her order. Please make sure you change the status to shipped once the item is shipped!!");
                                array_push($this->messages, $message);
                                break;
                            case "shipped":
                                $part_message = 'We have shipped your order via ' . $data['shipper'] . '. Below are the details.<br/>			  <br/>
											  <h4>Track your shipment</h4>
											  <table style="border:1px solid #000" class="shipping_info">
											  		 <tbody>
											  		 		<tr >
											  	  				<td>Tracking Number:</td>
																<td>' . $data['tracking_number'] . '</td>
											  	  			</tr>
															 <tr >
											  	  				<td>Transaction Number:</td>
																<td>' . $data['trans_i'] . '</td>
											  	  			</tr>
															<tr >
											  	  				<td>Shipping Date:</td>
																<td>' . $date . '</td>
											  	  			</tr>
		    												 <tr >
											  	  				<td>Website to track:</td>
																<td><a href="' . $website . '">Go To ' . $data['shipper'] . ' site.</a></td>
											  	  			</tr>
											  		 </tbody>
											  </table> 
											  <br/>
											  <h4>Order Details</h4>
											  <table style="border:1px solid #000" class="shipping_info">
											  		 <tbody>
											  		 		<tr >
											  	  				<td>Shipped to:</td>
																<td>' . $details['order_name'] . '</td>
											  	  			</tr>
															 <tr >
											  	  				<td>Ship to:</td>
																<td>' . $details['order_address_1'] . '&nbsp;' . $details['order_address_2'] . '<br/>
                                                                                                                                ' . $details['order_city'] . ', &nbsp; ' . $details['order_state'] . '&nbsp; ' . $details['order_zip'] . ', &nbsp; ' . $details['order_country'] . '    
                                                                                                                                </td>
											  	  			</tr>
															<tr >
											  	  				<td>Customer Email:</td>
																<td>' . $details['order_email'] . '</td>
											  	  			</tr>
		    												 <tr >
											  	  				<td>Order total:</td>
																<td>' . $details['item_total'] . ' (USD)</td>
											  	  			</tr>
	         												 <tr >
											  	  				<td>Transaction ID#:</td>
																<td>' . $details['transaction_id'] . '</td>
											  	  			</tr>
															<tr >
											  	  				<td>Authorization Code:</td>
																<td>' . $details['authorization'] . '</td>
											  	  			</tr>
											  		 </tbody>
											  </table> ';
                                $this->flag = 1;
                                $message = array("1" => "Your have just updated shipment information to shipped. Your customer will receive an update email in regards to his/her order.");
                                array_push($this->messages, $message);
                                break;
                            case "canceled":
                                $part_message = 'Your order has been canceled. Please contact our customer service for more information.<br/>';
                                $this->flag = 1;
                                $message = array("1" => "Your have just notified customer that his/her order has been canceled. If you have an more information to pass on to the customer please email them.");
                                array_push($this->messages, $message);
                                break;
                        }



                        $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>Underscore-Responsive Email Template</title>
      <style type="text/css">
         /* Client-specific Styles */
         #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
         body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; background-color:#dbdbdb;}
         /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
         .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing. */
         #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
         img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
         a img {border:none;}
         .image_fix {display:block;}
         p {margin: 0px 0px !important;}
         table td {border-collapse: collapse;}
         table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
         a {color: #33b9ff;text-decoration: none;text-decoration:none!important;}
         /*STYLES*/
         table[class=full] { width: 100%; clear: both; }
         /*IPAD STYLES*/
         @media only screen and (max-width: 640px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 440px!important;text-align:center!important;}
         table[class=devicewidthmob] {width: 420px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
         img[class=banner] {width: 440px!important;height:157px!important;}
         img[class=col2img] {width: 440px!important;height:330px!important;}
         table[class="cols3inner"] {width: 100px!important;}
         table[class="col3img"] {width: 131px!important;}
         img[class="col3img"] {width: 131px!important;height: 82px!important;}
         table[class="removeMobile"]{width:10px!important;}
         img[class="blog"] {width: 420px!important;height: 162px!important;}
         }

         /*IPHONE STYLES*/
         @media only screen and (max-width: 480px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important; 
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 280px!important;text-align:center!important;}
         table[class=devicewidthmob] {width: 260px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
         img[class=banner] {width: 280px!important;height:100px!important;}
         img[class=col2img] {width: 280px!important;height:210px!important;}
         table[class="cols3inner"] {width: 260px!important;}
         img[class="col3img"] {width: 280px!important;height: 175px!important;}
         table[class="col3img"] {width: 280px!important;}
         img[class="blog"] {width: 260px!important;height: 100px!important;}
         td[class="padding-top-right15"]{padding:15px 15px 0 0 !important;}
         td[class="padding-right15"]{padding-right:15px !important;}
         }
		 .shipping_info td{
		 border:1px solid #000 !important;
		 padding:5px !important;
		  }
      </style>
   </head>
   <body>
  <!-- Start of preheader -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader" >
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 10px;color: #303030;text-align:center;" st-content="viewonline">
                                    If you can’t read this email.Please 
                                    <a href="#" style="text-decoration: none; color: #7a6e67">view online</a> 
                                 </td>
                                 <!-- Spacing -->
                              </tr>
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of preheader -->      

<!-- fulltext -->
<table width="100%"  cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="left-image">
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="520" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidthinner">
                                       <tbody>
    
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="15" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- /Spacing -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 18px; color: #2d2a26; text-align:left; line-height: 24px;">
                    Dear ' . $details['order_name'] . ',
                                             </td>
                                          </tr>
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="15" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- /Spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #7a6e67; text-align:left; line-height: 24px;">
                                              Thank you for your order. We have an update in regards to your purchase. Please review the message below.<br />
                                             
                                              
											 ' . $part_message . '
								
                                             </td>
                                          </tr>

                                          <!-- end of content -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <!-- Spacing -->
                              <tr>
                                 <td height="5"  style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of fulltext -->
<!-- Start of footer -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="footer">
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#00000" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <!-- logo -->
                                    <table width="194" align="left" border="0" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td width="20"></td>
                                             <td width="174" height="40" align="left">
                                                <div class="imgpop">
                                                   <a target="_blank" href="<[customer website]>">
												   <!--Logo off the customer-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/theline_logo/logo_1.png" alt="" border="0" width="80%" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of logo -->
                                    <!-- start of social icons -->
                                    <table width="60" height="40" align="right" vaalign="middle"  border="0" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td width="22" height="22" align="left">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
												   <!--Facebook logo-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/social_media/faceboo_color.png" alt="" border="0" width="22" height="22" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                             <td align="left" width="10" style="font-size:1px; line-height:1px;">&nbsp;</td>
                                             <td width="22" height="22" align="right">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
												   <!-- Twitter-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/social_media/twitter_color.png" alt="" border="0" width="22" height="22" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>

                                             <td align="left" width="20" style="font-size:1px; line-height:1px;">&nbsp;</td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of social icons -->
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of footer -->
<!-- Start of postfooter -->
<table width="100%" bgcolor="#dbdbdb" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader" >
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #7a6e67;text-align:center;" st-content="viewonline">
                                    If you wish not to receive further updates.Please 
                                    <a href="#" style="text-decoration: none; color: #303030">Unsubscribe</a> 
                                 </td>
                              </tr>
                                 <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of postfooter -->

   </body>
   </html>
';
                        $this->SendEmailToCustomers($details['order_email'], "Shipping information", $message, CUSTOMER_EMAIL);
                    }
                }
            } else {
                /*
                 * Insert new data into the table
                 */
                $insert = "INSERT INTO `shippingInfo` (transaction_id, status, shipper, tracking_number, date) VALUES"
                        . " ( "
                        . "'" . $data['trans_i'] . "', "
                        . "'" . $data['status'] . "', "
                        . "'" . $data['shipper'] . "', "
                        . "'" . $data['tracking_number'] . "', "
                        . "'" . $date . "'"
                        . " )";
                $insert_res = $this->_mysqli->query($insert);
                if ($insert) {

                    switch ($data['shipper']) {
                        case "FedEx":
                            $website = "https://www.fedex.com/apps/fedextrack/?tracknumbers=" . $data['tracking_number'];
                            break;
                        case "UPS":
                            $website = "https://www.ups.com/";
                            break;
                        case "USPS":
                            $website = "https://tools.usps.com/go/TrackConfirmAction.action?tRef=fullpage&tLc=1&text28777=&tLabels=" . $data['tracking_number'];
                            break;
                        case "Aramex":
                            $website = "https://www.aramex.com/track-results-multiple.aspx?ShipmentNumber=" . $data['tracking_number'];
                            break;
                    }


                    /*
                     * Send email to customer about the information
                     */
                    $this->trans_detail = NULL;
                    $this->GetDataFromStoreOrders($data['trans_i']);
                    foreach ($this->trans_detail as $details) {

                        switch ($data['status']) {
                            case "pending":
                                $part_message = 'Your shipment is being prepared to be shipped via ' . $data['shipper'] . '. Below are the details.<br/>			  <br/>
											  <h4>Track your shipment</h4>
											  <table style="border:1px solid #000" class="shipping_info">
											  		 <tbody>
											  		 		<tr >
											  	  				<td>Tracking Number:</td>
																<td>' . $data['tracking_number'] . '</td>
											  	  			</tr>
															 <tr >
											  	  				<td>Transaction Number:</td>
																<td>' . $data['trans_i'] . '</td>
											  	  			</tr>
															<tr >
											  	  				<td>Shipping Date:</td>
																<td>' . $date . '</td>
											  	  			</tr>
		    												 <tr >
											  	  				<td>Website to track:</td>
																<td><a href="' . $website . '">Go To ' . $data['shipper'] . ' site.</a></td>
											  	  			</tr>
											  		 </tbody>
											  </table> 
											  <br/>
											  <h4>Order Details</h4>
											  <table style="border:1px solid #000" class="shipping_info">
											  		 <tbody>
											  		 		<tr >
											  	  				<td>Shipped to:</td>
																<td>' . $details['order_name'] . '</td>
											  	  			</tr>
															 <tr >
											  	  				<td>Ship to:</td>
																<td>' . $details['order_address_1'] . '&nbsp;' . $details['order_address_2'] . '<br/>
                                                                                                                                ' . $details['order_city'] . ', &nbsp; ' . $details['order_state'] . '&nbsp; ' . $details['order_zip'] . ', &nbsp; ' . $details['order_country'] . '    
                                                                                                                                </td>
											  	  			</tr>
															<tr >
											  	  				<td>Customer Email:</td>
																<td>' . $details['order_email'] . '</td>
											  	  			</tr>
		    												 <tr >
											  	  				<td>Order total:</td>
																<td>' . $details['item_total'] . ' (USD)</td>
											  	  			</tr>
	         												 <tr >
											  	  				<td>Transaction ID#:</td>
																<td>' . $details['transaction_id'] . '</td>
											  	  			</tr>
															<tr >
											  	  				<td>Authorization Code:</td>
																<td>' . $details['authorization'] . '</td>
											  	  			</tr>
											  		 </tbody>
											  </table> ';
                                $this->flag = 1;
                                $message = array("1" => "Your have just updated shipment information to status pending. Your customer will receive an update email in regards to his/her order. Please make sure you change the status to shipped once the item is shipped!!");
                                array_push($this->messages, $message);
                                break;
                            case "shipped":
                                $part_message = 'We have shipped your order via ' . $data['shipper'] . '. Below are the details.<br/>			  <br/>
											  <h4>Track your shipment</h4>
											  <table style="border:1px solid #000" class="shipping_info">
											  		 <tbody>
											  		 		<tr >
											  	  				<td>Tracking Number:</td>
																<td>' . $data['tracking_number'] . '</td>
											  	  			</tr>
															 <tr >
											  	  				<td>Transaction Number:</td>
																<td>' . $data['trans_i'] . '</td>
											  	  			</tr>
															<tr >
											  	  				<td>Shipping Date:</td>
																<td>' . $date . '</td>
											  	  			</tr>
		    												 <tr >
											  	  				<td>Website to track:</td>
																<td><a href="' . $website . '">Go To ' . $data['shipper'] . ' site.</a></td>
											  	  			</tr>
											  		 </tbody>
											  </table> 
											  <br/>
											  <h4>Order Details</h4>
											  <table style="border:1px solid #000" class="shipping_info">
											  		 <tbody>
											  		 		<tr >
											  	  				<td>Shipped to:</td>
																<td>' . $details['order_name'] . '</td>
											  	  			</tr>
															 <tr >
											  	  				<td>Ship to:</td>
																<td>' . $details['order_address_1'] . '&nbsp;' . $details['order_address_2'] . '<br/>
                                                                                                                                ' . $details['order_city'] . ', &nbsp; ' . $details['order_state'] . '&nbsp; ' . $details['order_zip'] . ', &nbsp; ' . $details['order_country'] . '    
                                                                                                                                </td>
											  	  			</tr>
															<tr >
											  	  				<td>Customer Email:</td>
																<td>' . $details['order_email'] . '</td>
											  	  			</tr>
		    												 <tr >
											  	  				<td>Order total:</td>
																<td>' . $details['item_total'] . ' (USD)</td>
											  	  			</tr>
	         												 <tr >
											  	  				<td>Transaction ID#:</td>
																<td>' . $details['transaction_id'] . '</td>
											  	  			</tr>
															<tr >
											  	  				<td>Authorization Code:</td>
																<td>' . $details['authorization'] . '</td>
											  	  			</tr>
											  		 </tbody>
											  </table> ';
                                $this->flag = 1;
                                $message = array("1" => "Your have just updated shipment information to shipped. Your customer will receive an update email in regards to his/her order.");
                                array_push($this->messages, $message);
                                break;
                            case "canceled":
                                $part_message = 'Your order has been canceled. Please contact our customer service for more information.<br/>';
                                $this->flag = 1;
                                $message = array("1" => "Your have just notified customer that his/her order has been canceled. If you have an more information to pass on to the customer please email them.");
                                array_push($this->messages, $message);
                                break;
                        }



                        $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>Underscore-Responsive Email Template</title>
      <style type="text/css">
         /* Client-specific Styles */
         #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
         body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; background-color:#dbdbdb;}
         /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
         .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing. */
         #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
         img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
         a img {border:none;}
         .image_fix {display:block;}
         p {margin: 0px 0px !important;}
         table td {border-collapse: collapse;}
         table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
         a {color: #33b9ff;text-decoration: none;text-decoration:none!important;}
         /*STYLES*/
         table[class=full] { width: 100%; clear: both; }
         /*IPAD STYLES*/
         @media only screen and (max-width: 640px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 440px!important;text-align:center!important;}
         table[class=devicewidthmob] {width: 420px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
         img[class=banner] {width: 440px!important;height:157px!important;}
         img[class=col2img] {width: 440px!important;height:330px!important;}
         table[class="cols3inner"] {width: 100px!important;}
         table[class="col3img"] {width: 131px!important;}
         img[class="col3img"] {width: 131px!important;height: 82px!important;}
         table[class="removeMobile"]{width:10px!important;}
         img[class="blog"] {width: 420px!important;height: 162px!important;}
         }

         /*IPHONE STYLES*/
         @media only screen and (max-width: 480px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important; 
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 280px!important;text-align:center!important;}
         table[class=devicewidthmob] {width: 260px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
         img[class=banner] {width: 280px!important;height:100px!important;}
         img[class=col2img] {width: 280px!important;height:210px!important;}
         table[class="cols3inner"] {width: 260px!important;}
         img[class="col3img"] {width: 280px!important;height: 175px!important;}
         table[class="col3img"] {width: 280px!important;}
         img[class="blog"] {width: 260px!important;height: 100px!important;}
         td[class="padding-top-right15"]{padding:15px 15px 0 0 !important;}
         td[class="padding-right15"]{padding-right:15px !important;}
         }
		 .shipping_info td{
		 border:1px solid #000 !important;
		 padding:5px !important;
		  }
      </style>
   </head>
   <body>
  <!-- Start of preheader -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader" >
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 10px;color: #303030;text-align:center;" st-content="viewonline">
                                    If you can’t read this email.Please 
                                    <a href="#" style="text-decoration: none; color: #7a6e67">view online</a> 
                                 </td>
                                 <!-- Spacing -->
                              </tr>
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of preheader -->      

<!-- fulltext -->
<table width="100%"  cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="left-image">
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="520" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidthinner">
                                       <tbody>
    
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="15" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- /Spacing -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 18px; color: #2d2a26; text-align:left; line-height: 24px;">
                    Dear ' . $details['order_name'] . ',
                                             </td>
                                          </tr>
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="15" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- /Spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #7a6e67; text-align:left; line-height: 24px;">
                                              Thank you for your order. We have an update in regards to your purchase. Please review the message below.<br />
                                             
                                              
											 ' . $part_message . '
								
                                             </td>
                                          </tr>

                                          <!-- end of content -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <!-- Spacing -->
                              <tr>
                                 <td height="5"  style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of fulltext -->
<!-- Start of footer -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="footer">
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#00000" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <!-- logo -->
                                    <table width="194" align="left" border="0" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td width="20"></td>
                                             <td width="174" height="40" align="left">
                                                <div class="imgpop">
                                                   <a target="_blank" href="<[customer website]>">
												   <!--Logo off the customer-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/theline_logo/logo_1.png" alt="" border="0" width="80%" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of logo -->
                                    <!-- start of social icons -->
                                    <table width="60" height="40" align="right" vaalign="middle"  border="0" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td width="22" height="22" align="left">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
												   <!--Facebook logo-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/social_media/faceboo_color.png" alt="" border="0" width="22" height="22" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                             <td align="left" width="10" style="font-size:1px; line-height:1px;">&nbsp;</td>
                                             <td width="22" height="22" align="right">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
												   <!-- Twitter-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/social_media/twitter_color.png" alt="" border="0" width="22" height="22" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>

                                             <td align="left" width="20" style="font-size:1px; line-height:1px;">&nbsp;</td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of social icons -->
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of footer -->
<!-- Start of postfooter -->
<table width="100%" bgcolor="#dbdbdb" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader" >
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #7a6e67;text-align:center;" st-content="viewonline">
                                    If you wish not to receive further updates.Please 
                                    <a href="#" style="text-decoration: none; color: #303030">Unsubscribe</a> 
                                 </td>
                              </tr>
                                 <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of postfooter -->

   </body>
   </html>
';
                        $this->SendEmailToCustomers($details['order_email'], "Shipping information", $message, CUSTOMER_EMAIL);
                    }
                }
            }
        }
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
