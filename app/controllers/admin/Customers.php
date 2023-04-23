<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->admin_load('customers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('companies_model');
        $this->load->admin_model('general_model');
    }

    // New Code
    public function index(){

        $this->data['route_id'] = $this->input->get('route_id');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customers')));
        $meta = array('page_title' => lang('customers'), 'bc' => $bc);
        $this->page_construct2('customers/index', $meta, $this->data);
    }
    public function get_customers(){
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
            $button = '<a href="'.base_url("admin/customers/credits/".$row->id).'" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light md-btn-mini">Credit Limit</a>';
            $button .= '<a href="'.base_url("admin/customers/addresses/".$row->id).'" class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini">Delivery Address</a><br><br>';
            $button .= '<a href="'.base_url("admin/customers/stockinfo/".$row->id).'" class="md-btn md-btn-information md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" >Stock Info</a>';
            $button .= '<a href="'.base_url("admin/customers/edit/".$row->id).'" class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini">Edit</a>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->sales_type,
                $row->name,
                $row->company,
                $row->email,
                $row->phone,
                $row->cnic,
                $row->postal_code,
                $row->city,
                $row->state,
                $row->country,
                $row->cf1,
                $row->gst_no,
                $row->vat_no,
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
            'companies.sales_type',
            'companies.name',
            'companies.company',
            'companies.email',
            'companies.phone',
            'companies.cnic',
            'companies.postal_code',
            'companies.city',
            'companies.state',
            'companies.country',
            'companies.cf1',
            'companies.gst_no',
            'companies.vat_no',
            'companies.linces',
            'companies.address'
        );
        $column_search = array(
            'companies.id',
            'companies.sales_type',
            'companies.name',
            'companies.company',
            'companies.email',
            'companies.phone',
            'companies.cnic',
            'companies.postal_code',
            'companies.city',
            'companies.state',
            'companies.country',
            'companies.cf1',
            'companies.gst_no',
            'companies.vat_no',
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
                companies.sales_type,
                companies.name,
                companies.company,
                companies.email,
                companies.phone,
                companies.cnic,
                companies.postal_code,
                companies.city,
                companies.state,
                companies.country,
                companies.cf1,
                companies.gst_no,
                companies.vat_no,
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
        $this->db->where('companies.group_id',3);
        if($_POST['route_id'] != ""){
            $this->db->where('companies.route_id',$_POST['route_id']);
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
                $this->db->select('id');
                $this->db->from('sales');
                $this->db->where('customer_id',$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('companies', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Customer delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete Sales or remove this customer form that sales then delete this customer!";
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customers')));
        $meta = array('page_title' => lang('Add Customer'), 'bc' => $bc);
        $this->page_construct2('customers/add', $meta, $this->data);
    }
    public function edit($id){

        $this->db->select('*');
        $this->db->from('companies');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0 ){
            $this->data['customer'] = $q->result()[0];
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('customers')));
            $meta = array('page_title' => lang('Edit Customer'), 'bc' => $bc);
            $this->page_construct2('customers/edit', $meta, $this->data);
        }
        else{
            redirect(base_url('admin/customers'));
        }
    }
    public function insert(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $formtype = $this->input->post('formtype');
        $sales_type = $this->input->post('selling');
        $name = $this->input->post('name');
        $company = $this->input->post('company');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $postal = $this->input->post('postal');
        $cnic = $this->input->post('cnic');
        $vat = $this->input->post('vat');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $ntn = $this->input->post('ntn');
        $gst_no = $this->input->post('gst');
        $linces = $this->input->post('linces');
        if(($name != "" && $formtype == 'pos') || ($sales_type != "" && $name != "" && $company != "")){
            $this->db->select('*');
            $this->db->from('companies');
            $this->db->where('name',$name);
            $this->db->where('phone',$phone);
            $this->db->where('group_id',3);
            $q = $this->db->get();
            if($q->num_rows() == 0){
                $data = array(
                    'sales_type' => $sales_type,
                    'name' => $name,
                    'company' => $company,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => $address,
                    'postal_code' => $postal,
                    'cnic' => $cnic,
                    'vat_no' => $vat,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'cf1' => $ntn,
                    'gst_no' => $gst_no,
                    'linces' => $linces,
                    'group_id' => '3',
                    'group_name' => 'customer'
                );
                $this->db->insert('companies',$data);
                $senddata['status'] = true;
                $senddata['message'] = "Customer add successfully!";
            }
            else{
                $senddata['message'] = "Customer already available";
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
        $sales_type = $this->input->post('selling');
        $name = $this->input->post('name');
        $company = $this->input->post('company');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $postal = $this->input->post('postal');
        $cnic = $this->input->post('cnic');
        $vat = $this->input->post('vat');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $ntn = $this->input->post('ntn');
        $gst_no = $this->input->post('gst');
        $linces = $this->input->post('linces');
        if($sales_type != "" && $name != "" && $company != ""){
            $data = array(
                'sales_type' => $sales_type,
                'name' => $name,
                'company' => $company,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'postal_code' => $postal,
                'cnic' => $cnic,
                'vat_no' => $vat,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'cf1' => $ntn,
                'gst_no' => $gst_no,
                'linces' => $linces,
            );
            $this->db->set($data);
            $this->db->where('id',$id);
            $this->db->update('companies');
            $senddata['status'] = true;
            $senddata['message'] = "Customer add successfully!";
        }
        else{
            $senddata['message'] = "Filled Required Fields";
        }
        echo json_encode($senddata);
    }
    public function stockinfo($id){
        $this->data['customer_id'] = $id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Customers Stock Info')));
        $meta = array('page_title' => lang('Customers Stock Info'), 'bc' => $bc);
        $this->page_construct2('customers/stockinfo', $meta, $this->data);
    }
    public function get_stockinfo(){
        // Count Total Rows
        $this->db->from('stock_info_tb');
        $totalq = $this->db->get();
        $this->runquery_stockinfo('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_stockinfo();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();
        
        $data = array();
        foreach($rows as $row){
            $button = '<a href="'.base_url("admin/customers/stockinfodetail/".$row->id).'" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light md-btn-mini">Detail</a>';
            if($row->products > 0){
                
                $data[] = array(
                    $row->id,
                    $row->created_at,
                    $row->products,
                    $row->qty,
                    $row->created_by,
                    $button
                );
            }
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
    public function runquery_stockinfo($onlycoun = "no"){
        $column_search = array(
            's.id',
            's.created_at',
            'u.first_name',
            'u.last_name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('s.id as id');
        }
        else{
            $this->db->select('
                s.id,
                s.created_at,
                (
                    SELECT COUNT(si.product_id) FROM sma_stock_items_tb as si WHERE si.stock_info_id = s.id
                ) as products,
                (
                    SELECT SUM(si.quantity) FROM sma_stock_items_tb as si WHERE si.stock_info_id = s.id
                ) as qty,
                CONCAT(u.first_name," ",u.last_name) as created_by
            ');
        }
        $this->db->from('sma_stock_info_tb as s');
        // $this->db->join('sma_stock_items_tb as si','si.stock_info_id = s.id','left');
        $this->db->join('sma_users as u','u.id = s.created_by','left');
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
        if(isset($_POST['customer_id'])){
            $this->db->where('s.customer_id',$_POST['customer_id']);
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }
    public function stockinfodetail($id){
        $this->data['id'] = $id;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Customers Stock Info')));
        $meta = array('page_title' => lang('Customers Stock Info'), 'bc' => $bc);
        $this->page_construct2('customers/stockinfodetail', $meta, $this->data);
    }
    public function get_stockinfodetail(){
        // Count Total Rows
        $this->db->from('stock_info_tb');
        $totalq = $this->db->get();
        $this->runquery_stockinfodetail('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_stockinfodetail();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();
        
        $data = array();
        foreach($rows as $row){
                
            $data[] = array(
                $row->id,
                $row->product_id,
                $row->product_name,
                $row->quantity,
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
    public function runquery_stockinfodetail($onlycoun = "no"){
        $column_search = array(
            'si.id',
            'si.product_id',
            'p.name',
            'si.quantity'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('si.id as id');
        }
        else{
            $this->db->select('
                si.*,
                p.name as product_name
            ');
        }
        $this->db->from('sma_stock_items_tb as si');
        $this->db->join('sma_products as p','p.id = si.product_id','left');
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
        if(isset($_POST['id'])){
            $this->db->where('si.stock_info_id',$_POST['id']);
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }
    // credits Code Start
    function credits($id){
        $this->data['id'] = $id;
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Credits')));
        $meta = array('page_title' => lang('Credits'), 'bc' => $bc);
        $this->page_construct2('customers/credits', $meta, $this->data);
    }
    public function get_credits(){
        // Count Total Rows
        $this->db->from('customer_limits');
        $totalq = $this->db->get();
        $this->runquery_credits('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_credits();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-customer_id="'.$row->customer_id.'" data-supplier_id="'.$row->supplier_id.'" data-creadit_limit="'.$row->creadit_limit.'" data-durration="'.$row->durration.'" data-crossdock_discount="'.$row->crossdock_discount.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->supplier,
                $row->creadit_limit,
                $row->durration,
                $row->crossdock_discount,
                $row->create_at,
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
    public function runquery_credits($onlycoun = "no"){
        $column_search = array(
            'companies.name',
            'credits.creadit_limit',
            'credits.durration',
            'credits.crossdock_discount',
            'credits.create_at',
            'u.first_name',
            'u.last_name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('credits.id as id');
        }
        else{
            $this->db->select('
                credits.id,
                companies.name as supplier,
                credits.supplier_id,
                credits.customer_id,
                credits.creadit_limit,
                credits.durration,
                credits.crossdock_discount,
                credits.create_at,
                CONCAT(u.first_name," ",u.last_name) as created_by
            ');
        }
        $this->db->from('customer_limits as credits');
        $this->db->join('companies as companies', 'companies.id = credits.supplier_id', 'left');
        $this->db->join('users as u', 'u.id = credits.create_by', 'left');
        if(isset($_POST['customer_id'])){
            $this->db->where('credits.customer_id',$_POST['customer_id']);
        }
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
    public function insert_credit(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $customer = $this->input->post('customer');
        $supplier = $this->input->post('supplier');
        $limit = $this->input->post('limit');
        $paymentterms = $this->input->post('paymentterms');
        $mrpdiscount = $this->input->post('mrpdiscount');
        if($customer != "" && $supplier != "" && $limit != "" && $paymentterms != "" && $mrpdiscount != ""){

            $insertdata['customer_id'] = $customer;
            $insertdata['supplier_id'] = $supplier;
            $insertdata['creadit_limit'] = $limit;
            $insertdata['durration'] = $paymentterms;
            $insertdata['crossdock_discount'] = $mrpdiscount;
            $insertdata['create_by'] = $this->session->userdata('user_id');
            $insertdata['status'] = 1;
            $this->db->insert('customer_limits',$insertdata);
            $senddata['message'] = "Credit create successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter Required Value!";
        }
        echo json_encode($senddata);
    }
    public function update_credit(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $supplier = $this->input->post('supplier');
        $limit = $this->input->post('limit');
        $paymentterms = $this->input->post('paymentterms');
        $mrpdiscount = $this->input->post('mrpdiscount');
        if($id != "" && $supplier != "" && $limit != "" && $paymentterms != "" && $mrpdiscount != ""){

            $insertdata['supplier_id'] = $supplier;
            $insertdata['creadit_limit'] = $limit;
            $insertdata['durration'] = $paymentterms;
            $insertdata['crossdock_discount'] = $mrpdiscount;
            $this->db->set($insertdata);
            $this->db->where('id',$id);
            $this->db->update('customer_limits');
            $senddata['message'] = "Credit update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter Required Value!";
        }
        echo json_encode($senddata);
    }
    public function delete_credit(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->delete('customer_limits', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Credit delete successfully!";
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
    // Credit Code End
    // Address Code Start
    function addresses($id){
        $this->data['id'] = $id;
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Addresses')));
        $meta = array('page_title' => lang('Addresses'), 'bc' => $bc);
        $this->page_construct2('customers/addresses', $meta, $this->data);
    }
    public function get_addresses(){
        // Count Total Rows
        $this->db->from('addresses');
        $totalq = $this->db->get();
        $this->runquery_addresses('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_addresses();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-line1="'.$row->line1.'" data-line2="'.$row->line2.'" data-city="'.$row->city.'" data-postal_code="'.$row->postal_code.'" data-state="'.$row->state.'" data-country="'.$row->country.'" data-phone="'.$row->phone.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->line1,
                $row->line2,
                $row->city,
                $row->postal_code,
                $row->state,
                $row->country,
                $row->phone,
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
    public function runquery_addresses($onlycoun = "no"){
        $column_search = array(
            'companies.id',
            'companies.line1',
            'companies.line2',
            'companies.city',
            'companies.postal_code',
            'companies.state',
            'companies.country',
            'companies.phone'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('addresses.id as id');
        }
        else{
            $this->db->select('*');
        }
        $this->db->from('addresses as addresses');
        if(isset($_POST['company_id '])){
            $this->db->where('addresses.company_id ',$_POST['company_id ']);
        }
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
    public function insert_address(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $company_id  = $this->input->post('company_id');
        $line1 = $this->input->post('line1');
        $line2 = $this->input->post('line2');
        $city = $this->input->post('city');
        $postal_code = $this->input->post('postal_code');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $phone = $this->input->post('phone');
        if($company_id != "" && $line1 != "" && $line2 != "" && $city != "" && $postal_code != "" && $state != "" && $country != "" && $phone != ""){

            $insertdata['company_id'] = $company_id;
            $insertdata['line1'] = $line1;
            $insertdata['line2'] = $line2;
            $insertdata['city'] = $city;
            $insertdata['postal_code'] = $postal_code;
            $insertdata['state'] = $state;
            $insertdata['country'] = $country;
            $insertdata['phone'] = $phone;
            $this->db->insert('addresses',$insertdata);
            $senddata['message'] = "Address create successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter Required Value!";
        }
        echo json_encode($senddata);
    }
    public function update_address(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id  = $this->input->post('id');
        $line1 = $this->input->post('line1');
        $line2 = $this->input->post('line2');
        $city = $this->input->post('city');
        $postal_code = $this->input->post('postal_code');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $phone = $this->input->post('phone');


        if($id != "" && $line1 != "" && $line2 != "" && $city != "" && $postal_code != "" && $state != "" && $country != "" && $phone != ""){

            $insertdata['line1'] = $line1;
            $insertdata['line2'] = $line2;
            $insertdata['city'] = $city;
            $insertdata['postal_code'] = $postal_code;
            $insertdata['state'] = $state;
            $insertdata['country'] = $country;
            $insertdata['phone'] = $phone;
            $this->db->set($insertdata);
            $this->db->where('id',$id);
            $this->db->update('addresses');
            $senddata['message'] = "Address update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter Required Value!";
        }
        echo json_encode($senddata);
    }
    public function delete_address(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->delete('addresses', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Address delete successfully!";
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
    // Product Address Code End



}
