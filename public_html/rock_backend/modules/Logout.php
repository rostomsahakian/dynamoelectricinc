<?php

/**
 * Description of Logout
 *
 * @author rostom
 */
class Logout {

    private $_mysqli;
    private $_db;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function DoLogOut($session_code) {
        if ($session_code != "") {
            $new_login_code = uniqid();
            $sql = "UPDATE `rock_users` SET `login_code` = '" . $new_login_code . "', `login_status` = '0' WHERE `login_code` ='" . $session_code . "'";
            $result = $this->_mysqli->query($sql);
            if ($result) {
                unset($_SESSION['logged_in']);
                unset($_SESSION['login_code']);
                unset($_SESSION['USER_ID']);

                //session_destroy();
                header("Location: rock_backend/admin/index.php");
            }
        }
    }

}
