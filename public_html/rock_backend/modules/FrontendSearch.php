<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FrontendSearch
 *
 * @author rostom
 */
class FrontendSearch {

    private $_mysqli;
    private $_db;
    public $_search_res;
    public $search_parents;
    public $result_count = 0;
    public $_page;
    public $_limit;
    public $_results;
    public $_total;
    public $res;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
    }

    public function DoSearch($keyword) {
        if ($keyword != '') {

            $page_id = isset($_REQUEST['p']) ? $_REQUEST['p'] : "1";
            $this->_page = $page_id;
            $this->_limit = 6;
            $l = ($this->_page - 1) * $this->_limit;

            $get_total = "SELECT DISTINCT `item_name` AS item_name, `price`, `page_parent`, `image_0`, `price`, `tags`, `page_id` FROM `pages_products` WHERE `item_name` LIKE '%" . $keyword . "%'"
                    . " OR `model_number` LIKE '" . $keyword . "%' "
                    . " OR `tags` LIKE '%" . $keyword . "%' "
                    . " OR `description` LIKE '%" . $keyword . "%'";
            $total_result = $this->_mysqli->query($get_total);
            $this->_total = $total_result->num_rows;

            $sql = "SELECT DISTINCT `item_name` AS item_name, `price`, `page_parent`, `image_0`, `price`, `tags` , `page_id` FROM `pages_products` WHERE `item_name` LIKE '%" . $keyword . "%'"
                    . " OR `model_number` LIKE '" . $keyword . "%' "
                    . " OR `tags` LIKE '%" . $keyword . "%' "
                    . " OR `description` LIKE '%" . $keyword . "%'  LIMIT {$l}, {$this->_limit}";
            $result = $this->_mysqli->query($sql);
            $num_result = $result->num_rows;
            $this->result_count = $num_result;

            if ($num_result > 0) {
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                    $this->_search_res[] = $row;
                } return $this->_search_res;
            }
        } else {
            $this->_search_res = NULL;
        }
    }

    public function Getresults() {
        ?>
        <div class="container rock-main-container">
            <h4 style="text-transform: uppercase;">Showing search result for <b><?= $_REQUEST['keyword'] ?></b>, Result Count: <?= $this->ReturnTotal() ?>  </h4>
            <?php
            if ($this->ReturnTotal() == 0) {
                ?>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <p><i>No results matched <b><?= $_REQUEST['keyword']; ?></b>. Please try again with another keyword.</i></p>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="col-md-12">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="rock-top-filter-container">
                        <div class="row">
                            <div class="rock-pagination">
                                <?php
                                $k = $this->createLinks(2, "pagination pagination-sm");
                                echo $k;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="col-md-2"></div>
                <div class="col-md-10">
                    <div class="col-12">
                        <?php
                        if ($this->RetPageData() != NULL) {
                            foreach ($this->RetPageData() as $search_result) {
                                $this->FindParents($search_result['page_parent']);
                                ?>

                                <div class="col-md-4 rock-item-image-holder">

                                    <div class="row">
                                        <?php
                                        $item_no_spaces = str_replace(" ", "-", $search_result['item_name']);
                                        $item_no_upper_case = strtolower($item_no_spaces);
                                        $item_no_ands = str_replace("&", "and", $item_no_upper_case);
                                        $clean_item_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $item_no_ands);


                                        foreach ($this->search_parents as $parent) {
                                            
                                        }
                                        $page_parent_no_spaces = str_replace(" ", "-", $parent['page_name']);
                                        $no_upper_case = strtolower($page_parent_no_spaces);
                                        $no_ands = str_replace("&", "and", $no_upper_case);
                                        $clean_parent_name = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);
                                        $url = "/" . $clean_parent_name . "/" . $clean_item_name . "/" . $search_result['page_id'];
                                        ?>

                                        <a href="/<?= $clean_parent_name ?>" class="rock-brand-in-box"><?= $parent['page_name'] ?></a>
                                        <a  href="<?= $url ?>"  class="rock-product-link">
                                            <span class="rollover" >                                                                            
                                            </span>
                                        </a>
                                        <?php ?>
                                        <img src="<?= $search_result['image_0'] ?>" class="rock-item-image">

                                    </div>
                                    <div class="row rock-item-captions">

                                        <p class="rock-item-name"><a href="<?= $url ?>" > <?= $search_result['item_name'] ?></a><p> 
                                            <!--NOT USED FOR THE LINE-->
                                        <p class="rock-item-name">REG PRICE: <?= $search_result['price'] ?></p>
                                        <?php
                                        if ($search_result['tags'] != "") {

                                            $tags = explode(",", $search_result['tags']);
                                            ?>
                                            <p><i class="fa fa-tags" aria-hidden="true"></i>:&nbsp;
                                                <?php
                                                for ($i = 0; $i < count($tags); $i++) {
                                                    ?>
                                                    <a href="/?keyword=<?= trim($tags[$i]) ?>&cmd=search"><?= $tags[$i] ?></a><span>,&nbsp;</span>
                                                    <?php
                                                }
                                                ?>
                                            </p>
                                            <?php
                                        }
                                        ?>

                                    </div>
                                </div>

                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 20px; min-height: 40px;"> 

            </div>
        </div>

        <?php
    }

    public function FindParents($parents) {

        $sql = "SELECT `id`,`page_name` , `page_parent` FROM `pages` WHERE `id` = '" . $parents . "'";
        $result = $this->_mysqli->query($sql);
        $num_rows = $result->num_rows;
        if ($num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                //$this->FindParents($row['page_parent']);
                $this->search_parents[] = $row;
            }
            return $this->search_parents;
        }
    }

    public function createLinks($links, $list_class) {
        if ($this->ReturnLimit() == 'all') {
            return '';
        }
        if ($this->ReturnTotal() != 0) {
            $last = ceil($this->ReturnTotal() / $this->ReturnLimit());

            $start = ( ( $this->ReturnPage() - $links ) > 0 ) ? $this->ReturnPage() - $links : 1;
            $end = ( ( $this->ReturnPage() + $links ) < $last ) ? $this->ReturnPage() + $links : $last;

            $html = '<ul class="' . $list_class . '">';

            $class = ( $this->ReturnPage() == 1 ) ? "disabled" : "";
            $disabled = ( $this->ReturnPage() == 1 ) ? "onclick='return false'" : "";
            $html .= '<li class="' . $class . '"><a href="?keyword=' . $_REQUEST['keyword'] . '&cmd=search&p=' . ( $this->ReturnPage() - 1 ) . '" ' . $disabled . '><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></a></li>';

            if ($start > 1) {
                $html .= '<li><a href="?p=1">1</a></li>';
                $html .= '<li class="disabled"><span>...</span></li>';
            }

            for ($i = $start; $i <= $end; $i++) {
                $class = ( $this->ReturnPage() == $i ) ? "active" : "";
                $html .= '<li class="' . $class . '"><a href="?keyword=' . $_REQUEST['keyword'] . '&cmd=search&p=' . $i . '">' . $i . '</a></li>';
            }

            if ($end < $last) {
                $html .= '<li class="disabled"><span>...</span></li>';
                $html .= '<li><a href="?keyword=' . $_REQUEST['keyword'] . '&cmd=search&p=' . $last . '">' . $last . '</a></li>';
            }

            $class = ( $this->ReturnPage() == $last ) ? "disabled" : "";
            $disabled = ( $this->ReturnPage() == $last ) ? "onclick='return false'" : "";
            $html .= '<li class="' . $class . '"><a href="?keyword=' . $_REQUEST['keyword'] . '&cmd=search&p=' . ( $this->ReturnPage() + 1 ) . '" ' . $disabled . '><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a></li>';

            $html .= '</ul>';
        } else {
            $html = "";
        }
        return $html;
    }

    public function ReturnTotal() {
        return $this->_total;
    }

    public function ReturnLimit() {
        return $this->_limit;
    }

    public function ReturnPage() {
        return $this->_page;
    }

    public function RetPageData() {
        return $this->_search_res;
    }

}
