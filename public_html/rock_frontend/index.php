<?php

/*
 * Logic for the front end will commanded from here 
 * Logic comes from FrontEndLogic class
 */
session_start();
$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);
require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/public_html/rock_backend/includes/config.php';
require $baseDir . '/public_html/ShoppingCart/constants/cart_config.php';
$front_end_logic = new FrontEndLogic();

$page_alias = NULL;
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';




/*
 * There are tow ways that we can get the page information
 * 1. By Page Alias
 * 2. By Page ID
 */
$page_array = explode("/", $page);
/*
 * If the page array is empty that means the url should be pointing to home page
 */
if ($page_array[0] == "") {
    /*
     * Get the home page ID
     */
    $page_id = $front_end_logic->GetHomePageID();
} else if ($page_array[0] != "" && count($page_array) > 1) {
    $link_count = count($page_array) - 1;
    /*
     * If array contains more that one element
     */
    for ($i = 0; $i < $link_count; $i++) {
        
    }
//The last element will always be the page ID
    $page_id = $page_array[$i];
} else if ($page_array[0] != "" && count($page_array) == 1) {
//This means that the page has alias
    $page_id = "";
    $page_alias = $page_array[0];
} else {
    $page_alias = "";
}

if ($page_alias != NULL) {
//If true then get the page id and get the data for that page
    if ($front_end_logic->CheckIfPageHasAlias($page_alias)) {
        $front_end_logic->GetPageDataByAlias($page_alias);
    }
} else {
    $front_end_logic->GetpageDataByID($page_id);
}

$page_data = $front_end_logic->ReturnPageData();

