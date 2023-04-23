<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Printers extends MY_Controller {
    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('settings', $this->Settings->user_language);
        $this->load->admin_model('general_model');

    }
    public function index(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('printers'), 'page' => lang('Printers')), array('link' => '#', 'page' => lang('List')));
        $meta = array('page_title' => lang('Printers'), 'bc' => $bc);
        $this->page_construct2('printers/index', $meta, $this->data);
    }
    public function get_printers(){
        // Count Total Rows
        $this->db->from('printers');
        $totalq = $this->db->get();
        $this->runquery_printers('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_printers();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();
        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/printers/edit/".$row->id).'" >Edit</a>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->title,
                $row->type,
                $row->profile,
                $row->char_per_line,
                $row->path,
                $row->ip_address,
                $row->port,
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
    public function runquery_printers($onlycoun = "no"){
        $column_search = array(
            'printers.id',
            'printers.title',
            'printers.type',
            'printers.profile',
            'printers.char_per_line',
            'printers.path',
            'printers.ip_address',
            'printers.port',
            'users.first_name',
            'users.last_name',
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('printers.id as id');
        }
        else{
            $this->db->select('
                printers.id,
                printers.title,
                printers.type,
                printers.profile,
                printers.char_per_line,
                printers.path,
                printers.ip_address,
                printers.port,
                CONCAT(users.first_name," ",users.last_name) as created_by,
            ');
        }
        $this->db->from('printers as printers');
        $this->db->join('users as users', 'users.id = printers.user_id', 'left');
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
    function add(){
        $this->db->select('id,first_name,last_name');
        $this->db->from('users');
        $this->db->where('active',1);
        $this->data['printer_users'] = $this->db->get()->result();


        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('printers'), 'page' => lang('printers')), array('link' => '#', 'page' => lang('Add Printer')));
        $meta = array('page_title' => lang('Add Printer'), 'bc' => $bc);
        $this->page_construct2('printers/add', $meta, $this->data);
    }
    function edit($id){
        if($id != ""){

            $this->db->from('printers');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['printer'] = $q->result()[0];


                $this->db->select('id,first_name,last_name');
                $this->db->from('users');
                $this->db->where('active',1);
                $this->data['printer_users'] = $this->db->get()->result();
        

                $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('printers'), 'page' => lang('Printers')), array('link' => '#', 'page' => lang('Edit Printer')));
                $meta = array('page_title' => lang('Edit Printer'), 'bc' => $bc);
                $this->page_construct2('printers/edit', $meta, $this->data);
            }
            else{
                redirect(base_url('admin/printers'));
            }
        }
        else{
            redirect(base_url('admin/printers'));
        }

    }
    public function create(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $title = $this->input->post('title');
        $type = $this->input->post('type');
        $profile = $this->input->post('profile');
        $charline = $this->input->post('charline');
        $path = $this->input->post('path');
        $ipaddress = $this->input->post('ipaddress');
        $port = $this->input->post('port');
        $user = $this->input->post('user');

        $insert['title'] = $title;
        $insert['type'] = $type;
        $insert['profile'] = $profile;
        $insert['char_per_line'] = $charline;
        $insert['path'] = $path;
        $insert['ip_address'] = $ipaddress;
        $insert['port'] = $port;
        $insert['user_id'] = $user;
        $this->db->insert('printers',$insert);
        $senddata['message'] = "Printers create successfully";
        $senddata['status'] = true;
        echo json_encode($senddata);
    }
    public function update(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $type = $this->input->post('type');
        $profile = $this->input->post('profile');
        $charline = $this->input->post('charline');
        $path = $this->input->post('path');
        $ipaddress = $this->input->post('ipaddress');
        $port = $this->input->post('port');
        $user = $this->input->post('user');
        $set['title'] = $title;
        $set['type'] = $type;
        $set['profile'] = $profile;
        $set['char_per_line'] = $charline;
        $set['path'] = $path;
        $set['ip_address'] = $ipaddress;
        $set['port'] = $port;
        $set['user_id'] = $user;
        $this->db->set($set);
        $this->db->where('id',$id);
        $this->db->update('printers');
        $senddata['message'] = "Printer update successfully";
        $senddata['status'] = true;
        echo json_encode($senddata);
    }
    public function delete(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->delete('printers', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Printer delete successfully!";
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
