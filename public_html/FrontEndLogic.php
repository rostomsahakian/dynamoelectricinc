<?php
/*
 * This class will decide how to load each pages in the front end
 *
 * @author rostom
 */

class FrontEndLogic {

    private $_mysqli;
    private $_db;
    public $page_data;
    public $breadCrumb;
    public $social_media;
    public $all_top_pages;
    public $all_hidden_pages;
    public $page_alias;
    public $store_info;
    public $store_logo;
    public $serarch_func;
    public $cart_num = 0;
    public $update_q;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->CreateTableForCSSPerPageType();
        $this->CreateTableForContactUs();
        $this->CreateTableForlogo();
        $this->CreateTableForFAQS();
        $this->CreateStoreOrders();
        $this->CreateTableStoreOrderItems();
        $this->CreateTableStates();
        $this->CreateTableCheckedOut();
        /*
         * Forntend user tables
         */
        $this->CreateFrontEndUserAddressTable(); //1
        $this->CreateFrontEndUserEmailtable(); //2
        $this->CreateFrontEndUserPurchaseHistoryTable(); //3
        $this->CreateFrontEndUsersStatusTable(); //4
        $this->CreateFrontEndUsersTable(); //5
        $this->CreateFrontEnduserPhoneFaxtable(); //6

        if (defined('WHOLESALE') && WHOLESALE == 1) {
            $this->CreateWholeSaleUserTable();
            $this->CreateWholesalerStatus();
        }
        /*
         * e-commerce shipping info
         */
        $this->CreateShippingInfoTable();
    }

    public function CheckIfPageHasAlias($data) {

        $sql = "SELECT `page_id`, `page_alias` FROM `page_alias` WHERE `page_alias` ='" . $data . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                if ($row['page_alias'] == NULL) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    public function GetPageDataByAlias($data) {
        $get_page_id = "SELECT `page_id`,`page_alias` FROM `page_alias` WHERE `page_alias` = '" . $data . "'";
        $get_page_id_result = $this->_mysqli->query($get_page_id);

        while ($row = $get_page_id_result->fetch_array(MYSQLI_ASSOC)) {

            $sql = "SELECT `id` FROM `pages` WHERE `page_id` ='" . $row['page_id'] . "'";
            $result = $this->_mysqli->query($sql);
            while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->GetpageDataByID($data['id']);
            }
        }
    }

    public function GetpageDataByID($data, $option = NULL) {
        /*
         * Get All The needed data from tables
         * 1. pages => get all info specially the page_id
         * 2. page_alias
         * 3. page_content
         * 4. page_images
         * 5. page_meta_data
         * 6. page_special
         * 7. page_url_option
         * 8. pages_products if page type is 10
         */
        if (preg_match('/^[0-9]{1,}$/', $data)) {
            $field = "id";
        } else {
            $field = "page_id";
        }

        $sql = "SELECT `id`, `page_name`, `page_type`, `page_parent`,`page_order`, `page_id` FROM `pages` WHERE `" . $field . "` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        $page_big_data = array();
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $page_data = array();

                $page_data['id'] = $row['id'];
                $page_data['page_name'] = $row['page_name'];
                $page_data['page_type'] = $row['page_type'];
                $page_data['page_parent'] = $row['page_parent'];
                $page_data['page_order'] = $row['page_order'];
                $page_data['page_id'] = $row['page_id'];

                $get_page_alias = "SELECT `page_alias` FROM `page_alias` WHERE `page_id` = '" . $row['page_id'] . "'";
                $get_page_alias_result = $this->_mysqli->query($get_page_alias);
                while ($page_alias = $get_page_alias_result->fetch_array(MYSQLI_ASSOC)) {
                    if ($page_alias['page_alias'] != NULL) {
                        $page_data['page_alias'] = $page_alias['page_alias'];
                    } else {
                        $page_data['page_alias'] = "";
                    }
                }
                $get_page_content = "SELECT `page_content` FROM `page_content` WHERE `page_id` = '" . $row['page_id'] . "'";
                $get_page_content_result = $this->_mysqli->query($get_page_content);
                while ($page_content = $get_page_content_result->fetch_array(MYSQLI_ASSOC)) {

                    $page_data['page_content'] = $page_content['page_content'];
                }
                $get_page_images = "SELECT `image_name`, `image_path`  FROM `page_images` WHERE `page_id` = '" . $row['page_id'] . "'";
                $get_page_images_result = $this->_mysqli->query($get_page_images);
                $page_images_data = array();
                while ($page_images = $get_page_images_result->fetch_array(MYSQLI_ASSOC)) {

                    $page_images_data['image_name'] = $page_images['image_name'];
                    $page_images_data['image_path'] = $page_images['image_path'];
                }
                array_push($page_data, $page_images_data);
                $get_page_meta = "SELECT `page_title`, `meta_data`,`description` FROM `page_meta_data` WHERE `page_id` = '" . $row['page_id'] . "'";
                $get_page_meta_result = $this->_mysqli->query($get_page_meta);
                while ($page_meta = $get_page_meta_result->fetch_array(MYSQLI_ASSOC)) {

                    $page_data['meta_data'] = $page_meta['meta_data'];
                    $page_data['page_title'] = $page_meta['page_title'];
                    $page_data['description'] = $page_meta['description'];
                }
                if ($row['page_type'] == 10) {

                    $get_date_for_product = "SELECT * FROM `pages_products` WHERE `page_id` ='" . $row['page_id'] . "'";
                    $get_date_for_product_res = $this->_mysqli->query($get_date_for_product);
                    while ($products = $get_date_for_product_res->fetch_array(MYSQLI_ASSOC)) {
                        $products_array = array();
                        $products_array['products'] = $products;
                    }
                    array_push($page_data, $products_array);
                }

                $get_css_files = "SELECT `page_type`, `css_file_name`, `css_file_location` FROM `pages_css` WHERE `page_type` = '" . $row['page_type'] . "'";
                $get_css_files_res = $this->_mysqli->query($get_css_files);
                while ($css_files = $get_css_files_res->fetch_array(MYSQLI_ASSOC)) {

                    $page_data['css_file_name'] = $css_files['css_file_name'];
                    $page_data['css_file_location'] = $css_files['css_file_location'];
                }

                if ($row['page_parent'] != "0") {
                    $get_parent_info = "SELECT `id`, `page_name`, `page_type`, `page_id`, `page_parent` FROM `pages` WHERE `id` = '" . $row['page_parent'] . "'";
                    $get_parent_info_rest = $this->_mysqli->query($get_parent_info);
                    while ($page_parent_info = $get_parent_info_rest->fetch_array(MYSQLI_ASSOC)) {
                        $page_data['parent_page_name'] = $page_parent_info['page_name'];
                        $page_data['parent_page_parnet'] = $page_parent_info['page_parent'];
                        $page_data['parent_page_type'] = $page_parent_info['page_type'];
                        $page_data['parent_page_id'] = $page_parent_info['page_id'];
                        $page_data['parent_page_uid'] = $page_parent_info['id'];
                    }
                }
                if ($row['page_type'] == "9" && $option == "homepage" || $option == "categories") {
                    $products_for_category = array();
                    $find_all_children = "SELECT * FROM `pages_products` WHERE `page_parent` ='" . $row['id'] . "' ORDER BY RAND() LIMIT 1";
                    $find_all_children_res = $this->_mysqli->query($find_all_children);
                    while ($child_products = $find_all_children_res->fetch_array(MYSQLI_ASSOC)) {
                        $products_for_category[] = $child_products;
                    }
                    array_push($page_data, $products_for_category);
                }

                $this->page_data = $page_data;
            }
        } else {
            echo "Oops we were unable to find the page.";
        }
    }

    public function ReturnPageData() {
        return $this->page_data;
    }

    public function GetHomePageID() {

        $sql = "SELECT `id` FROM `pages` WHERE `page_type` ='1' AND `page_parent` ='0'";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

            $page_id = $row['id'];
        }
        return $page_id;
    }

    public function BuildBreadCrumb($page_parent, $page_type) {

        /*
         * To build the bread crumb find the page parents 
         */
        if ($page_type == "9" || $page_type == "10" || $page_type == "11" || $page_type == "5" && $page_parent != "0") {
            $get_parents = "SELECT `id`,`page_name`,`page_parent`, `page_type`, `page_id` FROM `pages` WHERE `id` = '" . $page_parent . "' ORDER BY `page_parent` ASC";
            $get_parents_res = $this->_mysqli->query($get_parents);
            while ($row = $get_parents_res->fetch_array(MYSQLI_ASSOC)) {

                $this->breadCrumb[] = $row;
                $this->BuildBreadCrumb($row['page_parent'], $row['page_type']);
            }
            return $this->breadCrumb;
        } else {
            return FALSE;
        }
    }

    public function BreadCrumbLinks() {

        if ($this->breadCrumb != NULL) {
            ?>
            <li><a href="/"><i class="fa fa-home"></i></a></li>
                    <?php
                    foreach (array_reverse($this->breadCrumb) as $breadcrumb_links) {
                        $no_spaces = str_replace(" ", "-", $breadcrumb_links['page_name']);
                        $no_upper_case = strtolower($no_spaces);
                        $no_ands = str_replace("&", "and", $no_upper_case);
                        $clean_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);

                        $build_bread_crumb_url = "/" . $clean_name . "/" . $breadcrumb_links['id'];
                        ?>
                <li><a href="<?= $build_bread_crumb_url; ?>"><?= $breadcrumb_links['page_name'] ?></a></li>
                <?php
            }
        }
    }

    public function CreateTableForCSSPerPageType() {

        $sql = "CREATE TABLE IF NOT EXISTS pages_css"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_type INT(10) NOT NULL,"
                . "css_file_name VARCHAR(50) NOT NULL,"
                . "css_file_location VARCHAR(250) NOT NULL"
                . ")";
        $create = $this->_mysqli->query($sql);
        $css_files = array(
            "1" => array(
                "page_type" => "1",
                "css_file_name" => "home.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "2" => array(
                "page_type" => "2",
                "css_file_name" => "contact_us.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "3" => array(
                "page_type" => "3",
                "css_file_name" => "about_us.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "4" => array(
                "page_type" => "4",
                "css_file_name" => "reviews.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "5" => array(
                "page_type" => "5",
                "css_file_name" => "static_page.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "6" => array(
                "page_type" => "6",
                "css_file_name" => "brands.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "7" => array(
                "page_type" => "7",
                "css_file_name" => "single_page.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "8" => array(
                "page_type" => "8",
                "css_file_name" => "categories.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "9" => array(
                "page_type" => "9",
                "css_file_name" => "sub_categories.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "10" => array(
                "page_type" => "10",
                "css_file_name" => "products.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "11" => array(
                "page_type" => "11",
                "css_file_name" => "hidden.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
            "12" => array(
                "page_type" => "14",
                "css_file_name" => "faqs.css",
                "css_file_location" => "/" . F_ASSETS . "css/page_types_css/"
            ),
        );
        /*
         * First check if data already there
         */
        foreach ($css_files as $css) {
            $check_data = "SELECT `id` FROM `pages_css` WHERE `css_file_name` ='" . $css['css_file_name'] . "'";
            $check_data_res = $this->_mysqli->query($check_data);
            $get_num_rows = $check_data_res->num_rows;
            if ($get_num_rows == 0) {

                $insert_css_values = "INSERT INTO `pages_css` (page_type, css_file_name, css_file_location)"
                        . " VALUES ('" . $css['page_type'] . "','" . $css['css_file_name'] . "','" . $css['css_file_location'] . "')";
                var_dump($insert_css_values);
                $insert_css_values_res = $this->_mysqli->query($insert_css_values);
            }
        }
    }

    public function GetSocialMedia() {

        $sql = "SELECT * FROM `social_media` WHERE `status` ='1'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->social_media[] = $row;
            }
            return $this->social_media;
        }
    }

    public function GetFooterTopPages() {
        $sql = "SELECT `id`,`page_name`, `page_type`, `page_id` FROM `pages` WHERE `page_parent` = '0'  AND `page_type` != '13' AND `page_type` != '12' ORDER BY `page_order` ASC";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->all_top_pages[] = $row;
            }
            return $this->all_top_pages;
        }
    }

    public function GetFooterHiddenPages() {
        $get_parent_info = "SELECT `id`, `page_name`, `page_type`, `page_parent` FROM `pages` WHERE `page_type` = '13'";
        $get_parent_info_res = $this->_mysqli->query($get_parent_info);
        $get_parent_count = $get_parent_info_res->num_rows;
        if ($get_parent_count > 0) {

            while ($parent = $get_parent_info_res->fetch_array(MYSQLI_ASSOC)) {
                $sql = "SELECT `id`, `page_name`, `page_type`, `page_id` FROM `pages` WHERE `page_parent` = '" . $parent['id'] . "' AND `page_type` = '11' OR `page_type` ='14' ORDER BY `page_order` ASC";
                $result = $this->_mysqli->query($sql);
                $num_rows = $result->num_rows;
                if ($num_rows > 0) {
                    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                        $this->all_hidden_pages[] = $row;
                    }
                    return $this->all_hidden_pages;
                }
            }
        }
    }

    public function GetPageAlias($page_id) {
        $sql = "SELECT `page_id`, `page_alias` FROM `page_alias` WHERE `page_id` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if ($row['page_alias'] != NULL) {
                $this->page_alias[] = $row;
            } else {
                return FALSE;
            }
        }
        return $this->page_alias;
    }

    public function GetStoreInformation() {

        $sql = "SELECT * FROM `store_info` WHERE `primary` ='1'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->store_info[] = $row;
            }
            return $this->store_info;
        } else {
            return false;
        }
    }

    public function SignUpForNewsLetter($data) {
        $today = date('m/d/y');
        $sql = "SELECT `email` FROM `rock_newsletter` WHERE `email` = '" . trim($data['news_letters_email']) . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            return false;
        } else {
            $insert_email = "INSERT INTO `rock_newsletter` (email, date_added) VALUES ('" . trim($data['news_letters_email']) . "', '" . $today . "')";
            $insert_email_res = $this->_mysqli->query($insert_email);
            if ($insert_email) {
                $email = trim($data['news_letters_email']);
                $to = $email;
                $subject = 'Monthly Newsletter';
                $message = '
                    <html>
                        <head>
                            <title>"' . CUSTOMER . '" News Letter Sign up</title>
                        </head>
                        <body>
                            <p>Thank you for signing up for our newsletter. We usually send out a newsletter every month.</p>
                            <br/>
                            <br/>
                            <p>Sincerely,</p>
                            <p>"' . CUSTOMER . '" customer suppoert</p>
                        </body>
                    </html>
                            ';

                // To send HTML mail, the Content-type header must be set
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                // Mail it
                mail($to, $subject, $message, $headers);
                return true;
            }
        }
    }

    public function CreateTableForContactUs() {
        $sql = "CREATE TABLE IF NOT EXISTS contact_us_messages "
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                . "name VARCHAR (250) NOT NULL, "
                . "email VARCHAR (500) NOT NULL, "
                . "phone VARCHAR (250) NOT NULL, "
                . "message TEXT, "
                . "date_added VARCHAR (50) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateTableForFAQS() {
        $sql = "CREATE TABLE IF NOT EXISTS faqs"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                . "name VARCHAR (250) NOT NULL, "
                . "email VARCHAR (500) NOT NULL, "
                . "message TEXT, "
                . "date_added VARCHAR (50) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateTableForlogo() {
        $sql = "CREATE TABLE IF NOT EXISTS store_logo"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                . "logo_name VARCHAR (250) NOT NULL,"
                . "logo_path VARCHAR (500) NOT NULL, "
                . "date_added VARCHAR (50) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateStoreOrders() {
        $sql = "CREATE TABLE IF NOT EXISTS store_orders"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                . "order_date VARCHAR (50) NOT NULL, "
                . "order_name VARCHAR (100) NOT NULL, "
                . "order_address_1 VARCHAR (255) NOT NULL, "
                . "order_address_2 VARCHAR (100) NOT NULL, "
                . "order_city VARCHAR (255) NOT NULL, "
                . "order_state VARCHAR (200) NOT NULL, "
                . "order_zip VARCHAR (10) NOT NULL,"
                . "order_country VARCHAR (200) NOT NULL, "
                . "order_tel VARCHAR (30) NOT NULL, "
                . "order_email VARCHAR (100) NOT NULL, "
                . "item_total FLOAT (6,2) NOT NULL, "
                . "authorization VARCHAR (250) NOT NULL,"
                . "transaction_id VARCHAR (500) NOT NULL,"
                . "shipping_instructions TEXT, "
                . "status ENUM ('processed', 'pending')"
                . ")";

        $result = $this->_mysqli->query($sql);
    }

    public function CreateTableStoreOrderItems() {
        $sql = "CREATE TABLE IF NOT EXISTS store_orders_items"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                . "order_id VARCHAR(500) NOT NULL, "
                . "sel_item_id VARCHAR(255) NOT NULL,"
                . "sel_item_name VARCHAR (500) NOT NULL, "
                . "sel_item_qty INT(10) NOT NULL,"
                . "old_qty INT(10) DEFAULT 0, "
                . "sel_item_size VARCHAR (55) NOT NULL, "
                . "sel_item_color VARCHAR (55) NOT NULL,"
                . "sel_item_price FLOAT(6,2) NOT NULL, "
                . "date_added VARCHAR (50) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateTableStates() {
        $sql = "CREATE TABLE IF NOT EXISTS rock_states"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                . "name VARCHAR (255) NULL,"
                . "short VARCHAR(255) NOT NULL,"
                . "country VARCHAR(100) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function GetStoreLogo() {
        $select = "SELECT * FROM `store_logo`";
        $res = $this->_mysqli->query($select);
        $num_rows = $res->num_rows;
        if ($num_rows > 0) {
            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                $this->store_logo[] = $row;
            }
            return $this->store_logo;
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
        ;
    }

    public function CreateTableCheckedOut() {
        $sql = "CREATE TABLE IF NOT EXISTS checked_out"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                . "order_id VARCHAR (255) NULL, "
                . "transaction_id VARCHAR(255) NOT NULL, "
                . "sold_item_id VARCHAR(255) NOT NULL, "
                . "sold_item_qty INT(10) NOT NULL, "
                . "sold_item_size VARCHAR(100) NOT NULL, "
                . "sold_item_unit_price FLOAT (6,2) NOT NULL, "
                . "sold_item_color VARCHAR (100) NOT NULL, "
                . "sold_date_time VARCHAR(100) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    //1
    public function CreateFrontEndUsersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS frontend_users_desc "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "cust_id VARCHAR(250) NOT NULL, "
                . "f_name VARCHAR (100) NOT NULL, "
                . "l_name VARCHAR (100) NOT NULL, "
                . "middle_name VARCHAR (100) NOT NULL, "
                . "title VARCHAR (50) NOT NULL "
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    //2
    public function CreateFrontEndUserAddressTable() {
        $sql = "CREATE TABLE IF NOT EXISTS frontend_users_address "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "
                . "cust_id VARCHAR (250) NOT NULL, "
                . "address_1 VARCHAR (250) NOT NULL, "
                . "address_2 VARCHAR (100) NOT NULL, "
                . "city VARCHAR (200) NOT NULL, "
                . "zip_code VARCHAR (50) NOT NULL, "
                . "state VARCHAR (200) NOT NULL, "
                . "province VARCHAR (200) NOT NULL, "
                . "country VARCHAR (200) NOT NULL "
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    //3
    public function CreateFrontEnduserPhoneFaxtable() {
        $sql = "CREATE TABLE IF NOT EXISTS frontend_users_phone_fax "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "cust_id VARCHAR (250) NOT NULL, "
                . "phone VARCHAR (20) NOT NULL, "
                . "fax VARCHAR (20) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    //4
    public function CreateFrontEndUserEmailtable() {
        $sql = "CREATE TABLE IF NOT EXISTS frontend_users_email "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "cust_id VARCHAR (250) NOT NULL, "
                . "email VARCHAR (250) UNIQUE NOT NULL, "
                . "password VARCHAR (500) NOT NULL "
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    //5
    public function CreateFrontEndUsersStatusTable() {
        $sql = "CREATE TABLE IF NOT EXISTS frontend_users_status "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "cust_id VARCHAR (250) NOT NULL, "
                . "status INT(2) DEFAULT 0 NOT NULL, "
                . "last_login VARCHAR (50) NOT NULL, "
                . "last_session VARCHAR (250) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    //6
    public function CreateFrontEndUserPurchaseHistoryTable() {
        $sql = "CREATE TABLE IF NOT EXISTS frontend_users_purchase_history "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "cust_id VARCHAR (250) NOT NULL, "
                . "transaction_id VARCHAR (500) NOT NULL, "
                . "date_of_purchase VARCHAR (250) NOT NULL "
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateWholeSaleUserTable() {
        $sql = "CREATE TABLE IF NOT EXISTS wholesaleuser"
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "wholesaler_id VARCHAR (250) NOT NULL, "
                . "fname VARCHAR (250) NOT NULL, "
                . "lname VARCHAR (250) NOT NULL, "
                . "telephone VARCHAR (100) NOT NULL, "
                . "fax VARCHAR (100) NOT NULL, "
                . "email VARCHAR (250) NOT NULL, "
                . "address_1 VARCHAR (550) NOT NULL,"
                . "address_2 VARCHAR (100) NOT NULL, "
                . "city VARCHAR (250) NOT NULL, "
                . "state VARCHAR (250) NOT NULL, "
                . "zip_code VARCHAR (50) NOT NULL, "
                . "country VARCHAR (250) NOT NULL, "
                . "company_name VARCHAR (500) NOT NULL, "
                . "company_age VARCHAR (25) NOT NULL, "
                . "tax_id VARCHAR (250) NOT NULL, "
                . "website VARCHAR (500) NOT NULL, "
                . "password VARCHAR (500) NOT NULL,"
                . "date_added VARCHAR (100) NOT NULL "
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateWholesalerStatus() {
        $sql = "CREATE TABLE IF NOT EXISTS wholeselerstatus "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "wholesaler_id VARCHAR (250) NOT NULL, "
                . "status INT(10) DEFAULT 0 NOT NULL, "
                . "date_of_activation VARCHAR (250) NOT NULL "
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function CreateShippingInfoTable() {
        $sql = "CREATE TABLE IF NOT EXISTS shippingInfo "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "transaction_id VARCHAR (250) NOT NULL, "
                . "status VARCHAR (100) NOT NULL, "
                . "shipper VARCHAR (50) NOT NULL, "
                . "tracking_number VARCHAR (200) NOT NULL, "
                . "date VARCHAR (250) NOT NULL "
                . ")";
        $result = $this->_mysqli->query($sql);
    }

}
