<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModuleControler
 *
 * @author rostom
 */
class ModuleControler {

    private $_db;
    private $_mysqli;
    public $_queries;
    public $mods;
    public $res;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->CommandListener();
    }

    public function CheckModStatus() {
        
    }

    public function ShowAvailableModules($check_user_rights = FALSE) {
        if ($check_user_rights) {
            ?>

            <div class="panel-heading">
                <h5><strong><i class="fa fa-cogs" aria-hidden="true"></i>&nbsp;Module Manager</strong></h5>
            </div>
            <!--Check in data base available modules-->
            <div class="panel-body">
                <div class="col-md-12">
                    <?php
                    $data = array(
                        "table" => "modules",
                        "options" => 0
                    );

                    $sql = "SELECT * FROM `" . $data['table'] . "` ORDER BY `id` ASC";
                    $result = $this->_mysqli->query($sql);
                    $num_rows = $result->num_rows;
                    if ($num_rows > 0) {
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $this->res[] = $row;
                        }
                    }
                    if ($this->res != NULL) {
                        foreach ($this->res as $modules) {
                            ?>

                            <!--Divide into two sections-->
                            <div class="col-md-8">
                                <!--get the list of available modules and display names here-->
                                <?= $modules['name']; ?>
                            </div>
                            <div class="col-md-4">
                                <!-- get each status and display here-->
                                <?php
                                if ($modules['status'] == 1 || $modules['status'] == "1") {
                                    $link = "<a href='?cmd=mod_manager&val=mod_turn_off&mod_id={$modules['id']}'>Disable</a>";
                                } else {
                                    $link = "<a href='?cmd=mod_manager&val=mod_turn_on&mod_id={$modules['id']}'>Enable</a>";
                                }
                                echo $link;
                                ?>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>


            <?php
        } else {
            echo "You do not have the administrative privilages to view this page";
        }
    }

    public function CommandListener() {
        if (isset($_REQUEST['cmd']) && isset($_REQUEST['val'])) {
            $command = $_REQUEST['cmd'];
            $value = $_REQUEST['val'];
            if ($value == "mod_turn_off") {
                $value = 0;
            } else {
                $value = 1;
            }
            $mod_id = $_REQUEST['mod_id'];
            /*
             * Update The module's status
             */
            $data = array(
                "options" => 0,
                "table" => "modules",
                "field" => array(
                    "status"
                ),
                "value1" => array(
                    $value
                ),
                "field2" => "id",
                "value2" => $mod_id
            );

            $sql = "UPDATE `" . $data['table'] . "` SET ";
            for ($i = 0; $i < count($data['field']); $i++) {
                $sql .= "`" . $data['field'][$i] . "` =" . $data['value1'][$i];
            }
            $sql .= "  WHERE `" . $data['field2'] . "` =  '" . $data['value2'] . "'";
            $result = $this->_mysqli->query($sql);
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function AvailableModules() {
        $data = array(
            "table" => "modules",
            "field" => "status",
            "value" => "1",
            "options" => 1
        );

        $sql = "SELECT * FROM `modules` WHERE `status` = '1'";
        $result = $this->_mysqli->query($sql);
        if ($result) {

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $get_mods[] = $row;
            }
        }

        foreach ($get_mods as $mods) {

            $modules[$mods['id']] = array(
                "name" => $mods['name'],
                "link" => $mods['link'],
                "icon" => $mods['icon']
            );

            $this->mods = $modules;
        }
        return $this->mods;
    }

}
