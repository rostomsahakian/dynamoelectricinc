<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
if (isset($_SESSION['logged_in'])) {
    require_once 'rock_backend/admin/index.php';
} else {
    require_once 'rock_frontend/index.php';
}