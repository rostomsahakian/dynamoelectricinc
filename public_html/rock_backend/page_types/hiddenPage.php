<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of hiddenPage
 *
 * @author rostom
 */
class hiddenPage {

    public function __construct() {
        
    }

    public function HiddenStaticPages($data) {
        ?>
        <div class="container rock-main-container">
            <div class="col-md-12 rock-hidden-page-header">
                <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            </div>
            <div class="row">
                <?php
                if (preg_match("/^(\<p\>&lt;\[)([a-zA-Z0-9]*)(\]&gt;\<\/p\>)/", $data['page_content']) == 1) {
                    /*
                     * get the value of the <[code]> and check if the form exists in the /custom/froms/ folder @ rock_frontend
                     */
                    $form_name = preg_replace("/^(\<p\>&lt;\[)/", "", $data['page_content']);
                    $form_name_clean = trim(preg_replace("/(\]&gt;\<\/p\>)/", "", $form_name));
                    $filename = ABSOLUTH_ROOT . 'public_html/rock_frontend/custom/forms/' . $form_name_clean . ".php";
                  
                    if (file_exists($filename)) {
                        include_once $filename;
                    } else {
                        echo "no";
                    }
                } else {
                    echo $data['page_content'];
                }
                ?>
            </div>
        </div>
        <?php
    }

}
