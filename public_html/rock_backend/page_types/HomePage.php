<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HomePage
 *
 * @author rostom
 */
class HomePage {

    private $_mysqli;
    private $_db;
    public $categories;
    public $frontenddata;
    public $random_data;
    public $parent_child;
    public $top_parent;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->frontenddata = new FrontEndLogic();
    }

    public function MainHomePage($data) {
        ?>
        <div class="container rock-main-container">


            <!--Carousel-->
            <?php
            echo $data['page_content'];
            ?>
            <div class="row">
                <div class="col-md-12">
                    <hr/>
                    <h1><?= CUSTOMER ?></h1>
                </div>
                <div class="col-md-12 rock-items-col-12">
                    <?php
                    $this->GetRamdomDataForHomePage("4");
                    foreach ($this->random_data as $item_f) {
                        for ($i = 0; $i < count($item_f); $i++) {


                            foreach ($item_f[$i]['item_details'] as $item_d) {
                                ?>
                                <div class="col-md-4 rock-item-image-holder"> 

                                    <div class="row">
                                        <?php
                                        $this->top_parent = NULL;
                                        $this->GetTopParent($item_d['page_parent']);
                                        foreach ($this->top_parent as $top_parent) {
                                        
                                            $this->frontenddata->page_alias = NULL;
                                            if ($this->frontenddata->GetPageAlias($top_parent['page_id'])) {
                                                foreach ($this->frontenddata->page_alias as $parent_page_alias) {
                                                    $parent_url = "/" . $parent_page_alias['page_alias'];
                                                }
                                            } else {
                                                $parent_url = "/" . $clean_parent_name . "/" . $top_parent['id'];
                                            }
                                            ?>
                                            <a href="<?= $parent_url; ?>" class="rock-brand-in-box"><?= $top_parent['page_name'] ?></a>
                                            <?php
                                        }
                                        $page_name_no_spaces = str_replace(" ", "-", trim($item_d['item_name']));
                                        $page_no_upper_case = strtolower($page_name_no_spaces);
                                        $page_no_ands = str_replace("&", "and", $page_no_upper_case);
                                        $clean_page_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $page_no_ands);

                                        if ($this->frontenddata->GetPageAlias($item_d['page_id'])) {
                                            foreach ($this->frontenddata->page_alias as $page_alias) {
                                                $url = "/" . $page_alias['page_alias'];
                                            }
                                        } else {

                                            $url = "/" . $clean_page_name . "/" . $item_d['page_id'];
                                        }
                                        ?>
                                        <a  href="<?= $url ?>"  class="rock-product-link">
                                            <span class="rollover" >                                                                            
                                            </span>
                                        </a>
                                        <?php ?>
                                        <img src="<?= $item_d['image_0'] ?>" class="rock-item-image">


                                    </div>

                                    <div class="row rock-item-captions">


                                        <p class="rock-item-name"><?= $item_d['item_name'] ?></p>  

                                    </div>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>

            </div>
        </div>

        <?php
    }

    public function GetCategories($page_type) {

        $sql = "SELECT `id`,`page_name`, `page_type`, `page_id` FROM `pages` WHERE `page_type` ='" . $page_type . "'";
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

    public function GetRamdomDataForHomePage($limit) {

        $home_page = array();



        $sql = "SELECT * FROM `home_page_products` WHERE `value` ='1' ORDER BY `page_order` ASC LIMIT {$limit}";

        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            $big_array = array();
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $page_info = array();
                $page_info['page_id'] = $row['page_id'];
                $page_info['item_details'] = array();

                $get_page_info = "SELECT * FROM `pages_products` WHERE `page_id` = '" . $row['page_id'] . "' LIMIT 1";
                $get_page_info_res = $this->_mysqli->query($get_page_info);
                $get_page_info_nums = $get_page_info_res->num_rows;
                if ($get_page_info_nums > 0) {

                    while ($item_detail = $get_page_info_res->fetch_array(MYSQLI_ASSOC)) {
                        $items = array();
                        $items['item_name'] = $item_detail['item_name'];
                        $items['page_id'] = $item_detail['page_id'];
                        $items['image_0'] = $item_detail['image_0'];
                        $items['image_1'] = $item_detail['image_1'];
                        $items['image_2'] = $item_detail['image_2'];
                        $items['image_3'] = $item_detail['image_3'];
                        $items['image_4'] = $item_detail['image_4'];
                        $items['image_5'] = $item_detail['image_5'];
                        $items['image_6'] = $item_detail['image_6'];
                        $items['image_7'] = $item_detail['image_7'];
                        $items['image_8'] = $item_detail['image_8'];
                        $items['brand'] = $item_detail['brand'];
                        $items['category'] = $item_detail['category'];
                        $items['page_parent'] = $item_detail['page_parent'];

                        array_push($page_info['item_details'], $items);
                    }
                }
                array_push($big_array, $page_info);
            }

            $this->random_data[] = $big_array;
            return $this->random_data;
        }
    }

    public function GetAllParentChildern($page_id) {

        $sql = "SELECT `id`, `page_name`, `page_type`, `page_id` FROM `pages` WHERE `page_type` = '8'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            $big_array = array();
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {


                $this->FindChildern($row['id']);
            }
        }
    }

    public function FindChildern($data) {

        $get_children = "SELECT `id`, `page_name`, `page_type`, `page_id`, `page_parent` FROM `pages` WHERE `page_parent` = '" . $data . "' ORDER BY `page_parent` ASC";
        $get_children_res = $this->_mysqli->query($get_children);
        $get_num_children = $get_children_res->num_rows;
        if ($get_num_children > 0) {
            while ($child = $get_children_res->fetch_array(MYSQLI_ASSOC)) {

                $children = array();
                $children['id'] = $child['id'];
                $children['page_name'] = $child['page_name'];
                $children['page_type'] = $child['page_type'];
                $children['page_id'] = $child['page_id'];
                $children['page_parent'] = $child['page_parent'];

                $this->FindChildern($child['id']);
                $this->parent_child[] = $child;
            }
            return $this->parent_child;
        }
    }

    public function GetTopParent($page_id) {
        $sql = "SELECT `id`, `page_name`, `page_type`, `page_parent`, `page_id` FROM `pages` WHERE `id` ='" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                if ($row['page_parent'] == 0) {
                    $this->top_parent[] = $row;
                }
                $this->GetTopParent($row['page_parent']);
            }
            return $this->top_parent;
        }
    }

}
