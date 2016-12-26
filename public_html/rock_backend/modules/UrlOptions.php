<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UrlOptions
 *
 * @author rostom
 */
class UrlOptions {

    private $_mysqli;
    private $_db;
    public $fetch_res;
    public $url_alias;
    public $_today;
    public $flag = 0;
    public $alert_class = "";
    public $messages = array();

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function UrlOptionForm($page_id) {
        $this->_today = $today = date("m/d/y");
        if (isset($_REQUEST['update_url_option'])) {

            $this->UpdateUrlOption($page_id, $_REQUEST['url_option']);
        }
        if (isset($_REQUEST['assign_alias'])) {
            $this->UpdatepageAlias($page_id, trim($_REQUEST['url_alias']), $this->_today);
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5><strong><i class="fa fa-exchange" aria-hidden="true"></i>&nbsp;Url Options</strong></h5>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <form method="post">
                            <div class="col-md-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading"><h5><strong><i class="fa fa-external-link-square"></i>&nbsp;Url Type:</strong></h5></div>
                                    <div class="panel-body">
                                        <?php
                                        $long = "";
                                        $short = "";
                                        $this->fetch_res = NULL;
                                        if ($this->GetUrlOptions($page_id)) {

                                            foreach ($this->fetch_res as $option) {
                                                if ($option['url_option'] == "short") {
                                                    $short = 'checked="checked"';
                                                } else {
                                                    $long = 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="form-group">
                                            <input type="radio" name="url_option" value="short" <?= $short ?>/>                                   
                                            <label>Short Url</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="radio" name="url_option" value="long" <?= $long ?>/>
                                            <label>Long Url</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" value="Update" name="update_url_option" class="btn btn-danger btn-xs"/>
                                            <input type="hidden" value="<?= $page_id ?>" name="page_id"/>
                                            <input type="hidden" value="url-option" name="option"/>
                                            <input type="hidden" name="cmd" value="edit_page"/>
                                            <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form method="post">
                            <div class="col-md-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h5><strong><i class="fa fa-chain"></i>&nbsp;Alias</strong></h5>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Write an alias for the url</label>
                                            <?php
                                            $page_al = "";

                                            $this->GetPageAlias($page_id);
                                            if ($this->url_alias != NULL) {
                                                foreach ($this->url_alias as $page_alias) {
                                                    $page_al = $page_alias['page_alias'];
                                                }
                                            }
                                            ?>  
                                            <input type="text" name="url_alias" id="url-alias" value="<?=  $page_al; ?>" class="form-control" />


                                        </div>
                                        <div class="form-group">
                                            <input type="submit" value="Assign" name="assign_alias" class="btn btn-success btn-xs"/>
                                            <input type="hidden" value="<?= $page_id ?>" name="page_id"/>
                                            <input type="hidden" value="url-option" name="option"/>
                                            <input type="hidden" name="cmd" value="edit_page"/>
                                            <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        </form>
        <?php
    }

    public function GetUrlOptions($page_id) {
        $sql = "SELECT * FROM `page_url_option` WHERE `page_id` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        $get_num_rows = $result->num_rows;
        if ($get_num_rows > 0) {
            if ($result) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $this->fetch_res[] = $row;
                }
                return $this->fetch_res;
            }
        }
    }

    public function UpdateUrlOption($page_id, $value) {
        $sql = "UPDATE `page_url_option` SET `url_option` = '" . $value . "' WHERE `page_id` = '" . $page_id . "' ";
        $update = $this->_mysqli->query($sql);
        if ($update) {
            return true;
        } else {
            return FALSE;
        }
    }

    public function Messages($message, $class) {
        
    }

    public function GetPageAlias($page_id) {
        $sql = "SELECT * FROM `page_alias` WHERE `page_id` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->url_alias[] = $row;
            }
            return $this->url_alias;
        } else {

            return FALSE;
        }
    }

    public function UpdatepageAlias($page_id, $value, $date) {
        /*
         * first check and make sure the page alias is unique then convert the text to be kosher
         */
        $no_spaces = str_replace(" ", "-", $value);
        $no_upper_case = strtolower($no_spaces);
        $no_ands = str_replace("&", "and", $no_upper_case);
        $no_special_chars = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);

        $check_if_exists = "SELECT `page_alias` FROM `page_alias` WHERE `page_id` = '" . $page_id . "'";
        $check_if_exists_res = $this->_mysqli->query($check_if_exists);
        $if_is_there = $check_if_exists_res->num_rows;


        $check_value = "SELECT * FROM `page_alias` WHERE `page_alias` = '" . $no_special_chars . "'";
        $ret_res = $this->_mysqli->query($check_value);
        $num_rows = $ret_res->num_rows;
        /*
         * If there are no match then proceed with the update
         */
        if ($num_rows == 0 && $if_is_there > 0) {


            $sql = "UPDATE `page_alias` SET `page_alias` = '" . $no_special_chars . "', `date_modified` = '" . $date . "' WHERE `page_id` = '" . $page_id . "' ";
            $update = $this->_mysqli->query($sql);
            if ($update) {
                $this->flag = 1;
                $message = array("1" => "Page alias was updated.");
                $this->alert_class = "success";
                array_push($this->messages, $message);
            } else {
                $this->flag = 1;
                $message = array("1" => "Unable to update page alias. Page alias is taken");
                $this->alert_class = "warning";
                array_push($this->messages, $message);
            }
        } else if ($num_rows == 0 && $if_is_there == 0) {
            $insert_alias = "INSERT INTO `page_alias` (page_id, page_alias, date_added, date_modified) VALUES ('" . $page_id . "','" . $no_special_chars . "', '" . $date . "', '" . $date . "')";
            $insert_alias_res = $this->_mysqli->query($insert_alias);

            if ($insert_alias_res) {
                $this->flag = 1;
                $message = array("1" => "Page alias was added.");
                $this->alert_class = "success";
                array_push($this->messages, $message);
            } else {
                $this->flag = 1;
                $message = array("1" => "Unable to add alias.");
                $this->alert_class = "warning";
                array_push($this->messages, $message);
            }
        } else {
            $this->flag = 1;
            $message = array("1" => "Unable to update page alias. Page alias is taken");
            $this->alert_class = "warning";
            array_push($this->messages, $message);
        }
    }

}
