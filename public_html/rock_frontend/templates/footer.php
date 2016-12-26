<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$shopping_cart_updates = new ShoppingCartFunc();

if (isset($_COOKIE['order'])) {

    $num_items = $shopping_cart_updates->ReturnNumProductsInCart();
} else {
    $num_items = 0;
}
if ($num_items != 0) {
    $url = "/see_cart?cmd=cart&ssid=" . md5($_COOKIE['order']);
} else {
    $url = "#";
}

$myfile = fopen(ABSOLUTH_ROOT . 'public_html/ShoppingCart/qty.txt', "r") or die("Unable to open file!");
$cart_val = fread($myfile, filesize(ABSOLUTH_ROOT . 'public_html/ShoppingCart/qty.txt'));
fclose($myfile);
?>
<input type="hidden" id="url" value="<?= $url ?>"/>
<input type="hidden" value="<?= $shopping_cart_updates->ReturnNumProductsInCart() ?>" name="cart_num" id="cart_num"/>
<div class="rock-top-footer">
    <div class="container ">
        <div class="row">
            <div class="col-md-4">

                <div class="rock-social-media-footer">
                    <?php
                    $front_logic = new FrontEndLogic();
                    $front_logic->GetSocialMedia();
                    if ($front_logic->social_media != NULL) {
                        foreach ($front_logic->social_media as $sm) {
                            ?> 
                            <a href="<?= $sm['url'] ?>" alt="Social Media Icons" title="<?= $sm['image_name'] ?>" target="_blank"><img src="<?= $sm['image_url'] . $sm['image_name'] ?>" alt="" height="30" width="30"/></a>

                            <?php
                        }
                    }
                    ?>

                </div>
            </div>

            <div class="col-md-4 rock-top-footer-newsletter">

                <h4 class="rock-newsletter-h4"><i class="fa fa-envelope-o" aria-hidden="true"></i>&nbsp;&nbsp;Sign Up For Our NewsLetter</h4>
            </div>
            <?php
            $plac_holder_message = "Your email address";
            $style = "";

            if (isset($_REQUEST['sign_up'])) {
                if (empty($_REQUEST['news_letters_email'])) {
                    $plac_holder_message = "Please enter your email address.";
                    $style = "style='border-color:#EA3A3C !important; background-color:#FE8484 !important;'";
                } else if (filter_var($_REQUEST['news_letters_email'], FILTER_VALIDATE_EMAIL) === false) {
                    $plac_holder_message = "Please enter a valid email.";
                    $style = "style='border-color:#EA3A3C !important; background-color:#FE8484 !important;'";
                } else {
                    if ($front_logic->SignUpForNewsLetter($_REQUEST)) {
                        $plac_holder_message = "Thank you for signing up.";
                        $style = "";
                    } else {
                        $plac_holder_message = "You have already been signed up for our newsletter.";
                        $style = "style='border-color:#EA3A3C !important; '";
                    }
                }
            }
            ?>
            <div class="col-md-4 rock-top-footer-newsletter-form">
                <form method="post">
                    <div class="input-group">
                        <input type="email" class="form-control danger" name="news_letters_email" <?= $style ?> placeholder="<?= $plac_holder_message ?>">
                        <span class="input-group-btn">
                            <input type="submit" class="btn btn-default rock-input-button" name="sign_up" value="Sign Up" />
                        </span>
                    </div><!-- /input-group -->
                </form>
            </div>
        </div> 
    </div>
</div>

