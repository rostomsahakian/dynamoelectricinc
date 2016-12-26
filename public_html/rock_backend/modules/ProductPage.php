<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductPage
 *
 * @author rostom
 */
class ProductPage {

    private $_mysqli;
    private $_db;
    public $sql_res;
    public $q_res;
    public $parents;
    public $flag = 0;
    public $messages = array();
    public $alert_class = "";
    public $image_uploader;
    public $url_parents;
    public $preview_url;
    public $product_page_id;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->image_uploader = new RightSide();
        // $this->page_url = new CreatePageForm();
    }

    public function ChoosetableForProduct($table_name) {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5><strong><i class="fa fa-calendar"></i>Choose table to add product</strong></h5>
            </div>
            <div class="panel-body">
                <form method="post">
                    <div class="form-group">
                        <label>Table name:</label>
                        <select name="tabel_name" class="form-control">
                            <option value="--">--Select--</option>
                        </select>
                    </div>
                    <input type="submit" class="btn btn-success btn-xs" name="get_fields" value="next"/>
                </form>
            </div>
        </div>
        <?php
    }

    public function PageProductsFiledChooser($fields, $page_id) {
        ?>
        <form method="post">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h5><strong><i class="fa fa-book"></i>&nbsp; Products Field Selector</strong></h5>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <!--DIV 1-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Item Name:</label>
                                <select name="item_name" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['item_name'])) {
                                            if ($_REQUEST['item_name'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Model Number:</label>
                                <select name="model_number" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['model_number'])) {
                                            if ($_REQUEST['model_number'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Price:</label>
                                <select name="price" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['price'])) {
                                            if ($_REQUEST['price'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>
                        <!--END OF DIV 1-->
                        <!--DIV 2-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Color:</label>
                                <select name="color" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['color'])) {
                                            if ($_REQUEST['color'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Size:</label>
                                <select name="size" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['size'])) {
                                            if ($_REQUEST['size'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Weight:</label>
                                <select name="weight" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['weight'])) {
                                            if ($_REQUEST['weight'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--END OF DIV 2-->
                        <!--DIV 3-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Brand:</label>
                                <select name="brand" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['brand'])) {
                                            if ($_REQUEST['brand'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Manufacturer:</label>
                                <select name="manufacturer" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['manufacturer'])) {
                                            if ($_REQUEST['manufacturer'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Description:</label>
                                <select name="description" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['description'])) {
                                            if ($_REQUEST['description'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--END OF DIV 3-->
                    </div>
                    <!------NEW div 12 Begins--------------row2-->
                    <div class="col-md-12">
                        <!--Div 1 -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category:</label>
                                <select name="category" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['category'])) {
                                            if ($_REQUEST['category'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tags:</label>
                                <select name="tags" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['tags'])) {
                                            if ($_REQUEST['tags'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Shippable:</label>
                                <select name="shippable" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['shippable'])) {
                                            if ($_REQUEST['shippable'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--Div 1 ENDS-->
                        <!--DIV 2 Begins-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Currency:</label>
                                <select name="currency" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['currency'])) {
                                            if ($_REQUEST['currency'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Keywords:</label>
                                <select name="keywords" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['keywords'])) {
                                            if ($_REQUEST['keywords'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--Div 2 ENDS-->
                        <!--Div 3 Begins-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Year:</label>
                                <select name="year" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['year'])) {
                                            if ($_REQUEST['year'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Gender:</label>
                                <select name="gender" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['gender'])) {
                                            if ($_REQUEST['gender'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Item Status:</label>
                                <select name="item_status" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['item_status'])) {
                                            if ($_REQUEST['item_status'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>
                        <!--DIV 3--ENDS-->
                        <!----------NEW 12 Begins------row 3---------->
                        <div class="col-md-12">
                            <!--Div 1 -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Item Version:</label>
                                    <select name="item_version" class="form-control">
                                        <option value="--">--Select--</option>
                                        <?php
                                        $selected_f = "";
                                        foreach ($fields as $f) {
                                            if (isset($_REQUEST['item_version'])) {
                                                if ($_REQUEST['item_version'] == $f) {
                                                    $selected_f = 'selected="selected"';
                                                } else {
                                                    $selected_f = '';
                                                }
                                            }
                                            ?>
                                            <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Item Variation:</label>
                                    <select name="item_variation" class="form-control">
                                        <option value="--">--Select--</option>
                                        <?php
                                        $selected_f = "";
                                        foreach ($fields as $f) {
                                            if (isset($_REQUEST['item_variation'])) {
                                                if ($_REQUEST['item_variation'] == $f) {
                                                    $selected_f = 'selected="selected"';
                                                } else {
                                                    $selected_f = '';
                                                }
                                            }
                                            ?>
                                            <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!--Div 1 ENDS-->
                            <!--DIV 2 Begins-->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Similar Items:</label>
                                    <select name="similar_items" class="form-control">
                                        <option value="--">--Select--</option>
                                        <?php
                                        $selected_f = "";
                                        foreach ($fields as $f) {
                                            if (isset($_REQUEST['similar_items'])) {
                                                if ($_REQUEST['similar_items'] == $f) {
                                                    $selected_f = 'selected="selected"';
                                                } else {
                                                    $selected_f = '';
                                                }
                                            }
                                            ?>
                                            <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!--Div 2 ENDS-->
                            <!--Div 3 Begins-->
                            <div class="col-md-4">

                            </div>
                            <!--DIV 3--ENDS-->
                        </div>
                    </div>
                    <!----------NEW 12 Begins------row 4---------->
                    <div class="col-md-12">
                        <!--Div 1 -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Image 0:</label>
                                <select name="image_0" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_0'])) {
                                            if ($_REQUEST['image_0'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image 1:</label>
                                <select name="image_1" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_1'])) {
                                            if ($_REQUEST['image_1'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image 2:</label>
                                <select name="image_2" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_2'])) {
                                            if ($_REQUEST['image_2'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--Div 1 ENDS-->
                        <!--DIV 2 Begins-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Image 3:</label>
                                <select name="image_3" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_3'])) {
                                            if ($_REQUEST['image_3'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image 4:</label>
                                <select name="image_4" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_4'])) {
                                            if ($_REQUEST['image_4'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image 5:</label>
                                <select name="image_5" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_5'])) {
                                            if ($_REQUEST['image_5'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!--Div 2 ENDS-->
                        <!--Div 3 Begins-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Image 6:</label>
                                <select name="image_6" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_6'])) {
                                            if ($_REQUEST['image_6'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image 7:</label>
                                <select name="image_7" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_7'])) {
                                            if ($_REQUEST['image_7'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Image 8:</label>
                                <select name="image_8" class="form-control">
                                    <option value="--">--Select--</option>
                                    <?php
                                    $selected_f = "";
                                    foreach ($fields as $f) {
                                        if (isset($_REQUEST['image_8'])) {
                                            if ($_REQUEST['image_8'] == $f) {
                                                $selected_f = 'selected="selected"';
                                            } else {
                                                $selected_f = '';
                                            }
                                        }
                                        ?>
                                        <option value="<?= $f ?>" <?= $selected_f ?>><?= $f ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>
                        <!--DIV 3--ENDS-->
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="submit" value="Add Products to Pages" name="add_products" class="btn btn-success btn-xs"/>      
                                <input type="hidden" name="create_sub_pages" class="btn- btn-warning btn-xs" value="Choose Next field"/> 
                                <input type="hidden" value="<?= $page_id ?>" name="page_id"/>
                                <input type="hidden" value="create-cats" name="option"/>
                                <input type="hidden" name="cmd" value="edit_page"/>
                                <input type="hidden" name="PUUID" value="<?= $page_id ?>"/>
                                <input type="hidden" name="get_table_fields" value="xyz"/>
                                <input type="hidden" name="table" value="<?= isset($_REQUEST['table']) ? $_REQUEST['table'] : '' ?>"/>
                                <input type="hidden" name="prime_field" value="<?= isset($_REQUEST['prime_field']) ? $_REQUEST['prime_field'] : '' ?>"/>
                                <input type="hidden" name="column_choice" value="<?= isset($_REQUEST['column_choice']) ? $_REQUEST['column_choice'] : '' ?>"/>
                                <input type="hidden" name="choose_next" value="xyz"/>
                                <input type="hidden" name="secondary_field" value="<?= isset($_REQUEST['secondary_field']) ? $_REQUEST['secondary_field'] : '' ?>"/>
                                <input type="hidden" name="get_second_field"  value="Select"/> 

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </form>

        <?php
    }

    public function ProductPageForm($page_id) {

        if (isset($_REQUEST['update_product'])) {
            $this->DoUpdateProducts($page_id, $_REQUEST);
        }
        if (isset($_REQUEST['douploadimage'])) {
            $path = IMAGE_PATH . "page_id_" . $page_id . "_images/";
            $this->image_uploader->Do_Upload_images($_REQUEST['douploadimage'], $path, DATE_ADDED, $page_id, 10);
        }
        for ($img = 0; $img < 9; $img++) {
            if (isset($_REQUEST['delete_image_' . $img])) {
                $image_to_delete = $_REQUEST['image_' . $img];
                $image_extension = substr($image_to_delete, -3);
                $image_name = "image_" . $page_id . "_" . $img . "." . $image_extension;
                $field = "image_" . $img;
                $this->DeleteProductImages($page_id, trim($image_name), trim($image_to_delete), trim($field));
            }
        }

        if ($this->GetAllProductData($page_id) != NULL) {
            foreach ($this->sql_res as $product_data) {
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
                <form method="post" enctype="multipart/form-data">
                    <!--row 1 begins-->
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Item Name:</label>
                                <input type="text" name="item_name" value="<?= isset($_REQUEST['item_name']) ? $_REQUEST['item_name'] : trim($product_data['item_name']) ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Model Number:</label>
                                <input type="text" name="model_number" value="<?= isset($_REQUEST['model_number']) ? $_REQUEST['model_number'] : $product_data['model_number'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Price:</label>
                                <input type="text" name="price" value="<?= isset($_REQUEST['price']) ? $_REQUEST['price'] : $product_data['price'] ?>" class="form-control input-sm"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Color:</label>
                                <input type="text" name="color" value="<?= isset($_REQUEST['color']) ? $_REQUEST['color'] : $product_data['color'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Size:</label>
                                <input type="text" name="size" value="<?= isset($_REQUEST['size']) ? $_REQUEST['size'] : $product_data['size'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Weight:</label>
                                <input type="text" name="weight" value="<?= isset($_REQUEST['weight']) ? $_REQUEST['weight'] : $product_data['weight'] ?>" class="form-control input-sm"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Brand:</label>
                                <input type="text" name="brand" value="<?= isset($_REQUEST['brand']) ? $_REQUEST['brand'] : $product_data['brand'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Manufacturer:</label>
                                <input type="text" name="manufacturer" value="<?= isset($_REQUEST['manufacturer']) ? $_REQUEST['manufacturer'] : $product_data['manufacturer'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Description:</label>
                                <textarea class="form-control input-sm" name="description"><?= isset($_REQUEST['description']) ? $_REQUEST['description'] : $product_data['description'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <!--row 1 ends-->
                    <!--row 2 begins-->
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category:</label>
                                <input type="text" name="category" value="<?= isset($_REQUEST['category']) ? $_REQUEST['category'] : $product_data['category'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Tags:</label>
                                <input type="text" name="tags" value="<?= isset($_REQUEST['tags']) ? $_REQUEST['tags'] : $product_data['tags'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Shippable:</label>
                                <input type="text" name="shippable" value="<?= isset($_REQUEST['shippable']) ? $_REQUEST['shippable'] : $product_data['shippable'] ?>" class="form-control input-sm"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Currency:</label>
                                <select name="currency" class="form-control input-sm">
                                    <option value="--">Select Currency</option>
                                    <?php
                                    if ($this->GetWorldCurrencies() != NULL) {
                                        $selected_cur = '';
                                        foreach ($this->q_res as $currency) {
                                            if (isset($_REQUEST['currency'])) {
                                                if ($_REQUEST['currency'] == $currency['code']) {
                                                    $selected_cur = 'selected="selected"';
                                                } else {
                                                    $selected_cur = '';
                                                }
                                            }
                                            ?>
                                            <option value="<?= $currency['code'] ?>" <?= $selected_cur ?>><?= $currency['code'] ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Keywords:</label>
                                <input type="text" name="keywords" value="<?= isset($_REQUEST['keywords']) ? $_REQUEST['keywords'] : $product_data['keywords'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Year:</label>
                                <input type="text" name="year" value="<?= isset($_REQUEST['year']) ? $_REQUEST['year'] : $product_data['year'] ?>" class="form-control input-sm"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender:</label>
                                <input type="text" name="gender" value="<?= isset($_REQUEST['gender']) ? $_REQUEST['gender'] : $product_data['gender'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <input type="text" name="item_status" value="<?= isset($_REQUEST['item_status']) ? $_REQUEST['item_status'] : $product_data['item_status'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Version:</label>
                                <input type="text" name="item_version" value="<?= isset($_REQUEST['item_version']) ? $_REQUEST['item_version'] : $product_data['item_version'] ?>" class="form-control input-sm"/>
                            </div>
                        </div>
                    </div>
                    <!--row 2 ends-->
                    <!--row 3 begins-->
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Variation:</label>
                                <input type="text" name="item_variation" value="<?= isset($_REQUEST['item_variation']) ? $_REQUEST['item_variation'] : $product_data['item_variation'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Similar Items:</label>
                                <input type="text" name="similar_items" value="<?= isset($_REQUEST['similar_items']) ? $_REQUEST['similar_items'] : $product_data['similar_items'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Page Parent:</label>
                                <select name="page_parent" class="form-control input-sm">
                                    <option value="--">Select</option>
                                    <?php
                                    $page_parent = "";
                                    $page_parent = isset($_REQUEST['page_parent']) ? $_REQUEST['page_parent'] : $product_data['page_parent'];
                                    $this->Getparents($product_data['page_id']);
                                    foreach ($this->parents as $parents) {
                                        $selected_parent = '';
                                        if ((int) $page_parent == (int) $parents['id']) {
                                            $selected_parent = 'selected="selected"';
                                        } else {
                                            $selected_parent = '';
                                        }
                                        ?>
                                        <option value="<?= $parents['id'] ?>" <?= $selected_parent ?>><?= $parents['page_name'] ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Page ID:</label>
                                <input type="text" name="page_id" value="<?= $product_data['page_id'] ?>" class="form-control input-sm disabled" disabled="disabled"/>
                            </div>
                            <div class="form-group">
                                <label>Date Added:</label>
                                <input type="text" name="date_added" value="<?= $product_data['date_added'] ?>" class="form-control input-sm disabled" disabled="disabled"/>
                            </div>
                            <div class="form-group">
                                <label>Quantity on Hand:</label>
                                <input type="text" name="qty" value="<?= isset($_REQUEST['qty']) ? $_REQUEST['qty'] : $product_data['qty'] ?>" class="form-control input-sm disabled" />
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5><strong><i class="fa fa-image"></i>&nbsp;Upload Images</strong></h5>
                                </div>
                                <div class="panel-body">
                                    <input type="file" name="uploadimage"  class="btn btn-default btn-xs"/>

                                    <input type="submit" class="btn btn-danger btn-xs" name="douploadimage" value="Upload" style="margin-top: 10px;"/>
                                    <input type="hidden" name="option" value="u_image" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Wholesale Pricing</label>

                                <input type="text" name="wholesale_price" value="<?= isset($_REQUEST['wholesale_price']) ? $_REQUEST['wholesale_price'] : $product_data['wholesale_p'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Wholesale Qty On Hand</label>

                                <input type="text" name="wholesale_qty_on_hand" value="<?= isset($_REQUEST['wholesale_qty_on_hand']) ? $_REQUEST['wholesale_qty_on_hand'] : $product_data['wholesale_qty_on_hand'] ?>" class="form-control input-sm"/>
                            </div>
                            <div class="form-group">
                                <label>Wholesale Qty in Each Case</label>

                                <input type="text" name="wholesale_qty_in_case" value="<?= isset($_REQUEST['wholesale_qty_in_case']) ? $_REQUEST['wholesale_qty_in_case'] : $product_data['wholesale_qty_in_case'] ?>" class="form-control input-sm"/>
                            </div>
                        </div>

                    </div>

                    <!--row 3 ends-->
                    <!-- row 4 begins -->

                    <div class="col-md-12">
                        <hr/>
                    </div>
                    <!--row 5 begins-->
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Image 0:</label>
                                <?php
                                if ($product_data['image_0'] != "") {
                                    if ($this->CheckImageUrl($product_data['image_0'])) {
                                        $product_image_0 = $product_data['image_0'];
                                    } else {
                                        $product_image_0 = PROJECT_URL . $product_data['image_0'];
                                    }
                                    ?>
                                    <a href="<?= $product_image_0 ?>" target="_Blank">
                                        <img src="<?= $product_image_0 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                    </a>


                                    <input type="hidden" name="image_0" value="<?= $product_data['image_0'] ?>" />
                                    <br/>

                                    <input type="submit" value="Delete" name="delete_image_0" class="btn btn-danger btn-xs rock-del-btn"/>    

                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <label>Image 1:</label>
                                <?php
                                if ($product_data['image_1'] != "") {
                                    if ($this->CheckImageUrl($product_data['image_1'])) {
                                        $product_image_1 = $product_data['image_1'];
                                    } else {
                                        $product_image_1 = PROJECT_URL . $product_data['image_1'];
                                    }
                                    ?>
                                    <a href="<?= $product_image_1 ?>" target="_Blank">
                                        <img src="<?= $product_image_1 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                    </a>


                                    <input type="hidden" name="image_1" value="<?= $product_data['image_1'] ?>" />
                                    <br/>

                                    <input type="submit" value="Delete" name="delete_image_1" class="btn btn-danger btn-xs rock-del-btn"/>    

                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <label>Image 2:</label>
                                <?php
                                if ($product_data['image_2'] != "") {
                                    if ($this->CheckImageUrl($product_data['image_2'])) {
                                        $product_image_2 = $product_data['image_2'];
                                    } else {
                                        $product_image_2 = PROJECT_URL . $product_data['image_2'];
                                    }
                                    ?>
                                    <a href="<?= $product_image_2 ?>" target="_Blank">
                                        <img src="<?= $product_image_2 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                    </a>


                                    <input type="hidden" name="image_2" value="<?= $product_data['image_2'] ?>" />
                                    <br/>

                                    <input type="submit" value="Delete" name="delete_image_2" class="btn btn-danger btn-xs rock-del-btn"/>    

                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                    <?php
                                }
                                ?>
                            </div> 
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Image 3:</label>
                                <?php
                                if ($product_data['image_3'] != "") {
                                    if ($this->CheckImageUrl($product_data['image_3'])) {
                                        $product_image_3 = $product_data['image_3'];
                                    } else {
                                        $product_image_3 = PROJECT_URL . $product_data['image_3'];
                                    }
                                    ?>
                                    <a href="<?= $product_image_3 ?>" target="_Blank">
                                        <img src="<?= $product_image_3 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                    </a>


                                    <input type="hidden" name="image_3" value="<?= $product_data['image_3'] ?>" />
                                    <br/>

                                    <input type="submit" value="Delete" name="delete_image_3" class="btn btn-danger btn-xs rock-del-btn"/>    

                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <label>Image 4:</label>
                                <?php
                                if ($product_data['image_4'] != "") {
                                    if ($this->CheckImageUrl($product_data['image_4'])) {
                                        $product_image_4 = $product_data['image_4'];
                                    } else {
                                        $product_image_4 = PROJECT_URL . $product_data['image_4'];
                                    }
                                    ?>
                                    <a href="<?= $product_image_4 ?>" target="_Blank">
                                        <img src="<?= $product_image_4 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                    </a>


                                    <input type="hidden" name="image_4" value="<?= $product_data['image_4'] ?>" />
                                    <br/>

                                    <input type="submit" value="Delete" name="delete_image_4" class="btn btn-danger btn-xs rock-del-btn"/>    

                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <label>Image 5:</label>
                                <?php
                                if ($product_data['image_5'] != "") {
                                    if ($this->CheckImageUrl($product_data['image_5'])) {
                                        $product_image_5 = $product_data['image_5'];
                                    } else {
                                        $product_image_5 = PROJECT_URL . $product_data['image_5'];
                                    }
                                    ?>
                                    <a href="<?= $product_image_5 ?>" target="_Blank">
                                        <img src="<?= $product_image_5 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                    </a>


                                    <input type="hidden" name="image_5" value="<?= $product_data['image_5'] ?>" />
                                    <br/>

                                    <input type="submit" value="Delete" name="delete_image_5" class="btn btn-danger btn-xs rock-del-btn"/>    

                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                    <?php
                                }
                                ?>
                            </div> 

                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Image 6:</label>
                                <?php
                                if ($product_data['image_6'] != "") {
                                    if ($this->CheckImageUrl($product_data['image_6'])) {
                                        $product_image_6 = $product_data['image_6'];
                                    } else {
                                        $product_image_6 = PROJECT_URL . $product_data['image_6'];
                                    }
                                    ?>
                                    <a href="<?= $product_image_6 ?>" target="_Blank">
                                        <img src="<?= $product_image_6 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                    </a>


                                    <input type="hidden" name="image_6" value="<?= $product_data['image_6'] ?>" />
                                    <br/>

                                    <input type="submit" value="Delete" name="delete_image_6" class="btn btn-danger btn-xs rock-del-btn"/>    

                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                    <?php
                                }
                                ?>
                            </div> 
                            <div class="form-group">
                                <label>Image 7:</label>
                                <?php
                                if ($product_data['image_7'] != "") {
                                    if ($this->CheckImageUrl($product_data['image_7'])) {
                                        $product_image_7 = $product_data['image_7'];
                                    } else {
                                        $product_image_7 = PROJECT_URL . $product_data['image_7'];
                                    }
                                    ?>
                                    <a href="<?= $product_image_7 ?>" target="_Blank">
                                        <img src="<?= $product_image_7 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                    </a>


                                    <input type="hidden" name="image_7" value="<?= $product_data['image_7'] ?>" />
                                    <br/>

                                    <input type="submit" value="Delete" name="delete_image_7" class="btn btn-danger btn-xs rock-del-btn"/>    

                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    <label>Image 8:</label>
                                    <?php
                                    if ($product_data['image_8'] != "") {
                                        if ($this->CheckImageUrl($product_data['image_8'])) {
                                            $product_image_8 = $product_data['image_8'];
                                        } else {
                                            $product_image_8 = PROJECT_URL . $product_data['image_8'];
                                        }
                                        ?>
                                        <a href="<?= $product_image_8 ?>" target="_Blank">
                                            <img src="<?= $product_image_8 ?>" style="width:30% !important;" class="img-thumbnail"/>
                                        </a>


                                        <input type="hidden" name="image_8" value="<?= $product_data['image_8'] ?>" />
                                        <br/>

                                        <input type="submit" value="Delete" name="delete_image_8" class="btn btn-danger btn-xs rock-del-btn"/>    

                                        <?php
                                    } else {
                                        ?>
                                        <img src="<?= BE_IMAGES ?>noimagefound.jpg" title="no image" style="width:25% !important;"/>
                                        <?php
                                    }
                                    ?>
                                </div> 
                            </div>

                        </div>

                        <!--row 4 ends-->
                        <div class="col-md-12">

                        </div>
                        <div class="col-md-12">
                            <hr/>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="submit" name="update_product" value="Update Product" class="btn btn-primary" />
                                    <?php
                                    $this->MakeUrlForPreview($product_data['page_id'], $product_data['page_parent'], trim($product_data['item_name']));
                                    ?>
                                    <a href="<?= $this->preview_url ?>" title="veiw page" target="_Blank" class="btn btn-success">Preview Product</a>

                                </div>
                            </div>
                        </div>
                </form>
                <div class="col-md-12">
                    <div class="form-group" >
                        <?php
                        $sitemap_options = new CreatePageForm();
                        $sitemap_options->SiteMapSetup($page_id);
                        ?>
                    </div>
                </div>
                <!-- row 4 ends -->
                <?php
            }
        }
        ?>

        <?php
    }

    public function GetAllProductData($page_id) {

        $sql = "SELECT * FROM `pages_products` WHERE `page_id` = '" . $page_id . "'";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->sql_res[] = $row;
        }
        return $this->sql_res;
    }

    public function GetWorldCurrencies() {
        $sql = "SELECT DISTINCT `code` FROM world_currencies ORDER BY `code` ASC";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {


            $this->q_res[] = $row;
        }
        return $this->q_res;
    }

    public function GetParentName($parent_id) {
        $sql = "SELECT `page_name` FROM `pages` WHERE `id` ='" . $parent_id . "'";
        var_dump($sql);
        $result = $this->_mysqli->query($sql);
        while ($row[] = $result->fetch_array(MYSQLI_ASSOC)) {
            
        }
        return $row;
    }

    public function Getparents($page_id) {
        $sql = "SELECT `id`, `page_name` FROM `pages` WHERE `page_id` != '" . $page_id . "' ORDER BY page_parent ASC";
        $result = $this->_mysqli->query($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

            $this->parents[] = $row;
        }
        return $this->parents;
    }

    public function CheckImageUrl($image_url) {
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $image_url)) {

            return false;
        } else {
            return true;
        }
    }

    public function DoUpdateProducts($page_id, $data) {

        if (empty($data['item_name']) && $data['model_number'] == "" && $data['price'] == "" && $data['item_status'] == "" && empty($data['image_0'])) {
            $this->flag = 1;
            $message = array("1" => "Required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else if (empty($data['item_name']) || $data['model_number'] == "" || $data['price'] == "" || $data['item_status'] == "" || empty($data['image_0'])) {
            $this->flag = 1;
            $message = array("1" => "One or more of the required fields are empty.");
            array_push($this->messages, $message);
            $this->alert_class = "warning";
        } else {
            $item_name = $data['item_name'];
            $model_number = $data['model_number'];
            $price = $data['price'];
            $qty = $data['qty'];
            $color = $data['color'];
            $size = $data['size'];
            $weight = $data['weight'];
            $brand = $data['brand'];
            $manufacturer = $data['manufacturer'];
            $description = $data['description'];
            $category = $data['category'];
            $tags = $data['tags'];
            $shippable = $data['shippable'];
            $currecny = $data['currency'];
            $keywords = $data['keywords'];
            $year = $data['year'];
            $gender = $data['gender'];
            $item_status = $data['item_status'];
            $item_version = $data['item_version'];
            $item_variation = $data['item_variation'];
            $similar_items = $data['similar_items'];
            $page_parent = $data['page_parent'];
            $wholesale_price = $data['wholesale_price'];
            $wholesale_qoh = $data['wholesale_qty_on_hand'];
            $wholesale_qec = $data['wholesale_qty_in_case'];
            $things_to_update = array();
            /*
             * Compare the old data and make descision to update or not
             */
            $get_data_rows = "SELECT * FROM `pages_products` WHERE `page_id` = '" . $page_id . "'";
            $get_data_rows_res = $this->_mysqli->query($get_data_rows);
            $updates = array();
            while ($row = $get_data_rows_res->fetch_array(MYSQLI_ASSOC)) {

                if (trim($item_name) != trim($row['item_name'])) {
                    $updates['item_name'] = $item_name;
                }
                if (trim($model_number) != trim($row['model_number'])) {
                    $updates['model_number'] = $model_number;
                }
                if (trim($price) != trim($row['price'])) {
                    $updates['price'] = $price;
                }
                if (trim($qty) != trim($row['qty'])) {
                    $updates['qty'] = $qty;
                }
                if (trim($color) != trim($row['color'])) {
                    $updates['color'] = $color;
                }
                if (trim($size) != trim($row['size'])) {
                    $updates['size'] = $size;
                }
                if (trim($weight) != trim($row['weight'])) {
                    $updates['weight'] = $weight;
                }
                if (trim($brand) != trim($row['brand'])) {
                    $updates['brand'] = $brand;
                }
                if (trim($manufacturer) != trim($row['manufacturer'])) {
                    $updates['manufacturer'] = $manufacturer;
                }
                if (trim($description) != trim($row['description'])) {
                    $updates['description'] = mysqli_real_escape_string($this->_mysqli, $description);
                }
                if (trim($category) != trim($row['category'])) {
                    $updates['category'] = $category;
                }
                if (trim($tags) != trim($row['tags'])) {
                    $updates['tags'] = $tags;
                }
                if (trim($shippable) != trim($row['shippable'])) {
                    $updates['shippable'] = $shippable;
                }
                if (trim($currecny) != trim($row['currency'])) {
                    $updates['currency'] = $currecny;
                }
                if (trim($keywords) != trim($row['keywords'])) {
                    $updates['keywords'] = $keywords;
                }
                if (trim($year) != trim($row['year'])) {
                    $updates['year'] = $year;
                }
                if (trim($gender) != trim($row['gender'])) {
                    $updates['gender'] = $gender;
                }
                if (trim($item_status) != trim($row['item_status'])) {
                    $updates['item_status'] = $item_status;
                }
                if (trim($item_version) != trim($row['item_version'])) {
                    $updates['item_version'] = $item_version;
                }
                if (trim($item_variation) != trim($row['item_variation'])) {
                    $updates['item_variation'] = $item_variation;
                }
                if (trim($similar_items) != trim($row['similar_items'])) {
                    $updates['similar_items'] = $similar_items;
                }
                if (trim($wholesale_price) != trim($row['wholesale_p'])) {
                    $updates['wholesale_p'] = $wholesale_price;
                }
                if (trim($wholesale_qoh) != trim($row['wholesale_qty_on_hand'])) {
                    $updates['wholesale_qty_on_hand'] = $wholesale_qoh;
                }
                if (trim($wholesale_qec) != trim($row['wholesale_qty_in_case'])) {
                    $updates['wholesale_qty_in_case'] = $wholesale_qec;
                }
                if (trim($page_parent) != trim($row['page_parent'])) {
                    $updates['page_parent'] = $page_parent;
                }
            }
            array_push($things_to_update, $updates);


            /*
             * Now update
             */
            foreach ($things_to_update as $field => $value) {
                if (!empty($value)) {
                    foreach ($value as $f => $v) {

                        $update_data = "UPDATE `pages_products` SET `" . $f . "` = '" . $v . "' WHERE `page_id` = '" . $page_id . "'";

                        $update_data_res = $this->_mysqli->query($update_data);
                        $this->flag = 1;
                        $message = array("1" => "<strong>" . $f . "</strong> was updated");
                        $this->alert_class = "success";
                        array_push($this->messages, $message);
                    }
                    $update_data_pages = "UPDATE `pages` SET `page_name` = '" . $item_name . "', `page_parent` = '" . $page_parent . "' WHERE `page_id` = '" . $page_id . "'";
                    $update_data_pages_res = $this->_mysqli->query($update_data_pages);

                    $update_page_meta_data = "UPDATE `page_meta_data` SET `page_title` = '" . $item_name . "', `meta_data` = '" . $keywords . "' `description` = `" . $description . "` WHERE `page_id` = '" . $page_id . "'";
                    $update_page_meta_data_res = $this->_mysqli->query($update_page_meta_data);
                } else {
                    $this->flag = 1;
                    $message = array("1" => "There is nothing to update");
                    $this->alert_class = "warning";
                    array_push($this->messages, $message);
                }
            }
        }
    }

    public function DeleteProductImages($page_id, $image_name, $image_url, $field) {
        /*
         * First check if the image is local meaning was uploaded to our server or it is comming from somewhere else
         */
        if ($check_image_url = $this->CheckImageUrl($image_url)) {

            /*
             * Just update the pages_products table
             */
            $delete_image_url = "UPDATE `pages_products` SET `" . $field . "` ='' WHERE `page_id` = '" . $page_id . "' AND `" . $field . "` = '" . $image_url . "'";

            $delete_image_url_res = $this->_mysqli->query($delete_image_url);
        } else {
            /*
             * if image is native 
             */
            $delete_image_url = "UPDATE `pages_products` SET `" . $field . "` ='' WHERE `page_id` = '" . $page_id . "' AND `" . $field . "` = '" . $image_url . "'";

            $delete_image_url_res = $this->_mysqli->query($delete_image_url);

            $get_image_path = "SELECT `image_path`, `id` FROM `page_images` WHERE `image_name` = '" . $image_name . "' AND `page_id` = '" . $page_id . "'";

            $get_image_path_res = $this->_mysqli->query($get_image_path);

            while ($row = $get_image_path_res->fetch_array(MYSQLI_ASSOC)) {

                $image_path = FE_IMAGES . "page_id_" . $page_id . "_images";

                unlink($image_path . "/" . $image_name);
                if ($this->image_uploader->is_dir_empty($image_path)) {
                    rmdir($image_path);
                }

                $sql = "DELETE FROM `page_images` WHERE `id` = '" . $row['id'] . "' AND `page_id` = '" . $page_id . "'";

                $del_result = $this->_mysqli->query($sql);
            }
        }
    }

    public function MakeUrlForPreview($page_id, $page_parent, $page_name) {
        /*
         * url maker
         * if page parent is zero then it is already a top level page
         * if not then get the parent page name 
         */
        $sql = "SELECT `id` FROM `pages` WHERE `page_id` = '" . $page_id . "'";
        $sql_res = $this->_mysqli->query($sql);
        while ($product_page_id = $sql_res->fetch_array(MYSQLI_ASSOC)) {

            $this->product_page_id = $product_page_id['id'];
        }

        if ($page_parent == 0) {

            /*
             * Check for its page alias and url type
             */
            $select_page_url = "SELECT `page_alias` FROM `page_alias` WHERE `page_id` = '" . $page_id . "'";
            $select_page_url_res = $this->_mysqli->query($select_page_url);
            $num_rows_alias = $select_page_url_res->num_rows;
            if ($num_rows_alias > 0) {
                while ($row = $select_page_url_res->fetch_array(MYSQLI_ASSOC)) {

                    /*
                     * Make the url from url option
                     */
                    if ($row['page_alias'] != "") {
                        $url = "/" . $row['page_alias'] . "/" . $this->product_page_id;
                        $this->preview_url = $url;
                    } else {
                        /*
                         * make the page name kosher and then add it as a url
                         */
                        $no_spaces = str_replace(" ", "-", $page_name);
                        $no_upper_case = strtolower($no_spaces);
                        $no_ands = str_replace("&", "and", $no_upper_case);
                        $no_special_chars = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);
                        $this->preview_url = "";
                        $generic_url = "/" . $no_special_chars . "/" . $this->product_page_id;
                        $this->preview_url = $generic_url;
                    }
                }
            } else {

                $this->flag = 1;
                $message = array("1" => "Error Fro Url maker #1001");
                $this->alert_class = "warning";
                array_push($this->messages, $message);
            }
        } else if ($page_parent != 0) {
            /*
             * Check again in the url options to see if it has been assigned an alias
             */
            $select_child_page_url = "SELECT `page_alias` FROM `page_alias` WHERE `page_id` = '" . $page_id . "'";
            $select_child_page_url_res = $this->_mysqli->query($select_child_page_url);
            $num_rows_alias_for_child = $select_child_page_url_res->num_rows;
            if ($num_rows_alias_for_child > 0) {
                while ($child_row = $select_child_page_url_res->fetch_array(MYSQLI_ASSOC)) {

                    /*
                     * Make the url from url option
                     */
                    if ($child_row['page_alias'] != "") {
                        $url = "/" . $child_row['page_alias'] . "/" . $this->product_page_id;
                        $this->preview_url = $url;
                    } else {
                        /*
                         * Find all the parents and build a url like: Parent/Child/Grand-Child/...
                         */
                        $this->FindAllPageParents($page_parent);



                        $find_parent_name = array_reverse($this->url_parents);
                        $a = array();
                        for ($i = 0; $i < count($find_parent_name); $i++) {
                            $new_parent_url = $find_parent_name[$i]['page_name'];
                            array_push($a, $new_parent_url);
                        }
                        $parent_url = implode("/", $a);
                        $clear_parent_spaces = str_replace(" ", "-", $parent_url);
                        $remove_parent_ands = str_replace("&", "and", $clear_parent_spaces);
                        $parent_url = '/' . preg_replace('/[^a-zA-Z0-9,-\/]/', '-', strtolower($remove_parent_ands));
                        $clear_url_s = str_replace(" ", "-", $page_name);
                        $remove_slahes = str_replace("/", "-", $clear_url_s);
                        $remove_long_ands = str_replace("&", "and", $remove_slahes);
                        $remove_apostrophies = str_replace("'", "-", $remove_long_ands);
                        $remove_par_left = str_replace("(", "", $remove_apostrophies);
                        $remove_par_right = str_replace(")", "", $remove_par_left);
                        $url = strtolower($parent_url . '/' . preg_replace('/[^a-zA-Z0-9,-\/]/', '-', $remove_par_right));
                        $clean = $url . "/" . $this->product_page_id;
                        $this->preview_url = $clean;
                    }
                }
            }
        }
    }

    public function FindAllPageParents($page_parent) {
        $find_all_parents = "SELECT `page_parent`, `id`, `page_name`, `page_id` FROM `pages` WHERE `id` = '" . $page_parent . "' ORDER BY id ASC";
        $find_all_parents_res = $this->_mysqli->query($find_all_parents);
        while ($row = $find_all_parents_res->fetch_array(MYSQLI_ASSOC)) {
            $this->url_parents[] = $row;
            $this->FindAllPageParents($row['page_parent']);
        }
        return $this->url_parents;
    }

}
