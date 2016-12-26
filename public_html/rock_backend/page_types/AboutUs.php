<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AboutUs
 *
 * @author rostom
 */
class AboutUs {

    public function __construct() {
        ;
    }

    public function AboutUsStaticPage($data) {
        ?>
        <div class="container rock-main-container">
            <div class="col-md-12 rock-about-us-page-header">
                <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            </div>
            <div class="row">
                <?= $data['page_content'] ?>
            </div>
        </div>
        <?php
    }

}
