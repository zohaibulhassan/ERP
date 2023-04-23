<?php defined('BASEPATH') or exit('No direct script access allowed');
class Feedbacks extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    public function create(){
        $customer = $this->input->post('customer');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $created_by = $this->input->post('created_by');

        if($customer != "" && $subject != "" && $message != "" && $created_by){

            $insert['subject'] = $subject;
            $insert['customer_id'] = $customer;
            $insert['message'] = $message;
            $insert['created_by'] = $created_by; 
            $this->db->insert('feedbacks',$insert);
            $this->data['code_status'] = true;
            $this->data['message'] = "Success!";
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();


    }
    public function lists(){
        $text = $this->input->post('text');
        $customer = $this->input->post('customer');
        $to = $this->input->post('to');
        $from = $this->input->post('from');
        $user_id = $this->input->post('user_id');
        $status = $this->input->post('status');
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
            feedbacks.id,
            feedbacks.subject,
            feedbacks.customer_id,
            customer_detail.name as customer,
            feedbacks.created_at,
            feedbacks.created_by,
            CONCAT(users.first_name," ",users.last_name) as created_by,
            feedbacks.status
        ');
        $this->db->from('feedbacks');
        $this->db->join('companies as customer_detail', 'customer_detail.id = feedbacks.customer_id', 'left');
        $this->db->join('sma_users as users', 'users.id = feedbacks.created_by', 'left');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        if($text != ""){
            $this->db->where('feedbacks.subject LIKE "%'.$text.'%"');
        }
        if($status != ""){
            $this->db->where('feedbacks.status = '.$status);
        }
        if($customer != ""){
            $this->db->where('feedbacks.customer_id = '.$customer);
        }
        if($user_id != ""){
            $this->db->where('feedbacks.created_by = '.$user_id);
        }
        if($to != ""){
            $this->db->where('feedbacks.created_at >= "'.$to.'"');
        }
        if($from != ""){
            $this->db->where('feedbacks.created_at <= "'.$from.'"');
        }
        $q = $this->db->get();
        $this->data['feedbacks'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
    public function detail(){
        $id = $this->input->post('id');
        if($id){
            $this->db->select('
                feedbacks.*,
                customer_detail.name as customer,
                CONCAT(users.first_name," ",users.last_name) as created_user,
            ');
            $this->db->from('feedbacks');
            $this->db->join('companies as customer_detail', 'customer_detail.id = feedbacks.customer_id', 'left');
            $this->db->join('sma_users as users', 'users.id = feedbacks.created_by', 'left');
            $this->db->where('feedbacks.id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['feedback'] = $q->result()[0];
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Feed back not found";
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
