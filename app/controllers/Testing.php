<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Testing extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->admin_model('db_model');
        $this->load->admin_model('stores_model');
        $this->load->admin_model('wordpresswoocommerce_model','wp');
    }
    public function store_product(){
        // $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        // $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('survey'), 'page' => lang('Survey')), array('link' => '#', 'page' => lang('List')));
        // $meta = array('page_title' => lang('Questions'), 'bc' => $bc);
        // $this->page_construct2('store_product', $meta, $this->data);
    }
    public function products(){
        $products = $this->db->select('id')->from('products')->get()->result();
        echo json_encode($products);
    }
    public function product_update(){
        echo 'Code Running<br>';
        $pid = $this->input->get('pid');
        $q = $this->db->from('sma_store_products_tb')->where('product_id',$pid)->get();
        if($q->num_rows() == 0){
            $product = $this->db->from('sma_products')->where('id',$pid)->get()->row();
            $insert['store_id'] = "1";
            $insert['update_in'] = "full";
            $insert['product_id'] = $product->id;
            $insert['product_name'] = $product->name;
            $insert['store_product_id'] = "";
            $insert['update_qty_in'] = "pack";
            $insert['price_type'] = "mrp";
            $insert['discount'] = "no";
            $insert['warehouse_id'] = 4;
            $insert['supplier_id'] = $product->supplier1;
            $insert['created_by'] = "58";
            $insert['updated_by'] = "0";
            $insert['status'] = "active";
            $this->db->insert('sma_store_products_tb',$insert);
        }
        $insert2['product_id'] = $pid;
        $insert2['warehouse_id'] = 4;
        $insert2['store_id'] = 1;
        $insert2['type'] = "Qty Update";
        $insert2['status'] = "pending";
        // $this->db->insert('sma_store_requests_tb',$insert2);
    }
    public function storeupdate(){
        // echo '<pre>';
        // $this->wp->update_product(10);

    }
    public function testingpush(){
        
    }


}
