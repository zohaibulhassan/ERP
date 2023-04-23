<?php defined('BASEPATH') or exit('No direct script access allowed');

class Products extends MY_Controller
{

    function __construct(){
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->lang->admin_load('products', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('admin/wordpresswoocommerce_model','wp');
        $this->load->admin_model('products_model');
        $this->load->admin_model('general_model');
        $this->load->admin_model('settings_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->popup_attributes = array('width' => '900', 'height' => '600', 'window_name' => 'sma_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');

    }
    // New Code
    function index(){
        $user = $this->site->getUser();

        $this->data['warehouse_id'] = $this->input->get('warehouse');
        $this->data['supplier'] = $this->input->get('supplier');
        $this->data['brand'] = $this->input->get('brand');
        $this->data['category'] = $this->input->get('category');
        $this->data['group'] = $this->input->get('group');
        $this->data['taxtype'] = $this->input->get('taxtype');
        $this->data['status'] = $this->input->get('status');
        $this->data['formula'] = $this->input->get('formula');


        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['formulas'] = $this->general_model->GetAllFormulas();
        if($this->session->userdata('warehouse_id') != 0){
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
        }
        $this->data['warehouse'] = $this->site->getWarehouseByID($this->data['warehouse_id']);
        
        // $this->db->select('p.*');
        // $this->db->from('products as p');
        // $q = $this->db->get();
        // $this->data['products'] = $q->result();

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('products')));
        $meta = array('page_title' => lang('products'), 'bc' => $bc);
        $this->page_construct2('products/index', $meta, $this->data);
    }
    public function get_list(){
        // Count Total Rows
        $this->db->from('products');
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



            $button = '<a href="'.base_url('admin/products/view/'.$row->id).'" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" >View</a>';
            $button .= '<a href="'.base_url('admin/products/edit/'.$row->id).'" class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" >Edit</a>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deleteproduct" data-id="'.$row->id.'" >Delete</button>';
            $button .= '<button class="md-btn ';
            if($row->status == 1){
                $button .= 'md-btn-danger';
            }
            else{
                $button .= 'md-btn-primary';
            }
            $button .= ' md-btn-wave-light waves-effect waves-button waves-light md-btn-mini statuschangeproduct" data-id="'.$row->id.'" data-status="'.$row->status.'" >';
            if($row->status == 1){
                $button .= 'Deactive';
            }
            else{
                $button .= 'Active';
            }
            $button .= '</button>';
            $cost_tax = 0;
            if($row->tax_type == 1){
                $cost_tax = ($row->cost/100)*$row->tax_rate;
            }
            else if($row->tax_type == 2){
                $cost_tax = $row->tax_rate;
            }
            $cost_tax = decimalallow($cost_tax);
            $diseases = "";
            if($row->formula_diseases != 0){
                $formula_diseases = explode(',',$row->formula_diseases);
                $disease_rows = $this->db->from('sma_diseases')->where_in('id',$formula_diseases)->get()->result();
                foreach($disease_rows as $key => $disease_row){
                    if($key > 0){
                        $diseases .= ", ";
                    }
                    $diseases .= $disease_row->name;
                }
            }
            $image_url = "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png";
            if($row->image != "" && $row->image != "no_image.png"){
                $image_url = base_url('uploads/products/'.$row->image);
            }
            $data[] = array(
                '<img src="'.$image_url.'" >',
                $row->id,
                $row->code,
                $row->name,
                $row->total_qty,
                $row->unit,
                decimalallow($row->cost,4),
                decimalallow($cost_tax,4),
                decimalallow($row->cost+$cost_tax,4),
                decimalallow($row->mrp,4),
                $row->alert_quantity,
                $row->brand,
                $row->category,
                $row->subcategory,
                $row->formula_code,
                $row->prescription == 1 ? "Yes" : "No",
                $diseases,
                $row->tax_type == 1 ? "GST" :  "3rd Schedule",
                $row->tax_rate,
                $row->fed_tax,
                $row->company_code,
                "",
                $row->manufacturer,
                $row->carton_size,
                $row->pack_size,
                $row->short_expiry_duration,
                $row->es_durration,
                $row->total_sold,
                $row->group_id,
                $row->group_name,
                $row->status == 1 ? "<span class='uk-badge uk-badge-success'>Active</span>" : "<span class='uk-badge uk-badge-danger'>Deactive</span>",
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
        $warehouse_id = $this->input->get('warehouse');
        $supplier = $this->input->get('supplier');
        $brand = $this->input->get('brand');
        $category = $this->input->get('category');
        $group = $this->input->get('group');
        $taxtype = $this->input->get('taxtype');
        $formula = $this->input->get('formula');
        $status = $this->input->get('status');

        $column_search = array(
            'products.id',
            'products.code',
            'products.company_code',
            'products.name',
            'cat.name',
            'subcat.name',
            'tax_rates.type',
            'brands.name',
            'product_groups.name',
            'product_formulas.code'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('products.id as id');
        }
        else{
            $this->db->select('
                products.image,
                products.id,
                products.code,
                products.name,
            ');
            if($warehouse_id != 0 && $warehouse_id != ""){
                $this->db->select('
                    IFNULL((
                        SELECT
                            SUM(pi.quantity_balance)
                        FROM
                            sma_purchase_items AS pi 
                        WHERE 
                            pi.product_id = products.id
                            AND pi.warehouse_id = '.$warehouse_id.'
                    ),0) as total_qty,
                ');
            }
            else{
                $this->db->select('
                    IFNULL((
                        SELECT
                            SUM(pi.quantity_balance)
                        FROM
                            sma_purchase_items AS pi 
                        WHERE 
                            pi.product_id = products.id
                    ),0) as total_qty,
                ');
            }
            $this->db->select('
                units.code as unit,
                products.cost,
                "" AS taxvalue,
                "" AS costtaxvalue,
                products.mrp,
                products.alert_quantity,
                brands.name as brand,
                cat.name as category,
                subcat.name as subcategory,
                product_formulas.code as formula_code,
                products.prescription,
                product_formulas.diseases as formula_diseases,
                tax_rates.type as tax_type,
                tax_rates.rate as tax_rate,
                products.fed_tax,
                products.company_code,
                "" as supplier,
                sma_manufacturers.name as manufacturer,
                products.carton_size,
                products.pack_size,
                products.short_expiry_duration,
                products.es_durration,
                IFNULL((
                    SELECT SUM(si.quantity) FROM sma_sale_items as si WHERE si.product_id = products.id
                ),0) as total_sold,
                product_groups.id as group_id,
                product_groups.name as group_name,
                products.status
            ');
        }
        $this->db->from('products as products');
        $this->db->join('units','units.id = products.unit','left');
        $this->db->join('categories as cat','cat.id = products.category_id','left');
        $this->db->join('categories as subcat','subcat.id = products.subcategory_id','left');
        $this->db->join('tax_rates','tax_rates.id = products.tax_rate','left');
        $this->db->join('sma_manufacturers','sma_manufacturers.id = products.manufacturer','left');
        $this->db->join('brands','brands.id = products.brand','left');
        $this->db->join('product_formulas','product_formulas.id = products.formulas','left');
        $this->db->join('product_groups','product_groups.id = products.group_id','left');
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
        if($_POST['warehouse'] != ""){
        }
        if($_POST['supplier'] != ""){
            $this->db->where("(supplier1 = ".$_POST['supplier']." OR supplier2 = ".$_POST['supplier']." OR supplier3 = ".$_POST['supplier']." OR supplier4 = ".$_POST['supplier']." OR supplier5 = ".$_POST['supplier'].")");
        }
        if($_POST['brand'] != ""){
            $this->db->where('products.brand',$_POST['brand']);
        }
        if($_POST['category'] != ""){
            $this->db->where("(products.category_id = ".$_POST['category']." OR products.subcategory_id = ".$_POST['category'].")");
        }
        if($_POST['group'] != ""){
            $this->db->where('products.group_id',$_POST['group']);
        }
        if($_POST['taxtype'] != ""){
            $this->db->where('tax_rates.type',$_POST['taxtype']);
        }
        if($_POST['formula'] != ""){
            $this->db->where('product_formulas.id',$_POST['formula']);
        }
        if($_POST['status'] != ""){
            $this->db->where('products.status',$_POST['status']);
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }
    public function add(){
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $this->data['taxs'] = $this->general_model->GetAllProduct_tax();
        $this->data['units'] = $this->general_model->GetAllUnits();
        $this->data['groups'] = $this->general_model->GetAllGroups();
        $this->data['categories'] = $this->general_model->GetAllCategories(0);
        $this->data['subcategories'] = $this->general_model->GetAllSubCategories();
        $this->data['brands'] = $this->general_model->GetAllBrands();
        $this->data['manufacturers'] = $this->general_model->GetAllManufacturers();
        $this->data['formulas'] = $this->general_model->GetAllFormulas();
        $this->data['forms'] = $this->general_model->GetAllProductForms();

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_product')));
        $meta = array('page_title' => lang('add_product'), 'bc' => $bc);
        $this->page_construct2('products/add', $meta, $this->data);

    }
    public function insert_submit(){
        $sendvalue['status'] = false;
        $sendvalue['message'] = "";

        $this->db->select('id');
        $this->db->from('products');
        $this->db->where('code',$this->input->post('barcode'));
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $sendvalue['message'] = "Barcode already available!";
        }
        else{
            $barcode = $this->input->post('barcode');
            if($barcode == ""){
                $barcode = date('Y').''.date('m').''.date('d').''.date('H').''.date('i').''.date('s');
            }
            $insert['code'] = $barcode;
            $insert['company_code'] = $this->input->post('companycode');
            $insert['name'] = $this->input->post('name');
            $insert['unit'] = $this->input->post('unit');
            $insert['cost'] = $this->input->post('cost');
            $insert['price'] = $this->input->post('consignment');
            $insert['dropship'] = $this->input->post('dropship');
            $insert['crossdock'] = $this->input->post('crossdock');
            $insert['mrp'] = $this->input->post('mrp');
            $insert['discount_mrp'] = 0;
            $insert['alert_quantity'] = $this->input->post('alertqty');
            $insert['category_id '] = $this->input->post('category');
            $insert['subcategory_id'] = $this->input->post('subcategory');
            $insert['quantity'] = 0;
            $insert['tax_rate'] = $this->input->post('producttax');
            $insert['track_quantity'] = 0;
            $insert['product_details'] = $this->input->post('detail');
            $insert['warehouse'] = 0;
            $insert['barcode_symbology'] = 'code128';
            $insert['tax_method'] = $this->input->post('texmethod');
            $insert['type'] = 'standard';
            $insert['supplier1'] = $this->input->post('supplier1');
            $insert['supplier2'] = $this->input->post('supplier2');
            $insert['supplier3'] = $this->input->post('supplier3');
            $insert['supplier4'] = $this->input->post('supplier4');
            $insert['supplier5'] = $this->input->post('supplier5');
            $insert['sale_unit'] = $this->input->post('unit');
            $insert['purchase_unit'] = $this->input->post('unit');
            $insert['brand '] = $this->input->post('brnad');
            $insert['weight'] = $this->input->post('weight');
            $insert['hsn_code'] = $this->input->post('hsncode');
            $insert['discount_one'] = $this->input->post('si_dicount');
            $insert['discount_two'] = $this->input->post('t_discount');
            $insert['discount_three'] = $this->input->post('c_discount');
            $insert['fed_tax'] = $this->input->post('fed_tax');
            $insert['pack_size'] = $this->input->post('packsize');
            $insert['carton_size'] = $this->input->post('cartonsize');
            $insert['status'] = 1;
            $insert['unit_weight'] = $this->input->post('unit');
            $insert['short_expiry_duration'] = $this->input->post('se_expiry');
            $insert['es_durration'] = $this->input->post('sold_days');
            $insert['hold_stock'] = $this->input->post('hold_qty');
            $insert['group_id'] = $this->input->post('group');
            $insert['adv_tax_reg'] = $this->input->post('ratax_sale');
            $insert['adv_tax_nonreg'] = $this->input->post('nratax_sale');
            $insert['adv_tax_for_purchase'] = $this->input->post('ratax_purchase');
            $insert['manufacturer'] = $this->input->post('manufacturer');
            $insert['formulas'] = $this->input->post('formula');
            $insert['prescription'] = $this->input->post('prescription');
            $insert['form_id'] = $this->input->post('productform');

            $config['upload_path']          = './uploads/products/';
            $config['allowed_types']        = 'jpg|png|jpeg|gif|PNG';
            $config['encrypt_name'] = true;
            $config['file_name'] = date("Y")."".date("m")."".date("d")."".date("H")."".date("i")."".date("s");
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('image')){
                $insert['image'] = $this->upload->file_name;
            }
            $this->db->insert('products', $insert);
            $sendvalue['message'] = "Product create successfully.";
            $sendvalue['status'] = true;
            $sendvalue['id'] = $this->db->insert_id();

            $stores = $this->db->select('id,warehouse_id')->from('sma_stores_tb')->where('status','active')->get()->result();
            foreach($stores as $store){
                
                $insert2['store_id'] = $store->id;
                $insert2['update_in'] = "full";
                $insert2['product_id'] = $sendvalue['id'];
                $insert2['product_name'] = $this->input->post('name');
                $insert2['store_product_id'] = "";
                $insert2['update_qty_in'] = "pack";
                $insert2['price_type'] = "mrp";
                $insert2['discount'] = "no";
                $insert2['warehouse_id'] = 4;
                $insert2['supplier_id'] = $this->input->post('supplier1');
                $insert2['created_by'] = "58";
                $insert2['updated_by'] = "0";
                $insert2['status'] = "active";
                $this->db->insert('sma_store_products_tb',$insert2);

                $insert3['product_id'] = $sendvalue['id'];
                $insert3['warehouse_id'] = $store->warehouse_id;
                $insert3['store_id'] = $store->id;
                $insert3['type'] = "Qty Update";
                $insert3['status'] = "pending";
                $this->db->insert('sma_store_requests_tb',$insert3);
            }
        }
        echo json_encode($sendvalue);
    }
    public function edit($id = ""){
        if($id != ""){
            $this->db->select('*');
            $this->db->from('products');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['product'] = $q->result()[0];
                $this->data['product']->subcategory_id;
                $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
                $this->data['taxs'] = $this->general_model->GetAllProduct_tax();
                $this->data['units'] = $this->general_model->GetAllUnits();
                $this->data['groups'] = $this->general_model->GetAllGroups();
                $this->data['categories'] = $this->general_model->GetAllCategories(0);
                $this->data['subcategories'] = $this->general_model->GetAllSubCategories();
                $this->data['brands'] = $this->general_model->GetAllBrands();
                $this->data['manufacturers'] = $this->general_model->GetAllManufacturers();
                $this->data['formulas'] = $this->general_model->GetAllFormulas();
                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('Edit Product')));
                $meta = array('page_title' => lang('Edit Product'), 'bc' => $bc);
                $this->page_construct2('products/edit', $meta, $this->data);
            }
            else{
                redirect(base_url('admin/products'));
            }
        }
        else{
            redirect(base_url('admin/products'));
        }
    }
    public function update_submit(){
        $sendvalue['status'] = false;
        $sendvalue['message'] = "";
        $product_id = $this->input->post('product_id');

        $this->db->select('id');
        $this->db->from('products');
        $this->db->where('code = "'.$this->input->post('barcode').'" AND id != '.$product_id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $sendvalue['message'] = "Barcode already available!";
        }
        else{
            $set['code'] = $this->input->post('barcode');
            $set['company_code'] = $this->input->post('companycode');
            $set['name'] = $this->input->post('name');
            $set['unit'] = $this->input->post('unit');
            $set['cost'] = $this->input->post('cost');
            $set['price'] = $this->input->post('consignment');
            $set['dropship'] = $this->input->post('dropship');
            $set['crossdock'] = $this->input->post('crossdock');
            $set['mrp'] = $this->input->post('mrp');
            $set['discount_mrp'] = 0;
            $set['alert_quantity'] = $this->input->post('alertqty');
            $set['category_id '] = $this->input->post('category');
            $set['subcategory_id'] = $this->input->post('subcategory');
            $set['quantity'] = 0;
            $set['tax_rate'] = $this->input->post('producttax');
            $set['track_quantity'] = 0;
            $set['product_details'] = $this->input->post('detail');
            $set['warehouse'] = 0;
            $set['barcode_symbology'] = 'code128';
            $set['tax_method'] = $this->input->post('texmethod');
            $set['type'] = 'standard';
            $set['supplier1'] = $this->input->post('supplier1');
            $set['supplier2'] = $this->input->post('supplier2');
            $set['supplier3'] = $this->input->post('supplier3');
            $set['supplier4'] = $this->input->post('supplier4');
            $set['supplier5'] = $this->input->post('supplier5');
            $set['sale_unit'] = $this->input->post('unit');
            $set['purchase_unit'] = $this->input->post('unit');
            $set['brand '] = $this->input->post('brnad');
            $set['weight'] = $this->input->post('weight');
            $set['hsn_code'] = $this->input->post('hsncode');
            $set['discount_one'] = $this->input->post('si_dicount');
            $set['discount_two'] = $this->input->post('t_discount');
            $set['discount_three'] = $this->input->post('c_discount');
            $set['fed_tax'] = $this->input->post('fed_tax');
            $set['pack_size'] = $this->input->post('packsize');
            $set['carton_size'] = $this->input->post('cartonsize');
            $set['status'] = $this->input->post('status');
            $set['unit_weight'] = $this->input->post('unit');
            $set['short_expiry_duration'] = $this->input->post('se_expiry');
            $set['es_durration'] = $this->input->post('sold_days');
            $set['hold_stock'] = $this->input->post('hold_qty');
            $set['group_id'] = $this->input->post('group');
            $set['adv_tax_reg'] = $this->input->post('ratax_sale');
            $set['adv_tax_nonreg'] = $this->input->post('nratax_sale');
            $set['adv_tax_for_purchase'] = $this->input->post('ratax_purchase');
            $set['manufacturer'] = $this->input->post('manufacturer');
            $set['formulas'] = $this->input->post('formula');
            $set['prescription'] = $this->input->post('prescription');
            $set['form_id'] = $this->input->post('productform');
            $config['upload_path']          = './uploads/products/';
            $config['allowed_types']        = 'jpg|png|jpeg|gif|PNG';
            $config['encrypt_name'] = true;
            $config['file_name'] = date("Y")."".date("m")."".date("d")."".date("H")."".date("i")."".date("s");
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('image')){
                $set['image'] = $this->upload->file_name;
            }
            $this->db->set($set);
            $this->db->where('id',$product_id);
            $this->db->update('products');
            $this->load->model('admin/wordpresswoocommerce_model','wp');
            // $this->wp->update_product_detail($product_id);
            $this->wp->update_product($product_id);
            $stores = $this->db->select('id,warehouse_id')->from('sma_stores_tb')->where('status','active')->get()->result();
            foreach($stores as $store){
                $insert3['product_id'] = $product_id;
                $insert3['warehouse_id'] = $store->warehouse_id;
                $insert3['store_id'] = $store->id;
                $insert3['type'] = "Qty Update";
                $insert3['status'] = "pending";
                $this->db->insert('sma_store_requests_tb',$insert3);
            }
            $sendvalue['message'] = "Product update successfully.";
            $sendvalue['status'] = true;
        }
        echo json_encode($sendvalue);
    }
    public function view($id = ""){
        if($id != ""){
            $this->db->select('
                products.*,
                units.code as unit,
                cat.name as category,
                subcat.name as subcategory,
                tax_rates.type as tax_type,
                tax_rates.rate as tax_rate,
                brands.name as brand,
                product_groups.id as group_id,
                product_groups.name as group_name
            ');
            $this->db->from('products');
            $this->db->join('units','units.id = products.unit','left');
            $this->db->join('categories as cat','cat.id = products.category_id','left');
            $this->db->join('categories as subcat','subcat.id = products.subcategory_id','left');
            $this->db->join('tax_rates','tax_rates.id = products.tax_rate','left');
            $this->db->join('brands','brands.id = products.brand','left');
            $this->db->join('product_groups','product_groups.id = products.group_id','left');
            $this->db->where('products.id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['product'] = $q->result()[0];
                $sid = array();
                if($this->data['product']->supplier1 != 0 ){ $sid[] = $this->data['product']->supplier1; }
                if($this->data['product']->supplier2 != 0 ){ $sid[] = $this->data['product']->supplier2; }
                if($this->data['product']->supplier3 != 0 ){ $sid[] = $this->data['product']->supplier3; }
                if($this->data['product']->supplier4 != 0 ){ $sid[] = $this->data['product']->supplier4; }
                if($this->data['product']->supplier5 != 0 ){ $sid[] = $this->data['product']->supplier5; }
                $this->db->select('id,name');
                $this->db->from('companies');
                $this->db->where_in('id', $sid);
                $this->data['suppliers'] = $this->db->get()->result();

                $this->db->select('wp.warehouse_id,warehouses.name,wp.quantity');
                $this->db->from('warehouses_products as wp');
                $this->db->join('warehouses','warehouses.id = wp.warehouse_id','left');
                $this->db->where('wp.product_id', $id);
                $this->db->where('wp.quantity > 0');

                $this->data['warehouses'] = $this->db->get()->result();
                $this->data['purchases']  = $this->products_model->get_purchases($id);
                $this->data['sales']  = $this->products_model->get_sales($id);
                
                
                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('View Product')));
                $meta = array('page_title' => lang('View Product'), 'bc' => $bc);
                $this->page_construct2('products/view', $meta, $this->data);
            }
            else{
                redirect(base_url('admin/products'));
            }
        }
        else{
            redirect(base_url('admin/products'));
        }
    }
    public function delete(){
        
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner'] || $this->data['Admin'] || $this->data['GP']['products-delete'] ){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('purchase_items');
                $this->db->where('product_id',$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('products', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Product delete successfully!";
                }
                else{
                    $senddata['message'] = "You can not delete this product!";
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
    public function statuschangeproduct(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $reason = $this->input->post('reason');
        if($reason != ""){

            if($status == 1){
                $set['status'] = "0";
            }
            else{
                $set['status'] = "1";
            }

            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('products');

            $senddata['status'] = true;
            $senddata['message'] = "Product status change successfully!";
        }
        else{
            $senddata['message'] = "Enter Reason!";
        }
        echo json_encode($senddata);
    }
    public function groups(){
        $this->data['wid'] = $this->input->get('warehouse');
        $this->data['warehouses'] = $this->general_model->GetAllWarehouses();
        if($this->session->userdata('warehouse_id') != 0){
            $this->data['wid'] = $this->session->userdata('warehouse_id');
        }
        $this->data['brands'] = $this->general_model->GetAllBrands();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('products')), array('link' => '#', 'page' => lang('Groups')));
        $meta = array('page_title' => lang('Product Groups'), 'bc' => $bc);
        $this->page_construct2('products/groups', $meta, $this->data);
    }
    public function get_group(){
        // Count Total Rows
        $this->db->from('product_groups');
        $totalq = $this->db->get();
        $this->runquery_group('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_group();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/products?group=".$row->id).'" >Products List</a>';
            $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-name="'.$row->name.'" data-bid="'.$row->brand_id.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->name,
                $row->brand,
                $row->active_sku,
                $row->deactive_sku,
                $row->total_sku,
                $row->total_qty,
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
    public function runquery_group($onlycoun = "no"){
        $wid = $_POST['wid'];
        $column_order = array(
            'groups.id',
            'groups.name',
            'brands.name',
            '3',
            '4',
            '5',
            '6'
        );
        $column_search = array(
            'groups.id',
            'groups.name',
            'brands.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('groups.id as id');
        }
        else{
            $this->db->select('
                groups.id,
                groups.name,
                brands.name as brand,
                (
                    SELECT COUNT(id) FROM sma_products WHERE group_id = groups.id AND status = 1
                ) as active_sku,
                (
                    SELECT COUNT(id) FROM sma_products WHERE group_id = groups.id AND status = 0
                ) as deactive_sku,
                (SELECT active_sku) - (SELECT deactive_sku) as total_sku,
            ');
            if($wid != 0 && $wid != ""){
                $this->db->select('
                    IFNULL((
                        SELECT
                            SUM(pi.quantity_balance)
                        FROM
                            sma_products AS pro 
                        LEFT JOIN sma_purchase_items AS pi ON pi.product_id = pro.id
                        WHERE 
                            pro.group_id = groups.id
                            AND pi.warehouse_id = '.$wid.'
                    ),0) as total_qty,
                ');
            }
            else{
                $this->db->select('
                    IFNULL((
                        SELECT
                            SUM(pi.quantity_balance)
                        FROM
                            sma_products AS pro 
                        LEFT JOIN sma_purchase_items AS pi ON pi.product_id = pro.id
                        WHERE 
                            pro.group_id = groups.id
                    ),0) as total_qty,
                ');
            }
            $this->db->select('groups.brand_id');
        }
        $this->db->from('product_groups as groups');
        $this->db->join('brands', 'brands.id = groups.brand_id', 'left');
        // $this->db->join('products as products', 'products.group_id = groups.id', 'left');
        // $this->db->group_by('products.id');
        
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
    public function insert_group(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $name = $this->input->post('name');
        $brand = $this->input->post('brand');
        if($name != ""){
            if($brand != ""){
                $this->db->select('*');
                $this->db->from('product_groups');
                $this->db->where('name = "'.$name.'" AND brand_id = "'.$brand.'"');
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $insert['name'] = $name;
                    $insert['brand_id'] = $brand;
                    $this->db->insert('product_groups',$insert);
                    $senddata['message'] = "Product group create successfully";
                    $senddata['status'] = true;
                }
                else{
                    $senddata['message'] = "Product group already available";
                }
            }
            else{
                $senddata['message'] = "Select Brand";
            }
        }
        else{
            $senddata['message'] = "Enter Group Name!";
        }
        echo json_encode($senddata);
    }
    public function update_group(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $brand = $this->input->post('brand');
        if($name != ""){
            if($brand != ""){
                $set['name'] = $name;
                $set['brand_id'] = $brand;
                $this->db->set($set);
                $this->db->where('id',$id);
                $this->db->update('product_groups');
                $senddata['message'] = "Product gorup update successfully";
                $senddata['status'] = true;
            }
            else{
                $senddata['message'] = "Select Brand";
            }
        }
        else{
            $senddata['message'] = "Enter Group Name!";
        }
        echo json_encode($senddata);
    }
    public function delete_group(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('products');
                $this->db->where('group_id = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('product_groups', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Product group delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete Products or remove this product group form that products then delete this product group!";
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
    // Product Formula Start
    function formulas(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Formulas')));
        $meta = array('page_title' => lang('Formulas'), 'bc' => $bc);
        $this->data['diseases'] = $this->general_model->GetAllDisease();
        $this->data['forms'] = $this->general_model->GetAllFormulaForm();
        $this->data['strengths'] = $this->general_model->GetAllFormulaStrengths();
        $this->page_construct2('products/formulas', $meta, $this->data);
    }
    public function get_formulas(){
        // Count Total Rows
        $this->db->from('product_formulas');
        $totalq = $this->db->get();
        $this->runquery_formulas('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_formulas();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/products?formula=".$row->id).'" >Products List</a>';
            $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'" data-name="'.$row->name.'" data-form="'.$row->form_id.'" data-strength="'.$row->strength_id.'" data-diseases="'.$row->disease.'" data-code="'.$row->code.'" data-description="'.$row->description.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->code,
                $row->name,
                $row->form_name,
                $row->strength_name,
                $row->disease,
                $row->description,
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
    public function runquery_formulas($onlycoun = "no"){
        $column_order = array(
            'product_formulas.id',
            'product_formulas.code',
            'product_formulas.name',
            'form_tb.name',
            'strength_tb.name',
            'product_formulas.diseases',
            'product_formulas.description',
            5
        );
        $column_search = array(
            'product_formulas.id',
            'product_formulas.code',
            'product_formulas.name',
            'form_tb.name',
            'strength_tb.name',
            'product_formulas.diseases',
            'product_formulas.description'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('product_formulas.id as id');
        }
        else{
            $this->db->select('
                product_formulas.id,
                product_formulas.code,
                product_formulas.name,
                product_formulas.form_id,
                product_formulas.strength_id,
                form_tb.name as form_name,
                strength_tb.name as strength_name,
                product_formulas.diseases as disease,
                product_formulas.description,
                (
                    SELECT COUNT(sma_products.id) FROM sma_products WHERE sma_products.formulas = product_formulas.id

                ) as no_products
            ');
        }
        $this->db->from('product_formulas as product_formulas');
        $this->db->join('formula_forms as form_tb','form_tb.id = product_formulas.form_id','left');
        $this->db->join('formula_strengths as strength_tb','strength_tb.id = product_formulas.strength_id','left');
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
    public function insert_formula(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $form = $this->input->post('form');
        $strength = $this->input->post('strength');
        $diseases = $this->input->post('diseases');
        $dis = "";
        foreach($diseases as $key => $d){
            if($key > 0){
                $dis .= ",";
            }
            $dis .= $d;
        }
        $description = $this->input->post('description');
        if($name != ""){
            $this->db->select('*');
            $this->db->from('product_formulas');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Formula already available";
            }
            else{
                $insert['code'] = $code;
                $insert['name'] = $name;
                $insert['diseases'] = $dis;
                $insert['form_id'] = $form;
                $insert['strength_id'] = $strength;
                $insert['description'] = $description;
                $insert['created_by'] = $this->session->userdata('user_id');
                $this->db->insert('product_formulas',$insert);
                $senddata['message'] = "Formula name create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_formula(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $name = $this->input->post('name');
        $form = $this->input->post('form');
        $strength = $this->input->post('strength');
        $diseases = $this->input->post('diseases');
        $dis = "";
        foreach($diseases as $key => $d){
            if($key > 0){
                $dis .= ",";
            }
            $dis .= $d;
        }
        $description = $this->input->post('description');
        if($name != ""){
            $set['code'] = $code;
            $set['name'] = $name;
            $set['diseases'] = $dis;
            $set['form_id'] = $form;
            $set['strength_id'] = $strength;
            $set['description'] = $description;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('product_formulas');
            $senddata['message'] = "Formula update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_formula(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('products');
                $this->db->where('formulas = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('product_formulas', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "Formula delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete purchases then delete this formula!";
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
    // Product Formula End
    // Formula Form Code Start
    function formula_forms(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Brands')));
        $meta = array('page_title' => lang('Forms'), 'bc' => $bc);
        $this->page_construct2('products/forms', $meta, $this->data);
    }
    public function get_forms(){
        // Count Total Rows
        $this->db->from('formula_forms');
        $totalq = $this->db->get();
        $this->runquery_formula_forms('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_formula_forms();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'"  data-name="'.$row->name.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
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
    public function runquery_formula_forms($onlycoun = "no"){
        $column_order = array(
            null,
            'formula_forms.id',
            'formula_forms.name'
        );
        $column_search = array(
            'formula_forms.id',
            'formula_forms.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('formula_forms.id as id');
        }
        else{
            $this->db->select('
                formula_forms.id,
                formula_forms.name,
            ');
        }
        $this->db->from('formula_forms as formula_forms');
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
    public function insert_form(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $name = $this->input->post('name');
        if($name != ""){
            $this->db->select('*');
            $this->db->from('formula_forms');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Form already available";
            }
            else{
                $insert['name'] = $name;
                $this->db->insert('formula_forms',$insert);
                $senddata['message'] = "Form create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_form(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        if($name != ""){
            $set['name'] = $name;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('formula_forms');
            $senddata['message'] = "Form update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_form(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('product_formulas');
                $this->db->where('form_id = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('formula_forms', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "From delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete formulas then delete this form!";
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
    // Formula Form Code End

    // Formula strength Code Start
    function formula_strengths(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Strengths')));
        $meta = array('page_title' => lang('Strengths'), 'bc' => $bc);
        $this->page_construct2('products/strengths', $meta, $this->data);
    }
    public function get_strengths(){
        // Count Total Rows
        $this->db->from('formula_strengths');
        $totalq = $this->db->get();
        $this->runquery_formula_strengths('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_formula_strengths();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'"  data-name="'.$row->name.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
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
    public function runquery_formula_strengths($onlycoun = "no"){
        $column_order = array(
            null,
            'formula_strengths.id',
            'formula_strengths.name'
        );
        $column_search = array(
            'formula_strengths.id',
            'formula_strengths.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('formula_strengths.id as id');
        }
        else{
            $this->db->select('
                formula_strengths.id,
                formula_strengths.name,
            ');
        }
        $this->db->from('formula_strengths as formula_strengths');
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
    public function insert_strength(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $name = $this->input->post('name');
        if($name != ""){
            $this->db->select('*');
            $this->db->from('formula_strengths');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Strength already available";
            }
            else{
                $insert['name'] = $name;
                $this->db->insert('formula_strengths',$insert);
                $senddata['message'] = "Strength create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_strength(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        if($name != ""){
            $set['name'] = $name;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('formula_strengths');
            $senddata['message'] = "Strength update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_strengths(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('product_formulas');
                $this->db->where('strength_id = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('formula_strengths', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "From delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete formulas then delete this strength!";
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
    // Formula strength Code End
    // Product Forms Code Start
    function product_forms(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('system_settings'), 'page' => lang('system_settings')), array('link' => '#', 'page' => lang('Product Forms')));
        $meta = array('page_title' => lang('product_forms'), 'bc' => $bc);
        $this->page_construct2('products/product_forms', $meta, $this->data);
    }
    public function get_product_forms(){
        // Count Total Rows
        $this->db->from('product_forms');
        $totalq = $this->db->get();
        $this->runquery_product_forms('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_product_forms();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini editbtn" data-id="'.$row->id.'"  data-name="'.$row->name.'" >edit</button>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
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
    public function runquery_product_forms($onlycoun = "no"){
        $column_order = array(
            null,
            'product_forms.id',
            'product_forms.name'
        );
        $column_search = array(
            'product_forms.id',
            'product_forms.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('product_forms.id as id');
        }
        else{
            $this->db->select('
                product_forms.id,
                product_forms.name,
            ');
        }
        $this->db->from('product_forms as product_forms');
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
    public function insert_product_form(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $name = $this->input->post('name');
        if($name != ""){
            $this->db->select('*');
            $this->db->from('product_forms');
            $this->db->where('name = "'.$name.'"');
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $senddata['message'] = "Product form already available";
            }
            else{
                $insert['name'] = $name;
                $this->db->insert('product_forms',$insert);
                $senddata['message'] = "Product form create successfully";
                $senddata['status'] = true;
            }
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function update_product_form(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        if($name != ""){
            $set['name'] = $name;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('product_forms');
            $senddata['message'] = "Product form update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_product_form(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->select('id');
                $this->db->from('product_formulas');
                $this->db->where('form_id = '.$id);
                $q = $this->db->get();
                if($q->num_rows() == 0){
                    $this->db->delete('product_forms', array('id' => $id));
                    $senddata['status'] = true;
                    $senddata['message'] = "From delete successfully!";
                }
                else{
                    $senddata['message'] = "Delete formulas then delete this product forms!";
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
    // Product Form Code End






    // Old Code 

    function set_rack($product_id = NULL, $warehouse_id = NULL)
    {
        $this->sma->checkPermissions('edit', true);

        $this->form_validation->set_rules('rack', lang("rack_location"), 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = array(
                'rack' => $this->input->post('rack'),
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
            );
        } elseif ($this->input->post('set_rack')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("products/" . $warehouse_id);
        }

        if ($this->form_validation->run() == true && $this->products_model->setRack($data)) {
            $this->session->set_flashdata('message', lang("rack_set"));
            admin_redirect("products/" . $warehouse_id);
        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['product'] = $this->site->getProductByID($product_id);
            $wh_pr = $this->products_model->getProductQuantity($product_id, $warehouse_id);
            $this->data['rack'] = $wh_pr['rack'];
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'products/set_rack', $this->data);
        }
    }

    function barcode($product_code = NULL, $bcs = 'code128', $height = 40)
    {
        if ($this->Settings->barcode_img) {
            header('Content-Type: image/png');
        } else {
            header('Content-type: image/svg+xml');
        }
        echo $this->sma->barcode($product_code, $bcs, $height, true, false, true);
    }

    function print_barcodes($product_id = NULL)
    {
        $this->sma->checkPermissions('barcode', true);

        $this->form_validation->set_rules('style', lang("style"), 'required');

        if ($this->form_validation->run() == true) {

            $style = $this->input->post('style');
            $bci_size = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            $currencies = $this->site->getAllCurrencies();
            $s = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            if ($s < 1) {
                $this->session->set_flashdata('error', lang('no_product_selected'));
                admin_redirect("products/print_barcodes");
            }
            for ($m = 0; $m < $s; $m++) {
                $pid = $_POST['product'][$m];
                $quantity = $_POST['quantity'][$m];
                $product = $this->products_model->getProductWithCategory($pid);
                $product->price = $this->input->post('check_promo') ? ($product->promotion ? $product->promo_price : $product->price) : $product->price;
                if ($variants = $this->products_model->getProductOptions($pid)) {
                    foreach ($variants as $option) {
                        if ($this->input->post('vt_' . $product->id . '_' . $option->id)) {
                            $barcodes[] = array(
                                'site' => $this->input->post('site_name') ? $this->Settings->site_name : FALSE,
                                'name' => $this->input->post('product_name') ? $product->name . ' - ' . $option->name : FALSE,
                                'image' => $this->input->post('product_image') ? $product->image : FALSE,
                                'barcode' => $product->code . $this->Settings->barcode_separator . $option->id,
                                'bcs' => 'code128',
                                'bcis' => $bci_size,
                                // 'barcode' => $this->product_barcode($product->code . $this->Settings->barcode_separator . $option->id, 'code128', $bci_size),
                                'price' => $this->input->post('price') ?  $this->sma->formatMoney($option->price != 0 ? ($product->price + $option->price) : $product->price, 'none') : FALSE,
                                'rprice' => $this->input->post('price') ?  ($option->price != 0 ? ($product->price + $option->price) : $product->price) : FALSE,
                                'unit' => $this->input->post('unit') ? $product->unit : FALSE,
                                'category' => $this->input->post('category') ? $product->category : FALSE,
                                'currencies' => $this->input->post('currencies'),
                                'variants' => $this->input->post('variants') ? $variants : FALSE,
                                'quantity' => $quantity
                            );
                        }
                    }
                } else {
                    $barcodes[] = array(
                        'site' => $this->input->post('site_name') ? $this->Settings->site_name : FALSE,
                        'name' => $this->input->post('product_name') ? $product->name : FALSE,
                        'image' => $this->input->post('product_image') ? $product->image : FALSE,
                        // 'barcode' => $this->product_barcode($product->code, $product->barcode_symbology, $bci_size),
                        'barcode' => $product->code,
                        'bcs' => $product->barcode_symbology,
                        'bcis' => $bci_size,
                        'price' => $this->input->post('price') ?  $this->sma->formatMoney($product->price, 'none') : FALSE,
                        'rprice' => $this->input->post('price') ? $product->price : FALSE,
                        'unit' => $this->input->post('unit') ? $product->unit : FALSE,
                        'category' => $this->input->post('category') ? $product->category : FALSE,
                        'currencies' => $this->input->post('currencies'),
                        'variants' => FALSE,
                        'quantity' => $quantity
                    );
                }
            }
            $this->data['barcodes'] = $barcodes;
            $this->data['currencies'] = $currencies;
            $this->data['style'] = $style;
            $this->data['items'] = false;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('print_barcodes')));
            $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
            $this->page_construct('products/print_barcodes', $meta, $this->data);
        } else {

            if ($this->input->get('purchase') || $this->input->get('transfer')) {
                if ($this->input->get('purchase')) {
                    $purchase_id = $this->input->get('purchase', TRUE);
                    $items = $this->products_model->getPurchaseItems($purchase_id);
                } elseif ($this->input->get('transfer')) {
                    $transfer_id = $this->input->get('transfer', TRUE);
                    $items = $this->products_model->getTransferItems($transfer_id);
                }
                if ($items) {
                    foreach ($items as $item) {
                        if ($row = $this->products_model->getProductByID($item->product_id)) {
                            $selected_variants = false;
                            if ($variants = $this->products_model->getProductOptions($row->id)) {
                                foreach ($variants as $variant) {
                                    $selected_variants[$variant->id] = isset($pr[$row->id]['selected_variants'][$variant->id]) && !empty($pr[$row->id]['selected_variants'][$variant->id]) ? 1 : ($variant->id == $item->option_id ? 1 : 0);
                                }
                            }
                            $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $item->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                        }
                    }
                    $this->data['message'] = lang('products_added_to_list');
                }
            }

            if ($product_id) {
                if ($row = $this->site->getProductByID($product_id)) {

                    $selected_variants = false;
                    if ($variants = $this->products_model->getProductOptions($row->id)) {
                        foreach ($variants as $variant) {
                            $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                        }
                    }
                    $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);

                    $this->data['message'] = lang('product_added_to_list');
                }
            }

            if ($this->input->get('category')) {
                if ($products = $this->products_model->getCategoryProducts($this->input->get('category'))) {
                    foreach ($products as $row) {
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }
                    $this->data['message'] = lang('products_added_to_list');
                } else {
                    $pr = array();
                    $this->session->set_flashdata('error', lang('no_product_found'));
                }
            }

            if ($this->input->get('subcategory')) {
                if ($products = $this->products_model->getSubCategoryProducts($this->input->get('subcategory'))) {
                    foreach ($products as $row) {
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }
                    $this->data['message'] = lang('products_added_to_list');
                } else {
                    $pr = array();
                    $this->session->set_flashdata('error', lang('no_product_found'));
                }
            }

            $this->data['items'] = isset($pr) ? json_encode($pr) : false;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('print_barcodes')));
            $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
            $this->page_construct('products/print_barcodes', $meta, $this->data);
        }
    }


    /* ------------------------------------------------------- */

    function add_old($id = NULL){
        $expire_karachi = $this->input->post('expire_karachi');
        $expire_price_karachi = $this->input->post('expire_price_karachi');
        $expire_lahore = $this->input->post('expire_lahore');
        $expire_price_lahore = $this->input->post('expire_price_lahore');
        $productcompanyname_asanbuy = $this->input->post('productcompanyname_asanbuy');
        $productcompanyprice_asanbuy = $this->input->post('productcompanyprice_asanbuy');
        $productcompanyname_daraz = $this->input->post('productcompanyname_daraz');
        $productcompanyprice_daraz = $this->input->post('productcompanyprice_daraz');
        $productcompanyname_olper_lhr = $this->input->post('productcompanyname_olper_lhr');
        $productcompanyprice_olper_lhr = $this->input->post('productcompanyprice_olper_lhr');
        $productcompanyname_distro = $this->input->post('productcompanyname_distro');
        $productcompanyprice_distro = $this->input->post('productcompanyprice_distro');
        $productcompanyname_hummart = $this->input->post('productcompanyname_hummart');
        $productcompanyprice_hummart = $this->input->post('productcompanyprice_hummart');
        $productcompanyname_mycart = $this->input->post('productcompanyname_mycart');
        $productcompanyprice_mycart = $this->input->post('productcompanyprice_mycart');
        $productcompanyname_mygerries = $this->input->post('productcompanyname_mygerries');
        $productcompanyprice_mygerries = $this->input->post('productcompanyprice_mygerries');
        $productcompanyname_qne = $this->input->post('productcompanyname_qne');
        $productcompanyprice_qne = $this->input->post('productcompanyprice_qne');
        $productcompanyname_areywow = $this->input->post('productcompanyname_areywow');
        $productcompanyprice_areywow = $this->input->post('productcompanyprice_areywow');
        $productcompanyname_telemart = $this->input->post('productcompanyname_telemart');
        $productcompanyprice_telemart = $this->input->post('productcompanyprice_telemart');
        $productcompanyname_ishopping = $this->input->post('productcompanyname_ishopping');
        $productcompanyprice_ishopping = $this->input->post('productcompanyprice_ishopping');
        $productcompanyname_yayvo = $this->input->post('productcompanyname_yayvo');
        $productcompanyprice_yayvo = $this->input->post('productcompanyprice_yayvo');
        $product_sku_company_list = array(
            $productcompanyname_asanbuy         =>      $productcompanyprice_asanbuy,
            $productcompanyname_daraz           =>      $productcompanyprice_daraz,
            $productcompanyname_olper_lhr       =>      $productcompanyprice_olper_lhr,
            $productcompanyname_distro          =>      $productcompanyprice_distro,
            $productcompanyname_hummart         =>      $productcompanyprice_hummart,
            $productcompanyname_mycart          =>      $productcompanyprice_mycart,
            $productcompanyname_mygerries       =>      $productcompanyprice_mygerries,
            $productcompanyname_qne             =>      $productcompanyprice_qne,
            $productcompanyname_areywow         =>      $productcompanyprice_areywow,
            $productcompanyname_telemart        =>      $productcompanyprice_telemart,
            $productcompanyname_ishopping       =>      $productcompanyprice_ishopping,
            $productcompanyname_yayvo           =>      $productcompanyprice_yayvo,
            $expire_karachi                     =>      $expire_price_karachi,
            $expire_lahore                      =>      $expire_price_lahore
        );

        // AsanBuy MRP Integration new added 24-Feb
        $mrp = $_POST['mrp'] *  $_POST['pack_size'];
        $asanBuy_pid = $_POST['productcompanyprice_asanbuy'];
        $total_tax = $this->input->post('total_tax');
        if ($total_tax != null) {
            $data = array(
                'name' => $this->input->post('name'),
                'code' => 'add_product_manually',
                'type' => '2',
                'rate' => $total_tax,
            );
            $this->settings_model->addTaxRate($data);
            $tax_id = $this->db->insert_id();
            $tax_method = '1';
        }
        else {
            $tax_id = $this->input->post('tax_rate');
            $tax_method = $this->input->post('tax_method');
        }
        $this->sma->checkPermissions();
        $this->load->helper('security');
        $warehouses = $this->site->getAllWarehouses();
        $this->form_validation->set_rules('category', lang("category"), 'required|is_natural_no_zero');
        if ($this->input->post('type') == 'standard') {
            $this->form_validation->set_rules('cost', lang("product_cost"), 'required');
            $this->form_validation->set_rules('unit', lang("product_unit"), 'required');
        }
        $this->form_validation->set_rules('code', lang("product_code"), 'is_unique[products.code]|alpha_dash');
        if (SHOP) {
            $this->form_validation->set_rules('slug', lang("slug"), 'required|is_unique[products.slug]|alpha_dash');
        }
        $this->form_validation->set_rules('weight', lang("weight"), 'numeric');
        $this->form_validation->set_rules('product_image', lang("product_image"), 'xss_clean');
        $this->form_validation->set_rules('digital_file', lang("digital_file"), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang("product_gallery_images"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if($this->input->post('cost') > $this->input->post('price')){
                $this->session->set_flashdata('error', 'Please check cost amount');
                admin_redirect("products/add");
            }
            else if($this->input->post('cost') > $this->input->post('mrp')){
                $this->session->set_flashdata('error', 'Please check cost amount');
                admin_redirect("products/add");
            }
            else if($this->input->post('price') > $this->input->post('mrp')){
                $this->session->set_flashdata('error', 'Please check MRP amount');
                admin_redirect("products/add");
            }
            else{

                $gid = $this->input->post('gid');
                $tax_rate = $this->input->post('tax_rate') ? $this->site->getTaxRateByID($this->input->post('tax_rate')) : NULL;
                $data = array (
                    'code' => $this->input->post('code'),
                    'barcode_symbology' => $this->input->post('barcode_symbology'),
                    'company_code' => $this->input->post('company_code'),
                    'name' => $this->input->post('name'),
                    'type' => $this->input->post('type'),
                    'brand' => $this->input->post('brand'),
                    'category_id' => $this->input->post('category'),
                    'subcategory_id' => $this->input->post('subcategory') ? $this->input->post('subcategory') : NULL,
                    'cost' => $this->sma->formatDecimal($this->input->post('cost')),
                    'price' => $this->sma->formatDecimal($this->input->post('price')),
                    'dropship' => $this->sma->formatDecimal($this->input->post('dropship')),
                    'crossdock' => $this->sma->formatDecimal($this->input->post('crossdock')),
                    'mrp' => $this->sma->formatDecimal($this->input->post('mrp')),
                    'discount_mrp' => $this->sma->formatDecimal($this->input->post('discount_mrp')),
                    'company_prices_and_names' => json_encode($product_sku_company_list),
                    'discount_one' => $this->sma->formatDecimal($this->input->post('discount_one')),
                    'discount_two' => $this->sma->formatDecimal($this->input->post('discount_two')),
                    'discount_three' => $this->sma->formatDecimal($this->input->post('discount_three')),
                    'username' => $this->input->post('username'),
                    'apikey' => $this->input->post('apikey'),
                    'sku_code' => $this->input->post('sku_code'),
                    'pack_size' => $this->input->post('pack_size'),
                    'carton_size' => $this->input->post('carton_size'),
                    'unit' => $this->input->post('unit'),
                    'sale_unit' => $this->input->post('default_sale_unit'),
                    'purchase_unit' => $this->input->post('default_purchase_unit'),
                    'tax_rate' => $tax_id,
                    'tax_method' => $tax_method,
                    'alert_quantity' => $this->input->post('alert_quantity'),
                    'track_quantity' => $this->input->post('track_quantity') ? $this->input->post('track_quantity') : '0',
                    'details' => $this->input->post('details'),
                    'product_details' => $this->input->post('product_details'),
                    'supplier1' => $this->input->post('supplier'),
                    'supplier1price' => $this->sma->formatDecimal($this->input->post('supplier_price')),
                    'supplier2' => $this->input->post('supplier_2'),
                    'supplier2price' => $this->sma->formatDecimal($this->input->post('supplier_2_price')),
                    'supplier3' => $this->input->post('supplier_3'),
                    'supplier3price' => $this->sma->formatDecimal($this->input->post('supplier_3_price')),
                    'supplier4' => $this->input->post('supplier_4'),
                    'supplier4price' => $this->sma->formatDecimal($this->input->post('supplier_4_price')),
                    'supplier5' => $this->input->post('supplier_5'),
                    'supplier5price' => $this->sma->formatDecimal($this->input->post('supplier_5_price')),
                    'cf1' => $this->input->post('cf1'),
                    'cf2' => $this->input->post('cf2'),
                    'cf3' => $this->input->post('cf3'),
                    'cf4' => $this->input->post('cf4'),
                    'cf5' => $this->input->post('cf5'),
                    'cf6' => $this->input->post('cf6'),
                    'promotion' => $this->input->post('promotion'),
                    'promo_price' => $this->sma->formatDecimal($this->input->post('promo_price')),
                    'start_date' => $this->input->post('start_date') ? $this->sma->fsd($this->input->post('start_date')) : NULL,
                    'end_date' => $this->input->post('end_date') ? $this->sma->fsd($this->input->post('end_date')) : NULL,
                    'supplier1_part_no' => $this->input->post('supplier_part_no'),
                    'supplier2_part_no' => $this->input->post('supplier_2_part_no'),
                    'supplier3_part_no' => $this->input->post('supplier_3_part_no'),
                    'supplier4_part_no' => $this->input->post('supplier_4_part_no'),
                    'supplier5_part_no' => $this->input->post('supplier_5_part_no'),
                    'file' => $this->input->post('file_link'),
                    'slug' => $this->input->post('slug'),
                    'weight' => $this->input->post('weight'),
                    'featured' => $this->input->post('featured'),
                    'hsn_code' => $this->input->post('hsn_code'),
                    'fed_tax' => $this->input->post('fed_tax'),
                    'status' => $this->input->post('product_status'),
                    'hide' => $this->input->post('hide') ? $this->input->post('hide') : 0,
                    'second_name' => $this->input->post('second_name'),
                    'short_expiry_duration' => $this->input->post('shotexpiry_days'),
                    'es_durration' => $this->input->post('es_duration'),
                    'hold_stock' => $this->input->post('hold_qty'),
                    'group_id' => $gid,
                    'adv_tax_reg' => $this->input->post('adv_tax_reg'),
                    'adv_tax_nonreg' => $this->input->post('adv_tax_nonreg'),
                    'adv_tax_for_purchase' => $this->input->post('adv_tax_for_purchase')
                );
                $warehouse_qty = NULL;
                $product_attributes = NULL;
                $this->load->library('upload');
                if ($this->input->post('type') == 'standard') {
                    $wh_total_quantity = 0;
                    $pv_total_quantity = 0;
                    for ($s = 2; $s > 5; $s++) {
                        $data['suppliers' . $s] = $this->input->post('supplier_' . $s);
                        $data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                    }
                    foreach ($warehouses as $warehouse) {
                        if ($this->input->post('wh_qty_' . $warehouse->id)) {
                            $warehouse_qty[] = array(
                                'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                                'quantity' => $this->input->post('wh_qty_' . $warehouse->id),
                                'rack' => $this->input->post('rack_' . $warehouse->id) ? $this->input->post('rack_' . $warehouse->id) : NULL
                            );
                            $wh_total_quantity += $this->input->post('wh_qty_' . $warehouse->id);
                        }
                    }
                    if ($this->input->post('attributes')) {
                        $a = sizeof($_POST['attr_name']);
                        for ($r = 0; $r <= $a; $r++) {
                            if (isset($_POST['attr_name'][$r])) {
                                $product_attributes[] = array(
                                    'name' => $_POST['attr_name'][$r],
                                    'warehouse_id' => $_POST['attr_warehouse'][$r],
                                    'quantity' => $_POST['attr_quantity'][$r],
                                    'price' => $_POST['attr_price'][$r],
                                );
                                $pv_total_quantity += $_POST['attr_quantity'][$r];
                            }
                        }
                    } else {
                        $product_attributes = NULL;
                    }
                    if ($wh_total_quantity != $pv_total_quantity && $pv_total_quantity != 0) {
                        $this->form_validation->set_rules('wh_pr_qty_issue', 'wh_pr_qty_issue', 'required');
                        $this->form_validation->set_message('required', lang('wh_pr_qty_issue'));
                    }
                }
                if ($this->input->post('type') == 'service') {
                    $data['track_quantity'] = 0;
                }
                else if($this->input->post('type') == 'combo') {
                    $total_price = 0;
                    $c = sizeof($_POST['combo_item_code']) - 1;
                    for ($r = 0; $r <= $c; $r++) {
                        if(isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                            $items[] = array(
                                'item_code' => $_POST['combo_item_code'][$r],
                                'quantity' => $_POST['combo_item_quantity'][$r],
                                'unit_price' => $_POST['combo_item_price'][$r],
                            );
                        }
                        $total_price += $_POST['combo_item_price'][$r] * $_POST['combo_item_quantity'][$r];
                    }
                    if ($this->sma->formatDecimal($total_price) != $this->sma->formatDecimal($this->input->post('price'))) {
                        $this->form_validation->set_rules('combo_price', 'combo_price', 'required');
                        $this->form_validation->set_message('required', lang('pprice_not_match_ciprice'));
                    }
                    $data['track_quantity'] = 0;
                }
                else if($this->input->post('type') == 'digital') {
                    if($_FILES['digital_file']['size'] > 0) {
                        $config['upload_path'] = $this->digital_upload_path;
                        $config['allowed_types'] = $this->digital_file_types;
                        $config['max_size'] = $this->allowed_file_size;
                        $config['overwrite'] = FALSE;
                        $config['encrypt_name'] = TRUE;
                        $config['max_filename'] = 25;
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('digital_file')) {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            admin_redirect("products/add");
                        }
                        $file = $this->upload->file_name;
                        $data['file'] = $file;
                    }
                    else{
                        if(!$this->input->post('file_link')) {
                            $this->form_validation->set_rules('digital_file', lang("digital_file"), 'required');
                        }
                    }
                    $config = NULL;
                    $data['track_quantity'] = 0;
                }
                if(!isset($items)) {
                    $items = NULL;
                }
                if($_FILES['product_image']['size'] > 0) {
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size'] = $this->allowed_file_size;
                    $config['max_width'] = $this->Settings->iwidth;
                    $config['max_height'] = $this->Settings->iheight;
                    $config['overwrite'] = FALSE;
                    $config['max_filename'] = 25;
                    $config['encrypt_name'] = TRUE;
                    $this->upload->initialize($config);
                    if(!$this->upload->do_upload('product_image')){
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect("products/add");
                    }
                    $photo = $this->upload->file_name;
                    $data['image'] = $photo;
                    $this->load->library('image_lib');
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $this->upload_path . $photo;
                    $config['new_image'] = $this->thumbs_path . $photo;
                    $config['maintain_ratio'] = TRUE;
                    $config['width'] = $this->Settings->twidth;
                    $config['height'] = $this->Settings->theight;
                    $this->image_lib->clear();
                    $this->image_lib->initialize($config);
                    if (!$this->image_lib->resize()) {
                        echo $this->image_lib->display_errors();
                    }
                    if ($this->Settings->watermark) {
                        $this->image_lib->clear();
                        $wm['source_image'] = $this->upload_path . $photo;
                        $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                        $wm['wm_type'] = 'text';
                        $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                        $wm['quality'] = '100';
                        $wm['wm_font_size'] = '16';
                        $wm['wm_font_color'] = '999999';
                        $wm['wm_shadow_color'] = 'CCCCCC';
                        $wm['wm_vrt_alignment'] = 'top';
                        $wm['wm_hor_alignment'] = 'left';
                        $wm['wm_padding'] = '10';
                        $this->image_lib->initialize($wm);
                        $this->image_lib->watermark();
                    }
                    $this->image_lib->clear();
                    $config = NULL;
                }
                if ($_FILES['userfile']['name'][0] != "") {
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size'] = $this->allowed_file_size;
                    $config['max_width'] = $this->Settings->iwidth;
                    $config['max_height'] = $this->Settings->iheight;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                    $files = $_FILES;
                    $cpt = count($_FILES['userfile']['name']);
                    for ($i = 0; $i < $cpt; $i++) {
                        $_FILES['userfile']['name'] = $files['userfile']['name'][$i];
                        $_FILES['userfile']['type'] = $files['userfile']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                        $_FILES['userfile']['error'] = $files['userfile']['error'][$i];
                        $_FILES['userfile']['size'] = $files['userfile']['size'][$i];
                        $this->upload->initialize($config);
                        if(!$this->upload->do_upload()){
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            admin_redirect("products/add");
                        }
                        else{
                            $pho = $this->upload->file_name;
                            $photos[] = $pho;
                            $this->load->library('image_lib');
                            $config['image_library'] = 'gd2';
                            $config['source_image'] = $this->upload_path . $pho;
                            $config['new_image'] = $this->thumbs_path . $pho;
                            $config['maintain_ratio'] = TRUE;
                            $config['width'] = $this->Settings->twidth;
                            $config['height'] = $this->Settings->theight;
                            $this->image_lib->initialize($config);
                            if (!$this->image_lib->resize()) {
                                echo $this->image_lib->display_errors();
                            }
                            if ($this->Settings->watermark) {
                                $this->image_lib->clear();
                                $wm['source_image'] = $this->upload_path . $pho;
                                $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                                $wm['wm_type'] = 'text';
                                $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                                $wm['quality'] = '100';
                                $wm['wm_font_size'] = '16';
                                $wm['wm_font_color'] = '999999';
                                $wm['wm_shadow_color'] = 'CCCCCC';
                                $wm['wm_vrt_alignment'] = 'top';
                                $wm['wm_hor_alignment'] = 'left';
                                $wm['wm_padding'] = '10';
                                $this->image_lib->initialize($wm);
                                $this->image_lib->watermark();
                            }
                            $this->image_lib->clear();
                        }
                    }
                    $config = NULL;
                } else {
                    $photos = NULL;
                }
                $data['quantity'] = 0;
            }
        }

        if ($this->form_validation->run() == true && $this->products_model->addProduct($data, $items, $warehouse_qty, $product_attributes, $photos)) {
            $this->session->set_flashdata('message', lang("product_added"));
            admin_redirect('products');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            


            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['pgroups'] = $this->site->getAllProductGroups();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['brands'] = $this->site->getAllBrands();
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->data['warehouses'] = $warehouses;
            $this->data['warehouses_products'] = $id ? $this->products_model->getAllWarehousesWithPQ($id) : NULL;
            $this->data['product'] = $id ? $this->products_model->getProductByID($id) : NULL;
            $this->data['variants'] = $this->products_model->getAllVariants();
            $this->data['combo_items'] = ($id && $this->data['product']->type == 'combo') ? $this->products_model->getProductComboItems($id) : NULL;
            $this->data['product_options'] = $id ? $this->products_model->getProductOptionsWithWH($id) : NULL;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_product')));
            $meta = array('page_title' => lang('add_product'), 'bc' => $bc);
            $this->page_construct('products/add', $meta, $this->data);
        }
    }

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1, 'rate' => $row->rate, 'cost' => $row->cost, 'crossdock' => $row->crossdock, 'dropship' => $row->dropship, 'mrp' => $row->mrp);
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function get_suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductsForPrinting($term);
        if ($rows) {
            foreach ($rows as $row) {
                $variants = $this->products_model->getProductOptions($row->id);
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1, 'variants' => $variants);
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function addByAjax()
    {
        if (!$this->mPermissions('add')) {
            exit(json_encode(array('msg' => lang('access_denied'))));
        }
        if ($this->input->get('token') && $this->input->get('token') == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product');
            if (!isset($product['code']) || empty($product['code'])) {
                exit(json_encode(array('msg' => lang('product_code_is_required'))));
            }
            if (!isset($product['name']) || empty($product['name'])) {
                exit(json_encode(array('msg' => lang('product_name_is_required'))));
            }
            if (!isset($product['category_id']) || empty($product['category_id'])) {
                exit(json_encode(array('msg' => lang('product_category_is_required'))));
            }
            if (!isset($product['unit']) || empty($product['unit'])) {
                exit(json_encode(array('msg' => lang('product_unit_is_required'))));
            }
            if (!isset($product['price']) || empty($product['price'])) {
                exit(json_encode(array('msg' => lang('product_price_is_required'))));
            }
            if (!isset($product['cost']) || empty($product['cost'])) {
                exit(json_encode(array('msg' => lang('product_cost_is_required'))));
            }
            if ($this->products_model->getProductByCode($product['code'])) {
                exit(json_encode(array('msg' => lang('product_code_already_exist'))));
            }
            if($this->input->post('cost') > $this->input->post('price')){
                exit(json_encode(array('msg' => lang('Please check cost amount'))));
            }
            else if($this->input->post('cost') > $this->input->post('mrp')){
                exit(json_encode(array('msg' => lang('Please check cost amount'))));
            }
            else if($this->input->post('price') > $this->input->post('mrp')){
                exit(json_encode(array('msg' => lang('Please check MRP amount'))));
            }
            if ($row = $this->products_model->addAjaxProduct($product)) {
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $pr = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0');
                $this->sma->send_json(array('msg' => 'success', 'result' => $pr));
            } else {
                exit(json_encode(array('msg' => lang('failed_to_add_product'))));
            }
        } else {
            json_encode(array('msg' => 'Invalid token'));
        }
    }

    /* -------------------------------------------------------- */

    function edit_old($id = NULL)
    {

        $expire_karachi = $this->input->post('expire_karachi');
        $expire_price_karachi = $this->input->post('expire_price_karachi');

        $expire_lahore = $this->input->post('expire_lahore');
        $expire_price_lahore = $this->input->post('expire_price_lahore');

        $productcompanyname_asanbuy = $this->input->post('productcompanyname_asanbuy');
        $productcompanyprice_asanbuy = $this->input->post('productcompanyprice_asanbuy');

        $productcompanyname_daraz = $this->input->post('productcompanyname_daraz');
        $productcompanyprice_daraz = $this->input->post('productcompanyprice_daraz');

        $productcompanyname_olper_lhr = $this->input->post('productcompanyname_olper_lhr');
        $productcompanyprice_olper_lhr = $this->input->post('productcompanyprice_olper_lhr');

        $productcompanyname_distro = $this->input->post('productcompanyname_distro');
        $productcompanyprice_distro = $this->input->post('productcompanyprice_distro');

        $productcompanyname_hummart = $this->input->post('productcompanyname_hummart');
        $productcompanyprice_hummart = $this->input->post('productcompanyprice_hummart');

        $productcompanyname_mycart = $this->input->post('productcompanyname_mycart');
        $productcompanyprice_mycart = $this->input->post('productcompanyprice_mycart');

        $productcompanyname_mygerries = $this->input->post('productcompanyname_mygerries');
        $productcompanyprice_mygerries = $this->input->post('productcompanyprice_mygerries');

        $productcompanyname_qne = $this->input->post('productcompanyname_qne');
        $productcompanyprice_qne = $this->input->post('productcompanyprice_qne');

        $productcompanyname_areywow = $this->input->post('productcompanyname_areywow');
        $productcompanyprice_areywow = $this->input->post('productcompanyprice_areywow');

        $productcompanyname_telemart = $this->input->post('productcompanyname_telemart');
        $productcompanyprice_telemart = $this->input->post('productcompanyprice_telemart');

        $productcompanyname_ishopping = $this->input->post('productcompanyname_ishopping');
        $productcompanyprice_ishopping = $this->input->post('productcompanyprice_ishopping');

        $productcompanyname_yayvo = $this->input->post('productcompanyname_yayvo');
        $productcompanyprice_yayvo = $this->input->post('productcompanyprice_yayvo');

        $product_sku_company_list = array(
            $productcompanyname_asanbuy         =>      $productcompanyprice_asanbuy,
            $productcompanyname_daraz           =>      $productcompanyprice_daraz,
            $productcompanyname_olper_lhr           =>  $productcompanyprice_olper_lhr,
            $productcompanyname_distro          =>      $productcompanyprice_distro,
            $productcompanyname_hummart         =>      $productcompanyprice_hummart,
            $productcompanyname_mycart          =>      $productcompanyprice_mycart,
            $productcompanyname_mygerries       =>      $productcompanyprice_mygerries,
            $productcompanyname_qne             =>      $productcompanyprice_qne,
            $productcompanyname_areywow         =>      $productcompanyprice_areywow,
            $productcompanyname_telemart        =>      $productcompanyprice_telemart,
            $productcompanyname_ishopping       =>      $productcompanyprice_ishopping,
            $productcompanyname_yayvo           =>      $productcompanyprice_yayvo,
            $expire_karachi           =>      $expire_price_karachi,
            $expire_lahore => $expire_price_lahore
        );

        // AsanBuy MRP Integration
        // $mrp = $_POST['mrp'] *  $_POST['pack_size'];
        // $asanBuy_pid = $_POST['productcompanyprice_asanbuy'];

        //die;

        $total_tax = $this->input->post('total_tax');
        if ($total_tax != null) {
            $data = array(
                'name' => $this->input->post('name'),
                'code' => 'add_product_manually',
                'type' => '2',
                'rate' => $total_tax,
            );
            $this->settings_model->addTaxRate($data);
            $tax_id = $this->db->insert_id();
            $tax_method = '1';
        } else {
            $tax_id = $this->input->post('tax_rate');
            $tax_method = $this->input->post('tax_method');
        }

        $this->sma->checkPermissions();
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $warehouses = $this->site->getAllWarehouses();
        $warehouses_products = $this->products_model->getAllWarehousesWithPQ($id);
        $product = $this->site->getProductByID($id);
        if (!$id || !$product) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->form_validation->set_rules('category', lang("category"), 'required|is_natural_no_zero');
        if ($this->input->post('type') == 'standard') {
            $this->form_validation->set_rules('cost', lang("product_cost"), 'required');
            $this->form_validation->set_rules('unit', lang("product_unit"), 'required');
        }
        $this->form_validation->set_rules('code', lang("product_code"), 'alpha_dash');
        if ($this->input->post('code') !== $product->code) {
            $this->form_validation->set_rules('code', lang("product_code"), 'is_unique[products.code]');
        }
        if (SHOP) {
            $this->form_validation->set_rules('slug', lang("slug"), 'required|alpha_dash');
            if ($this->input->post('slug') !== $product->slug) {
                $this->form_validation->set_rules('slug', lang("slug"), 'required|is_unique[products.slug]|alpha_dash');
            }
        }
        $this->form_validation->set_rules('weight', lang("weight"), 'numeric');
        $this->form_validation->set_rules('product_image', lang("product_image"), 'xss_clean');
        $this->form_validation->set_rules('digital_file', lang("digital_file"), 'xss_clean');
        $this->form_validation->set_rules('userfile', lang("product_gallery_images"), 'xss_clean');

        if ($this->form_validation->run('products/add') == true) {

            if($this->input->post('cost') > $this->input->post('price')){
                $this->session->set_flashdata('error', 'Please check cost amount');
                redirect($_SERVER["HTTP_REFERER"]);
            }
            else if($this->input->post('cost') > $this->input->post('mrp')){
                $this->session->set_flashdata('error', 'Please check cost amount');
                redirect($_SERVER["HTTP_REFERER"]);
            }
            else if($this->input->post('price') > $this->input->post('mrp')){
                $this->session->set_flashdata('error', 'Please check MRP amount');
                redirect($_SERVER["HTTP_REFERER"]);
            }
            else{
                $gid = $this->input->post('gid');
                $data = array(
    
    
                    'code' => $this->input->post('code'),
                    'barcode_symbology' => $this->input->post('barcode_symbology'),
    
                    'company_code' => $this->input->post('company_code'),
    
                    'name' => $this->input->post('name'),
                    'type' => $this->input->post('type'),
                    'brand' => $this->input->post('brand'),
                    'category_id' => $this->input->post('category'),
                    'subcategory_id' => $this->input->post('subcategory') ? $this->input->post('subcategory') : NULL,
                    'cost' => $this->sma->formatDecimal($this->input->post('cost')),
                    'price' => $this->sma->formatDecimal($this->input->post('price')),
    
                    'dropship' => $this->sma->formatDecimal($this->input->post('dropship')),
                    'crossdock' => $this->sma->formatDecimal($this->input->post('crossdock')),
                    'mrp' => $this->sma->formatDecimal($this->input->post('mrp')),
                    'discount_mrp' => $this->sma->formatDecimal($this->input->post('discount_mrp')),
    
                    'company_prices_and_names' => json_encode($product_sku_company_list),
    
                    'discount_one' => $this->sma->formatDecimal($this->input->post('discount_one')),
                    'discount_two' => $this->sma->formatDecimal($this->input->post('discount_two')),
                    'discount_three' => $this->sma->formatDecimal($this->input->post('discount_three')),
    
                    'username' => $this->input->post('username'),
                    'apikey' => $this->input->post('apikey'),
                    'sku_code' => $this->input->post('sku_code'),
                    'pack_size' => $this->input->post('pack_size'),
                    'carton_size' => $this->input->post('carton_size'),
    
                    'unit' => $this->input->post('unit'),
                    'sale_unit' => $this->input->post('default_sale_unit'),
                    'purchase_unit' => $this->input->post('default_purchase_unit'),
                    'tax_rate' => $tax_id,
                    'tax_method' => $tax_method,
                    'alert_quantity' => $this->input->post('alert_quantity'),
                    'track_quantity' => $this->input->post('track_quantity') ? $this->input->post('track_quantity') : '0',
                    'details' => $this->input->post('details'),
                    'product_details' => $this->input->post('product_details'),
                    'supplier1' => $this->input->post('supplier'),
                    'supplier1price' => $this->sma->formatDecimal($this->input->post('supplier_price')),
                    'supplier2' => $this->input->post('supplier_2'),
                    'supplier2price' => $this->sma->formatDecimal($this->input->post('supplier_2_price')),
                    'supplier3' => $this->input->post('supplier_3'),
                    'supplier3price' => $this->sma->formatDecimal($this->input->post('supplier_3_price')),
                    'supplier4' => $this->input->post('supplier_4'),
                    'supplier4price' => $this->sma->formatDecimal($this->input->post('supplier_4_price')),
                    'supplier5' => $this->input->post('supplier_5'),
                    'supplier5price' => $this->sma->formatDecimal($this->input->post('supplier_5_price')),
                    'cf1' => $this->input->post('cf1'),
                    'cf2' => $this->input->post('cf2'),
                    'cf3' => $this->input->post('cf3'),
                    'cf4' => $this->input->post('cf4'),
                    'cf5' => $this->input->post('cf5'),
                    'cf6' => $this->input->post('cf6'),
                    'promotion' => $this->input->post('promotion'),
                    'promo_price' => $this->sma->formatDecimal($this->input->post('promo_price')),
                    'start_date' => $this->input->post('start_date') ? $this->sma->fsd($this->input->post('start_date')) : NULL,
                    'end_date' => $this->input->post('end_date') ? $this->sma->fsd($this->input->post('end_date')) : NULL,
                    'supplier1_part_no' => $this->input->post('supplier_part_no'),
                    'supplier2_part_no' => $this->input->post('supplier_2_part_no'),
                    'supplier3_part_no' => $this->input->post('supplier_3_part_no'),
                    'supplier4_part_no' => $this->input->post('supplier_4_part_no'),
                    'supplier5_part_no' => $this->input->post('supplier_5_part_no'),
                    'file' => $this->input->post('file_link'),
                    'slug' => $this->input->post('slug'),
                    'weight' => $this->input->post('weight'),
                    'featured' => $this->input->post('featured'),
                    'hsn_code' => $this->input->post('hsn_code'),
                    'fed_tax' => $this->input->post('fed_tax'),
                    'status' => $this->input->post('product_status'),
                    'hide' => $this->input->post('hide') ? $this->input->post('hide') : 0,
                    'second_name' => $this->input->post('second_name'),
                    'hold_stock' => $this->input->post('hold_qty'),
                    'es_durration' => $this->input->post('es_duration'),
                    'short_expiry_duration' => $this->input->post('shotexpiry_days'),
                    'group_id' => $gid,
                    'adv_tax_reg' => $this->input->post('adv_tax_reg'),
                    'adv_tax_nonreg' => $this->input->post('adv_tax_nonreg'),
                    'adv_tax_for_purchase' => $this->input->post('adv_tax_for_purchase')
                );
                $warehouse_qty = NULL;
                $product_attributes = NULL;
                $update_variants = array();
                $this->load->library('upload');
                if ($this->input->post('type') == 'standard') {
                    if ($product_variants = $this->products_model->getProductOptions($id)) {
                        foreach ($product_variants as $pv) {
                            $update_variants[] = array(
                                'id' => $this->input->post('variant_id_' . $pv->id),
                                'name' => $this->input->post('variant_name_' . $pv->id),
                                'cost' => $this->input->post('variant_cost_' . $pv->id),
                                'price' => $this->input->post('variant_price_' . $pv->id),
                            );
                        }
                    }
                    for ($s = 2; $s > 5; $s++) {
                        $data['suppliers' . $s] = $this->input->post('supplier_' . $s);
                        $data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                    }
                    foreach ($warehouses as $warehouse) {
                        $warehouse_qty[] = array(
                            'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                            'rack' => $this->input->post('rack_' . $warehouse->id) ? $this->input->post('rack_' . $warehouse->id) : NULL
                        );
                    }
    
                    if ($this->input->post('attributes')) {
                        $a = sizeof($_POST['attr_name']);
                        for ($r = 0; $r <= $a; $r++) {
                            if (isset($_POST['attr_name'][$r])) {
                                if ($product_variatnt = $this->products_model->getPrductVariantByPIDandName($id, trim($_POST['attr_name'][$r]))) {
                                    $this->form_validation->set_message('required', lang("product_already_has_variant") . ' (' . $_POST['attr_name'][$r] . ')');
                                    $this->form_validation->set_rules('new_product_variant', lang("new_product_variant"), 'required');
                                } else {
                                    $product_attributes[] = array(
                                        'name' => $_POST['attr_name'][$r],
                                        'warehouse_id' => $_POST['attr_warehouse'][$r],
                                        'quantity' => $_POST['attr_quantity'][$r],
                                        'price' => $_POST['attr_price'][$r],
                                    );
                                }
                            }
                        }
                    } else {
                        $product_attributes = NULL;
                    }
                }
    
                if ($this->input->post('type') == 'service') {
                    $data['track_quantity'] = 0;
                } elseif ($this->input->post('type') == 'combo') {
                    $total_price = 0;
                    $c = sizeof($_POST['combo_item_code']) - 1;
                    for ($r = 0; $r <= $c; $r++) {
                        if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                            $items[] = array(
                                'item_code' => $_POST['combo_item_code'][$r],
                                'quantity' => $_POST['combo_item_quantity'][$r],
                                'unit_price' => $_POST['combo_item_price'][$r],
                            );
                        }
                        $total_price += $_POST['combo_item_price'][$r] * $_POST['combo_item_quantity'][$r];
                    }
                    if ($this->sma->formatDecimal($total_price) != $this->sma->formatDecimal($this->input->post('price'))) {
                        $this->form_validation->set_rules('combo_price', 'combo_price', 'required');
                        $this->form_validation->set_message('required', lang('pprice_not_match_ciprice'));
                    }
                    $data['track_quantity'] = 0;
                } elseif ($this->input->post('type') == 'digital') {
                    if ($this->input->post('file_link')) {
                        $data['file'] = $this->input->post('file_link');
                    }
                    if ($_FILES['digital_file']['size'] > 0) {
                        $config['upload_path'] = $this->digital_upload_path;
                        $config['allowed_types'] = $this->digital_file_types;
                        $config['max_size'] = $this->allowed_file_size;
                        $config['overwrite'] = FALSE;
                        $config['encrypt_name'] = TRUE;
                        $config['max_filename'] = 25;
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('digital_file')) {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            admin_redirect("products/add");
                        }
                        $file = $this->upload->file_name;
                        $data['file'] = $file;
                    }
                    $config = NULL;
                    $data['track_quantity'] = 0;
                }
                if (!isset($items)) {
                    $items = NULL;
                }
                if ($_FILES['product_image']['size'] > 0) {
    
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size'] = $this->allowed_file_size;
                    $config['max_width'] = $this->Settings->iwidth;
                    $config['max_height'] = $this->Settings->iheight;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('product_image')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect("products/edit/" . $id);
                    }
                    $photo = $this->upload->file_name;
                    $data['image'] = $photo;
                    $this->load->library('image_lib');
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $this->upload_path . $photo;
                    $config['new_image'] = $this->thumbs_path . $photo;
                    $config['maintain_ratio'] = TRUE;
                    $config['width'] = $this->Settings->twidth;
                    $config['height'] = $this->Settings->theight;
                    $this->image_lib->clear();
                    $this->image_lib->initialize($config);
                    if (!$this->image_lib->resize()) {
                        echo $this->image_lib->display_errors();
                    }
                    if ($this->Settings->watermark) {
                        $this->image_lib->clear();
                        $wm['source_image'] = $this->upload_path . $photo;
                        $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                        $wm['wm_type'] = 'text';
                        $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                        $wm['quality'] = '100';
                        $wm['wm_font_size'] = '16';
                        $wm['wm_font_color'] = '999999';
                        $wm['wm_shadow_color'] = 'CCCCCC';
                        $wm['wm_vrt_alignment'] = 'top';
                        $wm['wm_hor_alignment'] = 'left';
                        $wm['wm_padding'] = '10';
                        $this->image_lib->initialize($wm);
                        $this->image_lib->watermark();
                    }
                    $this->image_lib->clear();
                    $config = NULL;
                }
    
                if ($_FILES['userfile']['name'][0] != "") {
    
                    $config['upload_path'] = $this->upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['max_size'] = $this->allowed_file_size;
                    $config['max_width'] = $this->Settings->iwidth;
                    $config['max_height'] = $this->Settings->iheight;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                    $files = $_FILES;
                    $cpt = count($_FILES['userfile']['name']);
                    for ($i = 0; $i < $cpt; $i++) {
    
                        $_FILES['userfile']['name'] = $files['userfile']['name'][$i];
                        $_FILES['userfile']['type'] = $files['userfile']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                        $_FILES['userfile']['error'] = $files['userfile']['error'][$i];
                        $_FILES['userfile']['size'] = $files['userfile']['size'][$i];
    
                        $this->upload->initialize($config);
    
                        if (!$this->upload->do_upload()) {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            admin_redirect("products/edit/" . $id);
                        } else {
    
                            $pho = $this->upload->file_name;
    
                            $photos[] = $pho;
    
                            $this->load->library('image_lib');
                            $config['image_library'] = 'gd2';
                            $config['source_image'] = $this->upload_path . $pho;
                            $config['new_image'] = $this->thumbs_path . $pho;
                            $config['maintain_ratio'] = TRUE;
                            $config['width'] = $this->Settings->twidth;
                            $config['height'] = $this->Settings->theight;
    
                            $this->image_lib->initialize($config);
    
                            if (!$this->image_lib->resize()) {
                                echo $this->image_lib->display_errors();
                            }
    
                            if ($this->Settings->watermark) {
                                $this->image_lib->clear();
                                $wm['source_image'] = $this->upload_path . $pho;
                                $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                                $wm['wm_type'] = 'text';
                                $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                                $wm['quality'] = '100';
                                $wm['wm_font_size'] = '16';
                                $wm['wm_font_color'] = '999999';
                                $wm['wm_shadow_color'] = 'CCCCCC';
                                $wm['wm_vrt_alignment'] = 'top';
                                $wm['wm_hor_alignment'] = 'left';
                                $wm['wm_padding'] = '10';
                                $this->image_lib->initialize($wm);
                                $this->image_lib->watermark();
                            }
    
                            $this->image_lib->clear();
                        }
                    }
                    $config = NULL;
                } else {
                    $photos = NULL;
                }
                // $data['quantity'] = isset($wh_total_quantity) ? $wh_total_quantity : 0;
                // $this->sma->print_arrays($data, $warehouse_qty, $update_variants, $product_attributes, $photos, $items);
                
            }
        }

        if ($this->form_validation->run() == true && $this->products_model->updateProduct($id, $data, $items, $warehouse_qty, $product_attributes, $photos, $update_variants)) {
            $this->load->model('admin/stores_model');
            $this->stores_model->UpdatePrice($id);
            $this->session->set_flashdata('message', lang("product_updated"));
            admin_redirect('products');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['pgroups'] = $this->site->getAllProductGroups();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['brands'] = $this->site->getAllBrands();
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->data['warehouses'] = $warehouses;
            $this->data['warehouses_products'] = $warehouses_products;
            $this->data['product'] = $product;
            $this->data['variants'] = $this->products_model->getAllVariants();
            $this->data['subunits'] = $this->site->getUnitsByBUID($product->unit);
            $this->data['product_variants'] = $this->products_model->getProductOptions($id);
            $this->data['combo_items'] = $product->type == 'combo' ? $this->products_model->getProductComboItems($product->id) : NULL;
            $this->data['product_options'] = $id ? $this->products_model->getProductOptionsWithWH($id) : NULL;


            // $this->sma->print_arrays($this->data);
            // exit;
            // die;


            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('edit_product')));
            $meta = array('page_title' => lang('edit_product'), 'bc' => $bc);
            $this->page_construct('products/edit', $meta, $this->data);
        }
    }

    /* ---------------------------------------------------------------- */

    function import_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("products/import_csv");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $updated = 0;
                $items = array();
                foreach ($arrResult as $key => $value) {
                    $item = [
                        'name'              => isset($value[0]) ? trim($value[0]) : '',
                        'code'              => isset($value[1]) ? trim($value[1]) : '',
                        'barcode_symbology' => isset($value[2]) ? mb_strtolower(trim($value[2]), 'UTF-8') : '',
                        'brand'             => isset($value[3]) ? trim($value[3]) : '',
                        'category_code'     => isset($value[4]) ? trim($value[4]) : '',
                        'unit'              => isset($value[5]) ? trim($value[5]) : '',
                        'sale_unit'         => isset($value[6]) ? trim($value[6]) : '',
                        'purchase_unit'     => isset($value[7]) ? trim($value[7]) : '',
                        'cost'              => isset($value[8]) ? trim($value[8]) : '',
                        'price'             => isset($value[9]) ? trim($value[9]) : '',
                        'alert_quantity'    => isset($value[10]) ? trim($value[10]) : '',
                        'tax_rate'          => isset($value[11]) ? trim($value[11]) : '',
                        'tax_method'        => isset($value[12]) ? (trim($value[12]) == 'exclusive' ? 1 : 0) : '',
                        'image'             => isset($value[13]) ? trim($value[13]) : '',
                        'subcategory_code'  => isset($value[14]) ? trim($value[14]) : '',
                        'variants'          => isset($value[15]) ? trim($value[15]) : '',
                        'cf1'               => isset($value[16]) ? trim($value[16]) : '',
                        'cf2'               => isset($value[17]) ? trim($value[17]) : '',
                        'cf3'               => isset($value[18]) ? trim($value[18]) : '',
                        'cf4'               => isset($value[19]) ? trim($value[19]) : '',
                        'cf5'               => isset($value[20]) ? trim($value[20]) : '',
                        'cf6'               => isset($value[21]) ? trim($value[21]) : '',
                        'hsn_code'          => isset($value[22]) ? trim($value[22]) : '',
                        'fed_tax'           => isset($value[23]) ? trim($value[23]) : '',
                        'second_name'       => isset($value[24]) ? trim($value[24]) : '',
                    ];

                    if ($catd = $this->products_model->getCategoryByCode($item['category_code'])) {
                        $tax_details = $this->products_model->getTaxRateByName($item['tax_rate']);
                        $prsubcat = $this->products_model->getCategoryByCode($item['subcategory_code']);
                        $brand = $this->products_model->getBrandByName($item['brand']);
                        $unit = $this->products_model->getUnitByCode($item['unit']);
                        $base_unit = $unit ? $unit->id : NULL;
                        $sale_unit = $base_unit;
                        $purcahse_unit = $base_unit;
                        if ($base_unit) {
                            $units = $this->site->getUnitsByBUID($base_unit);
                            foreach ($units as $u) {
                                if ($u->code == $item['sale_unit']) {
                                    $sale_unit = $u->id;
                                }
                                if ($u->code == $item['purchase_unit']) {
                                    $purcahse_unit = $u->id;
                                }
                            }
                        } else {
                            $this->session->set_flashdata('error', lang("check_unit") . " (" . $item['unit'] . "). " . lang("unit_code_x_exist") . " " . lang("line_no") . " " . ($key + 1));
                            admin_redirect("products/import_csv");
                        }

                        unset($item['category_code'], $item['subcategory_code']);
                        $item['unit'] = $base_unit;
                        $item['sale_unit'] = $sale_unit;
                        $item['category_id'] = $catd->id;
                        $item['purchase_unit'] = $purcahse_unit;
                        $item['brand'] = $brand ? $brand->id : NULL;
                        $item['tax_rate'] = $tax_details ? $tax_details->id : NULL;
                        $item['subcategory_id'] = $prsubcat ? $prsubcat->id : NULL;

                        if ($product = $this->products_model->getProductByCode($item['code'])) {
                            if ($product->type == 'standard') {
                                if ($item['variants']) {
                                    $vs = explode('|', $item['variants']);
                                    foreach ($vs as $v) {
                                        $variants[] = ['product_id' => $product->id, 'name' => trim($v)];
                                    }
                                }
                                unset($item['variants']);
                                if ($this->products_model->updateProduct($product->id, $item, null, null, null, null, $variants)) {
                                    $updated++;
                                }
                            }
                            $item = false;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang("check_category_code") . " (" . $item['category_code'] . "). " . lang("category_code_x_exist") . " " . lang("line_no") . " " . ($key + 1));
                        admin_redirect("products/import_csv");
                    }

                    if ($item) {
                        $items[] = $item;
                    }
                }
            }

            // $this->sma->print_arrays($items);
        }

        if ($this->form_validation->run() == true && !empty($items)) {
            if ($this->products_model->add_products($items)) {
                $updated = $updated ? '<p>' . sprintf(lang("products_updated"), $updated) . '</pre>' : '';
                $this->session->set_flashdata('message', sprintf(lang("products_added"), count($items)) . $updated);
                admin_redirect('products');
            }
        } else {
            if (isset($items) && empty($items)) {
                if ($updated) {
                    $this->session->set_flashdata('message', sprintf(lang("products_updated"), $updated));
                    admin_redirect('products');
                } else {
                    $this->session->set_flashdata('warning', lang('csv_issue'));
                }
                admin_redirect('products/import_csv');
            }

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['userfile'] = array(
                'name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('import_products_by_csv')));
            $meta = array('page_title' => lang('import_products_by_csv'), 'bc' => $bc);
            $this->page_construct('products/import_csv', $meta, $this->data);
        }
    }

    /* ------------------------------------------------------------------ */

    function update_price()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('message', lang("disabled_in_demo"));
                admin_redirect('welcome');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("products");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'price');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if (!$this->products_model->getProductByCode(trim($csv_pr['code']))) {
                        $this->session->set_flashdata('message', lang("check_product_code") . " (" . $csv_pr['code'] . "). " . lang("code_x_exist") . " " . lang("line_no") . " " . $rw);
                        admin_redirect("products");
                    }
                    $rw++;
                }
            }
        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("system_settings/group_product_prices/" . $group_id);
        }

        if ($this->form_validation->run() == true && !empty($final)) {
            $this->products_model->updatePrice($final);
            $this->session->set_flashdata('message', lang("price_updated"));
            admin_redirect('products');
        } else {

            $this->data['userfile'] = array(
                'name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'products/update_price', $this->data);
        }
    }

    /* ------------------------------------------------------------------------------- */

    function delete_old($id = NULL)
    {
        $this->sma->checkPermissions(NULL, TRUE);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->products_model->deleteProduct($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("product_deleted")));
            }
            $this->session->set_flashdata('message', lang('product_deleted'));
            admin_redirect('welcome');
        }
    }

    /* ----------------------------------------------------------------------------- */

    function quantity_adjustments($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('adjustments');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('quantity_adjustments')));
        $meta = array('page_title' => lang('quantity_adjustments'), 'bc' => $bc);
        $this->page_construct('products/quantity_adjustments', $meta, $this->data);
    }

    function getadjustments($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('adjustments');

        $delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line("delete_adjustment") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('products/delete_adjustment/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('adjustments')}.id as id, date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, attachment")
            ->from('adjustments')
            ->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
            ->join('users', 'users.id=adjustments.created_by', 'left')
            ->group_by("adjustments.id");
        if ($warehouse_id) {
            $this->datatables->where('adjustments.warehouse_id', $warehouse_id);
        }
        $this->datatables->add_column("Actions", "<div class='text-center'><a href='" . admin_url('products/edit_adjustment/$1') . "' class='tip' title='" . lang("edit_adjustment") . "'><i class='fa fa-edit'></i></a> " . $delete_link . "</div>", "id");

        echo $this->datatables->generate();
    }

    public function view_adjustment($id)
    {
        $this->sma->checkPermissions('adjustments', TRUE);

        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        $this->data['inv'] = $adjustment;
        $this->data['rows'] = $this->products_model->getAdjustmentItems($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($adjustment->warehouse_id);
        $this->load->view($this->theme . 'products/view_adjustment', $this->data);
    }

    function add_adjustment($count_id = NULL)
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');

        if ($this->form_validation->run() == true) {

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {

                $product_id = $_POST['product_id'][$r];
                $type = $_POST['type'][$r];
                $quantity = $_POST['quantity'][$r];
                $serial = $_POST['serial'][$r];
                $variant = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : NULL;

                if (!$this->Settings->overselling && $type == 'subtraction' && !$count_id) {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER["HTTP_REFERER"]);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                }

                $products[] = array(
                    'product_id' => $product_id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'warehouse_id' => $warehouse_id,
                    'option_id' => $variant,
                    'serial_no' => $serial,
                );
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("products"), 'required');
            } else {
                krsort($products);
            }

            $data = array(
                'date' => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'created_by' => $this->session->userdata('user_id'),
                'count_id' => $this->input->post('count_id') ? $this->input->post('count_id') : NULL,
            );

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);

        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment($data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang("quantity_adjusted"));
            admin_redirect('products/quantity_adjustments');
        } else {

            if ($count_id) {
                $stock_count = $this->products_model->getStouckCountByID($count_id);
                $items = $this->products_model->getStockCountItems($count_id);
                foreach ($items as $item) {
                    $c = sha1(uniqid(mt_rand(), true));
                    if ($item->counted != $item->expected) {
                        $product = $this->site->getProductByID($item->product_id);
                        $row = json_decode('{}');
                        $row->id = $item->product_id;
                        $row->code = $product->code;
                        $row->name = $product->name;
                        $row->qty = $item->counted - $item->expected;
                        $row->type = $row->qty > 0 ? 'addition' : 'subtraction';
                        $row->qty = $row->qty > 0 ? $row->qty : (0 - $row->qty);
                        $options = $this->products_model->getProductOptions($product->id);
                        $row->option = $item->product_variant_id ? $item->product_variant_id : 0;
                        $row->serial = '';
                        $ri = $this->Settings->item_addition ? $product->id : $c;

                        $pr[$ri] = array(
                            'id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                            'row' => $row, 'options' => $options
                        );
                        $c++;
                    }
                }
            }
            $this->data['adjustment_items'] = $count_id ? json_encode($pr) : FALSE;
            $this->data['warehouse_id'] = $count_id ? $stock_count->warehouse_id : FALSE;
            $this->data['count_id'] = $count_id;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_adjustment')));
            $meta = array('page_title' => lang('add_adjustment'), 'bc' => $bc);
            $this->page_construct('products/add_adjustment', $meta, $this->data);
        }
    }

    function edit_adjustment($id)
    {
        $this->sma->checkPermissions('adjustments', true);
        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');

        if ($this->form_validation->run() == true) {

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = $adjustment->date;
            }

            $reference_no = $this->input->post('reference_no');
            $warehouse_id = $this->input->post('warehouse');
            $note = $this->sma->clear_tags($this->input->post('note'));

            $i = isset($_POST['product_id']) ? sizeof($_POST['product_id']) : 0;
            for ($r = 0; $r < $i; $r++) {

                $product_id = $_POST['product_id'][$r];
                $type = $_POST['type'][$r];
                $quantity = $_POST['quantity'][$r];
                $serial = $_POST['serial'][$r];
                $variant = isset($_POST['variant'][$r]) && !empty($_POST['variant'][$r]) ? $_POST['variant'][$r] : null;

                if (!$this->Settings->overselling && $type == 'subtraction') {
                    if ($variant) {
                        if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                            if ($op_wh_qty->quantity < $quantity) {
                                $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                                redirect($_SERVER["HTTP_REFERER"]);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage'));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    }
                    if ($wh_qty = $this->products_model->getProductQuantity($product_id, $warehouse_id)) {
                        if ($wh_qty['quantity'] < $quantity) {
                            $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage'));
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                }

                $products[] = array(
                    'product_id' => $product_id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'warehouse_id' => $warehouse_id,
                    'option_id' => $variant,
                    'serial_no' => $serial,
                );
            }

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("products"), 'required');
            } else {
                krsort($products);
            }

            $data = array(
                'date' => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'created_by' => $this->session->userdata('user_id')
            );

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            // $this->sma->print_arrays($data, $products);

        }

        if ($this->form_validation->run() == true && $this->products_model->updateAdjustment($id, $data, $products)) {
            $this->session->set_userdata('remove_qals', 1);
            $this->session->set_flashdata('message', lang("quantity_adjusted"));
            admin_redirect('products/quantity_adjustments');
        } else {

            $inv_items = $this->products_model->getAdjustmentItems($id);
            // krsort($inv_items);
            foreach ($inv_items as $item) {
                $c = sha1(uniqid(mt_rand(), true));
                $product = $this->site->getProductByID($item->product_id);
                $row = json_decode('{}');
                $row->id = $item->product_id;
                $row->code = $product->code;
                $row->name = $product->name;
                $row->qty = $item->quantity;
                $row->type = $item->type;
                $options = $this->products_model->getProductOptions($product->id);
                $row->option = $item->option_id ? $item->option_id : 0;
                $row->serial = $item->serial_no ? $item->serial_no : '';
                $ri = $this->Settings->item_addition ? $product->id : $c;

                $pr[$ri] = array(
                    'id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'options' => $options
                );
                $c++;
            }

            $this->data['adjustment'] = $adjustment;
            $this->data['adjustment_items'] = json_encode($pr);
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('edit_adjustment')));
            $meta = array('page_title' => lang('edit_adjustment'), 'bc' => $bc);
            $this->page_construct('products/edit_adjustment', $meta, $this->data);
        }
    }

    function add_adjustment_by_csv()
    {
        $this->sma->checkPermissions('adjustments', true);
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');

        if ($this->form_validation->run() == true) {

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

            $reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
            $warehouse_id = $this->input->post('warehouse');
            $note = $this->sma->clear_tags($this->input->post('note'));
            $data = array(
                'date' => $date,
                'reference_no' => $reference_no,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'created_by' => $this->session->userdata('user_id'),
                'count_id' => NULL,
            );

            if ($_FILES['csv_file']['size'] > 0) {

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $csv = $this->upload->file_name;
                $data['attachment'] = $csv;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('code', 'quantity', 'variant');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                // $this->sma->print_arrays($final);
                $rw = 2;
                foreach ($final as $pr) {
                    if ($product = $this->products_model->getProductByCode(trim($pr['code']))) {
                        $csv_variant = trim($pr['variant']);
                        $variant = !empty($csv_variant) ? $this->products_model->getProductVariantID($product->id, $csv_variant) : FALSE;

                        $csv_quantity = trim($pr['quantity']);
                        $type = $csv_quantity > 0 ? 'addition' : 'subtraction';
                        $quantity = $csv_quantity > 0 ? $csv_quantity : (0 - $csv_quantity);

                        if (!$this->Settings->overselling && $type == 'subtraction') {
                            if ($variant) {
                                if ($op_wh_qty = $this->products_model->getProductWarehouseOptionQty($variant, $warehouse_id)) {
                                    if ($op_wh_qty->quantity < $quantity) {
                                        $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                        redirect($_SERVER["HTTP_REFERER"]);
                                    }
                                } else {
                                    $this->session->set_flashdata('error', lang('warehouse_option_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            }
                            if ($wh_qty = $this->products_model->getProductQuantity($product->id, $warehouse_id)) {
                                if ($wh_qty['quantity'] < $quantity) {
                                    $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            } else {
                                $this->session->set_flashdata('error', lang('warehouse_qty_is_less_than_damage') . ' - ' . lang('line_no') . ' ' . $rw);
                                redirect($_SERVER["HTTP_REFERER"]);
                            }
                        }

                        $products[] = array(
                            'product_id' => $product->id,
                            'type' => $type,
                            'quantity' => $quantity,
                            'warehouse_id' => $warehouse_id,
                            'option_id' => $variant,
                        );
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                    $rw++;
                }
            } else {
                $this->form_validation->set_rules('csv_file', lang("upload_file"), 'required');
            }

            // $this->sma->print_arrays($data, $products);

        }

        if ($this->form_validation->run() == true && $this->products_model->addAdjustment($data, $products)) {
            $this->session->set_flashdata('message', lang("quantity_adjusted"));
            admin_redirect('products/quantity_adjustments');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_adjustment')));
            $meta = array('page_title' => lang('add_adjustment_by_csv'), 'bc' => $bc);
            $this->page_construct('products/add_adjustment_by_csv', $meta, $this->data);
        }
    }

    function delete_adjustment($id = NULL)
    {
        $this->sma->checkPermissions('delete', TRUE);

        if ($this->products_model->deleteAdjustment($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("adjustment_deleted")));
        }
    }

    /* --------------------------------------------------------------------------------------------- */

    function modal_view($id = NULL)
    {

        $user = $this->site->getUser();

        $this->sma->checkPermissions('index', TRUE);

        $pr_details = $this->site->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            $this->sma->md();
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->products_model->getProductPhotos($id);
        $this->data['category'] = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['warehouses'] = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants'] = $this->products_model->getProductOptions($id);
        $this->data['check_warehouse_id'] = $user->warehouse_id;

        $this->load->view($this->theme . 'products/modal_view', $this->data);
    }

    function view_old($id = NULL)
    {
        $this->sma->checkPermissions('index');

        $pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['sale_unit'] = $this->site->getUnitByID($pr_details->sale_unit);
        $this->data['purchase_unit'] = $this->site->getUnitByID($pr_details->purchase_unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['supplier1'] = $this->products_model->getSupplierByID($pr_details->supplier1);
        $this->data['supplier2'] = $this->products_model->getSupplierByID($pr_details->supplier2);
        $this->data['supplier3'] = $this->products_model->getSupplierByID($pr_details->supplier3);
        $this->data['supplier4'] = $this->products_model->getSupplierByID($pr_details->supplier4);
        $this->data['supplier5'] = $this->products_model->getSupplierByID($pr_details->supplier5);
        $this->data['store_products'] = $this->products_model->getAllStores($id);
       
        $this->data['images'] = $this->products_model->getProductPhotos($id);
        $this->data['category'] = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants'] = $this->products_model->getProductOptions($id);
        $this->data['sold'] = $this->products_model->getSoldQty($id);
        $this->data['purchased'] = $this->products_model->getPurchasedQty($id);

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => $pr_details->name));
        $meta = array('page_title' => $pr_details->name, 'bc' => $bc);
        $this->page_construct('products/view', $meta, $this->data);
    }

    function pdf($id = NULL, $view = NULL)
    {
        $this->sma->checkPermissions('index');

        $pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->products_model->getProductPhotos($id);
        $this->data['category'] = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants'] = $this->products_model->getProductOptions($id);

        $name = $pr_details->code . '_' . str_replace('/', '_', $pr_details->name) . ".pdf";
        if ($view) {
            $this->load->view($this->theme . 'products/pdf', $this->data);
        } else {
            $html = $this->load->view($this->theme . 'products/pdf', $this->data, TRUE);
            if (!$this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            $this->sma->generate_pdf($html, $name);
        }
    }

    function getSubCategories($category_id = NULL)
    {
        if ($rows = $this->products_model->getSubCategories($category_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
        echo $data;
    }

    function product_actions($wh = NULL)
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'sync_quantity') {

                    foreach ($_POST['val'] as $id) {
                        $this->site->syncQuantity(NULL, NULL, NULL, $id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("products_quantity_sync"));
                    redirect($_SERVER["HTTP_REFERER"]);
                } elseif ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->products_model->deleteProduct($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("products_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                } elseif ($this->input->post('form_action') == 'labels') {

                    foreach ($_POST['val'] as $id) {
                        $row = $this->products_model->getProductByID($id);
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }

                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('print_barcodes')));
                    $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
                    $this->page_construct('products/print_barcodes', $meta, $this->data);
                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Products');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('brand'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('sale') . ' ' . lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('purchase') . ' ' . lang('unit_code'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('cost'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('price'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('Dropship'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('Crossdop'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('MRP'));
                    $this->excel->getActiveSheet()->SetCellValue('L1', lang('alert_quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('M1', lang('tax_method'));
                    $this->excel->getActiveSheet()->SetCellValue('N1', lang('subcategory_code'));
                    $this->excel->getActiveSheet()->SetCellValue('O1', lang('quantity'));
                    $this->excel->getActiveSheet()->SetCellValue('P1', lang('Supplier Name'));
                    $this->excel->getActiveSheet()->SetCellValue('Q1', lang('Tax Type'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $product = $this->products_model->getProductDetail($id);

                        $brand = $this->site->getBrandByID($product->brand);
                        $base_unit = $sale_unit = $purchase_unit = '';
                        if ($units = $this->site->getUnitsByBUID($product->unit)) {
                            foreach ($units as $u) {
                                if ($u->id == $product->unit) {
                                    $base_unit = $u->code;
                                }
                                if ($u->id == $product->sale_unit) {
                                    $sale_unit = $u->code;
                                }
                                if ($u->id == $product->purchase_unit) {
                                    $purchase_unit = $u->code;
                                }
                            }
                        }
                        $variants = $this->products_model->getProductOptions($id);
                        $product_variants = '';
                        if ($variants) {
                            foreach ($variants as $variant) {
                                $product_variants .= trim($variant->name) . '|';
                            }
                        }
                        $quantity = $product->quantity;
                        if ($wh) {
                            if ($wh_qty = $this->products_model->getProductQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
                        }

                        $get_supplier = $product->supplier1;
                        $get_supplier_details = $this->site->getSupplierByID($get_supplier);

                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $product->name);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $product->code);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, ($brand ? $brand->name : ''));
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $base_unit);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sale_unit);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $purchase_unit);
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $product->cost);
                        }
                        if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $product->price);
                        }


                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $product->dropship);
                        $this->excel->getActiveSheet()->SetCellValue('J' . $row, $product->crossdock);
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $product->mrp);


                        $this->excel->getActiveSheet()->SetCellValue('L' . $row, $product->alert_quantity);
                        $this->excel->getActiveSheet()->SetCellValue('M' . $row, $product->tax_method ? lang('exclusive') : lang('inclusive'));
                        $this->excel->getActiveSheet()->SetCellValue('N' . $row, $product->subcategory_code);
                        $this->excel->getActiveSheet()->SetCellValue('O' . $row, $quantity);
                        $this->excel->getActiveSheet()->SetCellValue('P' . $row, $get_supplier_details->company);

                        if (($product->tax_rate == 372) || ($product->tax_rate == 84) || ($product->tax_rate == 82)) {
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $product->tax_rate_name);
                        } else {
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, "3rd Schedule");
                        }

                        $row++;
                    }
                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'products_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'admin/products');
        }
    }

    public function delete_image($id = NULL)
    {
        $this->sma->checkPermissions('edit', true);
        if ($id && $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            $this->db->delete('product_photos', array('id' => $id));
            $this->sma->send_json(array('error' => 0, 'msg' => lang("image_deleted")));
        }
        $this->sma->send_json(array('error' => 1, 'msg' => lang("ajax_error")));
    }

    public function getSubUnits($unit_id)
    {
        $units = $this->site->getUnitsByBUID($unit_id);
        $this->sma->send_json($units);
    }

    public function qa_suggestions()
    {
        $term = $this->input->get('term', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->products_model->getQASuggestions($sr);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $options = $this->products_model->getProductOptions($row->id);
                $row->option = $option_id;
                $row->serial = '';
                $c = sha1(uniqid(mt_rand(), true));
                $pr[] = array(
                    'id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'options' => $options
                );
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function adjustment_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->products_model->deleteAdjustment($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("adjustment_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                } elseif ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('quantity_adjustments');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('created_by'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('items'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $adjustment = $this->products_model->getAdjustmentByID($id);
                        $created_by = $this->site->getUser($adjustment->created_by);
                        $warehouse = $this->site->getWarehouseByID($adjustment->warehouse_id);
                        $items = $this->products_model->getAdjustmentItems($id);
                        $products = '';
                        if ($items) {
                            foreach ($items as $item) {
                                $products .= $item->product_name . '(' . $this->sma->formatQuantity($item->type == 'subtraction' ? -$item->quantity : $item->quantity) . ')' . "\n";
                            }
                        }

                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($adjustment->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $adjustment->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $warehouse->name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $created_by->first_name . ' ' . $created_by->last_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->decode_html($adjustment->note));
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $products);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'quantity_adjustments_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function stock_counts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('stock_count');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('stock_counts')));
        $meta = array('page_title' => lang('stock_counts'), 'bc' => $bc);
        $this->page_construct('products/stock_counts', $meta, $this->data);
    }

    function getCounts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('stock_count', TRUE);

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $detail_link = anchor('admin/products/view_count/$1', '<label class="label label-primary pointer">' . lang('details') . '</label>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('stock_counts')}.id as id, date, reference_no, {$this->db->dbprefix('warehouses')}.name as wh_name, type, brand_names, category_names, initial_file, final_file")
            ->from('stock_counts')
            ->join('warehouses', 'warehouses.id=stock_counts.warehouse_id', 'left');
        if ($warehouse_id) {
            $this->datatables->where('warehouse_id', $warehouse_id);
        }

        $this->datatables->add_column('Actions', '<div class="text-center">' . $detail_link . '</div>', "id");
        echo $this->datatables->generate();
    }

    function view_count($id)
    {
        $this->sma->checkPermissions('stock_count', TRUE);
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count->finalized) {
            $this->sma->md('admin/products/finalize_count/' . $id);
        }

        $this->data['stock_count'] = $stock_count;
        $this->data['stock_count_items'] = $this->products_model->getStockCountItems($id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($stock_count->warehouse_id);
        $this->data['adjustment'] = $this->products_model->getAdjustmentByCountID($id);
        $this->load->view($this->theme . 'products/view_count', $this->data);
    }

    function count_stock($page = NULL)
    {
        $this->sma->checkPermissions('stock_count');
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');
        $this->form_validation->set_rules('type', lang("type"), 'required');

        if ($this->form_validation->run() == true) {

            $warehouse_id = $this->input->post('warehouse');
            $type = $this->input->post('type');
            $categories = $this->input->post('category') ? $this->input->post('category') : NULL;
            $brands = $this->input->post('brand') ? $this->input->post('brand') : NULL;
            $this->load->helper('string');
            $name = random_string('md5') . '.csv';
            $products = $this->products_model->getStockCountProducts($warehouse_id, $type, $categories, $brands);
            $pr = 0;
            $rw = 0;
            foreach ($products as $product) {
                if ($variants = $this->products_model->getStockCountProductVariants($warehouse_id, $product->id)) {
                    foreach ($variants as $variant) {
                        $items[] = array(
                            'product_code' => $product->code,
                            'product_name' => $product->name,
                            'variant' => $variant->name,
                            'expected' => $variant->quantity,
                            'counted' => ''
                        );
                        $rw++;
                    }
                } else {
                    $items[] = array(
                        'product_code' => $product->code,
                        'product_name' => $product->name,
                        'variant' => '',
                        'expected' => $product->quantity,
                        'counted' => ''
                    );
                    $rw++;
                }
                $pr++;
            }
            if (!empty($items)) {
                $csv_file = fopen('./files/' . $name, 'w');
                fprintf($csv_file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($csv_file, array(lang('product_code'), lang('product_name'), lang('variant'), lang('expected'), lang('counted')));
                foreach ($items as $item) {
                    fputcsv($csv_file, $item);
                }
                fclose($csv_file);
            } else {
                $this->session->set_flashdata('error', lang('no_product_found'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }
            $category_ids = '';
            $brand_ids = '';
            $category_names = '';
            $brand_names = '';
            if ($categories) {
                $r = 1;
                $s = sizeof($categories);
                foreach ($categories as $category_id) {
                    $category = $this->site->getCategoryByID($category_id);
                    if ($r == $s) {
                        $category_names .= $category->name;
                        $category_ids .= $category->id;
                    } else {
                        $category_names .= $category->name . ', ';
                        $category_ids .= $category->id . ', ';
                    }
                    $r++;
                }
            }
            if ($brands) {
                $r = 1;
                $s = sizeof($brands);
                foreach ($brands as $brand_id) {
                    $brand = $this->site->getBrandByID($brand_id);
                    if ($r == $s) {
                        $brand_names .= $brand->name;
                        $brand_ids .= $brand->id;
                    } else {
                        $brand_names .= $brand->name . ', ';
                        $brand_ids .= $brand->id . ', ';
                    }
                    $r++;
                }
            }
            $data = array(
                'date' => $date,
                'warehouse_id' => $warehouse_id,
                'reference_no' => $this->input->post('reference_no'),
                'type' => $type,
                'categories' => $category_ids,
                'category_names' => $category_names,
                'brands' => $brand_ids,
                'brand_names' => $brand_names,
                'initial_file' => $name,
                'products' => $pr,
                'rows' => $rw,
                'created_by' => $this->session->userdata('user_id')
            );
        }

        if ($this->form_validation->run() == true && $this->products_model->addStockCount($data)) {
            $this->session->set_flashdata('message', lang("stock_count_intiated"));
            admin_redirect('products/stock_counts');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['brands'] = $this->site->getAllBrands();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('count_stock')));
            $meta = array('page_title' => lang('count_stock'), 'bc' => $bc);
            $this->page_construct('products/count_stock', $meta, $this->data);
        }
    }

    function finalize_count($id)
    {
        $this->sma->checkPermissions('stock_count');
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count || $stock_count->finalized) {
            $this->session->set_flashdata('error', lang("stock_count_finalized"));
            admin_redirect('products/stock_counts');
        }

        $this->form_validation->set_rules('count_id', lang("count_stock"), 'required');

        if ($this->form_validation->run() == true) {

            if ($_FILES['csv_file']['size'] > 0) {
                $note = $this->sma->clear_tags($this->input->post('note'));
                $data = array(
                    'updated_by' => $this->session->userdata('user_id'),
                    'updated_at' => date('Y-m-d H:s:i'),
                    'note' => $note
                );

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('product_code', 'product_name', 'product_variant', 'expected', 'counted');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                $differences = 0;
                $matches = 0;
                foreach ($final as $pr) {
                    if ($product = $this->products_model->getProductByCode(trim($pr['product_code']))) {
                        $pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
                        if ($pr['expected'] == $pr['counted']) {
                            $matches++;
                        } else {
                            $pr['stock_count_id'] = $id;
                            $pr['product_id'] = $product->id;
                            $pr['cost'] = $product->cost;
                            $pr['product_variant_id'] = empty($pr['product_variant']) ? NULL : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
                            $products[] = $pr;
                            $differences++;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        admin_redirect('products/finalize_count/' . $id);
                    }
                    $rw++;
                }

                $data['final_file'] = $csv;
                $data['differences'] = $differences;
                $data['matches'] = $matches;
                $data['missing'] = $stock_count->rows - ($rw - 2);
                $data['finalized'] = 1;
            }
        }

        if ($this->form_validation->run() == true && $this->products_model->finalizeStockCount($id, $data, $products)) {
            $this->session->set_flashdata('message', lang("stock_count_finalized"));
            admin_redirect('products/stock_counts');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stock_count'] = $stock_count;
            $this->data['warehouse'] = $this->site->getWarehouseByID($stock_count->warehouse_id);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('products'), 'page' => lang('products')), array('link' => admin_url('products/stock_counts'), 'page' => lang('stock_counts')), array('link' => '#', 'page' => lang('finalize_count')));
            $meta = array('page_title' => lang('finalize_count'), 'bc' => $bc);
            $this->page_construct('products/finalize_count', $meta, $this->data);
        }
    }
    public function groups_old(){
        $user = $this->site->getUser();
        // $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['brands'] = $this->site->getAllBrands();

        $user_warehouse_id = $this->session->userdata('warehouse_id');
        if($user_warehouse_id != 0 && $user_warehouse_id != ''){
            $this->data['swarehosue'] = $user_warehouse_id;
        }
        else{
            $this->data['swarehosue'] = $this->input->get('warehosue');
        }

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['rows'] = array();
        $this->db->select('
            sma_product_groups.id,
            sma_product_groups.name,
            sma_product_groups.brand_id,
            sma_product_groups.status,
            sma_brands.name as brand,
            (
                SELECT COUNT(id) FROM sma_products WHERE sma_products.group_id = sma_product_groups.id AND sma_products.status = 0
            ) as inactive,
            (
                SELECT COUNT(id) FROM sma_products WHERE sma_products.group_id = sma_product_groups.id AND sma_products.status = 1
            ) as active
        ');
        $this->db->from('sma_product_groups');
        $this->db->join('sma_brands','sma_brands.id = sma_product_groups.brand_id','left');
        $q = $this->db->get();
        $rows = $q->result();
        foreach($rows as $row){
            $temp = $row;
            $this->db->select('SUM(sma_purchase_items.quantity_balance) as sum_qty');
            $this->db->from('sma_products');
            $this->db->join('sma_product_groups','sma_products.group_id = sma_product_groups.id','left');
            $this->db->join('sma_purchase_items','sma_purchase_items.product_id = sma_products.id','left');
            $this->db->where('sma_product_groups.id',$row->id);
            $this->db->where('sma_purchase_items.quantity_balance > ',0);
            if($this->data['swarehosue'] != 'all' && $this->data['swarehosue'] != ''){
                $this->db->where('sma_purchase_items.warehouse_id > ',$this->data['swarehosue']);
            }
            $q2 = $this->db->get();
            $res2 = $q2->result();
            $temp->qty = 0;
            if(isset($res2[0]->sum_qty)){
                if($res2[0]->sum_qty != ""){
                    $temp->qty = $res2[0]->sum_qty;
                }
            }
            $inerQuery1 = 'SELECT SUM(quantity_balance) as qb FROM sma_purchase_items WHERE product_id = sma_products.id';
            if($this->data['swarehosue'] != 'all' && $this->data['swarehosue'] != ''){
                $inerQuery1 .= ' AND warehouse_id = '.$this->data["swarehosue"];
            }
            $this->db->select('
                id,
                name,
                mrp,
                status,
                (
                    '.$inerQuery1.'
                ) as qty
            ');
            $this->db->from('sma_products');
            $this->db->where('group_id',$row->id);
            $q3 = $this->db->get();
            $temp->products = json_encode($q3->result());
            $this->data['rows'][] = $temp;
        }

        $this->db->select('
            0 as id,
            "Unknow Group" as name,
            0 brand_id,
            1 status,
            "Unknown Brand" as brand,
            (
                SELECT COUNT(id) FROM sma_products WHERE (sma_products.group_id = 0 OR sma_products.group_id IS NULL) AND sma_products.status = 0
            ) as inactive,
            (
                SELECT COUNT(id) FROM sma_products WHERE (sma_products.group_id = 0 OR sma_products.group_id IS NULL) AND sma_products.status = 1
            ) as active
        ');
        $this->db->from('sma_product_groups');
        $this->db->where('id',1);

        $q = $this->db->get();
        $rows = $q->result();
        foreach($rows as $row){
            $temp = $row;
            $this->db->select('SUM(sma_purchase_items.quantity_balance) as sum_qty');
            $this->db->from('sma_products');
            $this->db->join('sma_purchase_items','sma_purchase_items.product_id = sma_products.id','left');
            $this->db->where('(sma_products.group_id = 0 OR sma_products.group_id IS NULL)');
            $this->db->where('sma_purchase_items.quantity_balance > ',0);
            if($this->data['swarehosue'] != 'all' && $this->data['swarehosue'] != ''){
                $this->db->where('sma_purchase_items.warehouse_id > ',$this->data['swarehosue']);
            }
            $q2 = $this->db->get();
            $res2 = $q2->result();
            $temp->qty = 0;
            if(isset($res2[0]->sum_qty)){
                if($res2[0]->sum_qty != ""){
                    $temp->qty = $res2[0]->sum_qty;
                }
            }
            $this->data['rows'][] = $temp;
        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('products')), array('link' => '#', 'page' => lang('Groups')));
        $meta = array('page_title' => lang('Product Groups'), 'bc' => $bc);
        $this->page_construct('products/groups', $meta, $this->data);
    }
    public function addgroups(){
        $sendvalue['codestatus'] = "no";
        $sendvalue['message'] = "Try Again";
        $brand = $this->input->post('brand');
        $name = $this->input->post('name');
        if($brand == ""){
            $sendvalue['message'] = "Please select brand";
        }
        else if($name == ""){
            $sendvalue['message'] = "Please enter name";
        }
        else{
            $insertdata['name'] = $name;
            $insertdata['brand_id'] = $brand;
            $insertdata['created_by'] = $this->session->userdata('user_id');
            $insertdata['status'] = 1;
            $this->db->insert('sma_product_groups',$insertdata);
            $sendvalue['message'] = "Add new group successfully";
            $sendvalue['codestatus'] = "ok";
        }
        echo json_encode($sendvalue);
    }
    public function editgroups(){
        $sendvalue['codestatus'] = "no";
        $sendvalue['message'] = "Try Again";
        $brand = $this->input->post('brand');
        $name = $this->input->post('name');
        $id = $this->input->post('id');
        if($id == ""){
            $sendvalue['message'] = "Sameting wrong please refresh page than edit.";
        }
        if($brand == ""){
            $sendvalue['message'] = "Please select brand";
        }
        else if($name == ""){
            $sendvalue['message'] = "Please enter name";
        }
        else{
            $setdata['name'] = $name;
            $setdata['brand_id'] = $brand;
            $this->db->set($setdata);
            $this->db->where('id',$id);
            $this->db->update('sma_product_groups');
            $sendvalue['message'] = "Update group successfully";
            $sendvalue['codestatus'] = "ok";
        }
        echo json_encode($sendvalue);
    }
    public function deletegroups(){
        $id = $this->input->get('id');
        $this->db->where('id',$id);
        $this->db->delete('sma_product_groups');
        $sendvalue['codestatus'] = "ok";
        $sendvalue['message'] = "Delete Successfully";
        echo json_encode($sendvalue);
    }
}
