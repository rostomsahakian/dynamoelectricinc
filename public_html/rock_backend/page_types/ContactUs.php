<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContactUs
 *
 * @author rostom
 */
class ContactUs {

    private $_mysqli;
    private $_db;
    public $frontendlogic;
    public $flag = 0;
    public $messages = array();
    public $alert_class;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->frontendlogic = new FrontEndLogic();
    }

    public function ContactUspage($data) {
        if (isset($_REQUEST['send_message'])) {
            $this->DoProccessContactUsForm($_REQUEST);
        }
        ?>
        <div class="container rock-main-container">
            <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            <div class="row">
                <?php
                if ($this->frontendlogic->GetStoreInformation()) {
                    foreach ($this->frontendlogic->store_info as $store_info) {
                        ?>
                        <div class="col-md-7"><div class="rock-flexible-container">
                            <iframe src="<?= $store_info['google_maps'] ?>" width="480" height="480"></iframe>
                        </div>
                            </div>
                        <div class="col-md-4 <?= $this->alert_class ?>">
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
                            <fieldset>
                                <legend class="rock-contact-us-legend">Drop us a message.</legend>
                                <form method="post">
                                    <div class="form-group">
                                        <label><span style="color:#DA2431">*</span>Your Name:</label>
                                        <input type="text" name="name" value="<?= isset($_REQUEST['name']) ? $_REQUEST['name'] : '' ?>" class="form-control"/>
                                    </div>
                                    <div class="form-group">
                                        <label><span style="color:#DA2431">*</span>Email:</label>
                                        <input type="text" name="email" value="<?= isset($_REQUEST['email']) ? $_REQUEST['email'] : '' ?>" class="form-control disablecopypaste"/>
                                    </div>
                                    <div class="form-group">
                                        <label><span style="color:#DA2431">*</span>Re-Enter Email:</label>
                                        <input type="text" name="re_email" value="<?= isset($_REQUEST['re_email']) ? $_REQUEST['re_email'] : '' ?>" class="form-control disablecopypaste"/>
                                    </div>
                                    <div class="form-group">
                                        <label><span style="color:#DA2431">*</span>Message:</label>
                                        <textarea class="form-control rock-contact-textarea" name="message"><?= isset($_REQUEST['message']) ? $_REQUEST['message'] : '' ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="<?= RE_CAPTCH_SITE_KEY ?>"></div>

                                    </div>

                                    <div class="form-group">
                                        <input type="submit" value="Send" name="send_message" class="btn btn-danger"/>
                                    </div>
                                </form>
                            </fieldset>
                        </div>

                    </div>
                    <div class="col-md-12" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                        <div class="col-md-5">
                            <div itemscope itemtype="http://schema.org/Organization" >
                                <h2 itemprop="name"><?php
                                    if (defined('CUSTOMER')) {
                                        echo CUSTOMER;
                                    } else {
                                        echo 'Your Website name';
                                    }
                                    ?></h2>  
                            </div>

                            <h3 itemprop="telephone"><?= $store_info['phone1'] ?></h3>
                            <p itemprop="streetAddress"><?= $store_info['address_1'] . "" . $store_info['address_2'] ?> ,</p>
                            <p><span itemprop="addressLocality"><?= $store_info['city'] . " " . $store_info['state']; ?></span>&comma;&nbsp;<span itemprop="postalCode"><?= $store_info['zip'] ?></span>&nbsp;<span itemprop="addressLocality"><?= $store_info['country'] ?></span> </p>
                            <p class="rock-contact-p"><a href="mailto:<?= $store_info['email'] ?>" title="emial us" itemprop="email"><?= $store_info['email'] ?></a></p>

                        </div>
                        <div class="col-md-5">
                            <h3>Store Hours</h3>
                            <p><?= $store_info['store_hours'] ?></p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>

                <?php
            }
            ?>
        </div>
        <script>
            $(document).ready(function () {
                $('input.disablecopypaste').bind('copy paste', function (e) {
                    e.preventDefault();
                });
            });
        </script>

        <?php
    }

    public function DoProccessContactUsForm($data) {

        if (empty($data['name']) && empty($data['email']) && empty($data['re_email']) && empty($data['message'])) {
            $this->flag = 1;
            $message = array("1" => "All fields are empty! please enter required fields");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (empty($data['name']) || empty($data['email']) || empty($data['re_email']) || empty($data['message'])) {
            $this->flag = 1;
            $message = array("1" => "One or more fields are empty! please enter required fields");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->flag = 1;
            $message = array("1" => "Please enter a valid email address.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (!filter_var($data['re_email'], FILTER_VALIDATE_EMAIL)) {
            $this->flag = 1;
            $message = array("1" => "You have re-entered an invalid email address.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if ($data['re_email'] != $data['email']) {
            $this->flag = 1;
            $message = array("1" => "Emails did not match! please double check and try again.");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (isset($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
            if (!$captcha) {
                $this->flag = 1;
                $message = array("1" => "Please check the captcha form.");
                array_push($this->messages, $message);
                $this->alert_class = "rock-warning-message";
            } else {

                $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . RE_CAPTCHA_SECRET_KEY . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
                if ($response['success'] === false) {
                    $this->flag = 1;
                    $message = array("1" => "This is a spammer.");
                    array_push($this->messages, $message);
                    $this->alert_class = "rock-warning-message";
                } else {

                    $name = trim($data['name']);
                    $email = trim($data['email']);
                    $contact_message = trim($data['message']);
                    $date_added = date('m/d/y');

                    $sql = "INSERT INTO `contact_us_messages` (name, email, message, date_added) "
                            . "VALUES "
                            . "('" . $name . "', '" . $email . "', '" . $contact_message . "', '" . $date_added . "' )";
                    $result = $this->_mysqli->query($sql);
                    if ($result) {
                        $this->flag = 1;
                        $message = array("1" => "Dear {$name}, thank you for contacting us.<br/>We will contact you back shortly.");
                        array_push($this->messages, $message);
                        $this->alert_class = "rock-success-message";


                        /*
                         * Send first emil to customer
                         */
                        $email_message = ""
                                . "<html>"
                                . "<head>"
                                . "<title>Contact Us</title>"
                                . "<style>"
                                . "</style>"
                                . "</head>"
                                . "<body>"
                                . "<p>Dear {$name},</p>"
                                . "<br/>"
                                . "<p>Thank you for contacting us. We will contact you back shortly</p>"
                                . "<p><b>Original message:</b></p>"
                                . "<p>{$contact_message}</p>"
                                . "<br/>"
                                . "<p>Sincerely,</p>"
                                . "<p>" . CUSTOMER . "</p>"
                                . "</body>"
                                . "</html>";
                        $this->SendEmail($email, "Your message was sent", $email_message, CUSTOMER_EMAIL);

                        /*
                         * To customer (website owner)
                         */
                        $question_message = ""
                                . "<html>"
                                . "<head>"
                                . "<title>Contact Us</title>"
                                . "<style>"
                                . "</style>"
                                . "</head>"
                                . "<body>"
                                . "<p>Hi There,</p>"
                                . "<br/>"
                                . "<p>{$name} has contacted you from your website. Please respond back to him/her.</p>"
                                . "<p><b>{$name}'s Message: </b>:</p>"
                                . "<p>{$contact_message}</p>"
                                . "<br/>"
                                . "<p>you can contact him/her back via {$name}: {$email}</p>"
                                . "<p>Sincerely,</p>"
                                . "<p>" . CUSTOMER . "</p>"
                                . "</body>"
                                . "</html>";

                        $this->SendEmail(CUSTOMER_EMAIL, "A site visitor has contacted you", $question_message, $email);
                        unset($_REQUEST);
                    }
                }
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
