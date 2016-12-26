<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SubCategory
 *
 * @author rostom
 */
class SubCategory {

    private $_mysqli;
    private $_db;
    public $messages = array();
    public $flag;
    public $alert_class;
    public $table_c;
    public $fields;
    public $res;
    public $data_for_sub_pages;
    public $distinct_values;
    public $code = 0;
    public $product_field_chooser;
    public $parent;
    public $top_parent;
    public $query_res;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->product_field_chooser = new ProductPage();
    }

    public function ChooseSubCategories($page_id, $page_type) {
        if (isset($_REQUEST['get_table_fields'])) {
            $table_name = isset($_REQUEST['table']) ? $_REQUEST['table'] : '';
            $this->GettableFields($table_name);
            if (isset($_REQUEST['get_second_field'])) {
                $column = isset($_REQUEST['prime_field']) ? $_REQUEST['prime_field'] : '';
                $this->GetDataFromSelectedColumn($table_name, $column);

                if (isset($_REQUEST['choose_next'])) {
                    $this->table_c = NULL;
                    $this->GettableFields($table_name);

                    if (isset($_REQUEST['create_sub_pages'])) {
                        $column_choice = isset($_REQUEST['column_choice']) ? $_REQUEST['column_choice'] : '';
                        $secondary = isset($_REQUEST['secondary_field']) ? $_REQUEST['secondary_field'] : '';
                        $data_to_collect = array(
                            "table_name" => $table_name,
                            "field_1" => $column,
                            "field_2" => $secondary,
                            "column_choice" => $column_choice,
                            "page_id" => $page_id
                        );
                        $this->CreateSubPages($data_to_collect);
                    }
                }
            }
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5><strong><i class="fa fa-object-group" aria-hidden="true"></i>&nbsp; Create Categories and Sub-Categories</strong></h5>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <h4>Procedures:</h4>
                        <ol>
                            <li>Choose table which you would like to extract data from.</li>
                            <li>Select the primary field from chosen table.</li>
                            <li>Select the secondary field from chosen table.</li>
                        </ol>
                    </div>
                    <!--Step1-->
                    <div class="col-md-4">
                        <h4>Step 1</h4>
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h5><strong><i class="fa fa-stack-exchange"></i>&nbsp;Choose Table</strong></h5>
                            </div>
                            <form method="post">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Table:</label>
                                        <select name="table" class="form-control">
                                            <option value="--">Select a Table</option>
                                            <?php
                                            $table_selected = '';
                                            foreach ($this->GetTableNames("Rock_") as $k => $table) {

                                                if (isset($_REQUEST['table'])) {
                                                    if ($_REQUEST['table'] == $table['Tables_in_'.PROJECT_NAME.'(%Rock_%)']) {
                                                        $table_selected = 'selected="selected"';
                                                    } else {
                                                        $table_selected = '';
                                                    }
                                                }
                                                ?>
                                                <option value="<?= $table['Tables_in_'.PROJECT_NAME.'(%Rock_%)'] ?>" <?= $table_selected ?>><?= $table['Tables_in_'.PROJECT_NAME.'(%Rock_%)'] ?></option>
                                                <?php
                                            }
                                            ?>

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" name="get_table_fields" class="btn- btn-warning btn-xs" value="get fields"/> 
                                        <input type="hidden" value="<?= $page_id ?>" name="page_id"/>
                                        <input type="hidden" value="create-cats" name="option"/>
                                        <input type="hidden" name="cmd" value="edit_page"/>
                                        <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--Step 2-->
                        <h4>Step 2</h4>
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h5><strong><i class="fa fa-stack-exchange"></i>&nbsp;Choose Primary Field</strong></h5>
                                <span style="font-style: italic;">(i.e: by gender, by category, by brand)</span>

                            </div>

                            <div class="panel-body">
                                <?php
                                if ($this->table_c != NULL) {
                                    ?>
                                    <form method="post">
                                        <div class="form-group">
                                            <select name="prime_field" class="form-control">
                                                <option value="--">Select primary field</option>

                                                <?php
                                                $selected_field = "";
                                                foreach ($this->table_c as $table_fields) {
                                                    if (isset($_REQUEST['prime_field'])) {
                                                        if ($_REQUEST['prime_field'] == $table_fields) {
                                                            $selected_field = 'selected="selected"';
                                                        } else {
                                                            $selected_field = '';
                                                        }
                                                    }
                                                    ?>
                                                    <option value="<?= $table_fields ?>" <?= $selected_field ?>><?= $table_fields ?></option>

                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" name="get_second_field" class="btn- btn-warning btn-xs" value="Select"/> 
                                            <input type="hidden" value="<?= $page_id ?>" name="page_id"/>
                                            <input type="hidden" value="create-cats" name="option"/>
                                            <input type="hidden" name="cmd" value="edit_page"/>
                                            <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>
                                            <input type="hidden" name="get_table_fields" value="xyz"/>
                                            <input type="hidden" name="table" value="<?= isset($_REQUEST['table']) ? $_REQUEST['table'] : '' ?>"/>
                                        </div>
                                    </form>
                                    <?php
                                } else {
                                    echo "No table has been selected yet!";
                                }
                                ?>


                            </div>
                        </div>

                    </div>
                    <!--End of div-->
                    <div class="col-md-4">
                        <!--Step 3-->
                        <h4>Step 3</h4>
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h5><strong><i class="fa fa-stack-exchange"></i>&nbsp;Choose From <?= isset($_REQUEST['prime_field']) ? $_REQUEST['prime_field'] : 'TBD'; ?> Column</strong></h5>
                                <span style="font-style: italic;">(i.e: mens, womens, computers, etc.)</span>

                            </div>

                            <div class="panel-body">
                                <?php
                                if (isset($_REQUEST['prime_field'])) {
                                    ?>
                                    <form method="post">
                                        <div class="form-group">
                                            <select name="column_choice" class="form-control">
                                                <option value="--">Select one</option>
                                                <?php
                                                $selected_c = "";
                                                if ($this->res != NULL) {
                                                    foreach ($this->res as $column_vals) {
                                                        if (isset($_REQUEST['column_choice'])) {
                                                            if ($_REQUEST['column_choice'] == $column_vals[$_REQUEST['prime_field']]) {
                                                                $selected_c = 'selected="selected"';
                                                            } else {
                                                                $selected_c = '';
                                                            }
                                                        }
                                                        ?>
                                                        <option value="<?= $column_vals[$_REQUEST['prime_field']] ?>" <?= $selected_c ?>><?= $column_vals[$_REQUEST['prime_field']] ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" name="choose_next" class="btn- btn-warning btn-xs" value="Choose Next field"/> 
                                            <input type="hidden" value="<?= $page_id ?>" name="page_id"/>
                                            <input type="hidden" value="create-cats" name="option"/>
                                            <input type="hidden" name="cmd" value="edit_page"/>
                                            <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>
                                            <input type="hidden" name="get_table_fields" value="xyz"/>
                                            <input type="hidden" name="table" value="<?= isset($_REQUEST['table']) ? $_REQUEST['table'] : '' ?>"/>
                                            <input type="hidden" name="prime_field" value="<?= isset($_REQUEST['prime_field']) ? $_REQUEST['prime_field'] : '' ?>"/>
                                            <input type="hidden" name="get_second_field"  value="Select"/> 
                                        </div>
                                    </form>
                                    <?php
                                } else {
                                    echo "No field has been selected yet!";
                                }
                                ?>


                            </div>
                        </div>
                        <!--END of secondary div-->
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h5><strong><i class="fa fa-stack-exchange"></i>&nbsp; Choose Secondary Filed</strong></h5>
                            </div>
                            <div class="panel-body">
                                <?php
                                if (isset($_REQUEST['choose_next'])) {
                                    ?>
                                    <form method="post">
                                        <div class="form-group">
                                            <select name="secondary_field" class="form-control">
                                                <option value="--">Select Secondary field</option>

                                                <?php
                                                $selected_field = "";
                                                foreach ($this->table_c as $table_fields) {
                                                    if (isset($_REQUEST['secondary_field'])) {
                                                        if ($_REQUEST['secondary_field'] == $table_fields) {
                                                            $selected_field = 'selected="selected"';
                                                        } else {
                                                            $selected_field = '';
                                                        }
                                                    }
                                                    ?>
                                                    <option value="<?= $table_fields ?>" <?= $selected_field ?>><?= $table_fields ?></option>

                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" name="create_sub_pages" class="btn- btn-warning btn-xs" value="Choose Next field"/> 
                                            <input type="hidden" value="<?= $page_id ?>" name="page_id"/>
                                            <input type="hidden" value="create-cats" name="option"/>
                                            <input type="hidden" name="cmd" value="edit_page"/>
                                            <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>
                                            <input type="hidden" name="get_table_fields" value="xyz"/>
                                            <input type="hidden" name="table" value="<?= isset($_REQUEST['table']) ? $_REQUEST['table'] : '' ?>"/>
                                            <input type="hidden" name="prime_field" value="<?= isset($_REQUEST['prime_field']) ? $_REQUEST['prime_field'] : '' ?>"/>
                                            <input type="hidden" name="column_choice" value="<?= isset($_REQUEST['column_choice']) ? $_REQUEST['column_choice'] : '' ?>"/>
                                            <input type="hidden" name="choose_next" value="xyz"/>

                                            <input type="hidden" name="get_second_field"  value="Select"/> 
                                        </div>
                                    </form>
                                    <?php
                                } else {
                                    echo "No field has been selected yet!";
                                }
                                ?>

                            </div>
                        </div>
                        <!--End of list-->
                        <?php
                        $get_page_id = "SELECT `id`, `page_parent` FROM `pages` WHERE `page_id` = '" . $page_id . "'";
                        $get_page_id_res = $this->_mysqli->query($get_page_id);
                        while ($real_page_id = $get_page_id_res->fetch_array(MYSQLI_ASSOC)) {
                            $this->query_res = NULL;
                            $this->FindParent($real_page_id['page_parent']);
                            if ($this->query_res != NULL) {
                                foreach ($this->query_res as $pg_type) {

                                    if ($pg_type['page_type'] == 7) {
                                        ?>
                                        <div class="panel panel-warning">
                                            <div class="panel-heading">
                                                <h5><strong><i class="fa fa-copyright"></i>&nbsp; Choose Brand</strong></h5>
                                            </div>
                                            <div class="panel-body">
                                                <?php
                                                if (isset($_REQUEST['create_sub_pages'])) {
                                                    ?>
                                                    <form method="post">
                                                        <div class="form-group">
                                                            <select name="brand_selection" class="form-control">
                                                                <option value="--">Select Secondary field</option>

                                                                <?php
                                                                $selected_field = "";
                                                                foreach ($this->table_c as $table_fields) {
                                                                    if (isset($_REQUEST['brand_selection'])) {
                                                                        if ($_REQUEST['brand_selection'] == $table_fields) {
                                                                            $selected_field = 'selected="selected"';
                                                                        } else {
                                                                            $selected_field = '';
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <option value="<?= $table_fields ?>" <?= $selected_field ?>><?= $table_fields ?></option>

                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="submit" name="choose_brand" class="btn- btn-warning btn-xs" value="Choose Next field"/> 
                                                            <input type="hidden" value="<?= $page_id ?>" name="page_id"/>
                                                            <input type="hidden" value="create-cats" name="option"/>
                                                            <input type="hidden" name="cmd" value="edit_page"/>
                                                            <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>
                                                            <input type="hidden" name="get_table_fields" value="xyz"/>
                                                            <input type="hidden" name="table" value="<?= isset($_REQUEST['table']) ? $_REQUEST['table'] : '' ?>"/>
                                                            <input type="hidden" name="prime_field" value="<?= isset($_REQUEST['prime_field']) ? $_REQUEST['prime_field'] : '' ?>"/>
                                                            <input type="hidden" name="column_choice" value="<?= isset($_REQUEST['column_choice']) ? $_REQUEST['column_choice'] : '' ?>"/>
                                                            <input type="hidden" name="choose_next" value="xyz"/>
                                                            <input type="hidden" name="secondary_field" value="<?= isset($_REQUEST['secondary_field']) ? $_REQUEST['secondary_field'] : '' ?>"/>
                                                            <input type="hidden" name="create_sub_pages" value="get brand"/>
                                                            <input type="hidden" name="get_second_field"  value="Select"/> 
                                                        </div>
                                                    </form>
                                                    <?php
                                                } else {
                                                    echo "No field has been selected yet!";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>

                    </div>
                </div>

            </div>
        </div>
        <?php
        if (isset($_REQUEST['create_sub_pages']) && $this->code == 100) {
            $this->table_c = NULL;
            $this->GettableFields($table_name);
            $this->product_field_chooser->PageProductsFiledChooser($this->table_c, $page_id);
        }
        ?>

        <?php
    }

    public function GetTableNames($table_name) {

        $sql = "SHOW TABLES FROM `" . PROJECT_NAME . "` LIKE '%" . $table_name . "%'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;

        if ($num_rows > 0) {
            while ($row[] = $result->fetch_array(MYSQLI_ASSOC)) {
                
            }
            return $row;
        }
    }

    public function GettableFields($table_name) {
        if ($table_name == "--") {
            $this->flag = 1;
            $message = array("1" => "Please select a table");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else {
            $sql = "SHOW COLUMNS FROM `" . $table_name . "`";
            $result = $this->_mysqli->query($sql);
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->table_c[] = $row['Field'];

                $this->fields[] = "`" . $row['Field'] . "`";
            }
        }
    }

    public function GetDataFromSelectedColumn($table_name, $column) {
        if ($column == "--") {
            $this->flag = 1;
            $message = array("1" => "Please select a field.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else {

            $sql = "SELECT DISTINCT `" . $column . "` FROM `" . $table_name . "`";
            $result = $this->_mysqli->query($sql);

            if ($result) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                    $this->res[] = $row;
                }

                return $this->res;
            } else {
                return false;
            }
        }
    }

    public function CreateSubPages(array $data) {
        $this->CreateProductPagesTable();
        if ($data['field_2'] == "--") {
            $this->flag = 1;
            $message = array("1" => "Please select a field.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else {

            /*
             * First Create the the top page
             */
            $select_parent_page_name = "SELECT `page_name`, `id` FROM `pages` WHERE `page_id` = '" . $data['page_id'] . "'";
            $select_parent_page_name_res = $this->_mysqli->query($select_parent_page_name);
            while ($parent_page_name = $select_parent_page_name_res->fetch_array(MYSQLI_ASSOC)) {

                $select_distinct = "SELECT DISTINCT `" . $data['field_2'] . "` FROM `" . $data['table_name'] . "` WHERE `" . $data['field_1'] . "` = '" . $data['column_choice'] . "'";
                $distinct_result = $this->_mysqli->query($select_distinct);
                if ($distinct_result) {
                    while ($row = $distinct_result->fetch_array(MYSQLI_ASSOC)) {
                        $this->distinct_values[] = $row;
                        $get_data = "SELECT * FROM `" . $data['table_name'] . "` WHERE `" . $data['field_1'] . "` = '" . $data['column_choice'] . "' AND `" . $data['field_2'] . "` = '" . $row[$data['field_2']] . "'";
                        $data_res = $this->_mysqli->query($get_data);
                        while ($new_data = $data_res->fetch_array(MYSQLI_ASSOC)) {

                            $this->data_for_sub_pages[] = $new_data;
                        }
                    }
                }

                /*
                 * Now create pages
                 * category tyep = 8
                 * sub categroy type = 9
                 * product tyep= 10
                 * first will be the distinct ones
                 * insert into:
                 * pages
                 * page_meta_data
                 * page_special
                 * page_content
                 * page_url_option
                 * page_alias
                 */

                $date_added = date('m/d/y');
                $get_page_parent = "SELECT id FROM `pages` WHERE `page_id` = '" . $data['page_id'] . "'";
                $get_page_parent_result = $this->_mysqli->query($get_page_parent);
                while ($parent = $get_page_parent_result->fetch_array(MYSQLI_ASSOC)) {



                    foreach ($this->distinct_values as $top_pages) {


                        $check_if_exists = "SELECT id FROM `pages` WHERE `page_parent` = '" . $parent['id'] . "' AND `page_name` = '" . $top_pages[$data['field_2']] . "'";
                        $check_if_exists_res = $this->_mysqli->query($check_if_exists);
                        $get_num_rows = $check_if_exists_res->num_rows;
                        $UUID = uniqid();
                        if ($get_num_rows == 0) {


                            $insert_new_top_sub_pages = "INSERT INTO `pages` (page_name, page_type, page_parent, date_created, page_id)"
                                    . " VALUES"
                                    . "('" . implode("','", $top_pages) . "', '9', '" . $parent['id'] . "', '" . $date_added . "','" . $UUID . "')";

                            $insert_into_pages_result = $this->_mysqli->query($insert_new_top_sub_pages);

                            $insert_into_page_meta = "INSERT INTO `page_meta_data` (page_id, date_modified) VALUES ('" . $UUID . "', '" . $date_added . "')";
                            $insert_into_page_meta_res = $this->_mysqli->query($insert_into_page_meta);

                            $add_info_to_special = "INSERT INTO `page_special` (page_id, date_modified) VALUES ('" . $UUID . "', '" . $date_added . "')";
                            $add_info_to_special_res = $this->_mysqli->query($add_info_to_special);

                            $add_page_info_to_content = "INSERT INTO `page_content` (page_id, date_added) VALUES ('" . $UUID . "', '" . $date_added . "')";
                            $add_page_info_to_content_res = $this->_mysqli->query($add_page_info_to_content);

                            $insert_into_url_options = "INSERT INTO `page_url_option` (page_id, url_option, date_modified) VALUES ('" . $UUID . "', 'long', '" . $date_added . "' )";
                            $insert_into_url_options_res = $this->_mysqli->query($insert_into_url_options);

                            $insert_page_alias = "INSERT INTO `page_alias` (page_id, date_added) VALUES ('" . $UUID . "' , '" . $date_added . "')";
                            $insert_page_alias_res = $this->_mysqli->query($insert_page_alias);
                            /*
                             * Get the id of the new page that just got added
                             * it will be the parent id for the next pages that are created
                             */

                            $this->flag = 1;
                            $message = array(
                                "1" => "Page " . $top_pages[$data['field_2']] . " was added to the system"
                            );
                            array_push($this->messages, $message);
                            $this->alert_class = "success";
                            $this->code = 100;
                        } else {
                            $this->flag = 1;
                            $message = array(
                                "1" => "Page " . $top_pages[$data['field_2']] . " already Exists."
                            );
                            array_push($this->messages, $message);
                            $this->alert_class = "warning";
                            $this->code = 100;
                        }
                    }
                }
            }
            if (isset($_REQUEST['add_products'])) {


                $top_parents = "SELECT `id`, `page_name` FROM `pages` WHERE `page_id` = '" . $data['page_id'] . "'";

                $top_parents_res = $this->_mysqli->query($top_parents);
                while ($r = $top_parents_res->fetch_array(MYSQLI_ASSOC)) {

                    $find_child = "SELECT `id`, `page_name` FROM `pages` WHERE `page_parent` ='" . $r['id'] . "'";
                    $find_child_res = $this->_mysqli->query($find_child);
                    while ($c = $find_child_res->fetch_array(MYSQLI_ASSOC)) {


                        foreach ($this->data_for_sub_pages as $sub) {
                            if ($sub[$data['field_2']] == $c['page_name']) {

                                $product_parent = $c['id'];



                                if (isset($_REQUEST['item_name']) && $_REQUEST['item_name'] != "--") {
                                    $item_name = $sub[$_REQUEST['item_name']];
                                } else {
                                    $item_name = "";
                                }




                                if (isset($_REQUEST['model_number']) && $_REQUEST['model_number'] != "--") {
                                    $model_number = $sub[$_REQUEST['model_number']];
                                } else {
                                    $model_number = "";
                                }
                                $check_if_page_exists = "SELECT id FROM `pages_products` WHERE `page_parent` = '" . $product_parent . "' AND `model_number` = '" . $model_number . "'";
                                $check_if_page_exists_res = $this->_mysqli->query($check_if_page_exists);

                                $product_page_num_rows = $check_if_page_exists_res->num_rows;

                                if (isset($_REQUEST['price']) && $_REQUEST['price'] != "--") {
                                    $price = $sub[$_REQUEST['price']];
                                } else {
                                    $price = "";
                                }
                                if (isset($_REQUEST['color']) && $_REQUEST['color'] != "--") {
                                    $color = $sub[$_REQUEST['color']];
                                } else {
                                    $color = "";
                                }
                                if (isset($_REQUEST['size']) && $_REQUEST['size'] != "--") {
                                    $size = $sub[$_REQUEST['size']];
                                } else {
                                    $size = "";
                                }
                                if (isset($_REQUEST['weight']) && $_REQUEST['weight'] != "--") {
                                    $weight = $sub[$_REQUEST['weight']];
                                } else {
                                    $weight = "";
                                }
                                if (isset($_REQUEST['image_0']) && $_REQUEST['image_0'] != "--") {
                                    $image_0 = $sub[$_REQUEST['image_0']];
                                } else {
                                    $image_0 = "";
                                }
                                if (isset($_REQUEST['image_1']) && $_REQUEST['image_1'] != "--") {
                                    $image_1 = $sub[$_REQUEST['image_1']];
                                } else {
                                    $image_1 = "";
                                }
                                if (isset($_REQUEST['image_2']) && $_REQUEST['image_2'] != "--") {
                                    $image_2 = $sub[$_REQUEST['image_2']];
                                } else {
                                    $image_2 = "";
                                }
                                if (isset($_REQUEST['image_3']) && $_REQUEST['image_3'] != "--") {
                                    $image_3 = $sub[$_REQUEST['image_3']];
                                } else {
                                    $image_3 = "";
                                }
                                if (isset($_REQUEST['image_4']) && $_REQUEST['image_4'] != "--") {
                                    $image_4 = $sub[$_REQUEST['image_4']];
                                } else {
                                    $image_4 = "";
                                }
                                if (isset($_REQUEST['image_5']) && $_REQUEST['image_5'] != "--") {
                                    $image_5 = $sub[$_REQUEST['image_5']];
                                } else {
                                    $image_5 = "";
                                }
                                if (isset($_REQUEST['image_6']) && $_REQUEST['image_6'] != "--") {
                                    $image_6 = $sub[$_REQUEST['image_6']];
                                } else {
                                    $image_6 = "";
                                }
                                if (isset($_REQUEST['image_7']) && $_REQUEST['image_7'] != "--") {
                                    $image_7 = $sub[$_REQUEST['image_7']];
                                } else {
                                    $image_7 = "";
                                }
                                if (isset($_REQUEST['image_8']) && $_REQUEST['image_8'] != "--") {
                                    $image_8 = $sub[$_REQUEST['image_8']];
                                } else {
                                    $image_8 = "";
                                }
                                if (isset($_REQUEST['brand']) && $_REQUEST['brand'] != "--") {
                                    $brand = $sub[$_REQUEST['brand']];
                                } else {
                                    $brand = "";
                                }
                                if (isset($_REQUEST['manufacturer']) && $_REQUEST['manufacturer'] != "--") {
                                    $manufacturer = $sub[$_REQUEST['manufacturer']];
                                } else {
                                    $manufacturer = "";
                                }
                                if (isset($_REQUEST['description']) && $_REQUEST['description'] != "--") {
                                    $description = $sub[$_REQUEST['description']];
                                } else {
                                    $description = "";
                                }
                                if (isset($_REQUEST['category']) && $_REQUEST['category'] != "--") {
                                    $category = $sub[$_REQUEST['category']];
                                } else {
                                    $category = "";
                                }
                                if (isset($_REQUEST['tags']) && $_REQUEST['tags'] != "--") {
                                    $tags = $sub[$_REQUEST['tags']];
                                } else {
                                    $tags = "";
                                }
                                if (isset($_REQUEST['shippable']) && $_REQUEST['shippable'] != "--") {
                                    $shippable = $sub[$_REQUEST['shippable']];
                                } else {
                                    $shippable = "";
                                }
                                if (isset($_REQUEST['currency']) && $_REQUEST['currency'] != "--") {
                                    $currency = $sub[$_REQUEST['currency']];
                                } else {
                                    $currency = "";
                                }
                                if (isset($_REQUEST['keywords']) && $_REQUEST['keywords'] != "--") {
                                    $keywords = $sub[$_REQUEST['keywords']];
                                } else {
                                    $keywords = "";
                                }
                                if (isset($_REQUEST['year']) && $_REQUEST['year'] != "--") {
                                    $year = $sub[$_REQUEST['year']];
                                } else {
                                    $year = "";
                                }
                                if (isset($_REQUEST['gender']) && $_REQUEST['gender'] != "--") {
                                    $gender = $sub[$_REQUEST['gender']];
                                } else {
                                    $gender = "";
                                }
                                if (isset($_REQUEST['item_status']) && $_REQUEST['item_status'] != "--") {
                                    $item_status = $sub[$_REQUEST['item_status']];
                                } else {
                                    $item_status = "";
                                }
                                if (isset($_REQUEST['item_version']) && $_REQUEST['item_version'] != "--") {
                                    $item_version = $sub[$_REQUEST['item_version']];
                                } else {
                                    $item_version = "";
                                }
                                if (isset($_REQUEST['item_variation']) && $_REQUEST['item_variation'] != "--") {
                                    $item_variation = $sub[$_REQUEST['item_variation']];
                                } else {
                                    $item_variation = "";
                                }
                                if (isset($_REQUEST['similar_item']) && $_REQUEST['similar_item'] != "--") {
                                    $similar_item = $sub[$_REQUEST['similar_item']];
                                } else {
                                    $similar_item = "";
                                }
                                $UUIDP = uniqid();

                                if ($product_page_num_rows == 0) {

                                    $insert_data_into_pages = "INSERT INTO `pages` (page_name, page_type, page_parent, date_created, page_id) "
                                            . "VALUES"
                                            . " ('" . $item_name . "', '10', '" . $product_parent . "', '" . $date_added . "', '" . $UUIDP . "')";
                                    $insert_data_into_pages_res = $this->_mysqli->query($insert_data_into_pages);

                                    $insert_into_page_meta_products = "INSERT INTO `page_meta_data` (page_id, date_modified) VALUES ('" . $UUIDP . "', '" . $date_added . "')";
                                    $insert_into_page_meta_products_res = $this->_mysqli->query($insert_into_page_meta_products);

                                    $add_info_to_special_products = "INSERT INTO `page_special` (page_id, date_modified) VALUES ('" . $UUIDP . "', '" . $date_added . "')";
                                    $add_info_to_special_products_res = $this->_mysqli->query($add_info_to_special_products);

                                    $add_page_info_to_content_products = "INSERT INTO `page_content` (page_id, date_added) VALUES ('" . $UUIDP . "', '" . $date_added . "')";
                                    $add_page_info_to_content_products_res = $this->_mysqli->query($add_page_info_to_content_products);

                                    $insert_into_url_options_products = "INSERT INTO `page_url_option` (page_id, url_option, date_modified) VALUES ('" . $UUIDP . "', 'long', '" . $date_added . "' )";
                                    $insert_into_url_options_products_res = $this->_mysqli->query($insert_into_url_options_products);

                                    $insert_page_alias_products = "INSERT INTO `page_alias` (page_id, date_added) VALUES ('" . $UUIDP . "' , '" . $date_added . "')";
                                    $insert_page_alias_products_res = $this->_mysqli->query($insert_page_alias_products);


                                    $insert_data_into_products_pages = "INSERT INTO `pages_products`"
                                            . "(item_name, "
                                            . "model_number, "
                                            . "price, "
                                            . "color, "
                                            . "size, "
                                            . "weight, "
                                            . "image_0, "
                                            . "image_1, "
                                            . "image_2, "
                                            . "image_3, "
                                            . "image_4, "
                                            . "image_5, "
                                            . "image_6, "
                                            . "image_7, "
                                            . "image_8, "
                                            . "brand, "
                                            . "manufacturer, "
                                            . "description, "
                                            . "category, "
                                            . "tags, "
                                            . "shippable, "
                                            . "currency, "
                                            . "keywords, "
                                            . "year, "
                                            . "gender, "
                                            . "item_status, "
                                            . "item_version, "
                                            . "item_variation, "
                                            . "similar_items, "
                                            . "page_parent, "
                                            . "page_id, "
                                            . "date_added "
                                            . ")"
                                            . " VALUES "
                                            . "("
                                            . "'" . $item_name . "', "
                                            . "'" . $model_number . "', "
                                            . "'" . $price . "', "
                                            . "'" . $color . "', "
                                            . "'" . $size . "', "
                                            . "'" . $weight . "', "
                                            . "'" . $image_0 . "', "
                                            . "'" . $image_1 . "', "
                                            . "'" . $image_2 . "', "
                                            . "'" . $image_3 . "', "
                                            . "'" . $image_4 . "', "
                                            . "'" . $image_5 . "', "
                                            . "'" . $image_6 . "', "
                                            . "'" . $image_7 . "', "
                                            . "'" . $image_8 . "', "
                                            . "'" . $brand . "', "
                                            . "'" . $manufacturer . "', "
                                            . "'" . $description . "', "
                                            . "'" . $category . "', "
                                            . "'" . $tags . "', "
                                            . "'" . $shippable . "', "
                                            . "'" . $currency . "', "
                                            . "'" . $keywords . "', "
                                            . "'" . $year . "', "
                                            . "'" . $gender . "', "
                                            . "'" . $item_status . "', "
                                            . "'" . $item_version . "', "
                                            . "'" . $item_variation . "', "
                                            . "'" . $similar_item . "', "
                                            . "'" . $product_parent . "', "
                                            . "'" . $UUIDP . "', "
                                            . "'" . $date_added . "'"
                                            . ") ";

                                    $insert_data_into_products_pages_res = $this->_mysqli->query($insert_data_into_products_pages);


                                    $this->flag = 1;
                                    $message = array(
                                        "1" => "Page " . $item_name . " was added to the system"
                                    );
                                    array_push($this->messages, $message);
                                    $this->alert_class = "success";
                                    $this->code = 100;
                                } else {
                                    $this->flag = 1;
                                    $message = array(
                                        "1" => "Page " . $item_name . " already Exists."
                                    );
                                    array_push($this->messages, $message);
                                    $this->alert_class = "warning";
                                    $this->code = 100;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function CreateProductPagesTable() {
        /*
         * Create product page based on fields from temp
         */
        $create_product_page = "CREATE TABLE IF NOT EXISTS pages_products"
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "item_name VARCHAR (500) NOT NULL,"
                . "model_number VARCHAR (500) NOT NULL,"
                . "price VARCHAR(100) NOT NULL,"
                . "color VARCHAR (250) NOT NULL,"
                . "size VARCHAR (250) NOT NULL,"
                . "weight VARCHAR (250) NOT NULL,"
                . "image_0 VARCHAR (500) NOT NULL,"
                . "image_1 VARCHAR (500) NOT NULL,"
                . "image_2 VARCHAR (500) NOT NULL,"
                . "image_3 VARCHAR (500) NOT NULL,"
                . "image_4 VARCHAR (500) NOT NULL,"
                . "image_5 VARCHAR (500) NOT NULL,"
                . "image_6 VARCHAR (500) NOT NULL,"
                . "image_7 VARCHAR (500) NOT NULL,"
                . "image_8 VARCHAR (500) NOT NULL,"
                . "brand VARCHAR (500) NOT NULL,"
                . "manufacturer VARCHAR (500) NOT NULL,"
                . "description TEXT,"
                . "category VARCHAR (500) NOT NULL,"
                . "tags VARCHAR (500) NOT NULL,"
                . "shippable VARCHAR (10) NOT NULL,"
                . "currency VARCHAR (10) NOT NULL,"
                . "keywords VARCHAR (250) NOT NULL,"
                . "year VARCHAR (20) NOT NULL,"
                . "gender VARCHAR (100) NOT NULL,"
                . "item_status VARCHAR (10) NOT NULL,"
                . "item_version VARCHAR (250) NOT NULL,"
                . "item_variation VARCHAR (250) NOT NULL,"
                . "similar_items VARCHAR (250) NOT NULL,"
                . "wholesale_p VARCHAR (250) NOT NULL,"
                . "wholesale_qty_on_hand VARCHAR (250) NOT NULL, "
                . "wholesale_qty_in_case VARCHAR (250) NOT NULL, "
                . "page_parent INT(10) NOT NULL,"
                . "page_id VARCHAR(500) NOT NULL,"
                . "date_added VARCHAR (100) NOT NULL"
                . ")";

        $result = $this->_mysqli->query($create_product_page);
    }

    public function FindParent($page_parent) {
        $sql = "SELECT * FROM `pages` WHERE `id` = '" . $page_parent . "'";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if ($row['page_parent'] == 0) {
                $this->query_res[] = $row;
            }
            $this->FindParent($row['page_parent']);
        }
        return $this->query_res;
    }

}
