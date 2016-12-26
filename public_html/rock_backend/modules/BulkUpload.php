<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BulkUpload
 *
 * @author rostom
 */
class BulkUpload {

    private $_mysqli;
    private $_db;
    public $fetch_res;
    public $flag = 0;
    public $messages = array();
    public $notice = array();
    public $alert_class;
    public $table_c;
    public $clicks = 0;
    public $display = "";
    public $upload_command = "";
    public $fields;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->_mysqli->set_charset("utf8");
    }

    public function BulkUploadManager() {
        if (isset($_REQUEST['do_create_table'])) {
            /*
             * first check the values
             * Check the table name in db
             */
            $table_name = isset($_REQUEST['table_name']) ? trim($_REQUEST['table_name']) : '';
            $no_spaces_t = str_replace(" ", "-", $table_name);
            $no_upper_case_t = strtolower($no_spaces_t);
            $no_ands_t = str_replace("&", "and", $no_upper_case_t);
            $no_special_chars_for_table_name_t = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands_t);
            $t_name_modified = "Rock_" . $no_special_chars_for_table_name_t;

            $num_cols = isset($_REQUEST['num_cols']) ? $_REQUEST['num_cols'] : '';

            if (empty($table_name) && empty($num_cols)) {
                $this->flag = 1;
                $message = array("1" => "Please enter the table name and number of columns");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            } else if (empty($table_name) || empty($num_cols)) {
                $this->flag = 1;
                $message = array("1" => "Either the table name or number of columns are empty");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            } else if ($this->CheckTableName($t_name_modified)) {
                $this->flag = 1;
                $message = array("1" => "Table name taken");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            }
        }
        ?>


        <script type="text/javascript">
            $(document).ready(function () {
                $("span.question").hover(function() {
                    $(this).append('<div class="tooltip panel panel-default" ><p>This is a tooltip. It is typically used to explain something to a user without taking up space on the page.</p></div>');
                }, function () {
                    $("div.tooltip").remove();
                });
            });
        </script>
        <div class="panel-heading">
            <h5><strong><i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;Bulk Upload</strong></h5>
        </div>
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
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
                        <h5><strong><i class="fa fa-object-ungroup" aria-hidden="true"></i> &nbsp;Create table&nbsp;<span class="question ">?</span></strong></h5>
                    </div>

                    <div class="panel-body">
                        <form method="post">
                            <table class="table table-responsive">
                                <tr>
                                    <td>

                                        <label>Table Name  </label>
                                        <input type="text" name="table_name" value="<?= isset($_REQUEST['table_name']) ? $_REQUEST['table_name'] : '' ?>" class="form-control"/>
                                    </td>
                                    <td>
                                        <div class="col-md-4">
                                            <label>Number of Columns</label>
                                            <input type="number" name="num_cols" value="<?= isset($_REQUEST['num_cols']) ? $_REQUEST['num_cols'] : '' ?>" class="form-control"/>
                                        </div>
                                    </td>

                                </tr>
                                <tr>
                                    <td>
                                        <input type="submit" name="do_create_table" value="Go" class="btn btn-primary btn-xs"/>

                                    </td>
                                </tr>
                            </table>
                        </form>  
                    </div>
                </div>

                <?php
                if (isset($_REQUEST['do_create_table']) && $this->flag == 0) {


                    $this->TheTables($_REQUEST['table_name'], $_REQUEST['num_cols']);
                }
                if ($this->table_c == "upload_csv") {
                    $this->flag = 0;
                    unset($this->messages);
                    /// $this->UploadDataToTable(isset($_REQUEST['table_name']));
                } if (isset($_REQUEST['douploadfile'])) {
                    $this->upload_command = "do-upload";
                }
                if (isset($_REQUEST['file_upload_to_table'])) {
                    $table_n = isset($_REQUEST['table']) ? $_REQUEST['table'] : '';
                    $file_name_path = isset($_REQUEST['file']) ? $_REQUEST['file'] : '';

                    $this->DoUploadDataIntoTable($table_n, $file_name_path);
                }
                ?>
                <!--Just upload file-->
                <h3 style="text-align:center;">OR</h3>
                <?php
                $this->UploadDataToTable(isset($_FILES['uploadfile']['name']));
                ?>


                <h3 style="text-align:center;">OR</h3>
                <?php
                if ($this->flag == 3) {
                    ?>

                    <div class="alert alert-<?= $this->alert_class ?> alert-dismissible" role="alert" id="overlay">
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
                        <h5><strong><i class="fa fa-database"></i>&nbsp;Upload Data into Tables in DB</strong>&nbsp;&nbsp;&nbsp;<a href="" title="refresh"><i class="fa fa-refresh"></i></a></h5>
                    </div>
                    <div class="panel-body">
                        <form method="post">
                            <div class="form-group">
                                <label>Table:</label>
                                <select name="table" class="form-control">
                                    <option value="--">Select a Table</option>
                                    <?php
                                    $table_selected = '';
                                    foreach ($this->GETTABLES("Rock_") as $k => $table) {

                                        if (isset($_REQUEST['table'])) {
                                            if ($_REQUEST['table'] == $table['Tables_in_'.PROJECT_NAME.'(%Rock_%)']) {
                                                $table_selected = 'selected="selected"';
                                            } else {
                                                $table_selected = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $table['Tables_in_'.PROJECT_NAME.' (%Rock_%)'] ?>" <?= $table_selected ?>><?= $table['Tables_in_'.PROJECT_NAME.' (%Rock_%)'] ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <label>File:</label>
                                <select name="file" class="form-control">
                                    <option value="--">Select a File</option>
                                    <?php
                                    $file_selected = '';

                                    foreach ($this->GetFiles() as $file) {
                                        if (isset($_REQUEST['file'])) {
                                            $full_name = $file['file_path'] . $file['file_name'];
                                            if ($_REQUEST['file'] == $full_name) {
                                                $file_selected = 'selected="selected"';
                                            } else {
                                                $file_selected = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $file['file_path'] . $file['file_name'] ?>" <?= $file_selected ?>><i class="fa fa-file-excel-o"aria-hidden="true"></i>
                                        <?= $file['file_name'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Insert Into Table" name="file_upload_to_table" class="btn btn-warning"/>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    /*
     * Page type 8 category
     */

    public function TheTables($table_name, $num_cols) {
        if (isset($_REQUEST['save_table'])) {
            $this->DoCreateTable($_REQUEST);
        }
        ?>
        <?php
        if ($this->flag == 1) {
            ?>

            <div class="alert alert-<?= $this->alert_class ?> alert-dismissible" role="alert" id="overlay">
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
        <div class="panel panel-default" style="<?= $this->display; ?>">
            <div class="panel-heading">
                <h5><strong><i class="fa fa-building"></i>&nbsp;Structure</strong></h5>
            </div>
            <div class="panel-body">
                <form method="post">
                    <table class="table table-responsive table-hover">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Length</th>
                            <th>Index</th>
                            <th>AI(auto increment)</th>
                        </tr>


                        <?php
                        for ($i = 0; $i < $num_cols; $i++) {
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <input type="text" name="f_name_<?= $table_name . "_" . $i ?>" value="<?= isset($_REQUEST['f_name_' . $table_name . "_" . $i]) ? $_REQUEST['f_name_' . $table_name . "_" . $i] : '' ?>" class="form-control"/>
                                </td>
                                <td>

                                    <select class="form-control" name="type_<?= $table_name . "_" . $i ?>">
                                        <option value="--" >--Select Type--</option>

                                        <?php
                                        $types_array = array(
                                            "INT", "TINYINT", "BIGINT", "CHAR", "VARCHAR", "TEXT", "LONGTEXT", "DATE", "DATETIME", "TIMESTAMP"
                                        );

                                        foreach ($types_array as $type) {


                                            $select_type = '';
                                            if (isset($_REQUEST['type_' . $table_name . "_" . $i]) && $_REQUEST['type_' . $table_name . "_" . $i] == $type) {

                                                $select_type = 'selected="selected"';
                                            } else {
                                                $select_type = '';
                                            }
                                            ?>
                                            <option value="<?= $type ?>" <?= $select_type ?>><?= $type ?></option>
                                            <?php
                                        }
                                        ?>

                                    </select>

                                </td>
                                <td>
                                    <input type="number" name="length_<?= $table_name . "_" . $i ?>" value="<?= isset($_REQUEST['length_' . $table_name . "_" . $i]) ? $_REQUEST['length_' . $table_name . "_" . $i] : '' ?>" class="form-control"/>
                                </td>
                                <td>
                                    <select name="index_<?= $table_name . "_" . $i ?>" class="form-control">
                                        <option value="--">--Select Index--</option>
                                        <?php
                                        $index_array = array("PRIMARY KEY", "UNIQUE", "INDEX", "FULLTEXT");
                                        foreach ($index_array as $index) {
                                            if (isset($_REQUEST['index_' . $table_name . "_" . $i]) && $_REQUEST['index_' . $table_name . "_" . $i] == $index) {
                                                $selected = 'selected="selected"';
                                            } else {
                                                $selected = '';
                                            }
                                            ?>
                                            <option value="<?= $index ?>" <?= $selected ?>><?= $index ?></option>
                                            <?php
                                        }
                                        ?>

                                    </select>
                                </td>
                                <td>
                                    <?php
                                    if (isset($_REQUEST['ai_' . $table_name . "_" . $i])) {
                                        $checked = "checked='checked'";
                                    } else {
                                        $checked = '';
                                    }
                                    ?>
                                    <input type="checkbox" name="ai_<?= $table_name . "_" . $i ?>" value="AUTO_INCREMENT" class="form-control" <?= $checked ?>/> 
                                </td>

                            </tr>
                            <?php
                        }
                        ?>

                        <tr>
                            <td>
                                <input type="hidden" name="clicks" value="<?= (int) $this->clicks++ ?>" />
                                <input type="submit" value="Save" name="save_table" class="btn btn-default" id="save-table"/>
                                <input type="hidden" value="<?= isset($_REQUEST['table_name']) ? $_REQUEST['table_name'] : '' ?>" name="table_name" />
                                <input type="hidden" value="<?= isset($_REQUEST['num_cols']) ? $_REQUEST['num_cols'] : '' ?>" name="num_cols"/>
                                <input type="hidden" name="do_create_table" value="go" />

                            </td>
                        </tr>

                    </table>
                </form>
            </div>
        </div>
        <?php
    }

    public function DoCreateTable(array $values) {
        $big_array = array();



        for ($i = 0; $i < $values['num_cols']; $i++) {
            $row_number = $i + 1;
            $array = array();
            $array['name'] = trim($values['f_name_' . $values['table_name'] . "_" . $i]);

            $filed_names = array(
                "field_name" => trim($values['f_name_' . $values['table_name'] . "_" . $i])
            );

//-----------------------------------------//
            $field_types = array(
                "field_type" => $values['type_' . $values['table_name'] . "_" . $i]
            );
            $array['type'] = $values['type_' . $values['table_name'] . "_" . $i];

//----------------------------------------//

            $field_length = array(
                "field_length" => trim($values['length_' . $values['table_name'] . "_" . $i])
            );
            if ($field_types['field_type'] == "DATE" || $field_types['field_type'] == "DATETIME" || $field_types['field_type'] == "TIMESTAMP") {
                $array['length'] = "";
            } else {
                $array['length'] = trim(' (' . $values['length_' . $values['table_name'] . "_" . $i] . ')');
            }

            $field_index = array(
                "field_index" => $values['index_' . $values['table_name'] . "_" . $i]
            );
            $array['index'] = $values['index_' . $values['table_name'] . "_" . $i];

            $auto_increment = array(
                "auto_inc" => isset($values['ai_' . $values['table_name'] . "_" . $i]) ? $values['ai_' . $values['table_name'] . "_" . $i] : ''
            );
            $array['ai'] = isset($values['ai_' . $values['table_name'] . "_" . $i]) ? $values['ai_' . $values['table_name'] . "_" . $i] : '';
            array_push($big_array, $array);




            if (empty($filed_names['field_name']) && $field_types['field_type'] == "--" && empty($field_length['field_length'])) {
                $this->flag = 1;

                $message = array("1" => "Fill in the information for row #" . $row_number . ".");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            } else if (empty($filed_names['field_name']) || $field_types['field_type'] == "--" || empty($field_length['field_length'])) {
                $this->flag = 1;

                $message = array("1" => "Fill in the information for row #" . $row_number . ".");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            } else if ($field_index['field_index'] == "--") {
                $field_index['field_index'] = "";
            } else if (!is_numeric($field_length['field_length'])) {
                $this->flag = 1;
                $message = array("1" => "Field Length must be numeric at row #" . $row_number . ".");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            } else {
                $this->flag = 0;
                if (isset($_REQUEST['clicks'])) {
                    $_REQUEST['clicks'] ++;
                    $this->clicks = $_REQUEST['clicks'];
                }
            }
        }
        if ($this->flag == 0) {
            $no_spaces = str_replace(" ", "-", $values['table_name']);
            $no_upper_case = strtolower($no_spaces);
            $no_ands = str_replace("&", "and", $no_upper_case);
            $no_special_chars_for_table_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);
            $t_name_modified = "Rock_" . $no_special_chars_for_table_name;

            /*
             * If everything is good
             * Create table
             */
            $count = (int) count($big_array);
            $sql = "CREATE TABLE IF NOT EXISTS " . $t_name_modified . ""
                    . " ( ";

            for ($j = 0; $j < count($big_array); $j++) {


                if ($big_array[$j]['index'] == "--") {
                    $big_array[$j]['index'] = "";
                }
                $sql .= "" . $big_array[$j]['name'] . " " . $big_array[$j]['type'] . " " . $big_array[$j]['length'] . " " . $big_array[$j]['index'] . " " . $big_array[$j]['ai'] . "";
                if ($j < ($count - 1)) {
                    $sql .=', ';
                }
            }

            $sql .= " ) ";
            $result = $this->_mysqli->query($sql);

            if ($result) {
                $this->flag = 1;
                unset($this->messages);
                $this->messages = array();
                $message = array("1" => "Table " . $t_name_modified . " was created. Please continue on to the next step.");
                array_push($this->messages, $message);
                $this->alert_class = "success";
//Check number of clicks
                if ($values['clicks'] > 1) {
                    if ($this->CheckTableName($t_name_modified)) {
                        $this->flag = 1;
                        unset($this->messages);
                        $this->messages = array();
                        $message = array("1" => "Table " . $t_name_modified . "  is already in the system.");
                        array_push($this->messages, $message);
                        $this->alert_class = "warning";
                    }
                }
                $this->table_c = "upload_csv";
                $this->display = "display:none;";
                $this->DoCreateNeededtables();
            }
        }
    }

    public function CheckTableName($table_name) {
        $sql = "SHOW TABLES LIKE '" . $table_name . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;

        if ($num_rows > 0) {
            return true;
        } else {
            return FALSE;
        }
    }

    public function UploadDataToTable($file_name) {
        if ($this->upload_command == "do-upload") {
            $this->table_c = "upload_csv";
            $file_name = $_FILES['uploadfile']['name'];
            if (empty($file_name) || $file_name == NULL) {
                unset($this->flag);
                $this->flag = 2;

                $message = array(
                    "1" => "File cannot be empty"
                );
                array_push($this->notice, $message);
                $this->alert_class = "warning";
            } else if ($_FILES['uploadfile']['type'] != "text/comma-separated-values") {
                $this->flag = 2;
                unset($this->notice);
                $this->notice = array();
                $message = array(
                    "1" => "File must be a csv type."
                );
                array_push($this->notice, $message);
                $this->alert_class = "warning";
            } else {
                $this->flag = 0;
                $date_added = date('d/m/y');
                $this->DoCreateNeededtables();
                if ($this->Do_Upload_files($date_added, $file_name)) {

                    $this->flag = 2;
                    $message = array(
                        "1" => "File " . $file_name . " was saved into the system successfully."
                    );
                    array_push($this->notice, $message);
                    $this->alert_class = "success";
                } else {
                    $this->flag = 2;
                    $message = array(
                        "1" => "There was an error uploading file " . $file_name . ". Please tyr again."
                    );
                    array_push($this->notice, $message);
                    $this->alert_class = "warning";
                }
            }
        }
        ?>
        <?php
        if ($this->flag == 2) {
            ?>

            <div class="alert alert-<?= $this->alert_class ?> alert-dismissible" role="alert" id="overlay">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <ul>
                    <?php
                    foreach ($this->notice as $m) {
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
        <form method="post" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5><strong><i class="fa fa-file-excel-o"></i>&nbsp;Upload CSV File</strong></h5>
                </div>
                <div class="panel-body">
                    <input type="file" name="uploadfile"  class="btn btn-default btn-xs"/>

                    <input type="submit" class="btn btn-danger btn-xs" name="douploadfile" value="Upload" style="margin-top: 10px;"/>

                </div>
            </div>
        </form>
        <?php
    }

    /*
     * Upload Files
     * It will got to a frontend folder and will have its own directory for each page type
     * Extensions allowed:
     * 1.CSV
     */

    public function Do_Upload_files($date_added, $file_n) {


// Create directory if it does not exist
        if (!is_dir(FE_FILES . "file_name_" . $file_n . "_files/")) {
            mkdir(FE_FILES . "file_name_" . $file_n . "_files/");
        }
        $upload_file = FE_FILES . "file_name_" . $file_n . "_files/" . basename($_FILES['uploadfile']['name']);

        $path = FE_FILES . "file_name_" . $file_n . "_files/";

        $uploadOk = 1;

        $uploadFileType = pathinfo($upload_file, PATHINFO_EXTENSION);

        if (isset($_POST['douploadfile'])) {



            if (isset($_POST['douploadfile']) === false) {
                $uploadOk = 0;
            } else {
                $uploadOk = 1;
            }
        }


        if ($_FILES['uploadfile']["size"] > 5000000) {
            $uploadOk == 0;
        }
        if ($uploadFileType != "csv") {
            $uploadOk == 0;
        }
        if ($uploadOk == 0) {
            
        } else {
            if (file_exists("$path/$upload_file")) {
                unlink("$path/$upload_file");
            }

            if (move_uploaded_file($_FILES['uploadfile']["tmp_name"], $upload_file)) {

                $file_name = basename($_FILES['uploadfile']['name']);

                $table = array("table" => "temp_files");
                $columns = array("`file_name`", "`file_extension`", "`file_path`", "`date_added`");

                $values = array("'" . $file_name . "'", "'" . $uploadFileType . "'", "'" . $path . "'", "'" . DATE_ADDED . "'");
                $values_to_insert = array(
                    "tables" => $table,
                    "columns" => $columns,
                    "values" => $values
                );

                $files_to_delete = array(
                    "table" => "temp_files",
                    "field" => "file_name",
                    "value" => $file_name
                );

                $check_file_in_db = $this->GetData("temp_files", "file_name", $file_name);
                if ($check_file_in_db) {
                    $do = $this->DeleteServices($files_to_delete);
                }


                $insert_file_into = $this->Insertvalues($values_to_insert);
                return true;
            } else {
                return false;
            }
        }
    }

    public function DoCreateNeededtables() {

        $sql = "CREATE TABLE IF NOT EXISTS temp_files"
                . " ( "
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "file_name VARCHAR (250) NOT NULL,"
                . "file_extension VARCHAR (250) NOT NULL,"
                . "file_path TEXT NOT NULL,"
                . "date_added VARCHAR (50) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function GetData($table, $field, $value) {

        $sql = "SELECT `id` FROM `" . $table . "` WHERE `" . $field . "` = '" . $value . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function DeleteServices(array $data) {

        $sql = "DELETE FROM `" . $data['table'] . "` WHERE `" . $data['field'] . "` = '" . $data['value'] . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function Insertvalues(array $data) {
        $sql = "INSERT INTO `" . $data['tables']['table'] . "`";
        $sql .= " ( ";
        $sql .= implode(",", $data['columns']);
        $sql .= " ) ";
        $sql .= " VALUES ";
        $sql .= " ( ";
        $sql .= implode(",", $data['values']);

        $sql .= " ) ";

        $result = $this->_mysqli->query($sql);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function GetFiles() {

        $sql = "SELECT * FROM `temp_files`";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->fetch_res[] = $row;
            }
            return $this->fetch_res;
        }
    }

    public function GETTABLES($table_name) {
        $sql = "SHOW TABLES FROM `" . PROJECT_NAME . "` LIKE '%" . $table_name . "%'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;

        if ($num_rows > 0) {
            while ($row[] = $result->fetch_array(MYSQLI_ASSOC)) {
                
            }
            return $row;
        }
    }

    public function DoUploadDataIntoTable($table_name, $file_name_path) {


        if ($table_name == "--" && $file_name_path == "--") {

            $this->flag = 3;
            $message = array("1" => "Please Select table and file that you would like to upload.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else if ($table_name == "--" || $file_name_path == "--") {

            $this->flag = 3;
            $message = array("1" => "Either the table or the file has not been seleted.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else {

            $sql = "SHOW COLUMNS FROM `" . $table_name . "`";
            $result = $this->_mysqli->query($sql);
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->table_c[] = $row['Field'];

                $this->fields[] = "`" . $row['Field'] . "`";
            }

            $file = fopen($file_name_path, "r");
            $firstLine = fgets($file);


            $foundheaders = str_getcsv(trim($firstLine), ",", '"');
            /*
             * Check to see if the file and table selected are compatible
             */
            if ($foundheaders !== $this->table_c) {
                $this->flag = 3;
                $message = array("1" => "File and table selected are not compatible.");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            } else {
                /*
                 * Start uploading'
                 * 1st check if the table is empty or not
                 * if empty use the insert command
                 * else use the update command
                 */


                /*
                 * Read file
                 */
                $rows = 1;
                if (($handle = fopen($file_name_path, "r")) !== FALSE) {
                    fgetcsv($handle);
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        $rows++;
                        $csv_data[] = array($data);

                        $combined_data[] = array_combine($foundheaders, $data);
                    }

                    $check_table = "SELECT * FROM `" . $table_name . "`";
                    $check_table_res = $this->_mysqli->query($check_table);
                    $is_empty = $check_table_res->num_rows;
                    $updates = array();
                    if ($is_empty > 0) {
                        foreach ($combined_data as $csv) {

                            /*
                             * Use update there are rows in the table
                             */
                            $get_data_to_compare = "SELECT * FROM `" . $table_name . "` WHERE `id` = '" . $csv['id'] . "'";
                            $get_data_to_compare_result = $this->_mysqli->query($get_data_to_compare);
                            $num_rows_compare = $get_data_to_compare_result->num_rows;
                            $test = 0;
                            if ($num_rows_compare > 0) {
                                while ($row = $get_data_to_compare_result->fetch_array(MYSQLI_ASSOC)) {
                                    /*
                                     * Id must be present in the csv file and must be unique
                                     */
                                    foreach ($this->table_c as $table_fields) {
                                        if ($csv['id'] == $row['id']) {
                                            if ($csv[$table_fields] == $row[$table_fields]) {
                                                $this->flag = 34;
                                                continue;
                                            } else {
                                                $test = 1;
                                                /*
                                                 * Update here
                                                 */
                                                for ($i = 0; $i < count($csv[$table_fields]); $i++) {
                                                    $sql = "UPDATE `" . $table_name . "` SET  ";
                                                    for ($j = 0; $j < count($table_fields); $j++) {
                                                        $sql .= "`" . $table_fields . "`";
                                                        $sql .= " = " . "'" . $csv[$table_fields] . "'";
                                                    }
                                                    $sql .= " WHERE `id` = '" . $csv['id'] . "'";
                                                    $update_result = $this->_mysqli->query($sql);
                                                    if ($update_result) {
                                                        $this->flag = 34;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $this->InsertCSVIntoDB($csv_data, $this->fields, $table_name, $rows);
                            }
                        }
                    } else {
                        $this->InsertCSVIntoDB($csv_data, $this->fields, $table_name, $rows);
                    }
                }
            }



            fclose($file);

            mysqli_free_result($result);
        }
        if ($this->flag == 34) {
            $this->flag = 3;
            $message = array("1" => $table_name . " was updated.");
            array_push($this->messages, $message);
            $this->alert_class = "success";
        }
    }

    public function InsertCSVIntoDB($csv_data, $fields, $table_name, $num_rows) {
        /*
         * Use insert the table is empty
         */
        foreach ($csv_data as $cvs) {

            $insert_new_data = "INSERT INTO `" . $table_name . "`";
            $insert_new_data .= " (" . implode(",", $fields) . ") ";
            $insert_new_data .= "VALUES";

            for ($p = 0; $p < count($cvs); $p++) {
                $insert_new_data.= " (" . "'" . trim(implode("','", $cvs[$p])) . "') ";
            }
            $insert_result = $this->_mysqli->query($insert_new_data);

            if ($insert_result) {
                $this->flag = 33;
            } else {
                $this->flag = 35;
            }
        }
        /*
         * handle messages
         */
        if ($this->flag == 33) {
            $this->flag = 3;
            $message = array("1" => $num_rows . " rows of data were inserted into " . $table_name . ".");
            array_push($this->messages, $message);
            $this->alert_class = "success";
        } else if ($this->flag == 35) {
            $this->flag = 3;
            $message = array("1" => "There was an error, unable to insert data.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        }
    }

}
