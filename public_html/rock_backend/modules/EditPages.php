<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditPages
 *
 * @author rostom
 */
class EditPages {

    private $_mysqli;
    private $_db;
    public $pages;
    public $edit_page;
    public $messages = array();
    public $flag = 0;
    public $alert_class;
    public $children;
    public $count;
    public $res = array();
    public $childcount;
    public $is_on_home_page;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->edit_page = new CreatePageForm();
        $this->CreateSocialMediatable();
        $this->CreateStoreInfotable();
        $this->CreateNewsLetterTable();
        $this->CreatetableHomepageproducts();
    }

    public function EditPageManager() {
        if (isset($_REQUEST['delete_page'])) {
            $this->AskQuestion();
        }
        if (isset($_REQUEST['update_order'])) {
            $page_id_order = $_REQUEST['page_id_order'];
            $page_order = isset($_REQUEST['page_order']) ? $_REQUEST['page_order'] : $page_to_edit['page_order'];
            $page_parent = $_REQUEST['page_parent'];
            $this->UpdatePageOrder($page_id_order, $page_order, $page_parent);
        }
        if (isset($_REQUEST['is_on_home'])) {
            if (!array_key_exists("on_home_page", $_REQUEST)) {
                $_REQUEST['on_home_page'] = "0";
            }
            $this->InsertHomeProductsIn($_REQUEST);
        }
        ?>
        <?php
        if ($this->flag == 1) {
            ?>

            <div class="alert alert-<?= $this->alert_class ?> alert-dismissible" role="alert">
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
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h5><strong><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit Page Manager&nbsp;&nbsp;&nbsp;&nbsp;<a href='' onclick="goBack()"><i class='fa fa-mail-reply'></i></a></strong></h5>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control input-sm" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-sm" type="button">Go!</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">


                <table class="table table-bordered table-responsive">
                    <tr>
                        <th>Order</th>
                        <th>On Home?</th>
                        <th>Page Id</th>
                        <th>Page Name</th>
                        <th>Page Parent</th>
                        <th>Page Type</th>
                        <th>Children</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                    <?php
                    if (isset($_REQUEST['see_children']) && $_REQUEST['see_children'] == "true") {
                        $p_id = $_GET['page_id'];
                    } else {
                        $p_id = "0";
                    }
                    if ($this->CheckPageExsists($p_id)) {
                        $all_top_pages = $this->GetAllPageForEditing($p_id);
                        foreach ($all_top_pages as $page_to_edit) {
                            ?>
                            <tr>
                                <td>
                                    <!--Page Order-->

                                    <form method="post">

                                        <div class="col-md-4">
                                            <input type="text" name="page_order" value="<?= $page_to_edit['page_order'] ?>" class="form-control input-sm"/>

                                        </div>
                                        <div class="col-md-4">
                                            <input type="hidden" name="page_parent" value="<?= $page_to_edit['page_parent'] ?>" />
                                            <input type="hidden" name="page_id_order" value="<?= $page_to_edit['id'] ?>" />
                                            <input type="submit" name="update_order" value="Update" class="btn btn-success btn-xs"/>
                                        </div>

                                    </form>
                                </td>
                                <td>
                                    <?php 
                                    if($page_to_edit['page_type'] == "10"){
                                    ?>
                                    <form method="post">
                                        <div class="col-md-4">
                                            <?php
                                            $checked = '';
                                            if ($this->GetDataFromHomePageProducts($page_to_edit['page_id'])) {
                                                foreach ($this->is_on_home_page as $home_page_item) {
                                                    $req = isset($_REQUEST['on_home_page'])? "0" : "0";
                                                    //$hp = isset($_REQUEST['on_home_page']) ? "0" : $home_page_item['value'];


                                                    if ($home_page_item['value'] == "1") {

                                                        $checked = 'checked="checked"';
                                                    } else {
                                                        $checked = '';
                                                    }
                                                }
                                            }
                                            ?>


                                            <input type="checkbox" name="on_home_page"  value="1" <?= $checked ?>/>


                                        </div>
                                        <div class="col-md-4">
                                            <input type="hidden" name="home_page_item_order" value="<?= $page_to_edit['page_order'] ?>"/>
                                            <input type="hidden" name="home_page_id" value="<?= $page_to_edit['page_id'] ?>"/>
                                            <input type="submit" name="is_on_home" value="Set" class="btn btn-primary btn-xs"/>
                                        </div>
                                    </form> 
                                    <?php
                                    }else{
                                        echo "n/a";
                                    }
                                    ?>

                                </td>
                                <td><?= $page_to_edit['page_id'] ?></td>
                                <td><?= $page_to_edit['page_name'] ?></td>
                                <td><?= $page_to_edit['page_parent'] ?></td>
                                <td><?= $page_to_edit['page_type'] ?></td>
                                <td>
                                    <?php
                                    $this->res = NULL;
                                    $count_child = $this->GetNumberOfChildrenPerParent($page_to_edit['id']);
                                    $count_child = $this->ReturnNumChild();
                                    //var_dump($count_child);
                                    //$grand_children = $this->GetCountOfproductPages($page_to_edit['id']);
                                    // var_dump($grand_children);
                                    if (count($count_child) > 0) {
                                        ?>
                                        <a href="/public_html/rock_backend/?cmd=edit-page&PUUID=<?= $page_to_edit['page_id'] ?>&page_id=<?= $page_to_edit['id'] ?>&see_children=true">
                                            <?= count($count_child); ?>
                                        </a>
                                        <?php
                                    } else {
                                        echo count($count_child);
                                        ?>
                                    </td>
                                    <?php
                                }
                                ?>




                                <td><?php $this->CallEditPageFunction($page_to_edit['page_id']); ?></td>
                                <td><?php $this->DeleteApage($page_to_edit['page_id'], $page_to_edit['id']); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>


                </div>
            </div>
            <?php
        } else {
            $this->flag = 1;
            unset($this->messages);
            $this->messages = array();
            $message = array("1" => "<a href='/?cmd=edit-page'>Retun to Select edit<i class='fa fa-hand-o-left'><i></a>");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        }
    }

    public function GetAllPageForEditing($page_id) {
        $sql = "SELECT * FROM `pages` WHERE `page_parent` = '" . $page_id . "' ORDER BY page_order ASC";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($result && $num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->pages[] = $row;
            }
            return $this->pages;
        } else {
            return FALSE;
        }
    }

    public function CheckPageExsists($page_id) {
        $sql = "SELECT * FROM `pages` WHERE `page_parent` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Check if other page under the same parent has the same order
     * if yes
     * update the order for the new page order
     * then update the order for the other remaining ones by incrementing 
     * else
     * just update that page order
     */

    public function UpdatePageOrder($page_id, $page_order, $page_parent) {

        $check_order_taken = "SELECT `page_order`, `page_id`, `page_parent` FROM `pages` WHERE "
                . "`page_parent` = '" . $page_parent . "'"
                . " AND `page_order` ='" . $page_order . "' "
                . "AND `id` != '" . $page_id . "'";

        $check_result = $this->_mysqli->query($check_order_taken);
        $num_rows = $check_result->num_rows;

        if ($num_rows > 0) {
            $this->res = NULL;
            while ($rows = $check_result->fetch_array(MYSQLI_ASSOC)) {
                $this->res[] = $rows;


                /*
                 * Here you will get the matches if there are any 
                 */

                $sql = "UPDATE `pages` SET `page_order` = '" . $page_order . "' WHERE `id` = '" . $page_id . "'";
                $result = $this->_mysqli->query($sql);
            }


            foreach ($this->ReturnNumChild() as $k => $row) {

                $increment_old_order = (int) $row['page_order'] + $k + 1;
                $update_old_order = "UPDATE `pages` SET `page_order` = '" . $increment_old_order . "' WHERE `page_id` = '" . $row['page_id'] . "'";
                $update_result = $this->_mysqli->query($update_old_order);
            }
        } else {

            $sql = "UPDATE `pages` SET `page_order` = '" . $page_order . "' WHERE `id` = '" . $page_id . "'";
            $result = $this->_mysqli->query($sql);
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function CallEditPageFunction($page_id) {
        ?>
        <form method="get">
            <input type="submit" class="btn btn-primary btn-xs" name="edit_page" value="Edit"/>
            <input type="hidden" name="cmd" value="edit_page"/>
            <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>
            <input type="hidden" name="option" value="common" />
        </form>
        <?php
        if (isset($_REQUEST['edit_page'])) {
            $this->CallEditPageFunction($page_id);
        }
    }

    public function DeleteApage($page_uuid, $page_id) {
        ?>
        <form method="post">
            <input type="submit" class="btn btn-danger btn-xs" name="delete_page" value="Delete"/>
            <input type="hidden" name="cmd" value="edit-page"/>
            <input type="hidden" name="PUUID" value="<?= $page_uuid ?>"/>
            <input type="hidden" name="page_id" value="<?= $page_id ?>"/>
            <?php
            if (isset($_REQUEST['see_children'])) {
                ?>
                <input type="hidden" name="see_children" value="true" />
                <?php
            }
            ?>




        </form>  
        <?php
    }

    public function AskQuestion() {
        $this->flag = 1;
        $message = array(
            "1" => "<form method='post'>"
            . "<p>Are your sure you want to delete this page. by deleteing this page you will also delete any pages associated with this page.</p>"
            . "<div class='form-group'>"
            . "<input type='submit' value='yes' name='yes_del' class='btn btn-warning btn-xs' style='margin-right:5px;'/>"
            . "<input type='submit' value='no' name='no_dont' class='btn btn-warning btn-xs'/> "
            . "<input type='hidden' value='{$_REQUEST['page_id']}' name='page_id'/>"
            . "<input type='hidden' value='{$_REQUEST['PUUID']}' name='PUUID'/>"
            . "<input type='hidden' value='edit-page' name='cmd'/>"
            . "<input type='hidden' name='delete_page' value='Delete'/>"
            . "</div>"
            . "</form>"
        );
        $this->alert_class = "warning";
        array_push($this->messages, $message);
        if (isset($_REQUEST['yes_del'])) {

            $this->DoDeletePage($_REQUEST['page_id'], $_REQUEST['PUUID']);
        } else if (isset($_REQUEST['no_dont'])) {
            unset($this->messages);
            $this->flag = 0;
        }
    }

    public function DoDeletePage($page_id, $page_uuid) {

        /*
         * Delete all the Children's data including if there are any images or files associated with them
         */
        $tables_to_delete = array(
            "pages",
            "page_alias",
            "page_content",
            "page_images",
            "page_meta_data",
            "page_special",
            "page_url_option",
            "pages_products"
        );
        $folder_path = FE_IMAGES . "page_id_" . $page_uuid . "_images";
        if ($this->CheckForChildren($page_id) != false) {

            /*
             * Delete page
             */
            for ($j = 0; $j < count($tables_to_delete); $j++) {
                $sql = "DELETE FROM `" . $tables_to_delete[$j] . "` WHERE `page_id` = '" . $page_uuid . "'";
                $del_result = $this->_mysqli->query($sql);
            }

            /*
             * Deletes Images folder
             */
            $this->DeleteFolder($folder_path);
            unset($this->messages);
            $this->messages = array();
            $this->flag = 1;
            $message = array(
                "1" => "All pages under this page were deleted as well."
            );
            array_push($this->messages, $message);
            $this->alert_class = "success";
            /*
             * Delete its children as well
             */
            foreach ($this->CheckForChildren($page_id) as $child_page) {
                /*
                 * Deletes Images folder
                 */
                $image_path = FE_IMAGES . "page_id_" . $child_page['page_id'] . "_images";

                $this->DeleteFolder($image_path);

                for ($i = 0; $i < count($tables_to_delete); $i++) {
                    $sql = "DELETE FROM `" . $tables_to_delete[$i] . "` WHERE `page_id` = '" . $child_page['page_id'] . "'";
                    $result = $this->_mysqli->query($sql);
                }
            }
        } else {
            /* No child
             * Only Delete The single page
             */

            for ($k = 0; $k < count($tables_to_delete); $k++) {
                $sql = "DELETE FROM `" . $tables_to_delete[$k] . "` WHERE `page_id` = '" . $page_uuid . "'";
                $del_result = $this->_mysqli->query($sql);
            }
            /*
             * Deletes Images folder
             */
            $this->DeleteFolder($folder_path);
            unset($this->messages);
            $this->messages = array();
            $this->flag = 1;
            $message = array(
                "1" => "page deleted."
            );
            array_push($this->messages, $message);
            $this->alert_class = "success";
        }
    }

    public function CheckForChildren($page_id) {

        $sql = "SELECT * FROM `pages` WHERE `page_parent` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        $num_row = $result->num_rows;
        if ($num_row < 1) {
            return false;
        } else {

            if ($result) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $this->children[] = $row;
                    $this->CheckForChildren($row['id']);
                }
                return $this->children;
            }
        }
    }

    public function GetNumberOfChildrenPerParent($page_id) {

        $sqli = "SELECT * FROM `pages` WHERE `page_parent` = '" . $page_id . "'";
        $results = $this->_mysqli->query($sqli);
        $num_rows = $results->num_rows;
        if ($num_rows > 0) {

            while ($rows = $results->fetch_array(MYSQLI_ASSOC)) {
                $parents = $rows['id'];
                $this->res[] = $rows;
                $this->GetNumberOfChildrenPerParent($parents);
            }
        }
    }

    public function ReturnNumChild() {
        return $this->res;
    }

//    public function GetCountOfproductPages($children) {
//        if ($children != NULL) {
//            $this->childcount = array();
//            $sql = "SELECT `id`,`page_name`,`page_parent` FROM `pages`";
//
//            $result = $this->_mysqli->query($sql);
//            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
//
//                if (strpos($row['page_parent'], ",")) {
//                    $parent = explode(",", $row['page_parent']);
//                } else {
//                    $parent = $row['page_parent'];
//          
//                }
//
//
//                for ($i = 0; $i < count($parent); $i++) {
//                   
//                    $this->childcount[] = $parent[$i];
//                    
//                }
//            }
//            return $this->childcount;
//        }
//    }

    public function DeleteFolder($path) {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                $this->DeleteFolder(realpath($path) . '/' . $file);
            }

            return rmdir($path);
        } else if (is_file($path) === true) {
            return unlink($path);
        }

        return false;
    }

    public function CreateSocialMediatable() {
        $sql = "CREATE TABLE IF NOT EXISTS social_media"
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "url VARCHAR(500) NOT NULL,"
                . "image_url VARCHAR(500) NOT NULL,"
                . "image_name VARCHAR (250) NOT NULL,"
                . "status INT(2) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateStoreInfotable() {
        $sql = "CREATE TABLE IF NOT EXISTS store_info"
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "store_name VARCHAR (500) NOT NULL,"
                . "address_1 VARCHAR (500) NOT NULL,"
                . "address_2 VARCHAR (25) NOT NULL,"
                . "city VARCHAR (100) NOT NULL,"
                . "country VARCHAR (100) NOT NULL,"
                . "state VARCHAR (100) NOT NULL,"
                . "zip VARCHAR (100) NOT NULL,"
                . "phone1 VARCHAR (100) NOT NULL,"
                . "phone2 VARCHAR (100) NOT NULL,"
                . "fax VARCHAR (100) NOT NULL,"
                . "google_maps TEXT,"
                . "email VARCHAR (250) NOT NULL,"
                . "cust_email VARCHAR (250) NOT NULL,"
                . "store_hours VARCHAR (250) NOT NULL,"
                . "holiday_hours VARCHAR (250) NOT NULL,"
                . "closed_on VARCHAR (250) NOT NULL,"
                . "primary INT (2) NOT NULL,"
                . "date_added VARCHAR (50) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateNewsLetterTable() {
        $sql = "CREATE TABLE IF NOT EXISTS rock_newsletter"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "email VARCHAR (250) NOT NULL,"
                . "date_added VARCHAR (50) NOT NULL"
                . " )";
        $result = $this->_mysqli->query($sql);
    }

    public function CreatetableHomepageproducts() {
        $sql = "CREATE TABLE IF NOT EXISTS home_page_products"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_id VARCHAR (250) NOT NULL,"
                . "page_order INT(10) NOT NULL,"
                . "value INT (0) NOT NULL,"
                . "date_added VARCHAR (50) NOT NULL"
                . " )";
        $result = $this->_mysqli->query($sql);
    }

    public function InsertHomeProductsIn($data) {
        $check = "SELECT `page_id` FROM `home_page_products` WHERE `page_id` ='" . $data['home_page_id'] . "' ";

        $check_res = $this->_mysqli->query($check);
        $num_rows = $check_res->num_rows;
        if ($num_rows > 0) {
            $update = "UPDATE `home_page_products` SET `value` = '" . $data['on_home_page'] . "', `page_order` ='".$data['home_page_item_order']."'  WHERE `page_id` = '" . $data['home_page_id'] . "'";

            $update_res = $this->_mysqli->query($update);
        } else {
            $sql = "INSERT INTO `home_page_products` (page_id, page_order, value, date_added) VALUES ('" . $data['home_page_id'] . "', '".$data['home_page_item_order']."', '" . $data['on_home_page'] . "', '" . date('d/m/y') . "')";
            $result = $this->_mysqli->query($sql);
        }
    }

    public function GetDataFromHomePageProducts($data) {
        $sql = "SELECT * FROM `home_page_products` WHERE `page_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->is_on_home_page[] = $row;
            }
            return $this->is_on_home_page;
        }
    }

}
