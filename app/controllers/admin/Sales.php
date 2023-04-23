<?php defined('BASEPATH') or exit('No direct script access allowed');


class Sales extends MY_Controller
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
        $this->lang->admin_load('sales', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('sales_model');
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;

        $this->load->admin_model('general_model');
        $this->load->admin_model('price_model');
    }
    public function index(){


        $this->data['warehouse'] = $this->input->get('warehouse');
        $this->data['supplier'] = $this->input->get('supplier');
        $this->data['customer'] = $this->input->get('customer');
        $this->data['own_company'] = $this->input->get('own_company');
        $this->data['start_date'] = $this->input->get('start_date');
        $this->data['end_date'] = $this->input->get('end_date');

        $this->data['warehouses'] = $this->general_model->GetAllWarehouses();
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $this->data['customers'] = $this->general_model->GetAllCustomers();
        $this->data['owncompanies'] = $this->general_model->GetAllOwnCompanies();



        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('sales')));
        $meta = array('page_title' => lang('sales'), 'bc' => $bc);
        $this->page_construct2('sales/index', $meta, $this->data);
    }

    public function get_lists(){
        // Count Total Rows
        $this->db->from('sales');
        $totalq = $this->db->get();
        $this->runquery_sales('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_sales();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = "";
            $sal_type = "Direct Sale";
            if($row->sale_type == 1){
                $sal_type = "POS Sale";
            }
            else if($row->sale_type == 2){
                $sal_type = "Website Sale";
            }
            else if($row->sale_type == 3){
                $sal_type = "Call Sale";
            }
            else if($row->sale_type == 4){
                $sal_type = "Email Sale";
            }

            $button = '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini printBtn" data-id="'.$row->id.'" >Print Invoice</button>';
            $button .= '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url('admin/sales/detail/'.$row->id).'" >Detail</a>';
            if ($this->Owner) {
                $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            }

            $payment_method = "";
            $payment_rows = $this->db->select('paid_by')->from('sma_payments')->where('sale_id',$row->id)->get()->result();
            foreach($payment_rows as $key => $payment_row){
                if($key > 0){
                    $payment_method .= ",";
                }
                $payment_method .= $payment_row->paid_by;
            }
            $data[] = array(
                $row->date,
                $row->reference_no,
                $row->so_ref,
                $row->po_number,
                $row->customer_name,
                $row->customer_phone,
                $row->own_company,
                $row->warehosue_name,
                decimalallow($row->grand_total,2),
                decimalallow($row->paid,2),
                decimalallow($row->grand_total-$row->paid,2),
                $payment_method,
                $row->payment_status,
                $row->sale_status,
                $row->first_name.' '.$row->last_name,
                $sal_type,
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
    public function runquery_sales($onlycoun = "no"){
        $id = $this->input->post('id');
        $column_search = array(
            'sales.date',
            'sales.reference_no',
            'sales.po_number',
            'sales.grand_total',
            'sales.paid',
            'sales.payment_status',
            'sales.sale_status',
            'own_companies.companyname',
            'customer.name',
            'customer.phone',
            'warehouses.name',
            'so.ref_no'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('sales.id as id');
        }
        else{
            $this->db->select('
                sales.date,
                sales.reference_no,
                so.ref_no as so_ref,
                sales.po_number,
                customer.name as customer_name,
                customer.phone as customer_phone,
                own_companies.companyname as own_company,
                warehouses.name as warehosue_name,
                sales.grand_total,
                sales.paid,
                0 as balance,
                sales.payment_status,
                sales.sale_status,
                sales.id,
                sales.sale_type,
                sales.payment_method,
                u.first_name,
                u.last_name
            ');
        }
        $this->db->from('sales as sales');
        $this->db->join('companies as customer','customer.id = sales.customer_id','left');
        $this->db->join('own_companies as own_companies','own_companies.id = sales.own_company','left');
        $this->db->join('sales_orders_tb as so','so.id = sales.so_id','left');
        $this->db->join('warehouses as warehouses','warehouses.id = sales.warehouse_id','left');
        $this->db->join('users as u','u.id = sales.created_by','left');
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
    public function detail($id = null){
        if($id != ""){
            $this->sma->checkPermissions('index');
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $inv = $this->sales_model->getInvoiceByID($id);
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
            $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
            $this->data['etalier'] = $this->site->getCompanyByID($inv->etalier_id);
            $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
            $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
            $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
            $this->data['created_by'] = $this->site->getUser($inv->created_by);
            $this->data['updated_by'] = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
            $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
            $this->data['inv'] = $inv;
            $this->data['rows'] = $this->sales_model->getAllInvoiceItems($id);
            $this->data['return_sale'] = $this->sales_model->getAllReturnSale($id);
            $this->data['return_rows'] = $this->sales_model->getAllReturnItems($id);


            $this->data['addresslist'] = $this->getCustomerAddressList($inv->customer_id);
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['units'] = $this->site->getAllBaseUnits();
            $this->data['own_company'] = $this->site->getAllown_companies();
            $this->data['suppliers'] = $this->site->getAllCompanies('supplier');
            $this->data['lcustomers'] = $this->site->getAllCompanies('customer');
            $this->data['billerslist'] = $this->site->getAllCompaniesBiller('biller');
    
            // echo '<pre>';
            // print_r($this->data);
            // exit();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('view')));
            $meta = array('page_title' => 'Invoice No: '.$inv->reference_no.' '.lang('view_sales_details'), 'bc' => $bc);
            $this->page_construct2('sales/detail', $meta, $this->data);
        }
        else{
            redirect(admin_url('sales'));
        }
    }
    public function opened(){

        $this->db->select('
            s.*
        ');
        $this->db->from('suspended_bills as s');
        $this->data['sales'] = $this->db->get()->result();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Open Sales')));
        $meta = array('page_title' => lang('Open Sales'), 'bc' => $bc);
        $this->page_construct2('sales/opend', $meta, $this->data);
    }
    public function open_delete(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->delete('suspended_items', array('suspend_id ' => $id));
                $this->db->delete('suspended_bills', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Open sale delete successfully!";
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
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['units'] = $this->site->getAllBaseUnits();

        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['own_company'] = $this->site->getAllown_companies();
        $this->data['lcustomers'] = $this->site->getAllCompanies('customer');
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('add_sale')));
        $meta = array('page_title' => lang('add_sale'), 'bc' => $bc);
        $this->page_construct2('sales/add', $meta, $this->data);
    }
    public function submit($quote_id = null){
        $sendvalue['status'] = false;
        $sendvalue['message'] = '';
        $sale_id = $this->input->get('sale_id') ? $this->input->get('sale_id') : NULL;
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('customer', lang("customer"), 'required');


        // $this->form_validation->set_rules('biller', lang("biller"), 'required');
        // $this->form_validation->set_rules('sale_status', lang("sale_status"), 'required');
        // $this->form_validation->set_rules('payment_status', lang("payment_status"), 'required');

        if ($this->form_validation->run() == true) {
            $own_company = $this->input->post('own_company');
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
                    $reference = $r[0]->ref+1;
                }
                else{
                    $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
                }
            }
            else{
                $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
            }
            $check_reference_already_exits = $this->sales_model->check_reference_already_exits($reference);

            if ($check_reference_already_exits) {
                $sendvalue['message'] = "Invoie Number Already Exist" . ' (' . $reference . ')';
                echo json_encode($sendvalue);
                exit();

            }


            if ($this->Owner || $this->Admin) {
                $date = $this->input->post('date');
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $warehouse_id = $this->input->post('warehouse');
            if($warehouse_id == ""){
                $sendvalue['message'] = "Select Warehosue";
                echo json_encode($sendvalue);
                exit();
            }
            $customer_id = $this->input->post('customer');
            $deliveryaddress = $this->input->post('deliveryaddress');
            // $biller_id = $this->input->post('biller');
            $biller_id = 48;
            $total_items = $this->input->post('total_items');
            $etaliers = 0;
            // $etaliers = $this->input->post('etaliers');
            $supplier = $this->input->post('supplier');
            $sale_status = 'completed';
            // $sale_status = $this->input->post('sale_status');
            $payment_status = 'pending';
            // $payment_status = $this->input->post('payment_status');
            $payment_term = $this->input->post('payment_term');
            $due_date = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days', strtotime($date))) : null;
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = !empty($customer_details->company) && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = !empty($biller_details->company) && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
            $quote_id = $this->input->post('quote_id') ? $this->input->post('quote_id') : null;
            
            $po_number = $this->input->post('po_number');
            $cartidiage = $this->input->post('cartidiage');


            $total = 0;
            $advtax_total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $digital = FALSE;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            $counter = sizeof($_POST['product_code']);
            for ($r = 0; $r < $i; $r++) {

                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $company_code = $_POST['company_code'][$r];

                $product_price = $_POST['purchase_price'][$r];
                $product_mrp = $_POST['purchase_mrp'][$r];
                $product_dropship = $_POST['purchase_dropship'][$r];
                $product_dropship = $_POST['purchase_dropship'][$r];
                $product_crossdock = $_POST['purchase_crossdock'][$r];
                $expiry = $_POST['expiry'][$r];
                $batch = $_POST['batch_number'][$r];
                $product_purchase_id = $_POST['product_purchase_id'][$r];
                $discount_one = $_POST['discount_one_' . $counter];
                $discount_two =  $_POST['discount_two_' . $counter];
                $discount_three =  $_POST['discount_three_' . $counter];
                $advtax =  $_POST['advtax'][$r];
                $advtax_total += $advtax;
                $product_tax_final =  isset($_POST['product_tax_' . $counter]) ?  str_replace(',', '',  $_POST['product_tax_' . $counter]) : 0;
                $item_name = $_POST['product_name'][$r];
                $further_tax =  isset($_POST['further_tax_' . $counter]) ? $_POST['further_tax_' . $counter] : 0;
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' && $_POST['product_option'][$r] != 'null' ? $_POST['product_option'][$r] : null;
                $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
                $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_serial = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $fed_tax =  $_POST['fed_tax_' . $counter] * $item_unit_quantity;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['product_base_quantity'][$r];
                $remain_quantity = $_POST['remain_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->sales_model->getProductByCode($item_code) : null;
                    if ($item_type == 'digital') {
                        $digital = TRUE;
                    }

                    $pr_discount = $this->site->calculateDiscount($item_discount, $unit_price);
                    $unit_price = $this->sma->formatDecimal($unit_price);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {

                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax = $ctax['amount'];
                        $tax = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $unit_price;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $product_tax_final + $further_tax + $fed_tax+$advtax;
                    $subtotal = ((($item_net_price * $item_unit_quantity) + $product_tax_final + $further_tax + $fed_tax+$advtax) - $item_discount);

                    $unit = $this->site->getUnitByID($item_unit);

                    $product = array(
                        'product_id' => $item_id,
                        'product_code' => $item_code,
                        'company_code' => $company_code,
                        'product_name' => $item_name,
                        'product_type' => $item_type,
                        'option_id' => $item_option,
                        'net_unit_price' => $item_net_price,
                        'unit_price' => $this->sma->formatDecimal($item_net_price),
                        'quantity' => $item_quantity,
                        'product_unit_id' => $unit ? $unit->id : NULL,
                        'product_unit_code' => $unit ? $unit->code : NULL,
                        'unit_quantity' => $item_unit_quantity,
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $item_tax_rate,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'serial_no' => $item_serial,
                        'real_unit_price' => $real_unit_price,
                        'product_price' => $product_price,
                        'mrp' => $product_mrp,
                        'consignment' => $product_price,
                        'dropship' => $product_dropship,
                        'crossdock' => $product_crossdock,
                        'expiry' => $expiry,
                        'batch' => $batch,
                        'discount_one' => $discount_one,
                        'discount_two' => $discount_two,
                        'discount_three' => $discount_three,
                        'further_tax' => $further_tax,
                        'fed_tax' => $fed_tax,
                        'adv_tax' => $advtax,

                    );

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal((($subtotal)), 4);
                }
                $counter--;
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            $CheckDuplicateInvoice = $this->sales_model->CheckDuplicateInvoice($reference);

            if ($CheckDuplicateInvoice == true) {
                $this->form_validation->set_rules('CheckDuplicateInvoice', lang("Duplicate Invoice Found"), 'required');
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            $data = array(
                'date' => $date,
                'reference_no' => $reference,
                'supplier_id' => $supplier,
                'own_company' => $own_company,
                'po_number' => $po_number,
                'customer_id' => $customer_id,
                'customer_address_id' => $deliveryaddress,
                'customer' => $customer,
                'etalier_id' => $etaliers,
                'biller_id' => $biller_id,
                'biller' => $biller,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'staff_note' => $staff_note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $this->input->post('order_discount'),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'total_items' => $total_items,
                'sale_status' => $sale_status,
                'payment_status' => $payment_status,
                'payment_status' => $payment_status,
                'payment_term' => '',
                'due_date' => $due_date,
                'adv_tax' => $advtax_total,
                'paid' => 0,
                'created_by' => $this->session->userdata('user_id'),
                'hash' => hash('sha256', microtime() . mt_rand()),
                'payment_terms' => $this->input->post('payment_term'),
                'po_date' => $this->input->post('po_date'),
                'dc_num' => $this->input->post('dc_number'),
                'cartdiage' => $this->input->post('cartidiage')
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            //// Yaha se dekhna hy

            if ($payment_status == 'partial' || $payment_status == 'paid') {
                if ($this->input->post('paid_by') == 'deposit') {
                    if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                        
                        $sendvalue['message'] = lang("amount_greater_than_deposit");
                        echo json_encode($sendvalue);
                        exit();
    

                    }
                }
                if ($this->input->post('paid_by') == 'gift_card') {
                    $gc = $this->site->getGiftCardByNO($this->input->post('gift_card_no'));
                    $amount_paying = $grand_total >= $gc->balance ? $gc->balance : $grand_total;
                    $gc_balance = $gc->balance - $amount_paying;
                    $payment = array(
                        'date' => $date,
                        'reference_no' => $this->input->post('payment_reference_no'),
                        'amount' => $this->sma->formatDecimal($amount_paying),
                        'paid_by' => $this->input->post('paid_by'),
                        'cheque_no' => $this->input->post('cheque_no'),
                        'cc_no' => $this->input->post('gift_card_no'),
                        'cc_holder' => $this->input->post('pcc_holder'),
                        'cc_month' => $this->input->post('pcc_month'),
                        'cc_year' => $this->input->post('pcc_year'),
                        'cc_type' => $this->input->post('pcc_type'),
                        'created_by' => $this->session->userdata('user_id'),
                        'note' => $this->input->post('payment_note'),
                        'type' => 'received',
                        'gc_balance' => $gc_balance,
                    );
                } else {
                    $payment = array(
                        'date' => $date,
                        'reference_no' => $this->input->post('payment_reference_no'),
                        'amount' => $this->sma->formatDecimal($this->input->post('amount-paid')),
                        'paid_by' => $this->input->post('paid_by'),
                        'cheque_no' => $this->input->post('cheque_no'),
                        'cc_no' => $this->input->post('pcc_no'),
                        'cc_holder' => $this->input->post('pcc_holder'),
                        'cc_month' => $this->input->post('pcc_month'),
                        'cc_year' => $this->input->post('pcc_year'),
                        'cc_type' => $this->input->post('pcc_type'),
                        'created_by' => $this->session->userdata('user_id'),
                        'note' => $this->input->post('payment_note'),
                        'type' => 'received',
                    );
                }
            } else {
                $payment = array();
            }

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
                    $sendvalue['message'] = $error;
                    echo json_encode($sendvalue);
                    exit();
                }
                $photo = $this->upload->file_name;
                $data['attachment'] = $photo;
            }

            foreach ($products as $current_key => $current_array) {
                foreach ($products as $search_key => $search_array) {
                    if (($search_array['product_id'] == $current_array['product_id']) && ($search_array['batch'] == $current_array['batch'])) {
                        if ($search_key != $current_key) {
                            if ($search_key > 0) {
                                $sendvalue['message'] = "Duplicate Batch in same product selected" . ' (' . $reference . ')';
                                echo json_encode($sendvalue);
                                exit();
                            }
                        }
                    }
                }
            }

        }
        if ($this->form_validation->run() == true && $this->sales_model->addSale($data, $products, $payment)) {
            $sendvalue['message'] = lang("sale_added");
            $sendvalue['status'] = true;
        } else {
            $sendvalue['message'] = validation_errors();
        }
        echo json_encode($sendvalue);
    }
    public function add_payment($id = null){
        $sendvalue['status'] = false;
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $sale = $this->sales_model->getInvoiceByID($id);
        if ($sale->payment_status == 'paid' && $sale->grand_total == $sale->paid) {
            $sendvalue['message'] = lang("sale_already_paid");
        }
        else{
            $this->data['inv'] = $sale;
            $this->data['payment_ref'] = ''; //$this->site->getReference('ppay');
            $sendvalue['html'] = $this->load->view($this->theme . 'sales/add_payment', $this->data,true);
            $sendvalue['status'] = true;
        }
        echo json_encode($sendvalue);
    }
    public function add_payment_submit($id = null){
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $sale = $this->sales_model->getInvoiceByID($id);
        if ($sale->payment_status == 'paid' && $sale->grand_total == $sale->paid) {
            $this->session->set_flashdata('error', lang("sale_already_paid"));
            $this->sma->md();
        }
        //$this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if($this->input->post('pcc_status') == 1 && $this->input->post('cprno') == ""){
                $this->session->set_flashdata('error', 'Enter CPR No');
                redirect($_SERVER["HTTP_REFERER"]);
            }
            else{
                if ($this->input->post('paid_by') == 'deposit') {
                    $sale = $this->sales_model->getInvoiceByID($this->input->post('sale_id'));
                    $customer_id = $sale->customer_id;
                    if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                        $this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                } else {
                    $customer_id = null;
                }
            }
            // $date = $this->sma->fld(trim($this->input->post('date')));
            $date = $this->input->post('date').' '.date('H:i:s');
            // if ($this->Owner || $this->Admin) {
            // } else {
            //     $date = date('Y-m-d H:i:s');
            // }
            $hold_amount = 0;
            $paid_amount = $this->input->post('amount-paid');
            $payment = array(
                'date' => $date,
                'sale_id' => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay'),
                'amount' => $paid_amount,
                'hold_amount' => $hold_amount,
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
                'type' => 'received',
                'status' => $this->input->post('pcc_status'),
                'cpr_no' => $this->input->post('cprno'),
                'credit_no_per' => $this->input->post('pcnpre')
            );

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->sales_model->addPayment($payment, $customer_id)) {
            if ($sale->shop) {
                $this->load->library('sms');
                $this->sms->paymentReceived($sale->id, $payment['reference_no'], $payment['amount']);
            }
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        else {
            $this->session->set_flashdata('message', "Payment add failed");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function edit_payment($id = null){
        $sendvalue['status'] = false;
        $this->load->helper('security');
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $this->data['payment'] = $this->sales_model->getPaymentByID($id);
        $sendvalue['html'] = $this->load->view($this->theme . 'sales/edit_payment', $this->data,true);
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
            'sale_id' => $this->input->post('sale_id'),
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

        if ($this->sales_model->updatePayment($id, $payment)) {

            $this->session->set_flashdata('message', lang("payment_updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->session->set_flashdata('message', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function print_slip(){
        $sendvalue['status'] = false;
        $id = $this->input->get('id');
        $session_user = $this->db->select('id,first_name,last_name')->from('users')->where('username',$_SESSION['username'])->get()->row();

        $printer = $this->site->getPrinterByUser($session_user->id);
        if($printer){
            $sale = $this->db->select('*')->from('sales')->where('id',$id)->get()->row();
            $sitems = $this->db->select('*')->from('sale_items')->where('sale_id',$id)->get()->result();
            $owncompany = $this->db->select('*')->from('own_companies')->where('id',5)->get()->row();
            $payments = $this->db->select('*')->from('payments')->where('sale_id',5)->get()->result();
            $user = $this->db->select('id,first_name,last_name')->from('users')->where('id',$sale->created_by)->get()->row();
            $created_by = $user->first_name.' '.$user->last_name;
            $customerdata = $this->db->select('id,name,phone,email,address')->from('companies')->where('id',$sale->customer_id)->get()->row();
            $store['name'] = $owncompany->companyname;
            $store['address1'] = $owncompany->registeraddress;
            $store['receipt_header'] = $owncompany->slip_header;
            $store['receipt_footer'] = $owncompany->slip_footer;
            $store['city'] = '';
            $store['phone'] = $owncompany->mobile;
            $store['ntn'] = $owncompany->ntn;
            $store['strn'] = $owncompany->strn;
            $payableamount = $sale->payableamount;
            $changeamount = $payableamount-$sale->grand_total;
            if($changeamount < 0){
                $changeamount = 0;
            }
            $a = array(
                'customer' => $customerdata,
                'printer' => $printer,
                'store' => $store,
                'sale' => $sale,
                'items' => $sitems,
                'payments' => $payments,
                'payableamount' => $payableamount,
                'change_amount' => $changeamount,
                'created_by' => $created_by
            );
            $a = json_encode($a);
            $sendvalue['url'] = "http://localhost/rhoprinter/printers/print_receipt?data=".$a;
            $a2 = array(
                'printer' => $printer
            );
            $a2 = json_encode($a2);
            $a2 = urlencode($a2);
            $sendvalue['url2'] = "http://localhost/rhoprinter/printers/print_receipt?data=".$a2;
            $sendvalue['form_data'] = $a;
            $sendvalue['print'] = true;
            $sendvalue['status'] = true;
            $sendvalue['message'] = "Printer command sent.";
        }    
        else{
            $sendvalue['message'] = "Printr not found";
        }
        echo json_encode($sendvalue);
    }




    public function edit_payment_old($id = null){
        $this->sma->checkPermissions('edit', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $payment = $this->sales_model->getPaymentByID($id);
        if ($payment->paid_by == 'ppp' || $payment->paid_by == 'stripe' || $payment->paid_by == 'paypal' || $payment->paid_by == 'skrill') {
            $this->session->set_flashdata('error', lang('x_edit_payment'));
            $this->sma->md();
        }
        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if($this->input->post('pcc_status') == 1 && $this->input->post('cprno') == ""){
                $this->session->set_flashdata('error', 'Enter CPR No');
                redirect($_SERVER["HTTP_REFERER"]);
            }
            else{
                if ($this->input->post('paid_by') == 'deposit') {
                    $sale = $this->sales_model->getInvoiceByID($this->input->post('sale_id'));
                    $customer_id = $sale->customer_id;
                    $amount = $this->input->post('amount-paid') - $payment->amount;
                    if (!$this->site->check_customer_deposit($customer_id, $amount)) {
                        $this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                } else {
                    $customer_id = null;
                }
            }
            $date = $this->sma->fld(trim($this->input->post('date')));
            // if ($this->Owner || $this->Admin) {
            // } else {
            //     $date = $payment->date;
            // }
            $hold_amount = 0;
            $paid_amount = $this->input->post('amount-paid');
            $payment = array(
                'date' => $date,
                'sale_id' => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount' => $paid_amount,
                'hold_amount' => $hold_amount,
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
                'status' => $this->input->post('pcc_status'),
                'cpr_no' => $this->input->post('cprno'),
                'credit_no_per' => $this->input->post('pcnpre')
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);

        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->sales_model->updatePayment($id, $payment, $customer_id)) {
            $this->session->set_flashdata('message', lang("payment_updated"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['payment'] = $payment;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'sales/edit_payment', $this->data);
        }
    }
    
    
    
    
    
    
    //  Old Inex
    public function add_old($quote_id = null){
        $this->sma->checkPermissions();
        $sale_id = $this->input->get('sale_id') ? $this->input->get('sale_id') : NULL;
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('customer', lang("customer"), 'required');
        $this->form_validation->set_rules('biller', lang("biller"), 'required');
        $this->form_validation->set_rules('sale_status', lang("sale_status"), 'required');
        $this->form_validation->set_rules('payment_status', lang("payment_status"), 'required');
        if ($this->form_validation->run() == true) {
            $own_company = $this->input->post('own_company');
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
                    $reference = $r[0]->ref+1;
                }
                else{
                    $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
                }
            }
            else{
                $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
            }
            $check_reference_already_exits = $this->sales_model->check_reference_already_exits($reference);

            if ($check_reference_already_exits) {
                $this->session->set_flashdata('error', "Invoie Number Already Exist" . ' (' . $reference . ')');
                redirect($_SERVER["HTTP_REFERER"]);
            }


            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $deliveryaddress = $this->input->post('deliveryaddress');
            $biller_id = $this->input->post('biller');
            $total_items = $this->input->post('total_items');
            $etaliers = $this->input->post('etaliers');
            $supplier = $this->input->post('supplier');
            $sale_status = $this->input->post('sale_status');
            $payment_status = $this->input->post('payment_status');
            $payment_term = $this->input->post('payment_term');
            $due_date = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days', strtotime($date))) : null;
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = !empty($customer_details->company) && $customer_details->company != '-' ? $customer_details->company : $customer_details->name;
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = !empty($biller_details->company) && $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));
            $quote_id = $this->input->post('quote_id') ? $this->input->post('quote_id') : null;
            
            $po_number = $this->input->post('po_number');
            $cartidiage = $this->input->post('cartidiage');


            $total = 0;
            $advtax_total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $digital = FALSE;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            $i = isset($_POST['product_code']) ? sizeof($_POST['product_code']) : 0;
            $counter = sizeof($_POST['product_code']);
            for ($r = 0; $r < $i; $r++) {

                $item_id = $_POST['product_id'][$r];
                $item_type = $_POST['product_type'][$r];
                $item_code = $_POST['product_code'][$r];
                $company_code = $_POST['company_code'][$r];

                $product_price = $_POST['purchase_price'][$r];
                $product_mrp = $_POST['purchase_mrp'][$r];
                $product_dropship = $_POST['purchase_dropship'][$r];
                $product_dropship = $_POST['purchase_dropship'][$r];
                $product_crossdock = $_POST['purchase_crossdock'][$r];
                $expiry = $_POST['expiry'][$r];
                $batch = $_POST['batch_number'][$r];
                $product_purchase_id = $_POST['product_purchase_id'][$r];
                $discount_one = $_POST['discount_one_' . $counter];
                $discount_two =  $_POST['discount_two_' . $counter];
                $discount_three =  $_POST['discount_three_' . $counter];
                $advtax =  $_POST['advtax'][$r];
                $advtax_total += $advtax;
                $product_tax_final =  isset($_POST['product_tax_' . $counter]) ?  str_replace(',', '',  $_POST['product_tax_' . $counter]) : 0;
                $item_name = $_POST['product_name'][$r];
                $further_tax =  isset($_POST['further_tax_' . $counter]) ? $_POST['further_tax_' . $counter] : 0;
                $item_option = isset($_POST['product_option'][$r]) && $_POST['product_option'][$r] != 'false' && $_POST['product_option'][$r] != 'null' ? $_POST['product_option'][$r] : null;
                $real_unit_price = $this->sma->formatDecimal($_POST['real_unit_price'][$r]);
                $unit_price = $this->sma->formatDecimal($_POST['unit_price'][$r]);
                $item_unit_quantity = $_POST['quantity'][$r];
                $item_serial = isset($_POST['serial'][$r]) ? $_POST['serial'][$r] : '';
                $item_tax_rate = isset($_POST['product_tax'][$r]) ? $_POST['product_tax'][$r] : null;
                $fed_tax =  $_POST['fed_tax_' . $counter] * $item_unit_quantity;
                $item_discount = isset($_POST['product_discount'][$r]) ? $_POST['product_discount'][$r] : null;
                $item_unit = $_POST['product_unit'][$r];
                $item_quantity = $_POST['product_base_quantity'][$r];
                $remain_quantity = $_POST['remain_quantity'][$r];

                if (isset($item_code) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
                    $product_details = $item_type != 'manual' ? $this->sales_model->getProductByCode($item_code) : null;
                    if ($item_type == 'digital') {
                        $digital = TRUE;
                    }

                    $pr_discount = $this->site->calculateDiscount($item_discount, $unit_price);
                    $unit_price = $this->sma->formatDecimal($unit_price);
                    $item_net_price = $unit_price;
                    $pr_item_discount = $this->sma->formatDecimal($pr_discount);
                    $product_discount += $pr_item_discount;
                    $pr_item_tax = $item_tax = 0;
                    $tax = "";

                    if (isset($item_tax_rate) && $item_tax_rate != 0) {

                        $tax_details = $this->site->getTaxRateByID($item_tax_rate);
                        $ctax = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                        $item_tax = $ctax['amount'];
                        $tax = $ctax['tax'];
                        if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                            $item_net_price = $unit_price;
                        }
                        $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                        if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                            $total_cgst += $gst_data['cgst'];
                            $total_sgst += $gst_data['sgst'];
                            $total_igst += $gst_data['igst'];
                        }
                    }

                    $product_tax += $product_tax_final + $further_tax + $fed_tax+$advtax;
                    $subtotal = ((($item_net_price * $item_unit_quantity) + $product_tax_final + $further_tax + $fed_tax+$advtax) - $item_discount);

                    $unit = $this->site->getUnitByID($item_unit);

                    $product = array(
                        'product_id' => $item_id,
                        'product_code' => $item_code,
                        'company_code' => $company_code,
                        'product_name' => $item_name,
                        'product_type' => $item_type,
                        'option_id' => $item_option,
                        'net_unit_price' => $item_net_price,
                        'unit_price' => $this->sma->formatDecimal($item_net_price),
                        'quantity' => $item_quantity,
                        'product_unit_id' => $unit ? $unit->id : NULL,
                        'product_unit_code' => $unit ? $unit->code : NULL,
                        'unit_quantity' => $item_unit_quantity,
                        'warehouse_id' => $warehouse_id,
                        'item_tax' => $pr_item_tax,
                        'tax_rate_id' => $item_tax_rate,
                        'tax' => $tax,
                        'discount' => $item_discount,
                        'item_discount' => $pr_item_discount,
                        'subtotal' => $this->sma->formatDecimal($subtotal),
                        'serial_no' => $item_serial,
                        'real_unit_price' => $real_unit_price,
                        'product_price' => $product_price,
                        'mrp' => $product_mrp,
                        'consignment' => $product_price,
                        'dropship' => $product_dropship,
                        'crossdock' => $product_crossdock,
                        'expiry' => $expiry,
                        'batch' => $batch,
                        'discount_one' => $discount_one,
                        'discount_two' => $discount_two,
                        'discount_three' => $discount_three,
                        'further_tax' => $further_tax,
                        'fed_tax' => $fed_tax,
                        'adv_tax' => $advtax,

                    );

                    $products[] = ($product + $gst_data);
                    $total += $this->sma->formatDecimal((($subtotal)), 4);
                }
                $counter--;
            }
            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }

            $CheckDuplicateInvoice = $this->sales_model->CheckDuplicateInvoice($reference);

            if ($CheckDuplicateInvoice == true) {
                $this->form_validation->set_rules('CheckDuplicateInvoice', lang("Duplicate Invoice Found"), 'required');
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            $data = array(
                'date' => $date,
                'reference_no' => $reference,
                'supplier_id' => $supplier,
                'own_company' => $own_company,
                'po_number' => $po_number,
                'customer_id' => $customer_id,
                'customer_address_id' => $deliveryaddress,
                'customer' => $customer,
                'etalier_id' => $etaliers,
                'biller_id' => $biller_id,
                'biller' => $biller,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'staff_note' => $staff_note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $this->input->post('order_discount'),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'total_items' => $total_items,
                'sale_status' => $sale_status,
                'payment_status' => $payment_status,
                'payment_status' => $payment_status,
                'payment_term' => '',
                'due_date' => $due_date,
                'adv_tax' => $advtax_total,
                'paid' => 0,
                'created_by' => $this->session->userdata('user_id'),
                'hash' => hash('sha256', microtime() . mt_rand()),
                'payment_terms' => $this->input->post('payment_term'),
                'po_date' => $this->input->post('po_date'),
                'dc_num' => $this->input->post('dc_number'),
                'cartdiage' => $this->input->post('cartidiage')
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            //// Yaha se dekhna hy

            if ($payment_status == 'partial' || $payment_status == 'paid') {
                if ($this->input->post('paid_by') == 'deposit') {
                    if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                        $this->session->set_flashdata('error', lang("amount_greater_than_deposit"));
                        redirect($_SERVER["HTTP_REFERER"]);
                    }
                }
                if ($this->input->post('paid_by') == 'gift_card') {
                    $gc = $this->site->getGiftCardByNO($this->input->post('gift_card_no'));
                    $amount_paying = $grand_total >= $gc->balance ? $gc->balance : $grand_total;
                    $gc_balance = $gc->balance - $amount_paying;
                    $payment = array(
                        'date' => $date,
                        'reference_no' => $this->input->post('payment_reference_no'),
                        'amount' => $this->sma->formatDecimal($amount_paying),
                        'paid_by' => $this->input->post('paid_by'),
                        'cheque_no' => $this->input->post('cheque_no'),
                        'cc_no' => $this->input->post('gift_card_no'),
                        'cc_holder' => $this->input->post('pcc_holder'),
                        'cc_month' => $this->input->post('pcc_month'),
                        'cc_year' => $this->input->post('pcc_year'),
                        'cc_type' => $this->input->post('pcc_type'),
                        'created_by' => $this->session->userdata('user_id'),
                        'note' => $this->input->post('payment_note'),
                        'type' => 'received',
                        'gc_balance' => $gc_balance,
                    );
                } else {
                    $payment = array(
                        'date' => $date,
                        'reference_no' => $this->input->post('payment_reference_no'),
                        'amount' => $this->sma->formatDecimal($this->input->post('amount-paid')),
                        'paid_by' => $this->input->post('paid_by'),
                        'cheque_no' => $this->input->post('cheque_no'),
                        'cc_no' => $this->input->post('pcc_no'),
                        'cc_holder' => $this->input->post('pcc_holder'),
                        'cc_month' => $this->input->post('pcc_month'),
                        'cc_year' => $this->input->post('pcc_year'),
                        'cc_type' => $this->input->post('pcc_type'),
                        'created_by' => $this->session->userdata('user_id'),
                        'note' => $this->input->post('payment_note'),
                        'type' => 'received',
                    );
                }
            } else {
                $payment = array();
            }

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

            foreach ($products as $current_key => $current_array) {
                foreach ($products as $search_key => $search_array) {
                    if (($search_array['product_id'] == $current_array['product_id']) && ($search_array['batch'] == $current_array['batch'])) {
                        if ($search_key != $current_key) {
                            if ($search_key > 0) {
                                $this->session->set_flashdata('error', "Duplicate Batch in same product selected" . ' (' . $reference . ')');
                                redirect($_SERVER["HTTP_REFERER"]);
                            }
                        }
                    }
                }
            }

        }
        if ($this->form_validation->run() == true && $this->sales_model->addSale($data, $products, $payment)) {
            $this->session->set_userdata('remove_slls', 1);
            if ($quote_id) {
                $this->db->update('quotes', array('status' => 'completed'), array('id' => $quote_id));
            }
            $this->session->set_flashdata('message', lang("sale_added"));
            admin_redirect("sales");
        } else {

            if ($quote_id || $sale_id) {
                if ($quote_id) {
                    $this->data['quote'] = $this->sales_model->getQuoteByID($quote_id);
                    $items = $this->sales_model->getAllQuoteItems($quote_id);
                } elseif ($sale_id) {
                    $this->data['quote'] = $this->sales_model->getInvoiceByID($sale_id);
                    $items = $this->sales_model->getAllInvoiceItems($sale_id);
                }
                krsort($items);
                $c = rand(100000, 9999999);
                foreach ($items as $item) {
                    $row = $this->site->getProductByID($item->product_id);
                    if (!$row) {
                        $row = json_decode('{}');
                        $row->tax_method = 0;
                    } else {
                        unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    }
                    $row->quantity = 0;
                    $pis = $this->site->getPurchasedItems($item->product_id, $item->warehouse_id, $item->option_id);
                    if ($pis) {
                        foreach ($pis as $pi) {
                            $row->quantity += $pi->quantity_balance;
                        }
                    }
                    $row->id = $item->product_id;
                    $row->code = $item->product_code;
                    $row->name = $item->product_name;
                    $row->type = $item->product_type;
                    $row->qty = $item->quantity;
                    $row->base_quantity = $item->quantity;
                    $row->base_unit = $row->unit ? $row->unit : $item->product_unit_id;
                    $row->base_unit_price = $row->price ? $row->price : $item->unit_price;
                    $row->unit = $item->product_unit_id;
                    $row->qty = $item->unit_quantity;
                    $row->discount = $item->discount ? $item->discount : '0';
                    $row->price = $this->sma->formatDecimal($item->net_unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity));
                    $row->unit_price = $row->tax_method ? $item->unit_price + $this->sma->formatDecimal($item->item_discount / $item->quantity) + $this->sma->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
                    $row->real_unit_price = $item->real_unit_price;
                    $row->tax_rate = $item->tax_rate_id;
                    $row->serial = '';
                    $row->option = $item->option_id;
                    $options = $this->sales_model->getProductOptions($row->id, $item->warehouse_id);
                    if ($options) {
                        $option_quantity = 0;
                        foreach ($options as $option) {
                            $pis = $this->site->getPurchasedItems($row->id, $item->warehouse_id, $item->option_id);
                            if ($pis) {
                                foreach ($pis as $pi) {
                                    $option_quantity += $pi->quantity_balance;
                                }
                            }
                            if ($option->quantity > $option_quantity) {

                                $option->quantity = $option_quantity;
                            }
                        }
                    }
                    $combo_items = false;
                    if ($row->type == 'combo') {
                        $combo_items = $this->sales_model->getProductComboItems($row->id, $item->warehouse_id);
                    }
                    $units = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $ri = $this->Settings->item_addition ? $row->id : $c;

                    $pr[$ri] = array(
                        'id' => $c, 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                        'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options
                    );
                    $c++;
                }
                $this->data['quote_items'] = json_encode($pr);
            }
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['quote_id'] = $quote_id ? $quote_id : $sale_id;
            $this->data['billers'] = $this->site->getAllCompaniesBiller('biller');
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['units'] = $this->site->getAllBaseUnits();
            $this->data['own_company'] = $this->site->getAllown_companies();
            $this->data['suppliers'] = $this->site->getAllCompanies('supplier');
            $this->data['lcustomers'] = $this->site->getAllCompanies('customer');
            // print_r($this->data['lcustomers']);
            // exit();
            //$this->data['currencies'] = $this->sales_model->getAllCurrencies();
            $this->data['slnumber'] = ''; //$this->site->getReference('so');
            $this->data['payment_ref'] = ''; //$this->site->getReference('pay');
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('add_sale')));
            $meta = array('page_title' => lang('add_sale'), 'bc' => $bc);
            $this->page_construct('sales/add', $meta, $this->data);
        }
    }
    public function pdf($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/pdf', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
            $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
        }
    }
    public function dcpdf($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/dcpdf', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
            $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
        }
    }
    public function salestaxpdf1($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['invoicestatus'] = $this->input->get('invoicestatus');

        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);

        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/salestaxpdf1', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        $footerhtml = "
            <div >
                Stamp and signature is not required because it's system generated invoice.
            </div>
            <div>
                Please lets us know within seven days if there is any issue in this invoice.
            </div>
            <span style='' class='page-number' >Page </span>
        ";

        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            // return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $footerhtml);
        } else {
            // $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
            $this->sma->generate_pdf($html, $name, false, $footerhtml);
        }
    }
    public function salestaxpdf1trading($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['invoicestatus'] = $this->input->get('invoicestatus');
        //Working
        
        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);
        
        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/salestaxpdf1trading', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        
        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            // return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
            return $this->sma->generate_pdf($html, $name, $save_bufffer, '<span style="" class="page-number" >Page </span>');
        } else {
            // $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
            $this->sma->generate_pdf($html, $name, false, '<span style="" class="page-number" >Page </span>');
        }
    }
    public function bill_trading($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['invoicestatus'] = $this->input->get('invoicestatus');
        //Working

        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);

        $name = lang("sale") . "_Bill_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/billingtrade', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }

        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            // return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
            return $this->sma->generate_pdf($html, $name, $save_bufffer, '<span style="" class="page-number" >Page </span>');
        } else {
            // $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
            $this->sma->generate_pdf($html, $name, false, '<span style="" class="page-number" >Page </span>');
        }
    }
    public function salestaxpdf2($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);
        $this->data['invoicestatus'] = $this->input->get('invoicestatus');

        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/salestaxpdf2', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }

        $footerhtml = "
            <div >
                Stamp and signature is not required because it's system generated invoice.
            </div>
        ";
        if($this->data['invoicestatus'] == "perfoma"){
            $footerhtml .= "
                <div>
                    <b style='font-weight: bolder;' >Note: </b>This is a <b style='font-weight: bolder;' >Proforma invoice</b> for price Information at the time of stock inbounding. The original invoice will be issued on the basis of the stock receiving on the delivery challan.
                </div>
            ";
        }
        else{
            $footerhtml .= "
                <div>
                    Please lets us know within seven days if there is any issue in this invoice.
                </div>
            ";
        }
        $footerhtml .= "
            <span style='' class='page-number' >Page </span>
        ";
        // $this->data['biller']->invoice_footer

        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $footerhtml);
        } else {
            $this->sma->generate_pdf($html, $name, false, $footerhtml);
        }
    }
    public function salestaxpdf2_new($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);
        $this->data['invoicestatus'] = $this->input->get('invoicestatus');

        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/salestaxpdf2_new', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }

        $footerhtml = "
            <div >
                Stamp and signature is not required because it's system generated invoice.
            </div>
        ";
        if($this->data['invoicestatus'] == "perfoma"){
            $footerhtml .= "
                <div>
                    <b style='font-weight: bolder;' >Note: </b>This is a <b style='font-weight: bolder;' >Proforma invoice</b> for price Information at the time of stock inbounding. The original invoice will be issued on the basis of the stock receiving on the delivery challan.
                </div>
            ";
        }
        else{
            $footerhtml .= "
                <div>
                    Please lets us know within seven days if there is any issue in this invoice.
                </div>
            ";
        }
        $footerhtml .= "
            <span style='' class='page-number' >Page </span>
        ";
        // $this->data['biller']->invoice_footer

        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $footerhtml);
        } else {
            $this->sma->generate_pdf($html, $name, false, $footerhtml);
        }
    }
    public function salestaxpdf3($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);

        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/salestaxpdf3', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        $footerhtml = "
            <div >
                Stamp and signature is not required because it's system generated invoice.
            </div>
            <div>
                Please lets us know within seven days if there is any issue in this invoice.
            </div>
            <span style='' class='page-number' >Page </span>
        ";

        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $footerhtml);
        } else {
            $this->sma->generate_pdf($html, $name, false, $footerhtml);
        }
    }
    public function bill_with_one_and_two_discount_pdf($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);
        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;

        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/bill_with_one_and_two_discount_pdf', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }

        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
            $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
        }
    }
    public function bill_with_one_two_and_three_discount_pdf($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);
        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/bill_with_one_two_and_three_discount_pdf', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
            $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
        }
    }
    public function Delivery_challan($id = null, $view = null, $save_bufffer = null){
        // $this->sma->checkPermissions(); 
        
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->sales_model->getInvoiceByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['user'] = $this->site->getUser($inv->created_by);
        $this->data['caddress'] = $this->getCustomerAddress($inv->customer_address_id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv'] = $inv;
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithReturn($id);

        $this->data['own_company'] = $this->site->getown_companiesByID($inv->own_company);

        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;



        //$this->data['paypal'] = $this->sales_model->getPaypalSettings();
        //$this->data['skrill'] = $this->sales_model->getSkrillSettings();
        $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/delivery_challan_pdf', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }





        if ($view) {
            $this->load->view($this->theme . 'sales/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer, $this->data['biller']->invoice_footer);
        } else {
            $this->sma->generate_pdf($html, $name, false, $this->data['biller']->invoice_footer);
        }
    }
    public function Delivery_challan2($id = null, $view = null, $save_bufffer = null){
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->db->select('
            sma_companies.name as customer_name,
            sma_companies.company as customer_company,
            sma_companies.address as customer_address,
            sma_companies.vat_no as customer_vat_no,
            sma_companies.city as customer_city,
            sma_companies.state as customer_state,
            sma_companies.postal_code as customer_postal_code,
            sma_companies.country as customer_country,
            sma_companies.phone as customer_phone,
            sma_companies.cnic as cutomer_cnic,
            sma_companies.email as customer_email,
            sma_companies.cf1 as customer_cf1,
            sma_companies.gst_no as cutomer_gst,
            own_companies.companyname as oc_companyname,
            own_companies.ntn as oc_ntn,
            own_companies.strn as oc_strn,
            own_companies.registeraddress as oc_registeraddress,
            own_companies.warehouseaddress as oc_warehosueaddress,
            own_companies.srb as oc_srb,
            own_companies.registerperson as oc_regesteperson,
            own_companies.mobile as oc_mobile,
            sma_sales.*

        ');
        $this->db->from('sma_sales');
        $this->db->join('sma_companies','sma_companies.id = sma_sales.customer_id','left');
        $this->db->join('own_companies','own_companies.id = sma_sales.own_company','left');
        $this->db->where('sma_sales.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $inv = $q->result()[0];
            $this->data['inv'] = $inv;
            
            
            $this->db->select('
                sma_products.code AS sku,
                sma_products.company_code,
                sma_products.hsn_code,
                sma_sale_items.product_name,
                sma_sale_items.mrp,
                sma_products.weight,
                sma_products.pack_size,
                sma_products.carton_size,
                sma_products.second_name,
                sma_products.details,
                sma_sale_items.batch,
                sma_sale_items.expiry,
                sma_sale_items.quantity
            ');
            $this->db->from('sma_sale_items');
            $this->db->join('sma_products','sma_products.id = sma_sale_items.product_id');
            $this->db->where('sma_sale_items.sale_id',$id);
            $this->db->where('sma_sale_items.quantity >',0);
            $this->db->order_by('sma_sale_items.product_id', 'ASC');
            $q2 = $this->db->get();
            $this->data['rows'] = $q2->result();

            // echo '<pre>';
            // print_r($data['inv']);
            // exit();
            $name = lang("sale") . "_" . str_replace('/', '_', $inv->reference_no) . ".pdf";
            $html = $this->load->view($this->theme . 'sales/delivery_challan_pdf_new', $this->data, true);
            if (!$this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            if ($view) {
                $this->load->view($this->theme . 'sales/pdf', $this->data);
            } elseif ($save_bufffer) {
                return $this->sma->generate_pdf($html, $name, $save_bufffer, '');
            } else {
                $this->sma->generate_pdf($html, $name, false, '');
            }
        }
        else{
            redirect(base_url('admin/sales'));
        }

    }
    public function combine_pdf($sales_id){
        $this->sma->checkPermissions('pdf');

        foreach ($sales_id as $id) {

            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $inv = $this->sales_model->getInvoiceByID($id);
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $this->data['barcode'] = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
            $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
            $this->data['payments'] = $this->sales_model->getPaymentsForSale($id);
            $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
            $this->data['user'] = $this->site->getUser($inv->created_by);
            $this->data['warehouse'] = $this->site->getWarehouseByID($inv->warehouse_id);
            $this->data['inv'] = $inv;
            $this->data['rows'] = $this->sales_model->getAllInvoiceItems($id);
            $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : NULL;
            $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : NULL;
            $html_data = $this->load->view($this->theme . 'sales/pdf', $this->data, true);
            if (!$this->Settings->barcode_img) {
                $html_data = preg_replace("'\<\?xml(.*)\?\>'", '', $html_data);
            }

            $html[] = array(
                'content' => $html_data,
                'footer' => $this->data['biller']->invoice_footer,
            );
        }

        $name = lang("sales") . ".pdf";
        $this->sma->generate_pdf($html, $name);
    }
    public function email($id = null){
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $inv = $this->sales_model->getInvoiceByID($id);
        $this->form_validation->set_rules('to', lang("to") . " " . lang("email"), 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', lang("subject"), 'trim|required');
        $this->form_validation->set_rules('cc', lang("cc"), 'trim|valid_emails');
        $this->form_validation->set_rules('bcc', lang("bcc"), 'trim|valid_emails');
        $this->form_validation->set_rules('note', lang("message"), 'trim');

        if ($this->form_validation->run() == true) {
            if (!$this->session->userdata('view_right')) {
                $this->sma->view_rights($inv->created_by);
            }
            $to = $this->input->post('to');
            $subject = $this->input->post('subject');
            if ($this->input->post('cc')) {
                $cc = $this->input->post('cc');
            } else {
                $cc = null;
            }
            if ($this->input->post('bcc')) {
                $bcc = $this->input->post('bcc');
            } else {
                $bcc = null;
            }
            $customer = $this->site->getCompanyByID($inv->customer_id);
            $biller = $this->site->getCompanyByID($inv->biller_id);
            $this->load->library('parser');
            $parse_data = array(
                'reference_number' => $inv->reference_no,
                'contact_person' => $customer->name,
                'company' => $customer->company && $customer->company != '-' ? '(' . $customer->company . ')' : '',
                'order_link' => $inv->shop ? shop_url('orders/' . $inv->id . '/' . ($this->loggedIn ? '' : $inv->hash)) : base_url(),
                'site_link' => base_url(),
                'site_name' => $this->Settings->site_name,
                'logo' => '<img src="' . base_url() . 'assets/uploads/logos/' . $biller->logo . '" alt="' . ($biller->company != '-' ? $biller->company : $biller->name) . '"/>',
            );
            $msg = $this->input->post('note');
            $message = $this->parser->parse_string($msg, $parse_data);
            $paypal = $this->sales_model->getPaypalSettings();
            $skrill = $this->sales_model->getSkrillSettings();
            $btn_code = '<div id="payment_buttons" class="text-center margin010">';
            if ($paypal->active == "1" && $inv->grand_total != "0.00") {
                if (trim(strtolower($customer->country)) == $biller->country) {
                    $paypal_fee = $paypal->fixed_charges + ($inv->grand_total * $paypal->extra_charges_my / 100);
                } else {
                    $paypal_fee = $paypal->fixed_charges + ($inv->grand_total * $paypal->extra_charges_other / 100);
                }
                $btn_code .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=' . $paypal->account_email . '&item_name=' . $inv->reference_no . '&item_number=' . $inv->id . '&image_url=' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '&amount=' . (($inv->grand_total - $inv->paid) + $paypal_fee) . '&no_shipping=1&no_note=1&currency_code=' . $this->default_currency->code . '&bn=FC-BuyNow&rm=2&return=' . admin_url('sales/view/' . $inv->id) . '&cancel_return=' . admin_url('sales/view/' . $inv->id) . '&notify_url=' . admin_url('payments/paypalipn') . '&custom=' . $inv->reference_no . '__' . ($inv->grand_total - $inv->paid) . '__' . $paypal_fee . '"><img src="' . base_url('assets/images/btn-paypal.png') . '" alt="Pay by PayPal"></a> ';
            }
            if ($skrill->active == "1" && $inv->grand_total != "0.00") {
                if (trim(strtolower($customer->country)) == $biller->country) {
                    $skrill_fee = $skrill->fixed_charges + ($inv->grand_total * $skrill->extra_charges_my / 100);
                } else {
                    $skrill_fee = $skrill->fixed_charges + ($inv->grand_total * $skrill->extra_charges_other / 100);
                }
                $btn_code .= ' <a href="https://www.moneybookers.com/app/payment.pl?method=get&pay_to_email=' . $skrill->account_email . '&language=EN&merchant_fields=item_name,item_number&item_name=' . $inv->reference_no . '&item_number=' . $inv->id . '&logo_url=' . base_url() . 'assets/uploads/logos/' . $this->Settings->logo . '&amount=' . (($inv->grand_total - $inv->paid) + $skrill_fee) . '&return_url=' . admin_url('sales/view/' . $inv->id) . '&cancel_url=' . admin_url('sales/view/' . $inv->id) . '&detail1_description=' . $inv->reference_no . '&detail1_text=Payment for the sale invoice ' . $inv->reference_no . ': ' . $inv->grand_total . '(+ fee: ' . $skrill_fee . ') = ' . $this->sma->formatMoney($inv->grand_total + $skrill_fee) . '&currency=' . $this->default_currency->code . '&status_url=' . admin_url('payments/skrillipn') . '"><img src="' . base_url('assets/images/btn-skrill.png') . '" alt="Pay by Skrill"></a>';
            }

            $btn_code .= '<div class="clearfix"></div></div>';
            $message = $message . $btn_code;
            $attachment = $this->pdf($id, null, 'S');

            try {
                if ($this->sma->send_email($to, $subject, $message, null, null, $attachment, $cc, $bcc)) {
                    delete_files($attachment);
                    $this->session->set_flashdata('message', lang("email_sent"));
                    admin_redirect("sales");
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('error', $e->getMessage());
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } elseif ($this->input->post('send_email')) {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->session->set_flashdata('error', $this->data['error']);
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            if (file_exists('./themes/' . $this->Settings->theme . '/admin/views/email_templates/sale.html')) {
                $sale_temp = file_get_contents('themes/' . $this->Settings->theme . '/admin/views/email_templates/sale.html');
            } else {
                $sale_temp = file_get_contents('./themes/default/admin/views/email_templates/sale.html');
            }

            $this->data['subject'] = array(
                'name' => 'subject',
                'id' => 'subject',
                'type' => 'text',
                'value' => $this->form_validation->set_value('subject', lang('invoice') . ' (' . $inv->reference_no . ') ' . lang('from') . ' ' . $this->Settings->site_name),
            );
            $this->data['note'] = array(
                'name' => 'note',
                'id' => 'note',
                'type' => 'text',
                'value' => $this->form_validation->set_value('note', $sale_temp),
            );
            $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);

            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'sales/email', $this->data);
        }
    }
    /* ------------------------------------------------------------------ */
    /* ------------------------------------------------------------------------ */
    public function edit($id = null){
        admin_redirect("sales/detail/".$id);
        exit();
    }
    /* ------------------------------- */
    public function delete_return($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->sales_model->deleteReturn($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(array('error' => 0, 'msg' => lang("return_sale_deleted")));
            }
            $this->session->set_flashdata('message', lang('return_sale_deleted'));
            admin_redirect('welcome');
        }
    }
    public function sale_actions(){
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
                        $this->sales_model->deleteSale($id);
                    }
                    $this->session->set_flashdata('message', lang("sales_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                } elseif ($this->input->post('form_action') == 'combine') {
                    $html = $this->combine_pdf($_POST['val']);
                } elseif ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('sales'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('grand_total'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('paid'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('payment_status'));
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sale = $this->sales_model->getInvoiceByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($sale->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sale->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sale->biller);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sale->customer);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sale->grand_total);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang($sale->paid));
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, lang($sale->payment_status));
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'sales_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_sale_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    /* ------------------------------- */
    public function deliveries()
    {
        $this->sma->checkPermissions();
        $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('deliveries')));
        $meta = array('page_title' => lang('deliveries'), 'bc' => $bc);
        $this->page_construct('sales/deliveries', $meta, $this->data);
    }
    public function getDeliveries()
    {
        $this->sma->checkPermissions('deliveries');
        $detail_link = anchor('admin/sales/view_delivery/$1', '<i class="fa fa-file-text-o"></i> ' . lang('delivery_details'), 'data-toggle="modal" data-target="#myModal"');
        $email_link = anchor('admin/sales/email_delivery/$1', '<i class="fa fa-envelope"></i> ' . lang('email_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $edit_link = anchor('admin/sales/edit_delivery/$1', '<i class="fa fa-edit"></i> ' . lang('edit_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $pdf_link = anchor('admin/sales/pdf_delivery/$1', '<i class="fa fa-file-pdf-o"></i> ' . lang('download_pdf'));
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_delivery") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('sales/delete_delivery/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_delivery') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $pdf_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
        </div></div>';

        $this->load->library('datatables');
        $this->datatables
            ->select("deliveries.id as id, date, do_reference_no, sale_reference_no, customer, address, status, attachment")
            ->from('deliveries')
            ->join('sale_items', 'sale_items.sale_id=deliveries.sale_id', 'left')
            ->group_by('deliveries.id');
        $this->datatables->add_column("Actions", $action, "id");

        echo $this->datatables->generate();
    }

    public function pdf_delivery($id = null, $view = null, $save_bufffer = null)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $deli = $this->sales_model->getDeliveryByID($id);

        $this->data['delivery'] = $deli;
        $sale = $this->sales_model->getInvoiceByID($deli->sale_id);
        $this->data['biller'] = $this->site->getCompanyByID($sale->biller_id);
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithDetails($deli->sale_id);
        $this->data['user'] = $this->site->getUser($deli->created_by);

        $name = lang("delivery") . "_" . str_replace('/', '_', $deli->do_reference_no) . ".pdf";
        $html = $this->load->view($this->theme . 'sales/pdf_delivery', $this->data, true);
        if (!$this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'sales/pdf_delivery', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name);
        }
    }

    public function view_delivery($id = null)
    {
        $this->sma->checkPermissions('deliveries');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $deli = $this->sales_model->getDeliveryByID($id);
        $sale = $this->sales_model->getInvoiceByID($deli->sale_id);
        if (!$sale) {
            $this->session->set_flashdata('error', lang('sale_not_found'));
            $this->sma->md();
        }
        $this->data['delivery'] = $deli;
        $this->data['biller'] = $this->site->getCompanyByID($sale->biller_id);
        $this->data['rows'] = $this->sales_model->getAllInvoiceItemsWithDetails($deli->sale_id);
        $this->data['user'] = $this->site->getUser($deli->created_by);
        $this->data['page_title'] = lang("delivery_order");

        $this->load->view($this->theme . 'sales/view_delivery', $this->data);
    }

    public function add_delivery($id = null)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $sale = $this->sales_model->getInvoiceByID($id);
        if ($sale->sale_status != 'completed') {
            $this->session->set_flashdata('error', lang('status_is_x_completed'));
            $this->sma->md();
        }

        if ($delivery = $this->sales_model->getDeliveryBySaleID($id)) {
            $this->edit_delivery($delivery->id);
        } else {

            $this->form_validation->set_rules('sale_reference_no', lang("sale_reference_no"), 'required');
            $this->form_validation->set_rules('customer', lang("customer"), 'required');
            $this->form_validation->set_rules('address', lang("address"), 'required');

            if ($this->form_validation->run() == true) {
                if ($this->Owner || $this->Admin) {
                    $date = $this->sma->fld(trim($this->input->post('date')));
                } else {
                    $date = date('Y-m-d H:i:s');
                }
                $dlDetails = array(
                    'date' => $date,
                    'sale_id' => $this->input->post('sale_id'),
                    'do_reference_no' => $this->input->post('do_reference_no') ? $this->input->post('do_reference_no') : $this->site->getReference('do'),
                    'sale_reference_no' => $this->input->post('sale_reference_no'),
                    'customer' => $this->input->post('customer'),
                    'address' => $this->input->post('address'),
                    'status' => $this->input->post('status'),
                    'delivered_by' => $this->input->post('delivered_by'),
                    'received_by' => $this->input->post('received_by'),
                    'note' => $this->sma->clear_tags($this->input->post('note')),
                    'created_by' => $this->session->userdata('user_id'),
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
                    $dlDetails['attachment'] = $photo;
                }
            } elseif ($this->input->post('add_delivery')) {
                if ($sale->shop) {
                    $this->load->library('sms');
                    $this->sms->delivering($sale->id, $dlDetails['do_reference_no']);
                }
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($this->form_validation->run() == true && $this->sales_model->addDelivery($dlDetails)) {
                $this->session->set_flashdata('message', lang("delivery_added"));
                admin_redirect("sales/deliveries");
            } else {

                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                $this->data['customer'] = $this->site->getCompanyByID($sale->customer_id);
                $this->data['address'] = $this->site->getAddressByID($sale->address_id);
                $this->data['inv'] = $sale;
                $this->data['do_reference_no'] = ''; //$this->site->getReference('do');
                $this->data['modal_js'] = $this->site->modal_js();

                $this->load->view($this->theme . 'sales/add_delivery', $this->data);
            }
        }
    }

    public function edit_delivery($id = null)
    {
        $this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('do_reference_no', lang("do_reference_no"), 'required');
        $this->form_validation->set_rules('sale_reference_no', lang("sale_reference_no"), 'required');
        $this->form_validation->set_rules('customer', lang("customer"), 'required');
        $this->form_validation->set_rules('address', lang("address"), 'required');

        if ($this->form_validation->run() == true) {

            $dlDetails = array(
                'sale_id' => $this->input->post('sale_id'),
                'do_reference_no' => $this->input->post('do_reference_no'),
                'sale_reference_no' => $this->input->post('sale_reference_no'),
                'customer' => $this->input->post('customer'),
                'address' => $this->input->post('address'),
                'status' => $this->input->post('status'),
                'delivered_by' => $this->input->post('delivered_by'),
                'received_by' => $this->input->post('received_by'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'created_by' => $this->session->userdata('user_id'),
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
                $dlDetails['attachment'] = $photo;
            }

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
                $dlDetails['date'] = $date;
            }
        } elseif ($this->input->post('edit_delivery')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->form_validation->run() == true && $this->sales_model->updateDelivery($id, $dlDetails)) {
            $this->session->set_flashdata('message', lang("delivery_updated"));
            admin_redirect("sales/deliveries");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['delivery'] = $this->sales_model->getDeliveryByID($id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'sales/edit_delivery', $this->data);
        }
    }

    public function delete_delivery($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->sales_model->deleteDelivery($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("delivery_deleted")));
        }
    }

    public function delivery_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete_delivery');
                    foreach ($_POST['val'] as $id) {
                        $this->sales_model->deleteDelivery($id);
                    }
                    $this->session->set_flashdata('message', lang("deliveries_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('deliveries'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('do_reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('sale_reference_no'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('address'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('status'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $delivery = $this->sales_model->getDeliveryByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($delivery->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $delivery->do_reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $delivery->sale_reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $delivery->customer);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $delivery->address);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, lang($delivery->status));
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);

                    $filename = 'deliveries_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_delivery_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /* -------------------------------------------------------------------------------- */

    public function payments($id = null)
    {
        $this->sma->checkPermissions(false, true);
        $this->data['payments'] = $this->sales_model->getInvoicePayments($id);
        $this->data['inv'] = $this->sales_model->getInvoiceByID($id);
        $this->load->view($this->theme . 'sales/payments', $this->data);
    }

    public function payment_note($id = null)
    {
        $this->sma->checkPermissions('payments', true);
        $payment = $this->sales_model->getPaymentByID($id);
        $inv = $this->sales_model->getInvoiceByID($payment->sale_id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['page_title'] = lang("payment_note");

        $this->load->view($this->theme . 'sales/payment_note', $this->data);
    }

    public function email_payment($id = null)
    {
        $this->sma->checkPermissions('payments', true);
        $payment = $this->sales_model->getPaymentByID($id);
        $inv = $this->sales_model->getInvoiceByID($payment->sale_id);
        $this->data['biller'] = $this->site->getCompanyByID($inv->biller_id);
        $customer = $this->site->getCompanyByID($inv->customer_id);
        if (!$customer->email) {
            $this->sma->send_json(array('msg' => lang("update_customer_email")));
        }
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['customer'] = $customer;
        $this->data['page_title'] = lang("payment_note");
        $html = $this->load->view($this->theme . 'sales/payment_note', $this->data, TRUE);

        $html = str_replace(array('<i class="fa fa-2x">&times;</i>', 'modal-', '<p>&nbsp;</p>', '<p style="border-bottom: 1px solid #666;">&nbsp;</p>', '<p>' . lang("stamp_sign") . '</p>'), '', $html);
        $html = preg_replace("/<img[^>]+\>/i", '', $html);
        // $html = '<div style="border:1px solid #DDD; padding:10px; margin:10px 0;">'.$html.'</div>';

        $this->load->library('parser');
        $parse_data = array(
            'stylesheet' => '<link href="' . $this->data['assets'] . 'styles/helpers/bootstrap.min.css" rel="stylesheet"/>',
            'name' => $customer->company && $customer->company != '-' ? $customer->company :  $customer->name,
            'email' => $customer->email,
            'heading' => lang('payment_note') . '<hr>',
            'msg' => $html,
            'site_link' => base_url(),
            'site_name' => $this->Settings->site_name,
            'logo' => '<img src="' . base_url('assets/uploads/logos/' . $this->Settings->logo) . '" alt="' . $this->Settings->site_name . '"/>'
        );
        $msg = file_get_contents('./themes/' . $this->Settings->theme . '/admin/views/email_templates/email_con.html');
        $message = $this->parser->parse_string($msg, $parse_data);
        $subject = lang('payment_note') . ' - ' . $this->Settings->site_name;

        if ($this->sma->send_email($customer->email, $subject, $message)) {
            $this->sma->send_json(array('msg' => lang("email_sent")));
        } else {
            $this->sma->send_json(array('msg' => lang("email_failed")));
        }
    }

    public function delete_payment($id = null){
        $sendvalue['status'] = false;

        $id = $this->input->post('id');
        if($id != ""){
            if ($this->sales_model->deletePayment($id)) {
                $sendvalue['message'] = "Payment deleted";
                $sendvalue['status'] = true;
            }
            else{
                $sendvalue['message'] = "Payment not delete";
            }
        }
        else{
            $sendvalue['message'] = "Invalid ID";
        }
        echo json_encode($sendvalue);
    }
    /* --------------------------------------------------------------------------------------------- */
    public function get_remain_quantity(){
        $get_selected_batch_code = $this->input->get('get_selected_batch_code');
        $get_product_bar_code = $this->input->get('get_product_bar_code');
        $get_warehouse_id = $this->input->get('get_warehouse_id');
        $customer_id = $this->input->get('customer_id');
        //echo $customer_id;
        $get_product_qty = $this->sales_model->get_product_qty($get_selected_batch_code, $get_product_bar_code, $get_warehouse_id, $customer_id);
        // echo json_encode($get_product_qty);
        $this->sma->send_json($get_product_qty);
    }
    public function get_remain_quantity_transfer2(){
        $get_selected_batch_code = $this->input->get('get_selected_batch_code');
        $get_product_bar_code = $this->input->get('get_product_bar_code');
        $get_warehouse_id = $this->input->get('get_warehouse_id');
        $get_product_qty = $this->sales_model->get_product_qty_transfer($get_selected_batch_code, $get_product_bar_code, $get_warehouse_id);
        // echo json_encode($get_product_qty);
        $this->sma->send_json($get_product_qty);
    }
    /* --------------------------------------------------------------------------------------------- */
    // PRODUCT SUGGESTION FROM SALES
    public function suggestions(){
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $customer_id = $this->input->get('customer_id', true);
        $supplier_id = $this->input->get('supplier_id', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }
        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        $warehouse = $this->site->getWarehouseByID($warehouse_id);
        $customer = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        $rows = $this->sales_model->getProductNames($sr, $warehouse_id, $supplier_id);
        $further_tax = $this->sales_model->further_tax($q);
        if ($rows) {
            $r = 0;
            foreach ($rows as $row) {
                $c = uniqid(mt_rand(), true);
                $option = false;
                $row->quantity = $row->quantity;
                $row->item_tax_method = $row->tax_method;
                $row->qty = 1;
                $row->discount = '0';
                $row->get_selected_batch_code = '0';
                $row->get_selected_purchase_id = '0';
                $row->get_selected_product_price = '0';
                $row->get_selected_product_consiment = '0';
                $row->get_selected_product_mrp = '0';
                $row->get_selected_product_dropship = '0';
                $row->get_selected_product_crossdock = '0';
                $row->get_selected_expiry = '0';
                $row->get_selected_product_batch_quantity = '0';
                $row->get_selected_fed_tax_rate = '0';
                $row->consiment = $row->price;
                $row->discount_one_checked = 'false';
                $row->discount_two_checked = 'false';
                $row->discount_three_checked = 'false';
                $row->serial = '';
                $options = $this->sales_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->sales_model->getProductOptionByID($option_id) : $options[0];
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt = json_decode('{}');
                    $opt->price = 0;
                    $option_id = FALSE;
                }
                $row->option = $option_id;
                $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                if ($pis) {
                    $row->quantity = 0;
                    foreach ($pis as $pi) {
                        $row->quantity += $pi->quantity_balance;
                    }
                }
                if ($options) {
                    $option_quantity = 0;
                    foreach ($options as $option) {
                        $pis = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
                        if ($pis) {
                            foreach ($pis as $pi) {
                                $option_quantity += $pi->quantity_balance;
                            }
                        }
                        if ($option->quantity > $option_quantity) {
                            $option->quantity = $option_quantity;
                        }
                    }
                }
                if ($this->sma->isPromo($row)) {
                    $row->price = $row->promo_price;
                } elseif ($customer->price_group_id) {
                    if ($pr_group_price = $this->site->getProductGroupPrice($row->id, $customer->price_group_id)) {
                        $row->price = $pr_group_price->price;
                    }
                } elseif ($warehouse->price_group_id) {
                    if ($pr_group_price = $this->site->getProductGroupPrice($row->id, $warehouse->price_group_id)) {
                        $row->price = $pr_group_price->price;
                    }
                }
                $row->price = $row->price + (($row->price * $customer_group->percent) / 100);
                $row->real_unit_price = $row->price;
                $row->base_quantity = 1;
                $row->base_unit = $row->unit;
                $row->base_unit_price = $row->price;
                $row->unit = $row->sale_unit ? $row->sale_unit : $row->unit;
                $row->comment = '';
                $combo_items = false;
                $row->fed_tax_rate = $row->fed_tax_rate;
                $row->further_tax = $further_tax->further_tax;


                if ($row->type == 'combo') {
                    $combo_items = $this->sales_model->getProductComboItems($row->id, $warehouse_id);
                }
                $units = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);


                $batch_list = $this->sales_model->getAllBatchNumber($row->code);
                $check_discount_list = $this->sales_model->getAllDiscount($row->id, $supplier_id);

                $sales_type = $customer->sales_type;
                $gst_no = $customer->gst_no;

                $pr[] = array(
                    'id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'category' => $row->category_id,
                    'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, 'customer_type' => $sales_type, 'gst_no' => $gst_no, 'batch_list' => $batch_list, 'check_discount_list' => $check_discount_list
                );
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    public function CheckInvoiceNumber(){
        $reference = $this->input->get('ref_no');
        $test = $this->sales_model->CheckDuplicateInvoice($reference);
        echo $test;
    }
    public function gift_cards(){
        $this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('gift_cards')));
        $meta = array('page_title' => lang('gift_cards'), 'bc' => $bc);
        $this->page_construct('sales/gift_cards', $meta, $this->data);
    }
    public function getGiftCards(){
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('gift_cards') . ".id as id, card_no, value, balance, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name) as created_by, customer, expiry", false)
            ->join('users', 'users.id=gift_cards.created_by', 'left')
            ->from("gift_cards")
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('sales/view_gift_card/$1') . "' class='tip' title='" . lang("view_gift_card") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-eye\"></i></a> <a href='" . admin_url('sales/topup_gift_card/$1') . "' class='tip' title='" . lang("topup_gift_card") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-dollar\"></i></a> <a href='" . admin_url('sales/edit_gift_card/$1') . "' class='tip' title='" . lang("edit_gift_card") . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_gift_card") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('sales/delete_gift_card/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
        echo $this->datatables->generate();
    }
    public function view_gift_card($id = null){
        $this->data['page_title'] = lang('gift_card');
        $gift_card = $this->site->getGiftCardByID($id);
        $this->data['gift_card'] = $this->site->getGiftCardByID($id);
        $this->data['customer'] = $this->site->getCompanyByID($gift_card->customer_id);
        $this->data['topups'] = $this->sales_model->getAllGCTopups($id);
        $this->load->view($this->theme . 'sales/view_gift_card', $this->data);
    }
    public function topup_gift_card($card_id){
        $this->sma->checkPermissions('add_gift_card', true);
        $card = $this->site->getGiftCardByID($card_id);
        $this->form_validation->set_rules('amount', lang("amount"), 'trim|integer|required');
        if ($this->form_validation->run() == true) {
            $data = array(
                'card_id' => $card_id,
                'amount' => $this->input->post('amount'),
                'date' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->userdata('user_id'),
            );
            $card_data['balance'] = ($this->input->post('amount') + $card->balance);
            if ($this->input->post('expiry')) {
                $card_data['expiry'] = $this->sma->fld(trim($this->input->post('expiry')));
            }
        } elseif ($this->input->post('topup')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("sales/gift_cards");
        }

        if ($this->form_validation->run() == true && $this->sales_model->topupGiftCard($data, $card_data)) {
            $this->session->set_flashdata('message', lang("topup_added"));
            admin_redirect("sales/gift_cards");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['card'] = $card;
            $this->data['page_title'] = lang("topup_gift_card");
            $this->load->view($this->theme . 'sales/topup_gift_card', $this->data);
        }
    }
    public function validate_gift_card($no){
        if ($gc = $this->site->getGiftCardByNO($no)) {
            if ($gc->expiry) {
                if ($gc->expiry >= date('Y-m-d')) {
                    $this->sma->send_json($gc);
                } else {
                    $this->sma->send_json(false);
                }
            } else {
                $this->sma->send_json($gc);
            }
        } else {
            $this->sma->send_json(false);
        }
    }

    public function add_gift_card()
    {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('card_no', lang("card_no"), 'trim|is_unique[gift_cards.card_no]|required');
        $this->form_validation->set_rules('value', lang("value"), 'required');

        if ($this->form_validation->run() == true) {
            $customer_details = $this->input->post('customer') ? $this->site->getCompanyByID($this->input->post('customer')) : null;
            $customer = $customer_details ? $customer_details->company : null;
            $data = array(
                'card_no' => $this->input->post('card_no'),
                'value' => $this->input->post('value'),
                'customer_id' => $this->input->post('customer') ? $this->input->post('customer') : null,
                'customer' => $customer,
                'balance' => $this->input->post('value'),
                'expiry' => $this->input->post('expiry') ? $this->sma->fsd($this->input->post('expiry')) : null,
                'created_by' => $this->session->userdata('user_id'),
            );
            $sa_data = array();
            $ca_data = array();
            if ($this->input->post('staff_points')) {
                $sa_points = $this->input->post('sa_points');
                $user = $this->site->getUser($this->input->post('user'));
                if ($user->award_points < $sa_points) {
                    $this->session->set_flashdata('error', lang("award_points_wrong"));
                    admin_redirect("sales/gift_cards");
                }
                $sa_data = array('user' => $user->id, 'points' => ($user->award_points - $sa_points));
            } elseif ($customer_details && $this->input->post('use_points')) {
                $ca_points = $this->input->post('ca_points');
                if ($customer_details->award_points < $ca_points) {
                    $this->session->set_flashdata('error', lang("award_points_wrong"));
                    admin_redirect("sales/gift_cards");
                }
                $ca_data = array('customer' => $this->input->post('customer'), 'points' => ($customer_details->award_points - $ca_points));
            }
        } elseif ($this->input->post('add_gift_card')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("sales/gift_cards");
        }

        if ($this->form_validation->run() == true && $this->sales_model->addGiftCard($data, $ca_data, $sa_data)) {
            $this->session->set_flashdata('message', lang("gift_card_added"));
            admin_redirect("sales/gift_cards");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['users'] = $this->sales_model->getStaff();
            $this->data['page_title'] = lang("new_gift_card");
            $this->load->view($this->theme . 'sales/add_gift_card', $this->data);
        }
    }

    public function edit_gift_card($id = null)
    {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('card_no', lang("card_no"), 'trim|required');
        $gc_details = $this->site->getGiftCardByID($id);
        if ($this->input->post('card_no') != $gc_details->card_no) {
            $this->form_validation->set_rules('card_no', lang("card_no"), 'is_unique[gift_cards.card_no]');
        }
        $this->form_validation->set_rules('value', lang("value"), 'required');

        if ($this->form_validation->run() == true) {
            $gift_card = $this->site->getGiftCardByID($id);
            $customer_details = $this->input->post('customer') ? $this->site->getCompanyByID($this->input->post('customer')) : null;
            $customer = $customer_details ? $customer_details->company : null;
            $data = array(
                'card_no' => $this->input->post('card_no'),
                'value' => $this->input->post('value'),
                'customer_id' => $this->input->post('customer') ? $this->input->post('customer') : null,
                'customer' => $customer,
                'balance' => ($this->input->post('value') - $gift_card->value) + $gift_card->balance,
                'expiry' => $this->input->post('expiry') ? $this->sma->fsd($this->input->post('expiry')) : null,
            );
        } elseif ($this->input->post('edit_gift_card')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("sales/gift_cards");
        }

        if ($this->form_validation->run() == true && $this->sales_model->updateGiftCard($id, $data)) {
            $this->session->set_flashdata('message', lang("gift_card_updated"));
            admin_redirect("sales/gift_cards");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['gift_card'] = $this->site->getGiftCardByID($id);
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'sales/edit_gift_card', $this->data);
        }
    }

    public function sell_gift_card()
    {
        $this->sma->checkPermissions('gift_cards', true);
        $error = null;
        $gcData = $this->input->get('gcdata');
        if (empty($gcData[0])) {
            $error = lang("value") . " " . lang("is_required");
        }
        if (empty($gcData[1])) {
            $error = lang("card_no") . " " . lang("is_required");
        }

        $customer_details = (!empty($gcData[2])) ? $this->site->getCompanyByID($gcData[2]) : null;
        $customer = $customer_details ? $customer_details->company : null;
        $data = array(
            'card_no' => $gcData[0],
            'value' => $gcData[1],
            'customer_id' => (!empty($gcData[2])) ? $gcData[2] : null,
            'customer' => $customer,
            'balance' => $gcData[1],
            'expiry' => (!empty($gcData[3])) ? $this->sma->fsd($gcData[3]) : null,
            'created_by' => $this->session->userdata('user_id'),
        );

        if (!$error) {
            if ($this->sales_model->addGiftCard($data)) {
                $this->sma->send_json(array('result' => 'success', 'message' => lang("gift_card_added")));
            }
        } else {
            $this->sma->send_json(array('result' => 'failed', 'message' => $error));
        }
    }

    public function delete_gift_card($id = null)
    {
        $this->sma->checkPermissions();

        if ($this->sales_model->deleteGiftCard($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("gift_card_deleted")));
        }
    }


    public function get_price($id = null)
    {
        $this->sma->checkPermissions();

        if ($this->sales_model->get_price($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("gift_card_deleted")));
        }
    }

    public function gift_card_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete_gift_card');
                    foreach ($_POST['val'] as $id) {
                        $this->sales_model->deleteGiftCard($id);
                    }
                    $this->session->set_flashdata('message', lang("gift_cards_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                if ($this->input->post('form_action') == 'export_excel') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('gift_cards'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('card_no'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('value'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('customer'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $sc = $this->site->getGiftCardByID($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sc->card_no);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $sc->value);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sc->customer);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'gift_cards_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', lang("no_gift_card_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    public function get_award_points($id = null)
    {
        $this->sma->checkPermissions('index');

        $row = $this->site->getUser($id);
        $this->sma->send_json(array('sa_points' => $row->award_points));
    }
    /* -------------------------------------------------------------------------------------- */
    public function sale_by_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('customer', lang("customer"), 'required');
        $this->form_validation->set_rules('biller', lang("biller"), 'required');
        $this->form_validation->set_rules('sale_status', lang("sale_status"), 'required');
        $this->form_validation->set_rules('payment_status', lang("payment_status"), 'required');

        if ($this->form_validation->run() == true) {

            $reference = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('so');
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $warehouse_id = $this->input->post('warehouse');
            $customer_id = $this->input->post('customer');
            $biller_id = $this->input->post('biller');
            $total_items = $this->input->post('total_items');
            $sale_status = $this->input->post('sale_status');
            $payment_status = $this->input->post('payment_status');
            $payment_term = $this->input->post('payment_term');
            $due_date = $payment_term ? date('Y-m-d', strtotime('+' . $payment_term . ' days')) : null;
            $shipping = $this->input->post('shipping') ? $this->input->post('shipping') : 0;
            $customer_details = $this->site->getCompanyByID($customer_id);
            $customer = $customer_details->company != '-'  ? $customer_details->company : $customer_details->name;
            $biller_details = $this->site->getCompanyByID($biller_id);
            $biller = $biller_details->company != '-' ? $biller_details->company : $biller_details->name;
            $note = $this->sma->clear_tags($this->input->post('note'));
            $staff_note = $this->sma->clear_tags($this->input->post('staff_note'));

            $total = 0;
            $product_tax = 0;
            $product_discount = 0;
            $gst_data = [];
            $total_cgst = $total_sgst = $total_igst = 0;
            if (isset($_FILES["userfile"])) {
                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect("sales/sale_by_csv");
                }
                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'net_unit_price', 'quantity', 'variant', 'item_tax_rate', 'discount', 'serial');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {

                    if (isset($csv_pr['code']) && isset($csv_pr['net_unit_price']) && isset($csv_pr['quantity'])) {

                        if ($product_details = $this->sales_model->getProductByCode($csv_pr['code'])) {

                            if ($csv_pr['variant']) {
                                $item_option = $this->sales_model->getProductVariantByName($csv_pr['variant'], $product_details->id);
                                if (!$item_option) {
                                    $this->session->set_flashdata('error', lang("pr_not_found") . " ( " . $product_details->name . " - " . $csv_pr['variant'] . " ). " . lang("line_no") . " " . $rw);
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                            } else {
                                $item_option = json_decode('{}');
                                $item_option->id = null;
                            }

                            $item_id = $product_details->id;
                            $item_type = $product_details->type;
                            $item_code = $product_details->code;
                            $item_name = $product_details->name;
                            $item_net_price = $this->sma->formatDecimal($csv_pr['net_unit_price']);
                            $item_quantity = $csv_pr['quantity'];
                            $item_tax_rate = $csv_pr['item_tax_rate'];
                            $item_discount = $csv_pr['discount'];
                            $item_serial = $csv_pr['serial'];

                            if (isset($item_code) && isset($item_net_price) && isset($item_quantity)) {
                                $product_details = $this->sales_model->getProductByCode($item_code);
                                $pr_discount = $this->site->calculateDiscount($item_discount, $item_net_price);
                                $item_net_price = $this->sma->formatDecimal(($item_net_price - $pr_discount), 4);
                                $pr_item_discount = $this->sma->formatDecimal(($pr_discount * $item_quantity), 4);
                                $product_discount += $pr_item_discount;

                                $tax = "";
                                $pr_item_tax = 0;
                                $unit_price = $item_net_price;
                                $tax_details = ((isset($item_tax_rate) && !empty($item_tax_rate)) ? $this->sales_model->getTaxRateByName($item_tax_rate) : $this->site->getTaxRateByID($product_details->tax_rate));
                                if ($tax_details) {
                                    $ctax = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                                    $item_tax = $ctax['amount'];
                                    $tax = $ctax['tax'];
                                    if (!$product_details || (!empty($product_details) && $product_details->tax_method != 1)) {
                                        $item_net_price = $unit_price - $item_tax;
                                    }
                                    $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_quantity, 4);
                                    if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($biller_details->state == $customer_details->state), $tax_details)) {
                                        $total_cgst += $gst_data['cgst'];
                                        $total_sgst += $gst_data['sgst'];
                                        $total_igst += $gst_data['igst'];
                                    }
                                }

                                $product_tax += $pr_item_tax;
                                $subtotal = $this->sma->formatDecimal((($item_net_price * $item_quantity) + $pr_item_tax), 4);
                                $unit = $this->site->getUnitByID($product_details->unit);

                                $product = array(
                                    'product_id' => $product_details->id,
                                    'product_code' => $item_code,
                                    'product_name' => $item_name,
                                    'product_type' => $item_type,
                                    'option_id' => $item_option->id,
                                    'net_unit_price' => $item_net_price,
                                    'quantity' => $item_quantity,
                                    'product_unit_id' => $product_details->unit,
                                    'product_unit_code' => $unit->code,
                                    'unit_quantity' => $item_quantity,
                                    'warehouse_id' => $warehouse_id,
                                    'item_tax' => $pr_item_tax,
                                    'tax_rate_id' => $tax_details ? $tax_details->id : null,
                                    'tax' => $tax,
                                    'discount' => $item_discount,
                                    'item_discount' => $pr_item_discount,
                                    'subtotal' => $subtotal,
                                    'serial_no' => $item_serial,
                                    'unit_price' => $this->sma->formatDecimal(($item_net_price + $item_tax), 4),
                                    'real_unit_price' => $this->sma->formatDecimal(($item_net_price + $item_tax + $pr_discount), 4),
                                );

                                $products[] = ($product + $gst_data);
                                $total += $this->sma->formatDecimal(($item_net_price * $item_quantity), 4);
                            }
                        } else {
                            $this->session->set_flashdata('error', lang("pr_not_found") . " ( " . $csv_pr['code'] . " ). " . lang("line_no") . " " . $rw);
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                        $rw++;
                    }
                }
            }

            $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax));
            $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);
            $order_tax = $this->site->calculateOrderTax($this->input->post('order_tax'), ($total + $product_tax - $order_discount));
            $total_tax = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $this->sma->formatDecimal($shipping) - $order_discount), 4);
            $data = array(
                'date' => $date,
                'reference_no' => $reference,
                'customer_id' => $customer_id,
                'customer' => $customer,
                'biller_id' => $biller_id,
                'biller' => $biller,
                'warehouse_id' => $warehouse_id,
                'note' => $note,
                'staff_note' => $staff_note,
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount_id' => $this->input->post('order_discount'),
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax_id' => $this->input->post('order_tax'),
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'shipping' => $this->sma->formatDecimal($shipping),
                'grand_total' => $grand_total,
                'total_items' => $total_items,
                'sale_status' => $sale_status,
                'payment_status' => $payment_status,
                'payment_term' => $payment_term,
                'due_date' => $due_date,
                'paid' => 0,
                'created_by' => $this->session->userdata('user_id'),
            );
            if ($this->Settings->indian_gst) {
                $data['cgst'] = $total_cgst;
                $data['sgst'] = $total_sgst;
                $data['igst'] = $total_igst;
            }

            if ($payment_status == 'paid') {

                $payment = array(
                    'date' => $date,
                    'reference_no' => $this->site->getReference('pay'),
                    'amount' => $grand_total,
                    'paid_by' => 'cash',
                    'cheque_no' => '',
                    'cc_no' => '',
                    'cc_holder' => '',
                    'cc_month' => '',
                    'cc_year' => '',
                    'cc_type' => '',
                    'created_by' => $this->session->userdata('user_id'),
                    'note' => lang('auto_added_for_sale_by_csv') . ' (' . lang('sale_reference_no') . ' ' . $reference . ')',
                    'type' => 'received',
                );
            } else {
                $payment = array();
            }

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
        }

        if ($this->form_validation->run() == true && $this->sales_model->addSale($data, $products, $payment)) {
            $this->session->set_userdata('remove_slls', 1);
            $this->session->set_flashdata('message', lang("sale_added"));
            admin_redirect("sales");
        } else {

            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['billers'] = $this->site->getAllCompanies('biller');
            $this->data['slnumber'] = $this->site->getReference('so');

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('sales'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('add_sale_by_csv')));
            $meta = array('page_title' => lang('add_sale_by_csv'), 'bc' => $bc);
            $this->page_construct('sales/sale_by_csv', $meta, $this->data);
        }
    }
    public function update_status($id){

        $this->form_validation->set_rules('status', lang("sale_status"), 'required');

        if ($this->form_validation->run() == true) {
            $status = $this->input->post('status');
            $note = $this->sma->clear_tags($this->input->post('note'));
        } elseif ($this->input->post('update')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        }

        if ($this->form_validation->run() == true && $this->sales_model->updateStatus($id, $status, $note)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        } else {

            $this->data['inv'] = $this->sales_model->getInvoiceByID($id);
            $this->data['returned'] = FALSE;
            if ($this->data['inv']->sale_status == 'returned' || $this->data['inv']->return_id) {
                $this->data['returned'] = TRUE;
            }
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'sales/update_status', $this->data);
        }
    }

    public function packaging($id)
    {

        $sale = $this->sales_model->getInvoiceByID($id);
        $this->data['returned'] = FALSE;
        if ($sale->sale_status == 'returned' || $sale->return_id) {
            $this->data['returned'] = TRUE;
        }
        $this->data['warehouse'] = $this->site->getWarehouseByID($sale->warehouse_id);
        $items = $this->sales_model->getAllInvoiceItems($sale->id);
        foreach ($items as $item) {
            $packaging[] = array(
                'name' => $item->product_code . ' - ' . $item->product_name,
                'quantity' => $item->quantity . ' ' . $item->product_unit_code,
                'rack' => $this->sales_model->getItemRack($item->product_id, $sale->warehouse_id),
            );
        }
        $this->data['packaging'] = $packaging;
        $this->data['sale'] = $sale;

        $this->load->view($this->theme . 'sales/packaging', $this->data);
    }
    /* -------------------------------------------------------------------------------------- */
    public function get_purchase_list()
    {

        $purchase_id = $this->input->get('purchase_id');
        $batch_code = $this->input->get('batch_code');
        $rows = $this->sales_model->getPurchaseList($purchase_id, $batch_code);
        $this->sma->send_json($rows);
    }
    //---------------Ismail FSD Code-----------------------//
    public function getCustomerAddress($id){
        $sendvalue['address'] = '';
        $sendvalue['city'] = '';
        $sendvalue['state'] = '';
        $sendvalue['postal_code'] = '';
        $sendvalue['country'] = '';
        $sendvalue['phone'] = '';
        if($id != 0){
            $this->db->select('*');
            $this->db->from('sma_addresses');
            $this->db->where('id',$id);
            $addresq = $this->db->get();
            if ($addresq->num_rows() > 0) {
                $addres = json_decode(json_encode($addresq->result()));
                $customeraddress = $addres[0];
                $sendvalue['address'] = $customeraddress->line1.'<br>'.$customeraddress->line2;
                $sendvalue['city'] = $customeraddress->city;
                $sendvalue['state'] = $customeraddress->state;
                $sendvalue['postal_code'] = $customeraddress->postal_code;
                $sendvalue['country'] = $customeraddress->country;
                $sendvalue['phone'] = $customeraddress->phone;

            }
        }
        return $sendvalue;
    }
    public function getCustomerAddressList($id){
        $this->db->select('*');
        $this->db->from('sma_addresses');
        $this->db->where('company_id ',$id);
        $addresq = $this->db->get();
        $sendvalue = json_decode(json_encode($addresq->result()));
        return $sendvalue;

    }
    public function salesedit(){
        $sendvalue['codestatus'] = 'no';
        $id = $this->input->post('id');
        $reference_no = $this->input->post('reference_no');
        $date = $this->input->post('date');
        $biller_id = $this->input->post('biller_id');
        $po_date = $this->input->post('po_date');
        $dc_number = $this->input->post('dc_number');
        $po_number = $this->input->post('po_number');
        $etaliers = $this->input->post('etaliers');
        $own_company = $this->input->post('own_company');
        $discount = $this->input->post('discount');
        $shipping = $this->input->post('shipping');
        $deliveryaddress = $this->input->post('deliveryaddress');
        $payment_terms = $this->input->post('payment_term');
        $note = $this->input->post('note');
        $staff_note = $this->input->post('staff_note');
        $reason = $this->input->post('reason');
        if($reason != ""){
            $this->db->select('*');
            $this->db->from('sma_sales');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows()>0){
                $salesdetail = $q->result()[0];
                if($salesdetail->reference_no != $reference_no){
                    $check_reference_already_exits = $this->sales_model->check_reference_already_exits($reference_no);
                    if ($check_reference_already_exits) {
                        $sendvalue['codestatus'] = 'Invoie Number Already Exist';
                        echo json_encode($sendvalue);
                        exit();
                    }
                    else{
                        $setdata['reference_no'] = $reference_no;
                    }
                }
                $setdata['date'] = $date;
                $setdata['own_company'] = $own_company;
                $setdata['customer_address_id'] = $deliveryaddress;
                $setdata['po_number'] = $po_number;
                $setdata['biller_id'] = $biller_id;
                $setdata['etalier_id'] = $etaliers;
                $setdata['biller'] = 'Rhocom';
                $setdata['note'] = $note;
                $setdata['staff_note'] = $staff_note;
                $setdata['order_discount'] = $discount;
                $setdata['total_discount'] = $discount+$salesdetail->product_discount;
                $setdata['shipping'] = $shipping;
                $grand_total = $salesdetail->total+$shipping+$salesdetail->order_tax-$setdata['order_discount'];
                $setdata['grand_total'] = $grand_total;
                $setdata['payment_terms'] = $payment_terms;
                $setdata['po_date'] = date_format(date_create($po_date),"Y-m-d H:i:s");
                $setdata['dc_num'] = $dc_number;
                $this->db->set($setdata);
                $this->db->where('id',$id);
                $this->db->update('sma_sales');
                $activitynote = '<h6>Sale Information Edit. Reference No: '.$salesdetail->reference_no.'</h6>';
                $activitynote .= '<p style="color:red" ><b>Reason:</b> '.$reason.'</p>';
                $this->useractivities_model->add([
                    'note'=>$activitynote,
                    'location'=>'Sales->Detail->Edit->Submit',
                    'sale_id'=>$id,
                    'action_by'=>$this->session->userdata('user_id')
                ]);
                $sendvalue['codestatus'] = 'ok';
            }
            else{
                $sendvalue['codestatus'] = 'no';
            }
        }
        else{
            $sendvalue['codestatus'] = 'Enter Reason';
        }
        echo json_encode($sendvalue);
    }
    public function itemdetail(){
        $sendvalue['codestatus'] = "no";
        $id = $this->input->get('id');
        $this->db->select('
            sma_sale_items.*,
            sma_products.discount_one as pd1,
            sma_products.discount_two as pd2,
            sma_products.discount_three as pd3,
            sma_sales.customer_id,
            sma_sales.supplier_id,
            sma_companies.sales_type as customer_sales_type,
        ');
        $this->db->from('sma_sale_items');
        $this->db->join('sma_sales','sma_sales.id = sma_sale_items.sale_id','left');
        $this->db->join('sma_companies', 'sma_companies.id = sma_sales.customer_id', 'left');
        $this->db->join('sma_products','sma_products.id = sma_sale_items.product_id','left');
        $this->db->where('sma_sale_items.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $item = $q->result()[0];
            $this->db->select('expiry,quantity_balance as qb,batch as code');
            if ($item->customer_sales_type === 'cost') {
                $this->db->select('sma_purchase_items.net_unit_cost as selling_price');
            }
            else{
                $this->db->select('sma_purchase_items.price as selling_price');
            }
            // if($item->discount_three != $item->pd3 ){
            //     $item->pd3 = $item->discount_three;
            // }
            $this->db->from('sma_purchase_items');
            $this->db->where('product_id',$item->product_id);
            $this->db->where('warehouse_id',$item->warehouse_id);
            $this->db->where('quantity_balance != ','0.0000');
            $batchs = $this->db->get();
            $batchs = $batchs->result();

            $this->db->select('expiry,quantity_balance as qb,batch as code');
            if ($item->customer_sales_type === 'cost') {
                $this->db->select('sma_purchase_items.net_unit_cost as selling_price');
            }
            else{
                $this->db->select('sma_purchase_items.price as selling_price');
            }
            $this->db->from('sma_purchase_items');
            $this->db->where('product_id',$item->product_id);
            $this->db->where('warehouse_id',$item->warehouse_id);
            $this->db->where('batch',$item->batch);
            $this->db->where('quantity_balance = ','0.0000');
            $sbatchs = $this->db->get();
            if($sbatchs->num_rows() > 0){
                $batchs[] = $sbatchs->result()[0];
            }
            $batchhtml = '';
            foreach($batchs as $batch){
                // print_r($batch);
                $batchhtml .= '<option value="'.$batch->code.'" data-price="'.$batch->selling_price.'" '; 
                if($batch->code == $item->batch){ $batchhtml .= 'selected'; } 
                $batchhtml .= ' >'.$batch->code.' (Expiry: '.$batch->expiry.', Available Qty: '.$batch->qb.')</option>';
            }

            $sendvalue['ebatchs'] = $batchs;
            $sendvalue['htmlbatchs'] = $batchhtml;

            $this->db->select('id,discount_name,discount_code,percentage');
            $this->db->from('sma_bulk_discount');
            // $this->db->where('product_id',$item->product_id);
            $this->db->where('
                (
                    CURDATE() between start_date and end_date
                ) and 
                (
                    find_in_set(' . $item->supplier_id . ',supplier_id
                ) OR 
                find_in_set(' . $item->product_id . ',product_id)  <> 0)
            ');
            $sdiscount = $this->db->get();
            $discounts = $sdiscount->result();
            $discounthtml = '<option value="'.$item->pd3.'" >Select Discount</option>';
            foreach($discounts as $discount){
                $discounthtml .= '<option value="'.$discount->percentage.'" ';
                // if($item->discount_three == $discount->percentage){
                //     $discounthtml .= "selected";
                // }
                $discounthtml .= ' >'.$discount->discount_code.'</option>';
            }
            $sendvalue['ediscount'] = $discounts;
            $sendvalue['htmldiscount'] = $discounthtml;
            
            $sendvalue['detail'] = $item;
            $sendvalue['codestatus'] = "ok";
        }
        else{
            $sendvalue['codestatus'] = "Item not found";
        }
        echo json_encode($sendvalue);
    }
    public function updateitem(){
        $sendvalue['codestatus'] = 'no';
        $id = $this->input->post('id');
        $qty = $this->input->post('qty');
        $price = $this->input->post('price');
        $batch = $this->input->post('batch');
        $cd1 = $this->input->post('done_chk');
        $cd2 = $this->input->post('dtwo_chk');
        $cd3 = $this->input->post('dth_chk');
        $reason = $this->input->post('reason');
        // if($qty == 0 || $qty == ""){
        if($qty == ""){
            $sendvalue['codestatus'] = 'Enter Quantity';
        }
        else if ($reason == ""){
            $sendvalue['codestatus'] = 'Enter Reason';
        }
        else{
            $this->db->select('
                sma_sale_items.*,
                sma_products.fed_tax as pfed_tax,
                sma_products.adv_tax_reg as adv_tax_reg,
                sma_products.adv_tax_nonreg as adv_tax_nonreg,
                sma_tax_rates.name as tax_rate_name,
                sma_tax_rates.rate as tax_rate_rate,
                sma_tax_rates.code as tax_rate_code,
                sma_tax_rates.type as tax_rate_type,
                sma_sales.customer_id as s_customer_id,
                sma_companies.gst_no as customer_gst_no,
                sma_companies.sales_type as customer_sales_type
            ');
            $this->db->from('sma_sale_items');
            $this->db->join('sma_products','sma_products.id = sma_sale_items.product_id','left');
            $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_products.tax_rate', 'left');
            $this->db->join('sma_sales', 'sma_sales.id = sma_sale_items.sale_id', 'left');
            $this->db->join('sma_companies', 'sma_companies.id = sma_sales.customer_id', 'left');
            $this->db->where('sma_sale_items.id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $items = $q->result()[0];
                $selling_price = 0;
                if($items->batch != $batch){
                    $selling_price = 0;
                    $this->db->select('
                        sma_purchase_items.*,
                        sma_tax_rates.id as tax_rate_id,
                        sma_tax_rates.name as tax_rate_name,
                        sma_tax_rates.rate as tax_rate_rate,
                        sma_tax_rates.code as tax_rate_code,
                        sma_tax_rates.type as tax_rate_type,
                    ');
                    $this->db->from('sma_purchase_items');
                    $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_purchase_items.tax_rate_id', 'left');
                    $this->db->where('sma_purchase_items.product_id',$items->product_id);
                    $this->db->where('sma_purchase_items.warehouse_id',$items->warehouse_id);
                    $this->db->where('sma_purchase_items.batch',$batch);
                    $q2 = $this->db->get();
                    if($q2->num_rows() > 0){
                        $q2r = $q2->result()[0];
                        if($q2r->quantity_balance >= $qty){
                            $selling_price = 0;
                            if ($items->customer_sales_type === 'cost') {
                                $selling_price = $q2r->net_unit_cost;
                            }
                            else{
                                $selling_price = $q2r->price;
                            }
                            $setdata['net_unit_price'] = $selling_price;
                            $setdata['real_unit_price'] = $selling_price;
                            $setdata['product_price'] = $selling_price;
                            $setdata['consignment'] = $q2r->price;
                            $setdata['dropship'] = $q2r->dropship;
                            $setdata['crossdock'] = $q2r->crossdock;
                            $setdata['mrp'] = $q2r->mrp;
                            $setdata['expiry'] = $q2r->expiry;
                            $setdata['batch'] = $q2r->batch;
                            $setdata['quantity'] = $qty;
                            $setdata['unit_quantity'] = $qty;
                            if($items->tax_rate_type == 1){
                                $setdata['item_tax'] = (($selling_price/100)*$items->tax_rate_rate)*$qty;
                            }
                            else{
                                $setdata['item_tax'] = $items->tax_rate_rate*$qty;
                            }
                            if($items->customer_gst_no == ""){
                                if($items->tax_rate_type == 1){
                                    $further_tax_setting = $this->sales_model->further_tax();
                                    $further_tax = $further_tax_setting->further_tax;
                                    $setdata['further_tax'] = ((($selling_price/100)*$further_tax))*$qty;
                                }
                                else{
                                    $setdata['further_tax'] = 0;
                                }
                            }
                            else{
                                $setdata['further_tax'] = 0;
                            }
                            $setdata['fed_tax'] = 0;
                            $setdata['discount_one'] = 0;
                            $setdata['discount_two'] = 0;
                            $setdata['discount_three'] = 0;
                            $setdata['adv_tax'] = 0;
                            if($items->customer_gst_no == ""){
                                $setdata['adv_tax'] = decimalallow(((($selling_price*$qty)+$setdata['item_tax'])/100)*$items->adv_tax_nonreg,2);
                            }
                            else{
                                $setdata['adv_tax'] = decimalallow(((($selling_price*$qty)+$setdata['item_tax'])/100)*$items->adv_tax_reg,2);
                            }
                            $d1 = 0;
                            $d2 = 0;
                            $d3 = 0;
                            if(isset($_POST['done_chk'])){
                                $d1 = (($selling_price/100)*$_POST['done_chk'])*$qty;
                                $setdata['discount_one'] = $_POST['done_chk'];
                            }
                            if(isset($_POST['dtwo_chk'])){
                                $d2 = (($selling_price/100)*$_POST['dtwo_chk'])*$qty;
                                $setdata['discount_two'] = $_POST['dtwo_chk'];
                            }
                            if(isset($_POST['dth_chk'])){
                                $d3 = (($selling_price/100)*$_POST['dth_chk'])*$qty;
                                $setdata['discount_three'] = $_POST['dth_chk'];
                            }
                            $d = $d1+$d2+$d3;
                            $setdata['discount'] = $d;
                            $setdata['item_discount'] = $d;
                            $setdata['subtotal'] = ($selling_price*$qty)+($setdata['item_tax']+$setdata['further_tax']+$setdata['adv_tax'])-$d;
                            $type = '+';
                            $updateqty = 0;
                            $this->db->set($setdata);
                            $this->db->where('id',$id);
                            $this->db->update('sma_sale_items');

                            $this->db->select('id');
                            $this->db->from('sma_purchase_items');
                            $this->db->where('product_id',$items->product_id);
                            $this->db->where('warehouse_id',$items->warehouse_id);
                            $this->db->where('batch',$items->batch);
                            $q4 = $this->db->get();
                            if($q4->num_rows() > 0){
                                $this->updateQty($items->product_id,$items->warehouse_id,$q4->result()[0]->id,$items->quantity,'+');
                            }
                            $this->updateQty($items->product_id,$items->warehouse_id,$q2r->id,$qty,'-');
                            $this->updateSalePrice($items->sale_id);

                            $activitynote = '<h6>Old Data:</h6>';
                            $activitynote .= '<ul style="margin-left: 25px;" >';
                                $activitynote .= '<li>Qty: '.$items->quantity.'</li>';
                                $activitynote .= '<li>Batch: '.$items->batch.'</li>';
                                $activitynote .= '<li>Discount 1: '.$items->discount_one.'</li>';
                                $activitynote .= '<li>Discount 2: '.$items->discount_two.'</li>';
                                $activitynote .= '<li>Discount 3: '.$items->discount_three.'</li>';
                            $activitynote .= '</ul>';
                            $activitynote .= '<h6>New Data:</h6>';
                            $activitynote .= '<ul style="margin-left: 25px;" >';
                                $activitynote .= '<li>Qty: '.$qty.'</li>';
                                $activitynote .= '<li>Batch: '.$q2r->batch.'</li>';
                                $activitynote .= '<li>Discount 1: '.$setdata["discount_one"].'</li>';
                                $activitynote .= '<li>Discount 2: '.$setdata["discount_two"].'</li>';
                                $activitynote .= '<li>Discount 3: '.$setdata["discount_three"].'</li>';
                            $activitynote .= '</ul>';
                            $activitynote .= '<p style="color:red" ><b>Reason:</b> '.$reason.'</p>';

                            $this->useractivities_model->add([
                                'note'=>$activitynote,
                                'location'=>'Sales->Item->Edit->Submit',
                                'product_id'=>$items->product_id,
                                'sale_id'=>$items->sale_id,
                                'action_by'=>$this->session->userdata('user_id')
                            ]);
                            $this->load->admin_model('stores_model');
                            $sendvalue['apistatus'] = $this->stores_model->updateStoreQty($items->product_id,$items->warehouse_id,0,'Update Sale Item');
                            $sendvalue['codestatus'] = 'ok';

                        }
                        else{
                            $sendvalue['codestatus'] = $qty.' not available in '.$batch;
                        }
                    }
                    else{
                        $sendvalue['codestatus'] = 'Batch not available';
                    }
                }
                else{
                    $selling_price = $price;
                    $this->db->select('id,quantity_balance');
                    $this->db->from('sma_purchase_items');
                    $this->db->where('product_id',$items->product_id);
                    $this->db->where('warehouse_id',$items->warehouse_id);
                    $this->db->where('batch',$items->batch);
                    $q2 = $this->db->get();
                    if($q2->num_rows() > 0){
                        $q2r = $q2->result()[0];
                        $newqty = $q2r->quantity_balance+$items->quantity;
                        if($newqty >= $qty){
    
                            $setdata['net_unit_price'] = $selling_price;
                            $setdata['real_unit_price'] = $selling_price;
                            $setdata['product_price'] = $selling_price;
                            $setdata['unit_price'] = $selling_price;
                            $setdata['quantity'] = $qty;
                            $setdata['unit_quantity'] = $qty;
                            if($items->tax_rate_type == 1){
                                $setdata['item_tax'] = (($selling_price/100)*$items->tax_rate_rate)*$qty;
                            }
                            else{
                                $setdata['item_tax'] = $items->tax_rate_rate*$qty;
                            }
                            if($items->customer_gst_no == ""){
                                if($items->tax_rate_type == 1){
                                    $further_tax_setting = $this->sales_model->further_tax();
                                    $further_tax = $further_tax_setting->further_tax;
                                    $setdata['further_tax'] = (($selling_price/100)*$further_tax)*$qty;
                                }
                                else{
                                    $setdata['further_tax'] = 0;
                                }
                            }
                            else{
                                $setdata['further_tax'] = 0;
                            }

                            $setdata['adv_tax'] = 0;
                            if($items->customer_gst_no == ""){
                                $setdata['adv_tax'] = decimalallow(((($selling_price*$qty)+$setdata['item_tax'])/100)*$items->adv_tax_nonreg,2);
                            }
                            else{
                                $setdata['adv_tax'] = decimalallow(((($selling_price*$qty)+$setdata['item_tax'])/100)*$items->adv_tax_reg,2);
                            }
                            $setdata['fed_tax'] = 0;
                            $setdata['discount_one'] = 0;
                            $setdata['discount_two'] = 0;
                            $setdata['discount_three'] = 0;
                            $setdata['fed_tax'] = 0;
                            $d1 = 0;
                            $d2 = 0;
                            $d3 = 0;
                            if(isset($_POST['done_chk'])){
                                $d1 = (($selling_price/100)*$_POST['done_chk'])*$qty;
                                $setdata['discount_one'] = $_POST['done_chk'];
                            }
                            if(isset($_POST['dtwo_chk'])){
                                $d2 = (($selling_price/100)*$_POST['dtwo_chk'])*$qty;
                                $setdata['discount_two'] = $_POST['dtwo_chk'];
                            }
                            if(isset($_POST['dth_chk'])){
                                $d3 = (($selling_price/100)*$_POST['dth_chk'])*$qty;
                                $setdata['discount_three'] = $_POST['dth_chk'];
                            }
                            $d = $d1+$d2+$d3;
                            $setdata['discount'] = $d;
                            $setdata['item_discount'] = $d;
                            $setdata['subtotal'] = ($selling_price*$qty)+($setdata['item_tax']+$setdata['further_tax']+$setdata['adv_tax'])-$d;
                            $type = '+';
                            $updateqty = 0;
                            $this->db->set($setdata);
                            $this->db->where('id',$id);
                            $this->db->update('sma_sale_items');
                            if($qty>=$items->quantity){
                                $type = '-';
                                $updateqty = $qty-$items->quantity;
                            }
                            else{
                                $type = '+';
                                $updateqty = 0;
                                $updateqty = $items->quantity-$qty;
                            }
                            $this->updateQty($items->product_id,$items->warehouse_id,$q2r->id,$updateqty,$type);
                            $this->updateSalePrice($items->sale_id);
                            $activitynote = '<h6>Old Data:</h6>';
                            $activitynote .= '<ul style="margin-left: 25px;" >';
                                $activitynote .= '<li>Price: '.$items->net_unit_price.'</li>';
                                $activitynote .= '<li>Qty: '.$items->quantity.'</li>';
                                $activitynote .= '<li>Batch: '.$items->batch.'</li>';
                                $activitynote .= '<li>Discount 1: '.$items->discount_one.'</li>';
                                $activitynote .= '<li>Discount 2: '.$items->discount_two.'</li>';
                                $activitynote .= '<li>Discount 3: '.$items->discount_three.'</li>';
                            $activitynote .= '</ul>';
                            $activitynote .= '<h6>New Data:</h6>';
                            $activitynote .= '<ul style="margin-left: 25px;" >';
                                $activitynote .= '<li>Price: '.$selling_price.'</li>';
                                $activitynote .= '<li>Qty: '.$qty.'</li>';
                                $activitynote .= '<li>Batch: '.$items->batch.'</li>';
                                $activitynote .= '<li>Discount 1: '.$setdata["discount_one"].'</li>';
                                $activitynote .= '<li>Discount 2: '.$setdata["discount_two"].'</li>';
                                $activitynote .= '<li>Discount 3: '.$setdata["discount_three"].'</li>';
                            $activitynote .= '</ul>';
                            $activitynote .= '<p style="color:red" ><b>Reason:</b> '.$reason.'</p>';
                            $this->useractivities_model->add([
                                'note'=>$activitynote,
                                'location'=>'Sales->Item->Edit->Submit',
                                'product_id'=>$items->product_id,
                                'sale_id'=>$items->sale_id,
                                'action_by'=>$this->session->userdata('user_id')
                            ]);
                            $this->load->admin_model('stores_model');
                            $sendvalue['apistatus'] = $this->stores_model->updateStoreQty($items->product_id,$items->warehouse_id,0,'Update Sale Item');
                            $sendvalue['codestatus'] = 'ok';
                        }
                        else{
                            $sendvalue['codestatus'] = $qty.' not available in '.$items->batch;
                        }
                    }
                    else{
                        $sendvalue['codestatus'] = 'Batch not available';
                    }
                }
            }
            else{
                $sendvalue['codestatus'] = 'Sale Item Invalid';
            }
        }
        echo json_encode($sendvalue);
    }
    public function updateQty($p_id,$w_id,$pi_id,$qty,$type){
        //Batch Quantity Update in Purchase Table
        if($type == "+"){
            $this->db->set('quantity_balance', 'quantity_balance+'.$qty, FALSE);
        }
        else{
            $this->db->set('quantity_balance', 'quantity_balance-'.$qty, FALSE);
        }
        $this->db->where('id', $pi_id);
        $this->db->update('purchase_items');
        //Warehouse Quantity Update in Warehouse Product Table
        if($type == "+"){
            $this->db->set('quantity', 'quantity+'.$qty, FALSE);
        }
        else{
            $this->db->set('quantity', 'quantity-'.$qty, FALSE);
        }
        $this->db->where('product_id', $p_id);
        $this->db->where('warehouse_id', $w_id);
        $this->db->update('warehouses_products');
        //Product Quantity Update in Product Table
        if($type == "+"){
            $this->db->set('quantity', 'quantity+'.$qty, FALSE);
        }
        else{
            $this->db->set('quantity', 'quantity-'.$qty, FALSE);
        }
        $this->db->where('id', $p_id);
        $this->db->update('products');
    }
    public function updateSalePrice($id){
        $this->db->select('*');
        $this->db->from('sma_sales');
        $this->db->where('id',$id);
        $sq = $this->db->get();
        if($sq->num_rows() > 0){
            $sale = $sq->result()[0];
            $total = 0;
            $total_items = 0;
            $product_discount = 0;
            $product_tax = 0;
            $adv_tax = 0;
            $this->db->select('*');
            $this->db->from('sma_sale_items');
            $this->db->where('sale_id',$id);
            $q = $this->db->get();
            $items = $q->result();
            foreach($items as $item){
                $total += $item->subtotal;
                $total_items += $item->quantity;
                $product_discount += $item->discount;
                $product_tax += $item->item_tax;
                $adv_tax += $item->adv_tax;
            }
            $setdata['total_items'] = $total_items;
            $setdata['total'] = $total;
            $setdata['product_discount'] = $product_discount;
            $setdata['total_discount'] = $sale->order_discount+$product_discount;
            $setdata['product_tax'] = $product_tax;
            $setdata['adv_tax'] = $adv_tax;
            $setdata['total_tax'] = $sale->order_tax+$product_tax+$adv_tax;
            $setdata['grand_total'] = $total+$sale->order_tax+$sale->shipping-$sale->order_discount;
            $this->db->set($setdata);
            $this->db->where('id',$id);
            $this->db->update('sma_sales');
            return true;
        }
        else{
            return false;
        }
    }
    public function itemdelete(){
        $sendvalue['codestatus'] = 'no';
        $id = $this->input->get('id');
        $reason = $this->input->get('reason');
        if($reason != ""){
            $this->db->select('*');
            $this->db->from('sma_sale_items');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $items = $q->result()[0];
                $this->db->select('id,quantity_balance');
                $this->db->from('sma_purchase_items');
                $this->db->where('product_id',$items->product_id);
                $this->db->where('warehouse_id',$items->warehouse_id);
                $this->db->where('batch',$items->batch);
                $q2 = $this->db->get();
                if($q2->num_rows() > 0){
                    $q2r = $q2->result()[0];
                    $this->db->delete('sma_sale_items', array('id' => $id));
                    $this->updateQty($items->product_id,$items->warehouse_id,$q2r->id,$items->quantity,'+');
                    $this->updateSalePrice($items->sale_id);

                    $activitynote = '<h6>Item Detail:</h6>';
                    $activitynote .= '<ul style="margin-left: 25px;" >';
                        $activitynote .= '<li>Item Name: '.$items->product_name.'</li>';
                        $activitynote .= '<li>Qty: '.$items->quantity.'</li>';
                        $activitynote .= '<li>Batch: '.$items->batch.'</li>';
                    $activitynote .= '</ul>';
                    $activitynote .= '<p style="color:red" ><b>Reason:</b> '.$reason.'</p>';
                    $this->useractivities_model->add([
                        'note'=>$activitynote,
                        'location'=>'Sales->Item->Delete->Submit',
                        'product_id'=>$items->product_id,
                        'sale_id'=>$items->sale_id,
                        'action_by'=>$this->session->userdata('user_id')
                    ]);

                    $this->load->model('admin/stores_model');
                    $this->stores_model->updateStoreQty($items->product_id,$items->warehouse_id,0,'Delete Sale item');
    
                    $sendvalue['codestatus'] = 'ok';
                }
                else{
                    $sendvalue['codestatus'] = 'Batch not available';
                }
            }
            else{
                $sendvalue['codestatus'] = 'Sale Item Invalid';
            }
        }
        else{
            $sendvalue['codestatus'] = 'Enter Reason';
        }
        echo json_encode($sendvalue);
    }
    public function batches(){
        $pid = $this->input->post('pid');
        $wid = $this->input->post('wid');
        $sid = $this->input->post('sid');
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


        $this->db->select('id,discount_name,discount_code,percentage');
        $this->db->from('sma_bulk_discount');
        // $this->db->where('product_id',$pid);
        $this->db->where('
            (
                CURDATE() between start_date and end_date
            ) and 
            (
                find_in_set(' . $sid . ',supplier_id
            ) OR 
            find_in_set(' . $pid . ',product_id)  <> 0)
        ');
        $sdiscount = $this->db->get();
        $discounts = $sdiscount->result();
        $discounthtml = '<option value="" >Select Discount</option>';
        foreach($discounts as $discount){
            $discounthtml .= '<option value="'.$discount->percentage.'" >'.$discount->discount_code.'</option>';
        }
        $sendvalue['ediscount'] = $discounts;
        $sendvalue['htmldiscount'] = $discounthtml;


        echo json_encode($sendvalue);
    }
    public function productdetail(){
        $sendvalue['codestatus'] = 'no';
        $data['sellingprice'] = 0;
        $data['mrp'] = 0;
        $data['quantity'] = 1;
        $data['d1'] = 0;
        $data['d1a'] = 0;
        $data['d2'] = 0;
        $data['d2a'] = 0;
        $data['d3'] = 0;
        $data['d3a'] = 0;
        $data['d'] = 0;
        $data['fedtax'] = 0;
        $data['tax_id'] = 0;
        $data['tax_type'] = 0;
        $data['tax_rate'] = 0;
        $data['tax'] = 0;
        $data['subtotal'] = 0;

        $pid =  $this->input->post('pid');
        $qty =  $this->input->post('qty');
        $cid =  $this->input->post('cid');
        $wid =  $this->input->post('wid');
        $batch =  $this->input->post('batch');
        $cd1 =  $this->input->post('cd1');
        $cd2 =  $this->input->post('cd2');
        $cd3 =  $this->input->post('cd3');
        $bulkdiscount =  $this->input->post('bulkdiscount');

        $this->db->select('
            sma_purchase_items.*,
            sma_products.discount_one as d1,
            sma_products.discount_two as d2,
            sma_products.discount_three as d3,
            sma_products.fed_tax as pfed,
            sma_products.company_code,
            sma_products.type as product_type,
            sma_products.adv_tax_reg as adv_tax_reg,
            sma_products.adv_tax_nonreg as adv_tax_nonreg,
            sma_tax_rates.id as tax_rate_id,
            sma_tax_rates.name as tax_rate_name,
            sma_tax_rates.rate as tax_rate_rate,
            sma_tax_rates.code as tax_rate_code,
            sma_tax_rates.type as tax_rate_type,
        ');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_products', 'sma_products.id  = sma_purchase_items.product_id', 'left');
        $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_products.tax_rate', 'left');
        $this->db->where('sma_purchase_items.product_id',$pid);
        $this->db->where('sma_purchase_items.warehouse_id',$wid);
        $this->db->where('sma_purchase_items.batch',$batch);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $detail = $q->result()[0];
            $sprice = 0;

            $customer_gst_no = "";
            $customer_sales_type = "";
            $this->db->select('gst_no as customer_gst_no,sales_type as customer_sales_type');
            $this->db->from('sma_companies');
            $this->db->where('id',$cid);
            $cuget = $this->db->get();
            if ($cuget->num_rows() > 0) {
                $cugetdata = $cuget->result()[0];
                $customer_gst_no = $cugetdata->customer_gst_no;
                $customer_sales_type = $cugetdata->customer_sales_type;
            }
            if (customer_sales_type === 'cost') {
                $sprice = $detail->net_unit_cost;
            }
            else{
                $sprice = $detail->price;
            }
            $data['sellingprice'] = $sprice;
            $data['mrp'] = $detail->mrp;
            $data['quantity'] = 1;
            $data['d1'] = $detail->d1;
            $data['d1a'] = (($sprice/100)*$detail->d1)*$qty;
            $data['d2'] = $detail->d2;
            $data['d2a'] = (($sprice/100)*$detail->d2)*$qty;
            if($bulkdiscount == ""){
                $data['d3'] = $detail->d3;
                $data['d3a'] = (($sprice/100)*$detail->d3)*$qty;
            }
            else{
                $data['d3'] = $bulkdiscount;
                $data['d3a'] = (($sprice/100)*$bulkdiscount)*$qty;
            }
            $data['d'] = 0;
            if($cd1 == 'yes'){
                $data['d'] += $data['d1a'];
            }
            if($cd2 == 'yes'){
                $data['d'] += $data['d2a'];
            }
            if($cd3 == 'yes'){
                $data['d'] += $data['d3a'];
            }
            $data['fedtax'] = $detail->pfed*$qty;
            $data['tax_id'] = $detail->tax_rate_id;
            $data['tax_type'] = $detail->tax_rate_type;
            $data['tax_rate'] = $detail->tax_rate_rate;
            if($detail->tax_rate_type == 1){
                $data['tax'] = (($sprice/100)*$detail->tax_rate_rate)*$qty;
            }
            else{
                $data['tax'] = $detail->tax_rate_rate*$qty;
            }
            if($customer_gst_no == ""){
                $advtax = ($sprice+($data['tax']/$qty))/100*$detail->adv_tax_nonreg;
            }
            else{
                $advtax = ($sprice+($data['tax']/$qty))/100*$detail->adv_tax_reg;
            }
            $data['adv_tax'] = $advtax*$qty;



            $further_tax = 0;
            if($customer_gst_no == ""){
                if($data['tax'] > 0){
                    if($detail->tax_rate_type==1){
                        $further_tax_setting = $this->sales_model->further_tax();
                        $further_tax_per = $further_tax_setting->further_tax;
                        $further_tax = (($sprice/100)*$further_tax_per);
                    }
                }
            }
            $data['further_tax'] = $further_tax;
            // $data['subtotal'] = (($data['sellingprice']+$data['tax'])*$qty)+$further_tax-$data['d'];
            $subtotal = ($data['sellingprice']*$qty)+$data['tax']+$data['adv_tax']+$further_tax-$data['d'];
            $data['subtotal'] = (float)$subtotal;
        }
        else{
            $sendvalue['codestatus'] = 'Batch not available';
        }

        $sendvalue['detail'] = $data;
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
        $rows['results'] = $this->sales_model->productslist($term, $limit, $supplier_id, $warehouse_id, $suown_companypplier_id);
        $this->sma->send_json($rows);
    }
    public function additem(){
        $sendvalue['codestatus'] = 'no';
        $id = $this->input->post('sid');
        $this->db->select('sma_sales.*,sma_companies.gst_no as customer_gst_no,sma_companies.sales_type as customer_sales_type');
        $this->db->from('sma_sales');
        $this->db->join('sma_companies', 'sma_companies.id = sma_sales.customer_id', 'left');
        $this->db->where('sma_sales.id',$id);
        $q = $this->db->get();
        if($q->num_rows()>0){
            $salesdetail = $q->result()[0];
            $product_id = $this->input->post('product');
            $batch = $this->input->post('batch');
            $qty = $this->input->post('qty');
            $this->db->select('
                sma_purchase_items.*,
                sma_products.company_code,
                sma_products.type as product_type,
                sma_products.adv_tax_reg as adv_tax_reg,
                sma_products.adv_tax_nonreg as adv_tax_nonreg,
                sma_tax_rates.id as tax_rate_id,
                sma_tax_rates.name as tax_rate_name,
                sma_tax_rates.rate as tax_rate_rate,
                sma_tax_rates.code as tax_rate_code,
                sma_tax_rates.type as tax_rate_type,
            ');
            $this->db->from('sma_purchase_items');
            $this->db->join('sma_products', 'sma_products.id  = sma_purchase_items.product_id', 'left');
            $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_products.tax_rate', 'left');
            $this->db->where('sma_purchase_items.product_id',$product_id);
            $this->db->where('sma_purchase_items.warehouse_id',$salesdetail->warehouse_id);
            $this->db->where('sma_purchase_items.batch',$batch);
            $p_q = $this->db->get();
            if($p_q->num_rows() > 0){
                $productdetail = $p_q->result()[0];
                if($productdetail->quantity_balance>=$qty){
                    $selling_price = 0; 
                    if ($salesdetail->customer_sales_type === 'cost') {
                        $selling_price = $productdetail->net_unit_cost;
                    }
                    else{
                        $selling_price = $productdetail->price;
                    }
                    $itemitemdata['sale_id'] = $this->input->post('sid');
                    $itemitemdata['product_id'] = $productdetail->product_id;
                    $itemitemdata['product_code'] = $productdetail->product_code;
                    $itemitemdata['company_code'] = $productdetail->company_code;
                    $itemitemdata['product_name'] = $productdetail->product_name;
                    $itemitemdata['product_type'] = $productdetail->product_type;
                    $itemitemdata['option_id'] = $productdetail->option_id;
                    $itemitemdata['net_unit_price'] = $selling_price;
                    $itemitemdata['product_price'] = $selling_price;
                    $itemitemdata['unit_price'] = $selling_price;
                    $itemitemdata['consignment'] = $productdetail->price;
                    $itemitemdata['dropship'] = $productdetail->dropship;
                    $itemitemdata['crossdock'] = $productdetail->crossdock;
                    $itemitemdata['mrp'] = $productdetail->mrp;
                    $itemitemdata['expiry'] = $productdetail->expiry;
                    $itemitemdata['batch'] = $productdetail->batch;
                    $itemitemdata['quantity'] = $qty;
                    $itemitemdata['warehouse_id'] = $productdetail->warehouse_id;
                    $itemitemdata['tax_rate_id'] = $productdetail->tax_rate_id;
                    $itemitemdata['tax'] = $productdetail->tax_rate_rate;
                    $itemitemdata['real_unit_price'] = $selling_price;
                    $itemitemdata['product_unit_id'] = $productdetail->product_unit_id;
                    $itemitemdata['product_unit_code'] = $productdetail->product_unit_code;
                    $itemitemdata['unit_quantity'] = $qty;
                    $itemitemdata['gst'] = $productdetail->gst;
                    $itemitemdata['cgst'] = $productdetail->cgst;
                    $itemitemdata['sgst'] = $productdetail->sgst;
                    $itemitemdata['igst'] = $productdetail->igst;
                    $itemitemdata['discount_one'] = 0;
                    $itemitemdata['discount_two'] = 0;
                    $itemitemdata['discount_three'] = 0;
                    $d1 = 0;
                    if(isset($_POST['done_chk'])){
                        $itemitemdata['discount_one'] = $_POST['done_chk'];
                        $d1 = (($selling_price/100)*$itemitemdata['discount_one'])*$qty;
                    }
                    $d2 = 0;
                    if(isset($_POST['dtwo_chk'])){
                        $itemitemdata['discount_two'] = $_POST['dtwo_chk'];
                        $d2 = (($selling_price/100)*$itemitemdata['discount_two'])*$qty;
                    }
                    $d3 = 0;
                    if(isset($_POST['dth_chk'])){
                        $itemitemdata['discount_three'] = $_POST['dth_chk'];
                        $d3 = (($selling_price/100)*$itemitemdata['discount_three'])*$qty;
                    }
                    $itemitemdata['discount'] = $d1+$d2+$d3;
                    $itemitemdata['item_discount'] = $d1+$d2+$d3;
                    $itemitemdata['fed_tax'] = $this->input->post('fed');
                    if($productdetail->tax_rate_type == 1){
                        $itemitemdata['item_tax'] = (($selling_price/100)*$productdetail->tax_rate_rate)*$qty;
                    }
                    else{
                        $itemitemdata['item_tax'] = $productdetail->tax_rate_rate*$qty;
                    }
                    if($salesdetail->customer_gst_no == ""){
                        // if($itemitemdata['item_tax'] > 0){
                        if($productdetail->tax_rate_type == 1){
                            $further_tax_setting = $this->sales_model->further_tax();
                            $further_tax = $further_tax_setting->further_tax;
                            $itemitemdata['further_tax'] = (($selling_price/100)*$further_tax)*$qty;
                        }
                        else{
                            $itemitemdata['further_tax'] = 0;
                        }
                    }
                    else{
                        $itemitemdata['further_tax'] = 0;
                    }
                    $itemitemdata['adv_tax'] = 0;
                    if($salesdetail->customer_gst_no == ""){
                        $itemitemdata['adv_tax'] = decimalallow(((($selling_price*$qty)+$itemitemdata['item_tax'])/100)*$productdetail->adv_tax_nonreg,2);
                    }
                    else{
                        $itemitemdata['adv_tax'] = decimalallow(((($selling_price*$qty)+$itemitemdata['item_tax'])/100)*$productdetail->adv_tax_reg,2);
                    }
                    $totaltax = $itemitemdata['item_tax']+$itemitemdata['further_tax']+$itemitemdata['adv_tax'];
                    $itemitemdata['subtotal'] = ($selling_price*$qty)+$totaltax-$itemitemdata['discount'];
                    $this->db->insert('sma_sale_items', $itemitemdata);
                    $this->updateQty($product_id,$salesdetail->warehouse_id,$productdetail->id,$qty,'-');
                    $this->updateSalePrice($id);
                    $activitynote = '<h6>New Item Data:</h6>';
                    $activitynote .= '<ul style="margin-left: 25px;" >';
                        $activitynote .= '<li>Item Name: '.$productdetail->product_name.'</li>';
                        $activitynote .= '<li>Qty: '.$qty.'</li>';
                        $activitynote .= '<li>Batch: '.$batch.'</li>';
                        $activitynote .= '<li>Discount 1: '.$itemitemdata['discount_one'].'</li>';
                        $activitynote .= '<li>Discount 2: '.$itemitemdata['discount_two'].'</li>';
                        $activitynote .= '<li>Discount 3: '.$itemitemdata['discount_three'].'</li>';
                    $activitynote .= '</ul>';
                    $this->useractivities_model->add([
                        'note'=>$activitynote,
                        'location'=>'Sales->Item->Add->Submit',
                        'product_id'=>$productdetail->product_id,
                        'sale_id'=>$this->input->post('sid'),
                        'action_by'=>$this->session->userdata('user_id')
                    ]);
                    $this->load->admin_model('stores_model');
                    $sendvalue['apistatus'] = $this->stores_model->updateStoreQty($product_id,$salesdetail->warehouse_id,0,'Add Item in Sale');
                    $sendvalue['codestatus'] = 'Item Add Successfully';
                }
                else{
                    $sendvalue['codestatus'] = $qty.' quantity not available in '.$batch;
                }
            }
            else{
                $sendvalue['codestatus'] = 'Invalid Product';
            }
        }
        else{
            $sendvalue['codestatus'] = 'Invalid Sale';
        }
        echo json_encode($sendvalue);
    }
    public function delete($id = null){
        $this->sma->checkPermissions(null, true);
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $reason = $this->input->get('reason');
        if($reason != ""){
            $json_data = array();
            $this->db->select('*');
            $this->db->from('sma_sales');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $sale = $q->result()[0];
                $json_data['sale'] = $sale;
                if ($sale->sale_status == 'returned') {
                    $this->sma->send_json(array('error' => 1, 'msg' => lang("sale_x_action")));
                }
                $this->db->select('*');
                $this->db->from('sma_sale_items');
                $this->db->where('sale_id',$id);
                $itemq = $this->db->get();
                $items = $itemq->result();
                $json_data['items'] = $items;
                foreach($items as $item){
                    $this->db->select('id,quantity_balance');
                    $this->db->from('sma_purchase_items');
                    $this->db->where('product_id',$item->product_id);
                    $this->db->where('warehouse_id',$item->warehouse_id);
                    $this->db->where('batch',$item->batch);
                    $q2 = $this->db->get();
                    if($q2->num_rows() > 0){
                        $q2r = $q2->result()[0];
                        $this->db->delete('sma_sale_items', array('id' => $item->id));
                        $this->updateQty($item->product_id,$item->warehouse_id,$q2r->id,$item->quantity,'+');
                        $this->load->admin_model('stores_model');
                        $sendvalue['apistatus'] = $this->stores_model->updateStoreQty($item->product_id,$item->warehouse_id,0,'Delete Sale');
                        $sendvalue['codestatus'] = 'ok';
                    }
        
                }
                $this->db->delete('sma_sales', array('id' => $id));
                $this->db->delete('costing', array('sale_id' => $id));
                $this->db->delete('payments', array('sale_id' => $id));

                $srs = $this->db->select('id')->from('sma_sale_returns_tb')->where('sale_id',$id)->get()->result();
                foreach($srs as $sr){
                    $this->db->delete('sma_sale_returns_tb', array('id' => $sr->id));
                    $this->db->delete('sma_sale_return_items_tb', array('sale_return_id' => $sr->id));
                }
                $setdata['status'] = 'deleted';
                $this->db->set($setdata);
                $this->db->where('id',$sale->soc_id);
                $this->db->update('sma_sales_order_complete_tb');
    
                $activitynote = '<h6>Sale Deleted. Reference No: '.$sale->reference_no.'</h6>';
                $activitynote .= '<p style="color:red" ><b>Reason:</b> '.$reason.'</p>';
                $json_data = json_encode($json_data);
                $this->useractivities_model->add([
                    'note'=>$activitynote,
                    'json_data'=>$json_data,
                    'location'=>'Sales->Detail->Edit->Submit',
                    'sale_id'=>$id,
                    'action_by'=>$this->session->userdata('user_id')
                ]);
                $sendvalue['codestatus'] = "Sale Deleted";
            }
            else{
                $sendvalue['codestatus'] = "Sale Invalid";
            }
        }
        else{
            $sendvalue['codestatus'] = "Enter Reason";
        }
        echo json_encode($sendvalue);
    }
    public function getaddress(){
        $sendvalue['html'] = '<option value="0">Default Address</option>';
        $id =  $this->input->post('customerID');
        $sendvalue['pricemessage'] = "This sale calculate on Consignment (TP) Price!";
        $this->db->select('sales_type');
        $this->db->from('sma_companies');
        $this->db->where('id',$id);
        $q2 = $this->db->get();
        if($q2->num_rows() > 0){
            if($q2->result()[0]->sales_type == "cost"){
                $sendvalue['pricemessage'] = "This sale calculate on Cost (DP) Price!";
            }
        }

        $this->db->select('*');
        $this->db->from('sma_addresses');
        $this->db->where('company_id',$id);
        $q = $this->db->get();
        $rows = $q->result();
        foreach($rows as $row){
            $sendvalue['html'] .= '<option value="'.$row->id.'">'.$row->line1.'</option>';
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
    public function return_sale($id = null){
        if($id != ""){
            $items = array();
            $this->db->select('*');
            $this->db->from('sma_sale_items');
            $this->db->where('sale_id',$id);
            $q = $this->db->get();
            $this->data['items'] = $q->result();
            $this->data['sale_id'] = $id;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Sale Return'));
            $meta = array('page_title' => 'Sale Return', 'bc' => $bc);
            $this->page_construct2('sales/return_sale', $meta, $this->data);
        }
        else{
            redirect(base_url('admin/sales'));
        }
    }
    public function return_sale_submit(){
        $sendvalue['status'] = false;
        $returnitems = array();
        $sale_item_id = $this->input->post('saleitem_id');
        $rqty = $this->input->post('rqty');
        $reason = $this->input->post('reason');
        $sale_id = $this->input->post('sale_id');
        $returndata['sale_id'] = $sale_id;
        $returndata['date'] = date('Y-m-d');
        $returndata['total'] = 0;
        $returndata['total_tax'] = 0;
        $returndata['total_discount'] = 0;
        $returndata['created_by'] = $this->session->userdata('user_id');
        $this->db->select('customer_id');
        $this->db->from('sma_sales');
        $this->db->where('id',$sale_id);
        $sq = $this->db->get();
        if($sq->num_rows()>0){
            $sale_detail = $sq->result()[0];
            foreach($sale_item_id as $key => $value){
                if($rqty[$key] > 0 && $reason[$key] == ""){
                    die("Enter Reason");
                    exit();
                }
                else if ($rqty[$key] == 0 && $reason[$key] != ""){
                    die("Enter Return Quantity");
                    exit();
                }
                else{
                    if($rqty[$key] > 0 && $reason[$key] != ""){
                        $this->db->select('*');
                        $this->db->from('sma_sale_items');
                        $this->db->where('id',$sale_item_id[$key]);
                        $q = $this->db->get();
                        if($q->num_rows() > 0){
                            $item = $q->result()[0];
                            $itmtax = (($item->net_unit_price/100)*$item->tax)*$rqty[$key];
                            $futtax = ($item->further_tax/$item->quantity)*$rqty[$key];
                            $fedtax = ($item->fed_tax/$item->quantity)*$rqty[$key];
                            $totaltax = $itmtax+$futtax+$fedtax;
                            // $discount1 = ($item->net_unit_price*$rqty[$key])/100*$item->discount_one;
                            // $discount2 = ($item->net_unit_price*$rqty[$key])/100*$item->discount_two;
                            // $discount3 = ($item->net_unit_price*$rqty[$key])/100*$item->discount_three;
                            // $totaldiscount = $discount1+$discount2+$discount3;

                            $totaldiscount = ($item->item_discount/$item->quantity)*$rqty[$key];
                            $subtotal = ($item->net_unit_price*$rqty[$key])+$totaltax-$totaldiscount;
                            $returnitems[] = array(
                                "product_id" => $item->product_id,
                                "net_unit_price" => $item->net_unit_price,
                                "dropship" => $item->dropship,
                                "crossdock" => $item->crossdock,
                                "mrp" => $item->mrp,
                                "expiry" => $item->expiry,
                                "batch" => $item->batch,
                                "quantity" => $rqty[$key],
                                "warehouse_id" => $item->warehouse_id,
                                "item_tax_id" => $item->tax_rate_id,
                                "item_tax" => $itmtax,
                                "further_tax" => $futtax,
                                "fed_tax" => $fedtax,
                                "total_tax" => $totaltax,
                                "discount_one" => $item->discount_one != "" ? $item->discount_one : 0,
                                "discount_two" => $item->discount_two != "" ? $item->discount_two : 0,
                                "discount_three" => $item->discount_three != "" ? $item->discount_three : 0,
                                "total_discount" => $totaldiscount,
                                "subtotal" => $subtotal,
                                "reason" => $reason[$key]
                            );
                            $returndata['total'] += $subtotal;
                        }
                    }
                }
            }
            if(count($returnitems)>0){
                $returndata['total_tax'] = 0;
                $returndata['total_discount'] = 0;
                $returndata['grand_total'] = $returndata['total']+$returndata['total_tax']-$returndata['total_discount'];
                $this->db->insert('sma_sale_returns_tb', $returndata);
                $return_id = $this->db->insert_id();
                foreach($returnitems as $row){
                    $this->db->select('id,quantity_balance');
                    $this->db->from('sma_purchase_items');
                    $this->db->where('product_id',$row['product_id']);
                    $this->db->where('warehouse_id',$row['warehouse_id']);
                    $this->db->where('batch',$row['batch']);
                    $q2 = $this->db->get();
                    if($q2->num_rows() > 0){
                        $pitems = $q2->result()[0];
                        $row['sale_return_id'] = $return_id;
                        $this->db->insert('sma_sale_return_items_tb', $row);
                        $this->updateQty($row['product_id'],$row['warehouse_id'],$pitems->id,$row['quantity'],'+');
                    }
                }
                $refund = $this->input->post('refund');
                if($refund == "yes"){
                    $payment = array(
                        'date' => date('Y-m-d'),
                        'sale_id' => $sale_id,
                        'sale_return_id' => $return_id,
                        'reference_no' => $this->site->getReference('pay'),
                        'amount' => '-'.$returndata['grand_total'],
                        'hold_amount' => 0,
                        'paid_by' => 'cash',
                        'cheque_no' => '',
                        'cc_no' => '',
                        'cc_holder' => '',
                        'cc_month' => '',
                        'cc_year' => '',
                        'cc_type' => '',
                        'note' => 'Return Sale Refund Payment',
                        'created_by' => $this->session->userdata('user_id'),
                        'type' => 'received',
                        'status' => '',
                        'cpr_no' => '',
                        'credit_no_per' => ''
                    );
                    $this->sales_model->addPayment($payment, $sale_detail->customer_id);
                }
                die("Return Successfully");
                exit();
            }
        }
        else{
            die("Invalid Sale");
            exit();
        }

    }
    public function editinfo($id){
        $inv = $this->sales_model->getInvoiceByID($id);
        $this->data['inv'] = $inv;
        $this->data['addresslist'] = $this->getCustomerAddressList($inv->customer_id);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['units'] = $this->site->getAllBaseUnits();
        $this->data['own_company'] = $this->site->getAllown_companies();
        $this->data['suppliers'] = $this->site->getAllCompanies('supplier');
        $this->data['lcustomers'] = $this->site->getAllCompanies('customer');
        $this->data['billerslist'] = $this->site->getAllCompaniesBiller('biller');
        $this->load->view($this->theme . 'sales/editinfo', $this->data);

    }
    public function returndelete(){
        $sendvalue['codestatus'] = "";
        $id = $this->input->get('id');
        $reason = $this->input->get('reason');
        if($reason != ""){
            $sale_id = 0;
            $finaldata = array();
            $this->db->select('
                sma_sale_returns_tb.sale_id,
                sma_sale_return_items_tb.id,
                sma_sale_return_items_tb.quantity,
                sma_sale_return_items_tb.batch,
                sma_sale_return_items_tb.product_id,
                sma_sale_return_items_tb.warehouse_id
            ');
            $this->db->from('sma_sale_return_items_tb');
            $this->db->join('sma_sale_returns_tb','sma_sale_returns_tb.id = sma_sale_return_items_tb.sale_return_id');
            $this->db->where('sale_return_id',$id);
            $q = $this->db->get();
            $items = $q->result();
            foreach($items as $item){
                $sale_id = $item->sale_id;
                $this->db->select('id,product_name,quantity_balance');
                $this->db->from('sma_purchase_items');
                $this->db->where('product_id',$item->product_id);
                $this->db->where('warehouse_id',$item->warehouse_id);
                $this->db->where('batch',$item->batch);
                $q2 = $this->db->get();
                if($q2->num_rows() > 0){
                    $pitem = $q2->result()[0];
                    if($pitem->quantity_balance>=$item->quantity){
                        $temp['qty'] = (int)$item->quantity;
                        $temp['pid'] = $item->product_id;
                        $temp['wid'] = $item->warehouse_id;
                        $temp['iid'] = $pitem->id;
                        $temp['riid'] = $item->id;
                        $temp['batch'] = $item->batch;
                        $finaldata[] = $temp;
                    }
                    else{
                        $sendvalue['codestatus'] = "Return sale not delete. ".$pitem->sma_purchase_items."Qty Not Available.";
                        $sendvalue['purchase_item_id'] = $pitem->id;
                        echo json_encode($sendvalue);
                        exit();
                    }
                }
                else{
                    $sendvalue['codestatus'] = "Item not found";
                    echo json_encode($sendvalue);
                    exit(); 
                }
            }
            foreach($finaldata as $row){
                $this->db->delete('sma_sale_return_items_tb', array('id' => $row['riid']));
                $this->updateQty($row['pid'],$row['wid'],$row['iid'],$row['qty'],'-');
            }
            if(count($finaldata)>0){
                $this->db->delete('sma_sale_returns_tb', array('id' => $id));
                $sendvalue['codestatus'] = "Return sale deleted";
                echo json_encode($sendvalue);
            }
            else{
                $sendvalue['codestatus'] = "Return sale not delete";
                echo json_encode($sendvalue);
            }
            $activitynote = 'Return ID: '.$id.'<br><p style="color:red" ><b>Reason:</b> '.$reason.'</p>';
            $this->useractivities_model->add([
                'note'=>$activitynote,
                'location'=>'Sales->Detail->Return->Delete',
                'sale_id'=>$sale_id,
                'action_by'=>$this->session->userdata('user_id')
            ]);
        }
        else{
            $sendvalue['codestatus'] = "Enter Reason";
            echo json_encode($sendvalue);
            exit();
        }
    }
}
