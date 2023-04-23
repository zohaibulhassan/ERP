<?php defined('BASEPATH') or exit('No direct script access allowed');


class Feedbacks extends MY_Controller
{
    public function __construct(){
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->load->library('form_validation');
        $this->load->admin_model('feedbacks_model');
        $this->data['logo'] = true;
    }
    public function index(){

        $this->data['scustomer'] = $this->input->get('customer');
        $this->data['sstatus'] = $this->input->get('status');
        $this->data['sto'] = $this->input->get('to');
        $this->data['sfrom'] = $this->input->get('from');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Feedbacks'));
        $meta = array('page_title' => 'feedbacks', 'bc' => $bc);
        $this->page_construct2('feedbacks/index', $meta, $this->data);

    }
    public function get_list(){
        // Count Total Rows
        $this->db->from('feedbacks');
        $totalq = $this->db->get();
        $this->runquery('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            // $percentage = ($row->complete_qty/$row->total_qty)*100;
            $button = '<button class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light md-btn-mini feedbackdetail" data-feedback='; 
            $button .= "'".json_encode($row)."'";
            $button .= ' >View</button>';
            if($row->status == 0){
                $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            }
            
            $data[] = array(
                $row->id,
                $row->created_at,
                $row->customer,
                $row->subject,
                $row->message,
                $row->created_by,
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
    public function runquery($onlycoun = "no"){
        $column_order = array(
            'feedbacks.id',
            'feedbacks.subject',
            'customer_detail.name',
            'feedbacks.created_at',
            'users.first_name',
            'feedbacks.status'
        );
        $column_search = array(
            'feedbacks.id',
            'feedbacks.subject',
            'customer_detail.name',
            'feedbacks.created_at',
            'users.first_name',
            'feedbacks.status'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('feedbacks.id as id');
        }
        else{
            $this->db->select('
                feedbacks.*,
                customer_detail.name as customer,
                CONCAT(users.first_name," ",users.last_name) as created_by
            ');
        }
        $this->db->from('feedbacks');
        $this->db->join('companies as customer_detail', 'customer_detail.id = feedbacks.customer_id', 'left');
        $this->db->join('sma_users as users', 'users.id = feedbacks.created_by', 'left');
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
        if($_POST['status'] != "" && $_POST['status'] != "all"){
            $this->db->where('feedbacks.status',$_POST['status']);
        }
        if($_POST['customer'] != "" && $_POST['customer'] != "all"){
            $this->db->where('feedbacks.customer_id',$_POST['customer']);
        }
        if($_POST['to'] != ""){
            $this->db->where('feedbacks.created_at >= ',$_POST['to']);
        }
        if($_POST['from'] != ""){
            $this->db->where('feedbacks.created_at <= ',$_POST['from']);
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }
    public function delete(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id,status');
                $this->db->from('feedbacks');
                $this->db->where('id',$id);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $feedback =  $q->result()[0];
                    if($feedback->status == 0){
                        $this->db->delete('feedbacks', array('id' => $id));
                        $senddata['status'] = true;
                        $senddata['message'] = "Feedback delete successfully!";
                    }
                    else{
                        $senddata['message'] = "You cannot delete resolved feedback!";
                    }
                }
                else{
                    $senddata['message'] = "Feedback not found!";
                }
            }
            else{
                $senddata['message'] = "Enter Reason!";
            }
        }
        else{
            $senddata['message'] = "Permission Denied!";
        }
        echo json_encode($senddata);

    }
}