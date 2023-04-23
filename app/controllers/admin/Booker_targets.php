<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class Booker_targets extends MY_Controller{
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Bookers Targets')));
        $meta = array('page_title' => lang('Bookers Targets'), 'bc' => $bc);
        $this->data['user_id'] = $user_id;
        $this->data['routes'] = $this->db->get('sma_booker_targets')->result();
        $this->page_construct2('booker_targets/index', $meta, $this->data);
    }


     public function get_targets(){
        // Count Total Rows
        $this->db->from('sma_booker_targets');
        $totalq = $this->db->get();
        $this->runquery_target('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_target();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-month="'.$row->month.'" data-year="'.$row->year.'" data-target_shop="'.$row->target_shop.'" data-target_orders="'.$row->target_orders.'" data-target_amount="'.$row->target_amount.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->month,
                $row->year,
                $row->target_orders,
                $row->target_amount,
                $row->target_shop,
                $button
            );
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $totalq->num_rows(),
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );
        // Output to JSON format
        echo json_encode($output);
    }


    public function runquery_target($onlycoun = "no"){
        $column_order = array(
            null,
            'booker_targets.id',
            'booker_targets.month',
            'booker_targets.year',
            'booker_targets.target_orders',
            'booker_targets.target_amount',
            'booker_targets.target_shop'
        );
        $column_search = array(
            'booker_targets.id',
            'booker_targets.month',
            'booker_targets.year',
            'booker_targets.target_orders',
            'booker_targets.target_amount',
            'booker_targets.target_shop'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('booker_targets.id as id');
        }
        else{
            $this->db->select('
                booker_targets.id,
                booker_targets.month,
                booker_targets.year,
                booker_targets.target_orders,
                booker_targets.target_amount,
                booker_targets.target_shop,
            ');
        }
        $this->db->from('booker_targets as booker_targets');
        $i = 0;
        // loop searchable columns 
        if($onlycoun != "yes"){
            foreach($column_search as $item){
                // if datatable send POST for search
                if($_POST['search']['value']){
                    // first loop
                    if($i===0){
                        // open bracket
                        $this->db->group_start();
                        $this->db->like($item, $_POST['search']['value']);
                    }else{
                        $this->db->or_like($item, $_POST['search']['value']);
                    }
                    // last loop
                    if(count($column_search) - 1 == $i){
                        // close bracket
                        $this->db->group_end();
                    }
                }
                $i++;
            }
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }

    public function insert_target(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $user_id = $this->input->post('user_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $target_orders = $this->input->post('target_orders');
        $target_amount = $this->input->post('target_amount');
        $target_shops = $this->input->post('target_shops');
        if($user_id != ""){
            $insert['user_id'] = $user_id;
            $insert['month'] = $month;
            $insert['year'] = $year;
            $insert['target_orders'] = $target_orders;
            $insert['target_amount'] = $target_amount;
            $insert['target_shop'] = $target_shops;
            $this->db->insert('sma_booker_targets',$insert);
            $senddata['message'] = "Target create successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_target(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $user_id = $this->input->post('user_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $target_orders = $this->input->post('target_orders');
        $target_amount = $this->input->post('target_amount');
        $target_shops = $this->input->post('target_shops');

        if($user_id != ""){
            $set['user_id'] = $user_id;
            $set['month'] = $month;
            $set['year'] = $year;
            $set['target_orders'] = $target_orders;
            $set['target_amount'] = $target_amount;
            $set['target_shop'] = $target_shops;
            
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('sma_booker_targets');
            $senddata['message'] = "Target update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_target(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        if($this->data['Owner']){
            $this->db->where('id',$id);
            $this->db->delete('sma_booker_targets');
            $senddata['status'] = true;
            $senddata['message'] = "Target delete successfully!";
        }
        else{
            $senddata['message'] = "Permission Denied!";
        }
        echo json_encode($senddata);

    }



}