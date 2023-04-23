<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends MY_Controller {
    function __construct(){
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('suppliers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('companies_model');
    }
    // New Code
    public function index(){

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('suppliers')));
        $meta = array('page_title' => lang('suppliers'), 'bc' => $bc);
        $this->page_construct2('suppliers/index', $meta, $this->data);
    }
    public function get_suppliers(){
        // Count Total Rows
        $this->db->from('companies');
        $totalq = $this->db->get();
        $this->runquery_users('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_users();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/products?supplier=".$row->id).'" >Products List</a>';
            $button .= '<a href="'.base_url("admin/suppliers/edit/".$row->id).'" class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini">Edit</a>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->name,
                $row->company,
                $row->email,
                $row->phone,
                $row->postal_code,
                $row->city,
                $row->state,
                $row->country,
                $row->cf1,
                $row->gst_no,
                $row->linces,
                $row->address,
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
    public function runquery_users($onlycoun = "no"){
        $column_order = array(
            'companies.id',
            'companies.name',
            'companies.company',
            'companies.email',
            'companies.phone',
            'companies.postal_code',
            'companies.city',
            'companies.state',
            'companies.country',
            'companies.cf1',
            'companies.gst_no',
            'companies.linces',
            'companies.address'
        );
        $column_search = array(
            'companies.id',
            'companies.name',
            'companies.company',
            'companies.email',
            'companies.phone',
            'companies.postal_code',
            'companies.city',
            'companies.state',
            'companies.country',
            'companies.cf1',
            'companies.gst_no',
            'companies.linces',
            'companies.address'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('companies.id as id');
        }
        else{
            $this->db->select('
                companies.id,
                companies.name,
                companies.company,
                companies.email,
                companies.phone,
                companies.postal_code,
                companies.city,
                companies.state,
                companies.country,
                companies.cf1,
                companies.gst_no,
                companies.linces,
                companies.address
            ');
        }
        $this->db->from('companies as companies');
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
        $this->db->where('companies.group_id',4);
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
                $this->db->select('id');
                $this->db->from('products');
                $this->db->where('supplier1 = '.$id.' OR supplier2 = '.$id.' OR supplier3 = '.$id.' OR supplier4 = '.$id.' OR supplier5 = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->select('id');
                    $this->db->from('purchases');
                    $this->db->where('supplier_id = '.$id);
                    $q2 = $this->db->get();
                    if($q2->num_rows() == 0){
                        $this->db->delete('companies', array('id' => $id));
                        $senddata['status'] = true;
                        $senddata['message'] = "Supplier delete successfully!";
                    }
                    else{
                        $senddata['message'] = "Delete purchases then delete this supplier!";
                    }
                }
                else{
                    $senddata['message'] = "Delete Products or remove this supplier form that products then delete this supplier!";
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
    public function add(){
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('suppliers')));
        $meta = array('page_title' => lang('Add Supplier'), 'bc' => $bc);
        $this->page_construct2('suppliers/add', $meta, $this->data);
    }
    public function edit($id){

        $this->db->select('*');
        $this->db->from('companies');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0 ){
            $this->data['supplier'] = $q->result()[0];
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('suppliers')));
            $meta = array('page_title' => lang('Edit Supplier'), 'bc' => $bc);
            $this->page_construct2('suppliers/edit', $meta, $this->data);
        }
        else{
            redirect(base_url('admin/suppliers'));
        }
    }
    public function insert(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $name = $this->input->post('name');
        $company = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $postal = $this->input->post('postal');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $ntn = $this->input->post('ntn');
        $gst_no = $this->input->post('gst');
        $linces = $this->input->post('linces');
        if($name != "" && $company != ""){
            $this->db->select('*');
            $this->db->from('companies');
            $this->db->where('name',$name);
            $this->db->where('group_id',4);
            $q = $this->db->get();
            if($q->num_rows() == 0){
                $data = array(
                    'name' => $name,
                    'company' => $company,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => $address,
                    'postal_code' => $postal,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'cf1' => $ntn,
                    'gst_no' => $gst_no,
                    'linces' => $linces,
                    'group_id' => '4',
                    'group_name' => 'supplier'
                );
                $this->db->insert('companies',$data);
                $senddata['status'] = true;
                $senddata['message'] = "Supplier add successfully!";
            }
            else{
                $senddata['message'] = "Supplier name already available";
            }
        }
        else{
            $senddata['message'] = "Filled Required Fields";
        }
        echo json_encode($senddata);
    }
    public function update(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $company = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $postal = $this->input->post('postal');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $ntn = $this->input->post('ntn');
        $gst_no = $this->input->post('gst');
        $linces = $this->input->post('linces');
        if($name != "" && $company != ""){
            $data = array(
                'name' => $name,
                'company' => $company,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'postal_code' => $postal,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'cf1' => $ntn,
                'gst_no' => $gst_no,
                'linces' => $linces
            );
            $this->db->set($data);
            $this->db->where('id',$id);
            $this->db->update('companies');
            $senddata['status'] = true;
            $senddata['message'] = "Supplier update successfully!";
        }
        else{
            $senddata['message'] = "Filled Required Fields";
        }
        echo json_encode($senddata);
    }

    
}
