<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class Pop_journey extends MY_Controller{
    public function __construct(){
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->load->library('form_validation');
        $this->load->admin_model('general_model');
    }
    function index($user_id){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Pop Journey')));
        $meta = array('page_title' => lang('Pop Journey'), 'bc' => $bc);
        $this->data['user_id'] = $user_id;
        $this->data['routes'] = $this->db->get('sma_routes_tb')->result();
        $this->page_construct2('pop_journey/index', $meta, $this->data);
    }


    function load_journey(){
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $user_id = $this->input->get('id');
        $this->db->where('a.user_id',$user_id);
        $this->db->where('a.date >=',$start);
        $this->db->where('a.date <',$end);
        $this->db->select('a.*, b.name as title');
        $this->db->from('sma_pop_journey as a');
        $this->db->join('sma_routes_tb as b','b.id = a.route_id');
        $q = $this->db->get();
        echo json_encode($q->result());
    }

    public function insert_journey(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $user_id = $this->input->post('user_id');
        $date = $this->input->post('date');
        $route_id = $this->input->post('route_id');
        if($user_id != "" && $route_id != ''){
            $insert['user_id'] = $user_id;
            $insert['date'] = date('Y-m-d',strtotime($date));
            $insert['route_id'] = $this->input->post('route_id');
            $this->db->insert('sma_pop_journey',$insert);
            $senddata['message'] = "Journey create successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_journey(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $user_id = $this->input->post('user_id');
        $date = $this->input->post('date');
        $route_id = $this->input->post('route_id');

        if($user_id != "" && $route_id != ''){
            $set['user_id'] = $user_id;
            $set['date'] = date('Y-m-d',strtotime($date));
            $set['route_id'] = $this->input->post('route_id');
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('sma_pop_journey');
            $senddata['message'] = "Journey update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_journey(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        if($this->data['Owner']){
            $this->db->where('id',$id);
            $this->db->delete('sma_pop_journey');
            $senddata['status'] = true;
            $senddata['message'] = "Journey delete successfully!";
        }
        else{
            $senddata['message'] = "Permission Denied!";
        }
        echo json_encode($senddata);

    }



}