if (isset($_REQUEST['cmd'])) {
    if ($_REQUEST['cmd'] == "search") {
        $page_data['page_type'] = 1001; // Search View
    }
    if ($_REQUEST['cmd'] == "cart" && $_REQUEST['ssid'] == md5($_COOKIE['order'])) {
        $page_data['page_title'] = "Shopping Cart";
        $page_data['meta_data'] = CUSTOMER . " shopping cart page.";
        $page_data['description'] = "";
        $page_data['page_type'] = 1002; // Cart veiw
        $page_data['page_name'] = "Your shopping cart items.";
    }
    if ($_REQUEST['cmd'] == "pre_check_out") {


        /*
         * if user is logged in skip the the check out option and go straight to credit card information part 
         */
        if (!isset($_SESSION['user_log'])) {
            $page_data['page_type'] = 1003; // Pre-check out
            $page_data['page_title'] = "Shopping Cart - Step 1";
            $page_data['meta_data'] = CUSTOMER . " shopping cart page.";
            $page_data['description'] = "";
            $page_data['page_name'] = "Check out Step 1";
        } else {

            $page_data['page_type'] = 1004; // Pre-check out
            $page_data['page_title'] = "Shopping Cart - Guest Check out";
            $page_data['meta_data'] = CUSTOMER . " shopping cart page.";
            $page_data['description'] = "";
            $page_data['page_name'] = "Check out Step 2";
        }
    }
    if ($_REQUEST['cmd'] == "check-out-as-guest" || $_REQUEST['cmd'] == "user-check-out") {

        $page_data['page_type'] = 1004; // Pre-check out
        $page_data['page_title'] = "Shopping Cart - Guest Check out";
        $page_data['meta_data'] = CUSTOMER . " shopping cart page.";
        $page_data['description'] = "";
        $page_data['page_name'] = "Check out Step 2";
    }
    if ($_REQUEST['cmd'] == "review order") {
        $page_data['page_type'] = 1005; // Order review
        $page_data['page_title'] = "Shopping Cart - Review order";
        $page_data['meta_data'] = CUSTOMER . " shopping cart page.";
        $page_data['description'] = "";
        $page_data['page_name'] = "Check out Step 3";
    }
    if ($_REQUEST['cmd'] == "create") {
        $page_data['page_type'] = 1006; // Create Account
        $page_data['page_title'] = "Create new account";
        $page_data['meta_data'] = CUSTOMER . " Sign Up.";
        $page_data['description'] = "";
        $page_data['page_name'] = "Sing Up!";
    }
    if ($_REQUEST['cmd'] == "login") {
        $page_data['page_type'] = 1007; // Login
        $page_data['page_title'] = "Login to your account";
        $page_data['meta_data'] = CUSTOMER . " Login in.";
        $page_data['description'] = "";
        $page_data['page_name'] = "Log in";
    }
    if ($_REQUEST['cmd'] == "activate") {
        $page_data['page_type'] = 1007; // activate account
        $page_data['page_title'] = "Login to your account";
        $page_data['meta_data'] = CUSTOMER . " Login in.";
        $page_data['description'] = "";
        $page_data['page_name'] = "Log in";
    }
    if ($_REQUEST['cmd'] == "user-acount") {

        if (isset($_SESSION['user_log'])) {
            $page_data['page_type'] = 1008; // user panel
            $page_data['page_title'] = "Account Details";
            $page_data['meta_data'] = CUSTOMER . " User panel.";
            $page_data['description'] = "";
            $page_data['page_name'] = "Personal information";
        } else {
            $page_data['page_type'] = 1007; // activate account
            $page_data['page_title'] = "Login to your account";
            $page_data['meta_data'] = CUSTOMER . " Login in.";
            $page_data['description'] = "";
            $page_data['page_name'] = "Log in";
        }
    }
    if ($_REQUEST['cmd'] == "logout") {

        $page_data['page_type'] = 1009; // logout
        $page_data['page_title'] = "logged out";
        $page_data['meta_data'] = CUSTOMER . " User logged out.";
        $page_data['description'] = "";
        $page_data['page_name'] = "You have logged out of your account.";
    }
    if ($_REQUEST['cmd'] == "forms") {
        $page_data['page_type'] = 1010; // logout
        $page_data['page_title'] = "Forms";
        $page_data['meta_data'] = CUSTOMER . " custom forms";
        $page_data['description'] = "";
        $page_data['page_name'] = "";
    }
}
if ($page_data == NULL) {
    $page_data['page_type'] = 404; // logout
    $page_data['page_title'] = "404 - page";
    $page_data['meta_data'] = CUSTOMER . " Unkown page.";
    $page_data['description'] = "";
    $page_data['page_name'] = "OOps something went wrong.";
}
switch ($page_data['page_type']) {

    case "1":
        include 'templates/header.php';
        $home_page = new HomePage();
        $home_page->MainHomePage($page_data);
        include 'templates/footer.php';
        break;
    case "":
        include 'templates/header.php';
        $home_page = new HomePage();
        $home_page->MainHomePage();
        include 'templates/footer.php';
        break;
    case "2":
        include 'templates/header.php';
        $contact_us = new ContactUs();
        $contact_us->ContactUspage($page_data);
        include 'templates/footer.php';
        break;
    case "3":
        include 'templates/header.php';
        $about_us = new AboutUs();
        $about_us->AboutUsStaticPage($page_data);
        include 'templates/footer.php';
        break;
    case "4":
        include 'templates/header.php';
        $reviews = new Reviews();
        $reviews->ReviewtaticPage($page_data);
        include 'templates/footer.php';
        break;
    case "5":
        include 'templates/header.php';
        $static_page = new StaticPage();
        $static_page->StaticPageContent($page_data);
        include 'templates/footer.php';
        break;
    case "6":
        include 'templates/header.php';
        $brands = new Brands();
        include 'templates/footer.php';
        break;
    case "7":
        include 'templates/header.php';
        $single_brand = new Brand();
        include 'templates/footer.php';
        break;
    case "8":
        include 'templates/header.php';
        $catergory_page = new Categories();
        $catergory_page->CategoriesPageSetup($page_data);
        include 'templates/footer.php';
        break;
    case "9":
        include 'templates/header.php';
        $subCategroy_page = new SubCategories();
        $subCategroy_page->SubCategoriesPage($page_data);
        include 'templates/footer.php';
        break;
    case "10":
        include 'templates/header.php';
        $products_page = new Products();
        $products_page->ProductPage($page_data);
        include 'templates/footer.php';
        break;
    case "11":
        include 'templates/header.php';
        $hidden_page = new hiddenPage();
        $hidden_page->HiddenStaticPages($page_data);
        include 'templates/footer.php';
        break;
    case "12":
        include 'templates/header.php';

        include 'templates/footer.php';
        break;
    case "13":
        include 'templates/header.php';
        $policies_page = new Policies();
        $policies_page->PoliciesPage($page_data);
        include 'templates/footer.php';
        break;
    case "14":
        include 'templates/header.php';
        $faq_page = new FAQs();
        $faq_page->FaqPage($page_data);
        include 'templates/footer.php';
        break;
    case "1001":
        include 'templates/header.php';
        $search_res = new FrontendSearch();
        $search_res->DoSearch($_REQUEST['keyword']);
        $search_res->Getresults();
        include 'templates/footer.php';
        break;
    case "1002":
        include 'templates/header.php';
        $shopping_cart_page = new ShoppingCartFunc();
        $shopping_cart_page->ShoppingCartForm($page_data);
        include 'templates/footer.php';
        break;
    case "1003":
        include 'templates/header.php';
        $check_p1 = new CheckOut();
        $check_p1->CheckOptionForm($page_data);
        include 'templates/footer.php';
        break;
    case "1004":
        include 'templates/header.php';
        $guestCheckout = new GuestCheckOut();
        $guestCheckout->CheckOutAsGuest($page_data, $_REQUEST);
        include 'templates/footer.php';
        break;
    case "1005":
        include 'templates/header.php';
        include ABSOLUTH_ROOT . 'public_html/ShoppingCart/checkoutAIM.php';
        include 'templates/footer.php';
        break;
    case "1006":
        include 'templates/header.php';
        $signupPage = new accounts();
        $signupPage->FrontEndSignUpForm($page_data);
        include 'templates/footer.php';
        break;
    case "1007":
        include 'templates/header.php';
        $loginPage = new accounts();
        $loginPage->FrontEndLoginForm($page_data);
        include 'templates/footer.php';
        break;
    case "1008":
        include 'templates/header.php';
        $userpanel = new accounts();
        $userpanel->FrontEndUserPanel($page_data);
        include 'templates/footer.php';
        break;
    case "1009":
        include 'templates/header.php';
        $logout = new accounts();
        $logout->LogFronTEndUserOut($page_data);
        include 'templates/footer.php';
        break;
    case "404":
        include 'templates/header.php';
        include ABSOLUTH_ROOT . 'public_html/rock_frontend/pagetypes/404.php';
        include 'templates/footer.php';
        break;
    case "1010":
        include 'templates/header.php';
        include ABSOLUTH_ROOT . 'public_html/rock_frontend/custom/forms/'.$_REQUEST['form-name'].'.php';
        include 'templates/footer.php';
        break;
}
    