<div class="rock-bottom-footer-div">
    <div class="container">
        <div class="row rock-bottom-row">

            <div class="col-md-3">
                <h4><?= CUSTOMER ?></h4>
                <ul>
                    <?php
                    $front_logic->GetFooterTopPages();
                    foreach ($front_logic->all_top_pages as $top_pages) {
                        $front_logic->page_alias = NULL;
                        if ($front_logic->GetPageAlias($top_pages['page_id'])) {

                            foreach ($front_logic->page_alias as $page_alias) {

                                $url = "/" . $page_alias['page_alias'];
                            }
                        } else {
                            $url_spaces = str_replace(" ", "-", strtolower($top_pages['page_name']));
                            $url_ands = str_replace("&", "and", $url_spaces);
                            $clean_url = preg_replace('/[^a-zA-Z0-9,-]/', '-', $url_ands);

                            $url = "/" . $clean_url . "/" . $top_pages['id'];
                        }
                        ?>
                        <li><a href="<?= $url ?>" title="<?= $top_pages['page_name'] ?>" alt="This is a link to <?= $top_pages['page_name'] ?> page."><?= $top_pages['page_name'] ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-3">
                <h4>Policies</h4>
                <ul>
                    <?php
                    $front_logic->GetFooterHiddenPages();
                    foreach ($front_logic->all_hidden_pages as $hidden_pages) {

                        if ($front_logic->GetPageAlias($hidden_pages['page_id'])) {
                            foreach ($front_logic->page_alias as $hidden_page_alias) {
                                $url_hidden = "/" . $hidden_page_alias['page_alias'];
                            }
                        } else {
                            $url_spaces_h = str_replace(" ", "-", strtolower($hidden_pages['page_name']));
                            $url_ands_h = str_replace("&", "and", $url_spaces_h);
                            $clean_url_h = preg_replace('/[^a-zA-Z0-9,-]/', '-', $url_ands_h);

                            $url_hidden = "/" . $clean_url_h . "/" . $hidden_pages['id'];
                        }
                        ?>
                        <li><a href="<?= $url_hidden ?>" title="<?= $hidden_pages['page_name'] ?>" alt="This is a link to <?= $hidden_pages['page_name'] ?> page."><?= $hidden_pages['page_name'] ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-3 rock-contact-us-footer">
                <h4>Contact Us</h4>
                <?php
                if ($front_logic->GetStoreInformation()) {
                    foreach ($front_logic->store_info as $store_info) {
                        ?>
                        <ul>
                            <li><i class="fa fa-home" aria-hidden="true"></i>&nbsp;<?= $store_info['address_1'] . " " . $store_info['address_2'] . ", " . $store_info['city'] . " " . $store_info['state'] . " " . $store_info['zip'] . " " . $store_info['country'] ?></li>
                            <li><i class="fa fa-mobile" aria-hidden="true"></i>&nbsp;Telephone: <?= $store_info['phone1'] ?></li>
                            <li><i class="fa fa-envelope" aria-hidden="true"></i>&nbsp; E-mail: <?= $store_info['email'] ?></li>
                            <li><i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp; <?= $store_info['store_hours'] ?></li>



                        </ul>

                        <?php
                    }
                }
                ?>

            </div>
            <div class="col-md-3">
                <h4>Payment options</h4>
                <ul class="rock-payment-options">
                    <li><a href="#"><img src="/<?= F_ASSETS ?>images/payment_images/payment-visa.png"/></a></li>
                    <li><a href="#"><img src="/<?= F_ASSETS ?>images/payment_images/payment-paypal.png"/></a></li>
                    <li><a href="#"><img src="/<?= F_ASSETS ?>images/payment_images/payment-mastercard.png"/></a></li>
                    <li><a href="#"><img src="/<?= F_ASSETS ?>images/payment_images/payment-ae.png"/></a></li>
                    <li><a href="#"><img src="/<?= F_ASSETS ?>images/payment_images/payment-discover.png"/></a></li>
                </ul>
            </div>

        </div>
    </div>
    <div class="rock-footer-all-rights">
        <center><?php
            if (defined('CUSTOMER')) {
                echo CUSTOMER;
            } else {
                echo 'Your Website name';
            }
            ?> All Rights Reserved &reg; Powered By <a href="/admin" target="_Blank">GrowaRock</a>
        </center>

        <center>
            <?php
            $xml_test = new xmlsitemap();

            $xml_test->GetSubs();
            $xml_test->CreateXMLSiteMap();
            ?>
            <a href="/data.xml" target="_BLANK">Sitemap</a>
        </center>
    </div>



</div>
<script>


    $(document).ready(function () {

        var time = 7; // time in seconds

        var $progressBar,
                $bar,
                $elem,
                isPause,
                tick,
                percentTime;

//Init the carousel
        $("#owl-demo").owlCarousel({
            slideSpeed: 500,
            paginationSpeed: 500,
            singleItem: true,
            afterInit: progressBar,
            afterMove: moved,
            startDragging: pauseOnDragging
        });

//Init progressBar where elem is $("#owl-demo")
        function progressBar(elem) {
            $elem = elem;
//build progress bar elements
            buildProgressBar();
//start counting
            start();
        }

//create div#progressBar and div#bar then prepend to $("#owl-demo")
        function buildProgressBar() {
            $progressBar = $("<div>", {
                id: "progressBar"
            });
            $bar = $("<div>", {
                id: "bar"
            });
            $progressBar.append($bar).prependTo($elem);
        }

        function start() {
//reset timer
            percentTime = 0;
            isPause = false;
//run interval every 0.01 second
            tick = setInterval(interval, 10);
        }
        ;

        function interval() {
            if (isPause === false) {
                percentTime += 1 / time;
                $bar.css({
                    width: percentTime + "%"
                });
//if percentTime is equal or greater than 100
                if (percentTime >= 100) {
                    //slide to next item 
                    $elem.trigger('owl.next')
                }
            }
        }

//pause while dragging 
        function pauseOnDragging() {
            isPause = true;
        }

//moved callback
        function moved() {
//clear interval
            clearTimeout(tick);
//start again
            start();
        }

//uncomment this to make pause on mouseover 
// $elem.on('mouseover',function(){
//   isPause = true;
// })
// $elem.on('mouseout',function(){
//   isPause = false;
// })

    });


</script>


<script src="/<?= F_ASSETS ?>js/bootstrap.min.js"></script>

<!-- Bootstrap Dropdown Hover JS -->
<script src="/<?= F_ASSETS ?>js/bootstrap-dropdownhover.min.js"></script>
</body>
<script>
    $(function () {
        $('#cc_number').validateCreditCard(function (result) {
            $('.log').html('Card type: ' + (result.card_type == null ? '-' : result.card_type.name)
                    + '<br>Valid: ' + result.valid
                    + '<br>Length valid: ' + result.length_valid
                    + '<br>Luhn valid: ' + result.luhn_valid);
            $('#type').attr('src', '/rock_frontend/frontend_assets/images/payment_images/blankcc.png');
            var c_type = (result.card_type == null ? '-' : result.card_type.name);
            $('#cc_length').val(result.length_valid);
            $('#cc_luhn').val(result.luhn_valid);
            $('#cc_is_valid').val(result.valid);
            $('#cc_type').val(c_type);
            $('#cc_number').keyup(function () {



                if (c_type === "visa") {
                    $('#type').attr('src', '/rock_frontend/frontend_assets/images/payment_images/payment-visa.png');
                } else if (c_type === "mastercard") {
                    $('#type').attr('src', '/rock_frontend/frontend_assets/images/payment_images/payment-mastercard.png');
                } else if (c_type === "amex") {
                    $('#type').attr('src', '/rock_frontend/frontend_assets/images/payment_images/payment-ae.png');
                } else if (c_type === "discover") {
                    $('#type').attr('src', '/rock_frontend/frontend_assets/images/payment_images/payment-discover.png');
                } else if (c_type === "-") {
                    $('#type').attr('src', '/rock_frontend/frontend_assets/images/payment_images/blankcc.png');
                }

            });
            $('#cc_number').keydown(function () {
                if (result.valid === false) {

                    $('#cc_number').css("border-color", "#D92736");
                } else {
                    $('#cc_number').css("border-color", "#02C54C");
                }
            });
        });
    });
</script>
</html>