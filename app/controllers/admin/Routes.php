<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Routes extends MY_Controller {
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('routes'), 'page' => lang('Routes')), array('link' => '#', 'page' => lang('List')));
        $meta = array('page_title' => lang('Routes'), 'bc' => $bc);
        $this->page_construct2('routes/index', $meta, $this->data);
    }
    public function get_routes(){
        // Count Total Rows
        $this->db->from('routes_tb');
        $totalq = $this->db->get();
        $this->runquery_routes('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_routes();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();
        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/customers?route_id=".$row->id).'" >Shop List</a>';
            $button .= '<a class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/routes/edit/".$row->id).'" >Edit</a>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->name,
                $row->address,
                $row->created_at,
                $row->created_by,
                $row->status == 0 ? "Deactive" : "Active",
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
    public function runquery_routes($onlycoun = "no"){
        $column_search = array(
            'routes.id',
            'routes.name',
            'routes.address',
            'routes.created_at',
            'users.first_name',
            'users.last_name',
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('routes.id as id');
        }
        else{
            $this->db->select('
                routes.id,
                routes.name,
                routes.address,
                routes.created_at,
                CONCAT(users.first_name," ",users.last_name) as created_by,
                routes.status
            ');
        }
        $this->db->from('routes_tb as routes');
        $this->db->join('sma_users as users', 'users.id = routes.created_by', 'left');
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
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('routes'), 'page' => lang('routes')), array('link' => '#', 'page' => lang('Add Route')));
        $meta = array('page_title' => lang('Add Route'), 'bc' => $bc);
        $this->page_construct2('routes/add', $meta, $this->data);
    }
    function edit($id){
        if($id != ""){

            $this->db->from('routes_tb');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['route'] = $q->result()[0];

                $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('routes'), 'page' => lang('Routes')), array('link' => '#', 'page' => lang('Edit Route')));
                $meta = array('page_title' => lang('Edit Routes'), 'bc' => $bc);
                $this->page_construct2('routes/edit', $meta, $this->data);
            }
            else{
                redirect(base_url('admin/routes'));
            }
        }
        else{
            redirect(base_url('admin/routes'));
        }

    }
    public function create(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $name = $this->input->post('name');
        $address = $this->input->post('address');
        if($name != "" && $address != ""){
            $this->db->select('*');
            $this->db->from('routes_tb');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Route already available";
            }
            else{
                $insert['name'] = $name;
                $insert['address'] = $address;
                $insert['created_by'] = $this->session->userdata('user_id');
                $this->db->insert('routes_tb',$insert);
                $senddata['message'] = "Route create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $address = $this->input->post('address');
        $status = $this->input->post('status');
        if($name != "" && $address != ""){
            $set['name'] = $name;
            $set['address'] = $address;
            $set['status'] = $status;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('routes_tb');
            $senddata['message'] = "Route update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('companies');
                $this->db->where('route_id = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('routes_tb', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Route delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete customer/supplier then delete this route!";
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
