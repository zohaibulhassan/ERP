<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD

class Useractivities_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }
    public function add($req){
        try {
            if(isset($req['product_id'])){
                $data['product_id'] = $req['product_id'];
            }
            if(isset($req['po_id'])){
                $data['po_id'] = $req['po_id'];
            }
            if(isset($req['purchase_id'])){
                $data['purchase_id'] = $req['purchase_id'];
            }
            if(isset($req['so_id'])){
                $data['so_id'] = $req['so_id'];
            }
            if(isset($req['sale_id'])){
                $data['sale_id'] = $req['sale_id'];
            }
            if(isset($req['transfer_id'])){
                $data['transfer_id'] = $req['transfer_id'];
            }
            if(isset($req['store_id'])){
                $data['store_id'] = $req['store_id'];
            }
            $data['url'] = $_SERVER['REQUEST_URI'];
            if(isset($_SERVER['HTTP_REFERER'])){
                $data['redirect_url'] = $_SERVER['HTTP_REFERER'];
            }
            else{
                $data['redirect_url'] = 'no';
            }
            if(isset($req['json_data'])){
                $data['json_data'] = $req['json_data'];
            }
            $data['note'] = $req['note'];
            $data['action_by'] = $req['action_by'];
            $data['location'] = $req['location'];
            $this->db->insert('sma_user_activities', $data);
        }
        catch(Exception $e) {
            // echo $e->getMessage();
        }
    }
}