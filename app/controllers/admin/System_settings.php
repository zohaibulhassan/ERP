<?php defined('BASEPATH') OR exit('No direct script access allowed');

class system_settings extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (
            !$this->Owner && 
            !$this->Admin && 
            !$this->GP['bulk_discount'] && 
            !$this->GP['wallet_view'] &&
            !$this->GP['wallet_add'] &&
            !$this->GP['wallet_edit'] &&
            !$this->GP['wallet_transations']
        ) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect('admin');
        }
        $this->lang->admin_load('settings', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('settings_model');
        $this->load->admin_model('general_model');
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
    }

    // New Code
    // Product Category Code Start
    function categories(){
        $this->data['categories'] = $this->general_model->GetAllCategories(0);
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('categories')));
        $meta = array('page_title' => lang('categories'), 'bc' => $bc);
        $this->page_construct2('settings/categories', $meta, $this->data);
    }
    public function get_categories(){
        // Count Total Rows
        $this->db->from('categories');
        $totalq = $this->db->get();
        $this->runquery_categories('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_categories();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/products?category=".$row->id).'" >Products List</a>';
            $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-pid="'.$row->parent_id.'" data-code="'.$row->code.'" data-name="'.$row->name.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                '<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" >',
                $row->id,
                $row->code,
                $row->name,
                $row->parent_name == "" ? "No Parent" : $row->parent_name,
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
    public function runquery_categories($onlycoun = "no"){
        $column_order = array(
            null,
            'categories.id',
            'categories.code',
            'categories.name',
            'parent.name',
            6
        );
        $column_search = array(
            'categories.id',
            'categories.code',
            'categories.name',
            'parent.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('categories.id as id');
        }
        else{
            $this->db->select('
                categories.id,
                categories.code,
                categories.name,
                categories.image,
                parent.name as parent_name,
                (
                    SELECT COUNT(sma_products.id) FROM sma_products WHERE sma_products.category_id = categories.id

                ) as no_products,
                categories.parent_id
            ');
        }
        $this->db->from('categories as categories');
        $this->db->join('categories as parent', 'parent.id = categories.parent_id', 'left');
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
    public function insert_category(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $category = $this->input->post('category');
        if($name != ""){
            $this->db->select('*');
            $this->db->from('categories');
            $this->db->where('code = "'.$code.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $category = $q->result()[0];
                if($category->code == $code){
                    $senddata['message'] = "Category code already available";
                }
                else if($category->name == $name){
                    $senddata['message'] = "Category name already available";
                }
                else{
                    $senddata['message'] = "Category already available";
                }
            }
            else{
                $insert['code'] = $code;
                $insert['name'] = $name;
                $insert['parent_id'] = $category;
                $this->db->insert('categories',$insert);
                $senddata['message'] = "Category create successfully";
                $senddata['status'] = true;

            }
        }
        else{
            $senddata['message'] = "Enter Category Name";
        }
        echo json_encode($senddata);
    }
    public function update_category(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $category = $this->input->post('category');
        if($code != ""){
            if($name != ""){
                $set['code'] = $code;
                $set['name'] = $name;
                $set['parent_id'] = $category;
                $this->db->set($set);
                $this->db->where('id',$id);
                $this->db->update('categories');
                $senddata['message'] = "Category update successfully";
                $senddata['status'] = true;
            }
            else{
                $senddata['message'] = "Enter Category Name";
            }
        }
        else{
            $senddata['message'] = "Enter Category!";
        }
        echo json_encode($senddata);
    }
    public function delete_category(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('products');
                $this->db->where('category_id = '.$id.' OR subcategory_id = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->select('id');
                    $this->db->from('categories');
                    $this->db->where('parent_id = '.$id);
                    $q2 = $this->db->get();
                    if($q2->num_rows() == 0){
                        $this->db->delete('categories', array('id' => $id));
                        $senddata['status'] = true;
                        $senddata['message'] = "Category delete successfully!";
                    }
                    else{
                        $senddata['message'] = "Delete sub category then delete this category!";
                    }
                }
                else{
                    $senddata['message'] = "Delete Products or remove this category form that products then delete this category!";
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
    // Product Category Code End
    // Expense Category Code Start
    function expense_categories(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('expense_categories')));
        $meta = array('page_title' => lang('categories'), 'bc' => $bc);
        $this->page_construct2('settings/expense_categories', $meta, $this->data);
    }
    public function get_excategories(){
        // Count Total Rows
        $this->db->from('expense_categories');
        $totalq = $this->db->get();
        $this->runquery_excategories('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_excategories();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-code="'.$row->code.'" data-name="'.$row->name.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->code,
                $row->name,
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
    public function runquery_excategories($onlycoun = "no"){
        $column_order = array(
            'categories.id',
            'categories.code',
            'categories.name'
        );
        $column_search = array(
            'categories.id',
            'categories.code',
            'categories.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('categories.id as id');
        }
        else{
            $this->db->select('
                categories.id,
                categories.code,
                categories.name,
            ');
        }
        $this->db->from('expense_categories as categories');
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
    public function insert_excategory(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        if($name != ""){
            if($code == ""){
                $code = strtolower(str_replace(' ','_',$name));
            }
            $this->db->select('*');
            $this->db->from('expense_categories');
            $this->db->where('name = "'.$name.'" OR code = "'.$code.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $category = $q->result()[0];
                if($category->code == $code){
                    $senddata['message'] = "Category code already available";
                }
                else if($category->name == $name){
                    $senddata['message'] = "Category name already available";
                }
                else{
                    $senddata['message'] = "Category already available";
                }
            }
            else{
                $insert['code'] = $code;
                $insert['name'] = $name;
                $this->db->insert('expense_categories',$insert);
                $senddata['message'] = "Category create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter Category Name";
        }
        echo json_encode($senddata);
    }
    public function update_excategory(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        if($name != ""){
            if($code == ""){
                $code = strtolower(str_replace(' ','_',$name));
            }
                $set['code'] = $code;
            $set['name'] = $name;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('expense_categories');
            $senddata['message'] = "Category update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter Category Name";
        }
        echo json_encode($senddata);
    }
    public function delete_excategory(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('expenses');
                $this->db->where('category_id = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('expense_categories', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Category delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete expense or remove this category form that expense then delete this category!";
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
    // Expense Category Code End
    // Warehouses Code Start
    function warehouses(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('warehouses')));
        $meta = array('page_title' => lang('warehouses'), 'bc' => $bc);
        $this->page_construct2('settings/warehouses', $meta, $this->data);
    }
    public function get_warehouses(){
        // Count Total Rows
        $this->db->from('warehouses');
        $totalq = $this->db->get();
        $this->runquery_warehouses('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_warehouses();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-code="'.$row->code.'" data-name="'.$row->name.'"  data-phone="'.$row->phone.'"  data-email="'.$row->email.'"  data-address="'.$row->address.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->code,
                $row->name,
                $row->phone,
                $row->email,
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
    public function runquery_warehouses($onlycoun = "no"){
        $column_order = array(
            'warehouses.id',
            'warehouses.code',
            'warehouses.name',
            'warehouses.phone',
            'warehouses.email',
            'warehouses.address'
        );
        $column_search = array(
            'warehouses.id',
            'warehouses.code',
            'warehouses.name',
            'warehouses.phone',
            'warehouses.email',
            'warehouses.address'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('warehouses.id as id');
        }
        else{
            $this->db->select('
                warehouses.id,
                warehouses.code,
                warehouses.name,
                warehouses.phone,
                warehouses.email,
                warehouses.address
            ');
        }
        $this->db->from('warehouses as warehouses');
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
    public function insert_warehouse(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        if($name != ""){
            if($code == ""){
                $code = strtolower(str_replace(' ','_',$name));
            }
            $this->db->select('*');
            $this->db->from('warehouses');
            $this->db->where('name = "'.$name.'" OR code = "'.$code.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $warehouse = $q->result()[0];
                if($warehouse->code == $code){
                    $senddata['message'] = "Warehouse code already available";
                }
                else if($warehouse->name == $name){
                    $senddata['message'] = "Warehouse name already available";
                }
                else{
                    $senddata['message'] = "Warehouse already available";
                }
            }
            else{
                $insert['code'] = $code;
                $insert['name'] = $name;
                $insert['phone'] = $phone;
                $insert['email'] = $email;
                $insert['address'] = $address;
                $this->db->insert('warehouses',$insert);
                $senddata['message'] = "Warehouse create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_warehouse(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        if($name != ""){
            if($code == ""){
                $code = strtolower(str_replace(' ','_',$name));
            }
            $set['code'] = $code;
            $set['name'] = $name;
            $set['phone'] = $phone;
            $set['email'] = $email;
            $set['address'] = $address;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('warehouses');
            $senddata['message'] = "Warehouse update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_warehouse(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('purchase_items');
                $this->db->where('warehouse_id = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('warehouses', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Warehouse delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete purchases then delete this warehouse!";
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
    // Warehouses Code End
    // Own Company Code Start
    function own_companies(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Own Companies')));
        $meta = array('page_title' => lang('Own Companies'), 'bc' => $bc);
        $this->page_construct2('settings/own_companies', $meta, $this->data);
    }
    public function get_own_companies(){
        // Count Total Rows
        $this->db->from('own_companies');
        $totalq = $this->db->get();
        $this->runquery_own_companies('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_own_companies();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-companyname="'.$row->companyname.'" data-mobile="'.$row->mobile.'" data-rperson="'.$row->registerperson.'" data-ntn="'.$row->ntn.'" data-strn="'.$row->strn.'" data-srb="'.$row->srb.'" data-raddress="'.$row->registeraddress.'" data-waddress="'.$row->warehouseaddress.'" data-autoinvoice="'.$row->auto_invoice_gen.'" data-slip_header="'.$row->slip_header.'" data-slip_footer="'.$row->slip_footer.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->companyname,
                $row->mobile,
                $row->registerperson,
                $row->ntn,
                $row->strn,
                $row->srb,
                $row->registeraddress,
                $row->warehouseaddress,
                $row->auto_invoice_gen == 0 ? "Off" : "On",
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
    public function runquery_own_companies($onlycoun = "no"){
        $column_order = array(
            'own_companies.id',
            'own_companies.companyname',
            'own_companies.mobile',
            'own_companies.registerperson',
            'own_companies.ntn',
            'own_companies.strn',
            'own_companies.srb',
            'own_companies.registeraddress',
            'own_companies.warehouseaddress',
            'own_companies.auto_invoice_gen',
            'own_companies.slip_header',
            'own_companies.slip_footer'
        );
        $column_search = array(
            'own_companies.id',
            'own_companies.companyname',
            'own_companies.mobile',
            'own_companies.registerperson',
            'own_companies.ntn',
            'own_companies.strn',
            'own_companies.srb',
            'own_companies.registeraddress',
            'own_companies.warehouseaddress',
            'own_companies.auto_invoice_gen'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('own_companies.id as id');
        }
        else{
            $this->db->select('
                own_companies.id,
                own_companies.companyname,
                own_companies.mobile,
                own_companies.registerperson,
                own_companies.ntn,
                own_companies.strn,
                own_companies.srb,
                own_companies.registeraddress,
                own_companies.warehouseaddress,
                own_companies.auto_invoice_gen,
                own_companies.slip_header,
                own_companies.slip_footer
            ');
        }
        $this->db->from('own_companies as own_companies');
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
    public function insert_own_company(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $companyname = $this->input->post('companyname');
        $mobile = $this->input->post('mobile');
        $rperson = $this->input->post('rperson');
        $ntn = $this->input->post('ntn');
        $strn = $this->input->post('strn');
        $srb = $this->input->post('srb');
        $raddress = $this->input->post('raddress');
        $waddress = $this->input->post('waddress');
        $slipheder = $this->input->post('slipheder');
        $slipfooter = $this->input->post('slipfooter');
        $autoinvoice = $this->input->post('autoinvoice');
        if($companyname != "" && $mobile != ""){
            $this->db->select('*');
            $this->db->from('own_companies');
            $this->db->where('companyname = "'.$companyname.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Own Company already available";
            }
            else{
                $insert['companyname'] = $companyname;
                $insert['ntn'] = $ntn;
                $insert['strn'] = $strn;
                $insert['registeraddress'] = $raddress;
                $insert['warehouseaddress'] = $waddress;
                $insert['srb'] = $srb;
                $insert['registerperson'] = $rperson;
                $insert['mobile'] = $mobile;
                $insert['auto_invoice_gen'] = $autoinvoice;
                $insert['slip_header'] = $slipheder;
                $insert['slip_footer'] = $slipfooter;
                $this->db->insert('own_companies',$insert);
                $senddata['message'] = "Own Company create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_own_company(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $companyname = $this->input->post('companyname');
        $mobile = $this->input->post('mobile');
        $rperson = $this->input->post('rperson');
        $ntn = $this->input->post('ntn');
        $strn = $this->input->post('strn');
        $srb = $this->input->post('srb');
        $raddress = $this->input->post('raddress');
        $waddress = $this->input->post('waddress');
        $slipheder = $this->input->post('slipheder');
        $slipfooter = $this->input->post('slipfooter');
        $autoinvoice = $this->input->post('autoinvoice');
        if($companyname != "" && $mobile != ""){
            $set['companyname'] = $companyname;
            $set['ntn'] = $ntn;
            $set['strn'] = $strn;
            $set['registeraddress'] = $raddress;
            $set['warehouseaddress'] = $waddress;
            $set['srb'] = $srb;
            $set['registerperson'] = $rperson;
            $set['mobile'] = $mobile;
            $set['slip_header'] = $slipheder;
            $set['slip_footer'] = $slipfooter;
            $set['auto_invoice_gen'] = $autoinvoice;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('own_companies');
            $senddata['message'] = "Own company update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_own_company(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('purchases');
                $this->db->where('own_company = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('own_companies', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Own company delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete purchases then delete this company!";
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
    // Own Company Code End
    // Brand Code Start
    function brands(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Brands')));
        $meta = array('page_title' => lang('Brands'), 'bc' => $bc);
        $this->page_construct2('settings/brands', $meta, $this->data);
    }
    public function get_brands(){
        // Count Total Rows
        $this->db->from('brands');
        $totalq = $this->db->get();
        $this->runquery_brands('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_brands();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/products?brand=".$row->id).'" >Products List</a>';
            $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-code="'.$row->code.'" data-name="'.$row->name.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                '<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" >',
                $row->id,
                $row->code,
                $row->name,
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
    public function runquery_brands($onlycoun = "no"){
        $column_order = array(
            null,
            'brands.id',
            'brands.code',
            'brands.name',
            5
        );
        $column_search = array(
            'brands.id',
            'brands.code',
            'brands.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('brands.id as id');
        }
        else{
            $this->db->select('
                brands.image,
                brands.id,
                brands.code,
                brands.name,
                (
                    SELECT COUNT(sma_products.id) FROM sma_products WHERE sma_products.brand = brands.id

                ) as no_products
            ');
        }
        $this->db->from('brands as brands');
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
    public function insert_brand(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        if($name != ""){
            if($code == ""){
                $code = strtolower(str_replace(' ','_',$name));
            }
            $this->db->select('*');
            $this->db->from('brands');
            $this->db->where('code = "'.$code.'" OR name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Brand already available";
            }
            else{
                $insert['code'] = $code;
                $insert['name'] = $name;
                $this->db->insert('brands',$insert);
                $senddata['message'] = "Brand create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_brand(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        if($name != ""){
            if($code == ""){
                $code = strtolower(str_replace(' ','_',$name));
            }
            $set['code'] = $code;
            $set['name'] = $name;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('brands');
            $senddata['message'] = "Brand update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_brand(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('products');
                $this->db->where('brand = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('brands', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Brand delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete purchases then delete this brand!";
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
    // Brand Code End
    // Units Code Start
    function units(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Units')));
        $meta = array('page_title' => lang('Units'), 'bc' => $bc);
        $this->page_construct2('settings/units', $meta, $this->data);
    }
    public function get_units(){
        // Count Total Rows
        $this->db->from('units');
        $totalq = $this->db->get();
        $this->runquery_units('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_units();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-code="'.$row->code.'" data-name="'.$row->name.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->code,
                $row->name,
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
    public function runquery_units($onlycoun = "no"){
        $column_order = array(
            'units.id',
            'units.code',
            'units.name',
            5
        );
        $column_search = array(
            'units.id',
            'units.code',
            'units.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('units.id as id');
        }
        else{
            $this->db->select('
                units.id,
                units.code,
                units.name
            ');
        }
        $this->db->from('units as units');
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
    public function insert_unit(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        if($code != "" && $name != ""){
            $this->db->select('*');
            $this->db->from('units');
            $this->db->where('code = "'.$code.'" OR name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Unit already available";
            }
            else{
                $insert['code'] = $code;
                $insert['name'] = $name;
                $this->db->insert('units',$insert);
                $senddata['message'] = "Unit create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_unit(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        if($code != "" && $name != ""){
            $set['code'] = $code;
            $set['name'] = $name;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('units');
            $senddata['message'] = "Unit update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_unit(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('products');
                $this->db->where('purchase_unit = '.$id.' OR sale_unit = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('units', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Unit delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete product then delete this unit!";
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
    // Units Code End
    // Currencies Code Start
    function currencies(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Currencies')));
        $meta = array('page_title' => lang('Currencies'), 'bc' => $bc);
        $this->page_construct2('settings/currencies', $meta, $this->data);
    }
    public function get_currencies(){
        // Count Total Rows
        $this->db->from('currencies');
        $totalq = $this->db->get();
        $this->runquery_currencies('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_currencies();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-code="'.$row->code.'" data-name="'.$row->name.'" data-symbol="'.$row->symbol.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->code,
                $row->name,
                $row->symbol,
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
    public function runquery_currencies($onlycoun = "no"){
        $column_order = array(
            'currencies.id',
            'currencies.code',
            'currencies.name',
            'currencies.symbol',
        );
        $column_search = array(
            'currencies.id',
            'currencies.code',
            'currencies.name',
            'currencies.symbol'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('currencies.id as id');
        }
        else{
            $this->db->select('
                currencies.id,
                currencies.code,
                currencies.name,
                currencies.symbol
            ');
        }
        $this->db->from('currencies as currencies');
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
    public function insert_currency(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $symbol = $this->input->post('symbol');
        if($code != "" && $name != ""){
            $this->db->select('*');
            $this->db->from('currencies');
            $this->db->where('code = "'.$code.'" OR name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Currency already available";
            }
            else{
                $insert['code'] = $code;
                $insert['name'] = $name;
                $insert['symbol'] = $symbol;
                $this->db->insert('currencies',$insert);
                $senddata['message'] = "Currency create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_currency(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $symbol = $this->input->post('symbol');
        if($code != "" && $name != ""){
            $set['code'] = $code;
            $set['name'] = $name;
            $set['symbol'] = $symbol;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('currencies');
            $senddata['message'] = "Currency update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_currency(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->delete('currencies', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Currency delete successfully!";
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
    // Currencies Code End
    // Tax Rate Code Start
    function tax_rates(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Tax Rates')));
        $meta = array('page_title' => lang('Tax Rates'), 'bc' => $bc);
        $this->page_construct2('settings/tax_rates', $meta, $this->data);
    }
    public function get_tax_rates(){
        // Count Total Rows
        $this->db->from('tax_rates');
        $totalq = $this->db->get();
        $this->runquery_tax_rates('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_tax_rates();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-code="'.$row->code.'" data-name="'.$row->name.'" data-rate="'.$row->rate.'" data-type="'.$row->type.'" >Edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->code,
                $row->name,
                $row->rate,
                $row->type == 1 ? "Percentage" : "Fixed",
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
    public function runquery_tax_rates($onlycoun = "no"){
        $column_order = array(
            'tax_rates.id',
            'tax_rates.code',
            'tax_rates.name',
            'tax_rates.rate',
            'tax_rates.type'
        );
        $column_search = array(
            'tax_rates.id',
            'tax_rates.code',
            'tax_rates.name',
            'tax_rates.rate',
            'tax_rates.type'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('tax_rates.id as id');
        }
        else{
            $this->db->select('
                tax_rates.id,
                tax_rates.code,
                tax_rates.name,
                tax_rates.rate,
                tax_rates.type
            ');
        }
        $this->db->from('tax_rates as tax_rates');
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
    public function insert_tax_rate(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $rate = $this->input->post('rate');
        $type = $this->input->post('type');
        if($name != ""){
            $this->db->select('*');
            $this->db->from('tax_rates');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Tax rate already available";
            }
            else{
                $insert['code'] = $code;
                $insert['name'] = $name;
                $insert['rate'] = $rate;
                $insert['type'] = $type;
                $this->db->insert('tax_rates',$insert);
                $senddata['message'] = "Tax rate create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_tax_rate(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $rate = $this->input->post('rate');
        $type = $this->input->post('type');
        if($code != "" && $name != ""){
            $set['code'] = $code;
            $set['name'] = $name;
            $set['rate'] = $rate;
            $set['type'] = $type;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('tax_rates');
            $senddata['message'] = "Tax rate update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_tax_rate(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->delete('tax_rates', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Tax rate delete successfully!";
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
    // Tax Rate Code End
    // User Groups Code Start
    function user_groups(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Tax Rates')));
        $meta = array('page_title' => lang('Tax Rates'), 'bc' => $bc);
        $this->page_construct2('settings/user_groups', $meta, $this->data);
    }
    public function get_user_groups(){
        // Count Total Rows
        $this->db->from('groups');
        $totalq = $this->db->get();
        $this->runquery_user_groups('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_user_groups();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a href="'.base_url("admin/system_settings/permissions/".$row->id).'" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light md-btn-mini">Permission</a>';
            $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-name="'.$row->name.'" data-description="'.$row->description.'" >Edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->name,
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
    public function runquery_user_groups($onlycoun = "no"){
        $column_order = array(
            'groups.id',
            'groups.name',
            'groups.description'
        );
        $column_search = array(
            'groups.id',
            'groups.name',
            'groups.description'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('groups.id as id');
        }
        else{
            $this->db->select('
                groups.id,
                groups.name,
                groups.description
            ');
        }
        $this->db->from('groups as groups');
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
    public function insert_user_group(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        if($name != "" && $description != ""){
            $this->db->select('*');
            $this->db->from('groups');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "User group already available";
            }
            else{
                $insert['name'] = $name;
                $insert['description'] = $description;
                $this->db->insert('groups',$insert);
                $group_id = $this->db->insert_id();
                $insert2['group_id'] = $group_id;
                $this->db->insert('permissions',$insert2);
                $senddata['message'] = "User group create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_user_group(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        if($description != "" && $name != ""){
            $set['name'] = $name;
            $set['description'] = $description;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('groups');
            $senddata['message'] = "User group update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_user_group(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->delete('groups', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "User group delete successfully!";
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
    function permissions($id = NULL){

        $this->data['id'] = $id;
        $this->data['p'] = $this->settings_model->getGroupPermissions($id);
        $this->data['group'] = $this->settings_model->getGroupByID($id);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('group_permissions')));
        $meta = array('page_title' => lang('group_permissions'), 'bc' => $bc);
        $this->page_construct2('settings/permissions', $meta, $this->data);


    }

    function permissions_submit($id = NULL){
        $sendvalue['status'] = false;
        $data = array(
            'products-index' => $this->input->post('products-index'),
            'products-edit' => $this->input->post('products-edit'),
            'products-add' => $this->input->post('products-add'),
            'products-delete' => $this->input->post('products-delete'),
            'products-cost' => $this->input->post('products-cost'),
            'products-price' => $this->input->post('products-price'),
            'customers-index' => $this->input->post('customers-index'),
            'customers-edit' => $this->input->post('customers-edit'),
            'customers-add' => $this->input->post('customers-add'),
            'customers-delete' => $this->input->post('customers-delete'),
            'suppliers-index' => $this->input->post('suppliers-index'),
            'suppliers-edit' => $this->input->post('suppliers-edit'),
            'suppliers-add' => $this->input->post('suppliers-add'),
            'suppliers-delete' => $this->input->post('suppliers-delete'),
            'purchases-expenses' => $this->input->post('purchases-expenses'),
            'purchases_add_expense' => $this->input->post('purchases_add_expense'),
            'purchases_edit_expense' => $this->input->post('purchases_edit_expense'),
            'purchases_delete_expense' => $this->input->post('purchases_delete_expense'),
            'sales-index' => $this->input->post('sales-index'),
            'sales-edit' => $this->input->post('sales-edit'),
            'sales-add' => $this->input->post('sales-add'),
            'sales-delete' => $this->input->post('sales-delete'),
            'sales-email' => $this->input->post('sales-email'),
            'sales-pdf' => $this->input->post('sales-pdf'),
            'sales-deliveries' => $this->input->post('sales-deliveries'),
            'sales-edit_delivery' => $this->input->post('sales-edit_delivery'),
            'sales-add_delivery' => $this->input->post('sales-add_delivery'),
            'sales-delete_delivery' => $this->input->post('sales-delete_delivery'),
            'sales-email_delivery' => $this->input->post('sales-email_delivery'),
            'sales-pdf_delivery' => $this->input->post('sales-pdf_delivery'),
            'sales-gift_cards' => $this->input->post('sales-gift_cards'),
            'sales-edit_gift_card' => $this->input->post('sales-edit_gift_card'),
            'sales-add_gift_card' => $this->input->post('sales-add_gift_card'),
            'sales-delete_gift_card' => $this->input->post('sales-delete_gift_card'),
            'quotes-index' => $this->input->post('quotes-index'),
            'quotes-edit' => $this->input->post('quotes-edit'),
            'quotes-add' => $this->input->post('quotes-add'),
            'quotes-delete' => $this->input->post('quotes-delete'),
            'quotes-email' => $this->input->post('quotes-email'),
            'quotes-pdf' => $this->input->post('quotes-pdf'),
            'purchases-index' => $this->input->post('purchases-index'),
            'purchases-edit' => $this->input->post('purchases-edit'),
            'purchases-add' => $this->input->post('purchases-add'),
            'purchases-delete' => $this->input->post('purchases-delete'),
            'purchases-email' => $this->input->post('purchases-email'),
            'purchases-pdf' => $this->input->post('purchases-pdf'),
            'transfers-index' => $this->input->post('transfers-index'),
            'transfers-edit' => $this->input->post('transfers-edit'),
            'transfers-add' => $this->input->post('transfers-add'),
            'transfers-delete' => $this->input->post('transfers-delete'),
            'transfers-email' => $this->input->post('transfers-email'),
            'transfers-pdf' => $this->input->post('transfers-pdf'),
            'sales-return_sales' => $this->input->post('sales-return_sales'),
            'reports-index' => $this->input->post('reports-index'),
            'reports-quantity_alerts' => $this->input->post('reports-quantity_alerts'),
            'reports-expiry_alerts' => $this->input->post('reports-expiry_alerts'),
            'reports-products' => $this->input->post('reports-products'),
            'reports-daily_sales' => $this->input->post('reports-daily_sales'),
            'reports-monthly_sales' => $this->input->post('reports-monthly_sales'),
            'reports-payments' => $this->input->post('reports-payments'),
            'reports-sales' => $this->input->post('reports-sales'),
            'reports-purchases' => $this->input->post('reports-purchases'),
            'reports-customers' => $this->input->post('reports-customers'),
            'reports-suppliers' => $this->input->post('reports-suppliers'),
            'report_details' => $this->input->post('report_details'),
            'batchwise_report' => $this->input->post('batchwise_report'),
            'batchwise_price_report' => $this->input->post('batchwise_price_report'),
            'dc_report' => $this->input->post('dc_report'),
            'short_expiry_stock' => $this->input->post('short_expiry_stock'),
            'ces_stock' => $this->input->post('ces_stock'),
            'expiry_stock' => $this->input->post('expiry_stock'),
            'old_stock' => $this->input->post('old_stock'),
            'so_items_wise' => $this->input->post('so_items_wise'),
            'monthly_items_demand' => $this->input->post('monthly_items_demand'),
            'credit_report' => $this->input->post('credit_report'),
            'due_invoice' => $this->input->post('due_invoice'),
            'product_ledger' => $this->input->post('product_ledger'),
            'customers_ledger' => $this->input->post('customers_ledger'),
            'customers_wht_ledger' => $this->input->post('customers_wht_ledger'),
            'legder_summary' => $this->input->post('legder_summary'),
            'supplier_ledger' => $this->input->post('supplier_ledger'),
            'report_sales_summary' => $this->input->post('report_sales_summary'),
            'store_view' => $this->input->post('store_view'),
            'store_add' => $this->input->post('store_add'),
            'store_edit' => $this->input->post('store_edit'),
            'store_delete' => $this->input->post('store_delete'),
            'store_product_integration' => $this->input->post('store_product_integration'),
            'store_product_integration_add' => $this->input->post('store_product_integration_add'),
            'store_product_integration_edit' => $this->input->post('store_product_integration_edit'),
            'store_product_integration_delete' => $this->input->post('store_product_integration_delete'),
            'store_product_integration_delete_both' => $this->input->post('store_product_integration_delete_both'),
            'store_product_integration_recycle' => $this->input->post('store_product_integration_recycle'),
            'store_product_integration_report' => $this->input->post('store_product_integration_report'),
            'po_view' => $this->input->post('po_view'),
            'po_add' => $this->input->post('po_add'),
            'po_edit' => $this->input->post('po_edit'),
            'po_delete' => $this->input->post('po_delete'),
            'po_receiving' => $this->input->post('po_receiving'),
            'po_edit_info' => $this->input->post('po_edit_info'),
            'po_add_new_item' => $this->input->post('po_add_new_item'),
            'po_delete_item' => $this->input->post('po_delete_item'),
            'po_edit_item' => $this->input->post('po_edit_item'),
            'po_close' => $this->input->post('po_close'),
            'po_add_receiving' => $this->input->post('po_add_receiving'),
            'po_edit_receiving' => $this->input->post('po_edit_receiving'),
            'po_delete_receiving' => $this->input->post('po_delete_receiving'),
            'po_deactivate_product' => $this->input->post('po_deactivate_product'),
            'po_create_invoice' => $this->input->post('po_create_invoice'),
            'so_view' => $this->input->post('so_view'),
            'so_add' => $this->input->post('so_add'),
            'so_edit' => $this->input->post('so_edit'),
            'so_delete' => $this->input->post('so_delete'),
            'so_edit_info' => $this->input->post('so_edit_info'),
            'so_cancel' => $this->input->post('so_cancel'),
            'so_add_new_item' => $this->input->post('so_add_new_item'),
            'so_edit_item' => $this->input->post('so_edit_item'),
            'so_delete_item' => $this->input->post('so_delete_item'),
            'so_complete_item' => $this->input->post('so_complete_item'),
            'so_delete_complete_item' => $this->input->post('so_delete_complete_item'),
            'so_create_invoice' => $this->input->post('so_create_invoice'),
            'so_dispatch' => $this->input->post('so_dispatch'),
            'sales-payments' => $this->input->post('sales-payments'),
            'purchases-payments' => $this->input->post('purchases-payments'),
            'purchases-expenses' => $this->input->post('purchases-expenses'),
            'products-adjustments' => $this->input->post('products-adjustments'),
            'bulk_actions' => $this->input->post('bulk_actions'),
            'customers-deposits' => $this->input->post('customers-deposits'),
            'customers-delete_deposit' => $this->input->post('customers-delete_deposit'),
            'products-barcode' => $this->input->post('products-barcode'),
            'purchases-return_purchases' => $this->input->post('purchases-return_purchases'),
            'reports-expenses' => $this->input->post('reports-expenses'),
            'reports-daily_purchases' => $this->input->post('reports-daily_purchases'),
            'reports-monthly_purchases' => $this->input->post('reports-monthly_purchases'),
            'products-stock_count' => $this->input->post('products-stock_count'),
            'edit_price' => $this->input->post('edit_price'),
            'returns-index' => $this->input->post('returns-index'),
            'returns-edit' => $this->input->post('returns-edit'),
            'returns-add' => $this->input->post('returns-add'),
            'returns-delete' => $this->input->post('returns-delete'),
            'returns-email' => $this->input->post('returns-email'),
            'returns-pdf' => $this->input->post('returns-pdf'),
            'reports-tax' => $this->input->post('reports-tax'),
        );
        if ($this->settings_model->updatePermissions($id, $data)) {
            $sendvalue['message'] = lang("group_permissions_updated");
            $sendvalue['status'] = true;
        }
        else {
            $sendvalue['message'] = "Permission update failed";
        }
        echo json_encode($sendvalue);
    }




    // User groups Code End
    // Bulk Discount Code Start
    function bulk_discounts(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Bulk Discounts')));
        $meta = array('page_title' => lang('Bulk Discounts'), 'bc' => $bc);
        $this->page_construct2('settings/bulk_discounts', $meta, $this->data);
    }
    public function get_bulk_discounts(){
        // Count Total Rows
        $this->db->from('bulk_discount');
        $totalq = $this->db->get();
        $this->runquery_bulk_discounts('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_bulk_discounts();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){

            $suppliers = "";
            $brands = "";
            $products = "";

            $button = '<a href="'.base_url("admin/system_settings/edit_bulk_discount/".$row->id).'" class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini">Edit</a>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $status = "<span class='uk-badge uk-badge-success' >Active</span>";
            if($row->end_date < date('Y-m-d')){
                $status = "<span class='uk-badge uk-badge-danger' >Expired</span>";
            }
            $data[] = array(
                $row->id,
                $row->apply_on,
                $row->discount_name,
                $row->discount_code,
                $row->percentage,
                $row->category_name,
                $row->start_date,
                $row->end_date,
                $row->created_by,
                $status,
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
    public function runquery_bulk_discounts($onlycoun = "no"){
        $column_order = array(
            'bulk_discount.id',
            'bulk_discount.apply_on',
            'bulk_discount.discount_name',
            'bulk_discount.discount_code',
            'bulk_discount.percentage',
            'bulk_discount.supplier_name',
            'bulk_discount.brand_name',
            'bulk_discount.product_name',
            'bulk_discount.start_date',
            'bulk_discount.end_date',
            'users.first_name'
        );
        $column_search = array(
            'bulk_discount.id',
            'bulk_discount.discount_name',
            'bulk_discount.discount_code',
            'bulk_discount.percentage',
            'bulk_discount.start_date',
            'bulk_discount.end_date',
            'bulk_discount.apply_on',
            'users.first_name',
            'users.last_name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('bulk_discount.id as id');
        }
        else{
            $this->db->select('
                bulk_discount.*,
                CONCAT(users.first_name," ",users.last_name) as created_by
            ');
        }
        $this->db->from('bulk_discount as bulk_discount');
        $this->db->join('users as users','users.id = bulk_discount.created_by','left');
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
    function add_bulk_discount(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Add Bulk Discounts')));
        $meta = array('page_title' => lang('Add Bulk Discounts'), 'bc' => $bc);
        $this->page_construct2('settings/bulk_discount_add', $meta, $this->data);
    }
    function edit_bulk_discount($id){
        if($id != ""){

            $this->db->from('bulk_discount');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['discount'] = $q->result()[0];

                $this->data['suppliers'] = $this->get_suppliersIds(explode(',',$this->data['discount']->supplier_id));
                $this->data['brands'] = $this->get_brandsIds(explode(',',$this->data['discount']->brand_id));
                $this->data['categories'] = $this->get_categoriesIds(explode(',',$this->data['discount']->category_id));
                $this->data['products'] = $this->get_productsIds(explode(',',$this->data['discount']->product_id));

                $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Edit Bulk Discounts')));
                $meta = array('page_title' => lang('Edit Bulk Discounts'), 'bc' => $bc);
                $this->page_construct2('settings/bulk_discount_edit', $meta, $this->data);
            }
            else{
                redirect(base_url('admin/system_settings/bulk_discounts'));
            }
        }
        else{
            redirect(base_url('admin/system_settings/bulk_discounts'));
        }
    }
    public function insert_bulk_discount(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $apply_on = $this->input->post('apply_on');
        $type = $this->input->post('type');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $start = $this->input->post('start_end');
        $end = $this->input->post('end_date');
        $percentage = $this->input->post('percentage');
        $suppliersIds = $this->input->post('suppliers');
        $brandsIds = $this->input->post('brands');
        $categoriesIds = $this->input->post('categories');
        $productsIds = $this->input->post('products');

        if($name != "" && $code != "" && $start != "" && $end != "" && $percentage != ""){
            if($start <= $end){
                $this->db->select('*');
                $this->db->from('bulk_discount');
                $this->db->where('discount_name = "'.$name.'"');
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $senddata['message'] = "Bulk discount already available";
                }
                else{
                    $insert['discount_name'] = $name;
                    $insert['discount_code'] = $code;
                    $insert['percentage'] = $percentage;
                    $uss = array();
                    $suppliers = $this->get_suppliersIds($suppliersIds);
                    foreach($suppliers as $key => $row){
                        $uss[] = $row->id;
                        if($key == 0){
                            $insert['supplier_id'] = $row->id;
                            $insert['supplier_name'] = $row->name;
                        }
                        else{
                            $insert['supplier_id'] .= ','.$row->id;
                            $insert['supplier_name'] .= ','.$row->name;
                        }
                    }
                    $ubs = array();
                    $brands = $this->get_brandsIds($brandsIds);
                    foreach($brands as $key => $row){
                        $ubs[] = $row->id;
                        if($key == 0){
                            $insert['brand_id'] = $row->id;
                            $insert['brand_name'] = $row->name;
                        }
                        else{
                            $insert['brand_id'] .= ','.$row->id;
                            $insert['brand_name'] .= ','.$row->name;
                        }
                    }
                    $ucs = array();
                    $categories = $this->get_categoriesIds($categoriesIds);
                    foreach($categories as $key => $row){
                        $ucs[] = $row->id;
                        if($key == 0){
                            $insert['category_id'] = $row->id;
                            $insert['category_name'] = $row->name;
                        }
                        else{
                            $insert['category_id'] .= ','.$row->id;
                            $insert['category_name'] .= ','.$row->name;
                        }
                    }
                    $ups = array();
                    $products = $this->get_productsIds($productsIds);
                    foreach($products as $key => $row){
                        $ups[] = $row->id;
                        if($key == 0){
                            $insert['product_id'] = $row->id;
                            $insert['product_name'] = $row->name;
                        }
                        else{
                            $insert['product_id'] .= ','.$row->id;
                            $insert['product_name'] .= ','.$row->name;
                        }
                    }
                    $insert['type'] = $type;
                    $insert['apply_on'] = $apply_on;
                    $insert['start_date'] = $start;
                    $insert['end_date'] = $end;
                    $insert['created_by'] = $this->session->userdata('user_id');
                    $this->db->insert('bulk_discount',$insert);
                    $discount_id = $this->db->insert_id();
                    if($apply_on == "all" || $apply_on == "website"){
                        // Create Request
                        $this->create_request('supplier1',$uss,$discount_id);
                        $this->create_request('brand',$ubs,$discount_id);
                        $this->create_request('category_id',$ucs,$discount_id);
                        $this->create_request('id',$ups,$discount_id);
                    }
                    $senddata['message'] = "Bulk discount create successfully";
                    $senddata['status'] = true;
                }
            }
            else{
                $senddata['message'] = "Please select correct discount durration";
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_bulk_discount(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $apply_on = $this->input->post('apply_on');
        $type = $this->input->post('type');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $start = $this->input->post('start_end');
        $end = $this->input->post('end_date');
        $percentage = $this->input->post('percentage');
        $suppliersIds = $this->input->post('suppliers');
        $brandsIds = $this->input->post('brands');
        $categoriesIds = $this->input->post('categories');
        $productsIds = $this->input->post('products');

        if($name != "" && $code != "" && $start != "" && $end != "" && $percentage != ""){
            if($start <= $end){
                
                $set['discount_name'] = $name;
                $set['discount_code'] = $code;
                $set['percentage'] = $percentage;
                $uss = array();
                $suppliers = $this->get_suppliersIds($suppliersIds);
                foreach($suppliers as $key => $row){
                    $uss[] = $row->id;
                    if($key == 0){
                        $set['supplier_id'] = $row->id;
                        $set['supplier_name'] = $row->name;
                    }
                    else{
                        $set['supplier_id'] .= ','.$row->id;
                        $set['supplier_name'] .= ','.$row->name;
                    }
                }
                $ubs = array();
                $brands = $this->get_brandsIds($brandsIds);
                foreach($brands as $key => $row){
                    $ubs[] = $row->id;
                    if($key == 0){
                        $set['brand_id'] = $row->id;
                        $set['brand_name'] = $row->name;
                    }
                    else{
                        $set['brand_id'] .= ','.$row->id;
                        $set['brand_name'] .= ','.$row->name;
                    }
                }
                $ucs = array();
                $categories = $this->get_categoriesIds($categoriesIds);
                foreach($categories as $key => $row){
                    $ucs[] = $row->id;
                    if($key == 0){
                        $set['category_id'] = $row->id;
                        $set['category_name'] = $row->name;
                    }
                    else{
                        $set['category_id'] .= ','.$row->id;
                        $set['category_name'] .= ','.$row->name;
                    }
                }
                $ups = array();
                $products = $this->get_productsIds($productsIds);
                foreach($products as $key => $row){
                    $ups[] = $row->id;
                    if($key == 0){
                        $set['product_id'] = $row->id;
                        $set['product_name'] = $row->name;
                    }
                    else{
                        $set['product_id'] .= ','.$row->id;
                        $set['product_name'] .= ','.$row->name;
                    }
                }
                $set['start_date'] = $start;
                $set['end_date'] = $end;
                // $set['apply_on'] = $apply_on;
                $set['type'] = $type;
                
                $this->db->set($set);
                $this->db->where('id',$id);
                $this->db->update('bulk_discount');
                if($apply_on == "all" || $apply_on == "website"){
                    // Create Request
                    $this->create_request('supplier1',$uss,$id);
                    $this->create_request('brand',$ubs,$id);
                    $this->create_request('category_id',$ucs,$id);
                    $this->create_request('id',$ups,$id);
                }
                $senddata['message'] = "Bulk discount create successfully";
                $senddata['status'] = true;
            }
            else{
                $senddata['message'] = "Please select correct discount durration";
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_bulk_discount(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $sproducts = $this->db->select('*')->from('sma_store_products_tb')->where('discount',$id)->get()->result();
                $this->db->set('discount','0')
                    ->where('discount',$id)
                    ->update('sma_store_products_tb');
                foreach($sproducts as $product){
                    $warehouse_id = 4;
                    $store_id = 1;
                    $this->db->set('status','canceled')
                        ->where('product_id',$product->product_id)
                        ->where('status','pending')
                        ->where('warehouse_id',$warehouse_id)
                        ->where('store_id',$store_id)
                        ->update('sma_store_requests_tb');
                    $sr_insert['product_id'] = $product->product_id;
                    $sr_insert['warehouse_id'] = $warehouse_id;
                    $sr_insert['store_id'] = $store_id;
                    $sr_insert['type'] = "Qty Update";
                    $sr_insert['status'] = 'pending';
                    $this->db->insert('sma_store_requests_tb',$sr_insert);
                }
                $this->db->delete('bulk_discount', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Bulk discount delete successfully!";
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
    public function create_request($col,$rows,$discount_id){
        if($col != "" && count($rows) > 0){
            $products = $this->db->select('id')->from('sma_products')->where_in($col,$rows)->get()->result();
            foreach($products as $product){
                $warehouse_id = 4;
                $store_id = 1;
                $this->db->set('discount',$discount_id)
                    ->where('product_id',$product->id)
                    ->where('store_id',$store_id)
                    ->update('sma_store_products_tb');
                $this->db->set('status','canceled')
                    ->where('product_id',$product->id)
                    ->where('status','pending')
                    ->where('warehouse_id',$warehouse_id)
                    ->where('store_id',$store_id)
                    ->update('sma_store_requests_tb');
                $sr_insert['product_id'] = $product->id;
                $sr_insert['warehouse_id'] = $warehouse_id;
                $sr_insert['store_id'] = $store_id;
                $sr_insert['type'] = "Qty Update";
                $sr_insert['status'] = 'pending';
                $this->db->insert('sma_store_requests_tb',$sr_insert);
            }
        }
    }
    public function get_suppliersIds($ids){
        if($ids != ""){
            $this->db->select('id,name');
            $this->db->from('companies');
            $this->db->where_in('id',$ids);
            $q = $this->db->get();
            return $q->result();
        }
        else{
            return array();
        }

    }
    public function get_brandsIds($ids){
        if($ids != ""){
            $this->db->select('id,name');
            $this->db->from('brands');
            $this->db->where_in('id',$ids);
            $q = $this->db->get();
            return $q->result();
        }
        else{
            return array();
        }
    }
    public function get_categoriesIds($ids){
        if($ids != ""){
        $this->db->select('id,name');
        $this->db->from('categories');
        $this->db->where_in('id',$ids);
        $q = $this->db->get();
            return $q->result();
        }
        else{
            return array();
        }
    }
    public function get_productsIds($ids){
        if($ids != ""){
            $this->db->select('id,name');
            $this->db->from('products');
            $this->db->where_in('id',$ids);
            $q = $this->db->get();
            return $q->result();
        }
        else{
            return array();
        }
    }
    // Bulk Discount Code End

    // Old Code
    function index()
    {
        $this->load->library('gst');
        $this->form_validation->set_rules('site_name', lang('site_name'), 'trim|required');
        $this->form_validation->set_rules('dateformat', lang('dateformat'), 'trim|required');
        $this->form_validation->set_rules('timezone', lang('timezone'), 'trim|required');
        $this->form_validation->set_rules('mmode', lang('maintenance_mode'), 'trim|required');
        //$this->form_validation->set_rules('logo', lang('logo'), 'trim');
        $this->form_validation->set_rules('iwidth', lang('image_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('iheight', lang('image_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('twidth', lang('thumbnail_width'), 'trim|numeric|required');
        $this->form_validation->set_rules('theight', lang('thumbnail_height'), 'trim|numeric|required');
        $this->form_validation->set_rules('display_all_products', lang('display_all_products'), 'trim|numeric|required');
        $this->form_validation->set_rules('watermark', lang('watermark'), 'trim|required');
        $this->form_validation->set_rules('currency', lang('default_currency'), 'trim|required');
        $this->form_validation->set_rules('email', lang('default_email'), 'trim|required');
        $this->form_validation->set_rules('language', lang('language'), 'trim|required');
        $this->form_validation->set_rules('warehouse', lang('default_warehouse'), 'trim|required');
        $this->form_validation->set_rules('biller', lang('default_biller'), 'trim|required');
        $this->form_validation->set_rules('tax_rate', lang('product_tax'), 'trim|required');
        $this->form_validation->set_rules('tax_rate2', lang('invoice_tax'), 'trim|required');
        $this->form_validation->set_rules('sales_prefix', lang('sales_prefix'), 'trim');
        $this->form_validation->set_rules('quote_prefix', lang('quote_prefix'), 'trim');
        $this->form_validation->set_rules('purchase_prefix', lang('purchase_prefix'), 'trim');
        $this->form_validation->set_rules('transfer_prefix', lang('transfer_prefix'), 'trim');
        $this->form_validation->set_rules('delivery_prefix', lang('delivery_prefix'), 'trim');
        $this->form_validation->set_rules('payment_prefix', lang('payment_prefix'), 'trim');
        $this->form_validation->set_rules('return_prefix', lang('return_prefix'), 'trim');
        $this->form_validation->set_rules('expense_prefix', lang('expense_prefix'), 'trim');
        $this->form_validation->set_rules('detect_barcode', lang('detect_barcode'), 'trim|required');
        $this->form_validation->set_rules('theme', lang('theme'), 'trim|required');
        $this->form_validation->set_rules('rows_per_page', lang('rows_per_page'), 'trim|required');
        $this->form_validation->set_rules('accounting_method', lang('accounting_method'), 'trim|required');
        $this->form_validation->set_rules('product_serial', lang('product_serial'), 'trim|required');
        $this->form_validation->set_rules('product_discount', lang('product_discount'), 'trim|required');
        $this->form_validation->set_rules('bc_fix', lang('bc_fix'), 'trim|numeric|required');
        $this->form_validation->set_rules('protocol', lang('email_protocol'), 'trim|required');
        $this->form_validation->set_rules('gst_tax', lang('gst_tax'), 'trim|required');
        $this->form_validation->set_rules('further_tax', lang('further_tax'), 'trim|required');
        // $this->form_validation->set_rules('fed_tax', lang('fed_tax'), 'trim|required');

        if ($this->input->post('protocol') == 'smtp') {
            $this->form_validation->set_rules('smtp_host', lang('smtp_host'), 'required');
            $this->form_validation->set_rules('smtp_user', lang('smtp_user'), 'required');
            $this->form_validation->set_rules('smtp_pass', lang('smtp_pass'), 'required');
            $this->form_validation->set_rules('smtp_port', lang('smtp_port'), 'required');
        }
        if ($this->input->post('protocol') == 'sendmail') {
            $this->form_validation->set_rules('mailpath', lang('mailpath'), 'required');
        }
        $this->form_validation->set_rules('decimals', lang('decimals'), 'trim|required');
        $this->form_validation->set_rules('decimals_sep', lang('decimals_sep'), 'trim|required');
        $this->form_validation->set_rules('thousands_sep', lang('thousands_sep'), 'trim|required');
        if ($this->Settings->indian_gst) {
            $this->form_validation->set_rules('state', lang('state'), 'trim|required');
        }

        if ($this->form_validation->run() == true) {

            $language = $this->input->post('language');

            if ((file_exists(APPPATH.'language'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'sma_lang.php') && is_dir(APPPATH.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$language)) || $language == 'english') {
                $lang = $language;
            } else {
                $this->session->set_flashdata('error', lang('language_x_found'));
                admin_redirect("system_settings");
                $lang = 'english';
            }

            $tax1 = ($this->input->post('tax_rate') != 0) ? 1 : 0;
            $tax2 = ($this->input->post('tax_rate2') != 0) ? 1 : 0;

            $data = array('site_name' => DEMO ? 'Stock Manager Advance' : $this->input->post('site_name'),
                'rows_per_page' => $this->input->post('rows_per_page'),
                'dateformat' => $this->input->post('dateformat'),
                'timezone' => DEMO ? 'Asia/Kuala_Lumpur' : $this->input->post('timezone'),
                'mmode' => trim($this->input->post('mmode')),
                'iwidth' => $this->input->post('iwidth'),
                'iheight' => $this->input->post('iheight'),
                'twidth' => $this->input->post('twidth'),
                'theight' => $this->input->post('theight'),
                'watermark' => $this->input->post('watermark'),
                // 'reg_ver' => $this->input->post('reg_ver'),
                // 'allow_reg' => $this->input->post('allow_reg'),
                // 'reg_notification' => $this->input->post('reg_notification'),
                'accounting_method' => $this->input->post('accounting_method'),
                'default_email' => DEMO ? 'noreply@sma.tecdiary.my' : $this->input->post('email'),
                'language' => $lang,
                'default_warehouse' => $this->input->post('warehouse'),
                'default_tax_rate' => $this->input->post('tax_rate'),
                'default_tax_rate2' => $this->input->post('tax_rate2'),
                'sales_prefix' => $this->input->post('sales_prefix'),
                'quote_prefix' => $this->input->post('quote_prefix'),
                'purchase_prefix' => $this->input->post('purchase_prefix'),
                'transfer_prefix' => $this->input->post('transfer_prefix'),
                'delivery_prefix' => $this->input->post('delivery_prefix'),
                'payment_prefix' => $this->input->post('payment_prefix'),
                'ppayment_prefix' => $this->input->post('ppayment_prefix'),
                'qa_prefix' => $this->input->post('qa_prefix'),
                'return_prefix' => $this->input->post('return_prefix'),
                'returnp_prefix' => $this->input->post('returnp_prefix'),
                'expense_prefix' => $this->input->post('expense_prefix'),
                'auto_detect_barcode' => trim($this->input->post('detect_barcode')),
                'theme' => trim($this->input->post('theme')),
                'product_serial' => $this->input->post('product_serial'),
                'customer_group' => $this->input->post('customer_group'),
                'product_expiry' => $this->input->post('product_expiry'),
                'product_discount' => $this->input->post('product_discount'),
                'default_currency' => $this->input->post('currency'),
                'bc_fix' => $this->input->post('bc_fix'),
                'tax1' => $tax1,
                'tax2' => $tax2,
                'overselling' => $this->input->post('restrict_sale'),
                'reference_format' => $this->input->post('reference_format'),
                'racks' => $this->input->post('racks'),
                'attributes' => $this->input->post('attributes'),
                'restrict_calendar' => $this->input->post('restrict_calendar'),
                'captcha' => $this->input->post('captcha'),
                'item_addition' => $this->input->post('item_addition'),
                'protocol' => DEMO ? 'mail' : $this->input->post('protocol'),
                'mailpath' => $this->input->post('mailpath'),
                'smtp_host' => $this->input->post('smtp_host'),
                'smtp_user' => $this->input->post('smtp_user'),
                'smtp_port' => $this->input->post('smtp_port'),
                'smtp_crypto' => $this->input->post('smtp_crypto') ? $this->input->post('smtp_crypto') : NULL,
                'decimals' => $this->input->post('decimals'),
                'decimals_sep' => $this->input->post('decimals_sep'),
                'thousands_sep' => $this->input->post('thousands_sep'),
                'default_biller' => $this->input->post('biller'),
                'invoice_view' => $this->input->post('invoice_view'),
                'rtl' => $this->input->post('rtl'),
                'each_spent' => $this->input->post('each_spent') ? $this->input->post('each_spent') : NULL,
                'ca_point' => $this->input->post('ca_point') ? $this->input->post('ca_point') : NULL,
                'each_sale' => $this->input->post('each_sale') ? $this->input->post('each_sale') : NULL,
                'sa_point' => $this->input->post('sa_point') ? $this->input->post('sa_point') : NULL,
                'sac' => $this->input->post('sac'),
                'qty_decimals' => $this->input->post('qty_decimals'),
                'display_all_products' => $this->input->post('display_all_products'),
                'display_symbol' => $this->input->post('display_symbol'),
                'symbol' => $this->input->post('symbol'),
                'remove_expired' => $this->input->post('remove_expired'),
                'barcode_separator' => $this->input->post('barcode_separator'),
                'set_focus' => $this->input->post('set_focus'),
                'disable_editing' => $this->input->post('disable_editing'),
                'price_group' => $this->input->post('price_group'),
                'barcode_img' => $this->input->post('barcode_renderer'),
                'update_cost' => $this->input->post('update_cost'),
                'apis' => $this->input->post('apis'),
                'pdf_lib' => $this->input->post('pdf_lib'),
                'gst_tax' => $this->input->post('gst_tax'),
                'further_tax' => $this->input->post('further_tax'),
                // 'fed_tax' => $this->input->post('fed_tax'),
                'state' => $this->input->post('state'),
            );
            if ($this->input->post('smtp_pass')) {
                $data['smtp_pass'] = $this->input->post('smtp_pass');
            }
        }

        if ($this->form_validation->run() == true && $this->settings_model->updateSetting($data)) {
            if ( ! DEMO && TIMEZONE != $data['timezone']) {
                if ( ! $this->write_index($data['timezone'])) {
                    $this->session->set_flashdata('error', lang('setting_updated_timezone_failed'));
                    admin_redirect('system_settings');
                }
            }

            $this->session->set_flashdata('message', lang('setting_updated'));
            admin_redirect("system_settings");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['settings'] = $this->settings_model->getSettings();
            $this->data['currencies'] = $this->settings_model->getAllCurrencies();
            $this->data['date_formats'] = $this->settings_model->getDateFormats();
            $this->data['tax_rates'] = $this->settings_model->getAllTaxRates();
            $this->data['customer_groups'] = $this->settings_model->getAllCustomerGroups();
            $this->data['price_groups'] = $this->settings_model->getAllPriceGroups();
            $this->data['warehouses'] = $this->settings_model->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('system_settings')));
            $meta = array('page_title' => lang('system_settings'), 'bc' => $bc);
            $this->page_construct('settings/index', $meta, $this->data);
        }
    }
    public function wallets(){
        $this->db->select('
            sma_wallets.*,
            sma_warehouses.name as wname
        ');
        $this->db->from('sma_wallets');
        $this->db->join('sma_wallet_users','sma_wallet_users.wallet_id = sma_wallets.id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_wallets.location_id','left');
        if (!$this->Owner) {
            $uid = $this->session->userdata('user_id');
            $this->db->where('sma_wallet_users.user_id = '.$uid);
        }
        $this->db->group_by('sma_wallets.id');
        $q = $this->db->get();
        $this->data['wallets'] = $q->result();
        // echo '<pre>';
        // print_r($this->data['wallets']);
        // exit();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('Wallets')));
        $meta = array('page_title' => lang('Wallets'), 'bc' => $bc);
        $this->page_construct('settings/wallets', $meta, $this->data);
    }
    public function add_wallet(){
        $this->data['warehouses'] = $this->settings_model->getAllWarehouses();
        $this->data['userslist'] = $this->settings_model->getAllUsers();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('Add New Wallets')));
        $meta = array('page_title' => lang('Add New Wallets'), 'bc' => $bc);
        $this->page_construct('settings/wallets_add', $meta, $this->data);
    }
    public function insert_wallet(){
        $insert['title'] = $this->input->post('title');
        $insert['location_id'] = $this->input->post('localtion');
        $insert['first_user'] = $this->input->post('fname');
        $insert['second_user'] = $this->input->post('sname');
        $this->db->insert('sma_wallets',$insert);
        redirect(base_url('admin/system_settings/wallets'));
    }
    public function wallets_add_transation(){
        $this->data['wid'] = $this->input->get('wid');
        if($this->data['wid'] != ""){
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('Deposit Wallet')));
            $meta = array('page_title' => lang('Deposit Wallet'), 'bc' => $bc);
            $this->page_construct('settings/wallets_add_transation', $meta, $this->data);
        }
        else{
            redirect(base_url('admin/system_settings/wallets'));            
        }
    }
    public function wallet_depoit(){
        $insert['user_id'] = $this->session->userdata('user_id');
        $insert['wallet_id'] = $this->input->post('wid');
        $insert['amount'] = $this->input->post('amount');
        $insert['type'] = '0';
        $this->db->insert('sma_wallet_transations',$insert);
        $this->db->set('amount', 'amount+'.$insert['amount'], FALSE);
        $this->db->where('id', $insert['wallet_id']);
        $this->db->update('sma_wallets');
        redirect(base_url('admin/system_settings/wallets'));
    }
    public function wallet_deactive(){
        $wid = $this->input->get('wid');
        if($wid != ""){
            $this->db->set('status', 'deactive');
            $this->db->where('id', $wid);
            $this->db->update('sma_wallets');
        }
        redirect(base_url('admin/system_settings/wallets'));
    }
    public function wallet_active(){
        $wid = $this->input->get('wid');
        if($wid != ""){
            $this->db->set('status', 'active');
            $this->db->where('id', $wid);
            $this->db->update('sma_wallets');
        }
        redirect(base_url('admin/system_settings/wallets'));
    }
    public function wallet_transations(){
        $this->data['wid'] = $this->input->get('wid');
        if($this->data['wid'] != ""){
            $this->db->select('
                sma_wallet_transations.*,
                sma_users.first_name as fname,
                sma_users.last_name as lname
            ');
            $this->db->from('sma_wallet_transations');
            $this->db->join('sma_users','sma_users.id = sma_wallet_transations.user_id','left');
            $this->db->where('sma_wallet_transations.wallet_id',$this->data['wid']);
            $q = $this->db->get();
            $this->data['transactions'] = $q->result();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('Wallet Transactions')));
            $meta = array('page_title' => lang('Wallet Transactions'), 'bc' => $bc);
            $this->page_construct('settings/wallets_transations', $meta, $this->data);
        }
        else{
            redirect(base_url('admin/system_settings/wallets'));            
        }
    }
}
