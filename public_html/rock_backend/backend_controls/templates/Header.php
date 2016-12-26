<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Description of Header
 *
 * @author rostom
 */
class Header {

    //put your code here
    public $header_files;
    public $JS;
    public $CSS = array();

    public function __construct() {
        /*
         * Will load all the backend stuff in construct
         */
    }

    /*
     * Header needs
     */

    public function SetHeader() {
        ?>
        <!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
            <head>
                <title>Project Rock Content Management System</title>
                <meta charset="utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                        <?php
                        foreach ($this->GetJSFiles() as $js_file) {
                            echo "<script src='" . $js_file . "'></script>";
                        }
                        foreach ($this->GetCSSFiles() as $css_file) {
                            echo "<link href='" . $css_file . "' rel='stylesheet'/>";
                        }
                        ?>
                        <script>
                            $(function () {
                                $("#datepicker").datepicker();
                            });
                        </script>

                        <script>
                            function goBack() {
                                window.history.back();
                            }
                        </script>
                        </head>    

                        <?php
                    }

                    public function SetJSFiles(array $jsFiles) {

                        $this->JS = $jsFiles;
                    }

                    public function GetJSFiles() {
                        return $this->JS;
                    }

                    public function SetCSSFiles(array $cssFiles) {

                        $this->CSS = $cssFiles;
                    }

                    public function GetCSSFiles() {

                        return $this->CSS;
                    }

                }
                