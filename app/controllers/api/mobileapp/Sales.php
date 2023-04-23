<?php defined('BASEPATH') or exit('No direct script access allowed');
class Sales extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    public function dueinvoices(){
        $id = $this->input->post('customer_id');
        $this->db->select('
            id,
            reference_no,
            grand_total,
            paid,
            (grand_total)-(paid) as balance,
            payment_status
        ');
        $this->db->from('sales');
        $this->db->where('customer_id',$id);
        $this->db->where('payment_status != "paid"');
        $q = $this->db->get();
        $this->data['invoices'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
}
