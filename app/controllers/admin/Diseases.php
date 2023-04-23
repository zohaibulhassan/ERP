<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class Diseases extends MY_Controller{
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Diseases')));
        $meta = array('page_title' => lang('Diseases'), 'bc' => $bc);
        $this->page_construct2('diseases/index', $meta, $this->data);
    }
    public function get_diseases(){
        // Count Total Rows
        $this->db->from('diseases');
        $totalq = $this->db->get();
        $this->runquery_diseases('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_diseases();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/products?disease=".$row->id).'" >Products List</a>';
            $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-name="'.$row->name.'" data-description="'.$row->description.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                '<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" >',
                $row->id,
                $row->name,
                $row->no_products,
                $row->description,
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
    public function runquery_diseases($onlycoun = "no"){
        $column_order = array(
            null,
            'diseases.id',
            'diseases.name',
            'diseases.description',
            5
        );
        $column_search = array(
            'diseases.id',
            'diseases.name',
            'diseases.description'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('diseases.id as id');
        }
        else{
            $this->db->select('
                diseases.image,
                diseases.id,
                diseases.name,
                diseases.description,
                (
                    SELECT 
                        COUNT(sma_products.id) 
                    FROM 
                        sma_products
                    LEFT JOIN  sma_product_formulas as f ON f.id = sma_products.formulas
                    WHERE 
                        f.diseases = diseases.id

                ) as no_products
            ');
        }
        $this->db->from('diseases as diseases');
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
    public function insert_disease(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        if($name != ""){
            $this->db->select('*');
            $this->db->from('diseases');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Disease already available";
            }
            else{
                $insert['name'] = $name;
                $insert['description'] = $description;
                $insert['created_by'] = $this->session->userdata('user_id');
                $this->db->insert('diseases',$insert);
                $senddata['message'] = "Disease create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_disease(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        if($name != ""){
            $set['name'] = $name;
            $set['description'] = $description;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('diseases');
            $senddata['message'] = "Disease update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_disease(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('products');
                $this->db->where('disease = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('diseases', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Disease delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete purchases then delete this disease!";
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