<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author    : Dabeer ul Hasan
 *  Email     : dabeer.hasan@gmail.com
 *  For       : Stock Manager Advance
 *  Web       : http://rholab.net
 *  ==============================================================================
*/

class Olpermart_API
{

    /**
     * Reference to the CodeIgniter instance
     *
     * @var object
     */
    protected $CI;
    public $conn = "";

    public $dbServerName = "";
    public $dbUsername = "";
    public $dbPassword = "";
    public $dbName = "";

    public function __construct()
    {

        $this->CI = &get_instance();
        $this
            ->CI
            ->load
            ->helper('url');
        $this
            ->CI
            ->config
            ->item('base_url');
        $this
            ->CI
            ->load
            ->database();
        $this
            ->CI
            ->load
            ->database();
    }

    public function expireBatches()
    {
        $arr = [

            'B#-18122020ADy',
            'B#-NOV-20-4-27JAN21D02',
            'B#-18122020ADJ',
            'B#-18122020A',
            'B#-18122020ADJ',
            'B#-18122020ADw',
            'B#-NOV-20-6-11JAN21D01',
            'B#-SEPT-20-11-08DEC20WM1',
            'B#-18122020ADJ',
            'B#-29112020',
            'B#-NOV-20-6-18JAN21L01',
            'B#-18122020ADJ',
            'B#-28032021A'
        ];

        return $arr;
    }

    public function expireProductSetup($products)
    {

        // olpermart Credential
        $dbServerName = "208.109.167.45";
        $dbUsername = "olper_lhr";
        $dbPassword = "x2a~c#;E[4V0";
        $dbName = "o_karachi";

        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);


        $batches = $this->expireBatches();

        $qty_balance_array = [];
        echo '<pre>';
        foreach ($products as $key => $val) {
            $p_id = $val['product_id'];

            foreach ($batches as $key => $item) {
                $qty_balance = $this->CI->db->query("SELECT `quantity_balance` FROM `sma_purchase_items` WHERE product_id= '$p_id' AND batch = '$item'")->result_array();
                array_push($qty_balance_array, $qty_balance);
            }

            $company_names = $this->CI->db->query("SELECT `company_prices_and_names` FROM `sma_products` WHERE id= '$p_id'")->result_array();
            $expire_karachi_id  = json_decode($company_names[0]['company_prices_and_names'])->ExpireKarachi;
        }
        $qty1 = array_values(array_filter($qty_balance_array));
        $qty_balance = floor($qty1[0][0]['quantity_balance']);
        // echo $expire_karachi_id;

        if ($conn) {

            $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . $qty_balance . ' WHERE `wp_postmeta`.`post_id` = ' . $expire_karachi_id . ' and `wp_postmeta`.`meta_key` = "_stock"';

            $check_remain_qty = $qty_balance;
            if ($check_remain_qty == '0') {

                $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . $expire_karachi_id . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

                if ($conn->query($query_out_of_stock) === true) {
                }
            } else {
                $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . $expire_karachi_id . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

                if ($conn->query($query_in_stock) === true) {
                }
            }

            if ($conn->query($sql) === true) {
                // echo "Record updated successfully";
            } else {
                // echo "Error updating record: " . $conn->error;
            }

            $conn->close();
        }

