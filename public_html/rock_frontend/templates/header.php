<?php
//session_start();
ob_start();
date_default_timezone_set('America/Los_Angeles');

if (!isset($_COOKIE['order'])) {
    $PHPSESSID = session_id();
    $cookie_name = "order";

    $cookie_value = $PHPSESSID;
    setcookie($cookie_name, $cookie_value, time() + (86400 * 1), "/"); // 86400 is 1 day
} else {
    
}
$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);
if ($_SERVER['SERVER_PORT'] == "443") {

    $protocol = "https";
} else {
    $protocol = "http";
}
?>
<!DOCTYPE html>
<html lang="en" >
    <head>

        <!--Title-->
        <title><?php
            if (array_key_exists("page_title", $page_data)) {
                if ($page_data['page_title'] != "") {
                    echo $page_data['page_title'];
                } else {
                    echo $page_data['page_name'];
                }
            }
            ?></title>
        <!--meta tags-->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php
        if (array_key_exists("meta_data", $page_data) || array_key_exists("page_name", $page_data) || array_key_exists("description", $page_data)) {
            if ($page_data['meta_data'] != "") {
                $meta_data = $page_data['meta_data'];
            } else {
                $meta_data = CUSTOMER . "," . $page_data['page_name'];
            }
            if ($page_data['description'] != "") {
                $description = $page_data['description'];
            } else {
                $description = '';
            }
        } else {
            $description = "";
            $meta_data = "";
        }
        ?>
        <meta name="description" content="<?= $description; ?>">
        <meta name="keywords" content="<?= $meta_data; ?>">
        <meta name="author" content="GrowaRock">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--Scripts-->
        <script src="<?= $protocol ?>://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
        <script src="/<?= F_ASSETS ?>js/creditcardvalidator/jquery.creditCardValidator.js"></script>

        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">

        <script src="/<?= F_ASSETS ?>js/frontend_search.js"></script>
        <script src="/<?= F_ASSETS ?>js/jquery.print.js"></script>

        <script src="/<?= F_ASSETS ?>js/owl.carousel/owl-carousel/owl.carousel.min.js" type="text/javascript"></script>
        <script src="https://use.fontawesome.com/721479b181.js"></script>
        <script src='https://www.google.com/recaptcha/api.js'></script>

        <!--CSS styles--> 
        <link rel="stylesheet" href="/<?= F_ASSETS ?>css/bootstrap.min.css" type="text/css"/>
        <!-- Bootstrap Dropdown Hover CSS -->
        <link href="/<?= F_ASSETS ?>css/animate.min.css" rel="stylesheet">
        <link href="/<?= F_ASSETS ?>css/bootstrap-dropdownhover.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/<?= F_ASSETS ?>owl.carousel/owl-carousel/owl.carousel.css" type="text/css"/>
        <link rel="stylesheet" href="/<?= F_ASSETS ?>owl.carousel/owl-carousel/owl.theme.css" type="text/css"/>
        <link rel="stylesheet" href="/<?= F_ASSETS ?>css/bootstrap-overwrites.css" type="text/css"/>
        <link rel="stylesheet" href="/<?= F_ASSETS ?>css/megamenu.css" type="text/css"/>
        <link rel="stylesheet" href="/<?= F_ASSETS ?>css/page_styles.css" type="text/css"/>


        <!--page Type specific CSS-->
        <?php
        if (array_key_exists("css_file_location", $page_data) && array_key_exists("css_file_name", $page_data)) {
            ?>
            <link rel="stylesheet" href="<?= $page_data['css_file_location'] . $page_data['css_file_name'] ?>"/>
            <?php
        }
        ?>
        <style>


            #owl-demo .item img{
                display: block;
                width: 100%;
                height: auto;
            }
            #bar{
                width: 0%;
                max-width: 100%;
                height: 4px;
                background: #E21A2C;
            }
            #progressBar{
                width: 100%;
                background: #EDEDED;
            }
            .rock-home-page-carousel img { border:3px solid #fff;}
            .jzoom {
                position: absolute;
                top: 250px;
                left: 100px;
                width: 350px;
                height: 350px;
            }


        </style>
        <script>
            $(document).ready(function () {
                // Opera 8.0+
                var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
                // Firefox 1.0+
                var isFirefox = typeof InstallTrigger !== 'undefined';
                // At least Safari 3+: "[object HTMLElementConstructor]"
                var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
                // Internet Explorer 6-11
                var isIE = /*@cc_on!@*/false || !!document.documentMode;
                // Edge 20+
                var isEdge = !isIE && !!window.StyleMedia;
                // Chrome 1+
                var isChrome = !!window.chrome && !!window.chrome.webstore;
                // Blink engine detection
                var isBlink = (isChrome || isOpera) && !!window.CSS;

                var output = 'Detecting browsers by ducktyping:<hr>';
                output += 'isFirefox: ' + isFirefox + '<br>';
                output += 'isChrome: ' + isChrome + '<br>';
                output += 'isSafari: ' + isSafari + '<br>';
                output += 'isOpera: ' + isOpera + '<br>';
                output += 'isIE: ' + isIE + '<br>';
                output += 'isEdge: ' + isEdge + '<br>';
                output += 'isBlink: ' + isBlink + '<br>';
                var url = $('#url').val();
                var item_num = $('#cart_num').val();

                if (isEdge === true || isIE === true) {

                    $('#cart_logo').replaceWith('<a id="new_cart_logo" href="' + url + '" class="btn btn-success btn-xs rock-cart-top-btn"><i class="fa fa-shopping-cart"></i>&nbsp;Cart ' + item_num + '</a>');

                    if (item_num > 0) {

                        $("#new_cart_logo").removeClass("rock-cart-top-btn");
                        //console.log(item_num + " " + url + " ");
                    }

                } else {
                    $("#cart_logo").attr("href", url);
                    $('#cart_logo').html('<i class="fa fa-shopping-cart"></i>&nbsp;Cart ' + item_num);
                    if (item_num > "0") {

                        $("#cart_logo").removeClass("rock-cart-top-btn");
                        //console.log(item_num + " " + url + " ");
                    }
                }



            });


        </script>

    </head>
    <body>
        <?php
        $naviagtion = new Navigation();
        $front_end_logic = new FrontEndLogic();
        ?>
        <div class="rock-home-page-carousel">
            <?php
            $naviagtion->TopNavigation();
            ?>
        </div>
        <div class="row rock-in-between-navs">
            <!--logo-->
            <div class="col-md-4">

                <?php
                if ($front_end_logic->GetStoreLogo()) {
                    foreach ($front_end_logic->store_logo as $logo) {
                        ?>
                        <img src="<?= $logo['logo_path'] . $logo['logo_name'] ?>" class="rock-header-logo"/>
                        <?php
                    }
                }
                ?>

            </div>

            <!--Search-->
            <div class="col-md-3" style="margin-top: 10px; margin-bottom: 10px;">
                <form method="post">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" id="search_box" placeholder="Search for...">
                            <span class="input-group-btn">
                                <input type="hidden" name="cmd" id="page" value="search"/>
                                <input type="submit" value="Go!" name="search_product" class="btn btn-default"/>
                            </span>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                </form>


            </div>
            <!--Social Media-->
            <div class="col-md-5">
                <div class="col-lg-4" style="margin-top: 10px; margin-bottom: 10px;">
                    <center>
                        <!--Social Media-->

                        <div class="btn-group">
                            <?php
                            $front_end_logic->GetSocialMedia();
                            if ($front_end_logic->social_media != NULL) {
                                foreach ($front_end_logic->social_media as $sm) {
                                    ?> 
                                    <a href="<?= $sm['url'] ?>" alt="Social Media Icons" title="<?= $sm['image_name'] ?>" target="_blank"><img src="<?= $sm['image_url'] . $sm['image_name'] ?>" alt="" height="30" width="30"/></a>

                                    <?php
                                }
                            }
                            ?>
                    </center>

                </div>
                <div class="col-md-12">
                    <div class="col-md-4 rock-shopping-cart">
                        <?php
                        ?>
                        <a href="#" class="btn btn-success btn-xs rock-cart-top-btn" id="cart_logo"></a>
                    </div>
                </div>


            </div>
        </div>

    </div>

    <div class="rock-background-color-manager">
        <div class="rock-nav-bar-container">
            <div class="container">
                <?php
                $naviagtion->MegaNavigationMenu();
                ?>
            </div>
        </div>
    </div>
    <?php
    If ($page_data['page_type'] == "9" || $page_data['page_type'] == "10" || $page_data['page_type'] == "11" || $page_data['page_type'] == "14" || $page_data['page_type'] == "5" && $page_data['page_parent'] != 0) {
        ?>
        <!--Bread Crumb-->
        <div class="container">
            <ul class="breadcrumb">

                <?php
                $front_end_logic->BuildBreadCrumb($page_data['page_parent'], $page_data['page_type']);
                $front_end_logic->BreadCrumbLinks();
                ?>
                <li class="active"><?= $page_data['page_name'] ?></li>
            </ul>
        </div>
        <?php
    }
    ?>
    <!--END OF HEADER-->