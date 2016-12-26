<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Categories
 *
 * @author rostom
 */
class Categories {

    private $_mysqli;
    private $_db;
    public $categories;
    public $frontenddata;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->frontenddata = new FrontEndLogic();
    }

    public function CategoriesPageSetup($data) {
        $this->categories = NULL;
        $this->GetCategories("9", $data['id']);
        //var_dump($this->categories);
        ?>
        <!--PAGE CONTENT GO HERE-->
        <div class="container rock-main-container">
            <?php
            echo $data['page_content'];
            ?>

            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="list-group">
                        <h4 href="#" class="list-group-item rock-list-group-heading">
                            <i class="fa fa-list fa-1x" aria-hidden="true"></i> &nbsp; &nbsp;  <?= str_replace("|", " ", $data['page_name']) ?>
                        </h4>
                        <?php
                        foreach ($this->categories as $category) {
                            $page_parent_no_spaces = str_replace(" ", "-", $category['parent_page_name']);
                            $no_upper_case = strtolower($page_parent_no_spaces);
                            $no_ands = str_replace("&", "and", $no_upper_case);
                            $clean_parent_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);

                            $page_name_no_spaces = str_replace(" ", "-", $category['page_name']);
                            $page_no_upper_case = strtolower($page_name_no_spaces);
                            $page_no_ands = str_replace("&", "and", $page_no_upper_case);
                            $clean_page_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $page_no_ands);

                            if ($this->frontenddata->GetPageAlias($category['page_id'])) {
                                foreach ($this->frontenddata->page_alias as $page_alias) {
                                    $url = "/" . $page_alias['page_alias'];
                                }
                            } else {

                                $url = "/" . $clean_parent_name . "/" . $clean_page_name . "/" . $category['id'];
                            }
                            ?>
                            <a href="<?= $url; ?>" class="list-group-item rock-list-group-item"><?= $category['page_name'] ?></a>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-9" style="margin-top: 10px;">
                    <?php
                    $this->categories = NULL;
                    $this->GetCategories("9", $data['id']);

                    foreach ($this->categories as $products) {

                        $page_parent_no_spaces = str_replace(" ", "-", $products['parent_page_name']);
                        $no_upper_case = strtolower($page_parent_no_spaces);
                        $no_ands = str_replace("&", "and", $no_upper_case);
                        $clean_parent_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);

                        $page_name_no_spaces = str_replace(" ", "-", $products['page_name']);
                        $page_no_upper_case = strtolower($page_name_no_spaces);
                        $page_no_ands = str_replace("&", "and", $page_no_upper_case);
                        $clean_page_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $page_no_ands);

                        if ($this->frontenddata->GetPageAlias($products['page_id'])) {
                            foreach ($this->frontenddata->page_alias as $page_alias) {
                                $url = "/" . $page_alias['page_alias'];
                            }
                        } else {

                            $url = "/" . $clean_parent_name . "/" . $clean_page_name . "/" . $products['id'];
                        }
                        $this->frontenddata->page_alias = NULL;
                        if ($this->frontenddata->GetPageAlias($products['parent_page_id'])) {
                            foreach ($this->frontenddata->page_alias as $parent_page_alias) {
                                $parent_url = "/" . $parent_page_alias['page_alias'];
                            }
                        } else {
                            $parent_url = "/" . $clean_parent_name . "/" . $products['parent_page_uid'];
                        }
                        foreach ($products[1] as $product) {
                            ?>
                            <center><div class="col-md-4 rock-item-image-holder"> 

                                    <div class="row">
                                        <?php
                                        $item_no_spaces = str_replace(" ", "-", $product['item_name']);
                                        $item_no_upper_case = strtolower($item_no_spaces);
                                        $item_no_ands = str_replace("&", "and", $item_no_upper_case);
                                        $clean_item_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $item_no_ands);
                                        /*
                                         * The $item_url has now been changed to go to sub categories page instead of the product page
                                         * RS 08/15/2016
                                         *  $item_url = "/" . $clean_parent_name . "/" . $clean_page_name . "/" . trim($clean_item_name) . "/" . $product['page_id'];
                                         */


                                        $item_url = $url;
                                        ?>

                                        <a href="<?= $parent_url ?>" class="rock-brand-in-box"><?= $products['parent_page_name'] ?></a>
                                        <a  href="<?= $item_url ?> "  class="rock-product-link">
                                            <span class="rollover" >                                                                            
                                            </span>
                                        </a>
                                        <?php ?>
                                        <img src="<?= $product['image_0'] ?>" class="rock-item-image">


                                    </div>

                                    <div class="row rock-item-captions">

                                        <p class="rock-item-name"><a href="<?= $url ?>" > <?= $products['page_name'] ?></a><p> 
                                            <!--NOT USED FOR THE LINE-->
                <!--                                    <p class="rock-item-name"><?php $product['item_name'] ?></p>-->
                <!--                                    <p class="rock-item-name">REG PRICE: $<?php $product['price'] ?></p>-->

                                    </div>
                                </div></center>
                            <?php
                        }
                    }
                    ?>
                </div>

            </div>
        </div>



        <?php
    }

    public function GetCategories($page_type, $id) {

        $sql = "SELECT `id`,`page_name`, `page_type`, `page_id` FROM `pages` WHERE `page_type` ='" . $page_type . "' AND `page_parent` = '" . $id . "'";

        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->frontenddata->page_data = NULL;
                $this->frontenddata->GetpageDataByID($row['id'], "homepage");
                $this->categories[] = $this->frontenddata->ReturnPageData();
            }
            return $this->categories;
        } else {
            return FALSE;
        }
    }

}
