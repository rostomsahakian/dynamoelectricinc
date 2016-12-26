<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WholesalerManager
 *
 * @author rostom
 */
class WholesalerManager {

    private $_mysqli;
    private $_db;
    public $flag = 0;
    public $messages = array();
    public $alert_class;
    public $wholesalers;

    public function __construct() {

        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function WholesalerManagement() {
        if (isset($_REQUEST['status'])) {

            $this->ActivateWholesaler($_REQUEST['status'], $_REQUEST['id'], $_REQUEST['fid']);
        }
        ?>
        <div class="panel-heading">
            <h5><strong><i class="fa fa-shopping-bag" aria-hidden="true"></i>&nbsp;Wholesaler Manager</strong></h5>
        </div>
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
                <table class="table table-hover table-responsive table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Fax</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Company</th>
                        <th>Years in Business</th>
                        <th>Tax ID</th>
                        <th>Website</th>
                        <th>Date of Request</th>
                        <th>Status</th>
                    </tr>

                    <?php
                    if ($this->GetAllWholeSalers() != NULL) {
                        foreach ($this->wholesalers as $wholesalers) {
                            foreach ($wholesalers as $wholesaler) {
                                ?>
                                <tr>
                                    <td><?= $wholesaler['data']['fname'] . " " . $wholesaler['data']['lname'] ?></td>
                                    <td><?= $wholesaler['data']['telephone'] ?></td>
                                    <td><?= $wholesaler['data']['fax'] ?></td>
                                    <td><?= $wholesaler['data']['email'] ?></td>
                                    <td><?= $wholesaler['data']['address_1'] . " " . $wholesaler['data']['address_2'] . " " . $wholesaler['data']['city'] . ", " . $wholesaler['data']['state'] . " " . $wholesaler['data']['zip_code'] . " " . $wholesaler['data']['country'] ?></td>
                                    <td><?= $wholesaler['data']['company_name'] ?></td>
                                    <td><?= $wholesaler['data']['company_age'] ?></td>
                                    <td><?= $wholesaler['data']['tax_id'] ?></td>
                                    <td><?= $wholesaler['data']['company_name'] ?></td>
                                    <td><?= $wholesaler['data']['date_added'] ?></td>
                                    <?php
                                    if ($wholesaler['status'][0]['status']['status'] == "0" || $wholesaler['status'][0]['status']['status'] == 0) {
                                        $class = "danger";
                                        $status = "Inactive";
                                        $s = 1;
                                    } else {
                                        $class = "success";
                                        $status = "Active";
                                        $s = 0;
                                    }
                                    ?>
                                    <td><a href="?cmd=wholesaler-management&status=<?= $s ?>&id=<?= $wholesaler['data']['id'] ?>&fid=<?= $wholesaler['data']['wholesaler_id'] ?>" class="btn btn-<?= $class ?> btn-xs"><?= $status ?></a></td>

                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>

                </table>
            </div>
        </div>
        <?php
    }

    public function GetAllWholeSalers() {

        $sql = "SELECT * FROM `wholesaleuser`";
        $result = $this->_mysqli->query($sql);
        $all_data = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $data = array();
                $data['data'] = $row;
                $data['status'] = array();
                $get_status = "SELECT `status` FROM `wholeselerstatus` WHERE `wholesaler_id` = '" . $row['wholesaler_id'] . "'";
                $get_status_res = $this->_mysqli->query($get_status);
                while ($status = $get_status_res->fetch_array(MYSQLI_ASSOC)) {
                    $s = array();
                    $s['status'] = $status;
                    array_push($data['status'], $s);
                }

                array_push($all_data, $data);
            }

            $this->wholesalers[] = $all_data;
        }
        return $this->wholesalers;
    }

    public function ActivateWholesaler($s, $i, $uid) {

        $sql = "UPDATE `wholeselerstatus` SET `status` = '" . $s . "' WHERE `id` = '" . $i . "'";
        $result = $this->_mysqli->query($sql);

        $front_end = "UPDATE `frontend_users_status` SET `status` = '" . $s . "' WHERE `cust_id` = '" . $uid . "'";
        $result_frontend = $this->_mysqli->query($front_end);

        $get_user_info = "SELECT * FROM `frontend_users_email` WHERE `cust_id` = '" . $uid . "'";
        $get_user_info_res = $this->_mysqli->query($get_user_info);
        if ($get_user_info_res->num_rows > 0) {
            while ($row = $get_user_info_res->fetch_array(MYSQLI_ASSOC)) {
                if ($result_frontend && $s == "1") {

                    $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>wholeSaler account activated</title>
      <style type="text/css">
         /* Client-specific Styles */
         #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
         body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; background-color:#dbdbdb;}
         /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
         .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing. */
         #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
         img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
         a img {border:none;}
         .image_fix {display:block;}
         p {margin: 0px 0px !important;}
         table td {border-collapse: collapse;}
         table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
         a {color: #33b9ff;text-decoration: none;text-decoration:none!important;}
         /*STYLES*/
         table[class=full] { width: 100%; clear: both; }
         /*IPAD STYLES*/
         @media only screen and (max-width: 640px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 440px!important;text-align:center!important;}
         table[class=devicewidthmob] {width: 420px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
         img[class=banner] {width: 440px!important;height:157px!important;}
         img[class=col2img] {width: 440px!important;height:330px!important;}
         table[class="cols3inner"] {width: 100px!important;}
         table[class="col3img"] {width: 131px!important;}
         img[class="col3img"] {width: 131px!important;height: 82px!important;}
         table[class="removeMobile"]{width:10px!important;}
         img[class="blog"] {width: 420px!important;height: 162px!important;}
         }

         /*IPHONE STYLES*/
         @media only screen and (max-width: 480px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important; 
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 280px!important;text-align:center!important;}
         table[class=devicewidthmob] {width: 260px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
         img[class=banner] {width: 280px!important;height:100px!important;}
         img[class=col2img] {width: 280px!important;height:210px!important;}
         table[class="cols3inner"] {width: 260px!important;}
         img[class="col3img"] {width: 280px!important;height: 175px!important;}
         table[class="col3img"] {width: 280px!important;}
         img[class="blog"] {width: 260px!important;height: 100px!important;}
         td[class="padding-top-right15"]{padding:15px 15px 0 0 !important;}
         td[class="padding-right15"]{padding-right:15px !important;}
         }
		 .shipping_info td{
		 border:1px solid #000 !important;
		 padding:5px !important;
		  }
      </style>
   </head>
   <body>
  <!-- Start of preheader -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader" >
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 10px;color: #303030;text-align:center;" st-content="viewonline">
                                    If you can’t read this email.Please 
                                    <a href="#" style="text-decoration: none; color: #7a6e67">view online</a> 
                                 </td>
                                 <!-- Spacing -->
                              </tr>
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of preheader -->      

<!-- fulltext -->
<table width="100%"  cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="left-image">
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="520" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidthinner">
                                       <tbody>
    
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="15" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- /Spacing -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 18px; color: #2d2a26; text-align:left; line-height: 24px;">
                    Hi There,
                                             </td>
                                          </tr>
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="15" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- /Spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #7a6e67; text-align:left; line-height: 24px;">
                                              We have revised your account and have activated your wholesaler account. Please visit <a href="http://theline.growarock.com/wholesale-account-signup-login">Our Site</a> and login with your credentials.
                                              <br/>
                                              Sincerely,<br/>
                                              ' . CUSTOMER . '
                                              
                                             </td>
                                          </tr>

                                          <!-- end of content -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <!-- Spacing -->
                              <tr>
                                 <td height="5"  style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of fulltext -->
<!-- Start of footer -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="footer">
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#00000" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <!-- logo -->
                                    <table width="194" align="left" border="0" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td width="20"></td>
                                             <td width="174" height="40" align="left">
                                                <div class="imgpop">
                                                   <a target="_blank" href="<[customer website]>">
												   <!--Logo off the customer-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/theline_logo/logo_1.png" alt="" border="0" width="80%" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of logo -->
                                    <!-- start of social icons -->
                                    <table width="60" height="40" align="right" vaalign="middle"  border="0" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td width="22" height="22" align="left">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
												   <!--Facebook logo-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/social_media/faceboo_color.png" alt="" border="0" width="22" height="22" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                             <td align="left" width="10" style="font-size:1px; line-height:1px;">&nbsp;</td>
                                             <td width="22" height="22" align="right">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
												   <!-- Twitter-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/social_media/twitter_color.png" alt="" border="0" width="22" height="22" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>

                                             <td align="left" width="20" style="font-size:1px; line-height:1px;">&nbsp;</td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of social icons -->
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of footer -->
<!-- Start of postfooter -->
<table width="100%" bgcolor="#dbdbdb" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader" >
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #7a6e67;text-align:center;" st-content="viewonline">
                                    If you wish not to receive further updates.Please 
                                    <a href="#" style="text-decoration: none; color: #303030">Unsubscribe</a> 
                                 </td>
                              </tr>
                                 <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of postfooter -->

   </body>
   </html>
';
                    $this->SendEmailToCustomers($row['email'], CUSTOMER . " - Wholesale account activated.", $message, CUSTOMER_EMAIL);
                } else {

                    $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>wholeSaler account deactivated</title>
      <style type="text/css">
         /* Client-specific Styles */
         #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
         body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; background-color:#dbdbdb;}
         /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
         .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing. */
         #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
         img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
         a img {border:none;}
         .image_fix {display:block;}
         p {margin: 0px 0px !important;}
         table td {border-collapse: collapse;}
         table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
         a {color: #33b9ff;text-decoration: none;text-decoration:none!important;}
         /*STYLES*/
         table[class=full] { width: 100%; clear: both; }
         /*IPAD STYLES*/
         @media only screen and (max-width: 640px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 440px!important;text-align:center!important;}
         table[class=devicewidthmob] {width: 420px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
         img[class=banner] {width: 440px!important;height:157px!important;}
         img[class=col2img] {width: 440px!important;height:330px!important;}
         table[class="cols3inner"] {width: 100px!important;}
         table[class="col3img"] {width: 131px!important;}
         img[class="col3img"] {width: 131px!important;height: 82px!important;}
         table[class="removeMobile"]{width:10px!important;}
         img[class="blog"] {width: 420px!important;height: 162px!important;}
         }

         /*IPHONE STYLES*/
         @media only screen and (max-width: 480px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important; 
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 280px!important;text-align:center!important;}
         table[class=devicewidthmob] {width: 260px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
         img[class=banner] {width: 280px!important;height:100px!important;}
         img[class=col2img] {width: 280px!important;height:210px!important;}
         table[class="cols3inner"] {width: 260px!important;}
         img[class="col3img"] {width: 280px!important;height: 175px!important;}
         table[class="col3img"] {width: 280px!important;}
         img[class="blog"] {width: 260px!important;height: 100px!important;}
         td[class="padding-top-right15"]{padding:15px 15px 0 0 !important;}
         td[class="padding-right15"]{padding-right:15px !important;}
         }
		 .shipping_info td{
		 border:1px solid #000 !important;
		 padding:5px !important;
		  }
      </style>
   </head>
   <body>
  <!-- Start of preheader -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader" >
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 10px;color: #303030;text-align:center;" st-content="viewonline">
                                    If you can’t read this email.Please 
                                    <a href="#" style="text-decoration: none; color: #7a6e67">view online</a> 
                                 </td>
                                 <!-- Spacing -->
                              </tr>
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of preheader -->      

<!-- fulltext -->
<table width="100%"  cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="left-image">
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="520" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidthinner">
                                       <tbody>
    
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="15" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- /Spacing -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 18px; color: #2d2a26; text-align:left; line-height: 24px;">
                    Hi There,
                                             </td>
                                          </tr>
                                          <!-- Spacing -->
                                          <tr>
                                             <td width="100%" height="15" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- /Spacing -->
                                          <!-- content -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #7a6e67; text-align:left; line-height: 24px;">
We have deactivated your account temporarily. Please contact our customer service for futher information. 
<br/>
Customer service phone#:  (844) 440-0420
<br/>
Customer service email: <a href="mailto:' . CUSTOMER_EMAIL . '">' . CUSTOMER_EMAIL . '</a>
<br/>
                                              Sincerely,<br/>
                                              ' . CUSTOMER . '
                                              <br/>
                                              '.CUSTOMER_EMAIL.'
                                              
                                             </td>
                                          </tr>

                                          <!-- end of content -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <!-- Spacing -->
                              <tr>
                                 <td height="5"  style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of fulltext -->
<!-- Start of footer -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="footer">
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#00000" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <!-- logo -->
                                    <table width="194" align="left" border="0" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td width="20"></td>
                                             <td width="174" height="40" align="left">
                                                <div class="imgpop">
                                                   <a target="_blank" href="<[customer website]>">
												   <!--Logo off the customer-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/theline_logo/logo_1.png" alt="" border="0" width="80%" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of logo -->
                                    <!-- start of social icons -->
                                    <table width="60" height="40" align="right" vaalign="middle"  border="0" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td width="22" height="22" align="left">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
												   <!--Facebook logo-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/social_media/faceboo_color.png" alt="" border="0" width="22" height="22" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>
                                             <td align="left" width="10" style="font-size:1px; line-height:1px;">&nbsp;</td>
                                             <td width="22" height="22" align="right">
                                                <div class="imgpop">
                                                   <a target="_blank" href="#">
												   <!-- Twitter-->
                                                   <img src="http://theline.growarock.com/rock_frontend/frontend_assets/images/social_media/twitter_color.png" alt="" border="0" width="22" height="22" style="display:block; border:none; outline:none; text-decoration:none;">
                                                   </a>
                                                </div>
                                             </td>

                                             <td align="left" width="20" style="font-size:1px; line-height:1px;">&nbsp;</td>
                                          </tr>
                                       </tbody>
                                    </table>
                                    <!-- end of social icons -->
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="10" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of footer -->
<!-- Start of postfooter -->
<table width="100%" bgcolor="#dbdbdb" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader" >
   <tbody>
      <tr>
         <td>
            <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table bgcolor="#ffffff" width="560" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #7a6e67;text-align:center;" st-content="viewonline">
                                    If you wish not to receive further updates.Please 
                                    <a href="#" style="text-decoration: none; color: #303030">Unsubscribe</a> 
                                 </td>
                              </tr>
                                 <!-- Spacing -->
                              <tr>
                                 <td width="100%" height="10"></td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of postfooter -->

   </body>
   </html>
';
                    $this->SendEmailToCustomers($row['email'], CUSTOMER . " - Wholesale account deactivated.", $message, CUSTOMER_EMAIL);
                }
            }
        }
    }

    public function SendEmailToCustomers($to, $mail_subject, $message, $from) {
        $subject = $mail_subject;
        $headers = "MIME-Version: 1.0 \r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
        $headers .= "From: $from \r\n";
        $headers .= "Reply-To: $to \r\n";
        mail($to, $subject, $message, $headers);
    }

}
