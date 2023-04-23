<?php defined('BASEPATH') or exit('No direct script access allowed');


class Complains extends MY_Controller
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
        $this->load->admin_model('complains_model');
        $this->data['logo'] = true;
    }
    public function index(){

        $this->data['spriority'] = $this->input->get('priority');
        $this->data['scustomer'] = $this->input->get('customer');
        $this->data['sstatus'] = $this->input->get('status');
        $this->data['sto'] = $this->input->get('to');
        $this->data['sfrom'] = $this->input->get('from');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Complains'));
        $meta = array('page_title' => 'Complains', 'bc' => $bc);
        $this->page_construct2('complains/index', $meta, $this->data);

    }
    public function get_list(){
        // Count Total Rows
        $this->db->from('complains');
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
            $button = '<button class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light md-btn-mini complaindetail" data-complain='; 
            $button .= "'".json_encode($row)."'";
            $button .= ' >View</button>';
            if($row->status == 0){
                $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini resolvedbtn" data-id="'.$row->id.'" >Resolved</button>';
                $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            }
            
            $data[] = array(
                $row->id,
                $row->subject,
                $row->customer,
                $row->created_at,
                $row->created_by,
                $row->priority,
                $row->status == 0 ? "Not Resolved" : "Resolved",
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
            'complains.id',
            'complains.subject',
            'customer_detail.name',
            'complains.created_at',
            'users.first_name',
            'complains.priority',
            'complains.status'
        );
        $column_search = array(
            'complains.id',
            'complains.subject',
            'customer_detail.name',
            'complains.created_at',
            'users.first_name',
            'complains.priority',
            'complains.status'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('complains.id as id');
        }
        else{
            $this->db->select('
                complains.*,
                customer_detail.name as customer,
                CONCAT(users.first_name," ",users.last_name) as created_by,
                CONCAT(rusers.first_name,rusers.last_name) as resolved_by
            ');
        }
        $this->db->from('complains');
        $this->db->join('companies as customer_detail', 'customer_detail.id = complains.customer_id', 'left');
        $this->db->join('sma_users as users', 'users.id = complains.created_by', 'left');
        $this->db->join('sma_users as rusers', 'rusers.id = complains.resolved_by', 'left');
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
        if($_POST['priority'] != "" && $_POST['priority'] != "all"){
            $this->db->where('complains.priority',$_POST['priority']);
        }
        if($_POST['status'] != "" && $_POST['status'] != "all"){
            $this->db->where('complains.status',$_POST['status']);
        }
        if($_POST['customer'] != "" && $_POST['customer'] != "all"){
            $this->db->where('complains.customer_id',$_POST['customer']);
        }
        if($_POST['to'] != ""){
            $this->db->where('complains.created_at >= ',$_POST['to']);
        }
        if($_POST['from'] != ""){
            $this->db->where('complains.created_at <= ',$_POST['from']);
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }
    public function resolved(){

        $id = $this->input->post('complain_id');
        $remarks = $this->input->post('remarks');
        $sendvalue['status'] = false;
        $sendvalue['message'] = 'Try Again';
        if($remarks != ""){
            $set['resolved_date'] = date('Y-m-d H:i:s');
            $set['resolved_by'] = $this->session->userdata('user_id');
            $set['remarks'] = $remarks;
            $set['status'] = 1;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('complains');
            $sendvalue['status'] = true;
            $sendvalue['message'] = "Complain Resolved";
        }
        else{
            $sendvalue['message'] = "Enter Remarks";
        }
        echo json_encode($sendvalue);
    }
    public function delete(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id,status');
                $this->db->from('complains');
                $this->db->where('id',$id);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $complain =  $q->result()[0];
                    if($complain->status == 0){
                        $this->db->delete('complains', array('id' => $id));
                        $senddata['status'] = true;
                        $senddata['message'] = "Complain delete successfully!";
                    }
                    else{
                        $senddata['message'] = "You cannot delete resolved complain!";
                    }
                }
                else{
                    $senddata['message'] = "Complain not found!";
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