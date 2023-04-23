<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class Logs extends MY_Controller{
    public function __construct(){
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
    }
    public function index(){
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Activity Logs'));
        $meta = array('page_title' => 'Activity Logs', 'bc' => $bc);
        $this->page_construct2('logs/index', $meta, $this->data);
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
            
            $data[] = array(
                $row->created_at,
                $row->username,
                $row->product_id,
                $row->po_id,
                $row->purchase_id,
                $row->so_id,
                $row->sale_id,
                $row->transfer_id,
                $row->store_id,
                $row->note,
                $row->location,
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
            'sma_user_activities.created_at',
            'sma_users.username',
            'sma_user_activities.product_id',
            'sma_user_activities.po_id',
            'sma_user_activities.purchase_id',
            'sma_user_activities.so_id',
            'sma_user_activities.sale_id',
            'sma_user_activities.transfer_id',
            'sma_user_activities.store_id',
            'sma_user_activities.note',
            'sma_user_activities.location'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('sma_user_activities.id');
        }
        else{
            $this->db->select('
                sma_user_activities.created_at,
                sma_users.username,
                sma_user_activities.product_id,
                sma_user_activities.po_id,
                sma_user_activities.purchase_id,
                sma_user_activities.so_id,
                sma_user_activities.sale_id,
                sma_user_activities.transfer_id,
                sma_user_activities.store_id,
                sma_user_activities.note,
                sma_user_activities.location
            ');
        }
        $this->db->from('sma_user_activities');
        $this->db->join('sma_users', 'sma_users.id = sma_user_activities.action_by', 'left');
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
        // if($_POST['status'] != "" && $_POST['status'] != "all"){
        //     $this->db->where('complains.status',$_POST['status']);
        // }
        // $this->db->where('sma_user_activities.created_at','2022-10-08 20:25:59');
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }







    public function index_old(){
        $this->data['product_id'] = $this->input->get('product_id') == "" ? "all" : $this->input->get('product_id');
        $this->data['po_id'] = $this->input->get('po_id') == "" ? "all" : $this->input->get('po_id');
        $this->data['purchase_id'] = $this->input->get('purchase_id') == "" ? "all" : $this->input->get('purchase_id');
        $this->data['so_id'] = $this->input->get('so_id') == "" ? "all" : $this->input->get('so_id');
        $this->data['sale_id'] = $this->input->get('sale_id') == "" ? "all" : $this->input->get('sale_id');
        $this->data['transfer_id'] = $this->input->get('transfer_id') == "" ? "all" : $this->input->get('transfer_id');
        $this->data['store_id'] = $this->input->get('store_id') == "" ? "all" : $this->input->get('store_id');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Activity Logs'));
        $meta = array('page_title' => 'Activity Logs', 'bc' => $bc);
        $this->page_construct('logs/index', $meta, $this->data);
    }
    public function get_logs(){
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
            $data[] = array(
                '<div style="width: 150px !important;" >'.$row->created_at.'</div>',
                '<div style="width: 150px !important;" >'.$row->username.'</div>',
                '<div style="width: 100px !important;" >'.$row->product_id.'</div>',
                '<div style="width: 100px !important;" >'.$row->po_id.'</div>',
                '<div style="width: 100px !important;" >'.$row->purchase_id.'</div>',
                '<div style="width: 100px !important;" >'.$row->so_id.'</div>',
                '<div style="width: 100px !important;" >'.$row->sale_id.'</div>',
                '<div style="width: 100px !important;" >'.$row->transfer_id.'</div>',
                '<div style="width: 100px !important;" >'.$row->store_id.'</div>',
                '<div style="width: 600px !important;overflow-wrap: anywhere;" >'.$row->note.'</div>',
                '<div style="width: 300px !important;overflow-wrap: anywhere;" >'.$row->location.'</div>',
            );
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsFiltered,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        );
        // Output to JSON format
        echo json_encode($output);

    }
    public function runquery2($onlycoun = "no"){
        $column_order = array('created_at','action_by','note','','location');
        $column_search = array(
            'sma_users.username',
            'sma_user_activities.action_by',
            'sma_user_activities.note',
            'sma_user_activities.location'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('sma_user_activities.id');
        }
        else{
            $this->db->select('
                sma_user_activities.created_at,
                sma_users.username,
                sma_user_activities.product_id,
                sma_user_activities.po_id,
                sma_user_activities.purchase_id,
                sma_user_activities.so_id,
                sma_user_activities.sale_id,
                sma_user_activities.transfer_id,
                sma_user_activities.store_id,
                sma_user_activities.note,
                sma_user_activities.location
            ');
        }
        $this->db->from('sma_user_activities');
        $this->db->join('sma_users', 'sma_users.id = sma_user_activities.action_by', 'left');
        $i = 0;
        // loop searchable columns 
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
        if($_POST['product_id'] != "" && $_POST['product_id'] != "all"){
            $this->db->where('sma_user_activities.product_id',$_POST['product_id']);
        }
        if($_POST['po_id'] != "" && $_POST['po_id'] != "all"){
            $this->db->where('sma_user_activities.po_id',$_POST['po_id']);
        }
        if($_POST['purchase_id'] != "" && $_POST['purchase_id'] != "all"){
            $this->db->where('sma_user_activities.purchase_id',$_POST['purchase_id']);
        }
        if($_POST['so_id'] != "" && $_POST['so_id'] != "all"){
            $this->db->where('sma_user_activities.so_id',$_POST['so_id']);
        }
        if($_POST['sale_id'] != "" && $_POST['sale_id'] != "all"){
            $this->db->where('sma_user_activities.sale_id',$_POST['sale_id']);
        }
        if($_POST['transfer_id'] != "" && $_POST['transfer_id'] != "all"){
            $this->db->where('sma_user_activities.transfer_id',$_POST['transfer_id']);
        }
        if($_POST['store_id'] != "" && $_POST['store_id'] != "all"){
            $this->db->where('sma_user_activities.store_id',$_POST['store_id']);
        }
        // $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        $this->db->order_by('sma_user_activities.created_at', 'desc');
    }
}
