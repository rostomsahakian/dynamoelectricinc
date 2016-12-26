<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$GLOBALS['style'] = "";
function ProcessFrom($data) {
    if (empty($data['fname']) && empty($data['lname']) && empty($data['email']) && empty($data['numppl']) && $data['meal'] == "--") {
        $message = '<div class="col-md-12"><div class="alert alert-danger" style="background-color:#D92736; color:#fff; border-color:#D92736"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>All required fields are empty!</div></div>';
        echo $message;
    } else if (empty($data['fname']) || empty($data['lname']) || empty($data['email']) || empty($data['numppl']) || $data['meal'] == "--") {
        $message = '<div class="col-md-12"><div class="alert alert-danger" style="background-color:#D92736; color:#fff; border-color:#D92736"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>One or more required fields are empty!</div></div>';
        echo $message;
    } else if (!is_numeric($data['numppl'])) {
        $message = '<div class="col-md-12"><div class="alert alert-danger" style="background-color:#D92736; color:#fff; border-color:#D92736"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Please enter a numberic value for number of people attending with you!</div></div>';
        echo $message;
    } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) === true) {
        $message = '<div class="col-md-12"><div class="alert alert-danger" style="background-color:#D92736; color:#fff; border-color:#D92736" > <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Invalid email is provided!</div></div>';
        echo $message;
    } else {
        if ($data['attend'] == "1") {
            $data['attend'] = "yes";
        } else {
            $data['attend'] = "No";
        }
        /*
         * To customer (website owner)
         */
        $question_message = ""
                . "<html>"
                . "<head>"
                . "<title>RSVP Request response</title>"
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
                . "<p>{$data['fname']} has RSVP'd.</p>"
                . "<p>Information:</p>"
                . "<table rules='all' style='border-color: #666;' cellpadding='10'>"
                . "<tr style='background: #eee;'>"
                . "<th>Name</th>"
                . "<th>Email</th>"
                . "<th>Number of People Attending</th>"
                . "<th>Meal Option</th>"
                . "<th>Meal For additional guests</th>"
                . "<th>Attendance</th>"
                . "<th>Additional Info</th>"
                . "</tr>"
                . "<tr>"
                . "<td>{$data['fname']} {$data['lname']}</td>"
                . "<td>{$data['email']}</td>"
                . "<td>{$data['numppl']}</td>"
                . "<td>{$data['meal']}</td>"
                . "<td>{$data['guestmeal']}</td>"
                . "<td>{$data['attend']}</td>"
                . "<td>{$data['addinfo']}</td>"
                . "</tr>"
                . "</table>"
                . "<p>Sincerely,</p>"
                . "<p>" . CUSTOMER . "</p>"
                . "</body>"
                . "</html>";

        SendEmail(CUSTOMER_EMAIL, $data['fname'] . " has responded to requested RSVP", $question_message, $data['email']);
        $GLOBALS['style'] ="display:none;";
        $thank_you_message = '<div class="col-md-12" style="background-color:#fff; color:#000; min-height:500px;">
    <div class="col-md-6">
      <h1>Thank You!</h1>
      <h5>Dear '.$data['fname'].'</h5>
          <p>Thank you for RSVP.</p>
    </div>
</div>';
        echo $thank_you_message;
        unset($_REQUEST);
    }
}

function SendEmail($to, $mail_subject, $message, $from) {

    $subject = $mail_subject;
    $headers = "MIME-Version: 1.0 \r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
    $headers .= "From: $from \r\n";
    $headers .= "Reply-To: $to \r\n";
    mail($to, $subject, $message, $headers);
}
/*
 * If the submit button is clicked it will use the fucntion to process the form
 */
if (isset($_REQUEST['submit'])) {
    ProcessFrom($_REQUEST);
}
/*
 * Chaned your meal Option here follow the below example.
 */
$meals = array("Chicken", "Steak", "Pork");
?>

<form method="post">
    <div class="col-md-12" style="<?= $GLOBALS['style'] ?>">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>RSVP</h4>
            </div>
            <div class="panel-body">
                <!--Row 1 -->
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color:#000;">First Name:<span style="color:#9c3328">*</span></label>
                            <input type="text" name="fname" value="<?= isset($_REQUEST['fname']) ? $_REQUEST['fname'] : '' ?>" placeholder="your first name" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color:#000;">Last Name:<span style="color:#9c3328">*</span></label>
                            <input type="text" name="lname" value="<?= isset($_REQUEST['lname']) ? $_REQUEST['lname'] : '' ?>" placeholder="your last name" class="form-control"/>
                        </div>
                    </div>
                </div>
                <!--Row 2-->
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color:#000;">Email:<span style="color:#9c3328">*</span></label>
                            <input type="email" name="email" value="<?= isset($_REQUEST['email']) ? $_REQUEST['email'] : '' ?>" placeholder="your email address" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color:#000;">Number of People coming with you:<span style="color:#9c3328">*</span></label>
                            <input type="text" name="numppl" value="<?= isset($_REQUEST['numppl']) ? $_REQUEST['numppl'] : '' ?>" placeholder="How many people will be coming with you?" class="form-control"/>
                        </div>
                    </div>
                </div>
                <!--Row 3-->
                <div class="col-md-12">
                    <div class="col-md-6">
                        <label style="color:#000;">Will you be attending?<span style="color:#9c3328">*</span></label>
                        <?php
                        $attend_yes = "";
                        $attend_no = "";
                        if (isset($_REQUEST['attend'])) {
                            if ($_REQUEST['attend'] == "1") {
                                $attend_yes = 'checked="checked"';
                            } else {
                                $attend_yes = '';
                            }
                            if ($_REQUEST['attend'] == "0") {
                                $attend_no = 'checked="checked"';
                            } else {
                                $attend_no = '';
                            }
                        }
                        ?>
                        <div class="form-group" style="color:#000;">
                            <input type="radio" name="attend" value="1" <?= $attend_yes ?>/>
                            Absolutely
                        </div>
                        <div class="form-group" style="color:#000;">
                            <input type="radio" name="attend" value="0" <?= $attend_no ?>/>
                            Sorry I have a prior engagement
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label style="color:#000;">Meal Options<span style="color:#9c3328">*</span></label>
                        <select name="meal" class="form-control">
                            <option value="--" >--Select Your Meal--</option>
                            <?php
                            foreach ($meals as $meal) {
                                $selected = "";
                                if (isset($_REQUEST['meal']) && $_REQUEST['meal'] == $meal) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                ?>
                                <option value="<?= $meal ?>" <?= $selected ?>><?= $meal ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <!--ROW 4-->
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color:#000;">Please Enter your guest's meal options as well<span style="color:#000">&nbsp;(optional)</span></label>
                            <textarea class="form-control" name="guestmeal"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="color:#000;">Additional Information<span style="color:#000">&nbsp;(optional)</span></label>
                            <textarea class="form-control" name="addinfo"></textarea>    
                        </div>
                    </div>
                </div>
                <!--ROW 5-->
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="submit" name="submit" value="Submit RSVP" class="btn btn-success" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

