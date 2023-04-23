<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class Manufacturers extends MY_Controller{
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
    function index(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Manufacturers')));
        $meta = array('page_title' => lang('Manufacturers'), 'bc' => $bc);
        $this->page_construct2('manufacturers/index', $meta, $this->data);
    }
    public function get_manufacturers(){
        // Count Total Rows
        $this->db->from('manufacturers');
        $totalq = $this->db->get();
        $this->runquery_manufacturers('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_manufacturers();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/products?manufacturer=".$row->id).'" >Products List</a>';
            $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-name="'.$row->name.'" data-email="'.$row->email.'" data-phone="'.$row->phone.'" data-address="'.$row->address.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->name,
                $row->email,
                $row->phone,
                $row->address,
                $row->no_products,
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
    public function runquery_manufacturers($onlycoun = "no"){
        $column_order = array(
            'manufacturers.id',
            'manufacturers.name',
            'manufacturers.email',
            'manufacturers.phone',
            'manufacturers.address',
        );
        $column_search = array(
            'manufacturers.id',
            'manufacturers.name',
            'manufacturers.email',
            'manufacturers.phone',
            'manufacturers.address'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('manufacturers.id as id');
        }
        else{
            $this->db->select('
                manufacturers.id,
                manufacturers.name,
                manufacturers.email,
                manufacturers.phone,
                manufacturers.address,
                (
                    SELECT COUNT(sma_products.id) FROM sma_products WHERE sma_products.manufacturer = manufacturers.id

                ) as no_products
            ');
        }
        $this->db->from('manufacturers as manufacturers');
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
    public function insert_manufacturer(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        // $code = $this->input->post('code');
        $name = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        if($name != ""){
            $this->db->select('*');
            $this->db->from('manufacturers');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Manufacturer already available";
            }
            else{
                // $insert['code'] = $code;
                $insert['name'] = $name;
                $insert['phone'] = $phone;
                $insert['email'] = $email;
                $insert['address'] = $address;
                $insert['created_by'] = $this->session->userdata('user_id');
                $this->db->insert('manufacturers',$insert);
                $senddata['message'] = "Manufacturer code/name create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_manufacturer(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        // $code = $this->input->post('code');
        $name = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        if($name != ""){
            // $set['code'] = $code;
            $set['name'] = $name;
            $set['phone'] = $phone;
            $set['email'] = $email;
            $set['address'] = $address;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('manufacturers');
            $senddata['message'] = "Manufacturer update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_manufacturer(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('products');
                $this->db->where('manufacturer = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('manufacturers', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Manufacturer delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete purchases then delete this manufacturer!";
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