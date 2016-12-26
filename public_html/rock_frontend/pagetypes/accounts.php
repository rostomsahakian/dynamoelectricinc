<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of accounts
 *
 * @author rostom
 */
class accounts {

    private $_mysqli;
    private $_db;
    public $flag = 0;
    public $messages = array();
    public $alert_class;
    public $user_data;
    public $shipping_info;
    public $country;
    public $trans_history;
    public $item_detail;
    public $order_detail;

    public function __construct() {

        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function FrontEndSignUpForm($data) {
        if (isset($_REQUEST['signup'])) {

            $this->DoRegisterFrontEndUser($_REQUEST);
        }
        ?>
        <div class="container rock-main-container">
            <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            <div class="row">
                <div class="col-md-12 rock-front-end-accounts">
                    <form method="post">
                        <div class="panel panel-default">
                            <div class="panel-heading">

                                <h5><b><i class="fa fa-user"></i>&nbsp;Sign up for a free account</b></h5>
                                <p style="text-align: right;"><span style="color:#DA2430;">*</span>&nbsp;Indicates required field.</p>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-12 <?= $this->alert_class ?>">
                                    <?php
                                    if ($this->flag == 1) {
                                        ?>

                                        <div class="alert alert-warning  alert-dismissible" role="alert">
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
                                </div>
                                <!--personal Information -->
                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h6><b><i class="fa fa-group"></i>&nbsp;Personal Information</b></h6>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>First name<span style="color:#DA2430;">*</span>:</label>
                                                <input type="text" name="f_name" value="<?= isset($_REQUEST['f_name']) ? $_REQUEST['f_name'] : '' ?>" class="form-control input-sm" id="account_fname"/>
                                            </div>
                                            <div class="form-group">
                                                <label>Last name<span style="color:#DA2430;">*</span>:</label>
                                                <input type="text" name="l_name" value="<?= isset($_REQUEST['l_name']) ? $_REQUEST['l_name'] : '' ?>" class="form-control input-sm" id="account_lname"/>
                                            </div>                                 
                                            <div class="form-group">
                                                <label>Middle name:</label>
                                                <input type="text" name="m_name" value="<?= isset($_REQUEST['m_name']) ? $_REQUEST['m_name'] : '' ?>" class="form-control input-sm" id="account_mname"/>
                                            </div>
                                            <div class="form-group">
                                                <label>Title:</label>
                                                <select name="user_title" class="form-control">
                                                    <option value="--">select</option>
                                                    <option value="mr">Mr.</option>
                                                    <option value="mrs">Mrs.</option>
                                                    <option value="ms">Miss</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <!---login information-->
                                <div class="col-md-6">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h6><b><i class="fa fa-key"></i>&nbsp;Login Information</b></h6>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>Email<span style="color:#DA2430;">*</span> (it will be used as your username)</label>
                                                <input type="email" name="user_email" value="<?= isset($_REQUEST['user_email']) ? $_REQUEST['user_email'] : '' ?>" id="user-email" class="form-control input-sm"/>
                                            </div>
                                            <div class="form-group">
                                                <label>Password<span style="color:#DA2430;">*</span>:</label>
                                                <input type="password" name="user_pass_1" value="<?= isset($_REQUEST['user_pass_1']) ? $_REQUEST['user_pass_1'] : '' ?>" id="user_pass_1" class="form-control input-sm"/>
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm Password<span style="color:#DA2430;">*</span>:</label>
                                                <input type="password" name="user_pass_2" value="<?= isset($_REQUEST['user_pass_2']) ? $_REQUEST['user_pass_2'] : '' ?>" id="user_pass_2" class="form-control input-sm"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="cmd" value="create"/>
                                        <input type="hidden" name="check_data" value="check"/>
                                        <input type="submit" name="signup" value="Register" class="btn btn-success"/>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php
    }

    public function DoRegisterFrontEndUser($data) {
        $register_date = date("d-M-Y H:i:s");

        $check_user_email = "SELECT `email` FROM `frontend_users_email` WHERE `email` = '" . mysqli_real_escape_string($this->_mysqli, $data['user_email']) . "'";
        $check_user_email_res = $this->_mysqli->query($check_user_email);
        $num_rows = $check_user_email_res->num_rows;

        if (empty($data['f_name']) && empty($data['l_name']) && empty($data['user_email']) && empty($data['user_pass_1']) && empty($data['user_pass_2'])) {
            $this->flag = 1;
            $message = array("1" => "All required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        }
        if (empty($data['f_name']) || empty($data['l_name']) || empty($data['user_email']) || empty($data['user_pass_1']) || empty($data['user_pass_2'])) {
            $this->flag = 1;
            $message = array("1" => "One or more of the required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        }
        if (!filter_var($data['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->flag = 1;
            $message = array("1" => "Please enter a valid email address.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        }
        if ($num_rows > 0) {
            $this->flag = 1;
            $message = array("1" => "Email is already registered.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        }
        if ($data['user_pass_2'] != $data['user_pass_1']) {
            $this->flag = 1;
            $message = array("1" => "Passwords did not match.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        }
        if ($this->flag == 0) {
            echo $num_rows;
            $first_name = trim($data['f_name']);
            $last_name = trim($data['l_name']);
            $miidle_name = trim($data['m_name']);
            $title = trim($data['user_title']);
            $email = trim($data['user_email']);
            $password = md5($data['user_pass_1']);

            $customer_id = uniqid();

            $insert_new_user = "INSERT INTO `frontend_users_desc` (cust_id, f_name, l_name, middle_name, title) "
                    . "VALUES"
                    . " ("
                    . "'" . $customer_id . "', "
                    . "'" . $first_name . "', "
                    . "'" . $last_name . "', "
                    . "'" . $miidle_name . "', "
                    . "'" . $title . "'"
                    . ")";
            $insert_new_user_res = $this->_mysqli->query($insert_new_user);

            $insert_new_user_email = "INSERT INTO `frontend_users_email` (cust_id, email, password) "
                    . "VALUES"
                    . "("
                    . "'" . $customer_id . "', "
                    . "'" . $email . "', "
                    . "'" . $password . "'"
                    . ")";
            $insert_new_user_email_res = $this->_mysqli->query($insert_new_user_email);

            $insert_user_status = "INSERT INTO `frontend_users_status` (cust_id, last_login) "
                    . "VALUES"
                    . "("
                    . "'" . $customer_id . "', "
                    . "'" . $register_date . "'"
                    . ")";
            $insert_user_status_res = $this->_mysqli->query($insert_user_status);

            if ($insert_user_status_res) {
                $this->flag = 1;
                $message = array("1" => "Your account has been registered. Please check your email for activation link.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-success-message";
                $hash = base64_encode($customer_id);
                $activation_message = ""
                        . "<html>"
                        . "<head>"
                        . "<title></title>"
                        . "<style>"
                        . ".rock-mail-container{"
                        . "    padding-right: 15px;
                               padding-left: 15px;
                               margin-right: auto;
                                margin-left: auto;"
                        . "}"
                        . "</style>"
                        . "</head>"
                        . "<body>"
                        . "<div class='rock-mail-container'>"
                        . "<h2>Thank you for registering with us.</h2>"
                        . "<p><b>Dear {$first_name} {$last_name},</b></p>"
                        . "<p>Below is a link to activate your account. Please follow the link.</p>"
                        . "<p>Activation Line: <a href='http://theline.growarock.com/accounts?cmd=activate&stat={$hash}'>Click for account activation</a></p>"
                        . "</div>"
                        . "</body>"
                        . "</html>";

                $this->SendEmail($email, "Account activation link", $activation_message, "rostom.sahakian@gmail.com");

                header("Refresh: 2; url='/account?cmd=login&ssid=" . md5($_COOKIE['order']) . "'");
            }
        }
    }

    public function SendEmail($to, $mail_subject, $message, $from) {

        $subject = $mail_subject;
        $headers = "MIME-Version: 1.0 \r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
        $headers .= "From: $from \r\n";
        $headers .= "Reply-To: $to \r\n";
        mail($to, $subject, $message, $headers);
    }

    public function ActivateFrontEndUser($data) {
        $activated_date = date("d-M-Y H:i:s");
        $activation_code = base64_decode($data);
        $sql = "UPDATE `frontend_users_status` SET status ='1', last_login = '" . $activated_date . "' WHERE `cust_id` = '" . $activation_code . "'";
        $result = $this->_mysqli->query($sql);
        if ($result) {
            return true;
        } else {
            return FALSE;
        }
    }

    public function FrontEndLoginForm($data) {
        if (isset($_REQUEST['stat'])) {

            if ($this->ActivateFrontEndUser($_REQUEST['stat'])) {
                $this->flag = 1;
                $message = array("1" => "Your account is now active. You may login with your credentials.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-success-message";
            }
        }
        if (isset($_REQUEST['login'])) {
            if ($this->DoLoginFrontEndUser($_REQUEST)) {
                
            }
        }
        ?>
        <div class="container rock-main-container">
            <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6 <?= $this->alert_class ?>">

                    <?php
                    if ($this->flag == 1) {
                        ?>

                        <div class="alert alert-warning  alert-dismissible" role="alert">
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
                </div>
                <div class="col-md-3"></div>

                <div class="col-md-12 rock-front-end-accounts">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">

                        <form method="post">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5><b><i class="fa fa-sign-in"></i>&nbsp; Login</b></h5>
                                    <p style="text-align: right;"><span style="color:#DA2430;">*</span>&nbsp;Indicates required field.</p>

                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Username<span style="color:#DA2430;">*</span>:</label>
                                        <input type="email" name="login_email" value="<?= isset($_REQUEST['login_email']) ? $_REQUEST['login_email'] : '' ?>" id="login_email" class="form-control input-sm"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Password<span style="color:#DA2430;">*</span>:</label>
                                        <input type="password" name="login_pass" value="<?= isset($_REQUEST['login_pass']) ? $_REQUEST['login_pass'] : '' ?>" id="login_pass" class="form-control input-sm"/>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="cmd" value="login" />
                                        <input type="submit" name="login" value="login" class="btn btn-danger"/>
                                    </div>
                                    <div class="form-group">
                                        <a href="#">Forgotten password?</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
        </div>
        <?php
    }

    public function DoLoginFrontEndUser($data) {


        if (empty($data['login_email']) && empty($data['login_pass'])) {
            $this->flag = 1;
            $message = array("1" => "All required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (empty($data['login_email']) || empty($data['login_pass'])) {
            $this->flag = 1;
            $message = array("1" => "One or more required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else {

            $sql = "SELECT `cust_id` FROM `frontend_users_email` WHERE `email` = '" . $data['login_email'] . "' AND `password` ='" . md5($data['login_pass']) . "'";
            $result = $this->_mysqli->query($sql);
            $num_rows = $result->num_rows;
            if ($num_rows > 0) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                    $get_account_stat = "SELECT `status` FROM `frontend_users_status` WHERE `cust_id` = '" . $row['cust_id'] . "' AND `status` = '1' ";
                    $get_account_stat_res = $this->_mysqli->query($get_account_stat);
                    $status = $get_account_stat_res->num_rows;
                    if ($status > 0) {
                        $_SESSION['timeout'] = time();
                        $_SESSION['user_log'] = uniqid();
                        $user_session = $_SESSION['user_log'];
                        $insert_session = "UPDATE `frontend_users_status` SET `last_session` = '" . $user_session . "' WHERE `cust_id` = '" . $row['cust_id'] . "'";
                        $insert_session_result = $this->_mysqli->query($insert_session);
                        $_SESSION['user_id'] = $row['cust_id'];

                        header('Location: /account?cmd=user-acount&s=' . $user_session);
                    } else {
                        $this->flag = 1;
                        $message = array("1" => "Account is not active.");
                        array_push($this->messages, $message);
                        $this->alert_class = "rock-warning-message";
                    }
                }
            } else {
                $this->flag = 1;
                $message = array("1" => "Authentication failed. You entered an incorrect username or password.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-warning-message";
            }
        }
    }

    public function FrontEndUserPanel($data) {
        ?>
        <div class="container rock-main-container ">
            <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            <div class="row">
                <?php
                $this->GetFrontEndUserData($_GET['s']);
                foreach ($this->user_data as $user_data) {
                    
                }
                ?>
                <div class="col-md-12 rock-accounts">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5><b><i class="fa fa-black-tie"></i>&nbsp;Your panel</b></h5>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5><b><i class="fa fa-cog"></i>&nbsp;Settings</b></h5>
                                        </div>
                                        <div class="panel-body">
                                            <ul class="list-group">
                                                <li class="list-group-item"><a href="/account?cmd=user-acount&view=edit-profile&s=<?= $_GET['s'] ?>">Edit profile</a></li>
                                                <li class="list-group-item"><a href="/account?cmd=user-acount&view=add-ship&s=<?= $_GET['s'] ?>">Add/Edit Shipping Information</a></li>
                                                <li class="list-group-item"><a href="/account?cmd=user-acount&view=change-pass&s=<?= $_GET['s'] ?>">Change password</a></li>
                                                <li class="list-group-item"><a href="/account?cmd=user-acount&view=view-wishlist&s=<?= $_GET['s'] ?>">View Wishlist</a></li>
                                                <li class="list-group-item"><a href="/account?cmd=user-acount&view=order-history&s=<?= $_GET['s'] ?>">Order history</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="panel panel-default rock-front-end-accounts">
                                        <div class="panel-heading">
                                            <h5><b><i class="fa fa-user"></i>&nbsp;Profile</b></h5>
                                        </div>
                                        <div class="panel-body">
                                            <?php
                                            if (isset($_REQUEST['view'])) {

                                                switch ($_REQUEST['view']) {
                                                    case "edit-profile":
                                                        if (isset($_REQUEST['edit_profile'])) {
                                                            $this->DoUpdateUserInformation($_REQUEST);
                                                        }
                                                        $this->AccountDoEditProfile($user_data);

                                                        break;
                                                    case "add-ship":
                                                        if (isset($_REQUEST['update_shipping'])) {
                                                            $this->DoAddOrUpdateUserAddress($_REQUEST);
                                                        }
                                                        $this->AccountAddShippingInfo($user_data['cust_id']);
                                                        break;
                                                    case "change-pass":
                                                        if(isset($_REQUEST['do_change_pass'])){
                                                            $this->DoChangeUserPassword($_REQUEST);
                                                        }
                                                        $this->AccountChangePassword($user_data['cust_id'],$user_data['password']);
                                                        break;
                                                    case "view-wishlist":
                                                        break;
                                                    case "order-history":
                                                        $this->SeeOrderHistory($user_data['cust_id']);
                                                        break;
                                                }
                                            } else {
                                                $this->AccountDoEditProfile($user_data);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function GetFrontEndUserData($data) {

        $sql = "SELECT `cust_id` FROM `frontend_users_status` WHERE `last_session` = '" . $data . "'";
     
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $get_all_data = "SELECT `desc`.*, `email`.* FROM `frontend_users_desc` `desc` , `frontend_users_email` `email` "
                        . " WHERE `desc`.cust_id = `email`.cust_id  AND `desc`.cust_id ='" . $row['cust_id'] . "'";

                $get_all_data_res = $this->_mysqli->query($get_all_data);
                if ($get_all_data_res->num_rows > 0) {
                    while ($user_data = $get_all_data_res->fetch_array(MYSQLI_ASSOC)) {

                        $this->user_data[] = $user_data;
                    }
                    return $this->user_data;
                }
            }
        }
    }

    public function LogFronTEndUserOut($data) {
        ?>
        <div class="container rock-main-container">
            <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            <div class="row">
                <div class="col-md-12" style="height:300px;">
                    <a href="/account?cmd=login&ssid=<?= md5($_COOKIE['order']) ?>"><i class="fa fa-sign-in"></i>&nbsp;Log back in</a>
                </div>
            </div>
        </div>
        <?php
    }

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $new_session = uniqid();
            $sql = "UPDATE `frontend_users_status` SET `last_session` ='" . $new_session . "' WHERE `last_session` = '" . $_SESSION['user_log'] . "'";
            $result = $this->_mysqli->query($sql);

            unset($_SESSION['user_id']);
            unset($_SESSION['user_log']);
            unset($_SESSION['timeout']);
            unset($_SESSION['wholesaler_on']);
        }
    }

    public function AccountDoEditProfile($data) {
        ?>
        <div class="row">
            <div class="col-md-12 <?= $this->alert_class ?>">

                <?php
                if ($this->flag == 1) {
                    ?>

                    <div class="alert alert-warning  alert-dismissible" role="alert">
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
            </div>
        </div>
        <form method="post">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="edit_f_name" value="<?= isset($_REQUEST['edit_f_name']) ? $_REQUEST['edit_f_name'] : $data['f_name'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>last Name:</label>
                <input type="text" name="edit_l_name" value="<?= isset($_REQUEST['edit_l_name']) ? $_REQUEST['edit_l_name'] : $data['l_name'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>Middle Name:</label>
                <input type="text" name="edit_m_name" value="<?= isset($_REQUEST['edit_m_name']) ? $_REQUEST['edit_m_name'] : $data['middle_name'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="edit_title" value="<?= isset($_REQUEST['edit_title']) ? $_REQUEST['edit_title'] : $data['title'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="text" name="edit_email" value="<?= isset($_REQUEST['edit_email']) ? $_REQUEST['edit_email'] : $data['email'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <input type="hidden" name="user_id" value="<?= $data['cust_id'] ?>"/>
                <input type="hidden" name="cmd" value="user-acount" />
                <input type="hidden" name="view" value="edit-profile"/>
                <input type="submit" name="edit_profile" value="Update" class="btn btn-success btn-xs"/>
            </div>
        </form>
        <?php
    }

    public function DoUpdateUserInformation($data) {

        if (empty($data['edit_f_name']) && empty($data['edit_l_name']) && empty($data['edit_email'])) {
            $this->flag = 1;
            $message = array("1" => "All required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (empty($data['edit_f_name']) || empty($data['edit_l_name']) || empty($data['edit_email'])) {
            $this->flag = 1;
            $message = array("1" => "One or more required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (!filter_var($data['edit_email'], FILTER_VALIDATE_EMAIL)) {
            $this->flag = 1;
            $message = array("1" => "Please enter a valid email address.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else {
            $things_to_update = array();
            $sql = "SELECT * FROM `frontend_users_desc` WHERE `cust_id` = '" . $data['user_id'] . "'";
            $result = $this->_mysqli->query($sql);
            if ($result->num_rows > 0) {
                $updates = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                    if (trim($data['edit_f_name']) != $row['f_name']) {
                        $updates['f_name'] = $data['edit_f_name'];
                    }
                    if (trim($data['edit_l_name']) != $row['l_name']) {
                        $updates['l_name'] = $data['edit_l_name'];
                    }
                    if (trim($data['edit_m_name']) != $row['middle_name']) {
                        $updates['middle_name'] = $data['edit_m_name'];
                    }
                    if (trim($data['edit_title']) != $row['title']) {
                        $updates['title'] = $data['edit_title'];
                    }
                }
                array_push($things_to_update, $updates);
                foreach ($things_to_update as $field => $value) {
                    if (!empty($value)) {
                        foreach ($value as $f => $v) {

                            $update_data = "UPDATE `frontend_users_desc` SET `" . $f . "` = '" . $v . "' WHERE `cust_id` = '" . $data['user_id'] . "'";

                            $update_data_res = $this->_mysqli->query($update_data);
                            $this->flag = 1;
                            $message = array("1" => "<strong>" . $f . "</strong> was updated");
                            $this->alert_class = "rock-success-message";
                            array_push($this->messages, $message);
                        }
                    } else {
                        $this->flag = 1;
                        $message = array("1" => "Nothing to update.");
                        array_push($this->messages, $message);
                        $this->alert_class = "rock-warning-message";
                    }
                }
            }
            $get_email = "SELECT `email` FROM `frontend_users_email` WHERE  `cust_id` = '" . $data['user_id'] . "'";

            $get_email_res = $this->_mysqli->query($get_email);

            if ($get_email_res->num_rows > 0) {
                while ($email_row = $get_email_res->fetch_array(MYSQLI_ASSOC)) {

                    if (trim($data['edit_email']) != $email_row['email']) {

                        $update_email = "UPDATE `frontend_users_email` SET `email` = '" . $data['edit_email'] . "' WHERE  `cust_id` = '" . $data['user_id'] . "'";
                        $update_email_res = $this->_mysqli->query($update_email);
                        if ($update_email_res) {
                            $this->flag = 1;
                            $message = array("1" => "<strong>Email </strong> was updated");
                            $this->alert_class = "rock-success-message";
                            array_push($this->messages, $message);
                        }
                    }
                }
            }
        }
    }

    public function AccountAddShippingInfo($data) {

        $sql = "SELECT * FROM `frontend_users_address` WHERE `cust_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->shipping_info[] = $row;
            }
        }
        if ($this->shipping_info != NULL) {
            foreach ($this->shipping_info as $shipping) {
                
            }
        } else {
            $shipping['address_1'] = "";
            $shipping['address_2'] = "";
            $shipping['city'] = "";
            $shipping['zip_code'] = "";
            $shipping['state'] = "";
            $shipping['province'] = "";
            $shipping['coutry'] = "";
        }
        ?>
        <div class="row">
            <div class="col-md-12 <?= $this->alert_class ?>">

                <?php
                if ($this->flag == 1) {
                    ?>

                    <div class="alert alert-warning  alert-dismissible" role="alert">
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
            </div>
        </div>
        <form method="post">
            <div class="form-group">
                <label>Address 1:</label>
                <input type="text" name="address_1" value="<?= isset($_REQUEST['address_1']) ? $_REQUEST['address_1'] : $shipping['address_1'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>Address 2:</label>
                <input type="text" name="address_2" value="<?= isset($_REQUEST['address_2']) ? $_REQUEST['address_2'] : $shipping['address_2'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>City:</label>
                <input type="text" name="city" value="<?= isset($_REQUEST['city']) ? $_REQUEST['city'] : $shipping['city'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>Zip Code/Postal Code:</label>
                <input type="text" name="zip_code" value="<?= isset($_REQUEST['zip_code']) ? $_REQUEST['zip_code'] : $shipping['zip_code'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>State:</label>
                <input type="text" name="state" value="<?= isset($_REQUEST['state']) ? $_REQUEST['state'] : $shipping['state'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>Province:</label>
                <input type="text" name="province" value="<?= isset($_REQUEST['province']) ? $_REQUEST['province'] : $shipping['province'] ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>Country:</label>
                <select name="country" class="form-control">
                    <option value="--">select</option>
                    <?php
                    $this->GetAllTheCountries();
                    foreach ($this->country as $country) {
                        $selected = "";
                        if ($shipping['country'] == $country['code'] && !isset($_REQUEST['country'])) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected_country = isset($_REQUEST['country']) ? $_REQUEST['country'] : '';
                            if (isset($_REQUEST['country'])) {
                                if ($selected_country == $country['code']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                            }
                        }
                        ?>
                        <option value="<?= $country['code'] ?>" <?= $selected ?>><?= $country['name'] ?></option>
                        <?php
                    }
                    ?>
                </select>

            </div>
            <div class="form-group">
                <input type="hidden" name="user_id" value="<?= $data ?>"/>
                <input type="hidden" name="cmd" value="user-acount" />
                <input type="hidden" name="view" value="add-ship"/>
                <input type="submit" name="update_shipping" value="Update" class="btn btn-success btn-xs"/>
            </div>
        </form>
        <?php
    }

    public function DoAddOrUpdateUserAddress($data) {

        if (empty($data['address_1']) && empty($data['city']) && empty($data['zip_code']) && $data['country'] == "--") {
            $this->flag = 1;
            $message = array("1" => "All reqired fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (empty($data['address_1']) || empty($data['city']) || empty($data['zip_code']) || $data['country'] == "--") {
            $this->flag = 1;
            $message = array("1" => "One or more required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if ($data['country'] == "US" && empty($data['state'])) {

            $this->flag = 1;
            $message = array("1" => "Please enter the sate that you reside.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if ($data['country'] != "US" && empty($data['province'])) {

            $this->flag = 1;
            $message = array("1" => "Please enter the province in which you reside.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else {


            $things_to_update = array();
            $sql = "SELECT * FROM `frontend_users_address` WHERE `cust_id` = '" . $data['user_id'] . "'";
            $result = $this->_mysqli->query($sql);
            if ($result->num_rows > 0) {
                /*
                 * Update 
                 */
                $updates = array();
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    if (trim($data['address_1']) != $row['address_1']) {
                        $updates['address_1'] = $data['address_1'];
                    }
                    if (trim($data['address_2']) != $row['address_2']) {
                        $updates['address_2'] = $data['address_2'];
                    }
                    if (trim($data['city']) != $row['city']) {
                        $updates['city'] = $data['city'];
                    }
                    if (trim($data['zip_code']) != $row['zip_code']) {
                        $updates['zip_code'] = $data['zip_code'];
                    }
                    if (trim($data['state']) != $row['state']) {
                        $updates['state'] = $data['state'];
                    }
                    if (trim($data['province']) != $row['province']) {
                        $updates['province'] = $data['province'];
                    }
                    if (trim($data['country']) != $row['country']) {
                        $updates['country'] = $data['country'];
                    }
                }
                array_push($things_to_update, $updates);
                foreach ($things_to_update as $field => $value) {
                    if (!empty($value)) {
                        foreach ($value as $f => $v) {

                            $update_data = "UPDATE `frontend_users_address` SET `" . $f . "` = '" . $v . "' WHERE `cust_id` = '" . $data['user_id'] . "'";
                            $update_data_res = $this->_mysqli->query($update_data);
                            $this->flag = 1;
                            $message = array("1" => "<strong>" . $f . "</strong> was updated");
                            $this->alert_class = "rock-success-message";
                            array_push($this->messages, $message);
                        }
                    } else {
                        $this->flag = 1;
                        $message = array("1" => "Nothing to updates.");
                        $this->alert_class = "rock-warning-message";
                        array_push($this->messages, $message);
                    }
                }
            } else {
                /*
                 * Insert the new data
                 */

                $insert = "INSERT INTO `frontend_users_address` (cust_id, address_1, address_2, city, zip_code, state, province, country)"
                        . "VALUES"
                        . "("
                        . "'" . $data['user_id'] . "', "
                        . "'" . $data['address_1'] . "',"
                        . "'" . $data['address_2'] . "',"
                        . "'" . $data['city'] . "', "
                        . "'" . $data['zip_code'] . "', "
                        . "'" . $data['state'] . "', "
                        . "'" . $data['province'] . "', "
                        . "'" . $data['country'] . "'"
                        . ")";
                $insert_result = $this->_mysqli->query($insert);
                if ($insert_result) {
                    $this->flag = 1;
                    $message = array("1" => "You shipping information was added.");
                    $this->alert_class = "rock-success-message";
                    array_push($this->messages, $message);
                } else {
                    $this->flag = 1;
                    $message = array("1" => "Unable to insert.");
                    $this->alert_class = "rock-success-message";
                    array_push($this->messages, $message);
                }
            }
        }
    }

    public function GetAllTheCountries() {
        $sql = "SELECT * FROM `countries` ORDER BY name ASC";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->country[] = $row;
        }
        return $this->country;
    }

    public function AccountChangePassword($u, $p) {
     
        ?>
        <div class="row">
            <div class="col-md-12 <?= $this->alert_class ?>">

                <?php
                if ($this->flag == 1) {
                    ?>

                    <div class="alert alert-warning  alert-dismissible" role="alert">
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
            </div>
        </div>
        <form method="post">
            <div class="form-group">
                <label>Enter Old Password:</label>
                <input type="password" name="old_password" value="<?= isset($_REQUEST['old_password']) ? $_REQUEST['old_password'] : '' ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password_1" value="<?= isset($_REQUEST['new_password_1']) ? $_REQUEST['new_password_1'] : '' ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="new_password_2" value="<?= isset($_REQUEST['new_password_2']) ? $_REQUEST['new_password_2'] : '' ?>" class="form-control input-sm"/>
            </div>
            <div class="form-group">
                <input type="hidden" name="user_id" value="<?= $u ?>"/>
                <input type="hidden" name="o_pass" value="<?= $p ?>"/>
                <input type="hidden" name="cmd" value="user-acount" />
                <input type="hidden" name="view" value="change-pass"/>
                <input type="submit" name="do_change_pass" value="Change Password" class="btn btn-success btn-xs"/>
            </div>
        </form>
        <?php
    }

    public function DoChangeUserPassword($data) {
       
        if (empty($data['old_password']) && empty($data['new_password_1']) && empty($data['new_password_2'])) {
            $this->flag = 1;
            $message = array("1" => "All reqired fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (empty($data['old_password']) || empty($data['new_password_1']) || empty($data['new_password_2'])) {
            $this->flag = 1;
            $message = array("1" => "One or more of the required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (md5($data['old_password']) != $data['o_pass']) {
            $this->flag = 1;
            $message = array("1" => "Incorrect password.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if ($data['new_password_2'] != $data['new_password_1']) {
            $this->flag = 1;
            $message = array("1" => "New passwords did not match.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else {
            $new_password = md5($data['new_password_1']);

            $sql = "UPDATE `frontend_users_email` SET `password` = '" . mysqli_real_escape_string($this->_mysqli, $new_password) . "' WHERE `cust_id` = '".$data['user_id']."'";
            $result = $this->_mysqli->query($sql);
            if ($result) {
                $this->flag = 1;
                $message = array("1" => "Password Updated.");
                $this->alert_class = "rock-success-message";
                array_push($this->messages, $message);
            }
        }
    }

    public function SeeOrderHistory($data) {
        ?>
        <table class="table table-bordered table-hover">
            <tr>
                <th>Transaction ID</th>
                <th>Order Qty</th>
                <th>Unit Price</th>
                <th>Total Price</th>             
                <th>Auth Code</th>
                <th>Status</th>
                <th>Order Date</th>

            </tr>
            <?php
            $this->GetPurchaseHistory($data);
            if ($this->trans_history != NULL) {
                foreach ($this->trans_history as $t_history) {
                    ?>
                    <tr>
                        <td><?= $t_history['transaction_id'] ?></td>
                        <td><?= $t_history['sold_item_qty'] ?></td>
                        <td><?= $t_history['sold_item_unit_price'] ?></td>
                        <td><?= $t_history['item_total'] ?></td>
                        <td><?= $t_history['order_id'] ?></td>
                        <td><?= $t_history['status'] ?></td>
                        <td><?= $t_history['sold_date_time'] ?></td>

                    </tr>
                    <?php
                }
            }
            ?>

        </table>
        <?php
    }

    public function GetPurchaseHistory($data) {

        $sql = "SELECT * FROM `frontend_users_purchase_history` WHERE `cust_id` = '" . $data . "'";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $get_transaction = "SELECT `so`.*, `co`.* FROM `store_orders`so, `checked_out`co  "
                        . "WHERE `so`.`transaction_id` = `co`.`transaction_id` AND `so`.`transaction_id` = '" . $row['transaction_id'] . "'";

                $get_transaction_result = $this->_mysqli->query($get_transaction);
                if ($get_transaction_result->num_rows > 0) {
                    while ($orders = $get_transaction_result->fetch_array(MYSQLI_ASSOC)) {

                        $this->trans_history[] = $orders;
                    }
                }
            }
            return $this->trans_history;
        }
    }

}
