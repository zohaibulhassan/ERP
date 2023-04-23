<?php defined('BASEPATH') or exit('No direct script access allowed');
class Pop_journey extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    
    public function lists(){
        $date = date('Y-m-d');
        $user_id = $this->input->post('user_id');
        $this->db->select('
            a.id,
            a.route_id as routes_id,
            b.name as routes_name
        ');
        $this->db->from('sma_pop_journey as a');
        $this->db->join('sma_routes_tb as b', 'b.id = a.route_id');
        $this->db->where('a.user_id',$user_id);
        $this->db->where('a.date',$date);
        $q = $this->db->get();
        $this->data['pop_journey'] = $q->row();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
}
