<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StoreInfo
 *
 * @author rostom
 */
class StoreInfo {

    private $_mysqli;
    private $_db;
    public $countries;
    public $flag = 0;
    public $messages = array();
    public $alert_class;
    public $stores;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function AddStoreInformationForm() {
        if (isset($_REQUEST['save_store_info'])) {
            $this->DoAddStorInfo($_REQUEST);
        }
        if (isset($_REQUEST['make_primary'])) {
         
            $id = $_REQUEST['store_prime'];
            if ($_REQUEST['make_primary'] == "Normal") {

                $value = 1;
            } else {
                $value = 0;
            }
            $data = array(
                "value" => $value,
                "id" => $id
            );
        
            $this->MakeAddressPrimary($data);
        }
        if (isset($_REQUEST['delete_store'])) {

            $this->DeleteStoreInfo($_REQUEST);
      
        }
        ?>
        <?php
        if ($this->flag == 1) {
            ?>

            <div class="alert alert-<?= $this->alert_class ?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <ul>
                    <?php
                    foreach ($this->messages as $m) {
                        ?>
                        <li><?= $m['1'] ?></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <?php
        }
        ?>
        <div class="panel-heading">
            <h5><strong><i class="fa fa-university" aria-hidden="true"></i>&nbsp;Add Store Information</strong></h5>
        </div>
        <!--Check in data base available modules-->
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-5">
                    <form method="post">
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>Store Name:</label>
                            <input type="text" name="store_name" value="<?= isset($_REQUEST['store_name']) ? $_REQUEST['store_name'] : '' ?>" class="form-control input-sm" />
                        </div>
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>Address 1:</label>
                            <input type="text" name="store_address1" value="<?= isset($_REQUEST['store_address1']) ? $_REQUEST['store_address1'] : '' ?>" class="form-control input-sm"/>
                        </div>
                        <div class="form-group">
                            <label>Address 2:</label>
                            <input type="text" name="store_address2" value="<?= isset($_REQUEST['store_address2']) ? $_REQUEST['store_address2'] : '' ?>" class="form-control input-sm"/>
                        </div>
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>City:</label>
                            <input type="text" name="city" value="<?= isset($_REQUEST['city']) ? $_REQUEST['city'] : '' ?>" class="form-control input-sm" />
                        </div>
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>Country:</label>
                            <select name="country" class="form-control input-sm">
                                <option value="--">Select Country</option>
                                <?php
                                $selected = '';
                                $this->GetListOfCountries();
                                foreach ($this->countries as $country) {
                                    if (isset($_REQUEST['country'])) {
                                        if ($_REQUEST['country'] == $country['name']) {
                                            $selected = 'selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                    }
                                    ?>
                                    <option value="<?= $country['code'] ?>" <?= $selected ?>><?= $country['name'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>State:</label>
                            <input type="text" name="state" value="<?= isset($_REQUEST['state']) ? $_REQUEST['state'] : '' ?>" class="form-control input-sm" />                              
                        </div>
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>Zip/Postal Code:</label>
                            <input type="text" name="zip" value="<?= isset($_REQUEST['zip']) ? $_REQUEST['zip'] : '' ?>" class="form-control input-sm" style="width: 130px;"/>                              
                        </div>
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>Phone 1:</label>
                            <input type="tex" name="phone1" value="<?= isset($_REQUEST['phone1']) ? $_REQUEST['phone1'] : '' ?>" class="form-control input-sm" />                              
                        </div>
                        <div class="form-group">
                            <label>Phone 2:</label>
                            <input type="tex" name="phone2" value="<?= isset($_REQUEST['phone2']) ? $_REQUEST['phone2'] : '' ?>" class="form-control input-sm" />                              
                        </div>
                        <div class="form-group">
                            <label>Fax</label>
                            <input type="text" name="fax" value="<?= isset($_REQUEST['fax']) ? $_REQUEST['fax'] : '' ?>" class="form-control input-sm" />
                        </div>
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>Google Map Embed Link:</label>
                            <input type="text" name="google_map" value="<?= isset($_REQUEST['google_map']) ? $_REQUEST['google_map'] : '' ?>" class="form-control input-sm"/>
                        </div>
                        <div class="form-group">
                            <label><span style="color: #B81D22;">*</span>Email:</label>
                            <input type="email" name="email" value="<?= isset($_REQUEST['email']) ? $_REQUEST['email'] : '' ?>" class="form-control input-sm"/>
                        </div>
                        <div class="form-group">
                            <label>Customer Service Email:</label>
                            <input type="email" name="cust_email" value="<?= isset($_REQUEST['cust_email']) ? $_REQUEST['cust_email'] : '' ?>" class="form-control input-sm"/>
                        </div>
                        <div class="form-group">
                            <label>Store Hours:</label>
                            <input type="text" name="store_hours" value="<?= isset($_REQUEST['store_hours']) ? $_REQUEST['store_hours'] : '' ?>" class="form-control input-sm"/>
                        </div>
                        <div class="form-group">
                            <label>Holiday Hours:</label>
                            <input type="text" name="holiday_hours" value="<?= isset($_REQUEST['holiday_hours']) ? $_REQUEST['holiday_hours'] : '' ?>" class="form-control input-sm"/>
                        </div>
                        <div class="form-group">
                            <label>Closed On:</label>
                            <input type="text" name="closed_on" value="<?= isset($_REQUEST['closed_on']) ? $_REQUEST['closed_on'] : '' ?>" class="form-control input-sm"/>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Save Store" name="save_store_info" class="btn btn-success btn-sm"/>

                        </div>

                    </form>
                </div>
                <div class="col-md-7">
                    <div class="form-group">
                        <?php
                        if (isset($_REQUEST['google_map']) && $_REQUEST['google_map'] != "") {
                            $link = $_REQUEST['google_map'];
                        } else {

                            $link = 'https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d13188.743782540912!2d-118.4805568!3d34.26931025!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1467773149937';
                        }
                        ?>
                        <iframe src="<?= $link ?>" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>';    
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5>Take Action</h5>
                        </div>
                        <div class="panel-body">
                            

                                <!-- Table -->
                                <table class="table table-hover table-bordered table-responsive">
                                    <tr >
                                        <th>Store/Location</th>
                                        <th>Make it Primary</th>
                                        <th>Delete</th>
                                    </tr>
                                    <?php
                                    if ($this->GetALLStoresInfo()) {

                                        $num_stores = count($this->stores);

                                        foreach ($this->stores as $store) {
                                            ?>
                                            <tr>
                                                <td><strong><?= $store['store_name'] . " - " . $store['address_1'] ?></strong></td>
                                                <td>
                                                    <?php
                                                    if ($store['primary'] == "1") {
                                                        $btn_class = "btn-success";
                                                        $values = "Primary";
                                                    } else {
                                                        $btn_class = "btn-primary";
                                                        $values = "Normal";
                                                    }
                                                    ?>
                                                    <form method="post">
                                                        <input type="hidden" value="<?= $store['id'] ?>" name="store_prime"/>
                                                        <input type="submit" value="<?= $values ?>" name="make_primary" class="btn <?= $btn_class; ?> btn-xs"/>
                                                    </form>
                                                </td>
                                                <td>
                                                    <form method="post">
                                                        <input type="hidden" value="<?= $num_stores ?>" name="num_stores" />
                                                        <input type="hidden" value="<?= $store['id'] ?>" name="store_to_del"/>
                                                        <input type="submit" value="Delete" name="delete_store" class="btn btn-danger btn-xs"/>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $num_stores++;
                                    } else {
                                        ?>
                                        <tr>
                                            <td>No Stores are available.</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function GetListOfCountries() {

        $sql = "SELECT `code`,`name` FROM `countries` ORDER BY `name` ASC";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->countries[] = $row;
        }
        return $this->countries;
    }

    public function DoAddStorInfo($data) {

        $today = date('m/d/y');

        if (empty($data['store_name']) && empty($data['store_address1']) && empty($data['city']) && empty($data['country']) && empty($data['state']) && empty($data['zip']) && empty($data['phone1']) && empty($data['google_map']) && empty($data['email'])) {
            $this->flag = 1;
            $message = array("1" => "Required fields are empty.");
            $this->alert_class = "warning";
            array_push($this->messages, $message);
        } else if (empty($data['store_name']) || empty($data['store_address1']) || empty($data['city']) || empty($data['country']) || empty($data['state']) || empty($data['zip']) || empty($data['phone1']) || empty($data['google_map']) || empty($data['email'])) {
            $this->flag = 1;
            $message = array("1" => "One or more required fields are empty.");
            $this->alert_class = "warning";
            array_push($this->messages, $message);
        } else if ($data['country'] == "--") {
            $this->flag = 1;
            $message = array("1" => "Please select your country.");
            $this->alert_class = "warning";
            array_push($this->messages, $message);
        } else {

            /*
             * First check if the info is not laready in the table
             */
            $store_name = trim($_REQUEST['store_name']);
            $address1 = trim($_REQUEST['store_address1']);
            $address2 = trim($_REQUEST['store_address2']);
            $city = trim($_REQUEST['city']);
            $country = trim($_REQUEST['country']);
            $state = trim($_REQUEST['state']);
            $zip = trim($_REQUEST['zip']);
            $phone1 = trim($_REQUEST['phone1']);
            $phone2 = trim($_REQUEST['phone2']);
            $fax = trim($_REQUEST['fax']);
            $google_maps = trim(addslashes($_REQUEST['google_map']));
            $email = trim($_REQUEST['email']);
            $customer_s_email = trim($_REQUEST['cust_email']);
            $store_hours = trim($_REQUEST['store_hours']);
            $holiday_hours = trim($_REQUEST['holiday_hours']);
            $closed_on = trim($_REQUEST['closed_on']);

            $get_store_info = "SELECT `id` FROM `store_info` WHERE `address_1` = '" . $address1 . "' AND `city` = '" . $city . "' AND `phone1` ='" . $phone1 . "' AND `google_maps` ='" . $google_maps . "'";

            $get_store_info_res = $this->_mysqli->query($get_store_info);
            $store_num_rows = $get_store_info_res->num_rows;

            if ($store_num_rows > 0) {
                $this->flag = 1;
                $message = array("1" => "Store information already exists in the system.");
                $this->alert_class = "warning";
                array_push($this->messages, $message);
            } else {

                $insert_new_store_data = "INSERT INTO `store_info` (store_name, address_1, address_2, city, country, state, zip, phone1, phone2, fax, google_maps, email, cust_email, store_hours, holiday_hours, closed_on, date_added)"
                        . " VALUES "
                        . "("
                        . "'" . $store_name . "', "
                        . "'" . $address1 . "', "
                        . "'" . $address2 . "', "
                        . "'" . $city . "', "
                        . "'" . $country . "', "
                        . "'" . $state . "', "
                        . "'" . $zip . "', "
                        . "'" . $phone1 . "', "
                        . "'" . $phone2 . "', "
                        . "'" . $fax . "', "
                        . "'" . $google_maps . "',"
                        . "'" . $email . "', "
                        . "'" . $customer_s_email . "', "
                        . "'" . $store_hours . "', "
                        . "'" . $holiday_hours . "', "
                        . "'" . $closed_on . "', "
                        . "'" . $today . "'"
                        . ")";

                $insert_new_store_data_res = $this->_mysqli->query($insert_new_store_data);
                if ($insert_new_store_data_res) {
                    $this->flag = 1;
                    $message = array("1" => "Store information has been added to the system");
                    $this->alert_class = "success";
                    array_push($this->messages, $message);
                }
            }
        }
    }

    public function GetALLStoresInfo() {
        $sql = "SELECT `id`, `store_name`, `address_1`, `primary` FROM `store_info` ORDER BY `id` ASC";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->stores[] = $row;
            }
            return $this->stores;
        } else {
            return false;
        }
    }

    public function MakeAddressPrimary($data) {
        $sql = "UPDATE `store_info` SET `primary` = '" . $data['value'] . "' WHERE `id` = '" . $data['id'] . "'";
        $result = $this->_mysqli->query($sql);
    }

    public function DeleteStoreInfo($data) {
        $sql = "DELETE FROM `store_info` WHERE `id` = '" . $data['store_to_del'] . "'";
        $result = $this->_mysqli->query($sql);
    }

}
