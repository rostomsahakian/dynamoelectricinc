<?php

/**
 * Description of ShoppingCartFunc
 *
 * @author rostom
 */
class ShoppingCartFunc {

    private $_mysqli;
    private $_db;
    public $_order_session;
    public $items_in_cart = 0;
    public $num_items = 0;
    public $flag = 0;
    public $message;
    public $shopping_cart_items;
    public $products;
    public $total_rows;
    public $old_val;
    public $val;
    public $data_products = array();
    public $get_vals;
    public $to_continue;
    public $total_item_price;
    public $sel_tax;
    public $sel_grand_price;
    public $sel_item_qty;
    public $sel_item_name;
    public $sel_item_price;
    public $item_id;

    public function __construct() {

        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->_order_session = uniqid();
        if (isset($_COOKIE['order'])) {
            $this->GetNumberOfItemsInCart($_COOKIE['order']);
            $this->num_items = $this->ReturnNumProductsInCart();
        }
    }

    public function ShoppingCartProccess($data) {

        $qty_selected = $data['qty'];
        $price = $data['h_price'];
        $size = $data['size'];
        $model_number = $data['item_model_number'];
        $item_category = $data['item_category'];
        $item_page_id = $data['item_page_id'];
        $item_page_parent = $data['item_parent_id'];
        $date_added = date('m/d/y');
        if (array_key_exists("item_color", $data)) {
            $item_color = $data['item_color'];
        } else {
            $item_color = "n/a";
        }


        /*
         * First we will add the temp data into store_orders_items
         * We will keep track by session
         * 
         */
        $check_product = "SELECT `order_id`,`sel_item_qty`,`sel_item_id` FROM `store_orders_items` WHERE `sel_item_id` = '" . $item_page_id . "' AND `sel_item_size` = '" . $size . "' AND `order_id` ='" . $_COOKIE['order'] . "'";
        $check_product_result = $this->_mysqli->query($check_product);
        $num_product_rows = $check_product_result->num_rows;
        if ($num_product_rows > 0) {
            while ($cart_products = $check_product_result->fetch_array(MYSQLI_ASSOC)) {
                /*
                 * Just Update the quantity
                 */
                $updated_quantity = $qty_selected + $cart_products['sel_item_qty'];
                $update_q = "UPDATE `store_orders_items` SET `sel_item_qty` = '" . $updated_quantity . "' , `old_qty` = '" . $updated_quantity . "' WHERE `sel_item_id` = '" . $item_page_id . "' AND `sel_item_size` = '" . $size . "'  AND `order_id` ='" . $_COOKIE['order'] . "'";
                $update_q_res = $this->_mysqli->query($update_q);
                if ($update_q) {
                    $this->flag = 1;
                    $this->message = array("1" => "Cart was Updated!");
                }
            }
        } else {

            $insert = "INSERT INTO `store_orders_items`"
                    . " (order_id, sel_item_id, sel_item_name, sel_item_qty, old_qty, sel_item_size, sel_item_color, sel_item_price, date_added)"
                    . " VALUES "
                    . "("
                    . "'" . $_COOKIE['order'] . "', "
                    . "'" . $item_page_id . "', "
                    . "'" . $data['item_name'] . "', "
                    . "'" . $qty_selected . "', "
                    . "'" . $qty_selected . "', "
                    . "'" . $size . "', "
                    . "'" . $item_color . "', "
                    . "'" . $price . "', "
                    . "'" . $date_added . "'"
                    . " )";

            $insert_result = $this->_mysqli->query($insert);
            if ($insert) {
                $this->flag = 1;
                $this->message = array("1" => "New Items were added to your cart.");
            }
        }
        /*
         * Second we need to update the quanitiy
         */

        $get_quantity = "SELECT * FROM `pages_products` WHERE `page_id` ='" . $item_page_id . "'";

        $get_quantity_results = $this->_mysqli->query($get_quantity);
        while ($q = $get_quantity_results->fetch_array(MYSQLI_ASSOC)) {

            if (strpos($q['size'], ",")) {

                $variation = explode(",", trim($q['size']));
                if (isset($_SESSION['wholesaler_on']) && !empty($_SESSION['wholesaler_on'])) {
                    $item_q = explode(";", $q['wholesale_qty_on_hand']);
                    $fe = 'wholesale_qty_on_hand';
                } else {
                    $item_q = explode(";", $q['qty']);
                    $fe = 'qty';
                }

                for ($i = 0; $i < count($variation); $i++) {
                    if ($variation[$i] == $size) {

                        $item_q[$i] = $item_q[$i] - $qty_selected;

                        $new_qty = implode(";", $item_q);
                    }
                }
            } else {

                $new_qty = abs($q['qty'] - $qty_selected);
            }

            $update_qty = "UPDATE `pages_products` SET `".$fe."` ='" . $new_qty . "'  WHERE `page_id` = '" . $item_page_id . "'";

            $update_result = $this->_mysqli->query($update_qty);
        }
    }

