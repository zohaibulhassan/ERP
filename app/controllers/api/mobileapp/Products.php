<?php defined('BASEPATH') or exit('No direct script access allowed');
class Products extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    public function lists(){
        $text = $this->input->post('text');
        $category = $this->input->post('category');
        $sub_category = $this->input->post('sub_category');
        $warehouse_id = $this->input->post('warehouse_id');
        $brand = $this->input->post('brand');
        $supplier = $this->input->post('supplier');
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
            p.id as product_id,
            p.image as product_image,
            p.name as product_name,
            p.code as product_barcode,
            p.mrp as product_mrp,
            0 as selling_price,
            b.name as brand,
            c.name as category,
            sc.name as subcategory
        ');
        $this->db->from('sma_products as p');
        $this->db->join('sma_brands as b','b.id = p.brand','left');
        $this->db->join('sma_categories as c','c.id = p.category_id','left');
        $this->db->join('sma_categories as sc','sc.id = p.subcategory_id','left');
        if($category != "" && $category != 0){
            $this->db->where('p.category_id',$category);
        }
        if($sub_category != "" && $sub_category != 0){
            $this->db->where('p.subcategory_id',$sub_category);
        }
        if($brand != "" && $brand != 0){
            $this->db->where('p.brand',$brand);
        }
        if($supplier != "" && $supplier != 0){
            $this->db->where('(p.supplier1 = "'.$supplier.'" OR p.supplier2 = "'.$supplier.'" OR p.supplier3 = "'.$supplier.'" OR p.supplier4 = "'.$supplier.'" OR p.supplier5 = "'.$supplier.'")');
        }
        if($text != ""){
            $this->db->where('(p.name LIKE "%'.$text.'%" OR p.code LIKE "%'.$text.'%")');
        }
        $this->db->where('p.status',1);
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        $q = $this->db->get();
        $this->data['products'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
    public function brands(){
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
        $this->db->select('*');
        $this->db->from('sma_brands as b');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        if($text != ""){
            $this->db->where('(b.name LIKE "%'.$text.'%" OR b.code LIKE "%'.$text.'%")');
        }
        $q = $this->db->get();
        $this->data['brands'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
    public function categories(){
        $category_id = $this->input->post('parent_id');
        $category_id = $category_id == "" ? 0 : $category_id;
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
        $this->db->select('*');
        $this->db->from('sma_categories as c');
        if($text != ""){
            $this->db->where('(c.name LIKE "%'.$text.'%" OR c.code LIKE "%'.$text.'%")');
        }
        $this->db->where('parent_id',$category_id);
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        $q = $this->db->get();
        $this->data['categories'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
}
