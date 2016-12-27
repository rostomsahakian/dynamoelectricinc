<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AddNewPage
 *
 * @author rostom
 */
class AddNewPage {

    public $_page_name;
    public $_page_type;
    public $_page_parent;
    private $_mysqli;
    private $_db;
    public $_message;
    public $query_result;
    public $queries;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->queries = new Queries();
        $this->CommandListerner();
        $this->CreatePagesTable();
        $this->CreatePageTypesTable();
    }

    public function AddNewPageManager() {
        ?>
        <div class="panel-heading">
            <h5><strong><i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;Add New Page&nbsp;&nbsp;&nbsp;&nbsp;<a href='' title="return" onclick="goBack()"><i class='fa fa-mail-reply'></i></a></strong></h5>
        </div>

        <!--Add New Page-->
        <div class="panel-body">
            <div class="col-md-12">

                <!--Add Page form goes here-->

                <?= $this->NewPageForm(); ?>
            </div>
        </div>
        <?php
    }

    /*
     * Add New Page
     */

    public function NewPageForm() {
        ?>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <?= $this->ReturnFormMessages(); ?>
            <form method="post">
                <div class="form-group">
                    <label>Page Name</label>
                    <input type="text" class="form-control" name="page_name" id="page-name" placeholder="Enter Page Name" value="<?= (isset($_REQUEST['page_name']) ? $_REQUEST['page_name'] : "") ?>"/>
                </div>
                <div class="form-group">
                    <label>Page Type</label>
                    <select name="page_type" class="form-control" id="page-type">
                        <option value="00">--Select Page Type--</option>
                        <?php
                        foreach ($this->SelectPage_type() as $page_types) {
                            ?>
                            <option value="<?= $page_types['page_type'] ?>"><?= $page_types['page_type'] . "/" . $page_types['type_name'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Page Parent</label>
                    <select name="page_parent[]" multiple="multiple" class="form-control" id="page-parent">
                        <option value="11">--Select Page Parent--</option>
                        <option value="0">None</option>

                        <?php
                        foreach ($this->GetAllPages() as $parents) {
                            ?>
                            <option value="<?= $parents['id'] ?>" ><?= $parents['page_name'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success" value="Add Page" name="do_add_page" id="do-add-page" />
                    <input type="submit" class="btn btn-primary" value="Add Page And Go" name="add_n_go" id="do-add-page" />

                </div>
            </form>
        </div>
        <?php
    }

    public function AddPageProcess() {
        if (isset($_REQUEST['do_add_page']) || isset($_REQUEST['add_n_go'])) {
            $this->_page_name = $_REQUEST['page_name'];
            $this->_page_type = $_REQUEST['page_type'];
            if (is_array($_REQUEST['page_parent'])) {
                $parents = implode(",", $_REQUEST['page_parent']);
                $this->_page_parent = $parents;
                var_dump($this->_page_parent);
            } else {
                $this->_page_parent = $_REQUEST['page_parent'];
            }

            $UUID = uniqid();
            /*
             * Check if all the required fields have been entered
             */
            if ($this->_page_name == "" && $this->_page_type == "00" && $this->_page_parent == "11") {
                $messages = array(
                    "All feilds are required. Error #0001"
                );
                $this->Messages($messages, "danger");
            } else if ($this->_page_name == "" || $this->_page_type == "00" || $this->_page_parent == "11") {
                $messages = array(
                    "All feilds are required. Error #0002."
                );
                $this->Messages($messages, "danger");
                /*
                 * Check for duplicate page names under same page parent 
                 */
            } else if ($this->CheckForDuplicates($this->_page_name, $this->_page_parent)) {
                $messages = array(
                    "The page name is a duplicate. Please choose another name for this page."
                );
                $this->Messages($messages, "danger");
            } else if ($this->_page_type == "1" && $this->CheckForSingleHomePage($this->_page_type)) {

                $messages = array(
                    "There can only be one home page."
                );
                $this->Messages($messages, "danger");
            } else if ($this->CheckParentName($this->_page_name, $this->_page_parent)) {
                $messages = array(
                    "The page name is a duplicate under same parent."
                );
                $this->Messages($messages, "danger");
            } else {
                $today = date("m/d/y");
                $sql = "INSERT INTO `pages` (page_name, page_type, page_parent, date_created, page_id) "
                        . "VALUES "
                        . "('" . $this->_page_name . "','" . $this->_page_type . "', '" . $this->_page_parent . "', '" . $today . "', '" . $UUID . "')";
                $insert_result = $this->_mysqli->query($sql);
                $this->CreateMetaDataTable();

                $add_meta_id = "INSERT INTO `page_meta_data` (page_id, date_modified) VALUES ('" . $UUID . "', '" . $today . "')";
                $meta_res = $this->_mysqli->query($add_meta_id);

                $this->CreateTableForSpecialCases();

                $add_info_to_special = "INSERT INTO `page_special` (page_id, date_modified) VALUES ('" . $UUID . "', '" . $today . "')";
                $special_cases = $this->_mysqli->query($add_info_to_special);

                $this->CreateTableForPageData();

                $add_page_info_to_content = "INSERT INTO `page_content` (page_id, date_added) VALUES ('" . $UUID . "', '" . $today . "')";
                $page_content_info_added = $this->_mysqli->query($add_page_info_to_content);

                if ($this->_page_type == 10) {
                    $insert_into_product_table = "INSERT INTO `pages_products` (item_name, page_parent, page_id, date_added ) VALUES ('" . $this->_page_name . "','" . $this->_page_parent . "','" . $UUID . "','" . $today . "')";
                    $insert_into_product_table_result = $this->_mysqli->query($insert_into_product_table);
                }

                $this->CreatePageUrlOptions($UUID, "long", $today);
                $this->CreatePageAliastable($UUID, $today);


                if (isset($_REQUEST['do_add_page'])) {

                    $messages = array(
                        "Page was added. Add another."
                    );
                    $this->Messages($messages, "success");
                } else {
                    header("Location: /public_html/rock_backend/?cmd=page_added&PUUID=" . $UUID . "&option=common");
                }
            }
        }
    }

    /*
     * Command Listerner
     */

    public function CommandListerner() {
        $this->AddPageProcess();
    }

    public function CreatePagesTable() {
        /*
         * Creates the pages table
         */
        $sql = "CREATE TABLE IF NOT EXISTS pages (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_name VARCHAR (250) NOT NULL,"
                . "page_type INT(10) NOT NULL,"
                . "page_parent INT(10) NOT NULL,"
                . "date_created DATETIME NOT NULL, "
                . "changefreq VARCHAR (50) NOT NULL, "
                . "priority FLOAT(2,1) NOT NULL, "
                . "page_id VARCHAR (250) NOT NULL"
                . "created_by VARCHAR (250) NOT NULL)";
        $results = $this->_mysqli->query($sql);
    }

    /*
     * Show the error message
     */

    public function Messages(array $messages, $type) {
        $this->_message = '<div class="alert alert-' . $type . '" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            
            <ul>';

        foreach ($messages as $message) {
            $this->_message .= '<li>' . $message . '</li>';
        }
        $this->_message .= '</ul></div>';
    }

    public function ReturnFormMessages() {
        return $this->_message;
    }

    /*
     * This function will create a table in the data base for page types
     * as of 06/07/2016 The page types are 
     * 1 = home page
     * 2 = contact us
     * 3 = about us
     * 4 = reviews
     * 5 = static pages 
     * 6 = Brands/Designers (Non-specific)
     * 7 = Brand or designer (specific)
     * 8 = Category
     * 9 = Sub Caregory
     * 10 = Product page
     */

    public function CreatePageTypesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS page_types"
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_type INT(10) NOT NULL,"
                . "type_name VARCHAR (250) NOT NULL,"
                . "short_description TEXT NOT NULL)";
        $create = $this->_mysqli->query($sql);

        if ($create) {
            /*
             * Now let's insert into the table the values
             * IF you want to add new page type do so from here...
             */
            $values_to_insert = array(
                "1" => array(
                    "page_type" => "1",
                    "type_name" => "Home page",
                    "short_description" => "Every website has a main page that people land on. There must only be one home page"
                ),
                "2" => array(
                    "page_type" => "2",
                    "type_name" => "Contact Us",
                    "short_description" => "It will have store or business information and a contact us form and a google map"
                ),
                "3" => array(
                    "page_type" => "3",
                    "type_name" => "About Us",
                    "short_description" => "It will give informative data about the company"
                ),
                "4" => array(
                    "page_type" => "4",
                    "type_name" => "Reviews",
                    "short_description" => "If you have any reviews from your customers you should use this. It will also include a write a review form. Users must sign in to write."
                ),
                "5" => array(
                    "page_type" => "5",
                    "type_name" => "Static Pages",
                    "short_description" => "A free form page that can include any html/css. it will be wrapped between the header and footer."
                ),
                "6" => array(
                    "page_type" => "6",
                    "type_name" => "Brands/Designers (Non-specific)",
                    "short_description" => "This page type will show all the designers or brands that you carry. It is the collection of them.()"
                ),
                "7" => array(
                    "page_type" => "7",
                    "type_name" => "Brand or designer (specific)",
                    "short_description" => "This were you will declare the specific brand or designer page."
                ),
                "8" => array(
                    "page_type" => "8",
                    "type_name" => "Category",
                    "short_description" => "Catergory pages"
                ),
                "9" => array(
                    "page_type" => "9",
                    "type_name" => "Sub Category",
                    "short_description" => "Child of category"
                ),
                "10" => array(
                    "page_type" => "10",
                    "type_name" => "Product",
                    "short_description" => "A Product page"
                ),
                "11" => array(
                    "page_type" => "11",
                    "type_name" => "Hidden",
                    "short_description" => "A page that will not show in the top naviagtion and is static"
                ),
                "12" => array(
                    "page_type" => "12",
                    "type_name" => "Categories(None-Product)",
                    "short_description" => "This is a page type that can have children but no products."
                ),
                "13" => array(
                    "page_type" => "13",
                    "type_name" => "Policies",
                    "short_description" => "A Policies page type with hidden properties. It will only show in the footer."
                ),
                "14" => array(
                    "page_type" => "14",
                    "type_name" => "FAQs",
                    "short_description" => "A frequently asked question page."
                ),
            );
            /*
             * Now insert but first check if they are already inserted
             */

            foreach ($values_to_insert as $k => $value) {

                $sql = "SELECT * FROM `page_types` WHERE `page_type` = '" . $value['page_type'] . "' ";
                $s_result = $this->_mysqli->query($sql);
                $num_rows = $s_result->num_rows;

                if ($num_rows > 0) {
                    continue;
                } else {
                    $sql = "INSERT INTO `page_types` (page_type, type_name, short_description) "
                            . "VALUES "
                            . "('" . $value['page_type'] . "','" . $value['type_name'] . "','" . $value['short_description'] . "')";
                    $insert_res = $this->_mysqli->query($sql);
                }
            }
        }
    }

    public function SelectPage_type() {
        $sql = "SELECT * FROM `page_types`";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->query_result[] = $row;
        }
        return $this->query_result;
    }

    public function CheckForDuplicates($page_name, $page_parent) {

        $sql = "SELECT * FROM pages WHERE `page_name` = '" . $page_name . "' AND `page_parent` = '" . $page_parent . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function CheckForSingleHomePage($page_type) {
        $sql = "SELECT * FROM pages WHERE `page_type` = '1'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;

        if ($num_rows == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function GetAllPages() {

        $data = array(
            "table" => "pages",
            "options" => 0
        );

        $this->queries->_res = NULL;
        $get_pages = $this->queries->Selection_queries($data);
        $get_pages = $this->queries->DoReturn();
        return $get_pages;
    }

    public function CheckParentName($page_name, $page_parent) {

        $sql = "SELECT * FROM `pages` WHERE `id` = '" . $page_parent . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                if ($row['page_name'] == $page_name) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function CreateMetaDataTable() {

        $sql = "CREATE TABLE IF NOT EXISTS page_meta_data "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_id VARCHAR (250) NOT NULL,"
                . "page_title VARCHAR (500) NOT NULL,"
                . "meta_data VARCHAR (500) NOT NULL,"
                . "description TEXT,"
                . "date_modified VARCHAR(100) NOT NULL"
                . ")";
        $results = $this->_mysqli->query($sql);
    }

    public function CreateTableForSpecialCases() {

        $sql = "CREATE TABLE IF NOT EXISTS page_special "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_id VARCHAR (250) NOT NULL,"
                . "home_page ENUM('true','false') NOT NULL Default 'false',"
                . "hidden ENUM('true','false') NOT NULL Default 'false',"
                . "date_modified VARCHAR (100) NOT NULL"
                . ")";

        $create = $this->_mysqli->query($sql);
    }

    public function CreateTableForPageData() {
        $sql = "CREATE TABLE IF NOT EXISTS page_content "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_id VARCHAR (250) NOT NULL,"
                . "page_content TEXT,"
                . "date_added VARCHAR(100),"
                . "modified_by VARCHAR (250) NULL "
                . ")";
        $create = $this->_mysqli->query($sql);
    }

    public function CreatePageUrlOptions($page_id, $default, $date_m) {
        $sql = "CREATE TABLE IF NOT EXISTS page_url_option "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_id VARCHAR (250) NOT NULL,"
                . "url_option VARCHAR (10) NOT NULL,"
                . "date_modified VARCHAR (50)"
                . ")";
        $create = $this->_mysqli->query($sql);
        /*
         * Insert Initial which by default is set to long url
         */
        $insert_sql = "INSERT INTO `page_url_option` (page_id, url_option, date_modified) "
                . "VALUES "
                . " ('" . $page_id . "', '" . $default . "', '" . $date_m . "' )";
        $insert_res = $this->_mysqli->query($insert_sql);
    }

    public function CreatePageAliastable($page_id, $date_a) {
        $sql = "CREATE TABLE IF NOT EXISTS page_alias "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_id VARCHAR (250) NOT NULL,"
                . "page_alias TEXT,"
                . "date_added VARCHAR (50),"
                . "date_modified VARCHAR (50)"
                . ")";
        $create = $this->_mysqli->query($sql);
        /*
         * Insert Initial value
         */
        $insert_sql = "INSERT INTO `page_alias` (page_id, date_added) VALUES ('" . $page_id . "' , '" . $date_a . "')";
        $insert_res = $this->_mysqli->query($insert_sql);
    }

}
