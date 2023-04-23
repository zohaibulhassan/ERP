<?php defined('BASEPATH') or exit('No direct script access allowed');
class Products extends MY_Controller{

    public function get(){
        $sendvalue['status'] = 'Access Denied';       

        $product_id = $this->input->get('product_id');       
        $secret_key = $this->input->get('secret_key');
        if($secret_key != "" && $product_id != ""){

            $insertdata['ip'] = $_SERVER['REMOTE_ADDR'];
            $insertdata['secret_key'] = $secret_key;

            $this->db->select('*');
            $this->db->from('sma_api_auth_tb');
            $this->db->where('secret_key',$secret_key);
            $q = $this->db->get();
            if($q->num_rows() > 0){


                $this->db->select('
                    sma_products.id,
                    sma_products.code,
                    sma_products.name,
                    sma_products.quantity as product_qty,
                    sma_warehouses_products.quantity as warehouse1_qty,
                    sma_warehouses_products.warehouse_id,
                ');
                $this->db->from('sma_products');
                $this->db->join('sma_warehouses_products', 'sma_warehouses_products.product_id = sma_products.id AND sma_warehouses_products.warehouse_id  = 1', 'left');
                $this->db->where('sma_products.id',$product_id);
                $q2 = $this->db->get();
                if($q2->num_rows() > 0){
                    $res = $q2->result();
                    $insertdata['detail'] = 'Successfully send product detail. ID: '.$res[0]->id;
                    $sendvalue['product_id'] = $res[0]->id;
                    $sendvalue['product_code'] = $res[0]->code;
                    $sendvalue['product_name'] = $res[0]->name;
                    // $sendvalue['product_qty'] = $res[0]->product_qty;
                    $sendvalue['product_qty'] = $res[0]->warehouse1_qty;
                    // $sendvalue['warehouse_id'] = $res[0]->warehouse_id;
                    $sendvalue['status'] = 'Success';
                }
                else{
                    $insertdata['detail'] = 'Invalid Product ID';
                }
            }
            else{
                $insertdata['detail'] = 'Invalid Secret Key';
            }
            $insertdata['status'] = $sendvalue['status'];
            $this->db->insert('sma_api_requests_tb',$insertdata);
        }
        echo json_encode($sendvalue);
    }

}