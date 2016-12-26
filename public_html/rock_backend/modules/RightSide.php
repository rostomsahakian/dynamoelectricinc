<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RightSide
 *
 * @author rostom
 */
class RightSide {

    private $_mysqli;
    private $_db;
    public $_res = array();
    public $_page_id;
    public $today;
    public $messages = array();
    public $flag = 0;
    public $page_images;
    public $image_flag = 0;
    public $image_id;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->today = date('m/d/y');
        $this->CreateImagesTable();
    }

    public function LoadRightSide($page_id) {
        $this->_page_id = $page_id;

        if (isset($_REQUEST['douploadimage'])) {
            $do_upload_image = $this->Do_Upload_images($_POST['douploadimage'], FE_IMAGES, $this->today, $page_id, NULL);
            if ($do_upload_image) {
                $this->flag = 1;
                $message = array("1" => "Image successfully uploaded");
                array_push($this->messages, $message);
                $this->alert_class = "success";
            } else if (isset($_FILES["uploadimage"]['name'])) {

                if (empty($_FILES["uploadimage"]['name'])) {
                    $this->flag = 1;
                    $message = array("1" => "No Imag selected.");
                    array_push($this->messages, $message);
                    $this->alert_class = "warning";
                } else if ($_FILES["uploadimage"]['size'] == 0) {
                    $this->flag = 1;
                    $message = array("1" => "No image selected.");
                    array_push($this->messages, $message);
                    $this->alert_class = "warning";
                }
            } else {
                $this->flag = 1;
                $message = array("1" => "System was unable to upload the image.");
                array_push($this->messages, $message);
                $this->alert_class = "warning";
            }
        }
        if (isset($_REQUEST['delete_image'])) {
            $this->DeleteImage($page_id, $_POST['image_id']);
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
        <div class="rock-cont-div">
            <form method="post" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><strong><i class="fa fa-image"></i>&nbsp;Upload Images</strong></h5>
                    </div>
                    <div class="panel-body">
                        <input type="file" name="uploadimage"  class="btn btn-default btn-xs"/>

                        <input type="submit" class="btn btn-danger btn-xs" name="douploadimage" value="Upload" style="margin-top: 10px;"/>
                        <input type="hidden" name="option" value="u_image" />
                    </div>
                </div>
            </form>
            <div class="rock-cont-div">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><strong>Uploaded Images</strong></h5>
                    </div>
                    <div class="panel-body">
                        <p>To use your uploaded images simply copy the URL below and get the image name from the image.</p>
                        <p><strong><i class="fa fa-anchor" aria-hidden="true"></i>:&nbsp; <span style="font-style:italic;">../rock_frontend/frontend_assets/images/page_id_<?= $page_id ?>_images/</span></strong></p>
                        <div class="col-md-12">
                            <hr/>
                        </div>
                        <?php
                        if ($this->GetAllImages($page_id) == 1) {
                            echo "There are currently 0 images for this page";
                        } else {
                            foreach ($this->ReturnImages() as $images) {
                                $this->image_id = $images['id'];
                                ?>
                                <div class="col-md-4 rock-cont-div">
                                    <a href="<?= $images['image_path'] . $images['image_name'] ?>" target="_BLANK" style="font-size:8px;">
                                        <img src="<?= $images['image_path'] . $images['image_name'] ?>" style="width: 90%;" class="thumbnail" />
                                        <br/>
                                        <label><?= $images['image_name'] ?></label>
                                    </a>
                                    <form method="post">
                                        <input type="submit" value="delete" class="btn btn-danger btn-xs" name="delete_image"/>
                                        <input type="hidden" value="<?= $images['id'] ?>"  name="image_id"/>
                                        <input type="hidden" name="option" value="u_image"/>
                                        <input type="hidden" name="image_name" value="<?= $images['image_name'] ?>"/>
                                        <input type="hidden" name="image_path" value="<?= $images['iamge_path'] ?>" />
                                    </form>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /*
     * Upload Images
     * It will got to a frontend fold and will have its own directory for each page type
     */

    public function Do_Upload_images($image, $path, $date_added, $page_uid, $page_type = NULL) {



        $data = array(
            "table" => "page_images",
            "field" => "page_id",
            "value" => $page_uid
        );
        $get_number_of_images = $this->GetNumberOfImages($data);


        $number = $get_number_of_images['row_count'];


        // Create directory if it does not exist
        if (!is_dir(FE_IMAGES . "page_id_" . $page_uid . "_images/")) {
            mkdir(FE_IMAGES . "page_id_" . $page_uid . "_images/");
        }


        $upload_file_new_name = preg_replace('/^.*\.([^.]+)$/D', "image_" . $page_uid . "_" . ((int) $number + 1) . ".$1", basename($_FILES["uploadimage"]['name']));

        $upload_file = FE_IMAGES . "page_id_" . $page_uid . "_images/" . $upload_file_new_name;

        $path_2 = FE_IMAGES . "page_id_" . $page_uid . "_images/";
        $path = IMAGE_PATH . "page_id_" . $page_uid . "_images/";
        $product_image_path = P_IMAGE_PATH . "page_id_" . $page_uid . "_images/";
        $uploadOk = 1;

        $imageFileType = pathinfo($upload_file, PATHINFO_EXTENSION);


        if (isset($_POST['douploadimage'])) {
            
        }
        if (file_exists($upload_file)) {
            $uploadOk = 0;
        }
        if ($_FILES['uploadimage']["size"] > 5000000) {
            
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "PNG" && $imageFileType != "JPG" && $imageFileType != "JPEG" && $imageFileType != "GIF") {
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            
        } else {
            if (file_exists("$path_2/$upload_file")) {
                unlink("$path_2/$upload_file");
            }

            if (move_uploaded_file($_FILES['uploadimage']["tmp_name"], $upload_file)) {

                $image_name = preg_replace('/^.*\.([^.]+)$/D', "image_" . $page_uid . "_" . ((int) $number + 1) . ".$1", basename($_FILES["uploadimage"]['name']));

                $table = array("table1" => "page_images");
                $columns = array("`page_id`", "`image_name`", "`image_path`", "`date_added`");

                $values = array("'" . $page_uid . "'", "'" . $image_name . "'", "'" . $path . "'", "'" . DATE_ADDED . "'");
                $values_to_insert = array(
                    "tables" => $table,
                    "columns" => $columns,
                    "values" => $values
                );

                if ($page_type == 10) {

                    /*
                     * First check if the table has anything in any of the image positions
                     */
                    $check_table_values = "SELECT `image_0`,`image_1`,`image_2`,`image_3`,`image_4`,`image_5`,`image_6`,`image_7`,`image_8` FROM `pages_products`"
                            . " WHERE `page_id` = '" . $page_uid . "'";
                    $check_table_values_res = $this->_mysqli->query($check_table_values);
                    while ($row = $check_table_values_res->fetch_array(MYSQLI_ASSOC)) {
                        if ($row['image_0'] == "" && $row['image_1'] == "" && $row['image_2'] == "" && $row['image_3'] == "" && $row['image_4'] == "" && $row['image_5'] == "" && $row['image_6'] == "" && $row['image_7'] == "" && $row['image_8'] == "") {
                            $number_image = 0;
                        } else {
                            $j = 0;
                            for ($i = 0; $i < 9; $i++) {

                                if ($row['image_' . $i] == "") {

                                    $number_image = $i - $j;
                                    $j++;
                                }
                            }
                        }
                    }


                    //$image_full_path_name = PROJECT_URL . "rock_frontend/frontend_assets/images/page_id_" . $page_uid . "_images/" . $image_name;
                    $image_full_path_name = $product_image_path.$image_name;
                    $insert_image_into_pages_products = "UPDATE `pages_products` SET `image_" . $number_image . "` = '" . $image_full_path_name . "' WHERE `page_id` ='" . $page_uid . "'";
                    $insert_image_into_pages_products_res = $this->_mysqli->query($insert_image_into_pages_products);
                }

                $insert_images_into = $this->InsertImages($values_to_insert);
                return true;
            } else {
                return false;
            }
        }
    }

    public function InsertImages(array $data) {
        $sql = "INSERT INTO `" . $data['tables']['table1'] . "`";
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

    public function CreateImagesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS page_images "
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "page_id VARCHAR (250) NOT NULL,"
                . "image_name VARCHAR(500) NOT NULL,"
                . "image_path VARCHAR (500) NOT NULL,"
                . "date_added VARCHAR (120) NOT NULL"
                . ")";
        $create = $this->_mysqli->query($sql);
    }

    public function GetNumberOfImages(array $data) {
        $sql = "SELECT COUNT(id) AS row_count FROM `" . $data['table'] . "` WHERE `" . $data['field'] . "` = '" . $data['value'] . "'";
        $result = $this->_mysqli->query($sql);
        $row = $result->fetch_array(MYSQLI_ASSOC);

        if ($result) {
            return $row;
        } else {
            return false;
        }
    }

    public function GetAllImages($page_id) {
        $sql = "SELECT * FROM `page_images` WHERE `page_id` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($result && $num_rows > 0) {

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->page_images[] = $row;
            }
        } else {
            $this->image_flag = 1;

            return $this->image_flag;
        }
    }

    public function ReturnImages() {
        return $this->page_images;
    }

    public function DeleteImage($page_id, $image_id) {
        $this->image_id = $image_id;
        /*
         * Before deleteing ask question 
         */
        $this->flag = 1;
        $message = array("1" => ""
            . "<form method='post'>"
            . "<p>Are You Sure?</p>"
            . "<div class='form-group'>"
            . "<input type='submit' value='yes' name='yes_del' class='btn btn-warning btn-xs' style='margin-right: 5px;'/>"
            . "<input type='submit' value='no' name='no_del' class='btn btn-warning btn-xs'/>"
            . "<input type='hidden' value='{$_POST['image_id']}' name='image_id'/>"
            . "<input type='hidden' value='u_image' name='option'/>"
            . "<input type='hidden' value='{$page_id}' name='page_id'/>"
            . "<input type='hidden' value='delete_image' name='delete_image'/>"
            . "<input type='hidden' value='{$_POST['image_name']}' name='image_name'/>"
            . "<input type='hidden' value='{$_POST['image_path']}' name='image_path'/>"
            . "</div>"
            . "</form>");
        array_push($this->messages, $message);
        $this->alert_class = "warning";

        if (isset($_REQUEST['yes_del'])) {
            unset($this->messages);
            $this->messages = array();
            $image_path = FE_IMAGES . "page_id_" . $page_id . "_images";

            unlink($image_path . "/" . $_POST['image_name']);
            if ($this->is_dir_empty($image_path)) {
                rmdir($image_path);
            }

            $sql = "DELETE FROM `page_images` WHERE `id` = '" . $_POST['image_id'] . "' AND `page_id` = '" . $page_id . "'";
            $del_result = $this->_mysqli->query($sql);

            $this->flag = 1;
            $message = array("1" => "Image deleted");

            array_push($this->messages, $message);
            $this->alert_class = "success";
        } else if (isset($_REQUEST['no_del'])) {
            $this->flag = 0;
        }
    }

    public function is_dir_empty($dir) {
        if (!is_readable($dir))
            return NULL;
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return FALSE;
            }
        }
        return TRUE;
    }

}
