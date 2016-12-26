<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FAQs
 *
 * @author rostom
 */
class FAQs {

    private $_mysqli;
    private $_db;
    public $flag = 0;
    public $alert_class = "";
    public $messages = array();

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function FaqPage($data) {
        if (isset($_REQUEST['ask'])) {
            $this->DoProccessFaqForm($_REQUEST);
        }
        ?>

        <div class="container rock-main-container">

            <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            <div class="row">
                <?= $data['page_content'] ?>
                <div class="col-md-7 <?= $this->alert_class ?>">
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
                <div class="col-md-12">
                    <div class="col-md-7">
                        <fieldset>
                            <legend style="color:#B8202A;">Ask Us a Question.</legend>
                            <form method="post">
                                <div class="form-group">
                                    <label><span style="color:#DA2431">*</span>Your Name:</label>
                                    <input type="text" name="name" value="<?= isset($_REQUEST['name']) ? $_REQUEST['name'] : '' ?>" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label><span style="color:#DA2431">*</span>Your Email:</label>
                                    <input type="email" name="email" value="<?= isset($_REQUEST['email']) ? $_REQUEST['email'] : '' ?>" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label><span style="color:#DA2431">*</span>Your Question:</label>
                                    <textarea name="question" class="form-control" style="height: 265px;"><?= isset($_REQUEST['question']) ? $_REQUEST['question'] : '' ?></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="<?= RE_CAPTCH_SITE_KEY ?>"></div>

                                </div>
                                <div class="form-group">
                                    <input type="submit" name="ask" value="Send Your Question" class="btn btn-danger"/>
                                </div>
                            </form>  
                        </fieldset>

                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function DoProccessFaqForm($data) {

        if (empty($data['name']) && empty($data['email']) && empty($data['question'])) {
            $this->flag = 1;
            $message = array("1" => "All fields are empty! please enter required fields");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (empty($data['name']) || empty($data['email']) || empty($data['question'])) {
            $this->flag = 1;
            $message = array("1" => "One or more fields are empty! please enter required fields");
            array_push($this->messages, $message);
            $this->alert_class = "rock-warning-message";
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->flag = 1;
            $message = array("1" => "Please enter a valid email address.");
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
                    $contact_message = trim($data['question']);
                    $date_added = date('m/d/y');

                    $sql = "INSERT INTO `faqs` (name, email, message, date_added) "
                            . "VALUES "
                            . "('" . $name . "', '" . $email . "', '" . addslashes($contact_message) . "', '" . $date_added . "' )";
                    $result = $this->_mysqli->query($sql);
                    if ($result) {
                        $this->flag = 1;
                        $message = array("1" => "Dear {$name}, thank you for contacting us. We will contact you back shortly.");
                        array_push($this->messages, $message);
                        $this->alert_class = "rock-success-message";


                        /*
                         * Send first emil to customer
                         */
                        $email_message = ""
                                . "<html>"
                                . "<head>"
                                . "<title>FAQ</title>"
                                . "<style>"
                                . ".container {
                                        padding-right: 15px;
                                        padding-left: 15px;
                                        margin-right: auto;
                                        margin-left: auto;
                                    }"
                                . "</style>"
                                . "</head>"
                                . "<body>"
                                . "<div class='container'>"
                                . "<p>Dear {$name},</p>"
                                . "<br/>"
                                . "<p>Thank you for asking us the question. We will contact you very soon with our answers.</p>"
                                . "<p>your question:</p>"
                                . "<p>{$contact_message}</p>"
                                . "<br/>"
                                . "<p>Sincerely,</p>"
                                . "<p>" . CUSTOMER . "</p>"
                                . "</div>"
                                . "</body>"
                                . "</html>";
                        $this->SendEmail($email, "Question for The Line", $email_message, CUSTOMER_EMAIL);

                        /*
                         * To customer (website owner)
                         */
                        $question_message = ""
                                . "<html>"
                                . "<head>"
                                . "<title>FAQ</title>"
                                . "<style>"
                                . "</style>"
                                . "</head>"
                                . "<body>"
                                . "<p>Hi There,</p>"
                                . "<br/>"
                                . "<p>{$name} has the following question for you;</p>"
                                . "<p>question:</p>"
                                . "<p>{$contact_message}</p>"
                                . "<br/>"
                                . "<p>you can contacting him/her back via {$name}: {$email}</p>"
                                . "<p>Sincerely,</p>"
                                . "<p>" . CUSTOMER . "</p>"
                                . "</body>"
                                . "</html>";

                        $this->SendEmail(CUSTOMER_EMAIL, "A site visitor has a question", $question_message, $email);
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
