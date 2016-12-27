<?php

/*
 * i.e. /home/dynamoelectric/public_html/project.rock
 * When configuring a new site please change the indicated paths and names
 * You have also need to change the path for config file it is located in the rock_backend/path_config.php
 * once that is done the site and the back end should be reading from same location
 * R.S 07/09/2016
 * 
 */
define("ROOT", $_SERVER['DOCUMENT_ROOT'] . "/public_html/");
define("BACKEND", ROOT . "rock_backend/");
define("B_ASSETS", "../rock_backend/backend_assets/");
define("ABSOLUTH_ROOT", "/home/dynamoelectric/public_html/dev.dynamoelectricinc/"); //Chaneg this if you are getting errors on a new site
define("DATE_ADDED", date("F j,Y, g:i a"));
define("BE_CSS", B_ASSETS . "css/");
define("BE_JS", B_ASSETS . "js/");
define("BE_FONTS", B_ASSETS . "fonts/");
define("BE_IMAGES", B_ASSETS . "images/");
define("PROJECT_NAME", "growaroc_theline"); // Change the new to the same as db name !important
define("PROJECT_URL", "http://dev.dynamoelectricinc.com/"); // change this
define("ADMIN_PASS", "616812d7a966392405fdf0b166c377a0");
define("CUSTOMER", "Dynamoelectric inc."); // change this
define("CUSTOMER_EMAIL", "rostom.sahakian@gmail.com"); // CHANGE THIS FOR NEW CLIENTS
/*
 * DB INFO
 * Change if the data is different
 */
define("DB_USERNAME", "dynamo_e");
define("DB_PASSWORD", "AGxNWuUQInD*");
define("DB_NAME", "dynamoelectric"); //change this
define("DB_HOST", "localhost");

/*
 * Fornt End
 */
define("URL", "dev.dynamoelectricinc.com/public_html/");
define("FRONTEND", "rock_frontend");
define("F_ASSETS", FRONTEND . "/frontend_assets/");
define("FE_IMAGES", ABSOLUTH_ROOT . "public_html/" . F_ASSETS . "images/");
define("IMAGE_PATH", "../rock_frontend/frontend_assets/images/");
define("P_IMAGE_PATH", "/rock_frontend/frontend_assets/images/");
define("WEBSITE_URL", "dev.dynamoelectricinc.com/");
define("FE_FILES", F_ASSETS . "files/");
define("FILE_PATH", "../rock_frontend/frontend_assets/files/");

/*
 * Re-Captcha 
 */
define("RE_CAPTCHA_SECRET_KEY", "6LcIjSQTAAAAAAoeYq1hYBKX5XUA0ZSltgEIO-V_"); //Change this when we go live get new key from google api
define("RE_CAPTCH_SITE_KEY", "6LcIjSQTAAAAALFyxGoK2Fa0PdTp4kfchOnW_80b"); //Change this when we go live get new key from google api
/*
 * To turn on the add to bag on and off
 * if 0 it means off
 */
define("PRICE_STATUS", 0);
define("SHOW_CART", 0);

define("PRODUCT_TYPE", "Additives"); //Change this based on good ans services sold
//
//Do not remove*******************************************************************************
define("WHOLESALE", 1); //If the whole sale is on(1) then it will create the wholesale tables
//********************************************************************************************