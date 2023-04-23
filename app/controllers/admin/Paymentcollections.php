<?php defined('BASEPATH') or exit('No direct script access allowed');


class Paymentcollections extends MY_Controller
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
        $this->load->admin_model('paymentcollections_model');
        $this->data['logo'] = true;
    }
    public function index(){

        $this->data['scustomer'] = $this->input->get('customer');
        $this->data['sstatus'] = $this->input->get('status');
        $this->data['sto'] = $this->input->get('to');
        $this->data['sfrom'] = $this->input->get('from');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Payment Collections'));
        $meta = array('page_title' => 'Payment Collections', 'bc' => $bc);
        $this->page_construct2('paymentcollections/index', $meta, $this->data);

    }
    public function get_list(){
        // Count Total Rows
        $this->db->from('paymentcollections');
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
            $button = ''; 
            if($row->status == 0){
                $button .= '<button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini receivedbtn" data-id="'.$row->id.'" >Received</button>';
                $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            }
            
            $data[] = array(
                $row->id,
                $row->created_at,
                $row->created_by,
                $row->customer,
                $row->amount,
                $row->paid_by,
                $row->cheque_no,
                $row->note,
                $row->received_at,
                $row->received_by,
                $row->status == 0 ? "Not Received" : "Received",
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
        $column_search = array(
            'paymentcollections.id',
            'customer_detail.name',
            'paymentcollections.paid_by',
            'paymentcollections.cheque_no',
            'paymentcollections.amount',
            'paymentcollections.note',
            'paymentcollections.created_at',
            'users.first_name',
            'users.last_name',
            'rusers.first_name',
            'rusers.last_name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('paymentcollections.id as id');
        }
        else{
            $this->db->select('
                paymentcollections.*,
                customer_detail.name as customer,
                CONCAT(users.first_name," ",users.last_name) as created_by,
                CONCAT(rusers.first_name,rusers.last_name) as received_by
            ');
        }
        $this->db->from('paymentcollections');
        $this->db->join('companies as customer_detail', 'customer_detail.id = paymentcollections.customer_id', 'left');
        $this->db->join('sma_users as users', 'users.id = paymentcollections.created_by', 'left');
        $this->db->join('sma_users as rusers', 'rusers.id = paymentcollections.received_by', 'left');
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
            $this->db->where('paymentcollections.status',$_POST['status']);
        }
        if($_POST['customer'] != "" && $_POST['customer'] != "all"){
            $this->db->where('paymentcollections.customer_id',$_POST['customer']);
        }
        if($_POST['to'] != ""){
            $this->db->where('paymentcollections.created_at >= ',$_POST['to']);
        }
        if($_POST['from'] != ""){
            $this->db->where('paymentcollections.created_at <= ',$_POST['from']);
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
                $this->db->from('paymentcollections');
                $this->db->where('id',$id);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $paymentcollection =  $q->result()[0];
                    if($paymentcollection->status == 0){
                        $this->db->delete('paymentcollections', array('id' => $id));
                        $senddata['status'] = true;
                        $senddata['message'] = "Payment Collection delete successfully!";
                    }
                    else{
                        $senddata['message'] = "You cannot delete received payment collection!";
                    }
                }
                else{
                    $senddata['message'] = "Payment collection not found!";
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
    public function received(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id,status');
                $this->db->from('paymentcollections');
                $this->db->where('id',$id);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $paymentcollection =  $q->result()[0];
                    if($paymentcollection->status == 0){
                        $set['received_by'] = date('Y-m-d H:i:s');
                        $set['received_by'] = $this->session->userdata('user_id');
                        $set['status'] = 1;
                        $this->db->set($set);
                        $this->db->where('id',$id);
                        $this->db->update('paymentcollections');
                        $senddata['status'] = true;
                        $senddata['message'] = "Payment Collection received successfully!";
                    }
                    else{
                        $senddata['message'] = "Already received";
                    }
                }
                else{
                    $senddata['message'] = "Payment collection not found!";
                }
            }
            else{
                $senddata['message'] = "Enter Receiveing note!";
            }
        }
        else{
            $senddata['message'] = "Permission Denied!";
        }
        echo json_encode($senddata);

    }
}