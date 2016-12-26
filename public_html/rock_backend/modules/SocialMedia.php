<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SocialMedia
 *
 * @author rostom
 */
class SocialMedia {

    public $_flag = 0;
    public $_queries;
    public $error_message = array();
    public $_message;
    private $_mysqli;
    private $_db;
    public $_social_media;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->_queries = new Queries();
    }

    public function SocialMediaManager() {
        ?>
        <div class="panel-heading">
            <h5><strong><i class="fa fa-facebook-square" aria-hidden="true"></i>&nbsp;Social Media</strong></h5>

        </div>
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
                <?php
                $this->GetAllSocialMediadata();
                $this->SocialMediaForm($this->_social_media);
                ?>
            </div>
        </div>
        <?php
    }

    /*
     * Social Media form
     * 1.Facebook
     * 2.twitter
     * 3.Instagram
     * 4.Youtube
     * 5.Linkedin
     * 6.Yelp
     * 7.google+
     * 
     */

    public function SocialMediaForm(array $social_media = NULL) {

        if (isset($_REQUEST['form']['social']['set_icons'])) {

            $facebook_url = (isset($_REQUEST['form']['social']['facebook']) ? (empty($_REQUEST['form']['social']['facebook']) ? 'https://www.facebook.com/' : $_REQUEST['form']['social']['facebook']) : 'https://www.facebook.com/');
            $facebook_icon = (isset($_REQUEST['form']['social']['facebook_icons']) ? $_REQUEST['form']['social']['facebook_icons'] : 'faceboo_color.png');
            $facebook_status = $_REQUEST['form']['social']['facebook_status'];


            $twitter_url = (isset($_REQUEST['form']['social']['twitter']) ? (empty($_REQUEST['form']['social']['twitter']) ? 'https://twitter.com/' : $_REQUEST['form']['social']['twitter']) : 'https://twitter.com/');
            $twitter_icon = (isset($_REQUEST['form']['social']['twitter_icons']) ? $_REQUEST['form']['social']['twitter_icons'] : 'twitter_color.png');
            $twitter_status = $_REQUEST['form']['social']['twitter_status'];


            $instagram_url = (isset($_REQUEST['form']['social']['instagram']) ? (empty($_REQUEST['form']['social']['instagram']) ? 'https://www.instagram.com/' : $_REQUEST['form']['social']['instagram']) : 'https://www.instagram.com/');
            $instagram_icon = (isset($_REQUEST['form']['social']['instagram_icons']) ? $_REQUEST['form']['social']['instagram_icons'] : 'instagram_color.png');
            $instagram_status = $_REQUEST['form']['social']['instagram_status'];

            $youtube_url = (isset($_REQUEST['form']['social']['youtube']) ? (empty($_REQUEST['form']['social']['youtube']) ? 'https://www.youtube.com/' : $_REQUEST['form']['social']['youtube']) : 'https://www.youtube.com/');
            $youtube_icon = (isset($_REQUEST['form']['social']['youtube_icons']) ? $_REQUEST['form']['social']['youtube_icons'] : 'youtube_color.png');
            $youtube_status = $_REQUEST['form']['social']['youtube_status'];

            $linkedin_url = (isset($_REQUEST['form']['social']['linkedin']) ? (empty($_REQUEST['form']['social']['linkedin']) ? 'https://www.linkedin.com/' : $_REQUEST['form']['social']['linkedin']) : 'https://www.linkedin.com/');
            $linkedin_icon = (isset($_REQUEST['form']['social']['linkedin_icons']) ? $_REQUEST['form']['social']['linkedin_icons'] : 'linkedin_color.png');
            $linkedin_status = $_REQUEST['form']['social']['linkedin_status'];

            $google_plus_url = (isset($_REQUEST['form']['social']['google']) ? (empty($_REQUEST['form']['social']['google']) ? 'https://plus.google.com' : $_REQUEST['form']['social']['google']) : 'https://plus.google.com');
            $google_plus_icon = (isset($_REQUEST['form']['social']['google_icons']) ? $_REQUEST['form']['social']['google_icons'] : 'google_plus_color.png');
            $google_status = $_REQUEST['form']['social']['google_status'];





            $table = "social_media";

            $image_path = "/".F_ASSETS."images/social_media/";
            $table = array("table1" => "social_media");
            $columns = array("`url`", "`image_url`", "`image_name`", "`status`");
            $facebook = array("'" . $facebook_url . "'", "'" . $image_path . "'", "'" . $facebook_icon . "'", "'" . $facebook_status . "'");
            $twitter = array("'" . $twitter_url . "'", "'" . $image_path . "'", "'" . $twitter_icon . "'", "'" . $twitter_status . "'");
            $instagram = array("'" . $instagram_url . "'", "'" . $image_path . "'", "'" . $instagram_icon . "'", "'" . $instagram_status . "'");
            $youtube = array("'" . $youtube_url . "'", "'" . $image_path . "'", "'" . $youtube_icon . "'", "'" . $youtube_status . "'");
            $linkedin = array("'" . $linkedin_url . "'", "'" . $image_path . "'", "'" . $linkedin_icon . "'", "'" . $linkedin_status . "'");
            $google_plus = array("'" . $google_plus_url . "'", "'" . $image_path . "'", "'" . $google_plus_icon . "'", "'" . $linkedin_status . "'");

            $values_to_insert = array(
                "tables" => $table,
                "columns" => $columns,
                "values" => array(
                    $facebook,
                    $twitter,
                    $instagram,
                    $youtube,
                    $linkedin,
                    $google_plus
                )
            );
            /*
             * First check see if the table has data
             */



            $check_db_for_social_media = $this->_queries->GetData("social_media", "image_url", $image_path, $option = "6");
            if ($check_db_for_social_media['row_count'] > 0) {
                
                /*
                 * if yes->UPDATE
                 */

                $facebook = array("'" . $facebook_url . "',", "'" . $image_path . "',", "'" . $facebook_icon . "',", "'" . $facebook_status . "'");
                $twitter = array("'" . $twitter_url . "',", "'" . $image_path . "',", "'" . $twitter_icon . "',", "'" . $twitter_status . "'");
                $instagram = array("'" . $instagram_url . "',", "'" . $image_path . "',", "'" . $instagram_icon . "',", "'" . $instagram_status . "'");
                $youtube = array("'" . $youtube_url . "',", "'" . $image_path . "',", "'" . $youtube_icon . "',", "'" . $youtube_status . "'");
                $linkedin = array("'" . $linkedin_url . "',", "'" . $image_path . "',", "'" . $linkedin_icon . "', ", "'" . $linkedin_status . "'");
                $google_plus = array("'" . $google_plus_url . "',", "'" . $image_path . "',", "'" . $google_plus_icon . "',", "'" . $google_status . "'");
                $cols = array("url", "image_url", "image_name", "status");
               
                for ($i = 0; $i < count($social_media); $i++) {
                    $values_to_update = array(
                        "table" => "social_media",
                        "fields" => $cols,
                        "values" => array(
                            $facebook,
                            $twitter,
                            $instagram,
                            $youtube,
                            $linkedin,
                            $google_plus,
                        ),
                        "field2" => "id",
                        "value2" => $social_media
                    );
                }

                $do_update_social_media = $this->_queries->UpdateQueriesServices($values_to_update, $option = "4");

                $flag = 1;
                $message = array("message" => "Social Media Updated.");
                array($this->_message, $message);
                $this->ReturnMessages($message, $flag);
            } else {

                $flag = 1;
                $message = array("message" => "Social Media Inserted.");
                array($this->_message, $message);
                $this->ReturnMessages($message, $flag);

                /*
                 * else INSERT
                 */


                $do_insert_soecial_media = $this->_queries->Insertvalues($values_to_insert, $option = "2");
            }
        }
        ?>
        <div class="col-md-12">
            <div class="col-md-12" style="margin-top: 10px !important;">
                <?php
                if ($this->_flag == 1) {
                    ?>
                    <div class="list-group">
                        <ul>
                            <?php
                            foreach ($this->error_message as $message) {

                                echo "<li class='list-group-item list-group-item-warning'><i class='glyphicon glyphicon-info-sign'></i>&nbsp;" . $message . "</li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <form method="post" name="form[social]">    

                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-cogs"></i>&nbsp;<span>Social Media Manager</span></div>
                    <div class="panel-body">
                        <!--Social Media Form enter-->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fa fa-facebook-square"></i>&nbsp;Facebook</label>
                                    <input type="text" name="form[social][facebook]" value="<?= (isset($_REQUEST['form']['social']['facebook']) ? $_REQUEST['form']['social']['facebook'] : $social_media[0]['url']) ?>" class="form-control"  placeholder="http://url of socail media" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <table class="table table-hover table-bordered" style="margin-top:21px">
                                        <!--LOGIC-->
                                        <?php
                                        $checked_1 = '';
                                        $checked_2 = '';
                                        $checked_3 = '';
                                        $selection = ((isset($_REQUEST['form']['social']['facebook_icons']) ? $_REQUEST['form']['social']['facebook_icons'] : $social_media[0]['image_name']));
                                        if ($selection == "faceboo_color.png") {
                                            $checked_1 = 'checked="checked"';
                                            $checked_2 = '';
                                            $checked_3 = '';
                                        } else if ($selection == "facebook.png") {
                                            $checked_1 = '';
                                            $checked_2 = 'checked="checked"';
                                            $checked_3 = '';
                                        } else {
                                            $checked_1 = '';
                                            $checked_2 = '';
                                            $checked_3 = 'checked="checked"';
                                        }
                                        ?>
                                        <tr>
                                            <td style="width:100px;">                                               
                                                <img src="/<?= F_ASSETS ?>images/social_media/faceboo_color.png" alt="" width="25" height="25"/>                                          
                                                <input type="radio" name="form[social][facebook_icons]" value="faceboo_color.png" <?= $checked_1 ?>  />                                                
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/facebook.png" alt="" width="25" height="25"/>
                                                <input type="radio" name="form[social][facebook_icons]" value="facebook.png" <?= $checked_2 ?> />
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/facebook_circle.png" alt="" width="25" height="25"/> 
                                                <input type="radio" name="form[social][facebook_icons]" value="facebook_circle.png" <?= $checked_3 ?>  />
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:30px">
                                    <label>Status</label>
                                    <?php
                                    $selection = (isset($_REQUEST['form']['social']['facebook_status']) ? $_REQUEST['form']['social']['facebook_status'] : $social_media[0]['status'] );
                                    if ($selection == 1) {
                                        $checked_1 = 'checked="checked"';
                                        $checked_2 = '';
                                    } else {
                                        $checked_1 = '';
                                    }
                                    ?>
                                    <input type="hidden"  name="form[social][facebook_status]" value="0" />
                                    <input type="checkbox"  name="form[social][facebook_status]" value="1" <?= $checked_1 ?>/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fa fa-twitter-square"></i>&nbsp;Twitter</label>
                                    <input type="text" name="form[social][twitter]" value="<?= (isset($_REQUEST['form']['social']['twitter']) ? $_REQUEST['form']['social']['twitter'] : $social_media[1]['url']) ?>" class="form-control"  placeholder="http://url of socail media" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <table class="table table-hover table-bordered" style="margin-top:21px">
                                        <!--LOGIC-->
                                        <?php
                                        $checked_1 = '';
                                        $checked_2 = '';
                                        $checked_3 = '';
                                        $selection = ((isset($_REQUEST['form']['social']['twitter_icons']) ? $_REQUEST['form']['social']['twitter_icons'] : $social_media[1]['image_name']));
                                        if ($selection == "twitter_color.png") {
                                            $checked_1 = 'checked="checked"';
                                            $checked_2 = '';
                                            $checked_3 = '';
                                        } else if ($selection == "twitter.png") {
                                            $checked_1 = '';
                                            $checked_2 = 'checked="checked"';
                                            $checked_3 = '';
                                        } else {
                                            $checked_1 = '';
                                            $checked_2 = '';
                                            $checked_3 = 'checked="checked"';
                                        }
                                        ?>
                                        <tr>
                                            <td style="width:100px;">                                               
                                                <img src="/<?= F_ASSETS ?>images/social_media/twitter_color.png" alt="" width="25" height="25"/>
                                                <input type="radio" name="form[social][twitter_icons]" value="twitter_color.png" <?= $checked_1 ?>  />                                                
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/twitter.png" alt="" width="25" height="25"/>
                                                <input type="radio" name="form[social][twitter_icons]" value="twitter.png" <?= $checked_2 ?>   />
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/twitter_circle.png" alt="" width="25" height="25"/> 
                                                <input type="radio" name="form[social][twitter_icons]" value="twitter_circle.png" <?= $checked_3 ?>  />
                                            </td>
                                        </tr>



                                    </table>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:30px">
                                    <label>Status</label>
                                    <?php
                                    $checked_1 = '';

                                    $selection = (isset($_REQUEST['form']['social']['twitter_status']) ? $_REQUEST['form']['social']['twitter_status'] : $social_media[1]['status'] );
                                    if ($selection == 1) {
                                        $checked_1 = 'checked="checked"';
                                    } else {
                                        $checked_1 = '';
                                    }
                                    ?>
                                    <input type="hidden"  name="form[social][twitter_status]" value="0"/>
                                    <input type="checkbox"  name="form[social][twitter_status]" value="1" <?= $checked_1 ?>/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fa fa-instagram"></i>&nbsp;Instagram</label>
                                    <input type="text" name="form[social][instagram]" value="<?= (isset($_REQUEST['form']['social']['instagram']) ? $_REQUEST['form']['social']['instagram'] : $social_media[2]['url']) ?>" class="form-control"  placeholder="http://url of socail media" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <table class="table table-hover table-bordered" style="margin-top:21px">
                                        <!--LOGIC-->
                                        <?php
                                        $checked_1 = '';
                                        $checked_2 = '';
                                        $checked_3 = '';
                                        $selection = ((isset($_REQUEST['form']['social']['instagram_icons']) ? $_REQUEST['form']['social']['instagram_icons'] : $social_media[2]['image_name']));
                                        if ($selection == "instagram_color.png") {
                                            $checked_1 = 'checked="checked"';
                                            $checked_2 = '';
                                            $checked_3 = '';
                                        } else if ($selection == "instagram.png") {
                                            $checked_1 = '';
                                            $checked_2 = 'checked="checked"';
                                            $checked_3 = '';
                                        } else {
                                            $checked_1 = '';
                                            $checked_2 = '';
                                            $checked_3 = 'checked="checked"';
                                        }
                                        ?>
                                        <tr>
                                            <td style="width:100px;">                                               
                                                <img src="/<?= F_ASSETS ?>images/social_media/instagram_color.png" alt="" width="25" height="25"/>                                          
                                                <input type="radio" name="form[social][instagram_icons]" value="instagram_color.png" <?= $checked_1 ?>  />                                                
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/instagram.png" alt="" width="25" height="25"/>
                                                <input type="radio" name="form[social][instagram_icons]" value="instagram.png" <?= $checked_2 ?>  />
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/instagram_circle.png" alt="" width="25" height="25"/> 
                                                <input type="radio" name="form[social][instagram_icons]" value="instagram_circle.png" <?= $checked_3 ?>   />
                                            </td>
                                        </tr>



                                    </table>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:30px">
                                    <label>Status</label>
                                    <?php
                                    $checked_1 = '';

                                    $selection = (isset($_REQUEST['form']['social']['instagram_status']) ? $_REQUEST['form']['social']['instagram_status'] : $social_media[2]['status'] );
                                    if ($selection == 1) {
                                        $checked_1 = 'checked="checked"';
                                    } else {
                                        $checked_1 = '';
                                    }
                                    ?>
                                    <input type="hidden"  name="form[social][instagram_status]" value="0" />
                                    <input type="checkbox"  name="form[social][instagram_status]" value="1" <?= $checked_1 ?>/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fa fa-youtube-square"></i>&nbsp;YouTube</label>
                                    <input type="text" name="form[social][youtube]" value="<?= (isset($_REQUEST['form']['social']['youtube']) ? $_REQUEST['form']['social']['youtube'] : $social_media[3]['url']) ?>" class="form-control"  placeholder="http://url of socail media" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <table class="table table-hover table-bordered" style="margin-top:21px">
                                        <!--LOGIC-->
                                        <?php
                                        $checked_1 = '';
                                        $checked_2 = '';
                                        $checked_3 = '';
                                        $selection = ((isset($_REQUEST['form']['social']['youtube_icons']) ? $_REQUEST['form']['social']['youtube_icons'] : $social_media[3]['image_name']));
                                        if ($selection == "youtube_color.png") {
                                            $checked_1 = 'checked="checked"';
                                            $checked_2 = '';
                                            $checked_3 = '';
                                        } else if ($selection == "youtube.png") {
                                            $checked_1 = '';
                                            $checked_2 = 'checked="checked"';
                                            $checked_3 = '';
                                        } else {
                                            $checked_1 = '';
                                            $checked_2 = '';
                                            $checked_3 = 'checked="checked"';
                                        }
                                        ?>
                                        <tr>
                                            <td style="width:100px;">                                               
                                                <img src="/<?= F_ASSETS ?>images/social_media/youtube_color.png" alt="" width="25" height="25"/>                                          
                                                <input type="radio" name="form[social][youtube_icons]" value="youtube_color.png"  <?= $checked_1 ?> />                                                
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/youtube.png" alt="" width="25" height="25"/>
                                                <input type="radio" name="form[social][youtube_icons]" value="youtube.png" <?= $checked_2 ?>   />
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/youtube_circle.png" alt="" width="25" height="25"/> 
                                                <input type="radio" name="form[social][youtube_icons]" value="youtube_circle.png"  <?= $checked_3 ?>  />
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:30px">
                                    <label>Status</label>
                                    <?php
                                    $checked_1 = '';

                                    $selection = (isset($_REQUEST['form']['social']['youtube_status']) ? $_REQUEST['form']['social']['youtube_status'] : $social_media[3]['status'] );
                                    if ($selection == 1) {
                                        $checked_1 = 'checked="checked"';
                                    } else {
                                        $checked_1 = '';
                                    }
                                    ?>
                                    <input type="hidden"  name="form[social][youtube_status]" value="0" />
                                    <input type="checkbox"  name="form[social][youtube_status]" value="1" <?= $checked_1 ?>/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fa fa-linkedin-square"></i>&nbsp;LinkedIn</label>
                                    <input type="text" name="form[social][linkedin]" value="<?= (isset($_REQUEST['form']['social']['linkedin']) ? $_REQUEST['form']['social']['linkedin'] : $social_media[4]['url']) ?>" class="form-control"  placeholder="http://url of socail media" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <table class="table table-hover table-bordered" style="margin-top:21px">
                                        <!--LOGIC-->
                                        <?php
                                        $checked_1 = '';
                                        $checked_2 = '';
                                        $checked_3 = '';
                                        $selection = ((isset($_REQUEST['form']['social']['linkedin_icons']) ? $_REQUEST['form']['social']['linkedin_icons'] : $social_media[4]['image_name']));
                                        if ($selection == "linkedin_color.png") {
                                            $checked_1 = 'checked="checked"';
                                            $checked_2 = '';
                                            $checked_3 = '';
                                        } else if ($selection == "linedin.png") {
                                            $checked_1 = '';
                                            $checked_2 = 'checked="checked"';
                                            $checked_3 = '';
                                        } else {
                                            $checked_1 = '';
                                            $checked_2 = '';
                                            $checked_3 = 'checked="checked"';
                                        }
                                        ?>
                                        <tr>
                                            <td style="width:100px;">                                               
                                                <img src="/<?= F_ASSETS ?>images/social_media/linkedin_color.png" alt="" width="25" height="25"/>                                          
                                                <input type="radio" name="form[social][linkedin_icons]" value="linkedin_color.png"  <?= $checked_1 ?>  />                                                
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/linedin.png" alt="" width="25" height="25"/>
                                                <input type="radio" name="form[social][linkedin_icons]" value="linedin.png" <?= $checked_2 ?>  />
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/linkedin_circle.png" alt="" width="25" height="25"/> 
                                                <input type="radio" name="form[social][linkedin_icons]" value="linkedin_circle.png" <?= $checked_3 ?>   />
                                            </td>
                                        </tr>



                                    </table>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:30px">
                                    <label>Status</label>
                                    <?php
                                    $checked_1 = '';

                                    $selection = (isset($_REQUEST['form']['social']['linkedin_status']) ? $_REQUEST['form']['social']['linkedin_status'] : $social_media[4]['status'] );
                                    if ($selection == 1) {
                                        $checked_1 = 'checked="checked"';
                                    } else {
                                        $checked_1 = '';
                                    }
                                    ?>
                                    <input type="hidden"  name="form[social][linkedin_status]" value="0" />
                                    <input type="checkbox"  name="form[social][linkedin_status]" value="1" <?= $checked_1 ?>/>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fa fa-google-plus-square"></i>&nbsp;Google Plus</label>
                                    <input type="text" name="form[social][google]" value="<?= (isset($_REQUEST['form']['social']['google']) ? $_REQUEST['form']['social']['google'] : $social_media[5]['url']) ?>" class="form-control"  placeholder="http://url of socail media" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <table class="table table-hover table-bordered" style="margin-top:21px">
                                        <!--LOGIC-->
                                        <?php
                                        $checked_1 = '';
                                        $checked_2 = '';
                                        $checked_3 = '';
                                        $selection = ((isset($_REQUEST['form']['social']['google_icons']) ? $_REQUEST['form']['social']['google_icons'] : $social_media[5]['image_name']));
                                        if ($selection == "google_plus_color.png") {
                                            $checked_1 = 'checked="checked"';
                                            $checked_2 = '';
                                            $checked_3 = '';
                                        } else if ($selection == "google_plus.png") {
                                            $checked_1 = '';
                                            $checked_2 = 'checked="checked"';
                                            $checked_3 = '';
                                        } else {
                                            $checked_1 = '';
                                            $checked_2 = '';
                                            $checked_3 = 'checked="checked"';
                                        }
                                        ?>
                                        <tr>
                                            <td style="width:100px;">                                               
                                                <img src="/<?= F_ASSETS ?>images/social_media/google_plus_color.png" alt="" width="25" height="25"/>                                          
                                                <input type="radio" name="form[social][google_icons]" value="google_plus_color.png" <?= $checked_1 ?>   />                                                
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/google_plus.png" alt="" width="25" height="25"/>
                                                <input type="radio" name="form[social][google_icons]" value="google_plus.png" <?= $checked_2 ?>   />
                                            </td>

                                            <td style="width:100px;"> 
                                                <img src="/<?= F_ASSETS ?>images/social_media/google_plus_circle.png" alt="" width="25" height="25"/> 
                                                <input type="radio" name="form[social][google_icons]" value="google_plus_circle.png" <?= $checked_3 ?>   />
                                            </td>
                                        </tr>



                                    </table>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:30px">
                                    <label>Status</label>
                                    <?php
                                    $checked_1 = '';

                                    $selection = (isset($_REQUEST['form']['social']['google_status']) ? $_REQUEST['form']['social']['google_status'] : $social_media[5]['status'] );
                                    if ($selection == 1) {
                                        $checked_1 = 'checked="checked"';
                                    } else {
                                        $checked_1 = '';
                                    }
                                    ?>
                                    <input type="hidden"  name="form[social][google_status]" value="0" />
                                    <input type="checkbox"  name="form[social][google_status]" value="1" <?= $checked_1 ?>/>
                                </div>
                            </div>
                        </div>
                        <!---END Panel-->
                        <div class="form-group">
                            <input type="submit" name="form[social][set_icons]" value="Set Social Media" class="btn btn-success"/>
                        </div>
                    </div>

                </div>

        </div>
        </form>
        <?php
    }

    /*
     * Returns the error messages and displays them where described.
     */

    public function ReturnMessages(array $message, $flag_value = 0) {

        if (isset($message) && $message != NULL && $flag_value != 0) {
            $this->_flag = $flag_value;
            array_push($this->error_message, $message['message']);
            $message = $this->error_message;
        }
    }

    public function GetAllSocialMediadata() {
        $sql = "SELECT * FROM `social_media`";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->_social_media[] = $row;
        }
        return $this->_social_media;
    }

}
