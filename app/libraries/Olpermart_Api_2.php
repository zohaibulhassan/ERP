<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author    : Dabeer ul Hasan
 *  Email     : dabeer.hasan@gmail.com
 *  For       : Stock Manager Advance
 *  Web       : http://rholab.net
 *  ==============================================================================
*/

class Olpermart_API_2
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

    public function get_update_product_olpermart_api($data, $products)
    {

        // olpermart Credential Lahore
        $dbServerName = "208.109.167.45";
        $dbUsername = "ll";
        $dbPassword = "AClsyqF0XF~G";
        $dbName = "lahore";

        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

        if ($conn) {
            foreach ($products as $key => $value) {
                $q = $this
                    ->CI
                    ->db
                    ->query("SELECT sma_products.*, sma_warehouses_products.* FROM `sma_warehouses_products` LEFT JOIN sma_products ON sma_warehouses_products.product_id = sma_products.id
                    WHERE sma_warehouses_products.warehouse_id = 2 and sma_warehouses_products.product_id = '" . $products[$key]['product_id'] . "'");

                // echo $q;
                // die;
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $result[] = $row;

                        // echo "<pre>";
                        // print_r(json_decode($row->company_prices_and_names)->OlpermartLHR) . "<br>";


                        //print_r(json_decode($row->company_prices_and_names)->OlpermartLHR) . "<br>";
                        if (json_decode($row->company_prices_and_names)->OlpermartLHR) {
                            $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . floor($row->quantity / $row->pack_size) . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock"';

                            $check_remain_qty = floor($row->quantity / $row->pack_size);
                            if ($check_remain_qty == '0') {

                                $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                //echo $query_out_of_stock;
                                if ($conn->query($query_out_of_stock) === true) {
                                }
                            } else {
                                $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

                                if ($conn->query($query_in_stock) === true) {
                                }
                            }

                            //echo $sql."<br>";
                            //die();
                            //exit();
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

            // echo "<pre>";
            // print_r($result);
            // die();
            // exit();
            $conn->close();
        }
    }

    public function get_update_product_olpermart_api_after_delete($id)
    {

        // olpermart Credential
        $dbServerName = "208.109.167.45";
        $dbUsername = "olper_lahore";
        $dbPassword = "AClsyqF0XF~G";
        $dbName = "lahore";

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
                        WHERE sma_warehouses_products.warehouse_id = 2 and sma_warehouses_products.product_id = '" . $row->product_id . "'");
                    if ($q1->num_rows() > 0) {
                        foreach (($q1->result()) as $row) {
                            $result[] = $row;

                            //print_r(json_decode($row->company_prices_and_names)->OlpermartLHR) . "<br>";
                            if (json_decode($row->company_prices_and_names)->OlpermartLHR) {
                                $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . floor($row->quantity / $row->pack_size) . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock"';

                                $check_remain_qty = floor($row->quantity / $row->pack_size);
                                if ($check_remain_qty == '0') {

                                    $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                    //echo $query_out_of_stock;
                                    if ($conn->query($query_out_of_stock) === true) {
                                    }
                                } else {
                                    $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

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
        $dbUsername = "olper_lahore";
        $dbPassword = "AClsyqF0XF~G";
        $dbName = "lahore";

        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);
        if ($conn) {
            $q = $this
                ->CI
                ->db
                ->query("SELECT * FROM `sma_sale_items` where sale_id = $id");

            // Phele sale ID sy saari product le aye
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $result[] = $row;

                    $QUANTITY_ = $row->quantity;


                    $q1 = $this
                        ->CI
                        ->db
                        ->query("SELECT sma_products.*, sma_warehouses_products.* FROM `sma_warehouses_products` LEFT JOIN sma_products ON sma_warehouses_products.product_id = sma_products.id
                        WHERE sma_warehouses_products.warehouse_id = 2 and sma_warehouses_products.product_id = '" . $row->product_id . "'");

                    // sale id ki products
                    // print_r($q1);


                    if ($q1->num_rows() > 0) {
                        foreach (($q1->result()) as $row) {
                            $result[] = $row;

                            // print_r($row);
                            // echo '<p>' . $row->name . '</p>';
                            // echo '<p>' . $row->company_prices_and_names . '</p>';
                            // echo '<p>' . json_decode($row->company_prices_and_names)->OlpermartLHR . '</p>';
                            // echo '<p>' . $row->quantity . '</p>';
                            // echo '<p>' . $row->pack_size . '</p>';
                            // echo '<p>cotton ' . floor($row->quantity / $row->pack_size) . '</p>';


                            if (json_decode($row->company_prices_and_names)->OlpermartLHR) {

                                $a = floor($row->quantity / $row->pack_size) + floor($QUANTITY_  / $row->pack_size);
                                $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . $a . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock"';
                                // echo  $a;
                                // echo  $sql;
                                //  die;
                                $check_remain_qty = $a;
                                if ($check_remain_qty == '0') {
                                    $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                    //echo $query_out_of_stock;
                                    if ($conn->query($query_out_of_stock) === true) {
                                    }
                                } else {
                                    $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->OlpermartLHR . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
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
        $get_warehouse_qty = $this->CI->db->query("SELECT quantity FROM `sma_warehouses_products` WHERE product_id = '$product_id' AND warehouse_id = '2'")->result_array();
        $qty = $get_warehouse_qty[0]['quantity'] / $get_pack_size[0]['pack_size'];

        // olpermart Credential Lahore
        $dbServerName = "208.109.167.45";
        $dbUsername = "olper_lahore";
        $dbPassword = "AClsyqF0XF~G";
        $dbName = "lahore";
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
