<?php defined('BASEPATH') or exit('No direct script access allowed');
class Warehouses extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    public function lists(){
        $text = $this->input->post('text');
        $page = $this->input->post('page');
        $limit = $this->input->post('limit');
        if($page == "" || $page <= 0){
            $page = 1;
        }
        if($limit == "" || $limit > 20){
            $limit = 20;
        }
        else if($limit <= 5){
            $limit = 5;
        }
        $end=$limit*$page;
        $start=$end-$limit;
        $this->db->select('
            id,
            name,
            code,
            email,
            phone,
            address,
            map
        ');
        $this->db->from('sma_warehouses');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        if($text != ""){
            $this->db->where('(name LIKE "%'.$text.'%" OR code LIKE "%'.$text.'%")');
        }
        $q = $this->db->get();
        $this->data['suppliers'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
}
