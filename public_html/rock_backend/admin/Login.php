<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author rostom
 */
class Login {

    private $_mysqli;
    private $_db;
    public $flag = 0;
    public $messages;
    public $_message_val = array();
    public $_query;
    public $alert_class = "";
    public $_sys_message = array();
    public $log_out;
    
    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->log_out = new Logout();
        if (isset($_REQUEST['do_login'])) {
            $this->LoginProcess($_REQUEST);
        }
        $this->CreatUserstable();

        if ($this->CheckForAdminUsersExistance("rostom.sahakian@gmail.com")) {

            $admin_info = array(
                "name" => "Rostom",
                "last_name" => "Sahakian",
                "email" => "rostom.sahakian@gmail.com",
                "password" => ADMIN_PASS,
                "company" => "GrowaRock",
                "phone" => "818-723-3190",
                "user_mode" => "Admin",
                "status" => "1",
                "login_status" => 0,
            );

            $this->InsertAdminInfoIntoTable($admin_info);
        }
        $this->_query = new Queries();
    }

    public function LoginProcess(array $logindata = NULL) {

        if ($logindata != NULL) {

            if ($logindata['uname'] == "" || $logindata['password'] == "") {

                $this->flag = 1;
                if ($this->flag == 1) {
                    $this->messages = array("One or more fields are empty");
                    $this->Messages($this->messages);
                    $this->flag = 0;
                }
            } else if ($logindata['uname'] == "" && $logindata['password'] == "") {
                $this->flag = 1;
                if ($this->flag == 1) {
                    $this->messages = array("Both fields are empty");
                    $this->Messages($this->messages);
                    $this->flag = 0;
                }
            } else {

                /*
                 * Check the given data
                 */
                $username = trim($logindata['uname']);
                $password = trim(md5($logindata['password']));

                $check_user = "SELECT * FROM `rock_users` WHERE `email` = '" . $username . "' AND `password` ='" . $password . "'";
                $check_user_res = $this->_mysqli->query($check_user);
                $num_rows = $check_user_res->num_rows;
                if ($num_rows > 0) {

                    while ($row = $check_user_res->fetch_array(MYSQLI_ASSOC)) {

                        if ($row['status'] == 0) {
                            $this->flag = 1;
                            if ($this->flag == 1) {
                                $this->messages = array("Your account has been <b>blcoked</b> by an administaror. <br/>Please contact your admin to resolve this issue.");
                                $this->Messages($this->messages);
                                $this->flag = 0;
                                
                            }
                        } else {

                            $_SESSION['logged_in'] = uniqid();
                            $_SESSION['login_code'] = $row['login_code'];
                            $_SESSION['USER_ID'] = $row['id'];
                            $_SESSION['USER_STATUS'] = $row['status'];
                            $update_login_status = "UPDATE `rock_users` SET `login_status` ='1' WHERE `id` = '" . $row['id'] . "'";
                            $update_login_status_res = $this->_mysqli->query($update_login_status);
                            header('Location: index.php');
                        }
                    }
                } else {
                    $this->flag = 1;
                    if ($this->flag == 1) {
                        $this->messages = array("Authentication failed.<br/> You entered an incorrect username or password.");
                        $this->Messages($this->messages);
                        $this->flag = 0;
                    }
                }
            }
        }
    }

    public function Messages(array $message) {

        $this->_message_val = $message;
    }

    public function PrintMessage() {
        return $this->_message_val;
    }

    public function LoginForm() {
        if ($this->PrintMessage() != NULL) {
            ?>
            <div class="col-md-12">
                <div class="panel panel-danger">
                    <div class="panel-body">
                        <span>
                            <ul>
                                <?php
                                foreach ($this->PrintMessage() as $m) {
                                    ?>
                                    <li>
                                        <?= $m ?>
                                    </li>   
                                    <?php
                                }
                                ?>
                            </ul>
                        </span>
                    </div>
                </div>
                <?php
            }
            ?>
            <form method="post">
                <div class="form-group">
                    <label>Username (<span style="font-style: italic; font-size: 9pt;">Email</span>):</label>
                    <input type="text" name="uname" class="form-control" id="uname_login" value="<?= isset($_REQUEST['uname']) ? $_REQUEST['uname'] : '' ?>"/>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" id="password_login" value="<?= isset($_REQUEST['password']) ? $_REQUEST['password'] : '' ?>"/>
                </div>
                <input type="submit" class="btn btn-success" name="do_login" id="do_login" value="login"/>

            </form>
            <ul class="prock-ul">
                <li><a href="?forgot-p=true">Forgotten password?</a></li>
            </ul>

        </div>


        <?php
    }

    public function CreatUserstable() {
        $sql = "CREATE TABLE IF NOT EXISTS rock_users"
                . "("
                . "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
                . "name VARCHAR (250) NOT NULL,"
                . "last_name VARCHAR (250) NOT NULL,"
                . "email VARCHAR (500) NOT NULL,"
                . "password VARCHAR (500) NOT NULL,"
                . "company VARCHAR(500) NOT NULL,"
                . "phone VARCHAR (500) NOT NULL,"
                . "user_mode VARCHAR (50) NOT NULL,"
                . "status INT(2) NOT NULL,"
                . "login_status INT(2) NOT NULL DEFAULT (0)"
                . "date_added VARCHAR (25) NOT NULL,"
                . "last_login VARCHAR (25) NOT NULL,"
                . "login_code VARCHAR (500) NOT NULL"
                . ")";
        $result = $this->_mysqli->query($sql);
    }

    public function InsertAdminInfoIntoTable(array $data) {
        $today = date('m/d/y');
        $login_code = uniqid();
        $sql = "INSERT INTO `rock_users` (name, last_name, email, password, company, phone, user_mode, status, login_status, date_added, last_login, login_code)"
                . " VALUES "
                . "('" . $data['name'] . "', '" . $data['last_name'] . "', '" . $data['email'] . "', '" . $data['password'] . "', '" . $data['company'] . "', '" . $data['phone'] . "', '" . $data['user_mode'] . "', '" . $data['status'] . "', '" . $data['login_status'] . "', '" . $today . "', '" . $today . "', '" . $login_code . "')";
        $result = $this->_mysqli->query($sql);
    }

    public function CheckForAdminUsersExistance($email) {

        $sql = "SELECT `email` FROM `rock_users` WHERE `email` = '" . $email . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function ForgotPasswordForm() {


        if ($this->flag == 1) {
            ?>

            <div class="alert alert-<?= $this->alert_class ?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <ul>
                    <?php
                    foreach ($this->_sys_message as $m) {
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


        <div class="panel panel-default">
            <div class="panel-heading prock-panel-heading">
                <h5><i class="fa fa-lock"></i>&nbsp; Send Token</h5>
            </div>
            <form method="post">
                <div class="panel-body">
                    <div class="form-group">
                        <label>Enter your email:</label>
                        <input type="email" name="email_rec" value="<?= isset($_REQUEST['email_rec']) ? $_REQUEST['email_rec'] : ''; ?>" class="form-control input-sm"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Send Token" name="check_email" class="btn btn-primary btn-sm" />
                    </div> 
                </div>
            </form>

            <form method="post">
                <div class="panel-body">
                    <p>If you've forgotten your password, use the form above to send a token for creating a new password, then use the token below to change your password.</p>
                    <div class="form-group">
                        <label>Token</label>
                        <input type="text" name="token" value="<?= isset($_REQUEST['token']) ? $_REQUEST['token'] : '' ?>" class="form-control input-sm"/>
                    </div>
                    <div class="form-group">
                        <label>Enter New Password</label>
                        <input type="password" name="new_pass_1" value="<?= isset($_REQUEST['new_pass_1']) ? $_REQUEST['new_pass_1'] : ''; ?>" class="form-control input-sm"/>
                    </div>
                    <div class="form-group">
                        <label>Re-Enter</label>
                        <input type="password" name="new_pass_2" value="<?= isset($_REQUEST['new_pass_2']) ? $_REQUEST['new_pass_2'] : ''; ?>" class="form-control input-sm"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Reset Password" name="do_reset" class="btn btn-primary btn-sm" />
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function DoSendToken($data) {
        if (empty($data['email_rec'])) {
            $this->flag = 1;
            $message = array("1" => "Please enter the registered email.");
            array_push($this->_sys_message, $message);
            $this->alert_class = "warning";
        } else if (filter_var($data['email_rec'], FILTER_VALIDATE_EMAIL) === false) {
            $this->flag = 1;
            $message = array("1" => "Please enter a valid email.");
            array_push($this->_sys_message, $message);
            $this->alert_class = "warning";
        } else {
            /*
             * Now lets check the system and see if user exists
             */
            $token = uniqid();
            $sql = "SELECT `id`, `name`, `last_name` FROM `rock_users` WHERE `email` = '" . trim($data['email_rec']) . "'";
            $result = $this->_mysqli->query($sql);
            $check_num = $result->num_rows;
            if ($check_num > 0) {
                /*
                 * Send Email
                 */
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $to = trim($data['email_rec']);
                    $subject = "Rock login password reset token.";
                    $message = "<html>"
                            . "<head>"
                            . "<title>Password Recovery</title>"
                            . "</head>"
                            . "<body>"
                            . "<h5>Hi There {$row['name']} {$row['last_name']}</h5>"
                            . "<p>You have requested to reset your Rock backend password.<br/>Please copy the token number.</p>"
                            . "<p>Token: <strong>{$token}</strong></p>"
                            . "<p>Follow Link: <a href='http://project.rock.webulence.com/admin/?forgot-p=true' target='_Blank'>Go to Rock's backend</p>"
                            . "<br/>"
                            . "<br/>"
                            . "<p>Sincerely,</p>"
                            . "<p>Rock customer service.</p>"
                            . "</body>"
                            . "</html>";

                    // To send HTML mail, the Content-type header must be set
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    // Additional headers
                    $headers .= 'To:' . $row['name'] . '  <' . $data['email_rec'] . '>' . "\r\n";
                    mail($to, $subject, $message, $headers);

                    /*
                     * Update the password and put in the token
                     */
                    $update_row = "UPDATE `rock_users` SET `password` = '" . $token . "' WHERE `email` = '" . $data['email_rec'] . "'";
                    $update_row_res = $this->_mysqli->query($update_row);

                    $this->flag = 1;
                    $message = array("1" => "An email with a token has been sent to {$data['email_rec']}.");
                    array_push($this->_sys_message, $message);
                    $this->alert_class = "success";
                }
            } else {
                $this->flag = 1;
                $message = array("1" => "We could not find that email address in our system. Please try again.");
                array_push($this->_sys_message, $message);
                $this->alert_class = "warning";
            }
        }
    }

    public function DoResetpassword($data) {

        if (empty($data['token']) && empty($data['new_pass_1']) && empty($data['new_pass_2'])) {
            $this->flag = 1;
            $message = array("1" => "All fields are required");
            array_push($this->_sys_message, $message);
            $this->alert_class = "warning";
        } else if (empty($data['token']) || empty($data['new_pass_1']) || empty($data['new_pass_2'])) {
            $this->flag = 1;
            $message = array("1" => "One or more fields are empty");
            array_push($this->_sys_message, $message);
            $this->alert_class = "warning";
        } else if ($data['new_pass_2'] != $data['new_pass_1']) {
            $this->flag = 1;
            $message = array("1" => "Passwords did not match");
            array_push($this->_sys_message, $message);
            $this->alert_class = "warning";
        } else if (strlen($data['new_pass_1']) < 4) {
            $this->flag = 1;
            $message = array("1" => "Password must be at least 4 letters long.");
            array_push($this->_sys_message, $message);
            $this->alert_class = "warning";
        } else {
            $check_token_value = "SELECT `password`, `email` FROM `rock_users` WHERE `password` = '" . trim($data['token']) . "'";
            $check_token_value_res = $this->_mysqli->query($check_token_value);
            $check_num_rows = $check_token_value_res->num_rows;
            if ($check_num_rows > 0) {

                $update_passowrd = "UPDATE `rock_users` SET `password` = '" . trim(md5($data['new_pass_1'])) . "' WHERE `password` = '" . trim($data['token']) . "'";
                $update_passowrd_res = $this->_mysqli->query($update_passowrd);
                if ($update_passowrd_res) {
                    $this->flag = 1;
                    $message = array("1" => "Password Updated.");
                    array_push($this->_sys_message, $message);
                    $this->alert_class = "success";
                }
            } else {
                $this->flag = 1;
                $message = array("1" => "Invalid token has been provided. Please double check");
                array_push($this->_sys_message, $message);
                $this->alert_class = "warning";
            }
        }
    }

}
