<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Body
 *
 * @author rostom
 */
class Body {
    /*
     * Load Different modules
     */

    public $module_control;
    public $promitions_manager;
    public $bulk_upload_manager;
    public $theme_manager;
    public $form_manager;
    public $social_media_manager;
    public $nav_promo_images;
    public $stuck_manager;
    public $sells_reports;
    public $google_analytics;
    public $carousel_manager;
    public $shopping_cart_manager;
    public $add_new_page;
    public $edit_page;
    public $account_detail;
    public $edit_profile;
    public $_main_editing_form;
    public $_right_side;
    public $_log_out;
    public $_store_infor;
    public $_logo_m;
    private $_mysqli;
    private $_db;
    public $wholeSale;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->promitions_manager = new BrandsPromotions();
        $this->bulk_upload_manager = new BulkUpload();
        $this->theme_manager = new Themes();
        $this->form_manager = new Forms();
        $this->social_media_manager = new SocialMedia();
        $this->nav_promo_images = new NavPromoImages();
        $this->stuck_manager = new StuckManager();
        $this->sells_reports = new SellsReports();
        $this->google_analytics = new GoogleAnalytics();
        $this->carousel_manager = new CarouselManager();
        $this->shopping_cart_manager = new ShoppingCart();
        $this->add_new_page = new AddNewPage();
        $this->edit_page = new EditPages();
        $this->account_detail = new AccountDetail();
        $this->edit_profile = new Profile();
        $this->_main_editing_form = new CreatePageForm();
        $this->_right_side = new RightSide();
        $this->module_control = new ModuleControler();
        $this->_store_infor = new StoreInfo();
        $this->_logo_m = new Logo_manager();
        $this->wholeSale = new WholesalerManager();
    }

    /*
     * Body will hold the back end main template
     * It will hold the top menu and the left/right place holder only
     */

    public function BackEndBody() {
        ?>
        <body>
            <div class="container">
                <?php
                if ($this->CheckuserIsAdmin($_SESSION['USER_ID'])) {
                    $module_manager = array(
                        "name" => "Module Manager",
                        "link" => "?cmd=mod_manager",
                        "icon" => "fa fa-plug"
                    );
                } else {
                    $module_manager = array(
                        "name" => "",
                        "link" => "",
                        "icon" => ""
                    );
                }
                $items = array(
                    "DropDowns" => array(
                        "Page Management" => array(
                            "items" => array(
                                "1" => array(
                                    "name" => "Add New Page",
                                    "link" => "?cmd=add-new-page",
                                    "icon" => "fa fa-plus-square"
                                ),
                                "2" => array(
                                    "name" => "Edit pages",
                                    "link" => "?cmd=edit-page",
                                    "icon" => "fa fa-pencil"
                                ),
                            )
                        ),
                        /*
                         * Mod controller
                         */
                        "Modules" => array(
                            "items" =>
                            $this->module_control->AvailableModules()
                        ),
                        "Account" => array(
                            "items" => array(
                                "1" => array(
                                    "name" => "Account Details",
                                    "link" => "?cmd=account-details",
                                    "icon" => "fa fa-exclamation-circle"
                                ),
                                "2" => array(
                                    "name" => "Profile",
                                    "link" => "?cmd=profile",
                                    "icon" => "glyphicon glyphicon-user"
                                ),
                                "3" => $module_manager,
                                "4" => array(
                                    "name" => "Log out",
                                    "link" => "?cmd=log_out",
                                    "icon" => "fa fa-power-off"
                                ),
                            )
                        ),
                    ),
                );
                $this->TopMenu($items);
                $this->BackEndBodyManager();
                ?>
            </div>
            
            <script src="<?= BE_JS ?>bootstrap.min.js"></script> 
<!--            <script src="https://code.jquery.com/ui/1.12.0-rc.1/jquery-ui.js"></script>-->
            <script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>

        </body>
        <?php
    }

    public function TopMenu(array $items) {
        /*
         * Navigation For backend
         * Will need class Module controller
         */
        $login_session = isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : '';
        //var_dump($this->GetUserStatus($_SESSION['USER_ID']));
        if (isset($_SESSION['logged_in']) || $login_session != "") {
            ?>
            <!-- Fixed navbar -->
            <nav class="navbar navbar-default navbar-fixed-top rock-project-nav-overwrite">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <!-- The mobile navbar-toggle button can be safely removed since you do not need it in a non-responsive implementation -->
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Rock 2016 1.1.2</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <?php
                            foreach ($items as $item) {

                                if (is_array($item)) {
                                    ?>
                                    <?php
                                    foreach ($item as $k => $d_item) {
                                        ?>
                                        <li class="dropdown">

                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $k ?><span class="caret"></span></a> 
                                            <ul class="dropdown-menu">
                                                <?php
                                                foreach ($d_item['items'] as $m => $sub_item) {
                                                    if ($sub_item['name'] != "") {
                                                        ?>
                                                        <li><a href="<?= $sub_item['link'] ?>"><i class="<?= $sub_item['icon'] ?>"></i>&nbsp;<?= $sub_item['name'] ?></a></li>
                                                        <?php
                                                    } else {
                                                        
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                } else {
                                    echo $item;
                                }
                            }
                            ?>

                        </ul>
                    </div>
                </div>

            </nav>
            <?php
        } else {
            header("Location: /admin");
        }
    }

    public function GetUserStatus($user_id) {
        $sql = "SELECT * FROM `rock_users` WHERE `id` = '" . $user_id . "' AND `status` = '1'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {

            return true;
        } else {
            unset($_SESSION);
            session_destroy();
            return false;
        }
    }

    public function BackEndBodyManager() {
        $login_session = isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : '';
        if (isset($_SESSION['logged_in']) || $login_session != "") {
            ?>

            <div class="container rock-container-new">
                <div class="panel panel-default">
                    <?php
                    $this->_login_code = isset($_SESSION['login_code']) ? $_SESSION['login_code'] : '';
                    $this->GetUserStatus($_SESSION['USER_ID']);
                    $this->CommandListener();
                    ?>
                </div>
            </div>
            <?php
        } else {
            header("Location: /admin");
        }
    }

    /*
     * this Fuction will load modules based on the command passed through the URI
     */

    public
            function CommandListener() {
        if (isset($_REQUEST['cmd']) && $_REQUEST['cmd'] != "") {
            $command = $_REQUEST['cmd'];

            switch ($command) {
                case "mod_manager":
                    $this->module_control->ShowAvailableModules($check_user_rights = TRUE);

                    break;
                case "brand-promotion":
                    $this->promitions_manager->PromotionsManagement();
                    break;
                case "bulk-upload":
                    $this->bulk_upload_manager->BulkUploadManager();
                    break;
                case "theme-manager":
                    $this->theme_manager->ThemeManager();
                    break;
                case "form-manager":
                    $this->form_manager->FormsManager();
                    break;
                case "social-media":
                    $this->social_media_manager->SocialMediaManager();
                    break;
                case "nav-promo":
                    $this->nav_promo_images->NavigatinPromotionImagesManager();
                    break;
                case "stuck-manager":
                    $this->stuck_manager->StuckManagement();
                    break;
                case "reports":
                    $this->sells_reports->SellsReportsManager();
                    break;
                case "google-analytics":
                    $this->google_analytics->GoogleAnalyticsManager();
                    break;
                case "carousel-manager":
                    $this->carousel_manager->CarouselManagement();
                    break;
                case "shopping-cart":
                    $this->shopping_cart_manager->ShoppingCartManager();
                    break;
                case "add-new-page":
                    $this->add_new_page->AddNewPageManager();
                    break;
                case "edit-page":
                    $this->edit_page->EditPageManager();
                    break;
                case "account-details":
                    $this->account_detail->VeiwAccountDetail();
                    break;
                case "profile":
                    $this->edit_profile->ProfileManagement();
                    break;
                case "page_added":
                    $page_id = $_REQUEST['PUUID'];

                    $this->_main_editing_form->CreatePageMainForm($page_id);
                    break;
                case "edit_page":
                    $page_id = $_REQUEST['PUUID'];
                    $this->_main_editing_form->CreatePageMainForm($page_id);
                    break;
                case "store_info":
                    $this->_store_infor->AddStoreInformationForm();
                    break;
                case "logo-manager":
                    $this->_logo_m->LogoManager();
                    break;
                case "wholesaler-management":
                    $this->wholeSale->WholesalerManagement();
                    break;
                default :
                    break;
            }
        }
    }

    public function CheckuserIsAdmin($login_code) {
        $sql = "SELECT `user_mode` FROM `rock_users` WHERE `id` = '" . $login_code . "' AND `user_mode` = 'Admin'";

        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;

        if ($num_rows > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
