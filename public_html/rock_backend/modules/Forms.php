<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Forms
 *
 * @author rostom
 */
class Forms {

    private $_mysqli;
    private $_db;
    public $_res;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function FormsManager() {
        ?>
        <div class="panel-heading">
            <h5><strong><i class="glyphicon glyphicon-tasks" aria-hidden="true"></i>&nbsp;Form Manager</strong></h5>
        </div>
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-3"></div>
                <div class="col-md-5">
                    <p>Please enter the name and number of fields for the form.</p>
                    <form method="post">
                        <div class="form-group">
                            <label>Form Name:</label>
                            <input type="text" name="form_name" value="<?= isset($_REQUEST['form_name']) ? $_REQUEST['form_name'] : "" ?>" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Number Of fields:</label>
                            <input type="number" name="num_field" value="<?= isset($_REQUEST['num_field']) ? $_REQUEST['num_field'] : '' ?>" class="form-control" />
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Create Form" name="do_create_form" class="btn btn-success" />
                        </div>
                    </form>
                </div>
            </div>

            <!--Form editing goes here-->
            <?php
            $num_fields = isset($_REQUEST['num_field']) ? $_REQUEST['num_field'] : '';
            $form_name = isset($_REQUEST['form_name']) ? $_REQUEST['form_name'] : '';
            $this->DoCreateForm($num_fields, $form_name);
            ?>
        </div>

        <?php
    }

    public function DoCreateForm($num_fields, $form_name) {
        if (isset($_REQUEST['do_create_form'])) {
            if ($num_fields > 0) {
                ?>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5><i class="fa fa-clipboard"></i>&nbsp;Edit Form</h5>
                        </div>
                        <div class="panel-body">
                            <form method="post">
                                <div class="col-md-10">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>Field Name</th>
                                            <th>Field Type</th>                                           
                                            <th>is required? Yes</th>
                                            <th>is required? No</th>
                                        </tr>
                                        <?php
                                        for ($i = 0; $i < $num_fields; $i++) {
                                            ?>

                                            <tr>
                                                <td>
                                                    <div class="form-group">

                                                        <input type="text" name="<?= $form_name . "_" . $i ?>" value="<?= isset($_REQUEST[$form_name . "_" . $i]) ? $_REQUEST[$form_name . "_" . $i] : '' ?>" placeholder="Enter Field name" class="form-control"/>

                                                    </div>     
                                                </td>
                                                <td>
                                                    <div class="form-group">

                                                        <select name="input_type" class="form-control">
                                                            <option value="--" >Select type</option>
                                                            <option value="text">Text</option>
                                                            <option value="select">Select</option>
                                                            <option value="email">Email</option>
                                                            <option value="radio">Radio</option>
                                                            <option value="checkbox">Checkbox</option>
                                                            <option value="number">Number</option>
                                                            <option value="tel">Telephone</option>
                                                            <option value="password">Password</option>
                                                            <option value="submit">Submit</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>


                                                    <input type="radio" name="required_<?= $form_name . "_", $i ?>" value="yes" class="form-control"/>


                                                </td>
                                                <td>

                                                    <input type="radio" name="required_<?= $form_name . "_", $i ?>" value="no" class="form-control"/>


                                                </td>
                                            </tr>



                                            <?php
                                        }
                                        ?>
                                    </table>

                                    <hr/>

                                    <p>Please Select the page you would like to add this form</p>
                                    <select class="form-control">
                                        <?php
                                        foreach ($this->GetAllPages() as $pages) {
                                            ?>
                                            <option value="<?= $pages['page_id'] ?>"><?= $pages['page_name'] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    }

    public function GetAllPages() {
        $sql = "SELECT * FROM `pages`";
        $result = $this->_mysqli->query($sql);

        if ($result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->_res[] = $row;
            }
            return $this->_res;
        }
    }

}
