<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DB_Connect
 *
 * @author rostom
 */
class DB_Connect {

    private $_connection;
    private static $_instance;
    private $_user = DB_USERNAME;
    private $_password = DB_PASSWORD;
    private $_db = DB_NAME;
    private $_host = DB_HOST;

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new DB_Connect();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->_connection = new mysqli($this->_host, $this->_user, $this->_password, $this->_db);
        if (mysqli_connect_error()) {
            trigger_error("Failed to connect to MySQL: " . mysqli_connect_error(), E_USER_ERROR);
        }
    }

    private function __clone() {
        
    }

    public function getConnection() {
        return $this->_connection;
    }

}