        return $qty_balance;
    }

    public function get_update_product_olpermart_api($data, $products)
    {

        // olpermart Credential
        $dbServerName = "208.109.167.45";
        $dbUsername = "olper_lhr";
        $dbPassword = "x2a~c#;E[4V0";
        $dbName = "o_karachi";

        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);


        if ($conn) {

            foreach ($products as $key => $value) {

                $qty_balance_expiry = $this->expireProductSetup($products);

                $q = $this
                    ->CI
                    ->db
                    ->query("SELECT sma_products.*, sma_warehouses_products.* FROM `sma_warehouses_products` LEFT JOIN sma_products ON sma_warehouses_products.product_id = sma_products.id
                    WHERE sma_warehouses_products.warehouse_id = 1 and sma_warehouses_products.product_id = '" . $products[$key]['product_id'] . "'");

                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $result[] = $row;

                        if (json_decode($row->company_prices_and_names)->OlpermartKHI) {
                            $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . floor(($row->quantity - $qty_balance_expiry) / $row->pack_size) . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock"';

                            $check_remain_qty = floor(($row->quantity - $qty_balance_expiry) / $row->pack_size);
                            if ($check_remain_qty == '0') {

                                $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                //echo $query_out_of_stock;
                                if ($conn->query($query_out_of_stock) === true) {
                                }
                            } else {
                                $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

                                if ($conn->query($query_in_stock) === true) {
                                }
                            }

                            if ($conn->query($sql) === true) {
                                // echo "Record updated successfully";
                            } else {
                                // echo "Error updating record: " . $conn->error;
                            }
                        }
                    }
                }
            }

            $conn->close();
        }
    }

    public function get_update_product_olpermart_api_after_delete($id)
    {

        // olpermart Credential Karachi
        $dbServerName = "208.109.167.45";
        $dbUsername = "olper_lhr";
        $dbPassword = "x2a~c#;E[4V0";
        $dbName = "o_karachi";

        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

        if ($conn) {
            $q = $this
                ->CI
                ->db
                ->query("SELECT * FROM `sma_purchase_items` where purchase_id = $id");
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $result[] = $row;

                    $q1 = $this
                        ->CI
                        ->db
                        ->query("SELECT sma_products.*, sma_warehouses_products.* FROM `sma_warehouses_products` LEFT JOIN sma_products ON sma_warehouses_products.product_id = sma_products.id
                        WHERE sma_warehouses_products.warehouse_id = 1 and sma_warehouses_products.product_id = '" . $row->product_id . "'");
                    if ($q1->num_rows() > 0) {
                        foreach (($q1->result()) as $row) {
                            $result[] = $row;

                            //print_r(json_decode($row->company_prices_and_names)->OlpermartKHI) . "<br>";
                            if (json_decode($row->company_prices_and_names)
                                ->Olpermart
                            ) {
                                $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . floor($row->quantity / $row->pack_size) . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock"';

                                $check_remain_qty = floor($row->quantity / $row->pack_size);
                                if ($check_remain_qty == '0') {

                                    $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                    //echo $query_out_of_stock;
                                    if ($conn->query($query_out_of_stock) === true) {
                                    }
                                } else {
                                    $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

                                    if ($conn->query($query_in_stock) === true) {
                                    }
                                }

                                //echo $sql."<br>";
                                if ($conn->query($sql) === true) {
                                    // echo "Record updated successfully";
                                } else {
                                    // echo "Error updating record: " . $conn->error;
                                }
                            }
                        }

                        //return $data;

                    }
                }
            }

            $conn->close();

            //echo "<pre>";
            //print_r($result);
            //die();
            //exit();

        }
    }

    public function get_update_product_asanbuy_api_after_sale_delete($id)
    {

        // olpermart Credential
        $dbServerName = "208.109.167.45";
        $dbUsername = "olper_lhr";
        $dbPassword = "x2a~c#;E[4V0";
        $dbName = "o_karachi";

        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);
        if ($conn) {
            $q = $this
                ->CI
                ->db
                ->query("SELECT * FROM `sma_sale_items` where sale_id = $id");

            // Phele sale ID sy saari product le aye

            // print_r($q->result());

            // die;


            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {


                    $result[] = $row;



                    $QUANTITY_ = $row->quantity;

                    $q1 = $this
                        ->CI
                        ->db
                        ->query("SELECT sma_products.*, sma_warehouses_products.* FROM `sma_warehouses_products` LEFT JOIN sma_products ON sma_warehouses_products.product_id = sma_products.id
                        WHERE sma_warehouses_products.warehouse_id = 1 and sma_warehouses_products.product_id = '" . $row->product_id . "'");

                    // sale id ki products
                    // print_r($q1);
                    // die;

                    if ($q1->num_rows() > 0) {
                        foreach (($q1->result()) as $row) {
                            $result[] = $row;

                            //  echo json_decode($row->company_prices_and_names)->OlpermartKHIKHI;
                            // print_r($row);
                            // echo '<p>' . $row->name . '</pre>';
                            // echo '<p>' . $row->company_prices_and_names . '</p>';
                            // echo '<p>' . json_decode($row->company_prices_and_names)->OlpermartKHI . '</p>';
                            // echo '<p>' . $row->quantity . '</p>';
                            // echo '<p>' . $row->pack_size . '</p>';
                            // echo '<p>cotton ' . floor($row->quantity / $row->pack_size) . '</p>';


                            if (json_decode($row->company_prices_and_names)->OlpermartKHI) {

                                //  echo json_decode($row->company_prices_and_names)->OlpermartKHI;
                                $a = floor($row->quantity / $row->pack_size) + floor($QUANTITY_  / $row->pack_size);
                                $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . $a . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock"';

                                $check_remain_qty =  $a;
                                if ($check_remain_qty == '0') {
                                    $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                    //echo $query_out_of_stock;
                                    if ($conn->query($query_out_of_stock) === true) {
                                    }
                                } else {
                                    $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartKHI . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                    if ($conn->query($query_in_stock) === true) {
                                    }
                                }
                                if ($conn->query($sql) === true) {
                                    //echo $sql;
                                } else {
                                    echo "Error updating record: " . $conn->error;
                                }
                            }
                        }
                        //return $data;
                    }
                }
            }

            $conn->close();

            //echo "<pre>";
            //print_r($result);
            //die();
            //exit();

        }
    }

    public function update_olpers_quantity($id, $olper_id)
    {
        // software
        $product_id = $id;

        $get_pack_size = $this->CI->db->query("SELECT `pack_size` AS pack_size FROM `sma_products` WHERE id = '$product_id'")->result_array();
        $get_warehouse_qty = $this->CI->db->query("SELECT quantity FROM `sma_warehouses_products` WHERE product_id = '$product_id' AND warehouse_id = '1'")->result_array();
        $qty = $get_warehouse_qty[0]['quantity'] / $get_pack_size[0]['pack_size'];

        // olpermart Credential Karachi
        $dbServerName = "208.109.167.45";
        $dbUsername = "olper_lhr";
        $dbPassword = "x2a~c#;E[4V0";
        $dbName = "o_karachi";
        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

        $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . $qty . ' WHERE `wp_postmeta`.`post_id` = ' . $olper_id . ' and `wp_postmeta`.`meta_key` = "_stock"';

        $check_remain_qty = $qty;
        if ($check_remain_qty == '0') {

            $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . $olper_id . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
            //echo $query_out_of_stock;
            if ($conn->query($query_out_of_stock) === true) {
            }
        } else {
            $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . $olper_id . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

            if ($conn->query($query_in_stock) === true) {
            }
        }

        //echo $sql."<br>";
        if ($conn->query($sql) === true) {
            // echo "Record updated successfully";
        } else {
            // echo "Error updating record: " . $conn->error;
        }
    }
}
