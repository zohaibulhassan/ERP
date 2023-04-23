<?php defined('BASEPATH') or exit('No direct script access allowed');



class Productions extends MY_Controller
{
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
        $this->lang->admin_load('purchases', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('general_model');
        $this->load->admin_model('productions_model');
    }
    public function index($warehouse_id = null){
        $this->data['warehouses'] = $this->general_model->GetAllWarehouses();
        $this->data['warehouse'] = $this->input->get('warehouse');        
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Productions'));
        $meta = array('page_title' => 'Productions', 'bc' => $bc);
        $this->page_construct2('productions/index', $meta, $this->data);


    }
    public function get_lists(){
        // Count Total Rows
        $this->db->from('productions');
        $totalq = $this->db->get();
        $this->runquery_productions('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_productions();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();
        // print_r($rows);

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/productions/view/".$row->id).'" >View</a>';
            // $button .= '<a class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/bill_of_materials/edit/".$row->id).'" >Edit</a>';
            $button .= '<a class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</a>';
            $data[] = array(
                $row->id,
                $row->created_at,
                $row->product_id,
                $row->product_name,
                $row->material_cost,
                $row->labour_cost,
                $row->factory_cost,
                $row->total_cost,
                $button
            );
        }
        // $output = array(
        //     "draw" => $_POST['draw'],
        //     "recordsTotal" => 0,
        //     "recordsFiltered" => 0,
        //     "data" => $data,
        // );
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $totalq->num_rows(),
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );
        // Output to JSON format
        echo json_encode($output);
    }
    public function runquery_productions($onlycoun = "no"){
        $column_search = array(
            'productions.id',
            'products.name as product_name',
            'productions.product_id',
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('productions.id as id');
        }
        else{
            $this->db->select('
                productions.*,
                products.name as product_name
            ');
        }
        $this->db->from('productions');
        $this->db->join('products','products.id = productions.product_id','left');
        // $this->db->join('users as u', 'u.id = productions.created_by', 'left');
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
    public function view($id = null){
        if($id != ""){
            $this->db->select('
                productions.*,
                warehouses.name as warehosue_name,
                products.name as product_name,
                manufacturers.name as m_name,
                manufacturers.email as m_email,
                manufacturers.phone as m_phone,
                manufacturers.address as m_address
            ');
            $this->db->from('productions');
            $this->db->join('products','products.id = productions.product_id','left');
            $this->db->join('manufacturers','manufacturers.id = productions.manufacturer_id','left');
            $this->db->join('warehouses','warehouses.id = productions.warehouse_id','left');
            $this->db->where('productions.id',$id);
            $q  = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['production'] = $q->result()[0];
                $this->db->select('
                    production_items.*,
                    p.name as product_name,
                    p.code as product_code
                ');
                $this->db->from('production_items');
                $this->db->join('products as p', 'p.id = production_items.material_id', 'left');
                $this->db->where('production_items.production_id',$id);
                $qi  = $this->db->get();
                $this->data['items'] = $qi->result();
                $this->data['payments'] = $this->db->select('*')->from('payments')->where('production_id',$id)->get()->result();
                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('productions'), 'page' => 'Productions'), array('link' => '#', 'page' => lang('view')));
                $meta = array('page_title' => 'Prodduction Detail', 'bc' => $bc);
                $this->page_construct2('productions/view', $meta, $this->data);
            }
            else{
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else{
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function get_items(){
        // Count Total Rows
        $this->db->from('purchase_items');
        $totalq = $this->db->get();
        $this->runquery_items('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_items();
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        $sno = 0;
        foreach($rows as $row){
            $sno++;
            $button = "";
            if($row->quantity == $row->quantity_balance){
                $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini itemedit" type="button" data-id="'.$row->id.'" data-product="'.$row->product_id.'" data-qty="'.$row->quantity.'" data-panem="'.$row->product_name.'" >Edit</button>';
                $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini itemdelete" type="button" data-id="'.$row->id.'" >Delete</button>';
            }
            $data[] = array(
                $sno,
                $row->product_id,
                $row->product_name,
                $row->barcode,
                $row->quantity,
                $row->net_unit_cost,
                // $row->expiry,
                // $row->batch,
                $row->adv_tax,
                $row->subtotal,
                $button
            );
        }
        $output = array(
            "data" => $data,
        );
        // Output to JSON format
        echo json_encode($output);
    }
    public function runquery_items($onlycoun = "no"){
        $id = $this->input->post('id');
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('p_items.id as id');
        }
        else{
            $this->db->select('
                bom_items.*,
                products.id as product_id,
                products.name as product_name,
                products.code as barcode,
            ');
        }
        $this->db->from('bom_items as b_items');
        $this->db->join('products as products', 'products.id = b_items.material_id', 'left');
        $this->db->where('b_items.bom_id',$id);
    }
    public function importmaterial(){
        $sendvalue['status'] = false;
        $sendvalue['message'] = '';
        $sendvalue['material_cost'] = 0;
        $sendvalue['labour_cost'] = 0;
        $sendvalue['factory_cost'] = 0;
        $sendvalue['total_cost'] = 0;
        $sendvalue['items'] = array();
        $fg_product = $this->input->get('fg_product');
        $fg_quantity = $this->input->get('fg_quantity');
        $fg_warehouse = $this->input->get('fg_warehouse');
        $this->db->select('*');
        $this->db->from('boms');
        $this->db->where('product_id',$fg_product);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $bom = $q->result()[0];
            $this->db->select('bom_items.*,products.name as product_name');
            $this->db->from('bom_items');
            $this->db->join('products','products.id = bom_items.material_id','left');
            $this->db->where('bom_items.bom_id',$bom->id);
            $materials = $this->db->get()->result();
            if(count($materials) > 0){
                foreach($materials as $material){
                    $temp['product_id'] = $material->material_id;
                    $temp['product_name'] = $material->product_name;
                    $temp['bom_item_id'] = $material->id;
                    $temp['quanity'] = $material->quantity;
                    $temp['rate'] = $material->rate;
                    $temp['material_qty'] = $material->quantity*$fg_quantity;
                    $temp['total'] = $material->total*$fg_quantity;
                    $sendvalue['material_cost'] += $temp['total'];
                    $sendvalue['items'][] = $temp;

                }
                $sendvalue['labour_cost'] = $bom->estimated_labour_cost*$fg_quantity;
                $sendvalue['factory_cost'] = $bom->estimiated_factory_cost*$fg_quantity;
                $sendvalue['total_cost'] = $sendvalue['material_cost']+$sendvalue['labour_cost']+$sendvalue['factory_cost'];
                $sendvalue['status'] = true;

            }
            else{
                $sendvalue['message'] = 'Material not found';
            }
        }
        else{
            $sendvalue['message'] = 'Product not found';
        }
        echo json_encode($sendvalue);
        // echo '<pre>';
        // print_r($sendvalue);
    }
    public function add(){
        $this->data['warehouses'] = $this->general_model->GetAllWarehouses();
        $this->data['manufacturers'] = $this->general_model->GetAllManufacturers();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Productions'));
        $meta = array('page_title' => 'Productions', 'bc' => $bc);
        $this->page_construct2('productions/add', $meta, $this->data);
    }
 
    public function product($id){
        $this->db->select('products.*,units.code as unit_code');
        $this->db->from('products');
        $this->db->join('units','units.id = products.unit','left');
        $this->db->where('products.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            return $q->result()[0];
        }
        else{
            return false;
        }
    }
    public function submit(){

        $sendvalue['status'] = false;
        $sendvalue['message'] = '';


        $insert['product_id'] = $this->input->post('product');
        $insert['quantity'] = $this->input->post('quantity');
        $insert['warehouse_id'] = $this->input->post('warehouse');
        $insert['manufacturer_id'] = $this->input->post('manufacturer');

        $insert['material_cost'] = 0;
        $insert['labour_cost'] = $this->input->post('elc_amount');
        $insert['factory_cost'] = $this->input->post('efo_ammount');
        $insert['total_cost'] = 0;
        $insertitems = array();

        $this->db->select('*');
        $this->db->from('boms');
        $this->db->where('product_id',$insert['product_id']);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $bom = $q->result()[0];
            $this->db->select('bom_items.*,products.name as product_name');
            $this->db->from('bom_items');
            $this->db->join('products','products.id = bom_items.material_id','left');
            $this->db->where('bom_items.bom_id',$bom->id);
            $materials = $this->db->get()->result();
            if(count($materials) > 0){
                foreach($materials as $material){
                    $temp['production_id'] = 0;
                    $temp['material_id'] = $material->material_id;
                    $temp['rate'] = $material->rate;
                    $temp['quantity'] = $material->quantity*$insert['quantity'];
                    $temp['total'] = $material->total*$insert['quantity'];
                    $insert['material_cost'] += $temp['total'];
                    $insertitems[] = $temp;
                }
                $insert['total_cost'] = $insert['material_cost']+$insert['labour_cost']+$insert['factory_cost'];
                $this->db->insert('productions',$insert);
                $insert_id = $this->db->insert_id();
                foreach($insertitems as $insertitem){
                    $insertitem['production_id'] = $insert_id;
                    $this->db->insert('production_items',$insertitem);
                    $this->db->select('
                        pi.*,
                        p.name,
                        p.code,
                        p.company_code
                    ');
                    $this->db->from('purchase_items as pi');
                    $this->db->join('products as p','p.id = pi.product_id','left');
                    $this->db->where('pi.product_id ',$insertitem['material_id']);
                    $this->db->where('pi.warehouse_id',$insert['warehouse_id']);
                    $this->db->where('pi.quantity_balance > 0');
                    $pq =  $this->db->get();
                    $purchases = $pq->result();
                    $remaining = $insertitem['quantity'];
                    foreach($purchases as $purchase){
                        $qty = $remaining;
                        if($remaining > $purchase->quantity_balance){
                            // $qty = $remaining-$purchase->quantity_balance;
                            $qty = $purchase->quantity_balance;
                            $remaining = $remaining-$qty;
                            //Batch Quantity Update in Purchase Table
                            $this->db->set('quantity_balance', 'quantity_balance-'.$qty, FALSE);
                            $this->db->where('id', $purchase->id);
                            $this->db->update('purchase_items');
                        }
                        else{
                            //Batch Quantity Update in Purchase Table
                            $this->db->set('quantity_balance', 'quantity_balance-'.$qty, FALSE);
                            $this->db->where('id', $purchase->id);
                            $this->db->update('purchase_items');
                            break;
                        }
                    }
                    //Warehouse Quantity Update in Warehouse Product Table
                    $this->db->set('quantity', 'quantity-'.$insertitem['quantity'], FALSE);
                    $this->db->where('product_id', $insertitem['material_id']);
                    $this->db->where('warehouse_id', $insert['warehouse_id']);
                    $this->db->update('warehouses_products');
                    
                    //Product Quantity Update in Product Table
                    $this->db->set('quantity', 'quantity-'.$insertitem['quantity'], FALSE);
                    $this->db->where('id', $insertitem['material_id']);
                    $this->db->update('products');

                    
                    
                    
                    
                }
                $productdetail =  $this->db->select('*')->from('products as p')->where('p.id',$insert['product_id'])->get()->row();
                $item = array(
                    'production_id' => $insert_id,
                    'product_id' => $productdetail->id,
                    'product_code' => $productdetail->code,
                    'product_name' => $productdetail->name,
                    'option_id' => 0,
                    'net_unit_cost' => $this->sma->formatDecimal($insert['total_cost']/$insert['quantity']),
                    'unit_cost' => $insert['total_cost']/$insert['quantity'],
                    'quantity' => $insert['quantity'],
                    'product_unit_id' => $productdetail->unit,
                    'product_unit_code' => $productdetail->unit,
                    'unit_quantity' => $insert['quantity'],
                    'quantity_balance' => $insert['quantity'],
                    'quantity_received' => $insert['quantity'],
                    'warehouse_id' => $insert['warehouse_id'],
                    'item_tax' => 0,
                    'tax_rate_id' => 0,
                    'tax' => 0,
                    'adv_tax' => 0,
                    'discount' => 0,
                    'item_discount' => 0,
                    'subtotal' => $this->sma->formatDecimal($insert['total_cost']),
                    'expiry' => '',
                    'batch' => $this->input->post('batch'),
                    'price' => 0,
                    'dropship' => 0,
                    'crossdock' => 0,
                    'mrp' => $productdetail->mrp,
                    'discount_one' => 0,
                    'discount_two' => 0,
                    'discount_three' => 0,
                    'fed_tax' => 0,
                    'gst_tax' => 0,
                    'further_tax' => 0,
                    'real_unit_cost' => $insert['total_cost']/$insert['quantity'],
                    'date' => date('Y-m-d H:i:s'),
                    'status' => 'received',
                    'supplier_part_no' => '',
                );
                $this->db->insert('purchase_items', $item);


                $this->db->select('id');
                $this->db->from('warehouses_products');
                $this->db->where('product_id', $item['product_id']);
                $this->db->where('warehouse_id', $item['warehouse_id']);
                $qq = $this->db->get();
                if($qq->num_rows() == 0){
                    $winsert['product_id '] = $item['product_id'];
                    $winsert['warehouse_id '] = $item['warehouse_id'];
                    $winsert['quantity'] = $item['quantity'];
                    $winsert['avg_cost'] = 0;
                    $this->db->insert('warehouses_products', $winsert);
                }
                else{
                    //Warehouse Quantity Update in Warehouse Product Table
                    $this->db->set('quantity', 'quantity+'.$item['quantity'], FALSE);
                    $this->db->where('product_id', $item['product_id']);
                    $this->db->where('warehouse_id', $item['warehouse_id']);
                    $this->db->update('warehouses_products');
                }
                //Product Quantity Update in Product Table
                $this->db->set('quantity', 'quantity+'.$item['quantity'], FALSE);
                $this->db->where('id', $item['product_id']);
                $this->db->update('products');
                $sendvalue['status'] = true;
                $sendvalue['message'] = 'Production successfully';
            }
            else{
                $sendvalue['message'] = 'Material not found';
            }
        }
        else{
            $sendvalue['message'] = 'Product not found';
        }
        echo json_encode($sendvalue);
    }
    public function getProductByID($id){
        $this->db->select('id,code,name,cost');
        $this->db->from('products');
        $this->db->where('id',$id);
        $q = $this->db->get();
        return $q->result()[0];
    }

    public function delete(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $detail =  $this->db->select('*')->from('productions')->where('id',$id)->get()->row();
                $items =  $this->db->select('*')->from('production_items')->where('production_id',$id)->get()->result();
                foreach($items as $item){
                    //Warehouse Quantity Update in Warehouse Product Table
                    $this->db->set('quantity', 'quantity+'.$item->quantity, FALSE);
                    $this->db->where('product_id', $item->material_id);
                    $this->db->where('warehouse_id', $detail->warehouse_id);
                    $this->db->update('warehouses_products');
                    
                    //Product Quantity Update in Product Table
                    $this->db->set('quantity', 'quantity+'.$item->quantity, FALSE);
                    $this->db->where('id', $item->material_id);
                    $this->db->update('products');
                }

                //Warehouse Quantity Update in Warehouse Product Table
                $this->db->set('quantity', 'quantity-'.$detail->quantity, FALSE);
                $this->db->where('product_id', $detail->product_id);
                $this->db->where('warehouse_id', $detail->warehouse_id);
                $this->db->update('warehouses_products');
                
                //Product Quantity Update in Product Table
                $this->db->set('quantity', 'quantity-'.$detail->quantity, FALSE);
                $this->db->where('id', $detail->product_id);
                $this->db->update('products');


                $this->db->delete('purchase_items', array('production_id' => $id));
                $this->db->delete('production_items', array('production_id' => $id));
                $this->db->delete('productions', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Production delete successfully!";
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

    public function add_payment($id = null){
        $sendvalue['status'] = false;
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $production = $this->productions_model->getProductionByID($id);
        if ($production->payment_status == 'paid' && $production->grand_total == $production->paid) {
            $sendvalue['message'] = 'This production bill already paid';
        }
        else{
            $this->data['inv'] = $production;
            $this->data['payment_ref'] = ''; //$this->site->getReference('ppay');
            $sendvalue['html'] = $this->load->view($this->theme . 'productions/add_payment', $this->data,true);
            $sendvalue['status'] = true;
        }
        echo json_encode($sendvalue);
    }
    public function add_payment_submit($id = null){
        $this->load->helper('security');
        $id = $this->input->get('id');
        $production = $this->productions_model->getProductionByID($id);
        if ($production->payment_status == 'paid' && $production->total_cost == $production->paid) {
            $this->session->set_flashdata('error', 'This production bill already paid');
        }
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        if ($this->form_validation->run() == true) {
            $date = $this->input->post('date');
            $payment = array(
                'date' => $date.' '.date('H:i:s'),
                'production_id' => $this->input->post('production_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('ppay'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'created_by' => $this->session->userdata('user_id'),
                'type' => 'sent',
            );
        }
        elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
        if ($this->form_validation->run() == true && $this->productions_model->addPayment($payment)) {
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function edit_payment($id = null){
        $sendvalue['status'] = false;
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $this->data['payment'] = $this->productions_model->getPaymentByID($id);
        $sendvalue['html'] = $this->load->view($this->theme . 'productions/edit_payment', $this->data,true);
        $sendvalue['status'] = true;
        echo json_encode($sendvalue);
    }
    public function edit_payment_submit($id = null){

        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $date = $this->input->post('date');
        $payment = array(
            'date' => $date,
            'production_id' => $this->input->post('production_id'),
            'reference_no' => $this->input->post('reference_no'),
            'amount' => $this->input->post('amount-paid'),
            'paid_by' => $this->input->post('paid_by'),
            'cheque_no' => $this->input->post('cheque_no'),
            'cc_no' => $this->input->post('pcc_no'),
            'cc_holder' => $this->input->post('pcc_holder'),
            'cc_month' => $this->input->post('pcc_month'),
            'cc_year' => $this->input->post('pcc_year'),
            'cc_type' => $this->input->post('pcc_type'),
            'note' => $this->sma->clear_tags($this->input->post('note')),
        );

        if ($this->productions_model->updatePayment($id, $payment)) {

            $this->session->set_flashdata('message', lang("payment_updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->session->set_flashdata('message', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function delete_payment($id = null){
        $this->sma->checkPermissions('delete', true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->productions_model->deletePayment($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang("payment_deleted"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }


}
