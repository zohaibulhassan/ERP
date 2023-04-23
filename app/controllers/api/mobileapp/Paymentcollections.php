<?php defined('BASEPATH') or exit('No direct script access allowed');
class Paymentcollections extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    public function create(){
        $sale_id = $this->input->post('sale_id');
        $customer_id = $this->input->post('customer_id');
        $paid_by = $this->input->post('paid_by');
        $cheque_no = $this->input->post('cheque_no');
        $amount = $this->input->post('amount');
        $created_by = $this->input->post('created_by');
        $attachment = '';
        $note = $this->input->post('note');
        $new_name = date("Y")."".date("m")."".date("d")."".date("H")."".date("i")."".date("s")."-".$created_by;
        if($sale_id != "" && $customer_id != "" && $paid_by != "" &&  $amount != "" &&  $created_by != "" && $note != ""){

            $this->db->select('id');
            $this->db->from('paymentcollections');
            $this->db->where('sale_id',$sale_id);
            $this->db->where('status','0');
            $q = $this->db->get();
            if($q->num_rows() == 0){
                $config['upload_path']          = './uploads/attachments/payment_collections/';
                $config['allowed_types']        = '*';
                $config['encrypt_name'] = true;
                $config['file_name'] = $new_name;
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('attachment')){
                    $attachment = $this->upload->file_name;
                }
                else{
                    $this->data['uploadfile_message'] = $this->upload->display_errors();
                }

                $insert['sale_id'] = $sale_id;
                $insert['customer_id'] = $customer_id;
                $insert['paid_by'] = $paid_by;
                $insert['cheque_no'] = $cheque_no;
                $insert['amount'] = $amount;
                $insert['created_by'] = $created_by;
                $insert['attachment'] = $attachment;
                $insert['note'] = $note;
                $this->db->insert('paymentcollections',$insert);
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Payment collection already insert in this invoice";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $m = "Required Query Parameter Null!";
            $this->data['message'] = $m;
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
    public function lists(){
        $page = $this->input->post('page');
        $limit = $this->input->post('limit');
        $customer_id = $this->input->post('customer_id');
        $sale_id = $this->input->post('sale_id');
        $status = $this->input->post('status');

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
            pc.id,
            pc.sale_id,
            s.reference_no,
            pc.paid_by,
            pc.cheque_no,
            pc.amount,
            CONCAT(u.first_name," ",u.last_name) as created_by,
            pc.attachment,
            pc.note,
            pc.created_at,
            pc.status
        ');
        $this->db->from('paymentcollections as pc');
        $this->db->join('sales as s','s.id = pc.sale_id','left');
        $this->db->join('users as u','u.id = pc.created_by','left');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        if($customer_id != ""){
            $this->db->where('pc.customer_id',$customer_id);
        }
        if($sale_id != ""){
            $this->db->where('pc.sale_id',$sale_id);
        }
        if($status != ""){
            $this->db->where('pc.status',$status);
        }
        $q = $this->db->get();
        $this->data['rows'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();

    }
    public function deleted(){
        $id = $this->input->post('id');
        $user_id = $this->input->post('user_id');
        if($id != "" && $user_id){
            $this->db->select('id,status');
            $this->db->from('paymentcollections');
            $this->db->where('id',$id);
            $this->db->where('created_by',$user_id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $data = $q->result()[0];
                if($data->status == 0){
                    $this->db->where('id',$id);
                    $this->db->delete('paymentcollections');
                    $this->data['code_status'] = true;
                    $this->data['message'] = "Success!";
                }
                else{
                    $this->data['message'] = "You do not delete verifed payment collection!";
                    $this->data['error_code'] = '004';
                }
            }
            else{
                $this->data['message'] = "Invalid Payment Collections";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
}
