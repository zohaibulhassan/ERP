<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class Pos extends MY_Controller{
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
        $this->load->admin_model('pos_model');
    }
    public function index(){
        $this->data['categories'] = $this->pos_model->categories(0);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'POS'));
        $meta = array('page_title' => 'POS', 'bc' => $bc);

        $user_id = $this->session->userdata('user_id');
        $this->db->select('*');
        $this->db->from('pos_register');
        $this->db->where('user_id',$user_id);
        $this->db->where('status != "close"');
        $rq = $this->db->get();
        if($rq->num_rows() == 0){
            
            $this->page_construct2('pos/open_register', $meta, $this->data);
        }
        else{   
            $this->data['register'] = $rq->result()[0];
            $this->data['bulkdiscounts'] = $this->pos_model->bulkdiscounts();
            $this->data['warehouse_id'] = 4;
            $this->data['leftmenu_open'] = false;
            $this->data['hold_bill_status'] = false;
            $hold_id = $this->input->get('hold');
            if($hold_id != ""){
                $this->data['hold_bill'] = $this->db->from('suspended_bills')->where('id',$hold_id)->get()->result();
                if(count($this->data['hold_bill']) > 0){
                    $this->data['hold_bill_status'] = true;
                    $products = $this->db->select('
                        products.id,
                        products.code,
                        products.company_code,
                        products.name,
                        products.cost,
                        suspended_items.net_unit_price as price,
                        products.carton_size,
                        products.formulas,
                        products.category_id,
                        products.subcategory_id,
                        suspended_items.mrp,
                        products.brand,
                        products.alert_quantity,
                        COALESCE((
                            SELECT SUM(sma_purchase_items.quantity_balance) FROM sma_purchase_items WHERE sma_purchase_items.product_id = suspended_items.product_id AND sma_purchase_items.warehouse_id = suspended_items.warehouse_id
                        ),0) as balance_qty,
                        products.fed_tax,
                        products.tax_method,
                        tax_rates.name as tax_name,
                        tax_rates.rate as tax_rate,
                        tax_rates.type as tax_type,
                        0 as product_tax,
                        0 as product_discount_all,
                        0 as product_discount_pos,
                        0 as product_discount_web,
                        suspended_items.quantity,
                        suspended_items.batch as batch,
                        suspended_items.expiry as expiry,
                        suspended_items.purchase_item_id as piid
                        ')->from('suspended_items as suspended_items')
                        ->join('products','products.id = suspended_items.product_id','left')
                        ->join('tax_rates','tax_rates.id = products.tax_rate','left')
                        ->where('suspended_items.suspend_id ',$hold_id)
                        ->get()->result();

                        foreach($products as $key => $product){
                            $this->db->select('percentage');
                            $this->db->from('bulk_discount');
                            $this->db->group_start();
                            $start_like = true;
                            if($product->brand != ""){
                                $this->db->like('brand_id',$product->brand);
                                $start_like = false;
                            }
                            if($product->id != ""){
                                if($start_like){
                                    $this->db->like('product_id',$product->id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('product_id',$product->id);
                                }
                            }
                            if($product->category_id != ""){
                                if($start_like){
                                    $this->db->like('category_id',$product->category_id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('category_id',$product->category_id);
                                }
                            }
                            if($product->subcategory_id != ""){
                                if($start_like){
                                    $this->db->like('category_id',$product->subcategory_id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('category_id',$product->subcategory_id);
                                }
                            }
                            $this->db->group_end();
                            $this->db->where('start_date <= ',date('Y-m-d H:i:s'));
                            $this->db->where('end_date >= ',date('Y-m-d H:i:s'));
                            $this->db->where('end_date >= ',date('Y-m-d H:i:s'));
                            $this->db->where('type',2);
                            $this->db->where('apply_on','all');
                            $bq1 = $this->db->get();

                            if($bq1->num_rows() > 0){
                                $discount = $bq1->result()[0];
                                $products[$key]->product_discount_all = $product->mrp/100*$discount->percentage;
                            }

                            $this->db->select('percentage');
                            $this->db->from('bulk_discount');
                            $this->db->group_start();
                            $start_like = true;
                            if($product->brand != ""){
                                $this->db->like('brand_id',$product->brand);
                                $start_like = false;
                            }
                            if($product->id != ""){
                                if($start_like){
                                    $this->db->like('product_id',$product->id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('product_id',$product->id);
                                }
                            }
                            if($product->category_id != ""){
                                if($start_like){
                                    $this->db->like('category_id',$product->category_id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('category_id',$product->category_id);
                                }
                            }
                            if($product->subcategory_id != ""){
                                if($start_like){
                                    $this->db->like('category_id',$product->subcategory_id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('category_id',$product->subcategory_id);
                                }
                            }
                            $this->db->group_end();
                            $this->db->where('start_date <= ',date('Y-m-d H:i:s'));
                            $this->db->where('end_date >= ',date('Y-m-d H:i:s'));
                            $this->db->where('end_date >= ',date('Y-m-d H:i:s'));
                            $this->db->where('type',2);
                            $this->db->where('apply_on','pos');
                            $bq2 = $this->db->get();
                            if($bq2->num_rows() > 0){
                                $discount = $bq2->result()[0];
                                $products[$key]->product_discount_pos = $product->mrp/100*$discount->percentage;
                            }

                            $this->db->select('percentage');
                            $this->db->from('bulk_discount');
                            $this->db->group_start();
                            $start_like = true;
                            if($product->brand != ""){
                                $this->db->like('brand_id',$product->brand);
                                $start_like = false;
                            }
                            if($product->id != ""){
                                if($start_like){
                                    $this->db->like('product_id',$product->id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('product_id',$product->id);
                                }
                            }
                            if($product->category_id != ""){
                                if($start_like){
                                    $this->db->like('category_id',$product->category_id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('category_id',$product->category_id);
                                }
                            }
                            if($product->subcategory_id != ""){
                                if($start_like){
                                    $this->db->like('category_id',$product->subcategory_id);
                                    $start_like = false;
                                }
                                else{
                                    $this->db->or_like('category_id',$product->subcategory_id);
                                }
                            }
                            $this->db->group_end();
                            $this->db->where('start_date <= ',date('Y-m-d H:i:s'));
                            $this->db->where('type',2);
                            $this->db->where('end_date >= ',date('Y-m-d H:i:s'));
                            $this->db->where('end_date >= ',date('Y-m-d H:i:s'));
                            $this->db->where('apply_on','website');
                            $bq3 = $this->db->get();
                            if($bq3->num_rows() > 0){
                                $discount = $bq3->result()[0];
                                $products[$key]->product_discount_web = $product->mrp/100*$discount->percentage;
                            }
                        }
            
                        $this->data['hold_bill_items'] = $products;
                }
                else{
                }
            }
            else{
            }
            $this->page_construct2('pos/index', $meta, $this->data);
        }
        
    }
    public function productlist(){
        $sendvalue['status'] = true;
        $type = $this->input->get('type');
        $category = $this->input->get('category');
        $brand = $this->input->get('brand');
        $html = "";
        $this->db->select('id,name,mrp,code');
        $this->db->from('products');
        if($type == "category"){
            if($category != ""){
                $this->db->where('category_id = '.$category.' OR subcategory_id = '.$category);
            }
        }
        else if($type == "brand"){
            if($brand != ""){
                $this->db->where('brand = '.$brand);
            }
        }
        if($category == "" && $brand == ""){
            $this->db->limit(100);
        }
        $q = $this->db->get();
        $products = $q->result();
        foreach($products as $product){
            $html .= '';
            $html .= '<div class="productdiv" data-barcode="'.$product->code.'" data-pid="'.$product->id.'" >';
                $html .= '<div class="md-card md-card-hover-img">';
                    // $html .= '<div class="md-card-head uk-text-center uk-position-relative">';
                        // $html .= '<img class="md-card-head-img" src="base_url('/themes/v1/assets/img/ecommerce/s6_edge.jpg')" alt=""/>';
                    // $html .= '</div>';
                    $html .= '<div class="md-card-content">';

                        $html .= '<button>';
                            $html .= $product->name;
                        $html .= '</button>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';

        }
        $sendvalue['html'] = $html;
        echo json_encode($sendvalue);
    }
    public function similarformula(){
        $sendvalue['status'] = true;
        $formulas = $this->input->get('formulas');
        $html = "";
        $this->db->select('id,name,mrp,code');
        $this->db->from('products');
        $this->db->where('formulas = '.$formulas);
        $this->db->where('quantity > 0');
        $q = $this->db->get();
        $products = $q->result();
        foreach($products as $product){
            $html .= '';
            $html .= '<div class="productdiv" data-barcode="'.$product->code.'" data-pid="'.$product->id.'" >';
                $html .= '<div class="md-card md-card-hover-img">';
                    $html .= '<div class="md-card-content">';
                        $html .= '<button>';
                            $html .= $product->name;
                        $html .= '</button>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';

        }
        $sendvalue['html'] = $html;
        echo json_encode($sendvalue);
    }
    public function hold_bill(){
        $sendvalue['status'] = false; 
        $sendvalue['message'] = ""; 
        $customer = $this->input->post('customer');
        if($customer == ""){
            $sendvalue['message'] = "Please select customer"; 
        }
        else{
            $cq = $this->db->select('id,name')->from('companies')->where('id',$customer)->get();
            if($cq->num_rows() == 0){
                $sendvalue['message'] = "Invalid Customer"; 
            }
            else{
                $customerdata = $cq->result()[0];
                $insert['date'] = date('Y-m-d H:i:s');
                $insert['reference_no'] = date('Y').''.date('m').''.date('d').''.date('H').''.date('i').''.date('s').''.$customerdata->id.'4'.rand(100,999);
                $insert['customer_id'] = $customerdata->id;
                $insert['customer'] = $customerdata->name;
                $insert['count'] = 0;
                $insert['order_discount_id'] = $this->input->post('discount');
                $insert['discount'] = $this->input->post('discount_val');
                $insert['shipping'] = $this->input->post('charges');
                $insert['total'] = 0;
                $insert['biller_id'] = 48;
                $insert['warehouse_id'] = 4;
                $insertitems = array();
                $products = $this->input->post('product_id');
                $qty = $this->input->post('qty');
                $batch = $this->input->post('batch');
                $expiry = $this->input->post('expiry');
                $purchase_item_id = $this->input->post('pi_id');
                $totalpdiscount = $this->input->post('totalpdiscount');
                if($products == "" || count($products) == 0){
                    $sendvalue['message'] = "Please select product"; 
                }
                else{
                    foreach($products as $key => $row){

                        $this->db->select('
                            pi.*,
                            p.name,
                            p.code,
                            p.company_code
                        ');
                        $this->db->from('purchase_items as pi');
                        $this->db->join('products as p','p.id = pi.product_id','left');
                        $this->db->where('pi.id',$purchase_item_id[$key]);
                        $this->db->where('pi.quantity_balance > 0');
                        $pq =  $this->db->get();
                        // $purchases = $pq->result();
                        // $pq = $this->db->from('products')->where('id',$row)->get();
                        if($pq->num_rows() > 0){
                            $product = $pq->result()[0];
                            $insert['count']++;
                            $temp['product_id '] = $product->product_id;
                            $temp['product_code'] = $product->code;
                            $temp['company_code'] = $product->company_code;
                            $temp['product_name'] = $product->name;
                            $temp['net_unit_price'] = $product->mrp;
                            $temp['unit_price'] = $product->price;
                            $temp['dropship'] = $product->dropship;
                            $temp['crossdock'] = $product->crossdock;
                            $temp['mrp'] = $product->mrp;
                            $temp['quantity'] = $qty[$key];
                            $temp['purchase_item_id'] = $purchase_item_id[$key];
                            $temp['item_discount'] = $totalpdiscount[$key];
                            $temp['subtotal'] = ($qty[$key]*$product->mrp)-$temp['item_discount'];
                            $temp['real_unit_price'] = $product->price;
                            $temp['unit_quantity'] = $qty[$key];
                            $temp['product_price'] = $product->mrp;
                            $temp['expiry'] = $expiry[$key];
                            $temp['batch'] = $batch[$key];
                            $temp['warehouse_id'] = 4;
                            $temp['cgst'] = 0;
                            $temp['sgst'] = 0;
                            $temp['igst'] = 0;
                            $temp['discount_one'] = 0;
                            $temp['discount_two'] = 0;
                            $temp['discount_three'] = 0;
                            $temp['further_tax'] = 0;
                            $temp['fed_tax'] = 0;
                            $insertitems[] = $temp;
                            $insert['total'] = $insert['total'] + $temp['subtotal'];
                        }
                    }
                    $insert['order_discount_id'] = $this->input->post('discount');
                    $insert['discount'] = $this->input->post('discount_val');
                    $insert['shipping'] = $this->input->post('charges');
                    $insert['total'] = $insert['total']+$insert['shipping']-$insert['discount'];
                    // print_r($insert);
                    // print_r($insertitems);
                    // exit();
                    if(count($insertitems) > 0){
                        $this->db->insert('suspended_bills',$insert);
                        $insert_id = $this->db->insert_id();
                        $owncompany = $this->db->select('*')->from('own_companies')->where('id',5)->get()->row();
                        $store['name'] = $owncompany->companyname;
                        $store['address1'] = $owncompany->registeraddress;
                        $store['receipt_header'] = $owncompany->slip_header;
                        $store['receipt_footer'] = $owncompany->slip_footer;
                        $store['city'] = '';
                        $store['phone'] = $owncompany->mobile;
                        $store['ntn'] = $owncompany->ntn;
                        $store['strn'] = $owncompany->strn;
        
                        $sale['customer'] = $insert['customer'];
                        $sale['reference_no'] = $insert['reference_no'];
                        $sale['date'] = date('Y-m-d H:i:s');
                        $items = array();
                        foreach($insertitems as $insertitem){
                            $insertitem['suspend_id '] = $insert_id;
                            $this->db->insert('suspended_items',$insertitem);
                            $temp['product_name'] = $insertitem['product_name'];
                            $temp['quantity'] = $insertitem['quantity'];
                            $items[] = $temp;
                        }
                        $uq = $this->db->select('id,first_name,last_name')->from('users')->where('username',$_SESSION['username'])->get();
                        if($uq->num_rows() > 0){
                            $user = $uq->result()[0];
                            $created_by = $user->first_name.' '.$user->last_name;
                            $printer = $this->site->getPrinterByUser( $user->id);
                            if($printer){
                                
                                $sale = $this->db->select('*')->from('suspended_bills')->where('id',$insert_id)->get()->row();
                                $sitems = $this->db->select('*')->from('suspended_items')->where('suspend_id',$insert_id)->get()->result();
                                $customer = $this->db->select('*')->from('companies')->where('id',$sale->customer_id)->get()->row();
                                $payments = array();
                                
                                $a = array(
                                    'printer' => $printer,
                                    'store' => $store,
                                    'sale' => $sale,
                                    'items' => $sitems,
                                    'payments' => $payments,
                                    'customer' => $customer,
                                    'created_by' => $created_by
                                );
                                $a = json_encode($a);
                                // $a = urlencode($a);
                                
                                $sendvalue['url'] = "http://localhost/rhoprinter/printers/hold_bill?data=".$a;
                                $a2 = array(
                                    'printer' => $printer
                                );
                                $a2 = json_encode($a2);
                                $a2 = urlencode($a2);
                                $sendvalue['url2'] = "http://localhost/rhoprinter/printers/hold_bill?data=".$a2;
                                $sendvalue['form_data'] = $a;
                                
                                // $this->load->library('escpos');
                                // $this->escpos->load($printer);
                                // $this->escpos->hold_bill($store, $sale, $sitems,$payments, $created_by,true);
                            }
                            else{
                                $sendvalue['status2'] = 2; 
                            }
                        }
                        else{
                            
                            $sendvalue['status2'] = 0; 
                        }
                        $sendvalue['message'] = "Bill on hold successfully"; 
                        $sendvalue['status'] = true; 
                    }
                    else{
                        $sendvalue['message'] = "Please select product"; 
                    }
                }
            }    
        }
        echo json_encode($sendvalue);
    }
    public function hold_bill_print(){
        $sendvalue['status'] = false; 
        $sendvalue['message'] = ""; 
        $id = $this->input->get('id');
        $uq = $this->db->select('id,first_name,last_name')->from('users')->where('username',$_SESSION['username'])->get();
        if($uq->num_rows() > 0){
            $user = $uq->result()[0];
            $created_by = $user->first_name.' '.$user->last_name;
            $printer = $this->site->getPrinterByUser( $user->id);
            if($printer){
                $owncompany = $this->db->select('*')->from('own_companies')->where('id',5)->get()->row();

                $store['name'] = $owncompany->companyname;
                $store['address1'] = $owncompany->registeraddress;
                $store['receipt_header'] = $owncompany->slip_header;
                $store['receipt_footer'] = $owncompany->slip_footer;
                $store['city'] = '';
                $store['phone'] = $owncompany->mobile;
                $store['ntn'] = $owncompany->ntn;
                $store['strn'] = $owncompany->strn;
            
            
                $sale = $this->db->select('*')->from('suspended_bills')->where('id',$id)->get()->row();
                $sitems = $this->db->select('*')->from('suspended_items')->where('suspend_id',$id)->get()->result();
                $customer = $this->db->select('*')->from('companies')->where('id',$sale->customer_id)->get()->row();
                $payments = array();
                
                $a = array(
                    'printer' => $printer,
                    'store' => $store,
                    'sale' => $sale,
                    'items' => $sitems,
                    'payments' => $payments,
                    'customer' => $customer,
                    'created_by' => $created_by
                );
                $a = json_encode($a);
                // $a = urlencode($a);
                
                $sendvalue['url'] = "http://localhost/rhoprinter/printers/hold_bill?data=".$a;
                $a2 = array(
                    'printer' => $printer
                );
                $a2 = json_encode($a2);
                $a2 = urlencode($a2);
                $sendvalue['url2'] = "http://localhost/rhoprinter/printers/hold_bill?data=".$a2;
                $sendvalue['form_data'] = $a;


                $sendvalue['status'] = true;
            }
            else{
                $sendvalue['message'] = 'Printer not defined';
            }
        }
        else{
            
            $sendvalue['message'] = 'Section Expired';
        }
        echo json_encode($sendvalue);
    }
    public function print_bill(){
        $sendvalue['status'] = false; 
        $sendvalue['message'] = ""; 
        $customer = $this->input->post('customer');
        if($customer == ""){
            $sendvalue['message'] = "Please select customer"; 
        }
        else{
            $cq = $this->db->select('id,name')->from('companies')->where('id',$customer)->get();
            if($cq->num_rows() == 0){
                $sendvalue['message'] = "Invalid Customer"; 
            }
            else{
                $customerdata = $cq->result()[0];

                $owncompany = $this->db->select('*')->from('own_companies')->where('id',5)->get()->row();
                $store['name'] = $owncompany->companyname;
                $store['address1'] = $owncompany->registeraddress;
                $store['receipt_header'] = $owncompany->slip_header;
                $store['receipt_footer'] = $owncompany->slip_footer;
                $store['city'] = '';
                $store['phone'] = $owncompany->mobile;
                $store['ntn'] = $owncompany->ntn;
                $store['strn'] = $owncompany->strn;



                $sale['customer'] = $customerdata->name;
                $sale['reference_no'] = '';
                $sale['date'] = date('Y-m-d H:i:s');
                $items = array();
                $products = $this->input->post('product_id');
                $qty = $this->input->post('qty');
                if($products == "" || count($products) == 0){
                    $sendvalue['message'] = "Please select product"; 
                }
                else{
                    foreach($products as $key => $row){
                        $pq = $this->db->from('products')->where('id',$row)->get();
                        if($pq->num_rows() > 0){
                            $product = $pq->result()[0];
                            $temp['product_name'] = $product->name;
                            $temp['quantity'] = $qty[$key];
                            $items[] = $temp;
                        }
                    }
                    if(count($items) > 0){
                        $uq = $this->db->select('id,first_name,last_name')->from('users')->where('username',$_SESSION['username'])->get();
                        if($uq->num_rows() > 0){
                            $user = $uq->result()[0];
                            $created_by = $user->first_name.' '.$user->last_name;
                            $printer = $this->site->getPrinterByUser( $user->id);

                            if($printer){
                                $a = array(
                                    'printer' => json_encode($printer),
                                    'store' => json_encode($store),
                                    'sale' => json_encode($sale),
                                    'items' => json_encode($items),
                                    'created_by' => json_encode($created_by)
                                );
                                $a = array(
                                    'printer' => $printer,
                                    'store' => $store,
                                    'sale' => $sale,
                                    'items' => $items,
                                    'created_by' => $created_by
                                );
                                $a = json_encode($a);
                                // $a = urlencode($a);

                                $sendvalue['url'] = "http://localhost/rhoprinter/printers/print_order?data=".$a;

                                $a2 = array(
                                    'printer' => $printer
                                );
                                $a2 = json_encode($a2);
                                $a2 = urlencode($a2);
                                $sendvalue['url2'] = "http://localhost/rhoprinter/printers/print_order?data=".$a2;
                                $sendvalue['form_data'] = $a;


                                // $this->load->library('escpos');
                                // $this->escpos->load($printer);
                                // $this->escpos->print_order($store, $sale, $items, $created_by);
                                $sendvalue['status'] = true; 
                            }
                            else{
                                $sendvalue['message'] = "Printer not defined"; 
                            }
                        }
                        else{
                            $sendvalue['message'] = "Invalid Sesssion"; 
                        }
                    }
                    else{
                        $sendvalue['message'] = "Please select product"; 
                    }
                }
            }    
        }
        echo json_encode($sendvalue);
    }
    public function open_drawer(){
        $sendvalue['status'] = false; 
        $sendvalue['message'] = ""; 
        $uq = $this->db->select('id,first_name,last_name')->from('users')->where('username',$_SESSION['username'])->get();
        if($uq->num_rows() > 0){
            $user = $uq->result()[0];
            $printer = $this->site->getPrinterByUser( $user->id);
            if($printer){
                $this->load->library('escpos');
                $this->escpos->load($printer);
                $this->escpos->open_drawer();        
                $sendvalue['status'] = true; 
            }
            else{
                $sendvalue['message'] = "Printer not defined"; 
            }
        }
        else{
            $sendvalue['message'] = "Invalid Sesssion"; 
        }
        echo json_encode($sendvalue);
    }
    public function submit(){
        $sendvalue['status'] = false; 
        $sendvalue['message'] = ""; 
        
        $uq = $this->db->select('id,first_name,last_name')->from('users')->where('username',$_SESSION['username'])->get();
        if($uq->num_rows() > 0){
            $user = $uq->result()[0];
            $customer = $this->input->post('customer');
            if($customer == ""){
                $sendvalue['message'] = "Please select customer"; 
            }
            else{
                $cq = $this->db->select('id,name,phone,email,address')->from('companies')->where('id',$customer)->get();
                if($cq->num_rows() == 0){
                    $sendvalue['message'] = "Invalid Customer"; 
                }
                else{
                    $customerdata = $cq->result()[0];
                    $payableamount = $this->input->post('payamountval');
    
    
    
                    
                    $insert['supplier_id'] = 0;
                    $insert['date'] = date('Y-m-d H:i:s');
                    $insert['reference_no'] = date('Y').''.date('m').''.date('d').''.date('H').''.date('i').''.date('s').''.$customerdata->id.'4'.rand(100,999);
                    $insert['customer_id'] = $customerdata->id;
                    $insert['customer'] = $customerdata->name;
                    $insert['own_company'] = 5;
                    $insert['biller_id'] = 48;
                    $insert['biller'] = 'Orah';
                    $insert['warehouse_id'] = 4;
                    $insert['order_discount'] = $this->input->post('discount_val');
                    $insert['sale_type'] = $this->input->post('saletype');
                    $insert['shipping'] = $this->input->post('charges');
                    $insert['sale_status'] = 'completed';
                    $insert['payment_status'] = 'pending';
                    $insert['created_by'] = $user->id;
    
                    $insert['payableamount'] = $payableamount;
                    $insert['total'] = 0;
                    $insert['grand_total'] = 0;
                    $insert['total_items'] = 0;
                    $insertitems = array();
                    // $products = $this->input->post('product_id');
                    $products = $this->input->post('pi_id');
                    $qty = $this->input->post('qty');
                    $totalpdiscount = $this->input->post('totalpdiscount');
                    if($products == "" || count($products) == 0){
                        $sendvalue['message'] = "Please select product"; 
                        
                    }
                    else{
                        foreach($products as $key => $row){
                            $this->db->select('
                                pi.*,
                                p.name,
                                p.code,
                                p.company_code
                            ');
                            $this->db->from('purchase_items as pi');
                            $this->db->join('products as p','p.id = pi.product_id','left');
                            $this->db->where('pi.id',$row);
                            $this->db->where('pi.quantity_balance > 0');
                            $pq =  $this->db->get();
                            $purchases = $pq->result();
                            $remaningqty = $qty[$key];
                            foreach($purchases as $purchase){
                                $saleqty = $qty[$key];
                                if($purchase->quantity_balance >= $remaningqty){
                                    $saleqty = $remaningqty;
                                    $remaningqty = 0;
                                }
                                else{
                                    $saleqty = $purchase->quantity_balance;
                                    $remaningqty = $remaningqty-$purchase->quantity_balance;
                                }

                                $itemdiscount = $totalpdiscount[$key];
                                if($itemdiscount == "undefined"){
                                    $itemdiscount = 0;
                                }
                                $temp['purcahse_item_id'] = $purchase->id;
                                $temp['sale_id'] = 0;
                                $temp['product_id'] = $purchase->product_id;
                                $temp['product_code'] = $purchase->code;
                                $temp['company_code'] = $purchase->company_code;
                                $temp['product_name'] = $purchase->name;

                                $temp['net_unit_price'] = $purchase->mrp;
                                $temp['unit_price'] = $purchase->mrp;
                                $temp['consignment'] = $purchase->price;
                                $temp['dropship'] = $purchase->dropship;
                                $temp['crossdock'] = $purchase->crossdock;
                                $temp['mrp'] = $purchase->mrp;
                                $temp['expiry'] = $purchase->expiry;
                                $temp['batch'] = $purchase->batch;
                                $temp['quantity'] = $saleqty;
                                $temp['warehouse_id'] = 4;
                                $temp['tax_rate_id'] = 0;
                                $temp['item_tax'] = 0;
                                $temp['tax'] = 0;
                                $temp['discount'] = $itemdiscount;
                                $temp['item_discount'] = $itemdiscount;
                                $temp['subtotal'] = ($saleqty*$purchase->mrp)-$itemdiscount;
                                $temp['real_unit_price'] = $purchase->price;
                                $temp['unit_quantity'] = $saleqty;
                                $temp['discount_one'] = 0;
                                $temp['discount_two'] = 0;
                                $temp['discount_three'] = 0;
                                $temp['product_price'] = $purchase->mrp;
                                $temp['further_tax'] = 0;
                                $temp['fed_tax'] = 0;
                                $temp['adv_tax'] = 0;
                                $insertitems[] = $temp;
                                $insert['total'] = $insert['total'] + $temp['subtotal'];
                                if($remaningqty == 0){
                                    break;
                                }
                            }
                        }
                        $insert['grand_total'] = $insert['total']+$insert['shipping']-$insert['order_discount'];
                        if(count($insertitems) > 0){
                            $this->db->insert('sales',$insert);
                            $insert_id = $this->db->insert_id();

                            $owncompany = $this->db->select('*')->from('own_companies')->where('id',5)->get()->row();
                            $store['name'] = $owncompany->companyname;
                            $store['address1'] = $owncompany->registeraddress;
                            $store['receipt_header'] = $owncompany->slip_header;
                            $store['receipt_footer'] = $owncompany->slip_footer;
                            $store['city'] = '';
                            $store['phone'] = $owncompany->mobile;
                            $store['ntn'] = $owncompany->ntn;
                            $store['strn'] = $owncompany->strn;

                            $items = array();
                            foreach($insertitems as $insertitem){
                                $insertitem['sale_id '] = $insert_id;
                                $this->db->insert('sale_items',$insertitem);
                                $temp['product_name'] = $insertitem['product_name'];
                                $temp['quantity'] = $insertitem['quantity'];
                                $items[] = $temp;

                                //Batch Quantity Update in Purchase Table
                                $this->db->set('quantity_balance', 'quantity_balance-'.$insertitem['quantity'], FALSE);
                                $this->db->where('id', $insertitem['purcahse_item_id']);
                                $this->db->update('purchase_items');
                                
                                //Warehouse Quantity Update in Warehouse Product Table
                                $this->db->set('quantity', 'quantity-'.$insertitem['quantity'], FALSE);
                                $this->db->where('product_id', $insertitem['product_id']);
                                $this->db->where('warehouse_id', $insertitem['warehouse_id']);
                                $this->db->update('warehouses_products');
                                
                                //Product Quantity Update in Product Table
                                $this->db->set('quantity', 'quantity-'.$insertitem['quantity'], FALSE);
                                $this->db->where('id', $insertitem['product_id']);
                                $this->db->update('products');

                            }

                            $paymentamount = $payableamount;
                            if($insert['grand_total'] < $payableamount){
                                $paymentamount = $insert['grand_total'];
                            }
                            $this->load->admin_model('sales_model');
                            $payments=array();
                            if($paymentamount > 0){
                                $payment = array(
                                    'date' => date('Y-m-d H:i:s'),
                                    'sale_id' => $insert_id,
                                    'sale_return_id' => 0,
                                    'reference_no' => $this->site->getReference('pay'),
                                    'amount' => $paymentamount,
                                    'hold_amount' => 0,
                                    'paid_by' => $this->input->post('paymethodval'),
                                    'cheque_no' => '',
                                    'cc_no' => '',
                                    'cc_holder' => '',
                                    'cc_month' => '',
                                    'cc_year' => '',
                                    'cc_type' => '',
                                    'note' => $this->input->post('payment_note_val'),
                                    'created_by' => $user->id,
                                    'type' => 'received',
                                    'status' => '',
                                    'cpr_no' => '',
                                    'credit_no_per' => ''
                                );
                                $this->sales_model->addPayment($payment, $insert['customer_id']);
                                $payments[] = $payment;
                            }

            
                            $created_by = $user->first_name.' '.$user->last_name;
                            $printer = $this->site->getPrinterByUser($user->id);
                            if($printer){
                                // $this->escpos->print_order($store, $sale, $items, $created_by);
                                $sale = $this->db->select('*')->from('sales')->where('id',$insert_id)->get()->row();
                                $sitems = $this->db->select('*')->from('sale_items')->where('sale_id',$insert_id)->get()->result();

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
                                // $a = urlencode($a);
                                
                                $sendvalue['url'] = "http://localhost/rhoprinter/printers/print_receipt?data=".$a;
                                $a2 = array(
                                    'printer' => $printer
                                );
                                $a2 = json_encode($a2);
                                $a2 = urlencode($a2);
                                $sendvalue['url2'] = "http://localhost/rhoprinter/printers/print_receipt?data=".$a2;
                                $sendvalue['form_data'] = $a;


                                $sendvalue['print'] = true;

                                // $this->load->library('escpos');
                                // $this->escpos->load($printer);
                                // $this->escpos->print_receipt($store, $sale, $sitems, $payments, $created_by,true);



                            }
                            else{
                                $sendvalue['print'] = false;
                            }
                            $hold_id = $this->input->post('hold_id');
                            if($hold_id != ""){
                                $this->db->delete('suspended_items', array('suspend_id' => $hold_id));
                                $this->db->delete('suspended_bills', array('id' => $hold_id));
                            }

                            $sendvalue['message'] = "Sale create successfully"; 
                            $sendvalue['status'] = true; 
                        }
                        else{
                            $sendvalue['message'] = "Please select product"; 
                        }
                    }
                }    
            }
        }
        else{
            $sendvalue['message'] = "Session Expired"; 
        }
        echo json_encode($sendvalue);
    }
    public function remove_hold_bill(){
        $sendvalue['status'] = false; 
        $sendvalue['message'] = ""; 
        $hold_id = $this->input->get('hid');
        if($hold_id != ""){
            $this->db->delete('suspended_items', array('suspend_id' => $hold_id));
            $this->db->delete('suspended_bills', array('id' => $hold_id));
            $sendvalue['message'] = "Hold bill removed"; 
            $sendvalue['status'] = true;
        }
        else{
            $sendvalue['message'] = "Invalid Hold Bill"; 
        }
        echo json_encode($sendvalue);
    }
    public function register_details()
    {
        $user_id = $this->session->userdata('user_id');
        $user_register                    = $user_id ? $this->pos_model->registerData($user_id) : null;
        // $register_open_time               = $this->session->userdata('register_open_time');
        $register_open_time               = $user_register ? $user_register->date : null;
        $this->data['cash_in_hand']       = $user_register ? $user_register->cash_in_hand : null;
        $this->data['register_open_time'] = $user_register ? $register_open_time : null;
        $this->data['ccsales']        = $this->pos_model->getRegisterCCSales($register_open_time);
        $this->data['cashsales']      = $this->pos_model->getRegisterCashSales($register_open_time);
        $this->data['chsales']        = $this->pos_model->getRegisterChSales($register_open_time);
        $this->data['gcsales']        = $this->pos_model->getRegisterGCSales($register_open_time);
        $this->data['pppsales']       = $this->pos_model->getRegisterPPPSales($register_open_time);
        $this->data['stripesales']    = $this->pos_model->getRegisterStripeSales($register_open_time);
        $this->data['othersales']     = $this->pos_model->getRegisterOtherSales($register_open_time);
        $this->data['authorizesales'] = $this->pos_model->getRegisterAuthorizeSales($register_open_time);
        $this->data['totalsales']     = $this->pos_model->getRegisterSales($register_open_time);
        $this->data['refunds']        = $this->pos_model->getRegisterRefunds($register_open_time);
        $this->data['returns']        = $this->pos_model->getRegisterReturns($register_open_time);
        $this->data['expenses']       = $this->pos_model->getRegisterExpenses($register_open_time);
        $this->load->view('v1/admin/views/pos/register_detail', $this->data);
    }
    public function registerclose_detail()
    {
        $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $user_id = $this->session->userdata('user_id');

        $user_register                    = $user_id ? $this->pos_model->registerData($user_id) : null;
        // $register_open_time               = $this->session->userdata('register_open_time');
        $register_open_time               = $user_register ? $user_register->date : null;
        $this->data['cash_in_hand']       = $user_register ? $user_register->cash_in_hand : null;
        $this->data['register_open_time'] = $user_register ? $register_open_time : null;
        $this->data['ccsales']         = $this->pos_model->getRegisterCCSales($register_open_time, $user_id);
        $this->data['cashsales']       = $this->pos_model->getRegisterCashSales($register_open_time, $user_id);
        $this->data['chsales']         = $this->pos_model->getRegisterChSales($register_open_time, $user_id);
        $this->data['gcsales']         = $this->pos_model->getRegisterGCSales($register_open_time);
        $this->data['pppsales']        = $this->pos_model->getRegisterPPPSales($register_open_time, $user_id);
        $this->data['stripesales']     = $this->pos_model->getRegisterStripeSales($register_open_time, $user_id);
        $this->data['othersales']      = $this->pos_model->getRegisterOtherSales($register_open_time);
        $this->data['authorizesales']  = $this->pos_model->getRegisterAuthorizeSales($register_open_time, $user_id);
        $this->data['totalsales']      = $this->pos_model->getRegisterSales($register_open_time, $user_id);
        $this->data['refunds']         = $this->pos_model->getRegisterRefunds($register_open_time, $user_id);
        $this->data['returns']         = $this->pos_model->getRegisterReturns($register_open_time, $user_id);
        $this->data['cashrefunds']     = $this->pos_model->getRegisterCashRefunds($register_open_time, $user_id);
        $this->data['expenses']        = $this->pos_model->getRegisterExpenses($register_open_time, $user_id);
        $this->data['users']           = $this->pos_model->getUsers($user_id);
        $this->data['suspended_bills'] = $this->pos_model->getSuspendedsales($user_id);
        $this->data['user_id']         = $user_id;
        $this->data['modal_js']        = $this->site->modal_js();
        $this->load->view('v1/admin/views/pos/close_register', $this->data);
    }
    public function close_register_submit($user_id = null)
    {
        $user_id = $this->session->userdata('user_id');
        $this->form_validation->set_rules('total_cash', lang('total_cash'), 'trim|required|numeric');
        $this->form_validation->set_rules('total_cheques', lang('total_cheques'), 'trim|numeric');
        $this->form_validation->set_rules('total_cc_slips', lang('total_cc_slips'), 'trim|numeric');

        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $user_register = $user_id ? $this->pos_model->registerData($user_id) : null;
                $rid           = $user_register ? $user_register->id : $this->session->userdata('register_id');
                $user_id       = $user_register ? $user_register->user_id : $this->session->userdata('user_id');
            } else {
                $rid     = $this->session->userdata('register_id');
                $user_id = $this->session->userdata('user_id');
            }
            $openregister_q = $this->db->select('id')->from('sma_suspended_bills')->get();
            if($openregister_q->num_rows() > 0){
                echo '
                    <script>
                        alert("Firstly remove all open bills")
                        location.href = "'.base_url('admin/pos').'";
                    </script>
                ';
                exit();
            }
            else{

            }
            $data = [
                'closed_at'                => date('Y-m-d H:i:s'),
                'total_cash'               => $this->input->post('total_cash'),
                'total_cheques'            => $this->input->post('total_cheques'),
                'total_cc_slips'           => $this->input->post('total_cc_slips'),
                'total_cash_submitted'     => $this->input->post('total_cash_submitted'),
                'total_cheques_submitted'  => $this->input->post('total_cheques_submitted'),
                'total_cc_slips_submitted' => $this->input->post('total_cc_slips_submitted'),
                'total_available_cash_submitted' => $this->input->post('total_available_cash_submitted'),
                'note'                     => $this->input->post('note'),
                'status'                   => 'close',
                'transfer_opened_bills'    => $this->input->post('transfer_opened_bills'),
                'closed_by'                => $this->session->userdata('user_id'),
            ];
        } elseif ($this->input->post('close_register')) {
            $this->session->set_flashdata('error', (validation_errors() ? validation_errors() : $this->session->flashdata('error')));
            echo 3;
            admin_redirect('pos');
        }

        if ($this->form_validation->run() == true && $this->pos_model->closeRegister($rid, $user_id, $data)) {
            $this->session->set_flashdata('message', lang('register_closed'));
            echo 1;
            // admin_redirect('welcome');
            admin_redirect('pos');
        } else {
            echo 2;
            admin_redirect('pos');
        }
    }




    // Old Code
    public function add_payment($id = null)
    {
        $this->sma->checkPermissions('payments', true, 'sales');
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            $sale = $this->pos_model->getInvoiceByID($this->input->post('sale_id'));
            if ($this->input->post('paid_by') == 'deposit') {
                $customer_id = $sale->customer_id;
                if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                    $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $customer_id = null;
            }
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'sale_id'      => $this->input->post('sale_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'cc_cvv2'      => $this->input->post('pcc_ccv'),
                'note'         => $this->input->post('note'),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => $sale->sale_status == 'returned' ? 'returned' : 'received',
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $msg = $this->pos_model->addPayment($payment, $customer_id)) {
            if ($msg) {
                if ($msg['status'] == 0) {
                    unset($msg['status']);
                    $error = '';
                    foreach ($msg as $m) {
                        if (is_array($m)) {
                            foreach ($m as $e) {
                                $error .= '<br>' . $e;
                            }
                        } else {
                            $error .= '<br>' . $m;
                        }
                    }
                    $this->session->set_flashdata('error', '<pre>' . $error . '</pre>');
                } else {
                    $this->session->set_flashdata('message', lang('payment_added'));
                }
            } else {
                $this->session->set_flashdata('error', lang('payment_failed'));
            }
            admin_redirect('pos/sales');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $sale                      = $this->pos_model->getInvoiceByID($id);
            $this->data['inv']         = $sale;
            $this->data['payment_ref'] = $this->site->getReference('pay');
            $this->data['modal_js']    = $this->site->modal_js();

            $this->load->view($this->theme . 'pos/add_payment', $this->data);
        }
    }

    public function add_printer()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('pos');
        }

        $this->form_validation->set_rules('title', $this->lang->line('title'), 'required');
        $this->form_validation->set_rules('type', $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line('profile'), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line('char_per_line'), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line('ip_address'), 'required|is_unique[printers.ip_address]');
            $this->form_validation->set_rules('port', $this->lang->line('port'), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line('path'), 'required|is_unique[printers.path]');
        }

        if ($this->form_validation->run() == true) {
            $data = ['title'    => $this->input->post('title'),
                'type'          => $this->input->post('type'),
                'profile'       => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path'          => $this->input->post('path'),
                'ip_address'    => $this->input->post('ip_address'),
                'port'          => ($this->input->post('type') == 'network') ? $this->input->post('port') : null,
            ];
        }

        if ($this->form_validation->run() == true && $cid = $this->pos_model->addPrinter($data)) {
            $this->session->set_flashdata('message', $this->lang->line('printer_added'));
            admin_redirect('pos/printers');
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'failed', 'msg' => validation_errors()]);
                die();
            }

            $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('add_printer');
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => admin_url('pos/printers'), 'page' => lang('printers')], ['link' => '#', 'page' => lang('add_printer')]];
            $meta                     = ['page_title' => lang('add_printer'), 'bc' => $bc];
            $this->page_construct('pos/add_printer', $meta, $this->data);
        }
    }

    public function barcode($text = null, $bcs = 'code128', $height = 50)
    {
        return admin_url('products/gen_barcode/' . $text . '/' . $bcs . '/' . $height);
    }

    public function check_pin()
    {
        $pin = $this->input->post('pw', true);
        if ($pin == $this->pos_pin) {
            $this->sma->send_json(['res' => 1]);
        }
        $this->sma->send_json(['res' => 0]);
    }
    public function delete($id = null)
    {
        $this->sma->checkPermissions('index');
        if (!$id) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);
        }
        if ($this->pos_model->deleteBill($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('suspended_sale_deleted')]);
        }
    }

    public function delete_printer($id = null)
    {
        if (DEMO) {
            $this->session->set_flashdata('error', $this->lang->line('disabled_in_demo'));
            $this->sma->md();
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id', true);
        }
        if (!$id) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);
        }

        if ($this->pos_model->deletePrinter($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('printer_deleted')]);
        }
    }

    public function edit_printer($id = null)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id', true);
        }

        $printer = $this->pos_model->getPrinterByID($id);
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'required');
        $this->form_validation->set_rules('type', $this->lang->line('type'), 'required');
        $this->form_validation->set_rules('profile', $this->lang->line('profile'), 'required');
        $this->form_validation->set_rules('char_per_line', $this->lang->line('char_per_line'), 'required');
        if ($this->input->post('type') == 'network') {
            $this->form_validation->set_rules('ip_address', $this->lang->line('ip_address'), 'required');
            if ($this->input->post('ip_address') != $printer->ip_address) {
                $this->form_validation->set_rules('ip_address', $this->lang->line('ip_address'), 'is_unique[printers.ip_address]');
            }
            $this->form_validation->set_rules('port', $this->lang->line('port'), 'required');
        } else {
            $this->form_validation->set_rules('path', $this->lang->line('path'), 'required');
            if ($this->input->post('path') != $printer->path) {
                $this->form_validation->set_rules('path', $this->lang->line('path'), 'is_unique[printers.path]');
            }
        }

        if ($this->form_validation->run() == true) {
            $data = ['title'    => $this->input->post('title'),
                'type'          => $this->input->post('type'),
                'profile'       => $this->input->post('profile'),
                'char_per_line' => $this->input->post('char_per_line'),
                'path'          => $this->input->post('path'),
                'ip_address'    => $this->input->post('ip_address'),
                'port'          => ($this->input->post('type') == 'network') ? $this->input->post('port') : null,
            ];
        }

        if ($this->form_validation->run() == true && $this->pos_model->updatePrinter($id, $data)) {
            $this->session->set_flashdata('message', $this->lang->line('printer_updated'));
            admin_redirect('pos/printers');
        } else {
            $this->data['printer']    = $printer;
            $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['page_title'] = lang('edit_printer');
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => admin_url('pos/printers'), 'page' => lang('printers')], ['link' => '#', 'page' => lang('edit_printer')]];
            $meta                     = ['page_title' => lang('edit_printer'), 'bc' => $bc];
            $this->page_construct('pos/edit_printer', $meta, $this->data);
        }
    }

    public function email_receipt($sale_id = null, $view = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->post('id')) {
            $sale_id = $this->input->post('id');
        }
        if (!$sale_id) {
            die('No sale selected.');
        }
        if ($this->input->post('email')) {
            $to = $this->input->post('email');
        }
        $this->data['error']   = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');

        $this->data['rows']            = $this->pos_model->getAllInvoiceItems($sale_id);
        $inv                           = $this->pos_model->getInvoiceByID($sale_id);
        $biller_id                     = $inv->biller_id;
        $customer_id                   = $inv->customer_id;
        $this->data['biller']          = $this->pos_model->getCompanyByID($biller_id);
        $this->data['customer']        = $this->pos_model->getCompanyByID($customer_id);
        $this->data['payments']        = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['pos']             = $this->pos_model->getSetting();
        $this->data['barcode']         = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['return_sale']     = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
        $this->data['inv']             = $inv;
        $this->data['sid']             = $sale_id;
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['page_title']      = $this->lang->line('invoice');

        $receipt = $this->load->view($this->theme . 'pos/email_receipt', $this->data, true);
        if ($view) {
            echo $receipt;
            die();
        }

        if (!$to) {
            $to = $this->data['customer']->email;
        }
        if (!$to) {
            $this->sma->send_json(['msg' => $this->lang->line('no_meil_provided')]);
        }

        try {
            if ($this->sma->send_email($to, lang('receipt_from') . ' ' . $this->data['biller']->company, $receipt)) {
                $this->sma->send_json(['msg' => $this->lang->line('email_sent')]);
            } else {
                $this->sma->send_json(['msg' => $this->lang->line('email_failed')]);
            }
        } catch (Exception $e) {
            $this->sma->send_json(['msg' => $e->getMessage()]);
        }
    }

    public function get_printers()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->load->library('datatables');
        $this->datatables
        ->select('id, title, type, profile, path, ip_address, port')
        ->from('printers')
        ->add_column('Actions', "<div class='text-center'> <a href='" . admin_url('pos/edit_printer/$1') . "' class='btn-warning btn-xs tip' title='" . lang('edit_printer') . "'><i class='fa fa-edit'></i></a> <a href='#' class='btn-danger btn-xs tip po' title='<b>" . lang('delete_printer') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('pos/delete_printer/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id')
        ->unset_column('id');
        echo $this->datatables->generate();
    }

    public function getProductDataByCode($code = null, $warehouse_id = null, $customer_id= null)
    {
       
        $this->sma->checkPermissions('index');
        if ($this->input->get('code')) {
            $code = $this->input->get('code', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        if ($this->input->get('customer_id')) {
            $customer_id = $this->input->get('customer_id', true);
        }
        if (!$code) {
            echo null;
            die();
        }
        $batch_method=$this->session->userdata('batch_method');
        
        $warehouse      = $this->site->getWarehouseByID($warehouse_id);
        $customer       = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        $row            = $this->pos_model->getWHProduct($code, $warehouse_id, $batch_method);
        $option         = false;
        // echo"<pre/>";
        // print_r($batch_method); die;
        if ($row) {
            unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
            $row->item_tax_method = $row->tax_method;
            $row->qty             = 1;
            $row->discount        = '0';
            $row->serial          = '';
            $options              = $this->pos_model->getProductOptions($row->id, $warehouse_id);
            if ($options) {
                $opt = current($options);
                if (!$option) {
                    $option = $opt->id;
                }
            } else {
                $opt        = json_decode('{}');
                $opt->price = 0;
            }
            $row->option   = $option;
            $row->quantity = 0;
            $pis           = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
            if ($pis) {
                foreach ($pis as $pi) {
                    $row->quantity += $pi->quantity_balance;
                }
            }
            if ($row->type == 'standard' && (!$this->Settings->overselling && $row->quantity < 1)) {
                echo null;
                die();
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
            if ($customer_group) {
                if ($customer_group->discount && $customer_group->percent < 0) {
                    $row->discount = (0 - $customer_group->percent) . '%';
                } else {
                    $row->price = $row->price + (($row->price * $customer_group->percent) / 100);
                }
            }
            $row->real_unit_price = $row->price;
            $row->base_quantity   = 1;
            $row->base_unit       = $row->unit;
            $row->base_unit_price = $row->price;
            $row->batch;
            $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
            $row->batches;
            $row->comment         = '';
            $combo_items          = false;
            if ($row->type == 'combo') {
                $combo_items = $this->pos_model->getProductComboItems($row->id, $warehouse_id);
            }
            $units    = $this->site->getUnitsByBUID($row->base_unit);
            $tax_rate = $this->pos_model->getOrderTaxRateByID($row->tax_rate);

            $pr = ['id' => sha1(uniqid(mt_rand(), true)), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'batch_method' => $batch_method, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];

            $this->sma->send_json($pr);

        } else {
            echo null;
        }
    }

    public function getProductPromo($pId = null, $warehouse_id = null)
    {
        $this->sma->checkPermissions('index');
        if ($this->input->get('product_id')) {
            $pId = $this->input->get('product_id', true);
        }
        if ($this->input->get('warehouse_id')) {
            $warehouse_id = $this->input->get('warehouse_id', true);
        }
        $this->load->admin_model('promos_model');
        $promos = $this->promos_model->getPromosByProduct($pId);

        if ($promos) {
            foreach ($promos as $promo) {
                $warehouse = $this->site->getWarehouseByID($warehouse_id);
                $row       = $this->pos_model->getWHProductById($promo->product2get, $warehouse_id);
                
                $option    = false;
                if ($row) {
                    unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    $row->item_tax_method = $row->tax_method;
                    $row->qty             = 1;
                    $row->price           = 0;
                    $row->discount        = '0';
                    $row->serial          = '';
                    $options              = $this->pos_model->getProductOptions($row->id, $warehouse_id);
                    if ($options) {
                        $opt = current($options);
                        if (!$option) {
                            $option = $opt->id;
                        }
                    }
                    $row->option          = $option;
                    $row->real_unit_price = $row->price;
                    $row->base_quantity   = 1;
                    $row->base_unit       = $row->unit;
                    $row->base_unit_price = $row->price;
                    $row->batch;
                    $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
                    $row->comment         = '';
                    $combo_items          = false;
                    // if ($row->type == 'combo') {
                    //     $combo_items = $this->pos_model->getProductComboItems($row->id, $warehouse_id);
                    // }
                    $units    = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = false; // $this->site->getTaxRateByID($row->tax_rate);

                    $pr = ['id' => sha1(uniqid(mt_rand(), true)), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id, 'row' => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options];

                    $this->sma->send_json($pr);
                } else {
                    echo null;
                }
            }
        } else {
            echo null;
        }
    }

    public function getSales($warehouse_id = null)
    {
        $this->sma->checkPermissions('index');

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $duplicate_link    = anchor('admin/pos/?duplicate=$1', '<i class="fa fa-plus-square"></i> ' . lang('duplicate_sale'), 'class="duplicate_pos"');
        $detail_link       = anchor('admin/pos/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('view_receipt'));
        $detail_link2      = anchor('admin/sales/modal_view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details_modal'), 'data-toggle="modal" data-target="#myModal"');
        $detail_link3      = anchor('admin/sales/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('sale_details'));
        $payments_link     = anchor('admin/sales/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link  = anchor('admin/pos/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');
        $packagink_link    = anchor('admin/sales/packaging/$1', '<i class="fa fa-archive"></i> ' . lang('packaging'), 'data-toggle="modal" data-target="#myModal"');
        $add_delivery_link = anchor('admin/sales/add_delivery/$1', '<i class="fa fa-truck"></i> ' . lang('add_delivery'), 'data-toggle="modal" data-target="#myModal"');
        $email_link        = anchor('admin/#', '<i class="fa fa-envelope"></i> ' . lang('email_sale'), 'class="email_receipt" data-id="$1" data-email-address="$2"');
        $edit_link         = anchor('admin/sales/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit_sale'), 'class="sledit"');
        $return_link       = anchor('admin/sales/return_sale/$1', '<i class="fa fa-angle-double-left"></i> ' . lang('return_sale'));
        $delete_link       = "<a href='#' class='po' title='<b>" . lang('delete_sale') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('sales/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_sale') . '</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
            <ul class="dropdown-menu pull-right" role="menu">
                <li>' . $duplicate_link . '</li>
                <li>' . $detail_link . '</li>
                <li>' . $detail_link2 . '</li>
                <li>' . $detail_link3 . '</li>
                <li>' . $payments_link . '</li>
                <li>' . $add_payment_link . '</li>
                <li>' . $packagink_link . '</li>
                <li>' . $add_delivery_link . '</li>
                <li>' . $edit_link . '</li>
                <li>' . $email_link . '</li>
                <li>' . $return_link . '</li>
                <li>' . $delete_link . '</li>
            </ul>
        </div></div>';

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables
                ->select($this->db->dbprefix('sales') . ".id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, (grand_total+COALESCE(rounding, 0)), paid, CONCAT(grand_total, '__', rounding, '__', paid) as balance, sale_status, payment_status, companies.email as cemail")
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left')
                ->where('warehouse_id', $warehouse_id)
                ->group_by('sales.id');
        } else {
            $this->datatables
                ->select($this->db->dbprefix('sales') . ".id as id, DATE_FORMAT(date, '%Y-%m-%d %T') as date, reference_no, biller, customer, (grand_total+COALESCE(rounding, 0)), paid, CONCAT(grand_total, '__', rounding, '__', paid) as balance, sale_status, payment_status, companies.email as cemail")
                ->from('sales')
                ->join('companies', 'companies.id=sales.customer_id', 'left')
                ->group_by('sales.id');
        }
        $this->datatables->where('pos', 1);
        if (!$this->Customer && !$this->Supplier && !$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        } elseif ($this->Customer) {
            $this->datatables->where('customer_id', $this->session->userdata('user_id'));
        }
        $this->datatables->add_column('Actions', $action, 'id, cemail')->unset_column('cemail');
        echo $this->datatables->generate();
    }
    public function hold_bill_list(){
      $sendvalue['html'] = "";
      $sendvalue['count'] = 0;
      $hold = $this->input->get('holdno');

      $this->db->select('id,reference_no,customer,date,sale_type');
      $this->db->from('suspended_bills');
      if($hold != ""){
        $this->db->like('reference_no',$hold);
      }
      $this->db->order_by('id', 'DESC');
      $this->db->limit(20);
      $holds = $this->db->get()->result();
      $sendvalue['count'] = count($holds);
      foreach($holds as $hold){
        $hold_type = "POS Sale";
        if($hold->sale_type == 2){
            $hold_type = "Website Sale";
        }
        else if($hold->sale_type == 3){
            $hold_type = "Call Sale";
        }
        else if($hold->sale_type == 4){
            $hold_type = "Email Sale";
        }
        $sendvalue['html'] .= '
            <li>
                <div class="md-list-content">
                    <span class="md-list-heading"><a href="'.base_url("admin/pos?hold=".$hold->id).'">'.$hold->reference_no.'</a></span>
                    <span>'.$hold->customer.' ('.$hold->date.')</span>
                    <span style="color:red" >'.$hold_type.'</span>
                </div>
            </li>
        ';
      }
      echo json_encode($sendvalue);
    }
    public function open_register()
    {
        $this->sma->checkPermissions('index');
        $this->form_validation->set_rules('cash_in_hand', lang('cash_in_hand'), 'trim|required|numeric');
        if ($register = $this->pos_model->registerData($this->session->userdata('user_id'))) {
            $register_data = ['register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date];
            $this->session->set_userdata($register_data);
            admin_redirect('pos');
        }

        if ($this->form_validation->run() == true) {
            $data = [
                'date'         => date('Y-m-d H:i:s'),
                'cash_in_hand' => $this->input->post('cash_in_hand'),
                'user_id'      => $this->session->userdata('user_id'),
                'status'       => 'open',
            ];
        }
        if ($this->form_validation->run() == true && $this->pos_model->openRegister($data)) {
            $this->session->set_flashdata('message', lang('welcome_to_pos'));
            admin_redirect('pos');
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('open_register')]];
            $meta                = ['page_title' => lang('open_register'), 'bc' => $bc];
            $this->page_construct('pos/open_register', $meta, $this->data);
        }
    }

    public function opened_bills($per_page = 0)
    {
        $this->load->library('pagination');

        //$this->table->set_heading('Id', 'The Title', 'The Content');
        if ($this->input->get('per_page')) {
            $per_page = $this->input->get('per_page');
        }

        $config['base_url']   = admin_url('pos/opened_bills');
        $config['total_rows'] = $this->pos_model->bills_count();
        $config['per_page']   = 6;
        $config['num_links']  = 3;

        $config['full_tag_open']   = '<ul class="pagination pagination-sm">';
        $config['full_tag_close']  = '</ul>';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';
        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';
        $config['cur_tag_open']    = '<li class="active"><a>';
        $config['cur_tag_close']   = '</a></li>';

        $this->pagination->initialize($config);
        $data['r'] = true;
        $bills     = $this->pos_model->fetch_bills($config['per_page'], $per_page);
        if (!empty($bills)) {
            $html = '';
            $html .= '<ul class="ob">';
            foreach ($bills as $bill) {
                $html .= '<li><button type="button" class="btn btn-info sus_sale" id="' . $bill->id . '"><p>' . $bill->suspend_note . '</p><strong>' . $bill->customer . '</strong><br>' . lang('date') . ': ' . $bill->date . '<br>' . lang('items') . ': ' . $bill->count . '<br>' . lang('total') . ': ' . $this->sma->formatMoney($bill->total) . '</button></li>';
            }
            $html .= '</ul>';
        } else {
            $html      = '<h3>' . lang('no_opeded_bill') . '</h3><p>&nbsp;</p>';
            $data['r'] = false;
        }

        $data['html'] = $html;

        $data['page'] = $this->pagination->create_links();
        echo $this->load->view($this->theme . 'pos/opened', $data, true);
    }

    public function printers()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('pos');
        }
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('printers');
        $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => '#', 'page' => lang('printers')]];
        $meta                     = ['page_title' => lang('list_printers'), 'bc' => $bc];
        $this->page_construct('pos/printers', $meta, $this->data);
    }


    public function registers()
    {
        $this->sma->checkPermissions();

        $this->data['error']     = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['registers'] = $this->pos_model->getOpenRegisters();
        $bc                      = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => '#', 'page' => lang('open_registers')]];
        $meta                    = ['page_title' => lang('open_registers'), 'bc' => $bc];
        $this->page_construct('pos/registers', $meta, $this->data);
    }

    public function sales($warehouse_id = null)
    {
        $this->sma->checkPermissions('index');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $user                       = $this->site->getUser();
            $this->data['warehouses']   = null;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse']    = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('pos'), 'page' => lang('pos')], ['link' => '#', 'page' => lang('pos_sales')]];
        $meta = ['page_title' => lang('pos_sales'), 'bc' => $bc];
        $this->page_construct('pos/sales', $meta, $this->data);
    }

    public function settings()
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->form_validation->set_message('is_natural_no_zero', $this->lang->line('no_zero_required'));
        $this->form_validation->set_rules('pro_limit', $this->lang->line('pro_limit'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('pin_code', $this->lang->line('delete_code'), 'numeric');
        $this->form_validation->set_rules('category', $this->lang->line('default_category'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('customer', $this->lang->line('default_customer'), 'required|is_natural_no_zero');
        $this->form_validation->set_rules('biller', $this->lang->line('default_biller'), 'required|is_natural_no_zero');

        if ($this->form_validation->run() == true) {
            $data = [
                'pro_limit'                 => $this->input->post('pro_limit'),
                'pin_code'                  => $this->input->post('pin_code') ? $this->input->post('pin_code') : null,
                'default_category'          => $this->input->post('category'),
                'default_customer'          => $this->input->post('customer'),
                'default_biller'            => $this->input->post('biller'),
                'display_time'              => $this->input->post('display_time'),
                'receipt_printer'           => $this->input->post('receipt_printer'),
                'cash_drawer_codes'         => $this->input->post('cash_drawer_codes'),
                'cf_title1'                 => $this->input->post('cf_title1'),
                'cf_title2'                 => $this->input->post('cf_title2'),
                'cf_value1'                 => $this->input->post('cf_value1'),
                'cf_value2'                 => $this->input->post('cf_value2'),
                'focus_add_item'            => $this->input->post('focus_add_item'),
                'add_manual_product'        => $this->input->post('add_manual_product'),
                'customer_selection'        => $this->input->post('customer_selection'),
                'add_customer'              => $this->input->post('add_customer'),
                'toggle_category_slider'    => $this->input->post('toggle_category_slider'),
                'toggle_subcategory_slider' => $this->input->post('toggle_subcategory_slider'),
                'toggle_brands_slider'      => $this->input->post('toggle_brands_slider'),
                'cancel_sale'               => $this->input->post('cancel_sale'),
                'suspend_sale'              => $this->input->post('suspend_sale'),
                'print_items_list'          => $this->input->post('print_items_list'),
                'finalize_sale'             => $this->input->post('finalize_sale'),
                'today_sale'                => $this->input->post('today_sale'),
                'open_hold_bills'           => $this->input->post('open_hold_bills'),
                'close_register'            => $this->input->post('close_register'),
                'tooltips'                  => $this->input->post('tooltips'),
                'keyboard'                  => $this->input->post('keyboard'),
                'pos_printers'              => $this->input->post('pos_printers'),
                'java_applet'               => $this->input->post('enable_java_applet'),
                'product_button_color'      => $this->input->post('product_button_color'),
                'paypal_pro'                => $this->input->post('paypal_pro'),
                'stripe'                    => $this->input->post('stripe'),
                'authorize'                 => $this->input->post('authorize'),
                'rounding'                  => $this->input->post('rounding'),
                'item_order'                => $this->input->post('item_order'),
                'after_sale_page'           => $this->input->post('after_sale_page'),
                'printer'                   => $this->input->post('receipt_printer'),
                'order_printers'            => json_encode($this->input->post('order_printers')),
                'auto_print'                => $this->input->post('auto_print'),
                'remote_printing'           => DEMO ? 1 : $this->input->post('remote_printing'),
                'customer_details'          => $this->input->post('customer_details'),
                'local_printers'            => $this->input->post('local_printers'),
            ];
            $payment_config = [
                'APIUsername'            => $this->input->post('APIUsername'),
                'APIPassword'            => $this->input->post('APIPassword'),
                'APISignature'           => $this->input->post('APISignature'),
                'stripe_secret_key'      => $this->input->post('stripe_secret_key'),
                'stripe_publishable_key' => $this->input->post('stripe_publishable_key'),
                'api_login_id'           => $this->input->post('api_login_id'),
                'api_transaction_key'    => $this->input->post('api_transaction_key'),
            ];
        } elseif ($this->input->post('update_settings')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('pos/settings');
        }

        if ($this->form_validation->run() == true && $this->pos_model->updateSetting($data)) {
            if (DEMO) {
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect('pos/settings');
            }
            if ($this->write_payments_config($payment_config)) {
                $this->session->set_flashdata('message', $this->lang->line('pos_setting_updated'));
                admin_redirect('pos/settings');
            } else {
                $this->session->set_flashdata('error', $this->lang->line('pos_setting_updated_payment_failed'));
                admin_redirect('pos/settings');
            }
        } else {
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['pos']        = $this->pos_model->getSetting();
            $this->data['categories'] = $this->site->getAllCategories();
            //$this->data['customer'] = $this->pos_model->getCompanyByID($this->pos_settings->default_customer);
            $this->data['billers'] = $this->pos_model->getAllBillerCompanies();
            $this->config->load('payment_gateways');
            $this->data['stripe_secret_key']      = $this->config->item('stripe_secret_key');
            $this->data['stripe_publishable_key'] = $this->config->item('stripe_publishable_key');
            $authorize                            = $this->config->item('authorize');
            $this->data['api_login_id']           = $authorize['api_login_id'];
            $this->data['api_transaction_key']    = $authorize['api_transaction_key'];
            $this->data['APIUsername']            = $this->config->item('APIUsername');
            $this->data['APIPassword']            = $this->config->item('APIPassword');
            $this->data['APISignature']           = $this->config->item('APISignature');
            $this->data['printers']               = $this->pos_model->getAllPrinters();
            $this->data['paypal_balance']         = null; // $this->pos_settings->paypal_pro ? $this->paypal_balance() : NULL;
            $this->data['stripe_balance']         = null; // $this->pos_settings->stripe ? $this->stripe_balance() : NULL;
            $bc                                   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('pos_settings')]];
            $meta                                 = ['page_title' => lang('pos_settings'), 'bc' => $bc];
            $this->page_construct('pos/settings', $meta, $this->data);
        }
    }

    public function stripe_balance()
    {
        if (!$this->Owner) {
            return false;
        }
        $this->load->admin_model('stripe_payments');

        return $this->stripe_payments->get_balance();
    }

    public function today_sale()
    {
        if (!$this->Owner && !$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['error']          = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['ccsales']        = $this->pos_model->getTodayCCSales();
        $this->data['cashsales']      = $this->pos_model->getTodayCashSales();
        $this->data['chsales']        = $this->pos_model->getTodayChSales();
        $this->data['pppsales']       = $this->pos_model->getTodayPPPSales();
        $this->data['stripesales']    = $this->pos_model->getTodayStripeSales();
        $this->data['authorizesales'] = $this->pos_model->getTodayAuthorizeSales();
        $this->data['totalsales']     = $this->pos_model->getTodaySales();
        $this->data['refunds']        = $this->pos_model->getTodayRefunds();
        $this->data['returns']        = $this->pos_model->getTodayReturns();
        $this->data['expenses']       = $this->pos_model->getTodayExpenses();
        $this->load->view($this->theme . 'pos/today_sale', $this->data);
    }

    public function updates()
    {
        if (DEMO) {
            $this->session->set_flashdata('warning', lang('disabled_in_demo'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        $this->form_validation->set_rules('purchase_code', lang('purchase_code'), 'required');
        $this->form_validation->set_rules('envato_username', lang('envato_username'), 'required');
        if ($this->form_validation->run() == true) {
            $this->db->update('pos_settings', ['purchase_code' => $this->input->post('purchase_code', true), 'envato_username' => $this->input->post('envato_username', true)], ['pos_id' => 1]);
            admin_redirect('pos/updates');
        } else {
            $fields = ['version' => $this->pos_settings->version, 'code' => $this->pos_settings->purchase_code, 'username' => $this->pos_settings->envato_username, 'site' => base_url()];
            $this->load->helper('update');
            $protocol              = is_https() ? 'https://' : 'http://';
            $updates               = get_remote_contents($protocol . 'api.tecdiary.com/v1/update/', $fields);
            $this->data['updates'] = json_decode($updates);
            $bc                    = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('updates')]];
            $meta                  = ['page_title' => lang('updates'), 'bc' => $bc];
            $this->page_construct('pos/updates', $meta, $this->data);
        }
    }

    /* ------------------------------------------------------------------------------------ */

    public function view($sale_id = null, $modal = null)
    {
        $this->sma->checkPermissions('index');
        $this->load->library('inv_qrcode');
        if ($this->input->get('id')) {
            $sale_id = $this->input->get('id');
        }
        $this->load->helper('pos');
        $this->data['error']   = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['message'] = $this->session->flashdata('message');
        $inv                   = $this->pos_model->getInvoiceByID($sale_id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by, true);
        }
        $this->data['rows']            = $this->pos_model->getAllInvoiceItems($sale_id);
        $biller_id                     = $inv->biller_id;
        $customer_id                   = $inv->customer_id;
        $this->data['biller']          = $this->pos_model->getCompanyByID($biller_id);
        $this->data['customer']        = $this->pos_model->getCompanyByID($customer_id);
        $this->data['payments']        = $this->pos_model->getInvoicePayments($sale_id);
        $this->data['order_tax']       = $this->pos_model->getOrderTaxRateByID($inv->order_tax_id);
        $this->data['pos']             = $this->pos_model->getSetting();
        $this->data['barcode']         = $this->barcode($inv->reference_no, 'code128', 30);
        $this->data['return_sale']     = $inv->return_id ? $this->pos_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows']     = $inv->return_id ? $this->pos_model->getAllInvoiceItems($inv->return_id) : null;
        $this->data['return_payments'] = $this->data['return_sale'] ? $this->pos_model->getInvoicePayments($this->data['return_sale']->id) : null;
        $this->data['inv']             = $inv;
        $this->data['sid']             = $sale_id;
        $this->data['modal']           = $modal;
        $this->data['created_by']      = $this->site->getUser($inv->created_by);
        $this->data['printer']         = $this->pos_model->getPrinterByID($this->pos_settings->printer);
        $this->data['page_title']      = $this->lang->line('invoice');
        $this->load->view($this->theme . 'pos/view', $this->data);
    }

    public function view_bill()
    {
        $this->sma->checkPermissions('index');
        $this->data['tax_rates'] = $this->pos_model->getOrderTaxRates();
        $this->load->view($this->theme . 'pos/view_bill', $this->data);
    }

    public function write_payments_config($config)
    {
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            admin_redirect('welcome');
        }
        if (DEMO) {
            return true;
        }
        $file_contents = file_get_contents('./assets/config_dumps/payment_gateways.php');
        $output_path   = APPPATH . 'config/payment_gateways.php';
        $this->load->library('parser');
        $parse_data = [
            'APIUsername'            => $config['APIUsername'],
            'APIPassword'            => $config['APIPassword'],
            'APISignature'           => $config['APISignature'],
            'stripe_secret_key'      => $config['stripe_secret_key'],
            'stripe_publishable_key' => $config['stripe_publishable_key'],
            'api_login_id'           => $config['api_login_id'],
            'api_transaction_key'    => $config['api_transaction_key'],
        ];
        $new_config = $this->parser->parse_string($file_contents, $parse_data);

        $handle = fopen($output_path, 'w+');
        @chmod($output_path, 0777);

        if (is_writable($output_path)) {
            if (fwrite($handle, $new_config)) {
                @chmod($output_path, 0644);
                return true;
            }
            @chmod($output_path, 0644);
            return false;
        }
        @chmod($output_path, 0644);
        return false;
    }

     /* --------------------------------------------------------------------------------------------- */

    public function suggestions($pos = 0)
    {
        $term         = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $customer_id  = $this->input->get('customer_id', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed  = $this->sma->analyze_term($term);
        $sr        = $analyzed['term'];
        $option_id = $analyzed['option_id'];
        $sr        = addslashes($sr);
        $strict    = $analyzed['strict']                    ?? false;
        $qty       = $strict ? null : $analyzed['quantity'] ?? null;
        $bprice    = $strict ? null : $analyzed['price']    ?? null;

        $warehouse      = $this->site->getWarehouseByID($warehouse_id);
        $customer       = $this->site->getCompanyByID($customer_id);
        $customer_group = $this->site->getCustomerGroupByID($customer->customer_group_id);
        $rows           = $this->pos_model->getProductNames($sr, $warehouse_id, $pos);

        if ($rows) {
            $r = 0;
            foreach ($rows as $row) {
                $c = uniqid(mt_rand(), true);
                unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology, $row->cf1, $row->cf2, $row->cf3, $row->cf4, $row->cf5, $row->cf6, $row->supplier1price, $row->supplier2price, $row->cfsupplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1, $row->supplier2, $row->supplier3, $row->supplier4, $row->supplier5, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                $option               = false;
                $row->quantity        = 0;
                $row->item_tax_method = $row->tax_method;
                $row->qty             = 1;
                $row->discount        = '0';
                $row->serial          = '';
                $options              = $this->sales_model->getProductOptions($row->id, $warehouse_id);
                if ($options) {
                    $opt = $option_id && $r == 0 ? $this->sales_model->getProductOptionByID($option_id) : $options[0];
                    if (!$option_id || $r > 0) {
                        $option_id = $opt->id;
                    }
                } else {
                    $opt        = json_decode('{}');
                    $opt->price = 0;
                    $option_id  = false;
                }
                $row->option = $option_id;
                $pis         = $this->site->getPurchasedItems($row->id, $warehouse_id, $row->option);
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
                if ($customer_group->discount && $customer_group->percent < 0) {
                    $row->discount = (0 - $customer_group->percent) . '%';
                } else {
                    $row->price = $row->price + (($row->price * $customer_group->percent) / 100);
                }
                $row->real_unit_price = $row->price;
                $row->base_quantity   = 1;
                $row->base_unit       = $row->unit;
                $row->base_unit_price = $row->price;
                $row->batch; 
                $row->unit            = $row->sale_unit ? $row->sale_unit : $row->unit;
                $row->comment         = '';
                $combo_items          = false;
                if ($row->type == 'combo') {
                    $combo_items = $this->sales_model->getProductComboItems($row->id, $warehouse_id);
                }
                if ($qty) {
                    $row->qty           = $qty;
                    $row->base_quantity = $qty;
                } else {
                    $row->qty = ($bprice ? $bprice / $row->price : 1);
                }
                $units    = $this->site->getUnitsByBUID($row->base_unit);
                $tax_rate = $this->pos_model->getOrderTaxRateByID($row->tax_rate);

                $pr[] = ['id' => sha1($c . $r), 'item_id' => $row->id, 'label' => $row->name . ' (' . $row->code . ')', 'category' => $row->category_id,
                    'row'     => $row, 'combo_items' => $combo_items, 'tax_rate' => $tax_rate, 'units' => $units, 'options' => $options, ];
                $r++;
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'label' => lang('no_match_found'), 'value' => $term]]);
        }
    }

    public function get_ordertax($order_id)
    {
        $tax_rate = $this->pos_model->getOrderTaxRateByID($order_id);
        $rate=$tax_rate->rate;
        print_r($rate);

    }



}


