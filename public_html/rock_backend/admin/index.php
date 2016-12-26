<?php
session_start();

error_reporting(E_ALL);

ini_set('display_errors', '1');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../includes/config.php';

require_once ABSOLUTH_ROOT.'vendor/autoload.php';



if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == "" || $_SESSION['USER_STATUS'] == 0) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <title>Rock Backend Login</title>
            <link href="../backend_assets/css/bootstrap.min.css" rel="stylesheet">
            <link href="../backend_assets/css/back-end-admin.css" rel="stylesheet">

        </head>
        <body>
            <div class="container prock-container-admin">
                <div class="col-lg-12">

                    <div class="col-lg-3"></div>
                    <div class="col-lg-5">

                        <div class="panel panel-default">

                            <div class="panel-heading prock-panel-heading">
                                <span>Rock CMS Login</span>
                            </div>
                            <div class="panel-body">
                                <?php
                                $login_f = new Login();


                                $login_f->LoginForm();
                                ?>
                            </div>
                            <?php
                            if(isset($_REQUEST['do_reset'])){
                                $login_f->DoResetpassword($_REQUEST);
                            }
                            if(isset($_REQUEST['check_email'])){
                                $login_f->DoSendToken($_REQUEST);
                            }
                            if (isset($_REQUEST['forgot-p']) && $_REQUEST['forgot-p'] == "true") {
                                $login_f->ForgotPasswordForm();
                            }
                            ?>  
                        </div>

                    </div> 

                </div>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"/>

        </body>

    </html>
    <?php
} else {

    header("Location: ../?cmd=edit-page&lc=" . $_SESSION['login_code']);
}
