<?php
session_start();
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'path_config.php';
require_once CONFIG_PATH; // Change this from path_config.php file to correct path otherwise the backend will read from wrong db
require_once ABSOLUTH_ROOT.'vendor/autoload.php';
$log_out = new Logout();
if ($_REQUEST['cmd'] == "log_out") {
    
    $log_out->DoLogOut($_SESSION['login_code']);
}
require 'backend_controls/Backend_Loader.php';
