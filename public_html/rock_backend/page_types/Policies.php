<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Policies
 *
 * @author rostom
 */
class Policies {

    public function __construct() {
        ;
    }

    public function PoliciesPage($data) {
        ?>
        <div class="container rock-main-container">
            <h2 style="text-transform: uppercase;"><?= $data['page_name']; ?></h2>
            <div class="row">
                <?= $data['page_content'] ?>
            </div>
        </div>
        <?php
    }

}
