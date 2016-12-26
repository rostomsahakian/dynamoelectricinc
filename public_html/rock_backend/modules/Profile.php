<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Profile
 *
 * @author rostom
 */
class Profile {

    private $_mysqli;
    private $_db;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function ProfileManagement() {
        ?>
        <div class="panel-heading">
            <h5><strong><i class="glyphicon glyphicon-user" aria-hidden="true"></i>&nbsp;Edit Profile</strong></h5>
        </div>
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
            </div>
        </div>
        <?php
    }

}
