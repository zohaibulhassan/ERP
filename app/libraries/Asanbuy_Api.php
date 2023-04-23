<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author    : Dabeer ul Hasan
 *  Email     : dabeer.hasan@gmail.com
 *  For       : Stock Manager Advance
 *  Web       : http://rholab.net
 *  ==============================================================================
*/

class Asanbuy_API
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

    public function get_update_product_asanbuy_api($data, $products)
    {

        // Asanbuy Credential
        $dbServerName = "208.109.167.45";
        $dbUsername = "newasanbuy";
        $dbPassword = "D([aX2jXghi4";
        $dbName = "newasanbuy";

        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

        if ($conn) {

            foreach ($products as $key => $value) {
                $q = $this
                    ->CI
                    ->db
                    ->query("SELECT sma_products.*, sma_warehouses_products.* FROM `sma_warehouses_products` LEFT JOIN sma_products ON sma_warehouses_products.product_id = sma_products.id
                    WHERE sma_warehouses_products.warehouse_id = 1 and sma_warehouses_products.product_id = '" . $products[$key]['product_id'] . "'");

                //echo "SELECT * from sma_products where sma_products.id = '" . $products[$key]['product_id'] . "' <br>";

                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $result[] = $row;


                        //print_r(json_decode($row->company_prices_and_names)->Asanbuy) . "<br>";
                        if (json_decode($row->company_prices_and_names)
                            ->Asanbuy
                        ) {
                            $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . floor($row->quantity / $row->pack_size) . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock"';

                            if ($conn->query($sql) === true) {
                                // echo "Record updated successfully";
                            } else {
                                // echo "Error updating record: " . $conn->error;
                            }

                            $check_remain_qty = floor($row->quantity / $row->pack_size);
                            if ($check_remain_qty == '0') {

                                $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                //echo $query_out_of_stock;
                                if ($conn->query($query_out_of_stock) === true) {
                                }
                            } else {
                                $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

                                if ($conn->query($query_in_stock) === true) {
                                }
                            }

                            // echo $sql."<br>";
                            //    echo $query_in_stock."<br>";
                            //   die();
                            //  exit();

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

    public function get_update_product_asanbuy_api_after_delete($id)
    {

        // Asanbuy Credential
        $dbServerName = "208.109.167.45";
        $dbUsername = "newasanbuy";
        $dbPassword = "D([aX2jXghi4";
        $dbName = "newasanbuy";

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

                            if (json_decode($row->company_prices_and_names)
                                ->Asanbuy
                            ) {
                                $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . floor($row->quantity / $row->pack_size) . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock"';

                                $check_remain_qty = floor($row->quantity / $row->pack_size);
                                if ($check_remain_qty == '0') {

                                    $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                    //echo $query_out_of_stock;
                                    if ($conn->query($query_out_of_stock) === true) {
                                    }
                                } else {
                                    $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

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

        // Asanbuy Credential
        $dbServerName = "208.109.167.45";
        $dbUsername = "newasanbuy";
        $dbPassword = "D([aX2jXghi4";
        $dbName = "newasanbuy";

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
                    if ($q1->num_rows() > 0) {
                        foreach (($q1->result()) as $row) {
                            $result[] = $row;

                            if (json_decode($row->company_prices_and_names)->Asanbuy) {

                                //  echo json_decode($row->company_prices_and_names)->OlpermartKHI;
                                $a = floor($row->quantity / $row->pack_size) + floor($QUANTITY_  / $row->pack_size);
                                $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . $a . ' WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock"';

                                $check_remain_qty =  $a;
                                if ($check_remain_qty == '0') {
                                    $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
                                    //echo $query_out_of_stock;
                                    if ($conn->query($query_out_of_stock) === true) {
                                    }
                                } else {
                                    $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . json_decode($row->company_prices_and_names)->Asanbuy . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
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


    public function update_asanBuy_quantity($id, $asanbuy_id)
    {
        // software
        $product_id = $id;

        $get_pack_size = $this->CI->db->query("SELECT `pack_size` AS pack_size FROM `sma_products` WHERE id = '$product_id'")->result_array();
        $get_warehouse_qty = $this->CI->db->query("SELECT quantity FROM `sma_warehouses_products` WHERE product_id = '$product_id' AND warehouse_id = '1'")->result_array();
        $qty = $get_warehouse_qty[0]['quantity'] / $get_pack_size[0]['pack_size'];

        // Asanbuy Credential
        $dbServerName = "208.109.167.45";
        $dbUsername = "newasanbuy";
        $dbPassword = "D([aX2jXghi4";
        $dbName = "newasanbuy";
        // create connection
        $conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);

        $sql = 'UPDATE `wp_postmeta` SET `meta_value` = ' . $qty . ' WHERE `wp_postmeta`.`post_id` = ' . $asanbuy_id . ' and `wp_postmeta`.`meta_key` = "_stock"';

        $check_remain_qty = $qty;
        if ($check_remain_qty == '0') {

            $query_out_of_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "outofstock" WHERE `wp_postmeta`.`post_id` = ' . $asanbuy_id . ' and `wp_postmeta`.`meta_key` = "_stock_status"';
            //echo $query_out_of_stock;
            if ($conn->query($query_out_of_stock) === true) {
            }
        } else {
            $query_in_stock = 'UPDATE `wp_postmeta` SET `meta_value` = "instock" WHERE `wp_postmeta`.`post_id` = ' . $asanbuy_id . ' and `wp_postmeta`.`meta_key` = "_stock_status"';

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