    public function GetNumberOfItemsInCart($seesion_id) {

        $sql = "SELECT `id` FROM `store_orders_items` WHERE `order_id` = '" . $seesion_id . "'";
        $result = $this->_mysqli->query($sql);
        $num_products = $result->num_rows;
        $this->items_in_cart = $num_products;
    }

    public function ReturnNumProductsInCart() {
        return $this->items_in_cart;
    }

    public function ShoppingCartForm($data) {
        $this->items_in_cart = NULL;
        $this->GetNumberOfItemsInCart($_COOKIE['order']);

        $loop = $this->ReturnNumProductsInCart();

        if (isset($_REQUEST['update_qty'])) {
            $this->UpdateQtyProducts($_POST);
        }
        if (isset($_REQUEST['delete_item'])) {
            $this->DeleteItemFromCart($_REQUEST);
        }
        ?>
        <input type="hidden" name="loop" value="<?= $loop; ?>" id="loop"/>
        <div class="container rock-main-container">

            <h4 style="text-transform: uppercase;"><?= $data['page_name']; ?></h4>
            <div class="row">
                <div class="col-md-12">
                    <!--table-->
                    <div class="col-md-8">
                        <table class="table rock-cart-table">
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Price</th>
                                <th> Quantity</th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php
                            if (isset($_COOKIE['order'])) {
                                $this->GetAllProductsForCart($_COOKIE['order']);
                                $k = 0;
                                if ($this->shopping_cart_items != NULL) {
                                    $to_continue = array();
                                    $this->item_id = array();
                                    foreach ($this->shopping_cart_items as $items) {
                                        $this->products = NULL;

                                        $this->GetDataFromProductpage($items['sel_item_id']);
                                        foreach ($this->products as $product) {

                                            if (isset($_REQUEST['c_shop'])) {
                                                $no_spaces = str_replace(" ", "-", trim($product['item_name']));
                                                $item_no_upper = strtolower($no_spaces);
                                                $item_no_ands = str_replace("&", "and", $item_no_upper);
                                                $clean_item_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $item_no_ands);
                                                header("Location: /" . $clean_item_name . "/" . $items['sel_item_id']);
                                            }
                                            ?>
                                            <tr>
                                            <form method="post">
                                                <td class="rock-cart-image-td"><img src="<?= $product['image_0'] ?>" /></td>


                                                <td>
                                                    <h3><?= $product['item_name'] ?></h3> by <?= $product['brand'] ?>
                                                    <p>Size: <?= $items['sel_item_size'] ?></p>
                                                    <p>Shipped from: <?= DEFAULT_SHIPPED_FROM ?></p>
                                                </td>
                                                <td>$<?= $items['sel_item_price'] ?></td>

                                                <td>
                                                    <?php ?>

                                                    <input type="hidden" name="item_id" value="<?= $items['id'] ?>" id="id"/>
                                                    <input type="hidden" name="current_item_qty" value="<?= $items['sel_item_qty'] ?>"/>
                                                    <input type="hidden" name="total_item_qty" value="<?= $product['qty'] ?>"/>
                                                    <input type="hidden" name="selected_size" value="<?= $items['sel_item_size'] ?>" />
                                                    <input type="hidden" name="item_size" value="<?= $product['size'] ?>"/>
                                                    <input type="hidden" name="page_s_id" value="<?= $product['page_id'] ?>"/>
                                                    <input type="hidden" name="ssid" value="<?= $_GET['ssid'] ?>" id="ssid"/>
                                                    <select class="form-control update-qty" name="cart_qty" >
                                                        <?php
                                                        $selected = '';
                                                        $uq = $items['sel_item_qty'];
                                                        if (strpos($product['qty'], ";") && strpos($product['size'], ",")) {

                                                            $p_size = explode(",", $product['size']);
                                                            $q = explode(";", $product['qty']);
                                                            for ($j = 0; $j < count($p_size); $j++) {
                                                                if ($p_size[$j] == $items['sel_item_size']) {
                                                                    $total_av = $q[$j] + $items['sel_item_qty'];
                                                                    for ($i = 1; $i <= $total_av; $i++) {

                                                                        if ($uq == $i) {
                                                                            $selected = 'selected="selected"';
                                                                        } else {
                                                                            $selected = '';
                                                                        }
                                                                        ?>
                                                                        <option data-old-qty="<?= $items['sel_item_qty'] ?>"  data-qty="<?= $q[$j] ?>" data-item-id="<?= $items['id'] ?>" data-item-size="<?= $items['sel_item_size'] ?>" data-page-id="<?= $product['page_id'] ?>" value="<?= $i ?>" <?= $selected ?>><?= $i ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            
                                                        }
                                                        ?>

                                                    </select>


                                                </td>
                                                <td>
                                                    <input type="submit" value="update" name="update_qty" class="btn btn-default btn-xs"/>

                                                </td>
                                            </form>

                                            <td>
                                                <form method="post">
                                                    <input type="hidden" value="<?= $items['id'] ?>" name="item_id"/>
                                                    <input type="submit" value="remove" name="delete_item" class="rock-cart-del-button"/>
                                                </form>
                                            </td>
                                            </tr>
                                            <?php
                                        }
                                        array_push($to_continue, $product['page_id']);
                                        $k++;
                                        array_push($this->item_id, $items['sel_item_size']);
                                    }
                                } else {
                                    ?>

                                    <td>You have nothing in the shopping cart.</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <td id="res">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                        </table>
                    </div>

                    <!--Check out detail-->
                    <div class="col-md-4">
                        <div class="panel panel-default ">
                            <div class="panel-heading rock-cart-panel">
                                <h4>Cart Details</h4>
                            </div>
                            <div class="panel-body rock-cart-total">
                                <?php
                                $total_array = array();
                                $this->GetSubTotalOnCart($_COOKIE['order']);
                                if ($this->get_vals != NULL) {
                                    foreach ($this->get_vals as $cart_sub_totals) {
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

                                $this->total_item_price = $total;
                                $this->sel_tax = $tax;
                                $this->sel_grand_price = $grand_total;
                                ?>

                                <?php
                                if (defined('MINIMUM_PURCHASE') && (float) $total < (float) MINIMUM_PURCHASE) {

                                    $message_to_cust = "<div class='alert alert-warning'><p>The minimum purchase amount is $ " . MINIMUM_PURCHASE . " (" . DEFAULT_CURRENCY . ")</p></div>";
                                } else {
                                    $message_to_cust = "";
                                }
                                echo $message_to_cust;
                                ?>

                                <ul class="list-group">
                                    <li class="list-group-item"><b>Subtotal:</b>&Tab;<span style="float: right">$<?= number_format($total, 2, ".", ""); ?>&nbsp;USD</span></li>
                                    <li class="list-group-item"><a href="#" data-toggle="tooltip" data-placement="right"  id="tax" title="<?= DEFAULT_SHIPPED_FROM ?> State Tax applies only if purchasing within The State of <?= DEFAULT_SHIPPED_FROM ?>."><b>Tax**:</b>&Tab;@<?= DEFAULT_TAX_RATE ?> </a><span style="float: right">$<?= number_format($tax, 2, ".", ""); ?>&nbsp;<?= DEFAULT_CURRENCY ?></span></li>
                                    <li class="list-group-item"><b>Shipping:</b>&Tab;<span style="float: right">$0.00</span></li>
                                    <li class="list-group-item"><b>Estimated Total:</b>&Tab;<span style="float: right">$<?= number_format($grand_total, 2, ".", ""); ?>&nbsp;USD</span></li>


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
                                        <?php
                                        $this->GetNumberOfItemsInCart($_COOKIE['order']);
                                        if ($this->ReturnNumProductsInCart() == "0" || defined('MINIMUM_PURCHASE') && (float) $total < (float) MINIMUM_PURCHASE) {
                                            $disabled = "disabled='disabled'";
                                        } else {
                                            $disabled = "";
                                        }
                                        for ($h = 0; $h < count($this->item_id); $h++) {
                                            ?>
                                            <input type="hidden" name="item_size_<?= $h ?>" value="<?= $this->item_id[$h] ?>"/>
                                            <?php
                                        }
                                        ?>
                                        <input type="hidden" name="a_size" value="<?= count($this->item_id) ?>"/>

                                        <button class="btn btn-success btn-sm" name="cmd"  value="pre_check_out" formaction="" type="submit" <?= $disabled ?>><i class="fa fa-credit-card" aria-hidden="true"></i>&nbsp;Check Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function GetAllProductsForCart($session_id) {
        $sql = "SELECT * FROM `store_orders_items` WHERE `order_id` = '" . $session_id . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->shopping_cart_items[] = $row;
            }
            return $this->shopping_cart_items;
        }
    }

    public function GetDataFromProductpage($data) {
        $sql = "SELECT * FROM `pages_products` WHERE `page_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->products[] = $row;
            }
            return $this->products;
        }
    }

    public function UpdateQtyProducts($data) {


        $item_id = $data['item_id'];
        $current_item_qty = $data['current_item_qty'];
        $total_item_qty = $data['total_item_qty'];
        $item_size = $data['selected_size'];
        $page_id = $data['page_s_id'];
        $ssid = $data['ssid'];
        $new_selected_qty = $data['cart_qty'];

        if ($current_item_qty != $new_selected_qty) {
            $sql = "UPDATE `store_orders_items` SET `sel_item_qty` = '" . $new_selected_qty . "' , `old_qty` = '" . $current_item_qty . "' WHERE `id` = '" . $item_id . "'";
            $result = $this->_mysqli->query($sql);
        }

        $get_qty = "SELECT * FROM `pages_products` WHERE `page_id` = '" . $page_id . "'";
        $get_qty_res = $this->_mysqli->query($get_qty);
        while ($rows = $get_qty_res->fetch_array(MYSQLI_ASSOC)) {

            if (strpos($rows['size'], ",") && strpos($rows['qty'], ";")) {
                $size = explode(",", $rows['size']);

                $temp = explode(";", $rows['qty']);

                for ($i = 0; $i < count($size); $i++) {
                    if ($size[$i] == $item_size) {

                        $get_old_value = "SELECT `sel_item_qty`,`old_qty` FROM `store_orders_items` WHERE `id` = '" . $item_id . "'";
                        $get_old_value_res = $this->_mysqli->query($get_old_value);
                        while ($row = $get_old_value_res->fetch_array(MYSQLI_ASSOC)) {
                            $old_val = $row['old_qty'];
                            $new_val = $row['sel_item_qty'];
                            if (isset($_SESSION['wholesaler_on']) && !empty($_SESSION['wholesaler_on'])) {
                                $qty = explode(";", $rows['wholesale_qty_on_hand']);
                                $field = 'wholesale_qty_on_hand';
                                
                            } else {
                                $qty = explode(";", $rows['qty']);
                                $field = 'qty';
                            }


                            if ($old_val == $new_val) {
                                continue;
                            } else if ($new_val > $old_val) {
                                $diff = $new_val - $old_val;
                                $qty[$i] = $qty[$i] - $diff;
                                $m = implode(";", $qty);
                                $update_qty = "UPDATE `pages_products` SET `" . $field . "` ='" . $m . "' WHERE `page_id` = '" . $page_id . "'";
                                $update_result = $this->_mysqli->query($update_qty);
                            } else if ($new_val < $old_val) {
                                $diff = abs($new_val - $old_val);
                                $qty[$i] = $qty[$i] + $diff;
                                $m = implode(";", $qty);
                                $update_qty = "UPDATE `pages_products` SET `" . $field . "` ='" . $m . "' WHERE `page_id` = '" . $page_id . "'";
                                $update_result = $this->_mysqli->query($update_qty);
                            }
                        }
                    }
                }
            } else {
                //Normal
            }
        }
    }

    public function DeleteItemFromCart($data) {

        $sql = "SELECT * FROM `store_orders_items` WHERE `id` = '" . $data['item_id'] . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {

            /*
             * First add back the qty to the pages_produts
             */
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $del_qty = $row['sel_item_qty'];
                $page_id = $row['sel_item_id'];
                $item_size = $row['sel_item_size'];
                $get_data_products = "SELECT * FROM `pages_products` WHERE `page_id` = '" . $page_id . "'";
                $get_data_products_res = $this->_mysqli->query($get_data_products);
                while ($products = $get_data_products_res->fetch_array(MYSQLI_ASSOC)) {


                    if (strpos($products['size'], ",") && strpos($products['qty'], ";")) {
                        $size = explode(",", $products['size']);
                        if (isset($_SESSION['wholesaler_on']) && !empty($_SESSION['wholesaler_on'])) {
                            $item_qty = explode(";", $products['wholesale_qty_on_hand']);
                            $field = 'wholesale_qty_on_hand';
                        } else {
                            $item_qty = explode(";", $products['qty']);
                            $field = 'qty';
                        }


                        for ($i = 0; $i < count($size); $i++) {
                            if ($size[$i] == $item_size) {

                                $item_qty[$i] = $item_qty[$i] + $del_qty;

                                $updated_qty = implode(";", $item_qty);

                                $update_item_qty = "UPDATE `pages_products` SET `" . $field . "` = '" . $updated_qty . "' WHERE `page_id` = '" . $products['page_id'] . "'";
                                $update_item_qty_res = $this->_mysqli->query($update_item_qty);
                                if ($update_item_qty) {

                                    /*
                                     * Now ready to delete item from shopping cart
                                     */
                                    $del_item = "DELETE FROM `store_orders_items` WHERE `id` = '" . $data['item_id'] . "'";
                                    $del_item_res = $this->_mysqli->query($del_item);

                                    $this->num_items = NULL;
                                    $this->GetNumberOfItemsInCart($_COOKIE['order']);
                                    $this->num_items = $this->ReturnNumProductsInCart();

                                    $fp = fopen(ABSOLUTH_ROOT . 'public_html/ShoppingCart/qty.txt', 'w');
                                    fwrite($fp, $this->num_items);
                                    fclose($fp);
                                    /*
                                     * this updates the cart qty below social media
                                     */
                                    //header("Refresh: 1; url=");
                                }
                            }
                        }
                    } else {
                        //Normal Case
                    }
                }
            }
        }
    }

    public function GetSubTotalOnCart($data) {
        $sql = "SELECT * FROM `store_orders_items` WHERE `order_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->get_vals[] = $row;
            }
            return $this->get_vals;
        }
    }

}
