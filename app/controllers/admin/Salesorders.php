<?php defined('BASEPATH') or exit('No direct script access allowed');


class Salesorders extends MY_Controller
{
    public function __construct(){
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->load->library('form_validation');
        $this->load->admin_model('salesorder_model');
        $this->load->admin_model('general_model');
        $this->load->admin_model('price_model');
        $this->data['logo'] = true;
    }
    // New Code
    public function index(){

        $this->data['warehouse'] = $this->input->get('warehouse');        
        $this->data['supplier'] = $this->input->get('supplier');        
        $this->data['customer'] = $this->input->get('customer');        
        $this->data['start_date'] = $this->input->get('start_date');        
        $this->data['end_date'] = $this->input->get('end_date');        
        $this->data['ostatus'] = $this->input->get('ostatus');        
        $this->data['astatus'] = $this->input->get('astatus');        
        $this->data['sostatus'] = $this->input->get('sostatus');        

        $this->data['warehouses'] = $this->general_model->GetAllWarehouses();
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $this->data['customers'] = $this->general_model->GetAllCustomers();

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Sale Orders'));
        $meta = array('page_title' => 'Sale Orders', 'bc' => $bc);
        $this->page_construct2('saleorders/index', $meta, $this->data);

    }
    public function get_lists(){
        // Count Total Rows
        $this->db->from('sma_sales_orders_tb');
        $totalq = $this->db->get();
        $this->runquery_so('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_so();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/salesorders/view/".$row->id).'" >Detail</a>';
            $status = ucwords($row->status);
            if($status == "Completed"){
                $status = '<span class="uk-badge uk-badge-success">'.$status.'</span>';
            }
            else if($status == "Partial"){
                $status = '<span class="uk-badge uk-badge-warning">'.$status.'</span>';
            }
            else if($status == "Pending"){
                $status = '<span class="uk-badge uk-badge-primary">'.$status.'</span>';
            }
            else if($status == "Closed"){
                $status = '<span class="uk-badge uk-badge-danger">'.$status.'</span>';
            }
            else if($status == "Cancel"){
                $status = '<span class="uk-badge uk-badge-danger">'.$status.'</span>';
            }
            $data[] = array(
                '<input type="checkbox" class="i-checks" data-md-icheck/>',
                $row->date,
                $row->ref_no,
                $row->po_number,
                $row->supplier_name,
                $row->customer_name,
                $row->warehouse_name,
                $row->total_qty,
                decimalallow($row->total_val),
                $row->complete_qty,
                decimalallow($row->total_cval),
                (int)$row->percal.'%',
                (int)$row->pervcal.'%',
                $row->created_by,
                ucwords($row->accounts_team_status),
                ucwords($row->operation_team_stauts),
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
    public function runquery_so($onlycoun = "no"){
        $warehouse = $this->input->post('warehouse');
        $supplier = $this->input->post('supplier');
        $customer = $this->input->post('customer');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $ostatus = $this->input->post('ostatus');
        $astatus = $this->input->post('astatus');
        $sostatus = $this->input->post('sostatus');


        $column_search = array(
            'sma_sales_orders_tb.id',
            'sma_sales_orders_tb.date',
            'sma_sales_orders_tb.ref_no',
            'sma_sales_orders_tb.warehouse_id',
            'sma_sales_orders_tb.po_number',
            'sma_sales_orders_tb.delivery_date',
            'sma_sales_orders_tb.created_at',
            'sma_sales_orders_tb.accounts_team_status',
            'sma_sales_orders_tb.operation_team_stauts',
            'sma_sales_orders_tb.status',
            'supplier_detail.name',
            'customer_detail.name',
            'sma_warehouses.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('sma_sales_orders_tb.id as id');
        }
        else{
            $this->db->select('
                sma_sales_orders_tb.id as id,
                sma_sales_orders_tb.date as date,
                sma_sales_orders_tb.ref_no as ref_no,
                sma_sales_orders_tb.warehouse_id as warehouse_id,
                sma_sales_orders_tb.po_number as po_number,
                sma_sales_orders_tb.delivery_date as delivery_date,
                sma_sales_orders_tb.created_at as created_at,
                sma_sales_orders_tb.accounts_team_status as accounts_team_status,
                sma_sales_orders_tb.operation_team_stauts as operation_team_stauts,
                sma_sales_orders_tb.status as status,
                supplier_detail.name as supplier_name,
                customer_detail.name as customer_name,
                sma_warehouses.name as warehouse_name,
                sma_warehouses.code as warehouse_code,
                CONCAT(sma_users.first_name, " ", sma_users.last_name) AS created_by,
                COALESCE((
                    SELECT 
                        SUM(COALESCE(sma_sales_order_items.quantity,0)) 
                    FROM 
                        sma_sales_order_items 
                    WHERE 
                        sma_sales_order_items.so_id=sma_sales_orders_tb.id
                ),0) AS total_qty,
                COALESCE((
                    SELECT
                        SUM(IF(
                            sma_tax_rates.type = 2,
                            sma_sales_order_items.quantity*(sma_products.price+sma_tax_rates.rate),
                            sma_sales_order_items.quantity*(sma_products.price+(sma_products.price/100*sma_tax_rates.rate))
                        ))
                    FROM
                        sma_sales_order_items
                    LEFT JOIN sma_products ON sma_products.id = sma_sales_order_items.product_id
                    LEFT JOIN sma_tax_rates ON sma_tax_rates.id = sma_products.tax_rate
                    WHERE so_id = sma_sales_orders_tb.id
                ),0) AS total_val,

                COALESCE((
                    SELECT 
                        SUM(COALESCE(sma_sales_order_complete_items.quantity,0)) 
                    FROM 
                        sma_sales_order_complete_items 
                    WHERE 
                        sma_sales_order_complete_items.so_id=sma_sales_orders_tb.id
                ),0) AS complete_qty,
                COALESCE((
                    SELECT
                        SUM(IF(
                            sma_tax_rates.type = 2,
                            sma_sales_order_complete_items.quantity*(sma_products.price+sma_tax_rates.rate),
                            sma_sales_order_complete_items.quantity*(sma_products.price+(sma_products.price/100*sma_tax_rates.rate))
                        ))
                    FROM
                        sma_sales_order_complete_items
                    LEFT JOIN sma_products ON sma_products.id = sma_sales_order_complete_items.product_id
                    LEFT JOIN sma_tax_rates ON sma_tax_rates.id = sma_products.tax_rate
                    WHERE so_id = sma_sales_orders_tb.id
                ),0) AS total_cval,
                ((SELECT complete_qty)/(SELECT total_qty))*100 AS percal,
                ((SELECT total_cval)/(SELECT total_val))*100 AS pervcal
            ');
        }
        $this->db->from('sma_sales_orders_tb');
        $this->db->join('sma_companies as customer_detail', 'customer_detail.id = sma_sales_orders_tb.customer_id', 'left');
        $this->db->join('sma_companies as supplier_detail', 'supplier_detail.id = sma_sales_orders_tb.supplier_id', 'left');
        $this->db->join('sma_warehouses', 'sma_warehouses.id = sma_sales_orders_tb.warehouse_id', 'left');
        $this->db->join('sma_users', 'sma_users.id = sma_sales_orders_tb.created_by', 'left');
        if($warehouse != "" && $warehouse != "all"){
            $this->db->where('sma_sales_orders_tb.warehouse_id',$warehouse);
        }
        if($supplier != "" && $supplier != "all"){
            $this->db->where('sma_sales_orders_tb.supplier_id',$supplier);
        }
        if($customer != "" && $customer != "all"){
            $this->db->where('sma_sales_orders_tb.customer_id',$customer);
        }
        if($ostatus != "" && $ostatus != "all"){
            $this->db->where('sma_sales_orders_tb.operation_team_stauts',$ostatus);
        }
        if($start_date != ""){
            $this->db->where('sma_sales_orders_tb.date >=',$start_date);
        }
        if($end_date != ""){
            $this->db->where('sma_sales_orders_tb.date <= ',$end_date);
        }
        if($astatus != "" && $astatus != "all"){
            $this->db->where('sma_sales_orders_tb.accounts_team_status',$astatus);
        }
        if($sostatus != "" && $sostatus != "all"){
            $this->db->where('sma_sales_orders_tb.status',$sostatus);
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
    public function view($id){
        $this->db->select('
            so.*,
            warehouse.id as warehouse_id,
            warehouse.code as warehouse_code,
            warehouse.name as warehouse_name,
            warehouse.address as warehouse_address,
            supplier.id as supplier_id,
            supplier.company as supplier_company,
            supplier.vat_no as supplier_vat_no,
            supplier.address as supplier_address,
            supplier.city as supplier_city,
            supplier.state as supplier_state,
            supplier.country as supplier_country,
            supplier.phone as supplier_phone,
            supplier.cnic as supplier_cnic,
            supplier.email as supplier_email,
            supplier.cf1 as supplier_ntn,
            supplier.gst_no as supplier_gst_no,
            customer.id as cusomer_id,
            customer.name as cusomer_name,
            customer.company as cusomer_company,
            customer.vat_no as cusomer_vat_no,
            customer.address as cusomer_address,
            customer.city as cusomer_city,
            customer.state as cusomer_state,
            customer.country as cusomer_country,
            customer.phone as cusomer_phone,
            customer.cnic as cusomer_cnic,
            customer.email as cusomer_email,
            customer.cf1 as cusomer_ntn,
            customer.gst_no as cusomer_gst_no,
            CONCAT(user.first_name," ",user.last_name) as created_by,
        ');
        $this->db->from('sales_orders_tb as so');
        $this->db->join('sma_companies as customer', 'customer.id = so.customer_id', 'left');
        $this->db->join('sma_companies as supplier', 'supplier.id = so.supplier_id', 'left');
        $this->db->join('sma_warehouses as warehouse', 'warehouse.id = so.warehouse_id', 'left');
        $this->db->join('sma_users as user', 'user.id = so.created_by', 'left');
        $this->db->where('so.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $this->data['so'] = $q->result()[0];


            
            // echo '<pre>';
            // print_r($this->data['so']);
            // exit();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Sale Order'));
            $meta = array('page_title' => 'Sale Order', 'bc' => $bc);
            $this->page_construct2('saleorders/view', $meta, $this->data);
        }
        else{
            redirect(base_url('admin/salesorders'));

        }


    }
    public function get_items(){
        // Count Total Rows
        $this->db->from('sma_sales_order_items');
        $totalq = $this->db->get();
        $this->runquery_soi('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_soi();
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        $sno = 0;
        foreach($rows as $row){
            $sno++;
            $single_tp_price = $this->price_model->calculate_tp_with_tax($row->tp_price,$row->tax_value,$row->tax_type);
            $total_tp_price = round($single_tp_price*$row->quantity,4);
            $completed_qty = $row->completed_qty;
            if($completed_qty == ""){
                $completed_qty = 0;
            }
            $total_tp_complete_price = round($single_tp_price*$completed_qty,4);
            $uncomplete_qty = $row->quantity-$completed_qty;
            $total_tp_uncomplete_price = round($single_tp_price*$uncomplete_qty,4);
            $complete_percentage = ($completed_qty/$row->quantity)*100;
            $expected_complete_qty = $row->expected_complete_qty;
            $expected_complete_percentage = 0;
            $group_expected_complete_percentage = 0;
            if($uncomplete_qty == 0){
                $expected_complete_qty =  "Completed";
                $expected_complete_percentage =  "Completed";
                $group_expected_complete_percentage =  "Completed";
            }
            else{
                if($uncomplete_qty < $row->expected_complete_qty){
                    $expected_complete_qty = $uncomplete_qty;
                }
                $expected_complete_percentage = ($expected_complete_qty/$row->quantity)*100;
                if($expected_complete_percentage > 100){
                    $expected_complete_percentage = 100;
                }
                $expected_complete_percentage = (int)$expected_complete_percentage.'%';


                $group_expected_complete_percentage = (($row->group_sku_expected_qty/$row->quantity)*100).'%';
                if($group_expected_complete_percentage > 100){
                    $group_expected_complete_percentage = '100%';
                }
                $group_expected_complete_percentage = (int)$group_expected_complete_percentage.'%';
            }
            $button = "";
            if($row->so_status == "pending" || $row->so_status == "partial"){
                if($uncomplete_qty > 0){
                    $button .= '<button class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini soi_completeqty" type="button" data-id="'.$row->id.'" data-soid="'.$row->so_id.'" data-dqty="'.$row->quantity.'" data-pid="'.$row->pid.'" >Complete</button>';
                }
                $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini soi_editbtn" type="button" data-id="'.$row->id.'" data-soid="'.$row->so_id.'" data-dqty="'.$row->quantity.'" data-pid="'.$row->pid.'" >Edit</button>';
                if($completed_qty == 0){
                    $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini soi_deletebtn" data-id="'.$row->id.'" data-soid="'.$row->so_id.'" data-dqty="'.$row->quantity.'" data-pid="'.$row->pid.'" type="button" >Delete</button>';
                }
            }

            $data[] = array(
                $sno,
                $row->pid,
                $row->barcode,
                $row->name,
                $row->quantity,
                $total_tp_price,
                $completed_qty,
                $total_tp_complete_price,
                $complete_percentage.'%',
                $uncomplete_qty,
                $total_tp_uncomplete_price,
                $expected_complete_qty,
                $expected_complete_percentage,
                $group_expected_complete_percentage,
                $button
            );
        }
        $output = array(
            "data" => $data,
        );
        // Output to JSON format
        echo json_encode($output);
    }
    public function runquery_soi($onlycoun = "no"){
        $id = $this->input->post('id');
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('soi.id as id');
        }
        else{
            $this->db->select('
                soi.id,
                soi.product_id as pid,
                products.code as barcode,
                products.name as name,
                soi.quantity,
                products.price as tp_price,
                tax_rates.rate as tax_value,
                tax_rates.type as tax_type,
                (
                    SELECT 
                        SUM(COALESCE(sma_sales_order_complete_items.quantity,0)) 
                    FROM 
                        sma_sales_order_complete_items 
                    WHERE 
                        sma_sales_order_complete_items.soi_id=soi.id
                ) AS completed_qty,
                (
                    SELECT 
                        SUM(COALESCE(sma_purchase_items.quantity_balance,0)) 
                    FROM 
                        sma_purchase_items 
                    WHERE 
                        sma_purchase_items.product_id=soi.product_id AND
                        sma_purchase_items.warehouse_id=soi.warehouse_id
                ) AS expected_complete_qty,
                (
                    SELECT 
                        SUM(sma_purchase_items.quantity_balance) 
                    FROM 
                        sma_product_groups 
                    LEFT JOIN sma_products as p2 ON p2.group_id = sma_product_groups.id
                    LEFT JOIN sma_purchase_items ON sma_purchase_items.product_id = p2.id
                    WHERE sma_product_groups.id = sma_products.group_id AND sma_purchase_items.warehouse_id = soi.warehouse_id
                ) AS group_sku_expected_qty,
                so.status as so_status,
                so.id as so_id
            ');
        }
        $this->db->from('sma_sales_order_items as soi');
        $this->db->join('sales_orders_tb as so', 'so.id = soi.so_id', 'left');
        $this->db->join('products', 'products.id = soi.product_id', 'left');
        $this->db->join('tax_rates', 'tax_rates.id = products.tax_rate', 'left');
        $this->db->where('soi.so_id',$id);
    }
    public function get_citems(){
        // Count Total Rows
        $this->db->from('sma_sales_order_items');
        $totalq = $this->db->get();
        $this->runquery_csoi('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_csoi();
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        $sno = 0;
        foreach($rows as $row){
            $sno++;
            $button = "";
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini soi_completeqty_delete" type="button" data-id="'.$row->id.'" >Delete</button>';

            $data[] = array(
                $sno,
                $row->product_id,
                $row->code,
                $row->name,
                $row->quantity,
                $row->batch,
                $row->expiry,
                $button
            );
        }
        $output = array(
            "data" => $data,
        );
        // Output to JSON format
        echo json_encode($output);
    }
    public function runquery_csoi($onlycoun = "no"){
        $id = $this->input->post('id');
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('csoi.id as id');
        }
        else{
            $this->db->select('
                csoi.id ,
                csoi.product_id,
                products.code,
                products.name,
                csoi.quantity,
                csoi.batch,
                pi.expiry
            ');
        }
        $this->db->from('sales_order_complete_items as csoi');
        $this->db->join('products', 'products.id = csoi.product_id', 'left');
        $this->db->join('purchase_items as pi', 'pi.product_id = csoi.product_id AND pi.batch = csoi.batch', 'left');
        $this->db->where('csoi.so_id = '.$id.' AND csoi.status = "pending"');
    }



    // Old Code
    public function index_old($warehouse_id = null){
        $senddata['otstatus'] = $this->input->get('otstatus');
        $senddata['atstatus'] = $this->input->get('atstatus');
        $senddata['status'] = $this->input->get('status');
        $senddata['sot'] = $this->input->get('sot');
        $senddata['status'] = $senddata['status'] == "" ? "pending" : $senddata['status'];
        $this->data['otstatus'] = $senddata['otstatus'];
        $this->data['atstatus'] = $senddata['atstatus'];
        $this->data['status'] = $senddata['status'];
        $this->data['sot'] = $senddata['sot'];

        $this->data['start'] = $this->input->get('start_date') == "" ? '' : $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date') == "" ? '' : $this->input->get('end_date');

        $this->data['ssupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['suppliers']     = $this->site->GetAllSupplierList();
        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');
        if($this->data['user_warehouses'] == ""){
            $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        }
        else{
            $this->data['swarehouse'] = $this->data['user_warehouses'];
        }
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['scustomer'] = $this->input->get('customers') == "" ? 'all' : $this->input->get('customers');
        $this->data['customers']     = $this->site->getAllCompanies('customer');

        // $this->data['so_rows'] = $this->salesorder_model->data($senddata);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Sale Orders'));
        $meta = array('page_title' => 'Sale Orders', 'bc' => $bc);
        $this->page_construct('saleorders/index', $meta, $this->data);
    }
    public function merged(){
        $sos = $this->input->get('so_check');
        $this->data['detail']['noso'] = count($sos);
        $this->data['detail']['demand_qty'] = 0;
        $this->data['detail']['demand_val'] = 0;
        $this->data['detail']['complete_qty'] = 0;
        $this->data['detail']['complete_val'] = 0;
        $this->data['detail']['uncomplete_qty'] = 0;
        $this->data['detail']['uncomplete_val'] = 0;
        $this->data['detail']['qty_percentage'] = 0;
        $this->data['detail']['val_percentage'] = 0;
        $this->db->select('*');
        $this->db->from('sma_sales_order_items');
        $this->db->where_in('so_id',$sos);
        $q = $this->db->get();
        $rows = $q->result();
        $this->data['detail']['nosku'] = count($rows);
        

        foreach($rows as $row){
            $this->db->select('sma_products.price,sma_tax_rates.rate,sma_tax_rates.type');
            $this->db->from('sma_products');
            $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_products.tax_rate','left');
            $this->db->where('sma_products.id',$row->product_id);
            $q2 = $this->db->get();
            $price = 0;
            if($q2->num_rows() > 0 ){
                $data = $q2->result()[0];
                if($data->type == 1){
                    $price = $data->price+($data->price/100*$data->rate);
                }
                else{
                    $price = $data->price+$data->rate;
                }
            }
            $this->data['detail']['demand_val'] += $price*$row->quantity;
            $this->data['detail']['demand_qty'] += $row->quantity;
        }
        $this->db->select('product_id,quantity');
        $this->db->from('sma_sales_order_complete_items');
        $this->db->where_in('so_id',$sos);
        $q3 = $this->db->get();
        $socs = $q3->result();
        foreach($socs as $soc){
            $this->db->select('sma_products.price,sma_tax_rates.rate,sma_tax_rates.type');
            $this->db->from('sma_products');
            $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_products.tax_rate','left');
            $this->db->where('sma_products.id',$soc->product_id);
            $q2 = $this->db->get();
            $price = 0;
            if($q2->num_rows() > 0 ){
                $data = $q2->result()[0];
                if($data->type == 1){
                    $price = $data->price+($data->price/100*$data->rate);
                }
                else{
                    $price = $data->price+$data->rate;
                }
            }
            $this->data['detail']['complete_qty'] += $soc->quantity;
            $this->data['detail']['complete_val'] += $price*$soc->quantity;

        }

        $this->data['detail']['uncomplete_qty'] = $this->data['detail']['demand_qty']-$this->data['detail']['complete_qty'];
        $this->data['detail']['uncomplete_val'] = $this->data['detail']['demand_val']-$this->data['detail']['complete_val'];

        $this->data['detail']['qty_percentage'] = decimalallow($this->data['detail']['complete_qty']/$this->data['detail']['demand_qty']*100,0).'%';
        $this->data['detail']['val_percentage'] = decimalallow($this->data['detail']['complete_val']/$this->data['detail']['demand_val']*100,0).'%';

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Sale Orders Merged'));
        $meta = array('page_title' => 'Sale Orders Merged', 'bc' => $bc);
        $this->page_construct('saleorders/merged', $meta, $this->data);
    }
    public function get_so(){
        // Count Total Rows
        $this->db->from('sma_sales_orders_tb');
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
            $buttnon = '
                <div class="text-center">
                    <div class="btn-group text-left">
                        <button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                            Action
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li>'.anchor('admin/salesorders/view/'.$row->id, '<i class="fa fa-file-text-o"></i> Detail').'</li>
                        </ul>
                    </div>
                </div>

            ';
            $data[] = array(
                '<input class="checkbox multi-select input-xs" type="checkbox" value="'.$row->id.'" name="so_check[]"/>',
                $row->date,
                $row->ref_no,
                $row->po_number,
                $row->supplier_name,
                $row->customer_name,
                $row->warehouse_name,
                $row->total_qty,
                decimalallow($row->total_val),
                $row->complete_qty,
                decimalallow($row->total_cval),
                (int)$row->percal.'%',
                (int)$row->pervcal.'%',
                // (int)$percentage.'%',
                $row->created_by,
                $row->accounts_team_status,
                $row->operation_team_stauts,
                $row->status,
                $buttnon
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
        $column_order = array(
            'sma_sales_orders_tb.date',
            'sma_sales_orders_tb.date',
            'sma_sales_orders_tb.ref_no',
            'sma_sales_orders_tb.po_number',
            'supplier_detail.name',
            'customer_detail.name',
            'sma_sales_orders_tb.warehouse_id',
            'total_qty',
            'total_val',
            'complete_qty',
            'total_cval',
            'percal',
            'pervcal',
            'sma_sales_orders_tb.created_by',
            'sma_sales_orders_tb.accounts_team_status',
            'sma_sales_orders_tb.operation_team_stauts',
            'sma_sales_orders_tb.status',
        );
        // $column_search = array(
        //     'total_qty',
        //     'complete_qty',
        //     'percal',
        //     'created_by',
        // );
        $column_search = array(
            'sma_sales_orders_tb.id',
            'sma_sales_orders_tb.date',
            'sma_sales_orders_tb.ref_no',
            'sma_sales_orders_tb.warehouse_id',
            'sma_sales_orders_tb.po_number',
            'sma_sales_orders_tb.delivery_date',
            'sma_sales_orders_tb.created_at',
            'sma_sales_orders_tb.accounts_team_status',
            'sma_sales_orders_tb.operation_team_stauts',
            'sma_sales_orders_tb.status',
            'supplier_detail.name',
            'customer_detail.name',
            'sma_warehouses.name'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('sma_sales_orders_tb.id as id');
        }
        else{
            $this->db->select('
                sma_sales_orders_tb.id as id,
                sma_sales_orders_tb.date as date,
                sma_sales_orders_tb.ref_no as ref_no,
                sma_sales_orders_tb.warehouse_id as warehouse_id,
                sma_sales_orders_tb.po_number as po_number,
                sma_sales_orders_tb.delivery_date as delivery_date,
                sma_sales_orders_tb.created_at as created_at,
                sma_sales_orders_tb.accounts_team_status as accounts_team_status,
                sma_sales_orders_tb.operation_team_stauts as operation_team_stauts,
                sma_sales_orders_tb.status as status,
                supplier_detail.name as supplier_name,
                customer_detail.name as customer_name,
                sma_warehouses.name as warehouse_name,
                sma_warehouses.code as warehouse_code,
                CONCAT(sma_users.first_name, " ", sma_users.last_name) AS created_by,
                COALESCE((
                    SELECT 
                        SUM(COALESCE(sma_sales_order_items.quantity,0)) 
                    FROM 
                        sma_sales_order_items 
                    WHERE 
                        sma_sales_order_items.so_id=sma_sales_orders_tb.id
                ),0) AS total_qty,
                COALESCE((
                    SELECT
                        SUM(IF(
                            sma_tax_rates.type = 2,
                            sma_sales_order_items.quantity*(sma_products.price+sma_tax_rates.rate),
                            sma_sales_order_items.quantity*(sma_products.price+(sma_products.price/100*sma_tax_rates.rate))
                        ))
                    FROM
                        sma_sales_order_items
                    LEFT JOIN sma_products ON sma_products.id = sma_sales_order_items.product_id
                    LEFT JOIN sma_tax_rates ON sma_tax_rates.id = sma_products.tax_rate
                    WHERE so_id = sma_sales_orders_tb.id
                ),0) AS total_val,

                COALESCE((
                    SELECT 
                        SUM(COALESCE(sma_sales_order_complete_items.quantity,0)) 
                    FROM 
                        sma_sales_order_complete_items 
                    WHERE 
                        sma_sales_order_complete_items.so_id=sma_sales_orders_tb.id
                ),0) AS complete_qty,
                COALESCE((
                    SELECT
                        SUM(IF(
                            sma_tax_rates.type = 2,
                            sma_sales_order_complete_items.quantity*(sma_products.price+sma_tax_rates.rate),
                            sma_sales_order_complete_items.quantity*(sma_products.price+(sma_products.price/100*sma_tax_rates.rate))
                        ))
                    FROM
                        sma_sales_order_complete_items
                    LEFT JOIN sma_products ON sma_products.id = sma_sales_order_complete_items.product_id
                    LEFT JOIN sma_tax_rates ON sma_tax_rates.id = sma_products.tax_rate
                    WHERE so_id = sma_sales_orders_tb.id
                ),0) AS total_cval,
                ((SELECT complete_qty)/(SELECT total_qty))*100 AS percal,
                ((SELECT total_cval)/(SELECT total_val))*100 AS pervcal
            ');
        }
        $this->db->from('sma_sales_orders_tb');
        $this->db->join('sma_companies as customer_detail', 'customer_detail.id = sma_sales_orders_tb.customer_id', 'left');
        $this->db->join('sma_companies as supplier_detail', 'supplier_detail.id = sma_sales_orders_tb.supplier_id', 'left');
        $this->db->join('sma_warehouses', 'sma_warehouses.id = sma_sales_orders_tb.warehouse_id', 'left');
        $this->db->join('sma_users', 'sma_users.id = sma_sales_orders_tb.created_by', 'left');
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
        $user_warehouse_id = $this->session->userdata('warehouse_id');
        if($user_warehouse_id != 0 && $user_warehouse_id != ''){
            $this->db->where('sma_sales_orders_tb.warehouse_id',$user_warehouse_id);
        }
        else{
            if($_POST['warehouse'] != "" && $_POST['warehouse'] != "all" && $_POST['warehouse'] != "0"){
                $this->db->where('sma_sales_orders_tb.warehouse_id',$_POST['warehouse']);
            }
        }
        if($_POST['otstatus'] != "" && $_POST['otstatus'] != "all"){
            $this->db->where('sma_sales_orders_tb.operation_team_stauts',$_POST['otstatus']);
        }
        if($_POST['atstatus'] != "" && $_POST['atstatus'] != "all"){
            $this->db->where('sma_sales_orders_tb.accounts_team_status',$_POST['atstatus']);
        }
        if($_POST['status'] != "" && $_POST['status'] != "all"){
            $this->db->where('sma_sales_orders_tb.status',$_POST['status']);
        }
        if($_POST['supplier'] != "" && $_POST['supplier'] != "all"){
            $this->db->where('sma_sales_orders_tb.supplier_id',$_POST['supplier']);
        }
        if($_POST['customer'] != "" && $_POST['customer'] != "all"){
            $this->db->where('sma_sales_orders_tb.customer_id',$_POST['customer']);
        }
        if(isset($_POST['sot'])){
            if($_POST['sot'] == "m"){
                $this->db->where('sma_sales_orders_tb.store_order_id IS NULL');
            }
            else if($_POST['sot'] == "a"){
                $this->db->where('sma_sales_orders_tb.store_order_id IS NOT NULL');
            }
        }
        if($_POST['start_date'] != ""){
            $this->db->where('sma_sales_orders_tb.date >= ',$_POST['start_date']);
        }
        if($_POST['end_date'] != ""){
            $this->db->where('sma_sales_orders_tb.date <= ',$_POST['end_date']);
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }
    public function so_summary(){
        $sendvalue['codestatus'] = false;
        $so_no = $this->input->post('so_no');
        

        if($so_no != ""){
            // Get Purchase Order Data
            $this->db->select('
                id,
                ref_no,
                po_number,
                accounts_team_status,
                operation_team_stauts,
                status,
                cancel_date,
                complete_date,
                warehouse_id
            ');
            $this->db->from('sma_sales_orders_tb');
            $this->db->where('ref_no',$so_no);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $result = $q->result();
                $sendvalue['so_id'] = $result[0]->id;
                $sendvalue['so_no'] = $result[0]->ref_no;
                $sendvalue['po_no'] = $result[0]->po_number;
                $sendvalue['astatus'] = $result[0]->accounts_team_status;
                $sendvalue['ostatus'] = $result[0]->operation_team_stauts;
                $sendvalue['so_status'] = $result[0]->status;
                $sendvalue['cancel_date'] = $result[0]->cancel_date;
                $sendvalue['complete_date'] = $result[0]->complete_date;
                $sendvalue['demand_qty'] = 0;
                $sendvalue['no_of_dc'] = 0;
                $sendvalue['no_of_pdc'] = 0;
                $sendvalue['dispatch_qty'] = 0;
                $sendvalue['pending_qty'] = 0;
                $sendvalue['dispatch_pre'] = 0;
                $sendvalue['expacted_qty'] = 0;
                $sendvalue['expacted_pre'] = 0;
                // Get Sale Order Items
                $this->db->select('
                    sma_products.price as product_price,
                    sma_tax_rates.rate as product_tax_value,
                    sma_tax_rates.type as product_tax_type,
                    sma_sales_order_items.quantity,
                    (
                        SELECT 
                            SUM(COALESCE(sma_sales_order_complete_items.quantity,0)) 
                        FROM 
                            sma_sales_order_complete_items 
                        WHERE 
                            sma_sales_order_complete_items.soi_id=sma_sales_order_items.id
                    ) AS completed_qty,
                    (
                        SELECT 
                            SUM(COALESCE(sma_purchase_items.quantity_balance,0)) 
                        FROM 
                            sma_purchase_items 
                        WHERE 
                            sma_purchase_items.product_id=sma_sales_order_items.product_id AND
                            sma_purchase_items.warehouse_id='.$result[0]->warehouse_id.'
                    ) AS expected_complete_qty
                ');
                $this->db->from('sma_sales_order_items');
                $this->db->join('sma_products', 'sma_products.id = sma_sales_order_items.product_id', 'left');
                $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_products.tax_rate', 'left');
                $this->db->where('so_id',$result[0]->id);
                $q5 = $this->db->get();
                $sendvalue['details'] = array();
                $no = 0;
                if ($q5->num_rows() > 0) {
                    $items = json_decode(json_encode($q5->result()));
                    foreach($items as $item){
                        $sendvalue['demand_qty'] += $item->quantity;
                        $sendvalue['dispatch_qty'] += $item->completed_qty;
                        $sendvalue['pending_qty'] = $sendvalue['demand_qty']-$sendvalue['dispatch_qty'];
                        $pending_item = $item->quantity-$item->completed_qty;
                        if($pending_item<$item->expected_complete_qty){
                            $sendvalue['expacted_qty'] += $pending_item;
                        }
                        else{
                            $sendvalue['expacted_qty'] += $item->expected_complete_qty;
                        }
        
                        if($item->completed_qty == ""){
                            $item->completed_qty = 0;
                        }
                        $value_qty_single = 0; 
                        if($item->product_tax_type == 2){
                            $value_qty_single = $item->product_price+$item->product_tax_value; 
                        }
                        else{
                            $value_qty_single = $item->product_price+(($item->product_price/100)*$item->product_tax_value); 
                        }
                        $item->value_qty = $item->quantity*$value_qty_single;
                        $item->value_cqty = $item->completed_qty*$value_qty_single;
                        $no++;
                    }
                    $sendvalue['expacted_qty'] = $sendvalue['expacted_qty']+$sendvalue['dispatch_qty'];
                    $sendvalue['dispatch_pre'] = (int)(($sendvalue['dispatch_qty']/$sendvalue['demand_qty'])*100);
                    $sendvalue['expacted_pre'] = (int)($sendvalue['expacted_qty']/$sendvalue['demand_qty']*100);
                    $this->db->select('id');
                    $this->db->from('sma_sales_order_complete_tb');
                    $this->db->where('so_id',$result[0]->id);
                    $dc_q = $this->db->get();
                    $sendvalue['no_of_dc'] = $dc_q->num_rows();
                    $this->db->select('id');
                    $this->db->from('sma_sales_order_complete_tb');
                    $this->db->where('so_id',$result[0]->id);
                    $this->db->where('status','pending');
                    $dc_q = $this->db->get();
                    $sendvalue['no_of_pdc'] = $dc_q->num_rows();
                    $sendvalue['codestatus'] = "ok";
                }
                else{
                    $sendvalue['codestatus'] = "Items Not Found";
                }
            }
            else{
                $sendvalue['codestatus'] = "Invalid Sale";
            }
        }
        else{
            $sendvalue['codestatus'] = "Something Wrong";
        }


        echo json_encode($sendvalue);
    }
    public function add($warehouse_id = null){
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line("no_zero_required"));
        $this->form_validation->set_rules('warehouse', $this->lang->line("warehouse"), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('supplier', $this->lang->line("supplier"), 'required');
        $this->session->unset_userdata('csrf_token');
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['own_company'] = $this->site->getAllown_companies();
        $this->data['lcustomers'] = $this->site->getAllCompanies('customer');
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();

        // generate Ref Number
        $this->data['generate_ref'] = "";
        $this->db->set_dbprefix('');
        $this->db->select('AUTO_INCREMENT');
        $this->db->from('information_schema.TABLES');
        $this->db->where('TABLE_SCHEMA = "salesinventory2" AND TABLE_NAME = "sma_sales_orders_tb"');
        $refq = $this->db->get();
        $refresult = $refq->result();
        $this->db->set_dbprefix('sma_');
        if(count($refresult)>0){
            $this->data['generate_ref'] = 'SO-'.sprintf("%05d", $refresult[0]->AUTO_INCREMENT);

        }
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Add Sale Orders'));
        $meta = array('page_title' => ' Add Sale Orders', 'bc' => $bc);
        $this->page_construct2('saleorders/add', $meta, $this->data);
    }
    public function generate_ref(){
        $sendvalue = "";
        // generate Ref Number
        $dbdetail = $this->db;
        $this->db->set_dbprefix('');
        $this->db->select('AUTO_INCREMENT');
        $this->db->from('information_schema.TABLES');
        $this->db->where('TABLE_SCHEMA = "'.$dbdetail->database.'" AND TABLE_NAME = "sma_sales_orders_tb"');
        $refq = $this->db->get();
        $refresult = $refq->result();
        $this->db->set_dbprefix('sma_');
        if(count($refresult)>0){
            $sendvalue = 'SO-'.sprintf("%05d", $refresult[0]->AUTO_INCREMENT);
        }
        return $sendvalue;

    }
    public function suggestions(){
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $supplier_id = $this->input->get('supplier_id', true);
        // $own_company = $this->input->get('own_company', true);
        // if($warehouse_id != "" && $supplier_id != ""){
        if($supplier_id != ""){
            if (strlen($term) < 1 || !$term) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
            }
            $analyzed = $this->sma->analyze_term($term);
            $sr = $analyzed['term'];
            // $sr = $term;
            // print_r($sr);
            // exit();
            $option_id = $analyzed['option_id'];
            $rows = $this->salesorder_model->product_detail($sr, $warehouse_id, $supplier_id);
            $fed_tax_price = $this->site->fed_tax();
            $gst_tax_price = $this->site->gst_tax();
            $further_tax_price = $this->site->further_tax();
            // $own_company_details = $this->site->getown_companiesByID($own_company);
            if(count($rows) > 0){
                $r = 0;
                foreach ($rows as $row) {
                    $c = uniqid(mt_rand(), true);
                    $option = false;
                    $row->item_tax_method = $row->tax_method;
                    $row->supplier_part_no = '';
                    if ($row->supplier1 == $supplier_id) {
                        $row->supplier_part_no = $row->supplier1_part_no;
                    } elseif ($row->supplier2 == $supplier_id) {
                        $row->supplier_part_no = $row->supplier2_part_no;
                    } elseif ($row->supplier3 == $supplier_id) {
                        $row->supplier_part_no = $row->supplier3_part_no;
                    } elseif ($row->supplier4 == $supplier_id) {
                        $row->supplier_part_no = $row->supplier4_part_no;
                    } elseif ($row->supplier5 == $supplier_id) {
                        $row->supplier_part_no = $row->supplier5_part_no;
                    }
                    // if ($opt->cost != 0) {
                    //     $row->cost = $opt->cost;
                    // }
                    // $row->cost = $supplier_id ? $this->getSupplierCost($supplier_id, $row) : $row->cost;
                    $row->real_unit_cost = $row->cost;
                    $row->base_quantity = 1;
                    $row->base_unit = $row->unit;
                    $row->base_unit_cost = $row->cost;
                    $row->unit = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                    $row->new_entry = 1;
                    $row->expiry = '';
                    $row->qty = 1;
                    $row->quantity_balance = '';
                    $row->discount = '0';
                    $row->consiment = $row->price;
                    $row->batch = '';
                    $row->discount_one_checked = 'false';
                    $row->discount_two_checked = 'false';
                    $row->discount_three_checked = 'false';
                    $row->fed_tax_checked = 'false';
                    $row->discount_one = $row->discount_one != null ? $row->discount_one : '0.000';
                    $row->discount_two = $row->discount_two != null ? $row->discount_two : '0.000';
                    $row->discount_three = $row->discount_three != null ? $row->discount_three : '0.000';
                    // $row->fed_tax_rate = $fed_tax_price->fed_tax;
                    $row->fed_tax_rate = $row->fed_tax;
                    $row->gst_tax_rate = $row->gst_tax;
                    $row->further_tax_rate = $row->further_tax;
                    // $row->own_companies_check_strn = $own_company_details->strn;
                    $row->show_further_tax = $further_tax_price->further_tax;
                    unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    $units = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $pr[] = array(
                        'id' => sha1($c . $r), 
                        'item_id' => $row->id, 
                        'label' => $row->name . " (" . $row->code . ") (MRP: ".$row->mrp.", Available Qty: ".$row->qbalance.")",
                        'row' => $row, 
                        'tax_rate' => $tax_rate, 
                        'units' => $units, 
                        /*'batch' => "123456", 
                        'expiry' => "12/12/2018", */ 
                        'options' => 0
                    );
                    $r++;
                }
                $this->sma->send_json($pr);
            }
            else{
                $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
            }
        }
        else{
            $this->sma->send_json(array(array('id' => 0, 'label' => 'You do not select warehouse or supplier', 'value' => $term)));
        }
    }
    public function submit(){
        $returndata['status'] = false;
        $sodata['date'] = $this->input->post('date');
        // $sodata['ref_no'] = $this->input->post('reference_no');
        $sodata['ref_no'] = $this->generate_ref();
        $sodata['warehouse_id'] = $this->input->post('warehosue');
        $sodata['po_number'] = $this->input->post('po_number');
        $sodata['po_date'] = $this->input->post('po_date');
        $sodata['delivery_date'] = $this->input->post('saledeliverydate');
        $sodata['supplier_id'] = $this->input->post('supplier');
        $sodata['etalier_id'] = 0;
        $sodata['customer_id'] = $this->input->post('customer');
        $products = $this->input->post('product_id');
        $quantity = $this->input->post('quantity');
        if($sodata['customer_id'] != "" && $sodata['customer_id'] != 0){
            $sodata['customer_address_id'] = $this->input->post('deliveryaddress');
            $sodata['sale_note'] = $this->input->post('note');
            $items = array();
            $no = 0;
            $totaldiscount = 0;
            $totalitemstax = 0;
            $total = 0;
            foreach($products as $key => $product_id){
                $product = $this->db->select('
                    products.*,
                    units.id as unit_id,
                    units.code as unit_code,
                    tax_rates.id as tax_id,
                    tax_rates.code as tax_code,
                    tax_rates.rate as tax_rate,
                    tax_rates.type as tax_type,
                    tax_rates.name as tax_name
                ')
                ->from('products')
                ->join('units','units.id = products.unit','left')
                ->join('tax_rates','tax_rates.id = products.tax_rate','left')
                ->where('products.id',$product_id)
                ->get()
                ->result()[0];
                $items[$no]['supplier_id'] = $sodata['supplier_id'];
                $items[$no]['product_id'] = $product_id;
                $items[$no]['warehouse_id'] = $sodata['warehouse_id'];
                $items[$no]['quantity'] = $quantity[$key];
                $items[$no]['unit'] = $product->unit_id;
                $items[$no]['unit_code'] = $product->unit_code;
                $items[$no]['status'] = 'pending';
                $no++;
            }
            $sodata['created_by'] = $this->session->userdata('user_id');
            $returndata = $this->salesorder_model->add_so($sodata,$items);
            // $returndata['so_id'] = $returndata;
            // $returndata['message'] = 'Sale order create successfully';
            // $returndata['status'] = true;
        }
        else{
            $returndata['message'] = 'Please Selete Customer.';
        }
        echo json_encode($returndata);
    }
    public function view_old($id = null){
        if($id != ""){
            $returndata  = $this->salesorder_model->details($id);
            if($returndata['codestatus'] == "ok"){

                $this->data['detail'] = $returndata['details'];
        
                $this->data['tax_rates'] = $this->site->getAllTaxRates();
                $this->data['warehouses'] = $this->site->getAllWarehouses();
                $this->data['own_company'] = $this->site->getAllown_companies();
                $this->data['lcustomers'] = $this->site->getAllCompanies('customer');

                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Sales Order'));
                $meta = array('page_title' => 'Sales Order', 'bc' => $bc);
                $this->page_construct('saleorders/view', $meta, $this->data);

            }
            else{
                echo '<script>alert("'.$returndata['codestatus'].'"); location.href = "'.base_url('admin/salesorders').'";</script>';
            }
        }
        else{
            redirect(base_url('admin/salesorders'));
        }
    }
    public function citem_delete(){
        $id = $this->input->get('id');

        $this->db->select('
            id,
            so_id,
            product_id,
            warehouse_id,
            batch,
            quantity
        ');
        $this->db->from('sales_order_complete_items');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $result = $q->result()[0];

            $this->db->delete('sales_order_complete_items', array('id' => $id)); 

            //Batch Quantity Update in Purchase Table
            $this->db->set('quantity_balance', 'quantity_balance+'.$result->quantity, FALSE);
            $this->db->where('product_id ', $result->product_id);
            $this->db->where('warehouse_id', $result->warehouse_id);
            $this->db->where('batch', $result->batch);
            $this->db->update('purchase_items');
            
            //Warehouse Quantity Update in Warehouse Product Table
            $this->db->set('quantity', 'quantity+'.$result->quantity, FALSE);
            $this->db->where('product_id', $result->product_id);
            $this->db->where('warehouse_id', $result->warehouse_id);
            $this->db->update('warehouses_products');
            
            //Product Quantity Update in Product Table
            $this->db->set('quantity', 'quantity+'.$result->quantity, FALSE);
            $this->db->where('id', $result->product_id);
            $this->db->update('products');
            $this->so_status($result->so_id);
                            
            $this->load->model('admin/stores_model');
            $this->stores_model->updateStoreQty($result->product_id,$result->warehouse_id,0,'Delete Complete item in SO');
        }
    }
    public function itemdetail(){
        $sendvalue['codestatus'] = "no";
        $id = $this->input->get('id');
        $this->db->select('
            sma_sales_order_items.*,
            (
                SELECT COALESCE(SUM(sma_sales_order_complete_items.quantity),0) 
                FROM sma_sales_order_complete_items 
                WHERE sma_sales_order_complete_items.soi_id=sma_sales_order_items.id
            ) AS complete_qty,
        ');
        $this->db->from('sma_sales_order_items');
        $this->db->where('sma_sales_order_items.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $sendvalue['detail'] = $q->result()[0];
            $sendvalue['codestatus'] = "ok";
        }
        else{
            $sendvalue['codestatus'] = "Item not found";
        }
        echo json_encode($sendvalue);
    }
    public function batchdetail(){
        $sendvalue['codestatus'] = "no";
        $id = $this->input->get('id');
        $this->db->select('
            sma_sales_order_items.*,
            (
                SELECT COALESCE(SUM(sma_sales_order_complete_items.quantity),0) 
                FROM sma_sales_order_complete_items 
                WHERE sma_sales_order_complete_items.soi_id=sma_sales_order_items.id
            ) AS complete_qty,
        ');
        $this->db->from('sma_sales_order_items');
        $this->db->where('sma_sales_order_items.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $item = $q->result();
            if($item[0]->quantity <= $item[0]->complete_qty){
                $sendvalue['codestatus'] = "This item always completed";
            }
            else{
                $this->db->select('expiry,quantity_balance as qb,batch as code');
                $this->db->from('sma_purchase_items');
                $this->db->where('product_id',$item[0]->product_id);
                $this->db->where('warehouse_id',$item[0]->warehouse_id);
                $this->db->where('quantity_balance != ','0.0000');
                $this->db->order_by('expiry', 'DESC');
                $batchs = $this->db->get();
                $batchs = $batchs->result();
                if(count($batchs)>0){
                    $sendvalue['ebatchs'] = $batchs;
                    $sendvalue['uncompletedqty'] = $item[0]->quantity-$item[0]->complete_qty;
                    $sendvalue['codestatus'] = "ok";
                }
                else{
                    $sendvalue['codestatus'] = "This product out of stock";
                }
            }
        }
        else{
            $sendvalue['codestatus'] = "Item not found";
        }
        echo json_encode($sendvalue);
    }
    public function so_status($id){
        $this->db->select('
            sma_sales_orders_tb.status,
            (
                SELECT 
                    SUM(COALESCE(sma_sales_order_items.quantity,0)) 
                FROM 
                    sma_sales_order_items 
                WHERE 
                    sma_sales_order_items.so_id=sma_sales_orders_tb.id
            ) AS total_qty,
            (
                SELECT 
                    SUM(COALESCE(sma_sales_order_complete_items.quantity,0)) 
                FROM 
                    sma_sales_order_complete_items 
                WHERE 
                    sma_sales_order_complete_items.so_id=sma_sales_orders_tb.id
            ) AS complete_qty,
            (
                SELECT 
                    SUM(COALESCE(sma_sales_order_complete_items.quantity,0)) 
                FROM 
                    sma_sales_order_complete_items 
                WHERE 
                    sma_sales_order_complete_items.so_id=sma_sales_orders_tb.id AND
                    sma_sales_order_complete_items.status="pending"
            ) AS pending_dc_item,
            (
                SELECT 
                    SUM(COALESCE(sma_sales_order_complete_items.quantity,0)) 
                FROM 
                    sma_sales_order_complete_items 
                WHERE 
                    sma_sales_order_complete_items.so_id=sma_sales_orders_tb.id AND
                    sma_sales_order_complete_items.status="complete"
            ) AS complete_dc_item,
        ');
        $this->db->from('sma_sales_orders_tb');
        $this->db->where('sma_sales_orders_tb.id',$id);
        $q = $this->db->get();
        if($q->num_rows()>0){
            $set['accounts_team_status'] = 'pending';
            $set['operation_team_stauts'] = 'pending';
            $so = $q->result()[0];
            if($so->complete_qty == 0){
                $set['accounts_team_status'] = 'pending';
                $set['operation_team_stauts'] = 'pending';
                $set['status'] = 'pending';
            }
            else if($so->total_qty == $so->complete_qty){
                $set['operation_team_stauts'] = 'complete dispatch';
                if($so->pending_dc_item == 0){
                    $set['accounts_team_status'] = 'completed invoiced';
                    $set['status'] = 'completed';
                }
                else if($so->total_qty == $so->pending_dc_item){
                    $set['accounts_team_status'] = 'pending';
                    $set['status'] = 'pending';
                }
                else{
                    // $set['accounts_team_status'] = 'partial invoiced';
                    $set['accounts_team_status'] = 'pending';
                }
                
            }
            else{
                $set['operation_team_stauts'] = 'partial dispatch';
                $complete_dc_item = $so->complete_dc_item == '' ? 0 : $so->complete_dc_item;
                if($so->complete_dc_item == 0){
                    $set['accounts_team_status'] = 'pending';
                    $set['status'] = 'pending';
                }
                else{
                    if($so->pending_dc_item == 0){
                        $set['accounts_team_status'] = 'partial invoiced';
                    }
                    else{
                        $set['accounts_team_status'] = 'pending';
                    }
                    $set['status'] = 'partial';
                }

            }
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('sma_sales_orders_tb');
        }
    }
    public function soc_created($id){
        $returndata = 0;
        $this->db->select('soc_id');
        $this->db->from('sma_sales_order_complete_items');
        $this->db->where('so_id = '.$id.' AND status = "pending"');
        $q = $this->db->get();
        if($q->num_rows()>0){
            $returndata = $q->result()[0]->soc_id;
        }
        else{
            $insert['so_id'] = $id;
            $insert['created_by'] = $this->session->userdata('user_id');
            $insert['status'] = 'pending';
            $this->db->insert('sma_sales_order_complete_tb',$insert);
            $returndata = $this->db->insert_id();
        }
        $this->so_status($id);
        return $returndata;
    }
    public function addbatch(){
        $sendvalue['codestatus'] = "ok";
        $batch = $this->input->post('batch');
        $qty = $this->input->post('qty');
        $itemid = $this->input->post('itemid');
        $soid = $this->input->post('soid');
        if($qty > 0){
            $this->db->select('
                sma_sales_order_items.*,
                (
                    SELECT COALESCE(SUM(sma_sales_order_complete_items.quantity),0) 
                    FROM sma_sales_order_complete_items 
                    WHERE sma_sales_order_complete_items.soi_id=sma_sales_order_items.id
                ) AS complete_qty,
            ');
            $this->db->from('sma_sales_order_items');
            $this->db->where('sma_sales_order_items.id',$itemid);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $item = $q->result();
                if($item[0]->quantity <= $item[0]->complete_qty){
                        $sendvalue['codestatus'] = "This item always completed";
                }
                else{
                    $this->db->select('
                        id,
                        product_id,
                        warehouse_id,
                        expiry,
                        quantity_balance as qb,
                        batch as code
                    ');
                    $this->db->from('sma_purchase_items');
                    $this->db->where('product_id',$item[0]->product_id);
                    $this->db->where('warehouse_id',$item[0]->warehouse_id);
                    $this->db->where('batch',$batch);
                    $batchq = $this->db->get();
                    if($batchq->num_rows() > 0){
                        $batchs = $batchq->result();
                        $countqb = 0;
                        foreach($batchs as $brow){
                            $countqb = $countqb + $brow->qb;
                        }
                        if($countqb >= $qty){
                            if(($item[0]->quantity-$item[0]->complete_qty)>=$qty){

                                $socid = $this->soc_created($soid);                                
                                $insert['so_id'] = $soid;
                                $insert['soc_id'] = $socid;
                                $insert['soi_id'] = $itemid;
                                $insert['product_id'] = $item[0]->product_id;
                                $insert['supplier_id'] = $item[0]->supplier_id;
                                $insert['warehouse_id'] = $item[0]->warehouse_id;
                                $insert['net_unit_price'] = $item[0]->net_unit_price;
                                $insert['unit_price'] = $item[0]->unit_price;
                                $insert['dropship'] = $item[0]->dropship;
                                $insert['crossdock'] = $item[0]->crossdock;
                                $insert['mrp'] = $item[0]->mrp;
                                $insert['expiry_date'] = $batchs[0]->expiry;
                                $insert['batch'] = $batch;
                                $insert['quantity'] = $qty;
                                $insert['unit'] = $item[0]->unit;
                                $insert['unit_code'] = $item[0]->unit_code;
                                $insert['total'] = $item[0]->total;
                                $insert['product_tax_id'] = $item[0]->product_tax_id;
                                $insert['product_tax'] = $item[0]->product_tax;
                                $insert['fed_tax'] = $item[0]->fed_tax;
                                $insert['further_tax'] = $item[0]->further_tax;
                                $insert['total_tax'] = $item[0]->total_tax;
                                $insert['discount_one'] = $item[0]->discount_one;
                                $insert['discount_two'] = $item[0]->discount_two;
                                $insert['discount_three'] = $item[0]->discount_three;
                                $insert['total_discount'] = $item[0]->total_discount;
                                $insert['sub_total'] = $item[0]->sub_total;
                                $insert['status'] = 'pending';
                                $this->db->insert('sma_sales_order_complete_items',$insert);
                                
                                //Batch Quantity Update in Purchase Table
                                $this->db->set('quantity_balance', 'quantity_balance-'.$qty, FALSE);
                                $this->db->where('id', $batchs[0]->id);
                                $this->db->update('purchase_items');
                                
                                //Warehouse Quantity Update in Warehouse Product Table
                                $this->db->set('quantity', 'quantity-'.$qty, FALSE);
                                $this->db->where('product_id', $batchs[0]->product_id);
                                $this->db->where('warehouse_id', $batchs[0]->warehouse_id);
                                $this->db->update('warehouses_products');
                                
                                //Product Quantity Update in Product Table
                                $this->db->set('quantity', 'quantity-'.$qty, FALSE);
                                $this->db->where('id', $batchs[0]->product_id);
                                $this->db->update('products');
    
                                $this->load->model('admin/stores_model');
                                $this->stores_model->updateStoreQty($batchs[0]->product_id,$batchs[0]->warehouse_id,0,"Complete Item in SO");
    
                                $this->so_status($soid);
                                $sendvalue['soid'] = $soid;
                                if(($item[0]->quantity-$item[0]->complete_qty)-$qty == 0){
                                    $sendvalue['codestatus'] = "ok";
                                }
                                else{
                                    $sendvalue['codestatus'] = "next";
                                }
                                // $sendvalue['codestatus'] = "Batch Quantity Add";
                            }
                            else{
                                $remainqty = $item[0]->quantity - $item[0]->complete_qty;
                                $sendvalue['codestatus'] = "Remain Quantity ".$remainqty;
                            }
                        }
                        else{
                            $sendvalue['codestatus'] = $qty. " quantity not available in ".$batch;
                        }
                    }
                    else{
                        $sendvalue['codestatus'] = "Batch not found";
                    }
                }
            }
            else{
                $sendvalue['codestatus'] = "Item not found";
            }
        }
        else{
            $sendvalue['codestatus'] = "Enter minimum 1 Quantity";
        }
       
        echo json_encode($sendvalue);
    }
    public function updateitem(){  
        $sendvalue['codestatus'] = "no";
        $id = $this->input->post('id');
        $qty = $this->input->post('qty');

        $this->db->select('
            sma_sales_order_items.so_id,
            sma_sales_order_items.quantity,
            (
                SELECT COALESCE(SUM(sma_sales_order_complete_items.quantity),0) 
                FROM sma_sales_order_complete_items 
                WHERE sma_sales_order_complete_items.soi_id=sma_sales_order_items.id
            ) AS complete_qty,
        ');
        $this->db->from('sma_sales_order_items');
        $this->db->where('sma_sales_order_items.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $item = $q->result()[0];
            if($qty >= $item->complete_qty){
                $this->db->set('quantity', $qty);
                $this->db->where('id', $id);
                $this->db->update('sma_sales_order_items');
                $this->so_status($item->so_id);
                $sendvalue['codestatus'] = "ok";
            }
            else{
                $sendvalue['codestatus'] = $item->complete_qty." quantity is already completed.";
            }
        }
        else{
            $sendvalue['codestatus'] = "Item not found";
        }
        echo json_encode($sendvalue);
    }
    public function itemdelete(){
        $sendvalue['status'] = false;
        $sendvalue['message'] = "no";
        $id = $this->input->get('id');
        $this->db->select('
            sma_sales_order_items.so_id,
            sma_sales_order_items.quantity,
            (
                SELECT COALESCE(SUM(sma_sales_order_complete_items.quantity),0) 
                FROM sma_sales_order_complete_items 
                WHERE sma_sales_order_complete_items.soi_id=sma_sales_order_items.id
            ) AS complete_qty,
        ');
        $this->db->from('sma_sales_order_items');
        $this->db->where('sma_sales_order_items.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $item = $q->result()[0];
            if($item->complete_qty == 0){
                $this->db->delete('sma_sales_order_items', array('id' => $id)); 
                $this->so_status($item->so_id);
                $sendvalue['message'] = "Item delete successfully";
                $sendvalue['status'] = true;
            }
            else{
                $sendvalue['message'] = $item->complete_qty." quantity is already completed.";
            }
        }
        else{
            $sendvalue['message'] = "Item not found";
        }
        echo json_encode($sendvalue);

    }
    public function update(){
        $sendvalue['codestatus'] = "no";
        $id = $this->input->post('id');
        $sdate = $this->input->post('date');
        $customer = $this->input->post('customer');
        $deliveryaddress = $this->input->post('deliveryaddress');
        $ponumber = $this->input->post('ponumber');
        $po_date = $this->input->post('po_date');
        $ddate = $this->input->post('saledeliverydate');
        $etalier_id = $this->input->post('etaliers');

        $this->db->select('*');
        $this->db->from('sma_sales_orders_tb');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $result = $q->result();

            $setdata['date'] = $sdate;
            $setdata['customer_id'] = $customer;
            $setdata['customer_address_id'] = $deliveryaddress;
            $setdata['po_number'] = $ponumber;
            $setdata['po_date'] = $po_date;
            $setdata['delivery_date'] = $ddate;
            $setdata['etalier_id'] = $etalier_id;
            $this->db->set($setdata);
            $this->db->where('id',$id);
            $this->db->update('sma_sales_orders_tb');
            $sendvalue['message'] = "Sale Order Update Successfully";
            $sendvalue['codestatus'] = "ok";
        }
        else{
            $sendvalue['message'] = "Sale Order Not Found";
        }
        echo json_encode($sendvalue);
    }
    public function cancel(){
        $sendvalue['status'] = false;
        $sendvalue['message'] = "no";
        $id = $this->input->get('id');

        $this->db->select('*');
        $this->db->from('sma_sales_orders_tb');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $result = $q->result();
            $this->allItemQtyUpdate($id,'+');
            $setdata['cancel_date'] = date('Y-m-d H:i:s');
            $setdata['status'] = 'cancel';
            $this->db->set($setdata);
            $this->db->where('id',$id);
            $this->db->update('sma_sales_orders_tb');
            $sendvalue['message'] = "Sale Order Canceled";
            $sendvalue['status'] = true;
        }
        else{
            $sendvalue['message'] = "Sale Order Not Found";
        }
        echo json_encode($sendvalue);
    }
    public function close(){
        $sendvalue['codestatus'] = "no";
        $id = $this->input->get('id');

        $this->db->select('*');
        $this->db->from('sma_sales_orders_tb');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $result = $q->result();
            $setdata['cancel_date'] = date('Y-m-d H:i:s');
            $setdata['status'] = 'close';
            $this->db->set($setdata);
            $this->db->where('id',$id);
            $this->db->update('sma_sales_orders_tb');
            $sendvalue['message'] = "Sale Order Canceled";
            $sendvalue['codestatus'] = "ok";
        }
        else{
            $sendvalue['message'] = "Sale Order Not Found";
        }
        echo json_encode($sendvalue);
    }
    public function allItemQtyUpdate($id,$type){
        $this->db->select('
            product_id,
            warehouse_id,
            batch,
            quantity
        ');
        $this->db->from('sales_order_complete_items');
        $this->db->where('so_id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $items = $q->result();
            foreach($items as $item){
                //Batch Quantity Update in Purchase Table
                if($type == "+"){
                    $this->db->set('quantity_balance', 'quantity_balance+'.$item->quantity, FALSE);
                }
                else{
                    $this->db->set('quantity_balance', 'quantity_balance-'.$item->quantity, FALSE);
                }
                $this->db->where('product_id ', $item->product_id);
                $this->db->where('warehouse_id', $item->warehouse_id);
                $this->db->where('batch', $item->batch);
                $this->db->update('purchase_items');
                
                //Warehouse Quantity Update in Warehouse Product Table
                if($type == "+"){
                    $this->db->set('quantity', 'quantity+'.$item->quantity, FALSE);
                }
                else{
                    $this->db->set('quantity', 'quantity-'.$item->quantity, FALSE);
                }
                $this->db->where('product_id', $item->product_id);
                $this->db->where('warehouse_id', $item->warehouse_id);
                $this->db->update('warehouses_products');
                
                //Product Quantity Update in Product Table
                if($type == "+"){
                    $this->db->set('quantity', 'quantity+'.$item->quantity, FALSE);
                }
                else{
                    $this->db->set('quantity', 'quantity-'.$item->quantity, FALSE);
                }
                $this->db->where('id', $item->product_id);
                $this->db->update('products');
    
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item->product_id,$item->warehouse_id,0,'Update Item Stock Qty by SO');
            }
        }
        else{
        }


    }
    public function delete(){
        $data['status'] = false;
        $id = $this->input->get('id');
        $data = array();
        if($id != ""){
            $this->db->select('id');
            $this->db->from('sma_sales');
            $this->db->where('so_id',$id);
            $q = $this->db->get();
            if($q->num_rows() == 0){

                $this->allItemQtyUpdate($id,'+');
                $this->db->delete('sma_sales_orders_tb', array('id' => $id));
                $this->db->delete('sma_sales_order_items', array('so_id' => $id));
                $this->db->delete('sma_sales_order_complete_items', array('so_id' => $id));
                $data['message'] = "Sale order delete successfully";
                $data['status'] = true;
            }
            else{
                $data['message'] = "Firstly Delete Purchase Invoice of this PO";
            }
        }
        else{
            $data['message'] = "Purchase Order ID not found";
        }
        echo json_encode($data);
    }
    public function create($id){
        $this->data['billers'] = $this->site->getAllCompaniesBiller('biller');
        $this->data['own_company'] = $this->site->getAllown_companies();
        $this->data['so'] = $this->salesorder_model->createdata($id);
        if($this->data['so']['status'] != "close" && $this->data['so']['status'] != "cancel" && $this->data['so']['status'] != "completed" && count($this->data['so']['items']) > 0 ){
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => 'Create Sale'));
            $meta = array('page_title' => 'Create Sale', 'bc' => $bc);
            $this->page_construct2('saleorders/create', $meta, $this->data);
        }
        else{
            echo '<script> location.href = "'.base_url('admin/salesorders/view/'.$id).'";</script>';
        }
    }
    public function created(){
        $sendvalue['codestatus'] = "no";
        $biller = 0;
        $own_company = $this->input->post('own_company');
        $warehouse = $this->input->post('warehouse');
        $customer = $this->input->post('customer');
        $customer_id = $this->input->post('customer_id');
        $po_number = $this->input->post('po_number');
        $po_date = $this->input->post('po_date');
        $orderdiscount = $this->input->post('order_discount');
        $shipping = $this->input->post('shipping');
        $payment_term = $this->input->post('payment_term');
        $dc_number = $this->input->post('dc_number');
        $cartidiage = $this->input->post('cartidiage');
        $note = $this->input->post('note');
        $so_id = $this->input->post('so_id');
        $staff_note = $this->input->post('staff_note');
        $total = 0;
        $productdiscount = 0;
        $totaldiscount = $productdiscount+$orderdiscount;
        $soproducttax = 0;
        $total_adv_tax = 0;
        $ordertax = 0;
        $totaltax = $soproducttax+$ordertax;
        $totalitem = 0;
        if($own_company != ""){
            $this->db->select('id');
            $this->db->from('sma_own_companies');
            $this->db->where('id = '.$own_company.' AND auto_invoice_gen = 1');
            $q2 = $this->db->get();
            if($q2->num_rows() > 0){
                $this->db->select('MAX(reference_no) as ref');
                $this->db->from('sma_sales');
                $this->db->where("
                    own_company = ".$own_company." AND 
                    reference_no REGEXP '^[0-9]+$' AND 
                    reference_no < 100000
                ");
                $q = $this->db->get();
                if($q->num_rows()>0){
                    $r = $q->result();
                    $reference_no = $r[0]->ref+1;
                }
                else{
                    $reference_no = $this->input->post('reference_no');
                }
            }
            else{
                $reference_no = $this->input->post('reference_no');
            }
        }
        else{
            $reference_no = "";
        }



        if($reference_no == ""){
            $sendvalue['codestatus'] = "Please Enter Invoice No";
        }
        else if($own_company == ""){
            $sendvalue['codestatus'] = "Please Select Own Company";
        }
        else{
            $this->db->select('
                sma_sales_orders_tb.*,
                supplier.name as supplier_name,
                customer.name as customer_name,
                customer.sales_type as customer_sales_type,
                "Default Biller" as biller_name,
            ');
            $this->db->from('sma_sales_orders_tb');
            $this->db->join('sma_companies as supplier', 'supplier.id = sma_sales_orders_tb.supplier_id', 'left');
            $this->db->join('sma_companies as customer', 'customer.id = sma_sales_orders_tb.customer_id', 'left');
            $this->db->where('sma_sales_orders_tb.id',$so_id);
            $so_q = $this->db->get();
            if($so_q->num_rows() == 0){
                $sendvalue['codestatus'] = "Invalid Sales Order";
            }
            else{
                $this->db->select('*');
                $this->db->from('sma_sales_order_complete_tb');
                $this->db->where('so_id',$so_id);
                $this->db->where('status','pending');
                $checkpendingso = $this->db->get();
                if($checkpendingso->num_rows() == 0){
                    $sendvalue['codestatus'] = "Invoice Already Created";
                }
                else{
                    $so_data = $so_q->result()[0];
                    $soitems = array();
                    $product_id = $this->input->post('product_id');
                    $soc_id = $this->input->post('soc_id');
                    $product_price = $this->input->post('product_price');
                    $productquantity = $this->input->post('productquantity');
                    $discount_one_amount = $this->input->post('discount_one_amount');
                    $prdiscount_two_amountoduct_id = $this->input->post('discount_two_amount');
                    $discount_three_amount = $this->input->post('discount_three_amount');
                    $discount_one_rate = $this->input->post('discount_one_rate');
                    $discount_two_rate = $this->input->post('discount_two_rate');
                    $discount_three_rate = $this->input->post('discount_three_rate');
                    $discountselect = $this->input->post('discountselect');
                    $productfed = $this->input->post('productfed');
                    $producttax = $this->input->post('producttax');
                    $productfuthertax = $this->input->post('productfuthertax');
                    $productadvtax = $this->input->post('productadvtax');
                    $producttotaltxt = $this->input->post('producttotaltxt');
                    $setting_further_tax = $this->salesorder_model->further_tax()->further_tax;
                    $numberofitem = count($product_id);
                    $socid = 0;
                    for($i=0; $i<$numberofitem;$i++){
                        $this->db->select('
                            sma_sales_order_complete_items.product_id,
                            sma_sales_order_complete_items.soc_id,
                            sma_purchase_items.product_code,
                            sma_products.company_code,
                            sma_purchase_items.product_name,
                            sma_products.type as product_type,
                            sma_products.adv_tax_reg as adv_tax_reg,
                            sma_products.adv_tax_nonreg as adv_tax_nonreg,
                            sma_purchase_items.option_id,
                            sma_purchase_items.net_unit_cost,
                            sma_purchase_items.price,
                            sma_purchase_items.dropship,
                            sma_purchase_items.crossdock,
                            sma_purchase_items.mrp,
                            sma_purchase_items.expiry,
                            sma_sales_order_complete_items.batch,
                            sma_sales_order_complete_items.warehouse_id,
                            sma_sales_order_complete_items.quantity,
                            sma_purchase_items.gst,
                            sma_purchase_items.cgst,
                            sma_purchase_items.sgst,
                            sma_purchase_items.igst,
                            sma_purchase_items.further_tax,
                            sma_purchase_items.fed_tax,
                            sma_purchase_items.product_unit_id,
                            sma_purchase_items.product_unit_code,
                            sma_tax_rates.id as tax_rate_id,
                            sma_tax_rates.name as tax_rate_name,
                            sma_tax_rates.rate as tax_rate_rate,
                            sma_tax_rates.code as tax_rate_code,
                            sma_tax_rates.type as tax_rate_type,
                            sma_companies.gst_no as customer_gst_no,
                            sma_sales_order_complete_tb.id as socid
                        ');
                        $this->db->from('sma_sales_order_complete_items');
                        $this->db->join('sma_sales_orders_tb', 'sma_sales_orders_tb.id = sma_sales_order_complete_items.so_id', 'left');
                        $this->db->join('sma_sales_order_complete_tb', 'sma_sales_order_complete_tb.so_id = sma_sales_order_complete_items.so_id AND sma_sales_order_complete_tb.status = "pending"', 'left');
                        $this->db->join('sma_companies', 'sma_companies.id = sma_sales_orders_tb.customer_id', 'left');
                        $this->db->join('sma_purchase_items', 'sma_purchase_items.product_id  = sma_sales_order_complete_items.product_id AND sma_purchase_items.batch  = sma_sales_order_complete_items.batch AND sma_purchase_items.warehouse_id  = sma_sales_order_complete_items.warehouse_id', 'left');
                        $this->db->join('sma_products', 'sma_products.id  = sma_sales_order_complete_items.product_id', 'left');
                        $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_purchase_items.tax_rate_id', 'left');
                        $this->db->where('sma_sales_order_complete_items.id = '.$soc_id[$i]);
                        $this->db->where('sma_sales_order_complete_items.status = "pending"');
                        $this->db->group_by("sma_sales_order_complete_items.id");
                        $soi_q = $this->db->get();
                        if($soi_q->num_rows() > 0){
                            $soi_data = $soi_q->result()[0];
                            $socid = $soi_data->socid;
                            if ($so_data->customer_sales_type === 'cost') {
                                $selling_price = $soi_data->net_unit_cost;
                            }
                            else if ($so_data->customer_sales_type === 'mrp') {
                                $selling_price = $soi_data->mrp;
                            }
                            else{
                                $selling_price = $soi_data->price;
                            }
                            $itemsfedtax = $soi_data->fed_tax*$soi_data->quantity;
                            $further_tax = 0;
                            if($soi_data->tax_rate_type == 1){
                                if($soi_data->customer_gst_no == ""){
                                    $further_tax = (($selling_price/100)*$setting_further_tax)*$soi_data->quantity;
                                }
    
                                $itemtotaltax = (($selling_price/100)*$soi_data->tax_rate_rate)*$soi_data->quantity;
                            }
                            else{
                                $itemtotaltax = $soi_data->tax_rate_rate*$soi_data->quantity;
                            }

                            $adv_tax = 0;
                            if($soi_data->customer_gst_no == ""){
                                $adv_tax = decimalallow(((($selling_price*$soi_data->quantity)+$itemtotaltax)/100)*$soi_data->adv_tax_nonreg,2 );
                            }
                            else{
                                $adv_tax = decimalallow(((($selling_price*$soi_data->quantity)+$itemtotaltax)/100)*$soi_data->adv_tax_reg,2 );
                            }
                            $total_adv_tax += $adv_tax;


                            $soproducttax += $itemtotaltax+$further_tax+$adv_tax;
    
                            $da1 = (($selling_price/100)*$discount_one_rate[$i])*$soi_data->quantity;
                            $da2 = (($selling_price/100)*$discount_two_rate[$i])*$soi_data->quantity;
                            $da3 = (($selling_price/100)*$discount_three_rate[$i])*$soi_data->quantity;
                            $itemtotaldiscount = $da1+$da2+$da3;
                            $productdiscount += $itemtotaldiscount;
                            $totalitem += $soi_data->quantity;
                            $itemtotal = ($selling_price*$soi_data->quantity)+($itemtotaltax+$further_tax+$adv_tax)-$itemtotaldiscount;
                            $total += $itemtotal;
                            // $itemitemdata['sale_id'] = 'id';
                            $itemitemdata['product_id'] = $soi_data->product_id;
                            $itemitemdata['product_code'] = $soi_data->product_code;
                            $itemitemdata['company_code'] = $soi_data->company_code;
                            $itemitemdata['product_name'] = $soi_data->product_name;
                            $itemitemdata['product_type'] = $soi_data->product_type;
                            $itemitemdata['option_id'] = $soi_data->option_id;
                            $itemitemdata['net_unit_price'] = $selling_price;
                            $itemitemdata['unit_price'] = $selling_price;
                            $itemitemdata['consignment'] = $soi_data->price;
                            $itemitemdata['dropship'] = $soi_data->dropship;
                            $itemitemdata['crossdock'] = $soi_data->crossdock;
                            $itemitemdata['mrp'] = $soi_data->mrp;
                            $itemitemdata['expiry'] = $soi_data->expiry;
                            $itemitemdata['batch'] = $soi_data->batch;
                            $itemitemdata['quantity'] = $soi_data->quantity;
                            $itemitemdata['warehouse_id'] = $soi_data->warehouse_id;
                            $itemitemdata['item_tax'] = $itemtotaltax;
                            $itemitemdata['tax_rate_id'] = $soi_data->tax_rate_id;
                            $itemitemdata['tax'] = $soi_data->tax_rate_rate;
                            $itemitemdata['discount'] = $itemtotaldiscount;
                            $itemitemdata['item_discount'] = $itemtotaldiscount;
                            $itemitemdata['subtotal'] = $itemtotal;
                            $itemitemdata['real_unit_price'] = $selling_price;
                            $itemitemdata['product_unit_id'] = $soi_data->product_unit_id;
                            $itemitemdata['product_unit_code'] = $soi_data->product_unit_code;
                            $itemitemdata['unit_quantity'] = $soi_data->quantity;
                            $itemitemdata['gst'] = $soi_data->gst;
                            $itemitemdata['cgst'] = $soi_data->cgst;
                            $itemitemdata['sgst'] = $soi_data->sgst;
                            $itemitemdata['igst'] = $soi_data->igst;
                            $itemitemdata['discount_one'] = $discount_one_rate[$i];
                            $itemitemdata['discount_two'] = $discount_two_rate[$i];
                            $itemitemdata['discount_three'] = $discount_three_rate[$i];
                            $itemitemdata['product_price'] = $selling_price;
                            $itemitemdata['further_tax'] = $further_tax;
                            $itemitemdata['fed_tax'] = $itemsfedtax;
                            $itemitemdata['adv_tax'] = $adv_tax;
                            $soitems[] = $itemitemdata;
    
                        }
                        else{
                            $soc_id[$i].' == '.$i;
    
                        }
                    }
                    $groudtotal = $total+$totaltax+$shipping-$totaldiscount;
                    $insertso['supplier_id'] = $so_data->supplier_id;
                    $insertso['date'] = date('Y-m-d H:i:s');
                    $insertso['reference_no'] = $reference_no;
                    $insertso['customer_id'] = $so_data->customer_id;
                    $insertso['customer_address_id'] = $so_data->customer_address_id;
                    $insertso['own_company'] = $own_company;
                    $insertso['po_number'] = $so_data->po_number;
                    $insertso['customer'] = $so_data->customer_name;
                    $insertso['biller_id'] = $biller;
                    $insertso['etalier_id'] = $so_data->etalier_id;
                    $insertso['biller'] = $so_data->biller_name;
                    $insertso['warehouse_id'] = $so_data->warehouse_id;
                    $insertso['note'] = $note;
                    $insertso['staff_note'] = $staff_note;
                    $insertso['total'] = $total;
                    $insertso['product_discount'] = $productdiscount;
                    $insertso['total_discount'] = $totaldiscount;
                    $insertso['order_discount'] = $orderdiscount;
                    $insertso['product_tax'] = $soproducttax;
                    $insertso['order_tax'] = $ordertax;
                    $insertso['adv_tax'] = $total_adv_tax;
                    $insertso['total_tax'] = $totaltax;
                    $insertso['shipping'] = $shipping;
                    $insertso['grand_total'] = $groudtotal;
                    $insertso['sale_status'] = "completed";
                    $insertso['payment_status'] = "pending";
                    $insertso['created_by'] = $this->session->userdata('user_id');
                    $insertso['total_items'] = $totalitem;
                    $insertso['hash'] = hash('sha256', microtime() . mt_rand());
                    $insertso['payment_terms'] = $payment_term;
                    $insertso['po_date'] = $po_date;
                    $insertso['dc_num'] = $dc_number;
                    $insertso['so_id'] = $so_id;
                    $insertso['soc_id'] = $socid;
                    $sendvalue = $this->salesorder_model->salecreated($insertso,$soitems);
                    $this->so_status($so_id);
                }
            }
        }
        echo json_encode($sendvalue);
    }
    public function batches(){
        $pid = $this->input->post('pid');
        $wid = $this->input->post('wid');
        $this->db->select('expiry,quantity_balance as qb,batch as code');
        $this->db->from('sma_purchase_items');
        $this->db->where('product_id',$pid);
        $this->db->where('warehouse_id',$wid);
        $this->db->where('quantity_balance != ','0.0000');
        $sbatchs = $this->db->get();
        $batchs = $sbatchs->result();
        $batchhtml = '<option>Select Batch</option>';
        foreach($batchs as $batch){
            // print_r($batch);
            $batchhtml .= '<option value="'.$batch->code.'" '; 
            $batchhtml .= ' >'.$batch->code.' (Expiry: '.$batch->expiry.', Available Qty: '.$batch->qb.')</option>';
        }
        $sendvalue['ebatchs'] = $batchs;
        $sendvalue['htmlbatchs'] = $batchhtml;
        echo json_encode($sendvalue);
    }
    public function productslist($term = NULL, $limit = NULL){
        $supplier_id = $this->input->get('supplier_id');
        $warehouse_id = $this->input->get('warehouse_id');
        $suown_companypplier_id = $this->input->get('own_company');
        if ($this->input->get('term')) {
            $term = $this->input->get('term', TRUE);
        }
        $limit = $this->input->get('limit', TRUE);
        $rows['results'] = $this->salesorder_model->productslist($term, $limit, $supplier_id, $warehouse_id, $suown_companypplier_id);
        $this->sma->send_json($rows);
    }
    public function additem(){

        $sendvalue['status'] = false;
        $sendvalue['message'] = 'no';
        $id = $this->input->post('soid');
        $this->db->select('*');
        $this->db->from('sma_sales_orders_tb');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows()>0){
            $salesdetail = $q->result()[0];
            $product_id = $this->input->post('product');
            $qty = $this->input->post('qty');
            $this->db->select('
                sma_products.id,
                sma_units.id as unit_id,
                sma_units.code as unit_code,
                sma_units.name as unit_name
            ');
            $this->db->from('sma_products');
            $this->db->join('sma_units','sma_units.id = sma_products.unit','left');
            $this->db->where('sma_products.id',$product_id);
            $p_q = $this->db->get();
            if($p_q->num_rows() > 0){
                $productdetail = $p_q->result()[0];

                $item['product_id'] = $product_id;
                $item['supplier_id'] = $salesdetail->supplier_id;
                $item['warehouse_id'] = $salesdetail->warehouse_id;
                $item['quantity'] = $qty;
                $item['unit'] = $productdetail->unit_name;
                $item['unit_code'] = $productdetail->unit_code;
                $item['status'] = 'pending';
                $item['so_id'] = $id;
                if($this->db->insert('sma_sales_order_items', $item)){
                    
                }
                $this->so_status($id);
                // $this->db->insert('sma_sale_items', $item);
                $sendvalue['message'] = 'Item Add Successfully';
                $sendvalue['status'] = true;

            }
            else{
                $sendvalue['message'] = 'Invalid Product';

            }
        }
        else{
            $sendvalue['message'] = 'Invalid Sale';
        }
        echo json_encode($sendvalue);
    }
    public function getinvoicenumber(){
        $sendvalue = '';
        $id = $this->input->post('owncom');
        $this->db->select('id');
        $this->db->from('sma_own_companies');
        $this->db->where('id = '.$id.' AND auto_invoice_gen = 1');
        $q2 = $this->db->get();
        if($q2->num_rows() > 0){
            $this->db->select('MAX(reference_no) as ref');
            $this->db->from('sma_sales');
            $this->db->where("
                own_company = ".$id." AND 
                reference_no REGEXP '^[0-9]+$' AND 
                reference_no < 100000
            ");
            $q = $this->db->get();
            if($q->num_rows()>0){
                $r = $q->result();
                $sendvalue = $r[0]->ref+1;
            }
        }
        echo $sendvalue;

    }
    public function autoinvoicecheck(){
        $sendvalue = '';
        $id = $this->input->post('owncom');
        $this->db->select('id');
        $this->db->from('sma_own_companies');
        $this->db->where('id = '.$id.' AND auto_invoice_gen = 1');
        $q2 = $this->db->get();
        if($q2->num_rows() > 0){
            echo 'true';
        }
        else{
            echo 'false';
        }
        
    }
    public function pocheck(){
        $sendvalue['status'] = false;
        $po = $this->input->post('po_number');
        $this->db->select('id');
        $this->db->from('sma_sales_orders_tb');
        $this->db->where('po_number',$po);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $sendvalue['status'] = true;
            $sendvalue['message'] = "This PO Number's SO Already Generated!";
        }
        echo json_encode($sendvalue);
    }
}