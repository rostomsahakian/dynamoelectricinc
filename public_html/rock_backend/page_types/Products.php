<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Products
 *
 * @author rostom
 */
class Products {

    private $_mysqli;
    private $_db;
    public $products;
    public $related_items;
    public $parents;
    public $variation;
    public $shopping_cart_func;
    public $flag = 0;
    public $messages = array();
    public $alert_class = "";

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->shopping_cart_func = new ShoppingCartFunc();
        ?>

        <?php
    }

    public function ProductPage($data) {
        if (isset($_REQUEST['add_to_cart'])) {

            if ($_REQUEST['qty'] == 0 || $_REQUEST['qty'] == "0") {
                $this->flag = 1;
                $message = array("1" => "Sorry item is out of stock.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-warning-alert";
            } else {

                $this->shopping_cart_func->ShoppingCartProccess($_REQUEST);
                if ($this->shopping_cart_func->flag == 1) {
                    $this->flag = 1;
                    array_push($this->messages, $this->shopping_cart_func->message);
                    $this->alert_class = "rock-success-message";
                    $this->shopping_cart_func->GetNumberOfItemsInCart($_COOKIE['order']);
                    $fp = fopen(ABSOLUTH_ROOT . 'public_html/ShoppingCart/qty.txt', 'w');
                    fwrite($fp, $this->shopping_cart_func->ReturnNumProductsInCart());
                    fclose($fp);
                    ?>




                    <?php
                }
            }
        }
        ?>

        <!--PAGE CONTENT GO HERE-->
        <div class="container rock-main-container" >

            <form method="post">

                <div class="row">
                    <div class="col-md-12"><div class="col-md-1">&nbsp;</div><div class="col-md-4"><center>
                                <h1 class="rock-item-page-title"><?= $data['page_name'] ?></h1></center></div>
                    </div>
                    <div class="col-md-12" itemscope itemtype="http://schema.org/Product">
                        <?php
                        $this->GetAllProductsData($data['page_id']);
                        foreach ($this->products as $product) {
                            ?>
                            <div class="col-md-1">&nbsp;</div>
                            <div class="col-md-4 rock-item-page-image-div" id="ex1">    
                                <img itemprop="image" alt="image of <?= $product['item_name'] ?>" src="<?= $product['image_0'] ?>" class="rock-item-image"/>
                            </div>
                            <div class="col-md-6 rock-item-page-details">
                                <?php
                                if ($this->flag == 1) {
                                    ?>

                                    <div class="alert alert-success <?= $this->alert_class ?> alert-dismissible" role="alert">
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
                                <div class="panel panel-default">
                                    <!-- Default panel contents -->
                                    <?php
                                    $no_spaces = str_replace("", "-", strtolower($product['category']));
                                    $no_ands = str_replace("&", "and", $no_spaces);
                                    $clean_name_for_url = preg_replace('/[^a-zA-Z0-9,-]/', '-', $no_ands);
                                    $parent_url = "/" . $clean_name_for_url . "/" . $product['page_parent'];
                                    ?>
                                    <div class="panel-heading"><span itemprop="name"><?= $product['item_name'] ?></span>
                                        <br/>
                                        <div itemprop="manufacturer" itemscope itemtype="http://schema.org/Store">
                                            <span >by </span><span itemprop="name"> <a href="<?= $parent_url ?>"><?= $product['brand'] ?></a></span> 
                                        </div>
                                        <?php
                                        if (isset($_SESSION['wholesaler_on']) && !empty($_SESSION['wholesaler_on'])) {
                                            ?>

                                            <p>Wholesale pricing.<a href="#" data-toggle="tooltip" data-placement="right"  id="wholesale_info" title="Note: The quantity and pricing shown, represents items per case and not as individual item."><i class="fa fa-question-circle"></i></a></p>    
                                            <script>

                                                $(document).ready(function () {
                                                    $("#wholesale_info").hover(function () {
                                                        $('#wholesale_info').tooltip('show');
                                                    });
                                                });
                                            </script>
                                            <?php
                                        }
                                        ?>
                                    </div>

                                    <!-- Price and Quantity -->
                                    <center><div class="panel-body">
                                            <div class="col-sm-4">
                                                <h3 class="output"></h3>

                                                <input type="hidden" name="h_price" value="" id="h-price" />
                                            </div>

                                            <input type="hidden" value=""  name="hidden_qty" class="h-qty" id="qty"/>
                                            <div class="col-sm-4 rock-quantity-div-select">
                                                <label>Quantity: </label>
                                                <?php ?>

                                                <select name="qty" id="mySelect" class="form-control findqty" >
                                                    <?php
                                                    $selected_qty = '';
                                                    if (strpos($product['qty'], ";")) {
                                                        $quantity = explode(";", $product['qty']);
                                                    } else {
                                                        $quantity = $product['qty'];
                                                    }
                                                    for ($i = 1; $i <= $quantity[0]; $i++) {

                                                        $qty_req = isset($_REQUEST['qty']) ? $_REQUEST['qty'] : 0;
                                                        if ($qty_req == $i) {

                                                            $selected_qty = 'selected="selected"';
                                                        } else {
                                                            $selected_qty = '';
                                                        }
                                                        ?>
                                                        <option value="<?= $i ?>" <?= $selected_qty ?>><?= $i ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>


                                            </div>
                                            <?php
                                            if ($product['price'] == "Call for price") {
                                                $div_id = "";
                                            } else {
                                                $div_id = "estimate";
                                            }
                                            ?>
                                            <div class="col-sm-4 rock-quantity-div-select" id="<?= $div_id ?>">

                                            </div>
                                        </div></center>
                                </div>
                            </div>
                            <div class="col-md-6 rock-item-page-details">
                                <div class="panel panel-default">
                                    <!-- Default panel contents -->
                                    <div class="panel-heading">Sizes</div>

                                    <!-- Sizes-->
                                    <?php
                                    if (isset($_SESSION['wholesaler_on']) && !empty($_SESSION['wholesaler_on'])) {
                                        ?>
                                        <input type="hidden" id="wholesaleon" value="1"/>
                                        <?php
                                    } else {
                                        ?>
                                        <input type="hidden" id="wholesaleon" value="0"/>
                                        <?php
                                    }
                                    ?>

                                    <center><div class="panel-body">
                                            <div class="col-md-7">
                                                <label>Size: </label>
                                                <select  name="size" class="form-control item_size" >

                                                    <?php
                                                    if (isset($_SESSION['wholesaler_on']) && !empty($_SESSION['wholesaler_on'])) {
                                                        $retail_price = $product['wholesale_p'];
                                                        $product_qty = $product['wholesale_qty_on_hand'];
                                                        $wholesale_qty_per_case = $product['wholesale_qty_in_case'];
                                                        
                                                    } else {
                                                        $retail_price = $product['item_variation'];
                                                        $product_qty = $product['qty'];
                                                        $wholesale_qty_per_case = NULL;
                                                       
                                                       
                                                    }


                                                    if (strpos($product['size'], ",") && strpos($retail_price, ";") || strpos($product_qty, ";")) {

                                                        $prices = explode(";", $retail_price);

                                                        $sizes = explode(",", $product['size']);

                                                        $qty = explode(";", $product_qty);
                                                        
                                                       

                                                        for ($i = 0; $i < count($sizes); $i++) {
                                                            $selected = '';
                                                            $product_size = isset($_REQUEST['size']) ? $_REQUEST['size'] : $sizes[0];

                                                            if ($product_size == $sizes[$i]) {
                                                                $selected = 'selected="selected"';
                                                            } else {
                                                                $selected = '';
                                                            }
                                                            if(isset($_SESSION['wholesaler_on']) && !empty($_SESSION['wholesaler_on'])){
                                                                 $wqpec = explode(";", $wholesale_qty_per_case);
                                                            }else{
                                                                $wqpec[$i] = "1";
                                                            }
                                                            ?>

                                                            <option data-iprice="<?= $prices[$i] ?>" data-price="<?= $prices[$i] * $wqpec[$i] ?>" data-qty="<?= $qty[$i] ?>" data-eachcase="<?= $wqpec[$i] ?>" value="<?= $sizes[$i] ?>" <?= $selected ?>><?= $sizes[$i] ?></option>

                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <option value="<?= $product['size'] ?>"><?= $product['size'] ?></option>
                                                        <?php
                                                    }
                                                    ?>

                                                </select>

                                                <script>

                                                    $(document).ready(function () {
                                                        var init_qty = $('select.item_size').find(':selected').data('qty');
                                                        $('.h-qty').val(init_qty);
                                                        console.log(init_qty);
                                                        if (init_qty === 0) {


                                                        }
                                                        var init_price = $('select.item_size').find(':selected').data('price');
                                                        var individual_price = $('select.item_size').find(':selected').data('iprice');
                                                        var in_each_case = $('select.item_size').find(':selected').data('eachcase');
                                                        $('#h-price').val(init_price);
                                                        var checkwholesale = $('#wholesaleon').val();
                                                        if (checkwholesale === "1") {
                                                            $('.output').html('$' + init_price + ' <span style="font-size:8pt; font-style:italic;"> /Per Case</span><br/><span style="font-size:8pt;"><b>Each Bottle $' + individual_price + '</b></span><br/><span style="font-size:8pt;"><b>Each case has ' + in_each_case + ' bottles.</b></span>');
                                                        } else {
                                                            $('.output').html('$' + init_price + ' <span style="font-size:8pt; font-style:italic;"> /Each</span>');

                                                        }

                                                        var qty_selected = $('select.findqty').find(':selected').val();
                                                        var price = $('select.item_size').find(':selected').data('price');

                                                        var total_price = qty_selected * price;
                                                        $('#estimate').append('<div id="est"><span style="color:#000; font-weight:bold;" itemprop="priceCurrency" content="USD">Estimated total:<br/> $</span><span style="color:#000; font-weight:bold" itemprop="price" content="' + total_price + '">' + total_price + ' USD</span><br/><span style="font-weight:bold;">Tax not included</span><br/><span style="color:red; font-weight:bold">Free Shipping.</span></div>');




                                                        $('#mySelect').find('option').remove().end();
                                                        if (init_qty != 0) {
                                                            var i;
                                                            for (i = 1; i <= init_qty; i++) {
                                                                $('#mySelect').append($('<option>', {
                                                                    value: i,
                                                                    text: i

                                                                }));


                                                            }
                                                        } else {
                                                            $('#mySelect').append($('<option>', {
                                                                value: "out-of-stock",
                                                                text: "Out of Stock"

                                                            }));
                                                        }
                                                        //ON CHANEG FOR THE SIZE

                                                        $('select.item_size').change(function () {
                                                            var x = document.getElementById("mySelect");
                                                            $('#mySelect').find('option').remove().end();
                                                            $('#estimate').find('p').remove().end();
                                                            var in_each_cases = $('select.item_size').find(':selected').data('eachcase');
                                                            var individual_prices = $('select.item_size').find(':selected').data('iprice');

                                                            var price = $('select.item_size').find(':selected').data('price');
                                                            var wholesale_price = price;
                                                            var qty = $('select.item_size').find(':selected').data('qty');
                                                            $('#h-price').val(price);


                                                            $('#estimate').find('#est').remove().end();
                                                            $('#estimate').append('<div id="est"><span style="color:#000; font-weight:bold;" itemprop="priceCurrency" content="USD">Estimated total:<br/> $</span><span style="color:#000; font-weight:bold" itemprop="price" content="' + price + '">' + price + ' USD</span><br/><span style="font-weight:bold;">Tax not included</span><br/><span style="color:red; font-weight:bold">Free Shipping.</span></div>');
                                                            var checkwholesale = $('#wholesaleon').val();
                                                            if (checkwholesale === "1") {
                                                                $('.output').html('$' + price + ' <span style="font-size:8pt; font-style:italic;"> /Per Case</span><br/><span style="font-size:8pt;"><b>Each Bottle $' + individual_prices + '</b></span><br/><span style="font-size:8pt;"><b>Each case has ' + in_each_cases + ' bottles.</b></span>');
                                                            } else {
                                                                $('.output').html('$' + price + ' <span style="font-size:8pt; font-style:italic;"> /Each</span>');

                                                            }
                                                            $('.h-qty').val(qty);
                                                            //IF QTY IS ZERO THEN SAY OUT OF STOCK
                                                            if (qty != 0) {
                                                                var i;
                                                                for (i = 1; i <= qty; i++) {
                                                                    $('#mySelect').append($('<option>', {
                                                                        value: i,
                                                                        text: i

                                                                    }));


                                                                }
                                                            } else {
                                                                $('#mySelect').append($('<option>', {
                                                                    value: "out-of-stock",
                                                                    text: "Out of Stock"

                                                                }));
                                                            }

                                                        });
                                                        $('select.findqty').change(function () {
                                                            var qty_selected = $('select.findqty').find(':selected').val();
                                                            var in_each_cases = $('select.item_size').find(':selected').data('eachcase');
                                                            var price = $('select.item_size').find(':selected').data('price');
                                                            $('#h-price').val(price);
                                                            var total_price = qty_selected * price;
                                                            $('#estimate').find('#est').remove().end();
                                                            $('#estimate').append('<div id="est"><span style="color:#000; font-weight:bold;" itemprop="priceCurrency" content="USD">Estimated total:<br/> $</span><span style="color:#000; font-weight:bold" itemprop="price" content="' + total_price + '">' + total_price + ' USD</span><br/><span style="font-weight:bold;">Tax not included</span><br/><span style="color:red; font-weight:bold">Free Shipping.</span></div>');
                                                        });
                                                    });
                                                </script>



                                            </div>

                                        </div></center>
                                </div>
                            </div>
                            <?php
                            if (defined('PRICE_STATUS')) {
                                if (PRICE_STATUS === 0) {
                                    ?>
                                    <?php
                                } else {
                                    ?>
                                    <div class="col-md-5">&nbsp;</div>
                                    <div class="col-md-6 rock-item-page-details">
                                        <input type="hidden" name="item_category" value="<?= $product['category'] ?>"/>
                                        <input type="hidden" name="item_model_number" value="<?= $product['model_number'] ?>"/>
                                        <input type="hidden" name="item_page_id" value="<?= $product['page_id'] ?>"/>
                                        <input type="hidden" name="item_parent_id" value="<?= $product['page_parent'] ?>"/>
                                        <input type="hidden" name="item_name" value="<?= $product['item_name'] ?>"/>
                                        <input type="submit" name="add_to_cart" value="ADD TO BAG" class="btn btn-success btn-large rock-add-to-cart-button"/>


                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5"></div>
                                <div class="col-md-6 rock-item-page-details">
                                    <button class="btn btn-default btn-large rock-add-to-cart-button wish-list">ADD TO WISHLIST</button>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>


                    <div class="row">
                        <div class="col-md-5"></div>
                        <!-- Description -->
                        <div class="col-md-6 rock-item-page-details">
                            <div class="panel panel-default">
                                <!-- Default panel contents -->
                                <div class="panel-heading">Details</div>

                                <!-- Sizes-->

                                <center><div class="panel-body">
                                        <div class="row">
                                            <ul class="nav nav-tabs" role="tablist" id="tabs_login" >
                                                <li class="active" aria-controls="login" role="tab" ><a href="#description" data-toggle="tab">Description</a></li>
                                                <li aria-controls="f_pass" role="tab" ><a href="#specs" data-toggle="tab">Specification</a></li>
                                            </ul>
                                        </div>

                                        <div class="tab-content roc">

                                            <div class="tab-pane active" id="description">
                                                <div class="col-md-12 rock-item-desc" itemprop="description">
                                                    <?php
                                                    if ($product['description'] != "") {
                                                        echo $product['description'];
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="specs">
                                                <div class="col-md-12 rock-item-desc">
                                                    <ul>
                                                        <?php
                                                        if ($product['color'] != "") {
                                                            ?>
                                                            <li itemprop="color">Color: <?= $product['color']; ?></li>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php
                                                        if ($product['category'] != "") {
                                                            ?>

                                                            <li itemprop="category">Category: <?= $product['category']; ?></li>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php
                                                        if ($product['model_number'] != "") {
                                                            ?>

                                                            <li itemprop="model">Model# :<?= $product['model_number']; ?></li>
                                                            <?php
                                                        }
                                                        ?>
                                                        <li itemprop="availability" href="http://schema.org/InStock">Availability: In Stock </li>
                                                    </ul>

                                                </div>
                                            </div>
                                        </div>
                                    </div></center>
                            </div>
                        </div>

                        <?php
                    }
                    ?>
                </div>
                <?php
                if ($this->GetrelatedItems($data) != false) {
                    ?>
                    <div class="col-md-12">
                        <hr/>
                        <h2>RELATED ITEMS</h2>
                    </div>
                    <div class="col-md-12">
                        <!--Related items will go here-->
                        <?php
                        foreach ($this->related_items as $related_item) {

                            //echo $related_item['item_name'];
                            ?>
                            <center><div class="col-md-4 rock-item-image-holder rock-related-items" itemprop="isRelatedTo" itemscope itemtype="http://schema.org/Product"> 

                                    <div class="row">
                                        <?php
                                        if ($this->GetParentInfo($related_item['page_parent'])) {
                                            $big = array();
                                            foreach (array_reverse($this->parents) as $parent) {
                                                $p = explode("/", $parent['page_name']);

                                                $parents = array_reverse($p);
                                                $parents_u = implode("/", $parents);
                                            }
                                            array_push($big, $parents_u);
                                            $m = implode("/", $big);

                                            $parent_no_spaces = str_replace(" ", "-", trim($m));
                                            $parent_no_upper_case = strtolower($parent_no_spaces);
                                            $parent_no_ands = str_replace("&", "and", $parent_no_upper_case);
                                            $parent_item_name = preg_replace('/[^a-zA-Z0-9,-\/]/', "-", $parent_no_ands);


                                            $item_no_spaces = str_replace(" ", "-", trim($related_item['item_name']));
                                            $item_no_upper_case = strtolower($item_no_spaces);
                                            $item_no_ands = str_replace("&", "and", $item_no_upper_case);
                                            $clean_item_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $item_no_ands);

                                            $related_item_url = "/" . $parent_item_name . "/" . $clean_item_name . "/" . $related_item['page_id'];
                                            ?>

                                            <a href="" class="rock-brand-in-box"></a>
                                            <a  href="<?= $related_item_url; ?>"  class="rock-product-link">
                                                <span class="rollover" >                                                                            
                                                </span>
                                            </a>
                                        <?php } ?>
                                        <img itemprop="image" alt="image of <?= $related_item['item_name'] ?>" src="<?= $related_item['image_0'] ?>" class="rock-item-image">


                                    </div>

                                    <div class="row rock-item-captions">

                                        <p class="rock-item-name" itemprop="name"> <?= $related_item['item_name'] ?><p> 


                                    </div>
                                </div></center>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
        </div>
        </form>
        </div>

        <?php
    }

    public function GetAllProductsData($data) {

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

    public function GetrelatedItems($data) {

        $sql = "SELECT * FROM `pages_products` WHERE `page_parent` ='" . $data['page_parent'] . "' AND `page_id` != '" . $data['page_id'] . "' ORDER BY RAND() LIMIT 6";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->related_items[] = $row;
            }
            return $this->related_items;
        } else {
            return false;
        }
    }

    public function GetParentInfo($data) {
        $sql = "SELECT `page_name`, `page_parent` FROM `pages` WHERE `id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->parents[] = $row;
                $this->GetParentInfo($row['page_parent']);
            }

            return $this->parents;
        } else {
            return FALSE;
        }
    }

    public function GetVariations($data) {
        $sql = "SELECT * FROM `pages_products` WHERE `page_id` ='" . $data . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->variation[] = $row;
            }
            return $this->variation;
        } else {
            return false;
        }
    }

}
