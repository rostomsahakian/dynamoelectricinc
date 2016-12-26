<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WholeSaleRegLog
 *
 * @author rostom
 */
class WholeSaleRegLog {

    private $_mysqli;
    private $_db;
    public $message = array();
    public $flag = 0;
    public $alert_class = "";

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function WholeSaleRegAndLoginForm() {
        if (isset($_REQUEST['whole_sale_sign_up'])) {
            $this->DoSignUpWholeSaler($_REQUEST);
        }
        if (isset($_REQUEST['whole_sale_login'])) {
            $this->DoLogWholeSalerIn($_REQUEST);
        }
        ?>
        <div class="row">
            <div class="col-md-12 <?= $this->alert_class ?>">
                <?php
                if ($this->flag == 2) {
                    ?>

                    <div class="alert alert-warning  alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                        <ul>
                            <?php
                            foreach ($this->message as $m) {
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
        <div class="col-md-12 wholesale-forms" >
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp; Login <span style="float: right;"><span style="color:#B8202A">*</span>&nbsp;Denotes required fields.</span> </h5>

                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <form method="post">
                                <div class="form-group">
                                    <label>Username:<span style="color:#B8202A">*</span></label>
                                    <input type="text" name="username" value="<?= isset($_REQUEST['username']) ? $_REQUEST['username'] : '' ?>" id="wholesale_uname" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Password:<span style="color:#B8202A">*</span></label>
                                    <input type="password" name="password" value="<?= isset($_REQUEST['password']) ? $_REQUEST['password'] : '' ?>" id="wholesale_uname" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-success btn-sm" name="whole_sale_login" value="login" />
                                </div>
                            </form>
                            <a href="#">Forgotten Password?</a>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-6">
                            <p style="text-align: justify;">
                                <b>EXISTING WHOLESALE CUSTOMERS</b> Access to this websites wholesale products as well as wholesale pricing is exclusively for approved and
                                <b>EXISTING WHOLESALE CUSTOMERS ONLY</b>. In order to view and order wholesale products you will need to have a registered and approved account. 
                                Once registered you will need to be logged into the website via your Wholesale account credentials. 
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 <?= $this->alert_class ?>">
                    <?php
                    if ($this->flag == 1) {
                        ?>

                        <div class="alert alert-warning  alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                            <ul>
                                <?php
                                foreach ($this->message as $m) {
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

            <!--wholesale sign up form-->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5><i class="fa fa-user"></i>&nbsp;Sign Up<span style="float: right;"><span style="color:#B8202A">*</span>&nbsp;Denotes required fields.</span></h5>
                </div>
                <form method="post">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <legend>Personal Information</legend>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name:<span style="color:#B8202A">*</span></label>
                                    <input type="text" class="form-control" name="new_w_fname" value="<?= isset($_REQUEST['new_w_fname']) ? $_REQUEST['new_w_fname'] : '' ?>" placeholder="Enter your first name"/>
                                </div>
                                <div class="form-group">
                                    <label>Last Name:<span style="color:#B8202A">*</span></label>
                                    <input type="text" class="form-control" name="new_w_last_name" value="<?= isset($_REQUEST['new_w_last_name']) ? $_REQUEST['new_w_last_name'] : '' ?>" placeholder="Enter your last name"/>
                                </div>
                                <div class="form-group">
                                    <label>Email Address:<span style="color:#B8202A">*</span></label>
                                    <input type="text" class="form-control" name="new_w_email" value="<?= isset($_REQUEST['new_w_email']) ? $_REQUEST['new_w_email'] : '' ?>" placeholder="Enter your email"/>
                                </div>
                                <div class="form-group">
                                    <label>Confirm Email Address:<span style="color:#B8202A">*</span></label>
                                    <input type="text" class="form-control" name="new_w_conf_email" value="<?= isset($_REQUEST['new_w_conf_email']) ? $_REQUEST['new_w_conf_email'] : '' ?>" placeholder="Enter your email"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Telephone:<span style="color:#B8202A">*</span></label>
                                    <input type="text" class="form-control" name="new_w_phone" value="<?= isset($_REQUEST['new_w_phone']) ? $_REQUEST['new_w_phone'] : '' ?>" placeholder="Enter your phone number"/>
                                </div>
                                <div class="form-group">
                                    <label>Fax:</label>
                                    <input type="text" class="form-control" name="new_w_fax" value="<?= isset($_REQUEST['new_w_fax']) ? $_REQUEST['new_w_fax'] : '' ?>" placeholder="Enter your fax number"/>

                                </div>

                            </div>
                        </div>
                        <!--Address information-->
                        <div class="col-md-12">
                            <legend>Address Information</legend>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Street Address 1:<span style="color:#B8202A">*</span></label>
                                    <input type="text" name="new_w_add_1" value="<?= isset($_REQUEST['new_w_add_1']) ? $_REQUEST['new_w_add_1'] : '' ?>" class="form-control" placeholder="Enter your Address 1"/>
                                </div>
                                <div class="form-group">
                                    <label>Street Address 2:</label>
                                    <input type="text" name="new_w_add_2" value="<?= isset($_REQUEST['new_w_add_2']) ? $_REQUEST['new_w_add_2'] : '' ?>" class="form-control" placeholder="Enter your Address 2"/>
                                </div>
                                <div class="form-group">
                                    <label>City:<span style="color:#B8202A">*</span></label>
                                    <input type="text" name="new_w_city" value="<?= isset($_REQUEST['new_w_city']) ? $_REQUEST['new_w_city'] : '' ?>" class="form-control" placeholder="Enter your city"/>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State/Province:<span style="color:#B8202A">*</span></label>
                                    <input type="text" name="new_w_state" value="<?= isset($_REQUEST['new_w_state']) ? $_REQUEST['new_w_state'] : '' ?>" class="form-control" placeholder="Enter your state or province"/>
                                </div>
                                <div class="form-group">
                                    <label>Zip/Postal Code:<span style="color:#B8202A">*</span></label>
                                    <input type="text" name="new_w_zip" value="<?= isset($_REQUEST['new_w_zip']) ? $_REQUEST['new_w_zip'] : '' ?>" class="form-control" placeholder="Enter your zip or postal code"/>
                                </div>
                                <div class="form-group">
                                    <label>Country:<span style="color:#B8202A">*</span></label>
                                    <input type="text" name="new_w_country" value="<?= isset($_REQUEST['new_w_country']) ? $_REQUEST['new_w_country'] : '' ?>" class="form-control" placeholder="Enter your country"/>
                                </div>
                            </div>

                        </div>
                        <!--Company Information-->
                        <div class="col-md-12">
                            <legend>Company Information</legend>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Company Name:</label>
                                    <input type="text" name="new_w_comp_name" value="<?= isset($_REQUEST['new_w_comp_name']) ? $_REQUEST['new_w_comp_name'] : '' ?>" class="form-control" placeholder="Enter your company name"/>
                                </div>   
                                <div class="form-group">
                                    <label>Tax ID Number:</label>
                                    <input type="text" name="new_w_tax_id" value="<?= isset($_REQUEST['new_w_tax_id']) ? $_REQUEST['new_w_tax_id'] : '' ?>" class="form-control" placeholder="Enter your tax id number"/>
                                </div>
                                <div class="form-group">
                                    <label>Website <i>(if available)</i>:</label>
                                    <input type="text" name="new_w_website" value="<?= isset($_REQUEST['new_w_website']) ? $_REQUEST['new_w_website'] : '' ?>" class="form-control" placeholder="http://yourdomian.com"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>How Long Has This Company been in business?:</label>
                                    <input type="text" name="new_w_com_age" value="<?= isset($_REQUEST['new_w_com_age']) ? $_REQUEST['new_w_com_age'] : '' ?>" class="form-control" placeholder="years, months"/>
                                </div>  
                            </div>
                        </div>
                        <!--login Information-->
                        <div class="col-md-12">
                            <legend>Login Information</legend>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password:<span style="color:#B8202A">*</span></label>
                                    <input type="password" name="new_w_password1" value="<?= isset($_REQUEST['new_w_password1']) ? $_REQUEST['new_w_password1'] : '' ?>" class="form-control" />
                                </div>  

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Confirm Password:<span style="color:#B8202A">*</span></label>
                                    <input type="password" name="new_w_password2" value="<?= isset($_REQUEST['new_w_password2']) ? $_REQUEST['new_w_password2'] : '' ?>" class="form-control" />
                                </div>  

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="<?= RE_CAPTCH_SITE_KEY ?>"></div>

                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="form-group">
                                <input type="submit" name="whole_sale_sign_up" value="signup" class="btn btn-primary btn-sm"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>





        <?php
    }

    public function DoLogWholeSalerIn($data) {

        if (empty($data['username']) && empty($data['password'])) {
            $this->flag = 2;
            $message = array("1" => "All required fields are empty");
            array_push($this->message, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        } else if (empty($data['username']) || empty($data['password'])) {
            $this->flag = 2;
            $message = array("1" => "One or more of the required fields are empty.");
            array_push($this->message, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        } else {
            /*
             * Check db to see if user exists
             */
            $check = "SELECT `wholesaler_id`,`email`, `password` FROM `wholesaleuser` WHERE `email` = '" . $data['username'] . "' AND `password` = '" . md5($data['password']) . "'";

            $check_res = $this->_mysqli->query($check);
            if ($check_res->num_rows > 0) {
                /*
                 * Log them in 
                 */
                while ($row = $check_res->fetch_array(MYSQLI_ASSOC)) {

                    $check_status = "SELECT `status` FROM `wholeselerstatus` WHERE `wholesaler_id` = '" . $row['wholesaler_id'] . "' AND `status` = '1'";
                    $check_status_res = $this->_mysqli->query($check_status);
                    if ($check_status_res->num_rows > 0) {
                        //user is enabled log them in

                        $_SESSION['wholesaler_on'] = $row['wholesaler_id'];
                        $_SESSION['timeout'] = time();
                        $_SESSION['user_log'] = uniqid();
                        $user_session = $_SESSION['user_log'];
                        $insert_session = "UPDATE `frontend_users_status` SET `last_session` = '" . $user_session . "' WHERE `cust_id` = '" . $row['wholesaler_id'] . "'";
                        $insert_session_result = $this->_mysqli->query($insert_session);
                        $_SESSION['user_id'] = $row['wholesaler_id'];

                        $this->flag = 2;
                        $message = array("1" => "You are now logged in as a wholesaler and all the pricing that you will see are wholesale prices.");
                        array_push($this->message, $message);
                        $this->alert_class = "rock-success-message";
                    } else {
                        $this->flag = 2;
                        $message = array("1" => "Your account is not activated yet. For more information please contact us at <a href='mailto:" . CUSTOMER_EMAIL . "' style='color:#fff;'>" . CUSTOMER_EMAIL . "</a>.");
                        array_push($this->message, $message);
                        $this->alert_class = "rock-check-out-alert-warning";
                    }
                }
            } else {
                $this->flag = 2;
                $message = array("1" => "Authentication failed. You entered an incorrect username or password.");
                array_push($this->message, $message);
                $this->alert_class = "rock-check-out-alert-warning";
            }
        }
    }

    public function DoSignUpWholeSaler($data) {

        if (empty($data['new_w_fname']) && empty($data['new_w_last_name']) && empty($data['new_w_email']) && empty($data['new_w_conf_email']) && empty($data['new_w_phone']) && empty($data['new_w_add_1']) && empty($data['new_w_city']) && empty($data['new_w_state']) && empty($data['new_w_zip']) && empty($data['new_w_country']) && empty($data['new_w_tax_id']) && empty($data['new_w_password1']) && empty($data['new_w_password2'])) {
            $this->flag = 1;
            $message = array("1" => "All required fields are empty");
            array_push($this->message, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        } else if (empty($data['new_w_fname']) || empty($data['new_w_last_name']) || empty($data['new_w_email']) || empty($data['new_w_conf_email']) || empty($data['new_w_phone']) || empty($data['new_w_add_1']) || empty($data['new_w_city']) || empty($data['new_w_state']) || empty($data['new_w_zip']) || empty($data['new_w_country']) || empty($data['new_w_tax_id']) || empty($data['new_w_password1']) || empty($data['new_w_password2'])) {
            $this->flag = 1;
            $message = array("1" => "One or more of the required fields are empty.");
            array_push($this->message, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }
        /*
         * Check if user exists 
         */
        $check_user = "SELECT `email` FROM `wholesaleuser` WHERE `email` = '" . $data['new_w_email'] . "'";
        $check_user_res = $this->_mysqli->query($check_user);
        if ($check_user_res->num_rows > 0) {
            $this->flag = 1;
            $message = array("1" => "User is already registere!");
            array_push($this->message, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }
        if (trim($data['new_w_conf_email']) != trim($data['new_w_email'])) {
            $this->flag = 1;
            $message = array("1" => "Emails did not match. Please check again.");
            array_push($this->message, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }
        if (!filter_var($data['new_w_email'], FILTER_VALIDATE_EMAIL) || !filter_var($data['new_w_conf_email'], FILTER_VALIDATE_EMAIL)) {
            $this->flag = 1;
            $message = array("1" => "You have entered an invalid email address.");
            array_push($this->message, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }
        if ($data['new_w_password2'] != $data['new_w_password1']) {
            $this->flag = 1;
            $message = array("1" => "Passwords did not match. Please check again.");
            array_push($this->message, $message);
            $this->alert_class = "rock-check-out-alert-warning";
        }
        if (isset($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
            if (!$captcha) {
                $this->flag = 1;
                $message = array("1" => "Please check the captcha form.");
                array_push($this->message, $message);
                $this->alert_class = "rock-check-out-alert-warning";
            } else {

                $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . RE_CAPTCHA_SECRET_KEY . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
                if ($response['success'] === false) {
                    $this->flag = 1;
                    $message = array("1" => "This is a spammer.");
                    array_push($this->message, $message);
                    $this->alert_class = "rock-check-out-alert-warning";
                }
            }
        }
        if ($this->flag == 0) {
            $seller_id = uniqid();
            $date_added = date('m/d/y');
            $sql = "INSERT INTO  `wholesaleuser` ("
                    . "wholesaler_id, fname, lname, telephone, fax, email, address_1, address_2, city, state, zip_code, country, company_name, company_age, tax_id, website, password, date_added)"
                    . " VALUES "
                    . "("
                    . "'" . $seller_id . "', "
                    . "'" . $data['new_w_fname'] . "', "
                    . "'" . $data['new_w_last_name'] . "', "
                    . "'" . $data['new_w_phone'] . "', "
                    . "'" . $data['new_w_fax'] . "', "
                    . "'" . $data['new_w_email'] . "', "
                    . "'" . $data['new_w_add_1'] . "', "
                    . "'" . $data['new_w_add_2'] . "', "
                    . "'" . $data['new_w_city'] . "', "
                    . "'" . $data['new_w_state'] . "', "
                    . "'" . $data['new_w_zip'] . "', "
                    . "'" . $data['new_w_country'] . "', "
                    . "'" . $data['new_w_comp_name'] . "', "
                    . "'" . $data['new_w_com_age'] . "', "
                    . "'" . $data['new_w_tax_id'] . "', "
                    . "'" . $data['new_w_website'] . "', "
                    . "'" . md5($data['new_w_password1']) . "', "
                    . "'" . $date_added . "' "
                    . " )";

            $result = $this->_mysqli->query($sql);

            //*******************************************************************************************************************************************
            $insert_status = "INSERT INTO `wholeselerstatus` (wholesaler_id) VALUES ('" . $seller_id . "')";
            $insert_status_res = $this->_mysqli->query($insert_status);
            $insert_new_user = "INSERT INTO `frontend_users_desc` (cust_id, f_name, l_name) "
                    . "VALUES"
                    . " ("
                    . "'" . $seller_id . "', "
                    . "'" . $data['new_w_fname'] . "', "
                    . "'" . $data['new_w_last_name'] . "' "
                    . ")";
            $insert_new_user_res = $this->_mysqli->query($insert_new_user);

            $insert_new_user_email = "INSERT INTO `frontend_users_email` (cust_id, email, password) "
                    . "VALUES"
                    . "("
                    . "'" . $seller_id . "', "
                    . "'" . $data['new_w_email'] . "', "
                    . "'" . md5($data['new_w_password1']) . "'"
                    . ")";
            $insert_new_user_email_res = $this->_mysqli->query($insert_new_user_email);

            $insert_user_status = "INSERT INTO `frontend_users_status` (cust_id, last_login) "
                    . "VALUES"
                    . "("
                    . "'" . $seller_id . "', "
                    . "'" . $date_added . "'"
                    . ")";
            $insert_user_status_res = $this->_mysqli->query($insert_user_status);

            $insert_address = "INSERT INTO `frontend_users_address` (cust_id, address_1, address_2, city, zip_code, state, country)"
                    . " VALUES "
                    . "("
                    . "'" . $seller_id . "',"
                    . "'" . $data['new_w_add_1'] . "', "
                    . "'" . $data['new_w_add_2'] . "', "
                    . "'" . $data['new_w_city'] . "', "
                    . "'" . $data['new_w_zip'] . "', "
                    . "'" . $data['new_w_state'] . "', "
                    . "'" . $data['new_w_country'] . "' "
                    . ")";
//            var_dump($insert_address);
            $insert_address_res = $this->_mysqli->query($insert_address);

            $insert_phone_fax = "INSERT INTO `frontend_users_phone_fax` (cust_id, phone, fax) VALUES ('" . $seller_id . "', '" . $data['new_w_phone'] . "', '" . $data['new_w_fax'] . "')";
            $insert_phone_fax_res = $this->_mysqli->query($insert_phone_fax);
//************************************************************************************************************************************************







            if ($result) {
                $this->flag = 1;
                $message = array("1" => "Dear {$data['new_w_fname']}, thank you for signing up for wholesale account. "
                    . "We will review your account shortly and if approved,<br/>we'll enable your account. "
                    . "You should receive an email with in 2-4 business days.");
                array_push($this->message, $message);
                $this->alert_class = "rock-success-message";

                /*
                 * Send first emil to customer
                 */
                $email = trim($data['new_w_email']);
                $email_message = ""
                        . "<html>"
                        . "<head>"
                        . "<title>Wholeseller account request</title>"
                        . "</head>"
                        . "<body style='background-color:#000; color:#fff;'>"
                        . "<div style=' padding-right: 15px;padding-left: 15px; margin-right: auto; margin-left: auto; border:1px solid #000; border-radius:5px;'>"
                        . "<div style='margin-bottom:12px;'><img src='http://theline.growarock.com/rock_frontend/frontend_assets/images/theline_logo/logo_1.png'></div>"
                        . "<h4>Dear {$data['new_w_fname']},</h4>"
                        . "<br/>"
                        . "<p>Thank you for signing up for wholesale account.</p>"
                        . "<p>We will review your account shortly and if approved, we'll enable your account.</p>"
                        . "<p>You should receive an email with in 2-4 business days.</p>"
                        . "<br/>"
                        . "<p>Sincerely,</p>"
                        . "<p>" . CUSTOMER . "</p>"
                        . "<p>22704 Ventura blvd. #459, Woodland Hills CA 91364 US</p>"
                        . "<p>Telephone: (844) 440-0420</p>"
                        . "<p>E-mail: <a href='mailto:support@yellowbottles.com'>support@yellowbottles.com</a><p>"
                        . "<p>9:00am-5:00pm</p>"
                        . "</div>"
                        . "</body>"
                        . "</html>";
                $this->SendEmail($email, "Wholesale Account Request", $email_message, CUSTOMER_EMAIL);


                /*
                 * To customer (website owner)
                 */
                $question_message = ""
                        . "<html>"
                        . "<head>"
                        . "<title>Wholesale account request</title>"
                        . "<style>"
                        . "body{"
                        . "  "
                        . "}"
                        . ".table{"
                        . "font-size:9px;"
                        . "border:1px solid #000;"
                        . "}"
                        . ".table th{"
                        . "background-color:#F1F1F1;"
                        . "}"
                        . ".table td{"
                        . "padding:10px;"
                        . "}"
                        . "</style>"
                        . "</head>"
                        . "<body>"
                        . "<p>Hi There,</p>"
                        . "<br/>"
                        . "<p>{$data['new_w_fname']} has requested a wholesaler account.</p>"
                        . "<p>Information:</p>"
                        . "<table rules='all' style='border-color: #666;' cellpadding='10'>"
                        . "<tr style='background: #eee;'>"
                        . "<th>Name</th>"
                        . "<th>Email</th>"
                        . "<th>Phone#</th>"
                        . "<th>Address</th>"
                        . "<th>Company Name</th>"
                        . "<th>Tax ID</th>"
                        . "<th>Website</th>"
                        . "</tr>"
                        . "<tr>"
                        . "<td>{$data['new_w_fname']} {$data['new_w_last_name']}</td>"
                        . "<td>{$data['new_w_email']}</td>"
                        . "<td>{$data['new_w_phone']}</td>"
                        . "<td>{$data['new_w_add_1']} {$data['new_w_add_2']} {$data['new_w_city']}, {$data['new_w_state']} {$data['new_w_zip']}, {$data['new_w_country']}</td>"
                        . "<td>{$data['new_w_comp_name']}</td>"
                        . "<td>{$data['new_w_tax_id']}</td>"
                        . "<td>{$data['new_w_website']}</td>"
                        . "</tr>"
                        . "</table>"
                        . "<p>Sincerely,</p>"
                        . "<p>" . CUSTOMER . "</p>"
                        . "</body>"
                        . "</html>";

                $this->SendEmail(CUSTOMER_EMAIL, "A site visitor has a question", $question_message, $email);

                unset($_REQUEST);
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

}

$test = new WholeSaleRegLog();
$test->WholeSaleRegAndLoginForm();
