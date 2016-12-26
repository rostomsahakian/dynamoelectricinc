<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Navigation
 *
 * @author rostom
 */
class Navigation {

    private $_mysqli;
    private $_db;
    public $parents;
    public $queries;
    public $children;
    public $page_alias;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->queries = new Queries();
    }

    public function TopNavigation() {
        ?>
        <!-- Static navbar -->
        <nav class="navbar navbar-default navbar-static-top rock-nav-default">
            <div class="container">

                <div  class="navbar-collapse collapse">
                    <ul class="nav navbar-nav rock-nav-overwrite">
                        <?php
                        if (isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == "logout") {
                            $logout = new accounts();
                            $logout->logout();
                        }
                        if (isset($_SESSION['user_log'])) {
                            $top_nav_sign_in = "logout";
                            $url = "/account?cmd=logout&s=" . md5($_COOKIE['order']);
                            $my_account = '  <li><a href="/account?cmd=user-acount&s=' . $_SESSION['user_log'] . '">My Account</a></li>';
                            $create = "";

                        } else {
                            $top_nav_sign_in = "Sign in";
                            $cookie = isset($_COOKIE['order']) ? isset($_COOKIE['order']) : '';
                            $url = "/account?cmd=login&ssid=" . md5($cookie);
                            $my_account = "";
                            $create = '  <li><a href="/acount?cmd=create&ssid=' . md5(isset($_COOKIE['order']) ? $_COOKIE['order'] : '') . '">Create Account</a></li>';
                        }
                        ?>
                        <li><a href="<?= $url ?>"><?= $top_nav_sign_in ?></a></li>
                        <?= $my_account ?>
                        <?= $create ?>
                        <?php
                        if (isset($_SESSION['wholesaler_on'])) {
                            ?>
                            <li style="background-color: #449D44; color: #fff; float: right;"><a>Wholesaler</a></li>
                            <?php
                        }
                        ?>

                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <?php
    }

    public function GetNavUrls() {


        $categories = array();

        $get_parents = "SELECT * FROM `pages` WHERE `page_parent` = '0' AND `page_type` != '11' AND `page_type` != '13'  ORDER BY `page_order` ASC";
        $get_parents_res = $this->_mysqli->query($get_parents);
        $num_rows = $get_parents_res->num_rows;
        if ($num_rows > 0) {
            while ($get_parents_rows = $get_parents_res->fetch_array(MYSQLI_ASSOC)) {
                $this->parents[] = $get_parents_rows;
                foreach ($this->parents as $parent) {


                    $category = array();

                    $category['id'] = $parent['id'];
                    $category['name'] = $parent['page_name'];
                    $category['parent'] = $parent['page_parent'];
                    $category['type'] = $parent['page_type'];
                    $category['page_id'] = $parent['page_id'];
                    if ($this->HasChild($parent['id'])) {
                        $category['sub_categories'] = array();
                    }

                    $get_children = "SELECT * FROM `pages` WHERE `page_parent` ='" . $parent['id'] . "' ORDER BY `page_order` ASC";
                    $get_children_res = $this->_mysqli->query($get_children);
                    $get_child_num_rows = $get_children_res->num_rows;
                    if ($get_child_num_rows > 0) {
                        while ($get_children_rows = $get_children_res->fetch_array(MYSQLI_ASSOC)) {
                            $this->children[] = $get_children_rows;
                            // if ($parent['id'] != "5") {
                            foreach ($this->children as $child) {
                                if ($parent['id'] == $child['page_parent']) {

                                    $subcat = array();
                                    $subcat['id'] = $child['id'];
                                    $subcat['name'] = $child['page_name'];
                                    $subcat['parent'] = $child['page_parent'];
                                    $subcat['page_id'] = $child['page_id'];


//                                        $subcat['promo_images'] = array();
//
//                                        $this->queries->_res = NULL;
//                                        $get_promo_images = $this->queries->GetData("promotional_images", "page_id", $parent['id'], "0");
//                                        $get_promo_images = $this->queries->RetData();
//                                        // var_dump($get_promo_images);
//                                        if ($get_promo_images != NULL) {
//                                            foreach ($get_promo_images as $promo_images) {
//
//
//                                                array_push($subcat['promo_images'], $promo_images);
//                                            }
//                                        }
                                }
                            }
                            array_push($category['sub_categories'], $subcat);
                        }

                        // }
                    }
                }
                array_push($categories, $category);
            }

            return $categories;
        }
    }

    /*
     * Checks to see if the given value has any children retuns true or false
     */

    public function HasChild($parent_id) {

        $data_to_ftech = array(
            "table" => "pages",
            "field" => "page_parent",
            "value" => $parent_id
        );


        $this->queries->_res = NULL;
        $get_children = $this->queries->findChildren($data_to_ftech, $option = 2);
        $get_children = $this->queries->DoReturn();
        $this->_children[] = $get_children;
        if (count($get_children) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function MegaNavigationMenu() {
        ?>
        <!-- Static navbar -->
        <nav class="navbar navbar-default megamenu">
            <div class="container-fluid">
                <div class="col-sm-12">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <?php
                            foreach ($this->GetNavUrls() as $no_child) {
                                /*
                                 * If the key does not exsist 
                                 */
                                if (!array_key_exists("sub_categories", $no_child)) {

//                                    $this->GetUrl($no_child['id']);
//                                    if ($no_child['parent'] == 0 || $no_child['parent'] == "0") {
//                                        $page_id_ext = "";
//                                    } else {
//                                        $page_id_ext = "/" . $no_child['id'];
//                                    }

                                    $no_child_spaces = str_replace(" ", "-", strtolower($no_child['name']));
                                    $no_child_ands = str_replace("&", "and", $no_child_spaces);
                                    $no_child_alias = preg_replace('/[^a-zA-Z0-9,-]/', '-', $no_child_ands);
                                    $this->page_alias = NULL;
                                    if ($this->CheckIfPageHasAlias($no_child['page_id'])) {

                                        $url = "/" . $this->page_alias[0]['page_alias'];
                                    } else {
                                        $url = "/" . $no_child_alias . "/" . $no_child['id'];
                                    }
                                    ?>
                                    <li ><a href="<?= $url ?>"><?= $no_child['name'] ?></a></li>
                                    <?php
                                } else {
                                    ?>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-animations="fadeIn" role="button" aria-haspopup="true" aria-expanded="false"><?= $no_child['name'] ?><span class="caret"></span></a>

                                        <ul class="dropdown-menu rock-mega-menu">
                                            <div class="rock-drop-down-header-div">
                                                <h4 class="rock-drop-down-header">
                                                    <?php
//                                                    $this->GetUrl($no_child['id']);
                                                    $with_child_spaces = str_replace(" ", "-", strtolower($no_child['name']));
                                                    $with_child_ands = str_replace("&", "and", $with_child_spaces);
                                                    $with_child_alias = preg_replace('/[^a-zA-Z0-9,-]/', '-', $with_child_ands);
                                                    $this->page_alias = NULL;
                                                    if ($this->CheckIfPageHasAlias($no_child['page_id'])) {
                                                        $url_w_child = "/" . $this->page_alias[0]['page_alias'];
                                                    } else {
                                                        $url_w_child = "/" . $with_child_alias . "/" . $no_child['id'];
                                                    }
                                                    ?>


                                                    <a href="<?= $url_w_child ?>" title="<?= $no_child['name'] ?>" alt="<?= $no_child['name'] ?>">
                                                        <?php
                                                        echo $no_child['name'];
                                                        ?>
                                                    </a>
                                                </h4>
                                            </div>

                                            <?php
                                            $sorted_children = array();
                                            foreach ($no_child['sub_categories'] as $children) {

                                                $child_id = $children['id'];
                                                $child_parent = $children['parent'];
                                                $child_name = $children['name'];
                                                $page_id = $children['page_id'];
                                                //$promo_image = $children['promo_images'];
                                                array_push($sorted_children, array("id" => $child_id, "parent" => $child_parent, "name" => $child_name, "image_name" => "", "page_id" => $page_id));
                                            }
                                            /*
                                             * Sorts children
                                             */
                                            // usort($sorted_children, array($this, "compare_name"));
                                            ?>
                                            <!--                                            <div class="row">-->
                                            <div class="col-sm-12 rock-drop-down-small-container">
                                                <?php
                                                foreach ($sorted_children as $child) {
                                                    ?>
                                                    <!--                                                <div class="col-sm-12">-->

                                                    <li>

                                                        <div class="col-sm-6 rock-drop-down-link">
                                                            <?php
//                                                            $this->GetUrl($child['id']);
//                                                            if ($child['parent'] == 0 || $child['parent'] == "0") {
//                                                                $page_id_ext = "";
//                                                            } else {
//                                                                $page_id_ext = "/" . $child['id'];
//                                                            }
                                                            $child_spaces = str_replace(" ", "-", strtolower($child['name']));
                                                            $child_ands = str_replace("&", "and", $child_spaces);
                                                            $child_alias = preg_replace('/[^a-zA-Z0-9,-]/', '-', $child_ands);
                                                            $this->page_alias = NULL;
                                                            if ($this->CheckIfPageHasAlias($child['page_id'])) {

                                                                $child_url = "/" . $this->page_alias[0]['page_alias'];
                                                            } else {
                                                                $child_url = "/" . $with_child_alias . "/" . $child_alias . "/" . $child['id'];
                                                            }
                                                            ?>
                                                            <a href="<?= $child_url ?>"><i class="fa fa-angle-double-right" aria-hidden="true"></i>&nbsp;<?= $child['name'] ?></a>

                                                        </div>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="col-sm-6 rock-nav-side-image">
                                                <?php
//                                                if ($child['image_name'] != NULL) {
//                                                    $image_name = $child['image_name'][0]['image_name'];
//                                                    $image_path = $child['image_name'][0]['image_path'];
                                                ?>

                                                <?php
//                                                }
                                                ?>
                                            </div>
                                            <!--                                            </div>-->
                                        </ul>
                                        <!--                                        </div>-->
                                        <?php
                                    }
                                }
                                ?>

                            </li>
                        </ul>
                    </div>
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </nav>

        <?php
    }

    public function CheckIfPageHasAlias($page_id) {

        $sql = "SELECT `page_id`, `page_alias` FROM `page_alias` WHERE  `page_id` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                if ($row['page_alias'] == NULL) {
                    return false;
                } else {
                    $this->page_alias[] = $row;
                }
                return $this->page_alias;
            }
        }
    }

}
