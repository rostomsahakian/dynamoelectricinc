<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Reviews
 *
 * @author rostom
 */
class Reviews {
     public function __construct() {
        ;
    }

    public function ReviewtaticPage($data) {
        ?>
        <div class="container rock-main-container">
            <div class="col-md-12 rock-reviews-page-header">
                <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            </div>
            <div class="row">
                <?= $data['page_content'] ?>
            </div>
        </div>
        <?php
    }
}
