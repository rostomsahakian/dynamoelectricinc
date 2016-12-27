<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SubCategories
 *
 * @author rostom
 */
class SubCategories {

    private $_mysqli;
    private $_db;
    public $items;
    public $frontenddata;
    public $parents;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->frontenddata = new FrontEndLogic();
    }

    public function SubCategoriesPage($data) {
        ?>
        <!--PAGE CONTENT GO HERE-->
        <div class="container rock-main-container">
            <h2><?= $data['page_name'] ?></h2> 
            <div class="col-md-12">

                <?= $data['page_content'] ?>

            </div>
            <div class="col-md-12">

                <?php
                $this->GetAllProductsForSubPages($data['id']);
                // var_dump($this->items);
                if ($this->items != NULL) {
                    if (array_key_exists("item_name", $this->items[0]) && array_key_exists("image_0", $this->items[0]) && array_key_exists("price", $this->items[0])) {
                        foreach ($this->items as $item) {



                            if ($this->frontenddata->GetPageAlias($item['page_id'])) {
                                foreach ($this->frontenddata->page_alias as $page_alias) {
                                    $url = $page_alias['page_alias'];
                                }
                            } else {

                                $this->GetPageParentName($data['page_parent']);
                                foreach ($this->parents as $parent) {

                                    $page_grand_parent_no_spaces = str_replace(" ", "-", trim($parent['page_name']));
                                    $page_grand_no_upper_case = strtolower($page_grand_parent_no_spaces);
                                    $page_grand_no_ands = str_replace("&", "and", $page_grand_no_upper_case);
                                    $clean_grand_parent_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $page_grand_no_ands);

                                    $page_parent_no_spaces = str_replace(" ", "-", trim($data['page_name']));
                                    $no_upper_case = strtolower($page_parent_no_spaces);
                                    $no_ands = str_replace("&", "and", $no_upper_case);
                                    $clean_parent_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);

                                    $no_spaces = str_replace(" ", "-", trim($item['item_name']));
                                    $item_no_upper = strtolower($no_spaces);
                                    $item_no_ands = str_replace("&", "and", $item_no_upper);
                                    $clean_item_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $item_no_ands);


                                    $url = "/" . $clean_grand_parent_name . "/" . $clean_parent_name . "/" . $clean_item_name . "/" . $item['page_id'];
                                }
                            }
                            ?>
                            <?php
                            if (isset($_SESSION['wholesaler_on'])) {
                                $hot_fix = "wholesaler-is-on";
                            } else {
                                $hot_fix = "";
                            }
                            ?>
                            <div class="col-md-3 rock-item-image-holder <?= $hot_fix ?>"> 
                                <div class="row"></div>
                                <div class="row">


                                    <a href="<?= "/" . $clean_grand_parent_name ?>" class="rock-brand-in-box"><?= $parent['page_name'] ?></a>
                                    <a  href="<?= $url ?> "  class="rock-product-link">
                                        <span class="rollover" >                                    

                                        </span>
                                    </a>
                                    <img id="zoom_<?= $item['page_id'] ?>" src="<?= $item['image_0'] ?>"   class="rock-item-image">


                                </div>

                                <div class="row rock-item-captions">

                                    <p class="rock-item-name"> <?= $item['item_name'] ?><p> 
                                        <?php
                                        if (isset($_SESSION['wholesaler_on'])) {
                                            if (strpos($item['wholesale_p'], ";")) {
                                                $r_price = explode(";", $item['wholesale_p']);
                                                $p = '<p>WHOLESALE PRICE: $' . $r_price[0] . '</p>';
                                            } else {
                                                $p = '<p>Call us for pricing information</p>';
                                            }
                                            ?>
                                        <p style=" text-decoration: line-through;">  REG. PRICE:  $<?= $item['price'] ?></p>

                                        <?php
                                        echo $p;
                                    } else {
                                        ?>
                                        <p>  REG. PRICE:  $<?= $item['price'] ?></p>  
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        /*
                         * List the page names
                         */
                        ?>
                        <div class="col-m-12">
                            <div class="list-group">
                                <?php
                                foreach ($this->items as $item) {
                                    if ($this->frontenddata->GetPageAlias($item['page_id'])) {
                                        foreach ($this->frontenddata->page_alias as $page_alias) {
                                            $url = $page_alias['page_alias'];
                                        }
                                    } else {

                                        $this->GetPageParentName($data['page_parent']);
                                        foreach ($this->parents as $parent) {

                                            $page_grand_parent_no_spaces = str_replace(" ", "-", trim($parent['page_name']));
                                            $page_grand_no_upper_case = strtolower($page_grand_parent_no_spaces);
                                            $page_grand_no_ands = str_replace("&", "and", $page_grand_no_upper_case);
                                            $clean_grand_parent_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $page_grand_no_ands);

                                            $page_parent_no_spaces = str_replace(" ", "-", trim($data['page_name']));
                                            $no_upper_case = strtolower($page_parent_no_spaces);
                                            $no_ands = str_replace("&", "and", $no_upper_case);
                                            $clean_parent_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);

                                            $no_spaces = str_replace(" ", "-", trim($item['page_name']));
                                            $item_no_upper = strtolower($no_spaces);
                                            $item_no_ands = str_replace("&", "and", $item_no_upper);
                                            $clean_item_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $item_no_ands);


                                            $url = "/" . $clean_grand_parent_name . "/" . $clean_parent_name . "/" . $clean_item_name . "/" . $item['id'];
                                        }
                                    }
                                    ?>
                                    <a href="<?= $url ?>" class="list-group-item"><?= $item['page_name'] ?></a>
                                    <?php
                                }
                                ?>

                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function GetAllProductsForSubPages($data) {
        /*
         * First check the pages table and see what type of page the item is if is a product then do the following else get contnt from page_content
         */
        $check_page_type = "SELECT * FROM `pages` WHERE `page_parent` = '" . $data . "'";
        $check_page_type_res = $this->_mysqli->query($check_page_type);
        if ($check_page_type_res->num_rows > 0) {
            // var_dump($check_page_type_res->num_rows);
            while ($r = $check_page_type_res->fetch_array(MYSQLI_ASSOC)) {
                if ($r['page_type'] != "10") {
                    /*
                     * get data from pages
                     */
                    $this->items[] = $r;
                } else {
                    $sql = "SELECT * FROM `pages_products` WHERE `page_parent` = '" . $data . "'";
                    $result = $this->_mysqli->query($sql);
                    $num_rows = $result->num_rows;
                    if ($num_rows > 0) {
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $this->items[] = $row;
                        }
                    }
                }
            }
            return $this->items;
        }
    }

    public function GetPageParentName($page_parent) {
        $sql = "SELECT `id`, `page_name`, `page_type`, `page_parent`, `page_id` FROM `pages` WHERE `id` = '" . $page_parent . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->GetPageParentName($row['page_parent']);
                $this->parents[] = $row;
            }
            return $this->parents;
        }
    }

}
