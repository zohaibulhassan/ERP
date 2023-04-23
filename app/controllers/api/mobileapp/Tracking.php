<?php defined('BASEPATH') or exit('No direct script access allowed');
class Tracking extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    public function list(){
        $id = $this->input->post('user_id');
        $date = $this->input->post('date');
        if($date == ""){
            $date = date('Y-m-d');
        }
        if($id != ""){
            $this->db->select('
                ut.*,
                u.first_name,
                u.last_name
            ');
            $this->db->from('sma_users_tracker as ut');
            $this->db->join('sma_users as u','u.id = ut.user_id','left');
            $this->db->where('ut.user_id',$id);
            $this->db->where('ut.created_at',$date);
            $q = $this->db->get();
            $this->data['tracking'] = $q->result();
            $this->data['code_status'] = true;
            $this->data['message'] = "Success!";
        }
        else{
            $m = "Required Query Parameter Null!";
            $this->data['message'] = $m;
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
}

