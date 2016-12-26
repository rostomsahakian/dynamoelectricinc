<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccountDetail
 *
 * @author rostom
 */
class AccountDetail {

    private $_mysqli;
    private $_db;
    public $all_users;
    private $user_logged_in;
    private $user;
    private $user_id;
    public $messages = array();
    public $flag = 0;
    public $alert_class;
    public $e = 0;
    public $e_class;
    public $e_messages = array();

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->user_logged_in = $_SESSION['login_code'];
        $this->user_id = $_SESSION['USER_ID'];
    }

    public function VeiwAccountDetail() {
        if ($this->GetSpecificUserInfo($this->user_id)) {
            if (isset($_REQUEST['block_user'])) {
                $this->BlockUser($_REQUEST);
            }
            if (isset($_REQUEST['delete_user'])) {
                $this->DeleteUser($_REQUEST);
            }
            if (isset($_REQUEST['update_account'])) {
                $this->UpdateUserInfo($_REQUEST);
            }


            foreach ($this->user as $user) {
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
                <div class="panel-heading">
                    <h5><strong><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;Account Detail</strong></h5>
                </div>
                <?php
                if ($user['user_mode'] == "Admin") {
                    ?>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <form method="post">
                                    <input type="submit" name="add_new" value="Add new User" class="btn btn-success btn-xs"/>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="panel-body">
                    <form method="post">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Name<span style="color:#C9302C;">*</span>:</label>
                                    <input type="text" name="name" value="<?= isset($_REQUEST['name']) ? $_REQUEST['name'] : $user['name'] ?>" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Last Name:</label>
                                    <input type="text" name="last_name" value="<?= isset($_REQUEST['last_name']) ? $_REQUEST['last_name'] : $user['last_name'] ?>" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Email<span style="color:#C9302C;">*</span>:</label>
                                    <input type="email" name="email" value="<?= isset($_REQUEST['email']) ? $_REQUEST['email'] : $user['email'] ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password<span style="color:#C9302C;">*</span>:</label>
                                    <input type="password" name="password_1" value="<?= isset($_REQUEST['password_1']) ? $_REQUEST['password_1'] : $user['password'] ?>" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Re Password:</label>
                                    <input type="password" name="password_2" value="<?= isset($_REQUEST['password_2']) ? $_REQUEST['password_2'] : '' ?>" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Company:</label>
                                    <input type="text" name="company" value="<?= isset($_REQUEST['company']) ? $_REQUEST['company'] : $user['company'] ?>" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone:</label>
                                    <input type="text" name="phone" value="<?= isset($_REQUEST['phone']) ? $_REQUEST['phone'] : $user['phone'] ?>" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>User Mode:</label>
                                    <?php
                                    if ($user['user_mode'] == "Admin") {
                                        ?>
                                        <select name="user_mode" class="form-control">
                                            <option value="Admin">Admin</option>
                                            <option value="user">User</option>
                                        </select>
                                        <?php
                                    } else {
                                        ?>
                                        <input type="text" value="<?= $user['user_mode'] ?>" class="form-control disabled" disabled="disabled"/>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Status:</label>
                                    <input type="text" name="status" value="<?= $user['status'] ?>" class="form-control disabled" disabled="disabled"/>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" name="user_mode" value="<?= $user['user_mode'] ?>"/>
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>"/>
                                    <input type="submit" name="update_account" value="Update Account" class="btn btn-danger" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-12"></div>
                <?php
                if ($user['user_mode'] == "Admin" && $this->GetAllRockUsers()) {
                    ?>

                    <div class="panel-body">
                        <h3>All Users In db &nbsp; <a href="?cmd=account-details"><i class="fa fa-refresh"></i></a></h3>
                        <table class="table table-bordered table-hover">
                            <tr >
                                <th>id</th>
                                <th>Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>User Mode</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Delete</th>
                                <th>Block</th>
                                <th>Active</th>
                            </tr>
                            <?php
                            foreach ($this->all_users as $all_users) {
                                ?>
                                <tr>
                                    <td><?= $all_users['id'] ?></td>
                                    <td><?= $all_users['name'] ?></td>
                                    <td><?= $all_users['last_name'] ?></td>
                                    <td><?= $all_users['email'] ?></td>
                                    <td><?= $all_users['user_mode'] ?></td>
                                    <td><?= $all_users['phone'] ?></td>
                                    <td><?= $all_users['status'] ?></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="user_id" value="<?= $all_users['id'] ?>" />
                                            <input type="submit" name="delete_user" value="delete" class="btn btn-danger btn-xs"/>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="post">
                                            <?php
                                            if ($all_users['status'] == "1") {
                                                $class = "btn-warning";
                                                $value = "Block";
                                                $status = 0;
                                            } else {
                                                $class = "btn-success";
                                                $value = "Un-Block";
                                                $status = 1;
                                            }
                                            ?>
                                            <input type="hidden" name="status" value="<?= $status ?>"/>
                                            <input type="hidden" name="user_id" value="<?= $all_users['id'] ?>" />
                                            <input type="submit" name="block_user" value="<?= $value ?>" class="btn <?= $class ?> btn-xs"/>
                                        </form>  
                                    </td>
                                    <?php
                                    if ($all_users['login_status'] == '1') {
                                        $stat = "style='color:#1DA076;'";
                                    } else {
                                        $stat = "style='color:#CD141A;'";
                                    }
                                    ?>
                                    <td <?= $stat ?>>
                                        <i class="fa fa-circle " aria-hidden="true"></i>

                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                    } else {
                        ?>

                        <?php
                    }
                }
            }
            ?>
        </div>
        </div>
        <?php
        if (isset($_REQUEST['add_new'])) {
            ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5><strong>Add new user</strong></h5>
                </div>

                <?php
                $this->AddNewUser();
                ?>

            </div>

            <?php
        }
    }

    public function GetAllRockUsers() {
        $sql = "SELECT * FROM `rock_users` WHERE `id` != '" . $this->user_id . "'";
        $results = $this->_mysqli->query($sql);
        $num_row = $results->num_rows;
        if ($num_row > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                $this->all_users[] = $row;
            }
            return $this->all_users;
        }
    }

    public function GetSpecificUserInfo($data) {
        $sql = "SELECT * FROM `rock_users` WHERE `id` = '" . $data . "'";
        $results = $this->_mysqli->query($sql);
        $num_row = $results->num_rows;
        if ($num_row > 0) {
            while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                $this->user[] = $row;
            }
            return $this->user;
        }
    }

    public function DeleteUser($data) {

        $sql = "DELETE FROM `rock_users` WHERE `id` = '" . $data['user_id'] . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            return true;
        }
    }

    public function BlockUser($data) {

        $sql = "UPDATE `rock_users` SET `status` = '" . $data['status'] . "' WHERE `id` = '" . $data['user_id'] . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            return true;
        }
    }

    public function UpdateUserInfo($data) {

        if (empty($data['name']) && empty($data['email']) && empty($data['password_1'])) {
            $this->flag = 1;
            $message = array("1" => "All required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else if (empty($data['name']) || empty($data['email']) || empty($data['password_1'])) {
            $this->flag = 1;
            $message = array("1" => "One or more required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else {
            /*
             * Check if anything has changed
             */
            $things_to_update = array();
            $sql = "SELECT * FROM `rock_users` WHERE `id` = '" . $data['user_id'] . "'";
            $result = $this->_mysqli->query($sql);
            $data_to_update = array();
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                if (trim($data['name']) != trim($row['name'])) {
                    $data_to_update['name'] = $data['name'];
                }
                if (trim($data['last_name']) != trim($row['last_name'])) {
                    $data_to_update['last_name'] = $data['last_name'];
                }
                if (trim($data['email']) != trim($row['email'])) {
                    $data_to_update['email'] = $data['email'];
                }
                if (!empty($data['password_2'])) {
                    if (md5($data['password_2']) != md5($data['password_1'])) {
                        $this->flag = 1;
                        $message = array("1" => "passwords did not match.");
                        array_push($this->messages, $message);
                        $this->alert_class = "warning";
                    }
                }
                if (trim($data['password_1']) != trim($row['password'])) {
                    $data_to_update['password'] = md5($data['password_1']);
                }
                if (trim($data['company']) != trim($row['company'])) {
                    $data_to_update['company'] = $data['company'];
                }
                if (trim($data['phone']) != trim($row['phone'])) {
                    $data_to_update['phone'] = $data['phone'];
                }
                if (trim($data['user_mode']) != trim($row['user_mode'])) {
                    $data_to_update['user_mode'] = $data['user_mode'];
                }
            }
            array_push($things_to_update, $data_to_update);
            /*
             * Now update
             */
            foreach ($things_to_update as $field => $value) {
                if (!empty($value)) {
                    foreach ($value as $f => $v) {

                        $update_data = "UPDATE `rock_users` SET `" . $f . "` = '" . $v . "' WHERE `id` = '" . $data['user_id'] . "'";
                        $update_data_res = $this->_mysqli->query($update_data);
                        $this->flag = 1;
                        $message = array("1" => "<strong>" . $f . "</strong> was updated");
                        $this->alert_class = "success";
                        array_push($this->messages, $message);
                        unset($_REQUEST);
                    }
                } else {
                    $this->flag = 1;
                    $message = array("1" => "Nothing to update!");
                    $this->alert_class = "warning";
                    array_push($this->messages, $message);
                }
            }
        }
    }

    public function AddNewUser() {
        if (isset($_REQUEST['do_add_new_user'])) {
            $this->DoAddnewuser($_REQUEST);
        }
        ?>
        <?php
        if ($this->e == 1) {
            ?>

            <div class="alert alert-<?= $this->e_class ?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <ul>
                    <?php
                    foreach ($this->e_messages as $e) {
                        ?>
                        <li><?= $e['1'] ?></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <?php
        }
        ?>
        <div class="panel-body">
            <form method="post">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Name<span style="color:#C9302C;">*</span>:</label>
                            <input type="text" name="new_name" value="<?= isset($_REQUEST['new_name']) ? $_REQUEST['new_name'] : '' ?>" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Last Name:</label>
                            <input type="text" name="new_last_name" value="<?= isset($_REQUEST['new_last_name']) ? $_REQUEST['new_last_name'] : '' ?>" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Email<span style="color:#C9302C;">*</span>:</label>
                            <input type="email" name="new_email" value="<?= isset($_REQUEST['new_email']) ? $_REQUEST['new_email'] : '' ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Password<span style="color:#C9302C;">**</span>:</label>
                            <input type="password" name="new_password_1" value="<?= isset($_REQUEST['new_password_1']) ? $_REQUEST['new_password_1'] : '' ?>" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Re Password<span style="color:#C9302C;">**</span>:</label>
                            <input type="password" name="new_password_2" value="<?= isset($_REQUEST['new_password_2']) ? $_REQUEST['new_password_2'] : '' ?>" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Company:</label>
                            <input type="text" name="new_company" value="<?= isset($_REQUEST['new_company']) ? $_REQUEST['new_company'] : '' ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phone:</label>
                            <input type="text" name="new_phone" value="<?= isset($_REQUEST['new_phone']) ? $_REQUEST['new_phone'] : '' ?>" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>User Mode<span style="color:#C9302C;">*</span>:</label>

                            <select name="new_user_mode" class="form-control">
                                <option value="--">--select mode--</option>
                                <option value="Admin">Admin</option>
                                <option value="user">User</option>
                            </select>

                        </div>
                        <div class="form-group">
                            <label>Status<span style="color:#C9302C;">*</span>:</label>
                            <table class="table">
                                <tr>
                                    <td>Active</td>
                                    <td><input type="radio" name="new_status" value="1" class="form-control" checked="checked"/></td>
                                </tr>
                                <tr>
                                    <td>Disabled</td>
                                    <td><input type="radio" name="new_status" value="0" class="form-control"/></td>
                                </tr>
                            </table>

                        </div>

                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" name="add_new" value=""/>
                            <input type="submit" name="do_add_new_user" value="Create Account" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function DoAddnewuser($data) {

        if (empty($data['new_name']) && empty($data['new_email']) && empty($data['new_password_1']) && empty($data['new_password_2']) && $data['new_user_mode'] == "--") {
            $this->e = 1;
            $message = array("1" => "All required fields are empty.");
            array_push($this->e_messages, $message);
            $this->e_class = "warning";
        } else if (empty($data['new_name']) || empty($data['new_email']) || empty($data['new_password_1']) || empty($data['new_password_2']) || $data['new_user_mode'] == "--") {
            $this->e = 1;
            $message = array("1" => "One or more required fields are empty.");
            array_push($this->e_messages, $message);
            $this->e_class = "warning";
        } else if ($data['new_password_2'] != $data['new_password_1']) {
            $this->e = 1;
            $message = array("1" => "Passwords did not match.");
            array_push($this->e_messages, $message);
            $this->e_class = "warning";
        } else {
            /*
             * check the email make sure it is unique
             */
            $email = trim($data['new_email']);

            $sql = "SELECT `email` FROM `rock_users` WHERE `email` = '" . $email . "'";
            $result = $this->_mysqli->query($sql);
            $num_rows = $result->num_rows;
            if ($num_rows > 0) {
                $this->e = 1;
                $message = array("1" => "Email provided is already in the system.");
                array_push($this->e_messages, $message);
                $this->e_class = "warning";
            } else {

                $login_code = uniqid();
                $today = date('m/d/y');
                $insert_into_users = "INSERT INTO `rock_users` (name, last_name, email, password, company, phone, user_mode, status, date_added, login_code)"
                        . " VALUES "
                        . " ( "
                        . "'" . trim($data['new_name']) . "', "
                        . "'" . trim($data['new_last_name']) . "', "
                        . "'" . trim($data['new_email']) . "', "
                        . "'" . trim(md5($data['new_password_1'])) . "', "
                        . "'" . trim($data['new_company']) . "', "
                        . "'" . trim($data['new_phone']) . "', "
                        . "'" . trim($data['new_user_mode']) . "', "
                        . "'" . trim($data['new_status']) . "', "
                        . "'" . trim($today) . "', "
                        . "'" . trim($login_code) . "'"
                        . " ) ";

                $insert_into_users_res = $this->_mysqli->query($insert_into_users);
                if ($insert_into_users_res) {
                    $this->e = 1;
                    $message = array("1" => "User was added to the system");
                    array_push($this->e_messages, $message);
                    $this->e_class = "success";
                }
            }
        }
    }

}
