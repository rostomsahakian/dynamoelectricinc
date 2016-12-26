<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreatePageForm
 *
 * @author rostom
 */
class CreatePageForm {

    public $page_type_selector;
    public $queries;
    private $_mysqli;
    private $_db;
    public $fetch_res;
    public $pages;
    public $page_meta;
    public $page_content;
    public $right_images_side;
    public $files_upload;
    public $URL_manager;
    public $_page_parent;
    public $pp;
    public $pt;
    public $messages = array();
    public $alert_class;
    public $flag = 0;
    public $categories;
    public $products;
    public $preview_url = "";
    public $url_parents;
    public $sitemap_flag = 0;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->page_type_selector = new AddNewPage();
        $this->right_images_side = new RightSide();
        $this->files_upload = new FilesManager();
        $this->URL_manager = new UrlOptions();
        $this->categories = new SubCategory();
        $this->products = new ProductPage();
    }

    public function CreatePageMainForm($page_d) {
        ?>

        <div class="panel-heading">
            <?php
            foreach ($this->GetPageInfo($page_d) as $page_data) {
                ?>
                <h5><strong><i class="glyphicon glyphicon-edit"></i>&nbsp;<?= $page_data['page_name'] ?>&nbsp;&nbsp;&nbsp;&nbsp;<a href='' title="return" onclick="goBack()"><i class='fa fa-mail-reply'></i></a></strong></h5>
            </div>
            <div class="panel-body">

                <?php
                if ($page_data['page_type'] != 10) {
                    $this->PageInfoForm($page_data);
                } else if ($page_data['page_type'] == 10) {
                    $this->products->ProductPageForm($page_data['page_id']);
                }
                ?>

            </div>
            <?php
        }
        ?>
        <?php
    }

    public function PageInfoForm(array $pg_data) {



        if (isset($_REQUEST['do_update_page'])) {
            $_REQUEST['option'] = "common";
            $page_data = array(
                "pages" => array(
                    "page_name" => isset($_REQUEST['main_page_name']) ? $_REQUEST['main_page_name'] : '',
                    "page_type" => isset($_REQUEST['main_page_type']) ? $_REQUEST['main_page_type'] : '',
                    "page_parent" => isset($_REQUEST['main_page_parent']) ? $_REQUEST['main_page_parent'] : '',
                    "c_date" => isset($_REQUEST['main_page_c_date']) ? $_REQUEST['main_page_c_date'] : ''
                ),
                "meta" => array(
                    "page_title" => isset($_REQUEST['main_page_title']) ? $_REQUEST['main_page_title'] : '',
                    "page_keywords" => isset($_REQUEST['main_page_keywords']) ? $_REQUEST['main_page_keywords'] : '',
                    "page_desc" => isset($_REQUEST['main_page_desc']) ? $_REQUEST['main_page_desc'] : ''
                ),
                "special" => array(
                    "home" => isset($_REQUEST['home_page']) ? $_REQUEST['home_page'] : '',
                    "hidden" => isset($_REQUEST['is_hidden']) ? $_REQUEST['is_hidden'] : ''
                ),
                "content" => array(
                    "page_content" => isset($_REQUEST['content']) ? $_REQUEST['content'] : ''
                )
            );
            $this->DoUpadtepageInfo($pg_data['page_id'], $page_data);
        }
        if (isset($_REQUEST['option'])) {
            if ($_REQUEST['option'] == "common") {
                $active_common = "active";
            } else {
                $active_common = "";
            }

            if ($_REQUEST['option'] == "url-option") {
                $active_url_tab = "active";
            } else {
                $active_url_tab = "";
            }
            if ($_REQUEST['option'] == "u_image") {
                $active_image_tab = "active";
            } else {
                $active_image_tab = "";
            }
            if ($_REQUEST['option'] == "create-cats") {
                $active_category = "active";
            } else {
                $active_category = "";
            }
            if ($_REQUEST['option'] == "sitemap-setup") {
                $active_sitemap = "active";
            } else {
                $active_sitemap = "";
            }
        }
        ?>
        <div class="col-lg-12">
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
            <div class="row">
                <ul class="nav nav-tabs" role="tablist" id="tabs_editpage" >
                    <li class="<?= $active_common ?>" aria-controls="login" role="tab" ><a href="#tabs-common-details" data-toggle="tab">Common Details</a></li>
                    <li class="<?= $active_image_tab; ?>" aria-controls="f_pass" role="tab" ><a href="#tabs-images" data-toggle="tab">Images</a></li>
                    <li aria-controls="f_pass" role="tab" ><a href="#tabs-files" data-toggle="tab">Files</a></li>
                    <li class="<?= $active_url_tab ?>" aria-controls="f_pass" role="tab" ><a href="#tabs-url-options" data-toggle="tab">URL Options</a></li>
                    <?php
                    if ($pg_data['page_type'] == 9 || $pg_data['page_type'] == 8 || $pg_data['page_type'] == 7) {
                        ?>
                        <li class="<?= $active_category ?>" aria-controls="f_pass" role="tab" ><a href="#tabs-categories" data-toggle="tab">Create Categories</a></li>
                        <?php
                    }
                    ?>
                    <?php
                    if ($pg_data['page_type'] == 10) {
                        ?>
                        <li class="<?= $active_product ?>" aria-controls="f_pass" role="tab" ><a href="#tabs-products" data-toggle="tab">Manage Product</a></li>
                        <?php
                    }
                    ?>
                    <li class="<?= $active_sitemap ?>" aria-controls="f_pass" role="tab" ><a href="#sitemap-manage" data-toggle="tab">Sitemap Setup</a></li>

                    <!--add plugin tabs here-->
                    <li class=""> </li>
                </ul>

            </div>

            <div class="tab-content rock-cont-div">
                <div class="tab-pane <?= $active_common ?>" id="tabs-common-details">
                    <form method="post">

                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Page Name:</label>
                                    <input type="text" name="main_page_name" id="main-page-name" value="<?= isset($_REQUEST['main_page_name']) ? $_REQUEST['main_page_name'] : $pg_data['page_name'] ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Page Title:</label>
                                    <?php
                                    $this->page_meta = NULL;
                                    foreach ($this->GetALLMetaData($pg_data['page_id']) as $page_title) {
                                        ?>

                                        <input type="text" name="main_page_title" id="main-page-title" value="<?= isset($_REQUEST['main_page_title']) ? $_REQUEST['main_page_title'] : $page_title['page_title'] ?>" class="form-control"/>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?php
                                    $this->MakeUrlForPreview($pg_data['id'], $pg_data['page_id'], $pg_data['page_parent'], $pg_data['page_name']);
                                    ?>
                                    <a href="<?= $this->preview_url ?>" target="_BLANK" class="btn btn-success">
                                        Preview Page
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Page Type:</label>
                                    <select class="form-control" name="main_page_type">

                                        <?php
                                        foreach ($this->page_type_selector->SelectPage_type() as $pg_type) {
                                            $type = isset($_REQUEST['main_page_type']) ? $_REQUEST['main_page_type'] : $pg_data['page_type'];
                                            $this->pt = $pg_data['page_type'];
                                            if ($type == $pg_type['page_type']) {
                                                $selected = "selected='selected'";
                                            } else {

                                                $selected = "";
                                            }
                                            ?>
                                            <option value="<?= $pg_type['page_type'] ?>" <?= $selected ?>><?= $pg_type['page_type'] . " / " . $pg_type['type_name'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Page Parent:</label>
                                    <select name="main_page_parent" class="form-control">
                                        <option value="0">None</option>
                                        <?php
                                        foreach ($this->CheckIfPageIsASubPage($pg_data['id']) as $pages) {
                                            $parent = isset($_REQUEST['main_page_parent']) ? $_REQUEST['main_page_parent'] : $pg_data['page_parent'];
                                            var_dump($pages['page_parent']);
                                            $this->pp = $pages['page_parent'];
                                            if ($parent == $pages['id']) {
                                                $p_selected = 'selected="selected"';
                                            } else {
                                                $p_selected = '';
                                            }
                                            ?>
                                            <option value="<?= $pages['id'] ?>" <?= $p_selected ?>><?= $pages['page_name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Created Date:</label>
                                    <input type="text" id="datepicker" class="form-control" name="main_page_c_date" value="<?= isset($_REQUEST['main_page_c_date']) ? $_REQUEST['main_page_c_date'] : $pg_data['date_created'] ?>">

                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr/>
                        </div>

                        <!--Meta and description -->
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <strong><i class="fa fa-magic" aria-hidden="true"></i>&nbsp;Meta Data</strong>
                                    </div>
                                    <?php
                                    $this->page_meta = NULL;
                                    foreach ($this->GetALLMetaData($pg_data['page_id']) as $page_meta) {
                                        ?>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>Keywords:</label>
                                                <input type="text" name="main_page_keywords" value="<?= isset($_REQUEST['main_page_keywords']) ? $_REQUEST['main_page_keywords'] : $page_meta['meta_data'] ?>" class="form-control"/>
                                            </div>
                                            <div class="form-group">
                                                <label>Description:</label>
                                                <input type="text" name="main_page_desc" value="<?= isset($_REQUEST['main_page_desc']) ? $_REQUEST['main_page_desc'] : $page_meta['description'] ?>" class="form-control" />
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <!--Special Case-->
                            <div class="col-md-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <strong><i class="fa fa-map-signs" aria-hidden="true"></i>&nbsp;Special Case:</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <?php
                                            if ($this->pt == "1") {
                                                $home_page = "checked='checked'";
                                            } else {
                                                $home_page = "";
                                            }
                                            ?>
                                            <input type="checkbox" name="home_page" value="false" <?= $home_page ?> />
                                            <label>Is Home Page?</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="checkbox" name="is_hidden" value="false" />
                                            <label>Is Hidden?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Other page order -->
                            <div class="col-md-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <strong><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>&nbsp;Other</strong>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Page Ordered</label>
                                            <select name="order" class="form-control">
                                                <option value="a">Ascending</option>
                                                <option value="d">Descending</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr/>
                        </div>
                        <!--CK EDITOR GOES HERE-->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Page Content</label>

                                <?php
                                foreach ($this->GetAllPageContent($pg_data['page_id']) as $page_content) {
                                    ?>
                                    <?= $this->ckeditor('content', (isset($_REQUEST['content']) ? $_REQUEST['content'] : $page_content['page_content'])) ?>    
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" value="Update Page" name="do_update_page" id="do-update-page" />
                        </div>
                    </form>
                </div>
                <!--Image Upload and view-->
                <div class="tab-pane <?= $active_image_tab; ?>" id="tabs-images">
                    <?php
                    $this->right_images_side->LoadRightSide($pg_data['page_id']);
                    ?>
                </div>
                <!--Files Upload and view-->
                <div class="tab-pane" id="tabs-files">
                    <?php
                    $this->files_upload->FilesUploadForm();
                    ?>
                </div>
                <!--Url Options -->
                <div class="tab-pane <?= $active_url_tab ?>" id="tabs-url-options">
                    <?php
                    $this->URL_manager->UrlOptionForm($pg_data['page_id']);
                    ?>
                </div>
                <!-- Create Categories -->
                <div class="tab-pane <?= $active_category ?>" id="tabs-categories">
                    <?php
                    $this->categories->ChooseSubCategories($pg_data['page_id'], $pg_data['page_type']);
                    ?>
                </div>
                <!--sitemap Options -->
                <div class="tab-pane <?= $active_sitemap ?>" id="sitemap-manage">
                    <?php
                    $this->SiteMapSetup($pg_data['page_id']);
                    ?>
                </div>
            </div>

            <?php ?>
        </div>
        <?php
    }

    public function GetPageInfo($id) {
        $sql = "SELECT * FROM `pages` WHERE `page_id` = '" . $id . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->fetch_res[] = $row;
            }
            return $this->fetch_res;
        }
    }

    public function GetALLPages($id) {
        $sql = "SELECT * FROM `pages` WHERE `id` != '" . $id . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->pages[] = $row;
            }
            return $this->pages;
        }
    }

    public function GetALLMetaData($id) {
        $sql = "SELECT * FROM `page_meta_data` WHERE `page_id` = '" . $id . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->page_meta[] = $row;
            }
            return $this->page_meta;
        }
    }

    /*
     * CKEDITOR Called in page edit form
     */

    public function ckeditor($name, $value = '', $height = 350) {
        return ' <textarea class="form-control" rows="20" name="' . addslashes($name) . '">' . htmlspecialchars($value) . '</textarea>'
                . '<script> CKEDITOR.replace( "' . $name . '"); '
                . ''
                . '</script>';
    }

    public function GetAllPageContent($id) {
        $sql = "SELECT * FROM `page_content` WHERE `page_id` = '" . $id . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->page_content[] = $row;
            }
            return $this->page_content;
        }
    }

    public function DoUpadtepageInfo($page_id, array $page_data) {
        /*
         * Select information from tables and compare them to the new values 
         * if there are any changes update the results 
         * else do nothing
         */
        $big_array = array();


        $pages = array();
        $sql_pages_table = "SELECT * FROM `pages` WHERE `page_id` = '" . $page_id . "'";
        $pages_results = $this->_mysqli->query($sql_pages_table);

        if ($pages_results) {

            while ($row = $pages_results->fetch_array(MYSQLI_ASSOC)) {
                $pages['pages'] = $row;
            }
        }


        $sql_page_meta_data_table = "SELECT * FROM `page_meta_data` WHERE `page_id` = '" . $page_id . "'";
        $page_meta_data_results = $this->_mysqli->query($sql_page_meta_data_table);
        if ($page_meta_data_results) {
            $meta = array();
            while ($meta_row = $page_meta_data_results->fetch_array(MYSQLI_ASSOC)) {
                $pages['meta'] = $meta_row;
            }
        }

        $sql_page_special_table = "SELECT * FROM `page_special` WHERE `page_id` = '" . $page_id . "'";
        $page_special_results = $this->_mysqli->query($sql_page_special_table);
        if ($page_special_results) {

            while ($special_row = $page_special_results->fetch_array(MYSQLI_ASSOC)) {
                $pages['special'] = $special_row;
            }
        }

        $sql_page_content_table = "SELECT * FROM `page_content` WHERE `page_id` = '" . $page_id . "'";
        $page_content_results = $this->_mysqli->query($sql_page_content_table);
        if ($page_content_results) {
            while ($content_row = $page_content_results->fetch_array(MYSQLI_ASSOC)) {
                $pages['content'] = $content_row;
            }
        }
        array_push($big_array, $pages);
        foreach ($big_array as $fetched_data) {

            /*
             * Pages table values to be checked
             */
            $flag = 0;
            if ($fetched_data['pages']['page_type'] == "1") {
                /*
                 * Check the selection ststus
                 */
                if ($page_data['pages']['page_type'] != "1") {
                    $flag = 1;
                    if ($flag == 1) {
                        /*
                         * Echo message
                         */
                        $this->flag = 1;
                        $this->alert_class = "warning";
                        $message = array("1" => "This is a home page and the page type cannot be modified.");
                        array_push($this->messages, $message);
                    }
                } else if ($page_data['pages']['page_parent'] != "0") {
                    $flag = 1;
                    if ($flag == 1) {
                        /*
                         * home parent can not be changed
                         */
                        $this->flag = 1;
                        $this->alert_class = "warning";
                        $message = array("1" => "This is a home page and it can not have any parents.");
                        array_push($this->messages, $message);
                    }
                }
            }
            if ($page_data['pages']['page_type'] == "1" && $this->CheckForHomePage() && $fetched_data['pages']['page_type'] != "1") {

                $flag = 1;
                if ($flag == 1) {
                    /*
                     * Echo message
                     */
                    $this->flag = 1;
                    $this->alert_class = "warning";
                    $message = array("1" => "There can be only one homepage.");
                    array_push($this->messages, $message);
                }
            }
            if (empty($page_data['pages']['page_name']) && empty($page_data['meta']['page_title'])) {
                $flag = 1;
                if ($flag == 1) {

                    $this->flag = 1;
                    $this->alert_class = "warning";
                    $message = array("1" => "Please enter the page name and page title.");
                    array_push($this->messages, $message);
                } else {
                    $flag = 0;
                    $this->flag = 0;
                }
            }
            if (empty($page_data['pages']['page_name']) || empty($page_data['meta']['page_title'])) {
                $flag = 1;
                if ($flag == 1) {

                    $this->flag = 1;
                    $this->alert_class = "warning";
                    $message = array("1" => "Either the page name or page title is missing.");
                    array_push($this->messages, $message);
                } else {
                    $flag = 0;
                    $this->flag = 0;
                }
            }
            if ($flag == 0) {



                $tables_fields_data = array(
                    "1" => array(
                        "table" => "pages",
                        "fields" => array(
                            "page_name",
                            "page_type",
                            "page_parent",
                            "date_created"
                        ),
                        "values" => array(
                            $page_data['pages']['page_name'] . "',",
                            $page_data['pages']['page_type'] . "',",
                            $page_data['pages']['page_parent'] . "',",
                            $page_data['pages']['c_date'] . "'"
                        ),
                        "key" => $page_id,
                        "find" => "page_id"
                    ),
                    "2" => array(
                        "table" => "page_meta_data",
                        "fields" => array(
                            "page_title",
                            "meta_data",
                            "description",
                            "date_modified"
                        ),
                        "values" => array(
                            $page_data['meta']['page_title'] . "',",
                            $page_data['meta']['page_keywords'] . "',",
                            $page_data['meta']['page_desc'] . "',",
                            $page_data['pages']['c_date'] . "'"
                        ),
                        "key" => $page_id,
                        "find" => "page_id"
                    ),
                    "3" => array(
                        "table" => "page_special",
                        "fields" => array(
                            "home_page",
                            "hidden",
                            "date_modified"
                        ),
                        "values" => array(
                            $page_data['special']['home'] . "',",
                            $page_data['special']['hidden'] . "',",
                            $page_data['pages']['c_date'] . "'"
                        ),
                        "key" => $page_id,
                        "find" => "page_id"
                    ),
                    "4" => array(
                        "table" => "page_content",
                        "fields" => array(
                            "page_content",
                            "date_added"
                        ),
                        "values" => array(
                            addslashes($page_data['content']['page_content']) . "',",
                            $page_data['pages']['c_date'] . "'"
                        ),
                        "key" => $page_id,
                        "find" => "page_id"
                    )
                );
                for ($i = 1; $i <= count($tables_fields_data); $i++) {
                    
                }
                $this->UpdateQuery($tables_fields_data, 1);
            }
        }
    }

    public
            function UpdateQuery(array $data, $option) {
        switch ($option) {
            case 0:
                $sql = "UPDATE `" . $data['table'] . "` SET `" . $data['field1'] . "` = '" . $data['value1'] . "' WHERE `" . $data['field2'] . "` = '" . $data['value2'] . "'";
                $res = $this->_mysqli->query($sql);
                if ($res) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 1:
                for ($i = 1; $i <= count($data); $i++) {
                    $sql = "UPDATE `" . $data[$i]['table'] . "` "
                            . " SET ";
                    for ($j = 0; $j < count($data[$i]['values']); $j++) {

                        $sql .= " `" . $data[$i]['fields'][$j] . "` ";
                        $sql .= " = ";

                        $sql .= "'" . $data[$i]['values'][$j] . "";
                    }
                    $sql .= " WHERE ";
                    $sql .= "`" . $data[$i]['find'] . "` ";
                    $sql .= " = ";
                    $sql .= "'" . $data[$i]['key'] . "' ";
                    $result = $this->_mysqli->query($sql);
                    if ($result) {


                        $this->flag = 1;
                        $message = array("1" => "Table " . $data[$i]['table'] . " was updated.");

                        array_push($this->messages, $message);
                        $this->alert_class = "success";
                    }
                }



                break;
        }
    }

    public function CheckIfPageIsASubPage($page_id) {
        /*
         * First Find all the page children
         * if the page has children do not show them under the page parent selection
         */
        $sql = "SELECT * FROM `pages` WHERE `page_parent` != '" . $page_id . "' AND `id` != '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->_page_parent[] = $row;
            }
            return $this->_page_parent;
        }
    }

    public function CheckForHomePage() {
        $sql = "SELECT * FROM `pages` WHERE `page_type`= '1'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            return true;
        } else {
            return FALSE;
        }
    }

    public function MakeUrlForPreview($id, $page_id, $page_parent, $page_name) {
        /*
         * url maker
         * if page parent is zero then it is already a top level page
         * if not then get the parent page name 
         */

        if ($page_parent == 0) {

            /*
             * Check for its page alias and url type
             */
            $select_page_url = "SELECT `page_alias` FROM `page_alias` WHERE `page_id` = '" . $page_id . "'";
            $select_page_url_res = $this->_mysqli->query($select_page_url);
            $num_rows_alias = $select_page_url_res->num_rows;
            if ($num_rows_alias > 0) {
                while ($row = $select_page_url_res->fetch_array(MYSQLI_ASSOC)) {

                    /*
                     * Make the url from url option
                     */
                    if ($row['page_alias'] != "") {
                        $url = "/" . $row['page_alias'] . "/" . $id;
                        $this->preview_url = $url;
                    } else {
                        /*
                         * make the page name kosher and then add it as a url
                         */
                        $no_spaces = str_replace(" ", "-", $page_name);
                        $no_upper_case = strtolower($no_spaces);
                        $no_ands = str_replace("&", "and", $no_upper_case);
                        $no_special_chars = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);
                        $this->preview_url = "";
                        $generic_url = "/" . $no_special_chars . "/" . $id;
                        $this->preview_url = $generic_url;
                    }
                }
            } else {

                $this->flag = 1;
                $message = array("1" => "Error Fro Url maker #1001");
                $this->alert_class = "warning";
                array_push($this->messages, $message);
            }
        } else if ($page_parent != 0) {
            /*
             * Check again in the url options to see if it has been assigned an alias
             */
            $select_child_page_url = "SELECT `page_alias` FROM `page_alias` WHERE `page_id` = '" . $page_id . "'";
            $select_child_page_url_res = $this->_mysqli->query($select_child_page_url);
            $num_rows_alias_for_child = $select_child_page_url_res->num_rows;
            if ($num_rows_alias_for_child > 0) {
                while ($child_row = $select_child_page_url_res->fetch_array(MYSQLI_ASSOC)) {

                    /*
                     * Make the url from url option
                     */
                    if ($child_row['page_alias'] != "") {
                        $url = "/" . $child_row['page_alias'] . "/" . $id;
                        $this->preview_url = $url;
                    } else {
                        /*
                         * Find all the parents and build a url like: Parent/Child/Grand-Child/...
                         */
                        $this->FindAllPageParents($page_parent);



                        $find_parent_name = array_reverse($this->url_parents);
                        $a = array();
                        for ($i = 0; $i < count($find_parent_name); $i++) {
                            $new_parent_url = $find_parent_name[$i]['page_name'];
                            array_push($a, $new_parent_url);
                        }
                        $parent_url = implode("/", $a);
                        $clear_parent_spaces = str_replace(" ", "-", $parent_url);
                        $remove_parent_ands = str_replace("&", "and", $clear_parent_spaces);
                        $parent_url = '/' . preg_replace('/[^a-zA-Z0-9,-\/]/', '-', strtolower($remove_parent_ands));
                        $clear_url_s = str_replace(" ", "-", $page_name);
                        $remove_long_ands = str_replace("&", "and", $clear_url_s);
                        $url = strtolower($parent_url . '/' . preg_replace('/[^a-zA-Z0-9,-\/]/', '-', $remove_long_ands));
                        $clean = $url . "/" . $id;
                        $this->preview_url = $clean;
                    }
                }
            }
        }
    }

    public function FindAllPageParents($page_parent) {
        $find_all_parents = "SELECT `page_parent`, `id`, `page_name`, `page_id` FROM `pages` WHERE `id` = '" . $page_parent . "' ORDER BY id ASC";
        $find_all_parents_res = $this->_mysqli->query($find_all_parents);
        while ($row = $find_all_parents_res->fetch_array(MYSQLI_ASSOC)) {
            $this->url_parents[] = $row;
            $this->FindAllPageParents($row['page_parent']);
        }
        return $this->url_parents;
    }

    public function CheckIfhasChildren($page_id) {
        $sql = "SELECT `id`, `page_id`, `page_parent` FROM `pages` WHERE `page_parent` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function SiteMapSetup($page_id) {

        $changefreq = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");
        $getsitemapdata = "SELECT `changefreq` , `priority` FROM `pages` WHERE `page_id` = '" . $page_id . "'";
        $result = $this->_mysqli->query($getsitemapdata);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $setres[] = $row;
            }
        }

        /*
         * Fetched variables used in the form
         */
        $cFreq = $setres[0]['changefreq'];
        $priority = $setres[0]['priority'];
        /*
         * Process the Form
         */
        if (isset($_REQUEST['setup'])) {
            $frequency = $_REQUEST['cfreq'];
            $sitemap_priority = $_REQUEST['priority'];

            if ($frequency == "--" && empty($sitemap_priority)) {
                $this->sitemap_flag = 1;
                $message = array("1" => "All required fields are empty");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            } else if ($frequency == "--" || empty($sitemap_priority)) {
                $this->sitemap_flag = 1;
                $message = array("1" => "One or more of the required fields are empty");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            } else {
                /*
                 * Insert data into the table
                 */
                $insert_xml_data = "UPDATE `pages` SET `changefreq` ='".$frequency."', `priority` = '".$sitemap_priority."' WHERE `page_id` = '".$page_id."'";
        
                $insert_res = $this->_mysqli->query($insert_xml_data);
                if ($insert_res) {
                    $this->sitemap_flag = 1;
                    $message = array("1" => "Sitemap data successfully updated.");
                    array_push($this->messages, $message);
                    $this->alert_class = "success";
                }
            }
        }
        ?>
        <?php
        if ($this->sitemap_flag == 1) {
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp; Sitemap settings</h5>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-2"></div>
                    <div class="col-md-5">
                        <form method="post">
                            <div class="form-group">
                                <label>How frequently the page is likely to change?</label>
                                <select class="form-control" name="cfreq">
                                    <option value="--">--Select--</option>
                                    <?php
                                    foreach ($changefreq as $cf) {
                                        $selected = '';
                                        $selected_item = isset($_REQUEST['cfreq']) ? $_REQUEST['cfreq'] : $cFreq;

                                        if (isset($_REQUEST['cfreq']) && $selected_item == $cf || $selected_item == $cf) {
                                            $selected = 'selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        ?>
                                        <option value="<?= $cf ?>" <?= $selected ?>><?= $cf ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Priority (Range From 0.0 to 1.0)</label>
                                <input type="text" value="<?= isset($_REQUEST['priority']) ? $_REQUEST['priority'] : $priority ?>" name="priority" class="form-control" width="30px"/>
                            </div>
                            <div class="form-group">
                                <input type="hidden" value="sitemap-setup" name="option"/>
                                <input type="submit" name="setup" value="Set Sitemap Values" class="btn btn-success"/>
                            </div>
                        </form>  
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

}
