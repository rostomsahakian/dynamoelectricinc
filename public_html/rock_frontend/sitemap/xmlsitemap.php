<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of xmlsitemap
 *
 * @author rostom
 */
class xmlsitemap {

    public $pages;
    private $_mysqli;
    private $_db;
    public $children;
    public $allPages = array();
    public $queries;

    public function __construct() {
        $this->_db = DB_Connect::getInstance();
        $this->_mysqli = $this->_db->getConnection();
        $this->queries = new Queries();
    }

    /*
     * First get all pages (top level)
     */

    public function GetAllPages() {
        $sql = "SELECT * FROM `pages` WHERE `page_parent` = '0' ORDER BY `page_type` ASC";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {

            while ($rows = $result->fetch_array(MYSQLI_ASSOC)) {

                $this->pages[] = $rows;
            }
        }
    }

    public function SetAllPages() {
        return $this->pages;
    }

    public function GetSubs() {
        $this->children = NULL;
        $this->pages = NULL;

        $this->GetAllPages();
        $top_pages = $this->SetAllPages();
        $array_page = array();

        foreach ($top_pages as $toppage) {


            $this->GetAllChildPages($toppage['id']);
            if ($this->children != NULL) {

                $res = array_merge($this->pages, $this->children);
            }
        }
        foreach ($res as $r) {
            $no_spaces = str_replace(" ", "-", $r['page_name']);
            $no_upper_case = strtolower($no_spaces);
            $no_ands = str_replace("&", "and", $no_upper_case);
            $no_special_chars = preg_replace('/[^a-zA-Z0-9,-]/', "-", $no_ands);

            $build_array = array(
                array('permalink' => WEBSITE_URL . $no_special_chars . "/" . $r['id'], 'updated' => $r['date_created'], 'changefreq' => $r['changefreq'], 'priority' => $r['priority']),
            );
            array_push($this->allPages, $build_array);
        }
//        echo "<br/>";
//        var_dump($this->allPages);
    }

    public function SetXMLData() {
        return $this->allPages;
    }

    /*
     * After getting all the pages check to see which pages have 
     * children if they have children then get their info as well 
     * from products_pages table as well
     */

    public function GetAllChildPages($parent) {

        /*
         * get its children
         * These are pages that are children them selves'
         * we need to check and see if they themseleves have any children
         * 
         */

        $getChildren = "SELECT * FROM `pages` WHERE `page_parent` = '" . $parent . "' ORDER BY `id` ASC ";
        $getChildren_res = $this->_mysqli->query($getChildren);
        if ($getChildren_res->num_rows > 0) {
            while ($child = $getChildren_res->fetch_array(MYSQLI_ASSOC)) {

                $this->children[] = $child;

                $this->GetAllChildPages($child['id']);
            }
            return $this->children;
            // 
        }
    }

    public function GetALLPagesLinks() {


        $categories = array();

        $get_parents = "SELECT * FROM `pages` WHERE `page_parent` = '0' ORDER BY `page_order` ASC";
        $get_parents_res = $this->_mysqli->query($get_parents);
        $num_rows = $get_parents_res->num_rows;
        if ($num_rows > 0) {
            while ($get_parents_rows = $get_parents_res->fetch_array(MYSQLI_ASSOC)) {
                $this->parents[] = $get_parents_rows;
                foreach ($this->parents as $parent) {


                    $category = array();

                    $category['id'] = $parent['id'];
                    $category['name'] = $parent['page_name'];
                    $category['parent'] = $parent['page_parent'];
                    $category['type'] = $parent['page_type'];
                    $category['page_id'] = $parent['page_id'];
                    if ($this->HasChild($parent['id'])) {
                        $category['sub_categories'] = array();
                    }

                    $get_children = "SELECT * FROM `pages` WHERE `page_parent` ='" . $parent['id'] . "' ORDER BY `page_order` ASC";
                    $get_children_res = $this->_mysqli->query($get_children);
                    $get_child_num_rows = $get_children_res->num_rows;
                    if ($get_child_num_rows > 0) {
                        while ($get_children_rows = $get_children_res->fetch_array(MYSQLI_ASSOC)) {
                            $this->children[] = $get_children_rows;
                            // if ($parent['id'] != "5") {
                            foreach ($this->children as $child) {
                                if ($parent['id'] == $child['page_parent']) {

                                    $subcat = array();
                                    $subcat['id'] = $child['id'];
                                    $subcat['name'] = $child['page_name'];
                                    $subcat['parent'] = $child['page_parent'];
                                    $subcat['page_id'] = $child['page_id'];
                                }
                            }
                            array_push($category['sub_categories'], $subcat);
                        }
                    }
                }
                array_push($categories, $category);
            }

            return $categories;
        }
    }

    public function MakeTheTree($data) {
        /*
         * Get all top level pages
         */
        $sql = "SELECT * FROM `pages` WHERE `page_parent` = '" . $data . "' ORDER BY 'page_parent' ASC";
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                if ($this->CheckIfHasChild($row['id'])) {
                    $this->MakeTheTree($row['id']);
                    var_dump($row['page_name']);
                }
            }
        }
    }

    public function CheckIfHasChild($data) {
        $sql = "SELECT * FROM `pages` WHERE `page_parent` ='" . $data . "' AND `page_parent` !='0'";
        //var_dump($sql);
        $result = $this->_mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->CheckIfHasChild($row['id']);
            }
            return true;
        } else {
            return FALSE;
        }
    }

    /*
     * Checks to see if the given value has any children retuns true or false
     */

    public function HasChild($parent_id) {

        $data_to_ftech = array(
            "table" => "pages",
            "field" => "page_parent",
            "value" => $parent_id
        );


        $this->queries->_res = NULL;
        $get_children = $this->queries->findChildren($data_to_ftech, $option = 2);
        $get_children = $this->queries->DoReturn();
        $this->_children[] = $get_children;
        if (count($get_children) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function CreateXMLSiteMap() {
        $xml = new DomDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        // creating base node
        $urlset = $xml->createElement('urlset');
        $urlset->appendChild(
                new DomAttr('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9')
        );
        // appending it to document
        $xml->appendChild($urlset);

        $this->SetXMLData();
        // building the xml document with your website content
        foreach ($this->allPages as $entry) {
            foreach ($entry as $e) {

                //Creating single url node
                $url = $xml->createElement('url');

                //Filling node with entry info
                $url->appendChild($xml->createElement('loc', $e['permalink']));
                $url->appendChild($lastmod = $xml->createElement('lastmod', $e['updated']));
                $url->appendChild($changefreq = $xml->createElement('changefreq', $e['changefreq']));
                $url->appendChild($priority = $xml->createElement('priority', $e['priority']));

                // append url to urlset node
                $urlset->appendChild($url);
            }
        }

        $xml->save("data.xml");
    }

}
