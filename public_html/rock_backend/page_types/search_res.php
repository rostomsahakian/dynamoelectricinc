<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="container rock-main-container">
    <h2 style="text-transform: uppercase;"></h2>
    <div class="row">
        <?php
        $search_res = new FrontendSearch();
         $search->DoSearch($_REQUEST['keyword']);
                
        $res = $search_res->Getresults();
        var_dump($res);
        ?>
    </div>
</div>