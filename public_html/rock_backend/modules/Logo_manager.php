<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logo_manager
 *
 * @author rostom
 */
class Logo_manager {

    private $_mysqli;
    private $_db;
    public $flag = 0;
    public $messages = array();
    public $alert_class;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function LogoManager() {
        $logo_name = str_replace(" ", "", strtolower(CUSTOMER));
        $date_added = date('m/d/y');
        if (isset($_REQUEST['douploadimage'])) {

            if ($this->Do_Upload_images($_REQUEST, $logo_name, $date_added)) {
                $this->flag = 1;
                $message = array("1" => "logo uploaded.");
                array_push($this->messages, $message);
                $this->alert_class = "success";
            }
        }
        if (isset($_REQUEST['del_logo'])) {
            $this->DeleteLogo($_REQUEST['logo_id'], $_REQUEST['logo_name']);
        }
        ?>
        <div class="panel-heading">
            <h5><strong><i class="fa fa-photo" aria-hidden="true"></i>&nbsp;Logo Management</strong></h5>
        </div>
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
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
                <form method="post" enctype="multipart/form-data">
                    <div class="col-md-4">
                        <label>Upload your company's logo:</label>
                        <input type="file" name="uploadimage"  class="btn btn-default btn-xs"/>

                        <input type="submit" class="btn btn-danger btn-xs" name="douploadimage" value="Upload" style="margin-top: 10px;"/>
                        <input type="hidden" name="option" value="u_image" />
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <hr/>
            </div>
            <div class="col-md-12">
                <div class="col-md-4">
                    <?php
                    $select = "SELECT * FROM `store_logo`";
                    $res = $this->_mysqli->query($select);
                    $num_rows = $res->num_rows;
                    if ($num_rows > 0) {
                        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                            ?>
                    <img src="<?= $row['logo_path'] . $row['logo_name'] ?>" class="thumbnail" style="width:25%;"/>
                            <form method="post">
                                <input type="hidden" name="logo_name" value="<?= $row['logo_name'] ?>"/>
                                <input type="hidden" name="logo_id" value="<?= $row['id'] ?>"/>
                                <input type="submit" name="del_logo" value="Delete" class="btn btn-danger btn-xs"/>
                            </form>

                            <?php
                        }
                    } else {
                        ?>
                        <p>No logo has been uploaded yet</p>
                        <?php
                    }
                    ?>
                </div>
            </div>

        </div>
        <?php
    }

    /*
     * Upload Images
     * It will got to a frontend fold and will have its own directory for each page type
     */

    public function Do_Upload_images($image, $path, $date_added) {




        $sql = "SELECT `id` FROM `store_logo`";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {

            $this->flag = 1;
            $message = array("1" => "There can only be one logo.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else {


            $number = $num_rows;


            // Create directory if it does not exist
            $logo_name = str_replace(" ", "", strtolower(CUSTOMER));
            if (!is_dir(FE_IMAGES . $logo_name . "_logo/")) {
                mkdir(FE_IMAGES . $logo_name . "_logo/");
            }


            $upload_file_new_name = preg_replace('/^.*\.([^.]+)$/D', "logo_" . ((int) $number + 1) . ".$1", basename($_FILES["uploadimage"]['name']));

            $upload_file = FE_IMAGES . $logo_name . "_logo/" . $upload_file_new_name;

            $path_2 = FE_IMAGES . $logo_name . "_logo/";
            $path = P_IMAGE_PATH . $logo_name . "_logo/";

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

                    $insert_image = "INSERT INTO `store_logo` (logo_name, logo_path, date_added) VALUES ('" . $upload_file_new_name . "', '" . $path . "', '" . $date_added . "')";
                    $insert_result = $this->_mysqli->query($insert_image);
                    return true;
                } else {
                    return false;
                }
            }
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

    public function DeleteLogo($logo_id, $image_name) {
        
        $logo_name = str_replace(" ", "", strtolower(CUSTOMER));
        $path_2 = FE_IMAGES . $logo_name . "_logo/";
        
        unlink($path_2 . "/" . $image_name);
        if ($this->is_dir_empty($path_2)) {
            rmdir($path_2);
        }

        $sql = "DELETE FROM `store_logo` WHERE `id` = '" . $logo_id . "'";

        $del_result = $this->_mysqli->query($sql);
    }

}
