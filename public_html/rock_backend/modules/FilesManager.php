<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FilesManager
 *
 * @author rostom
 */
class FilesManager {

    public function __construct() {
        ;
    }

    public function FilesUploadForm() {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading"><span><i class="glyphicon glyphicon-file" ></i>&nbsp;Upload Files</span></div>
            <div class="panel-body">
                <input type="file" name="form[page_edit][uploadfile]"   class="btn btn-default btn-xs"/>

                <input type="submit" class="btn btn-success btn-xs" name="form[page_edit][douploadfile]" value="Upload" style="margin-top: 10px;"/>
            </div>
        </div>
        <div class="rock-cont-div">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5><strong><i class="glyphicon glyphicon-file" ></i>&nbsp;Uploaded Files</strong></h5>
                </div>
                <div class="panel-body">
                    <p>To use your uploaded files simply copy the URL below and add the file name after the forward slash.</p>
                </div>
            </div>
        </div>

        <?php
    }

    public function FilesUploadProccess() {
        
    }

}
