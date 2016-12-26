<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Description of Backend_Loader
 *
 * @author rostom
 */
class Backend_Loader {

    //put your code here
    public $header;
    public $Footer;
    public $Body;
    public $rightside;
    public $leftside;

    public function __construct() {
        $this->header = new Header();
        $this->Footer = new Footer();
        $this->Body = new Body();
        $this->leftside = new LeftSide();
        $this->rightside = new RightSide();
    }

    public function LoadAll() {

        /*         * ******************************************
         * header Section
         * ******************************************
         */
        $jsFiles = array(
          
            "1" => "https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js",
            "2" => "https://code.jquery.com/ui/1.12.0-rc.1/jquery-ui.js",
            "3" => BE_JS."ckeditor/ckeditor.js"
        );

        $cssFiles = array(
            "1" => BE_CSS."bootstrap.min.css",
            "2" => BE_CSS."overwrites.css",
            "3" => BE_FONTS."font-aw/css/font-awesome.min.css",
            "4" => "//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"
            
        );

        $this->header->SetJSFiles($jsFiles);
        $this->header->GetJSFiles();
        $this->header->SetCSSFiles($cssFiles);
        $this->header->GetCSSFiles();
        $this->header->SetHeader();

        /*         * ******************************************
         * backend body controller
         * ******************************************
         */
        $this->Body->BackEndBody();
        /*         * ******************************************
         * Footer
         * ******************************************
         */
        $this->Footer->LoadFooter();
    }

}

$back_end = new Backend_Loader();
$back_end->LoadAll();
