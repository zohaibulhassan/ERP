<?php defined('BASEPATH') or exit('No direct script access allowed');
class Suppliers extends App_Controller{
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
            sales_type,
            name,
            company,
            phone,
            email,
            cnic
        ');
        $this->db->from('sma_companies');
        $this->db->where('group_name','supplier');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        if($text != ""){
            $this->db->where('(name LIKE "%'.$text.'%" OR company LIKE "%'.$text.'%")');
        }
        $q = $this->db->get();
        $this->data['suppliers'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
}
