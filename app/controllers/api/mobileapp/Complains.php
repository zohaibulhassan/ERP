<?php defined('BASEPATH') or exit('No direct script access allowed');
class Complains extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    public function create(){
        $customer = $this->input->post('customer');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $priority = $this->input->post('priority');
        $created_by = $this->input->post('created_by');

        if($customer != "" && $subject != "" && $message != "" && $priority != "" && $created_by){

            $insert['subject'] = $subject;
            $insert['customer_id'] = $customer;
            $insert['message'] = $message;
            $insert['priority'] = $priority;
            $insert['created_by'] = $created_by; 
            $this->db->insert('complains',$insert);
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
        $user_id = $this->input->post('user_id');
        $from = $this->input->post('from');
        $priority = $this->input->post('priority');
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
            complains.id,
            complains.subject,
            complains.customer_id,
            customer_detail.name as customer,
            complains.created_at,
            complains.created_by,
            CONCAT(users.first_name," ",users.last_name) as created_by,
            complains.priority,
            complains.status
        ');
        $this->db->from('complains');
        $this->db->join('companies as customer_detail', 'customer_detail.id = complains.customer_id', 'left');
        $this->db->join('sma_users as users', 'users.id = complains.created_by', 'left');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        if($text != ""){
            $this->db->where('complains.subject LIKE "%'.$text.'%"');
        }
        if($priority != ""){
            $this->db->where('complains.priority = "'.$priority.'"');
        }
        if($status != ""){
            $this->db->where('complains.status = '.$status);
        }
        if($customer != ""){
            $this->db->where('complains.customer_id = '.$customer);
        }
        if($user_id != ""){
            $this->db->where('complains.created_by = '.$user_id);
        }
        if($to != ""){
            $this->db->where('complains.created_at >= "'.$to.'"');
        }
        if($from != ""){
            $this->db->where('complains.created_at <= "'.$from.'"');
        }
        $q = $this->db->get();
        $this->data['complains'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
    public function detail(){
        $id = $this->input->post('id');
        if($id){
            $this->db->select('
                complains.*,
                customer_detail.name as customer,
                CONCAT(users.first_name," ",users.last_name) as created_user,
                CONCAT(resolve.first_name,resolve.last_name) as resolved_user
            ');
            $this->db->from('complains');
            $this->db->join('companies as customer_detail', 'customer_detail.id = complains.customer_id', 'left');
            $this->db->join('sma_users as users', 'users.id = complains.created_by', 'left');
            $this->db->join('sma_users as resolve', 'resolve.id = complains.resolved_by', 'left');
            $this->db->where('complains.id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['complain'] = $q->result()[0];
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Complain not found";
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
