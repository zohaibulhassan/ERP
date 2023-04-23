<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class Purchaseorder extends MY_Controller{
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
        $this->load->admin_model('purchaseorder_model');
        $this->load->admin_model('general_model');
    }
    public function index(){

        $this->data['warehouses'] = $this->general_model->GetAllWarehouses();
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();


        $this->data['warehouse'] = $this->input->get('warehouse');        
        $this->data['supplier'] = $this->input->get('supplier');        
        $this->data['status'] = $this->input->get('status');        


        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Purchase Order'));
        $meta = array('page_title' => 'Purchase Order', 'bc' => $bc);
        $this->page_construct2('purchase_order/index', $meta, $this->data);
    }
    public function get_lists(){
        // Count Total Rows
        $this->db->from('purchase_order_tb');
        $totalq = $this->db->get();
        $this->runquery_po('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_po();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/purchaseorder/view/".$row->id).'" >Detail</a>';
            $status = ucwords($row->status);
            if($status == "Pending"){
                $status = '<span class="uk-badge uk-badge-primary">'.$status.'</span>';
            }
            else if($status == "Partial"){
                $status = '<span class="uk-badge uk-badge-warning">'.$status.'</span>';
            }
            else if($status == "Received"){
                $status = '<span class="uk-badge uk-badge-success">'.$status.'</span>';
            }
            else if($status == "Closed"){
                $status = '<span class="uk-badge uk-badge-danger">'.$status.'</span>';
            }
            $data[] = array(
                $row->id,
                $row->created_at,
                $row->reference_no,
                $row->supplier,
                $row->own_company,
                $row->warehouse,
                $row->created_by,
                $row->completepercentage.'%',
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
    public function runquery_po($onlycoun = "no"){
        $column_search = array(
            'po.id',
            'po.created_at',
            'po.reference_no',
            'supplier.name',
            'w.name',
            'oc.companyname',
            'u.first_name',
            'u.last_name',
            'po.status'
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('po.id as id');
        }
        else{
            $this->db->select('
                po.id,
                po.created_at,
                po.reference_no,
                supplier.name as supplier,
                oc.companyname as own_company,
                w.name as warehouse,
                CONCAT(u.first_name," ",u.last_name) as created_by,
                COALESCE(ROUND(
                    (
                        (
                            SELECT SUM(COALESCE(sma_po_received_item_tb.received_qty,0)) 
                            FROM sma_po_received_item_tb 
                            WHERE sma_po_received_item_tb.po_id=po.id
                        )/
                        (
                            SELECT SUM(COALESCE(sma_purchase_order_items_tb.qty,0)) 
                            FROM sma_purchase_order_items_tb 
                            WHERE sma_purchase_order_items_tb.purchase_id=po.id
                        )
                    )*100
                ,0),0) AS completepercentage,
                po.status
            ');
        }
        $this->db->from('purchase_order_tb as po');
        $this->db->join('companies as supplier', 'supplier.id = po.supplier_id', 'left');
        $this->db->join('warehouses as w', 'w.id = po.warehouse_id', 'left');
        $this->db->join('own_companies as oc', 'oc.id = po.own_company', 'left');
        $this->db->join('users as u', 'u.id = po.created_by', 'left');
        if(isset($_POST['suppiler'])){
            if($_POST['suppiler'] != "all" && $_POST['suppiler'] != ""){
                $this->db->where('po.supplier_id',$_POST['suppiler']);
            }
        }
        if(isset($_POST['warehouse'])){
            if($_POST['warehouse'] != "all" && $_POST['warehouse'] != ""){
                $this->db->where('po.warehouse_id',$_POST['warehouse']);
            }
        }
        if(isset($_POST['status'])){
            if($_POST['status'] != "all" && $_POST['status'] != ""){
                $this->db->where('po.status',$_POST['status']);
            }
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
    public function add(){
        $this->data['owncompanies'] = $this->general_model->GetAllOwnCompanies();
        $this->data['suppliers'] = $this->general_model->GetAllSuppliers();
        $this->data['warehouses'] = $this->general_model->GetAllWarehouses();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Purchase Order'));
        $meta = array('page_title' => 'Purchase Order', 'bc' => $bc);
        $this->page_construct2('purchase_order/add', $meta, $this->data);
    }
    public function submit(){
        $sendvalue['status'] = false;
        $sendvalue['mesasge'] = '';
        $podata['reference_no'] = $this->generate_ref();
        $podata['supplier_id'] = $this->input->post('supplier');
        $podata['warehouse_id'] = $this->input->post('warehosue');
        $podata['own_company'] = $this->input->post('owncompany');
        $podata['receiving_date'] = dateformate($this->input->post('receiving_date'));

        $product_ids = $this->input->post('product_id');
        $qty = $this->input->post('qty');
        
        $items = array();
        $totaldiscount = 0;
        $totalitemstax = 0;
        $total = 0;
        $no = 0;
        foreach($product_ids as $key => $product_id){
            $product = $this->db->select('
                                products.*,
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
            $items[$no]['product_id'] = $product_id;
            $items[$no]['sales_incentive_discount'] = 0;
            $items[$no]['trade_discount'] = 0;
            $items[$no]['consumer_discount'] = 0;
            $items[$no]['sales_incentive_discount_amount'] = 0;
            $items[$no]['trade_discount_amount'] = 0;
            $items[$no]['consumer_discount_amount'] = 0;
            $items[$no]['total_discount'] = 0;
            $items[$no]['unit'] = $product->unit_code;
            $items[$no]['qty'] = $qty[$key];
            $items[$no]['qty_received'] = 0;
            $items[$no]['qty_balance'] = $qty[$key];
            $items[$no]['purchase_price'] = $product->cost;
            $items[$no]['consignment_price'] = $product->price;
            $items[$no]['dropship_price'] = $product->dropship;
            $items[$no]['cross_dock_price'] = $product->crossdock;
            $items[$no]['mrp'] = $product->mrp;
            $items[$no]['tax_id'] = $product->tax_id;
            $items[$no]['tax'] = $product->tax_name;
            if($product->tax_type == 2 && $product->tax_type == "2"){
                $items[$no]['tax_amount'] = $product->tax_rate;
            }
            else{
                $items[$no]['tax_amount'] = (($product->cost/100)*$product->tax_rate);
            }
            $items[$no]['fed_tax'] = $product->fed_tax;
            $items[$no]['total_tax'] = $items[$no]['tax_amount'];
            $items[$no]['sub_total'] = $items[$no]['purchase_price']+$items[$no]['total_tax']+0;
            $items[$no]['status'] = 'unreceived';

            $totalitemstax += $items[$no]['total_tax']*$qty[$key];
            $total += $items[$no]['sub_total']*$qty[$key];
            $no++;
        }



        $podata['items_discount'] = $totaldiscount;
        $podata['items_tax'] = $totalitemstax;
        $podata['total'] = $total;
        $podata['order_discount'] = 0;
        $podata['order_tax'] = 0;
        $podata['shipping'] = 0;
        $podata['grand_total'] = $podata['total']+$podata['order_tax']+$podata['shipping']-$podata['order_discount'];
        $podata['paid_amount'] = 0;
        $podata['payemnt_terms'] = '';
        $podata['created_by'] = $this->session->userdata('user_id');
        $podata['payment_status'] = "unpaid";
        $podata['status'] = "pending";
        $sendvalue = $this->purchaseorder_model->add_po($podata,$items);
        echo json_encode($sendvalue);
    }
    public function generate_ref(){
        $sendvalue = "";
        // generate Ref Number
        $dbdetail = $this->db;
        $this->db->set_dbprefix('');
        $this->db->select('AUTO_INCREMENT');
        $this->db->from('information_schema.TABLES');
        $this->db->where('TABLE_SCHEMA = "'.$dbdetail->database.'" AND TABLE_NAME = "sma_purchase_order_tb"');
        $refq = $this->db->get();
        $refresult = $refq->result();
        $this->db->set_dbprefix('sma_');
        if(count($refresult)>0){
            $sendvalue = 'PO-'.sprintf("%05d", $refresult[0]->AUTO_INCREMENT);
        }
        return $sendvalue;

    }
    public function view($id = ""){
        if($id != ""){
            $this->db->select('
                po.id as po_id,
                po.*,
                supplier.*,
                warehouses.name as warehosue_name,
                warehouses.id as warehosue_id,
                warehouses.phone as warehosue_phone,
                warehouses.email as warehosue_email
            ');
            $this->db->from('purchase_order_tb as po');
            $this->db->join('companies as supplier','supplier.id = po.supplier_id','left');
            $this->db->join('warehouses','warehouses.id = po.warehouse_id','left');
            $this->db->where('po.id',$id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $this->data['po'] = $q->result()[0];
                $this->data['deliveries'] = $this->purchaseorder_model->deliveries($id);
                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Purchase Order'));
                $meta = array('page_title' => 'Purchase Order', 'bc' => $bc);
                $this->page_construct2('purchase_order/view', $meta, $this->data);
            }
            else{
                redirect(base_url('admin/purchaseorder'));
            }
        }
        else{
            redirect(base_url('admin/purchaseorder'));
        }
    }
    public function get_items(){
        // Count Total Rows
        $this->db->from('purchase_order_items_tb');
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
            $button = "";
            if($row->postatus == "pending" OR $row->postatus == "partial"){
                $button .= '<button class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini itemedit" type="button" data-id="'.$row->id.'" data-product="'.$row->product_id.'" data-qty="'.$row->qty.'" data-panem="'.$row->product_name.'" >Edit</button>';
                $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini itemdelete" type="button" data-id="'.$row->id.'" >Delete</button>';
            }

            $data[] = array(
                $sno,
                $row->product_id,
                $row->barcode,
                $row->product_name,
                $row->qty,
                $row->count_receving,
                $row->qty-$row->count_receving,
                decimalallow(($row->count_receving/$row->qty)*100,2).'%',
                $row->purchase_price,
                amountformate($row->total_tax*$row->qty,4),
                amountformate($row->sub_total*$row->qty,4),
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
            $this->db->select('po_items.id as id');
        }
        else{
            $this->db->select('
                po_items.*,
                products.id as product_id,
                po.status as postatus,
                products.name as product_name,
                products.code as barcode,
                COALESCE((SELECT SUM(COALESCE(sma_po_received_item_tb.received_qty,0)) FROM sma_po_received_item_tb WHERE sma_po_received_item_tb.po_item_id=po_items.id),0) AS count_receving
            ');
        }
        $this->db->from('purchase_order_items_tb as po_items');
        $this->db->join('products as products', 'products.id = po_items.product_id', 'left');
        $this->db->join('purchase_order_tb as po', 'po.id = po_items.purchase_id', 'left');
        $this->db->where('purchase_id',$id);
    }
    public function insert_item(){
        $product = $this->input->post('product');
        $quanitty = $this->input->post('quanitty');

        $senddata['status'] = false;
        $senddata['message'] = "";
        
        $poid = $this->input->post('poid');
        $this->db->select('
            sma_products.*,
            sma_units.code as unit_code,
            sma_tax_rates.id as tax_id,
            sma_tax_rates.name as tax_name,
            sma_tax_rates.rate as tax_rates,
            sma_tax_rates.type as tax_type,
        ');
        $this->db->from('sma_products');
        $this->db->join('sma_units', 'sma_units.id = sma_products.unit', 'left');
        $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_products.tax_rate', 'left');
        $this->db->where('sma_products.id',$this->input->post('product'));
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $this->db->select('*');
            $this->db->from('sma_purchase_order_tb');
            $this->db->where('id',$poid);
            $q2 = $this->db->get();
            if($q2->num_rows() > 0){

                $this->db->select('*');
                $this->db->from('sma_purchase_order_items_tb');
                $this->db->where('product_id',$this->input->post('product'));
                $this->db->where('purchase_id',$poid);
                $q3 = $this->db->get();
                if($q3->num_rows() == 0){
                    $product = $q->result();
                    $insertdata['product_id'] = $this->input->post('product');
                    $insertdata['purchase_id'] = $poid;
                    $insertdata['supplier_part_no'] = '';
                    $insertdata['unit'] = $product[0]->unit_code;
                    $insertdata['qty'] = $this->input->post('quanitty');
                    $insertdata['qty_received'] = 0;
                    $insertdata['qty_balance'] = $this->input->post('quanitty');

                    $insertdata['sales_incentive_discount'] = 0;
                    $insertdata['sales_incentive_discount_amount'] = 0;

                    $insertdata['trade_discount'] = 0;
                    $insertdata['trade_discount_amount'] = 0;

                    $insertdata['consumer_discount'] = 0;
                    $insertdata['consumer_discount_amount'] = 0;

                    $insertdata['purchase_price'] = $product[0]->cost;
                    $insertdata['consignment_price'] = $product[0]->price;
                    $insertdata['dropship_price'] = $product[0]->dropship;
                    $insertdata['cross_dock_price'] = $product[0]->crossdock;
                    $insertdata['mrp'] = $product[0]->mrp;
                    $insertdata['tax_id'] = $product[0]->tax_id;
                    $insertdata['tax'] = $product[0]->tax_name;
                    if($product[0]->tax_type == 2){
                        $insertdata['tax_amount'] = $product[0]->tax_rates;
                    }
                    else{
                        $insertdata['tax_amount'] = ($product[0]->cost/100)*$product[0]->tax_rates;
                    }
                    $insertdata['fed_tax'] = $product[0]->fed_tax;
                    $insertdata['total_tax'] = $insertdata['tax_amount'];
                    $insertdata['total_discount'] = $insertdata['sales_incentive_discount_amount']+$insertdata['trade_discount_amount']+$insertdata['consumer_discount_amount'];
                    $insertdata['sub_total'] = $product[0]->cost+$insertdata['tax_amount']+$insertdata['fed_tax']-$insertdata['total_discount'];
                    $insertdata['status'] = 'unreceived';

                    $this->db->insert('sma_purchase_order_items_tb',$insertdata);
                    $this->updatepoprice($poid);
                    $senddata['message'] = "Item Add Successfully";
                    $senddata['status'] = true;
                }
                else{
                    $senddata['message'] = "This item already add this PO";
                }
            }
            else{
                $senddata['message'] = "PO Not Found";
            }
        }
        else{
            $senddata['message'] = "Product Not Found";
        }
        echo json_encode($senddata);



    }
    public function update_item(){
        $sendvalue['message'] = "no";
        $sendvalue['status'] = false;
        $id = $this->input->post('poid');
        $qty = $this->input->post('quanitty');

        $this->db->select('
            sma_purchase_order_items_tb.*,
            (
                SELECT COALESCE(SUM(sma_po_received_item_tb.received_qty),0) 
                FROM sma_po_received_item_tb 
                WHERE sma_po_received_item_tb.po_item_id=sma_purchase_order_items_tb.id
            ) AS count_receving,
            sma_products.discount_one,
            sma_products.discount_two,
            sma_products.discount_three,
            sma_products.name as product_name,
            sma_products.price as product_price,
        ');
        $this->db->from('sma_purchase_order_items_tb');
        $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
        $this->db->where('sma_purchase_order_items_tb.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $item = $q->result();

            if($qty >= $item[0]->qty_received){

                $setdata['qty'] = $qty;
                $setdata['qty_balance'] = $qty;

                $setdata['sales_incentive_discount_amount'] = 0;
                $setdata['sales_incentive_discount'] = 0;

                $setdata['trade_discount_amount'] = 0;
                $setdata['trade_discount'] = 0;

                $setdata['consumer_discount_amount'] = 0;
                $setdata['consumer_discount'] = 0;

                $setdata['total_discount'] = $setdata['sales_incentive_discount_amount']+$setdata['trade_discount_amount']+$setdata['consumer_discount_amount'];

                $this->db->set($setdata);
                $this->db->where('id',$id);
                $this->db->update('sma_purchase_order_items_tb');
                $this->updatepoprice($item[0]->purchase_id);
                $sendvalue['message'] = "Item update successfully";
                $sendvalue['status'] = true;
            }
            else{
                $alertqty = $item[0]->qty-$item[0]->qty_received;
                $sendvalue['message'] = "You can only decrease ".$alertqty." Qty";
            }
        }
        else{
            $sendvalue['message'] = "Item not found";
        }
        echo json_encode($sendvalue);

    }
    public function delete_item(){
        $poi_id = $this->input->post('id');
        $poid = $this->input->post('poid');
        $sendvalue['status'] = false;
        $sendvalue['message'] = '';
        $this->db->select('*');
        $this->db->from('sma_po_received_item_tb');
        $this->db->where('po_item_id',$poi_id);
        $q = $this->db->get();
        if($q->num_rows() == 0){
            $res = $q->result();
            $this->db->delete('sma_purchase_order_items_tb', array('id' => $poi_id));
            $this->updatepoprice($poid);
            $sendvalue['message'] = 'Item delete successfullly';
            $sendvalue['status'] = true;
        }
        else{
            $sendvalue['message'] = 'Firstly delete this item receving';
        }
        echo json_encode($sendvalue);
    }
    public function close(){
        $id = $this->input->post('id');
        $data['status'] = false;
        if($id != ""){
            $this->db->select('id,status');
            $this->db->from('sma_purchase_order_tb');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){

                $res = $q->result();

                if($res[0]->status == "closed"){
                    $data['message'] = "This Purchase Order already closed";
                }
                else if($res[0]->status == "received"){
                    $data['message'] = "This Purchase Order items received";
                }
                else{

                    $setdata['close_date'] = date('Y-m-d H:i:s');
                    $setdata['status'] = 'closed';
                    $this->db->set($setdata);
                    $this->db->where('id',$id);
                    $this->db->update('sma_purchase_order_tb');

                    $data['message'] = "Purchase Order Close Successfully";
                    $data['status'] = true;
                }
            }
            else{
                $data['message'] = "Purchase Order not found";
            }
        }
        else{
            $data['message'] = "Purchase Order not found";
        }
        echo json_encode($data);
    }
    public function delete(){
        $id = $this->input->post('id');
        $data['status'] = false;
        if($id != ""){
            $this->db->select('sma_purchase_order_tb.id');
            $this->db->from('sma_purchase_order_tb');
            $this->db->join('sma_po_received_tb', 'sma_po_received_tb.po_id = sma_purchase_order_tb.id', 'left');
            $this->db->where('sma_purchase_order_tb.id',$id);
            $this->db->where('sma_po_received_tb.purchase_create','yes');
            $q = $this->db->get();
            if($q->num_rows() == 0){
                $this->db->delete('sma_purchase_order_tb', array('id' => $id));
                $this->db->delete('sma_purchase_order_items_tb', array('purchase_id' => $id));
                $this->db->delete('sma_po_received_tb', array('po_id' => $id));
                $this->db->delete('sma_po_received_item_tb', array('po_id' => $id));
                $data['message'] = "PO delete successfully";
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
    public function pdf($id = null){
        if($id != ""){
            $returndata  = $this->purchaseorder_model->details($id);
            if($returndata['codestatus'] == "ok"){
                $this->data['detail'] = $returndata['details'];
                $this->data['deliveries'] = $this->purchaseorder_model->deliveries($id);
                $this->data['purchase_create'] = $this->purchaseorder_model->purchase_create($id);
                $name = $this->data['detail']->reference_no . ".pdf";
                $html = $this->load->view($this->theme . 'purchase_order/pdf', $this->data, true);
                if (!$this->Settings->barcode_img) {
                    $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
                }
                if (isset($save_bufffer)) {
                    return $this->sma->generate_pdf($html, $name, $save_bufffer);
                }
                else {
                    $this->sma->generate_pdf($html, $name);
                }
            }
            else{
                echo '<script>alert("'.$returndata['codestatus'].'"); location.href = "'.base_url('admin/purchaseorder').'";</script>';
            }
        }
        else{
            redirect(base_url('admin/purchaseorder'));
        }
    }
    public function addreciveing($id = null){
        if($id != ""){
            $items = $this->purchaseorder_model->getreceivingitems($id);
            $this->data['items'] = $items['items'];
            $this->data['po_id'] = $id;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Add Receiving Items'));
            $meta = array('page_title' => 'Add Receiving Items', 'bc' => $bc);
            $this->page_construct2('purchase_order/addreceiving', $meta, $this->data);
        }
        else{
            redirect(base_url('admin/purchaseorder'));
        }
    }
    public function submit_recived(){
        $sendvalue['status'] = false;
        $sendvalue['message'] = '';
        $po_item = $this->input->post('po_item');
        $po_id = $this->input->post('po_id');
        $product_id = $this->input->post('product_id');
        $rqty = $this->input->post('rqty');
        $batch = $this->input->post('batch');
        $expirydate = $this->input->post('expirydate');
        $rows = count($po_item);
        $items = array();
        for($i=0;$i<$rows;$i++){
            $itemdetails = $this->purchaseorder_model->getitem($po_item[$i]);

            if($rqty[$i] == ""){
                $rqty[$i] = "0";
            }

            if($batch[$i] == ""){

            }
            else if($rqty[$i] == "0" && $expirydate[$i] != ""){
                $sendvalue['message'] = 'Add item receiving quantity. Product Name: '.$itemdetails['product_name'];
                echo json_encode($sendvalue);
                exit();
            }
            else if($rqty[$i] != "0" && $expirydate[$i] == ""){
                $sendvalue['message'] = 'Add item expiry date. Product Name: '.$itemdetails['product_name'];
                echo json_encode($sendvalue);
                exit();
            }
            
            else{
                if($this->checkbatch($batch[$i],$product_id[$i]) == "yes"){
                    $sendvalue['message'] = $batch[$i].' batch already available.';
                    echo json_encode($sendvalue);
                    exit();
                    
                }
                else{
                    $dueitems = $itemdetails['qty'] - $itemdetails['count_receving'];
                    if($dueitems>=$rqty[$i]){
                        $items[$i]['po_id'] = $po_id[$i];
                        $items[$i]['po_item_id'] = $po_item[$i];
                        $items[$i]['product_id'] = $product_id[$i];
                        $items[$i]['received_qty'] = $rqty[$i];
                        $items[$i]['batch_code'] = $batch[$i];
                        // $items[$i]['expiry_date'] = date("Y-m-d H:i:s", strtotime(strtr($expirydate[$i], '/', '-')));
                        $items[$i]['expiry_date'] = date("Y-m-d H:i:s", strtotime($expirydate[$i]));
                        $items[$i]['created_by'] = $this->session->userdata('user_id');
                    }
                    else{
                        $sendvalue['message'] = 'You can received only '.$dueitems.' quantity. Product Name: '.$itemdetails['product_name'];
                        echo json_encode($sendvalue);
                        exit();
                    }
                }
            }
        }
        if(count($items) != 0){
            $this->purchaseorder_model->addreceiving($items);
            $sendvalue['message'] = 'Add record successfully';
            $sendvalue['status'] = true;
        }
        else{
            $sendvalue['message'] = 'Please add batch and Receiving Quantitty';
        }
        echo json_encode($sendvalue);
    }
    public function pordelete(){
        $sendvalue['status'] = false;
        $sendvalue['message'] = '';
        $id = $this->input->post('id');
        if($id != ""){
            $this->db->select('*');
            $this->db->from('sma_po_received_tb');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $res = $q->result();
                if($res[0]->purchase_create == "no"){
                    $this->db->delete('sma_po_received_tb', array('id' => $id));
                    $this->db->delete('sma_po_received_item_tb', array('receiving_id' => $id));
                    $this->purchaseorder_model->updatestatus($res[0]->po_id);
                    $sendvalue['status'] = true;
                    $sendvalue['message'] = "Purchase Order Receiving Delete Successfully";
                }
                else{
                    $sendvalue['message'] = "Firstly Delete Receiving.";
                }
            }
            else{
                $sendvalue['message'] = "Invalid Receving";
            }
        }
        else{
            $sendvalue['message'] = "Reveving ID not found";
        }
        echo json_encode($sendvalue);
    }
    public function editreciveing($id = null){
        $poid = $this->input->get('poid');
        $porid = $this->input->get('porid');
        if($poid != "" && $porid != ""){
            $receivingdata = $this->purchaseorder_model->getreceiving($porid);
            $this->data['items'] = $receivingdata['items'];
            $this->data['po_id'] = $poid;
            $this->data['porid'] = $porid;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Edit Receiving Items'));
            $meta = array('page_title' => 'Edit Receiving Items', 'bc' => $bc);
            $this->page_construct2('purchase_order/editreceiving', $meta, $this->data);

        }
        else{
            redirect(base_url('admin/purchaseorder'));
        }
    }
    public function submit_editrecived(){
        $sendvalue['status'] = false;
        $sendvalue['message'] = '';
        $por_item_id = $this->input->post('por_item_id');
        $po_item = $this->input->post('po_item');
        $po_id = $this->input->post('po_id');
        $product_id = $this->input->post('product_id');
        $rqty = $this->input->post('rqty');
        $batch = $this->input->post('batch');
        $expirydate = $this->input->post('expirydate');
        $getporid = $this->input->post('porid');
        $porid = 0;
        $rows = count($po_item);
        $items = array();
        for($i=0;$i<$rows;$i++){
            $itemdetails = $this->purchaseorder_model->getitem($po_item[$i]);
            $porid = $getporid[$i];
            if($rqty[$i] == ""){
                $rqty[$i] = "0";
            }

            if($batch[$i] == ""){

            }
            else if($rqty[$i] == "0" && $expirydate[$i] != ""){
                $sendvalue['message'] = 'Add item receiving quantity. Product Name: '.$itemdetails['product_name'];
                echo json_encode($sendvalue);
                exit();
            }
            else if($rqty[$i] != "0" && $expirydate[$i] == ""){
                $sendvalue['message'] = 'Add item expiry date. Product Name: '.$itemdetails['product_name'];
                echo json_encode($sendvalue);
                exit();
            }
            
            else{
                if($this->checkbatch2($batch[$i],$product_id[$i],$por_item_id[$i]) == "yes"){
                    $sendvalue['message'] = $batch[$i].' batch already available.';
                    echo json_encode($sendvalue);
                    exit();
                }
                else{
                    $dueitems = $itemdetails['qty'];
                    if($dueitems>=$rqty[$i]){
                        $items[$i]['po_id'] = $po_id[$i];
                        $items[$i]['po_item_id'] = $po_item[$i];
                        $items[$i]['product_id'] = $product_id[$i];
                        $items[$i]['received_qty'] = $rqty[$i];
                        $items[$i]['batch_code'] = $batch[$i];
                        $items[$i]['expiry_date'] = date("Y-m-d H:i:s", strtotime(strtr($expirydate[$i], '/', '-')));
                        $items[$i]['created_by'] = $this->session->userdata('user_id');
                    }
                    else{
                        $sendvalue['message'] = 'You can received only '.$dueitems.' quantity. Product Name: '.$itemdetails['product_name'];
                        echo json_encode($sendvalue);
                        exit();
                    }
                }
            }
        }
        if(count($items) != 0){
            $this->db->where('receiving_id', $porid);
            $this->db->delete('sma_po_received_item_tb');
            $this->purchaseorder_model->addreceiving2($items,$porid);
            $sendvalue['message'] = 'Edit record successfully';
            $sendvalue['status'] = true;
            echo json_encode($sendvalue);
        }
        else{
            $sendvalue['message'] = 'Please add batch and Receiving Quantitty';
            echo json_encode($sendvalue);
        }
    }
    public function checkbatch($batch,$pid){
        $sendvalue = "no";
        $this->db->select('id');
        $this->db->from('sma_purchase_items');
        $this->db->where('batch',$batch);
        $this->db->where('product_id ',$pid);
        $q1 = $this->db->get();

        $this->db->select('id');
        $this->db->from('sma_po_received_item_tb');
        $this->db->where('batch_code',$batch);
        $this->db->where('product_id ',$pid);
        $q2 = $this->db->get();

        if($q1->num_rows() > 0 || $q2->num_rows() > 0){
            $sendvalue = "yes";
        }
        return $sendvalue;

    }
    public function checkbatch2($batch,$pid,$por_item_id){
        $sendvalue = "no";
        $this->db->select('id');
        $this->db->from('sma_purchase_items');
        $this->db->where('batch',$batch);
        $this->db->where('product_id ',$pid);
        $q1 = $this->db->get();

        $this->db->select('id');
        $this->db->from('sma_po_received_item_tb');
        // $this->db->where('batch_code',$batch);
        // $this->db->where('product_id ',$pid);

        $this->db->where('batch_code = "'.$batch.'" AND product_id = '.$pid.' AND id != '.$por_item_id);


        $q2 = $this->db->get();

        if($q1->num_rows() > 0 || $q2->num_rows() > 0){
            $sendvalue = "yes";
        }
        return $sendvalue;

    }




    // Old Code
    public function suggestions(){
        $term = $this->input->get('term', true);
        $warehouse_id = $this->input->get('warehouse_id', true);
        $supplier_id = $this->input->get('supplier_id', true);
        $own_company = $this->input->get('own_company', true);
        if($warehouse_id != "" && $supplier_id != ""){
            if (strlen($term) < 1 || !$term) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
            }

            $analyzed = $this->sma->analyze_term($term);
            $sr = $analyzed['term'];
            $option_id = $analyzed['option_id'];

            $rows = $this->purchaseorder_model->product_detail($sr, $warehouse_id, $supplier_id);

            $fed_tax_price = $this->site->fed_tax($q);
            $gst_tax_price = $this->site->gst_tax($q);
            $further_tax_price = $this->site->further_tax($q);
            $own_company_details = $this->site->getown_companiesByID($own_company);

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
                    $carton_size = $row->carton_size == 0 ? 1 : $row->carton_size;
                    $row->real_unit_cost = $row->cost;
                    $row->base_quantity = $carton_size;
                    $row->base_unit = $row->unit;
                    $row->base_unit_cost = $row->cost;
                    $row->unit = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                    $row->new_entry = $carton_size;
                    $row->expiry = '';
                    $row->qty = $carton_size;
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

                    $row->own_companies_check_strn = $own_company_details->strn;
                    $row->show_further_tax = $further_tax_price->further_tax;

                    unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);

                    $units = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);

                    $pr[] = array(
                        'id' => $row->id, 'item_id' => $row->id, 'label' => $row->name . " (Code: " . $row->code . ", MRP: " . decimalallow($row->mrp,2) . ")",
                        'row' => $row, 'tax_rate' => $tax_rate, 'units' => $units, /*'batch' => "123456", 'expiry' => "12/12/2018", */ 'options' => 0
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
    public function gettex(){
        $id = $this->input->post('id');
        $tax_rate = $this->site->getTaxRateByID($id);
        echo json_encode($tax_rate);
    }
    public function itemdata(){
        $data = array();
        $id = $this->input->post('id');
        if($id != ""){
            $data = $this->purchaseorder_model->getitem($id);
        }
        else{
            $data['codestatus'] = "Someting wrong";
        }
        echo json_encode($data);
    }
    public function createpurchase(){
        $data = array();
        $id = $this->input->get('rid');
        if($id != ""){
            $data = $this->purchaseorder_model->create_purchase($id);
        }
        else{
            $data['codestatus'] = "Receiving ID not found";
        }
        echo json_encode($data);
    }
    public function allcreate(){
        $sendvalue['codestatus'] = 'no';
        $did = $this->input->post('did');
        $lists = array();
        if($did != ""){
            $lists = $did;
        }
        if(count($lists)>0){
            
            $finaldata =  array();
            $finaldata['po_ris'] = array();
            $finaldata['p']['total'] = 0;
            $finaldata['p']['product_discount'] = 0;
            $finaldata['p']['order_discount'] = 0;
            $finaldata['p']['total_discount'] = 0;
            $finaldata['p']['order_tax_id'] = "";
            $finaldata['p']['order_tax'] = 0;
            $finaldata['p']['product_tax'] = 0;
            $finaldata['p']['total_tax'] = 0;

            $finaldata['p']['grand_total'] = 0;

            $this->db->select('
                sma_po_received_item_tb.id as pori_id,
                sma_po_received_item_tb.receiving_id as por_id,
                sma_po_received_item_tb.received_qty as por_r_qty,
                sma_po_received_item_tb.batch_code as por_batch,
                sma_po_received_item_tb.expiry_date as por_expiry,
                sma_purchase_order_items_tb.*,
                sma_companies.name as supplier_name,
                sma_products.code as product_code,
                sma_products.name as product_name,
                sma_purchase_order_tb.reference_no as po_reference_no,
                sma_purchase_order_tb.supplier_id as po_supplier_id,
                sma_purchase_order_tb.warehouse_id as po_warehouse_id,
                sma_purchase_order_tb.note as po_note,
                sma_purchase_order_tb.own_company as po_own_company,
                sma_purchase_order_tb.payemnt_terms as po_payemnt_terms,
                sma_purchase_order_tb.payment_status as po_payment_status,
                sma_purchase_order_tb.status as po_status,
                sma_purchase_order_tb.paid_amount as po_paid_amount,
                sma_purchase_order_tb.shipping as po_shipping,
                sma_purchase_order_tb.order_tax_id as  po_order_tax_id,
                sma_tax_rates.rate as  tax_rate,
                sma_tax_rates.type as  tax_type,
            ');
            $this->db->from('sma_po_received_item_tb');
            $this->db->join('sma_purchase_order_items_tb', 'sma_purchase_order_items_tb.id = sma_po_received_item_tb.po_item_id', 'left');
            $this->db->join('sma_purchase_order_tb', 'sma_purchase_order_tb.id = sma_purchase_order_items_tb.purchase_id', 'left');
            $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
            $this->db->join('sma_companies', 'sma_companies.id = sma_purchase_order_tb.supplier_id', 'left');
            $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_purchase_order_tb.order_tax_id', 'left');

            foreach($lists as $list){
                $this->db->or_where('receiving_id',$list);
            }
            $q = $this->db->get();
            $rows = $q->result();

            $no = 0;
            foreach ($rows as $row) {

                $finaldata['p']['reference_no'] = $row->po_reference_no;
                $finaldata['p']['supplier_id'] = $row->po_supplier_id;
                $finaldata['p']['warehouse_id'] = $row->po_warehouse_id;
                $finaldata['p']['own_company'] = $row->po_own_company;
                $finaldata['p']['note'] = $row->po_note;
                $finaldata['p']['payment_status'] = $row->po_payment_status;
                $finaldata['p']['payment_term'] = $row->po_payemnt_terms;
                $finaldata['p']['status'] = $row->po_status;
                $finaldata['p']['supplier'] = $row->supplier_name;
                $finaldata['p']['surcharge'] = "0.000";
                $finaldata['p']['paid'] = $row->po_paid_amount;
                $finaldata['p']['created_by'] = $this->session->userdata('user_id');
                $finaldata['p']['shipping'] = $row->po_shipping;

                $finaldata['pts'][$no]['po_ri_id'] = $row->pori_id;
                $finaldata['pts'][$no]['product_id'] = $row->product_id;
                $finaldata['po_ris'][$no]['po_ri_id'] = $row->pori_id;
                $finaldata['po_ris'][$no]['po_r_id'] = $row->por_id;
                $finaldata['pts'][$no]['product_code'] = $row->product_code;
                $finaldata['pts'][$no]['product_name'] = $row->product_name;
                $finaldata['pts'][$no]['net_unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['price'] = $row->consignment_price;
                $finaldata['pts'][$no]['dropship'] = $row->dropship_price;
                $finaldata['pts'][$no]['crossdock'] = $row->cross_dock_price;
                $finaldata['pts'][$no]['mrp'] = $row->mrp;
                $finaldata['pts'][$no]['quantity'] = $row->por_r_qty;
                $finaldata['pts'][$no]['warehouse_id'] = $row->po_warehouse_id;
                $finaldata['pts'][$no]['item_tax'] = $row->tax_amount;
                $finaldata['pts'][$no]['tax_rate_id'] = $row->tax_id;
                $finaldata['pts'][$no]['tax'] = $row->tax;
                $finaldata['pts'][$no]['discount'] = ($row->sales_incentive_discount+$row->trade_discount_amount+$row->consumer_discount_amount)*$row->por_r_qty;
                $finaldata['pts'][$no]['item_discount'] = $finaldata['pts'][$no]['discount'];
                $finaldata['pts'][$no]['expiry'] = date_format(date_create($row->por_expiry),"d/m/Y");
                $finaldata['pts'][$no]['batch'] = $row->por_batch;                  ;
                $finaldata['pts'][$no]['quantity_balance'] = $row->por_r_qty;
                $finaldata['pts'][$no]['date'] = date('Y-m-d');
                $finaldata['pts'][$no]['status'] = 'received';
                $finaldata['pts'][$no]['unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['real_unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['quantity_received'] = $row->por_r_qty;
                $finaldata['pts'][$no]['supplier_part_no'] = $row->supplier_part_no;
                $finaldata['pts'][$no]['product_unit_code'] = $row->unit;
                $finaldata['pts'][$no]['unit_quantity'] = $row->por_r_qty;
                $finaldata['pts'][$no]['discount_one'] = $row->sales_incentive_discount;
                $finaldata['pts'][$no]['discount_two'] = $row->trade_discount;
                $finaldata['pts'][$no]['discount_three'] = $row->consumer_discount;
                $finaldata['pts'][$no]['fed_tax'] = $row->fed_tax;
                $finaldata['pts'][$no]['subtotal'] = ($row->purchase_price*$row->por_r_qty)+($row->tax_amount+$row->fed_tax)-$finaldata['pts'][$no]['discount'];

                $finaldata['p']['total'] = $finaldata['p']['total']+($finaldata['pts'][$no]['net_unit_cost']*$finaldata['pts'][$no]['quantity']);
                $finaldata['p']['product_discount'] = $finaldata['p']['product_discount']+$finaldata['pts'][$no]['discount'];
                $finaldata['p']['grand_total'] = $finaldata['p']['grand_total']+$finaldata['pts'][$no]['subtotal'];
                $finaldata['p']['order_discount'] = $finaldata['p']['product_discount'];
                $finaldata['p']['order_tax_id'] = $row->po_order_tax_id;
                $finaldata['p']['product_tax'] = $finaldata['p']['product_tax']+$finaldata['pts'][$no]['item_tax'];

                if($row->tax_type == 2){
                    $finaldata['p']['order_tax'] = $finaldata['p']['total']+$row->tax_rate;
                }
                else{
                    $finaldata['p']['order_tax'] = ($finaldata['p']['total']/100)*$row->tax_rate;
                } 
                $finaldata['p']['total_tax'] = $finaldata['p']['order_tax']+$finaldata['p']['product_tax'];

                $no++;
            }

            $this->db->insert('sma_purchases', $finaldata['p']);
            $p_id = $this->db->insert_id();
            $sendvalue['p_id'] = $p_id;

            $no2 = 0;
            foreach($finaldata['pts'] as $item){

                $insertdata = $item;
                unset($insertdata["po_ri_id"]);
                $insertdata['purchase_id'] = $p_id;
                $this->db->insert('sma_purchase_items', $insertdata);
                $this->updatewarehouseqty($item['product_id'],$item['warehouse_id'],$item['quantity_received'],$item['net_unit_cost']);
                $this->updateproductqty($item['product_id'],$item['quantity_received']);
                 $no2++;

            }
            foreach($finaldata['po_ris'] as $row2){
                $setdata['purchase_id'] = $p_id;
                $setdata['purchase_create'] = 'yes';
                $this->db->set($setdata);
                $this->db->where('id', $row2['po_r_id']);
                $this->db->update('sma_po_received_tb');

                $this->db->set($setdata);
                $this->db->where('id', $row2['po_ri_id']);
                $this->db->update('sma_po_received_item_tb');
            }
            $sendvalue['codestatus'] = 'ok';
            $sendvalue['message'] = 'All Purchases Create Successfully';
        }
        else{
            $sendvalue['message'] = 'Select delivery';
        }
        echo json_encode($sendvalue);
    }
    public function updatewarehouseqty($pid,$wid,$qty,$price){
        $this->db->select('*');
        $this->db->from('sma_warehouses_products');
        $this->db->where(['product_id'=>$pid,'warehouse_id '=>$wid]);
        $q = $this->db->get();
        if($q->num_rows() != 0){
            $result =  $q->result();
            $total = $result[0]->quantity+$qty;
            $this->db->set('quantity',$total);
            $this->db->where('id',$result[0]->id);
            $this->db->update('sma_warehouses_products');
        }
        else{
            $insertdata['product_id'] = $pid;
            $insertdata['warehouse_id'] = $wid;
            $insertdata['quantity'] = $qty;
            $insertdata['rack'] = '';
            $insertdata['avg_cost'] = $price;
            $this->db->insert('sma_warehouses_products',$insertdata);
        }
    }
    public function updateproductqty($pid,$qty){
        $this->db->select('*');
        $this->db->from('sma_products');
        $this->db->where(['id'=>$pid]);
        $q = $this->db->get();
        if($q->num_rows() != 0){
            $result =  $q->result();
            $total = $result[0]->quantity+$qty;
            $this->db->set('quantity',$total);
            $this->db->where('id',$result[0]->id);
            $this->db->update('sma_products');
        }
    }
    public function update(){
        $sendvalue['codestatus'] = "no";
        $id = $this->input->post('id');
        $rdate = $this->sma->fld(trim($this->input->post('date')));
        $warehouse = $this->input->post('warehouse');
        $own_company = $this->input->post('own_company');
        $order_tax = $this->input->post('order_tax');
        $discount = $this->input->post('discount');
        $shipping = $this->input->post('shipping');
        $payment_term = $this->input->post('payment_term');
        $note = $this->input->post('note');

        $this->db->select('*');
        $this->db->from('sma_purchase_order_tb');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $result = $q->result();

            $tax_rate = $this->site->getTaxRateByID($order_tax);
            $setdata['order_tax_id'] = $tax_rate->id;
            if($tax_rate->type == 2){
                $setdata['order_tax'] = $tax_rate->rate;
            }
            else{
                $setdata['order_tax'] = ($result[0]->total/100)*$tax_rate->rate;
            }

            $setdata['warehouse_id'] = $warehouse;
            $setdata['receiving_date'] = $rdate;
            $setdata['own_company'] = $own_company;
            $setdata['payemnt_terms'] = $payment_term;
            $setdata['updated_at'] = date('Y-m-d H:i:s');
            $setdata['note'] = $note;
            $setdata['order_discount'] = $discount;
            $setdata['shipping'] = $shipping;
            $setdata['grand_total'] = $result[0]->total+$setdata['order_tax']+$shipping-$discount;
            $this->db->set($setdata);
            $this->db->where('id',$id);
            $this->db->update('sma_purchase_order_tb');
            $sendvalue['message'] = "Purchase Update Successfully";
            $sendvalue['codestatus'] = "ok";
        }
        else{
            $sendvalue['message'] = "Purchase Order Not Found";
        }
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
        $rows['results'] = $this->purchaseorder_model->productslist($term, $limit, $supplier_id, $warehouse_id, $suown_companypplier_id);
        $this->sma->send_json($rows);
    }
    public function productdetail(){
        $data['codestatus'] = "no";
        $pid =  $this->input->post('pid');
        $this->db->select('
            products.id,
            products.cost,
            products.price,
            products.mrp,
            products.discount_one,
            products.discount_two,
            products.discount_three,
            products.fed_tax,
            products.tax_rate,
            sma_tax_rates.rate as tax_rate_price,
            sma_tax_rates.type as tax_type,
        ');
        $this->db->where('products.id',$pid);
        $this->db->from('products');
        $this->db->join('sma_tax_rates', 'sma_tax_rates.id = products.tax_rate', 'left');
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $res = $q->result();
            $data['detail']['cost'] = $this->sma->formatDecimal($res[0]->cost);
            $data['detail']['price'] = $this->sma->formatDecimal($res[0]->price);
            $data['detail']['mrp'] = $this->sma->formatDecimal($res[0]->mrp);

            $data['detail']['discount_one'] = $this->sma->formatDecimal($res[0]->discount_one);
            $data['detail']['discount_two'] = $this->sma->formatDecimal($res[0]->discount_two);
            $data['detail']['discount_three'] = $this->sma->formatDecimal($res[0]->discount_three);

            $data['detail']['discount_one_amount'] = $this->sma->formatDecimal(($res[0]->price/100)*$res[0]->discount_one);
            $data['detail']['discount_two_amount'] = $this->sma->formatDecimal(($res[0]->price/100)*$res[0]->discount_two);
            $data['detail']['discount_three_amount'] = $this->sma->formatDecimal(($res[0]->price/100)*$res[0]->discount_three);

            $data['detail']['fed_tax'] = $this->sma->formatDecimal($res[0]->fed_tax);
            $data['detail']['product_tax'] = $this->sma->formatDecimal(0);
            if($res[0]->tax_type == 2){
                $data['detail']['product_tax'] = $this->sma->formatDecimal($res[0]->tax_rate_price);
            }
            else{
                $data['detail']['product_tax'] = $this->sma->formatDecimal(($res[0]->cost/100)*$res[0]->tax_rate_price);
            }
            $data['codestatus'] = "ok";
        }
        echo json_encode($data);
    }
    public function updatepoprice($po_id){
        $items_discount = 0;
        $items_tax = 0;
        $order_tax = 0;
        $total = 0;
        $shipping = 0;
        $grand_total = 0;
        $this->db->select('
            sma_purchase_order_tb.*,
            sma_tax_rates.id as tax_id,
            sma_tax_rates.name as tax_name,
            sma_tax_rates.rate as tax_rates,
            sma_tax_rates.type as tax_type,
        ');
        $this->db->from('sma_purchase_order_tb');
        $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_purchase_order_tb.order_tax_id', 'left');
        $this->db->where('sma_purchase_order_tb.id',$po_id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $po = $q->result();
            $this->db->select('*');
            $this->db->from('sma_purchase_order_items_tb');
            $this->db->where('purchase_id',$po_id);
            $q2 = $this->db->get();
            if($q2->num_rows() > 0){
                $rows = $q2->result();
                foreach($rows as $row){
                    $items_discount += $row->total_discount*$row->qty;
                    $items_tax += $row->tax_amount*$row->qty;
                    $total += $row->sub_total*$row->qty;
                }
            }
            if($po[0]->tax_type == 2){
                $order_tax = $po[0]->tax_rates;
            }
            else{
                $order_tax = ($total/100)*$po[0]->tax_rates;
            }
            $shipping = $po[0]->shipping;
            $grand_total = $total+$order_tax+$shipping-$po[0]->order_discount;
            $setdata['items_discount'] = $items_discount;
            $setdata['items_tax'] = $items_tax;
            $setdata['order_tax'] = $order_tax;
            $setdata['total'] = $total;
            $setdata['shipping'] = $shipping;
            $setdata['grand_total'] = $grand_total;
            $this->db->set($setdata);
            $this->db->where('id',$po_id);
            $this->db->update('sma_purchase_order_tb');
        }
    }
    public function itemdelete(){
        $poi_id = $this->input->get('id');
        $poid = $this->input->get('poid');
        $sendvalue['codestatus'] = 'no';
        $this->db->select('*');
        $this->db->from('sma_po_received_item_tb');
        $this->db->where('po_item_id',$poi_id);
        $q = $this->db->get();
        if($q->num_rows() == 0){
            $res = $q->result();
            $this->db->delete('sma_purchase_order_items_tb', array('id' => $poi_id));
            $this->updatepoprice($poid);
            $sendvalue['codestatus'] = 'ok';
            // $sendvalue['codestatus'] = $poid;
        }
        else{
            $sendvalue['codestatus'] = 'Firstly delete this item receving';
        }
        echo json_encode($sendvalue);
    }
    public function itemdetail(){
        $sendvalue['codestatus'] = "no";
        $id = $this->input->get('id');
        $this->db->select('
            sma_purchase_order_items_tb.*,
            (
                SELECT COALESCE(SUM(sma_po_received_item_tb.received_qty),0) 
                FROM sma_po_received_item_tb 
                WHERE sma_po_received_item_tb.po_item_id=sma_purchase_order_items_tb.id
            ) AS count_receving,
            sma_products.discount_one,
            sma_products.discount_two,
            sma_products.discount_three,
            sma_products.name as product_name,
            sma_products.price as product_price,
        ');
        $this->db->from('sma_purchase_order_items_tb');
        $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
        $this->db->where('sma_purchase_order_items_tb.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $item = $q->result();
            if($item[0]->qty == $item[0]->count_receving){
                $sendvalue['codestatus'] = "You cannot edit this item because this full received";
            }
            else{
                // echo '<pre>';
                // print_r($item[0]);
                // exit();
                $sendvalue['detail']['id'] = $item[0]->id;
                $sendvalue['detail']['qty'] = $item[0]->qty;

                if($item[0]->sales_incentive_discount == "0" || $item[0]->sales_incentive_discount == "0.0000"){
                    $sendvalue['detail']['pd1'] = 'no';
                }
                else{
                    $sendvalue['detail']['pd1'] = 'yes';
                }
                if($item[0]->trade_discount == "0" || $item[0]->trade_discount == "0.0000"){
                    $sendvalue['detail']['pd2'] = 'no';
                }
                else{
                    $sendvalue['detail']['pd2'] = 'yes';
                }
                if($item[0]->consumer_discount == "0" || $item[0]->consumer_discount == "0.0000"){
                    $sendvalue['detail']['pd3'] = 'no';
                }
                else{
                    $sendvalue['detail']['pd3'] = 'yes';
                }
                $sendvalue['detail']['sales_incentive_discount'] = $this->sma->formatDecimal($item[0]->discount_one);
                $sendvalue['detail']['trade_discount'] = $this->sma->formatDecimal($item[0]->discount_two);
                $sendvalue['detail']['consumer_discount'] = $this->sma->formatDecimal($item[0]->discount_three);


                $sendvalue['detail']['sales_incentive_discount_amount'] = $this->sma->formatDecimal(($item[0]->product_price/100)*$item[0]->discount_one);
                $sendvalue['detail']['trade_discount_amount'] = $this->sma->formatDecimal(($item[0]->product_price/100)*$item[0]->discount_two);
                $sendvalue['detail']['consumer_discount_amount'] = $this->sma->formatDecimal(($item[0]->product_price/100)*$item[0]->discount_three);

                // $sendvalue['detail']['trade_discount_amount'] = $this->sma->formatDecimal($item[0]->trade_discount_amount);
                // $sendvalue['detail']['sales_incentive_discount_amount'] = $this->sma->formatDecimal($item[0]->sales_incentive_discount_amount);
                // $sendvalue['detail']['consumer_discount_amount'] = $this->sma->formatDecimal($item[0]->consumer_discount_amount);
                // print_r($sendvalue);
                $sendvalue['codestatus'] = "ok";
            }
        }
        else{
            $sendvalue['codestatus'] = "Item not found";
        }
        echo json_encode($sendvalue);
    }
    public function updateitem(){  
        $sendvalue['codestatus'] = "no";
         $id = $this->input->post('id');
         $qty = $this->input->post('qty');
         $done_txt = $this->input->post('done_txt');
         $dtwo_txt = $this->input->post('dtwo_txt');
         $dth_txt = $this->input->post('dth_txt');
         $done_chk = 0;
         $dtwo_chk = 0;
         $dth_chk = 0;
         if($this->input->post('done_chk')){$done_chk = $this->input->post('done_chk');}
         if($this->input->post('dtwo_chk')){$dtwo_chk = $this->input->post('dtwo_chk');}
         if($this->input->post('dth_chk')){$dth_chk = $this->input->post('dth_chk');}
         $td = $this->input->post('td');
        $this->db->select('
            sma_purchase_order_items_tb.*,
            (
                SELECT COALESCE(SUM(sma_po_received_item_tb.received_qty),0) 
                FROM sma_po_received_item_tb 
                WHERE sma_po_received_item_tb.po_item_id=sma_purchase_order_items_tb.id
            ) AS count_receving,
            sma_products.discount_one,
            sma_products.discount_two,
            sma_products.discount_three,
            sma_products.name as product_name,
            sma_products.price as product_price,
        ');
        $this->db->from('sma_purchase_order_items_tb');
        $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
        $this->db->where('sma_purchase_order_items_tb.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $item = $q->result();

            if($qty >= $item[0]->qty_received){

                $setdata['qty'] = $qty;
                $setdata['qty_balance'] = $qty;


                if($this->input->post('done_chk')){
                    $setdata['sales_incentive_discount_amount'] = $this->sma->formatDecimal(($item[0]->consignment_price/100)*$item[0]->discount_one);
                    $setdata['sales_incentive_discount'] = $item[0]->discount_one;
                }
                else{
                    $setdata['sales_incentive_discount_amount'] = 0;
                    $setdata['sales_incentive_discount'] = 0;
                }
                if($this->input->post('dtwo_chk')){
                    $setdata['trade_discount_amount'] = $this->sma->formatDecimal(($item[0]->consignment_price/100)*$item[0]->discount_two);
                    $setdata['trade_discount'] = $item[0]->discount_two;
                }
                else{
                    $setdata['trade_discount_amount'] = 0;
                    $setdata['trade_discount'] = 0;
                }
                if($this->input->post('dth_chk')){
                    $setdata['consumer_discount_amount'] = $this->sma->formatDecimal(($item[0]->consignment_price/100)*$item[0]->discount_three);
                    $setdata['consumer_discount'] = $item[0]->discount_three;
                }
                else{
                    $setdata['consumer_discount_amount'] = 0;
                    $setdata['consumer_discount'] = 0;
                }
                $setdata['total_discount'] = $setdata['sales_incentive_discount_amount']+$setdata['trade_discount_amount']+$setdata['consumer_discount_amount'];
                // echo $item[0]->purchase_id;

                $this->db->set($setdata);
                $this->db->where('id',$id);
                $this->db->update('sma_purchase_order_items_tb');
                $this->updatepoprice($item[0]->purchase_id);
                $sendvalue['codestatus'] = "ok";
            }
            else{
                $alertqty = $item[0]->qty-$item[0]->qty_received;
                $sendvalue['codestatus'] = "You can only decrease ".$alertqty." Qty";
            }
        }
        else{
            $sendvalue['codestatus'] = "Item not found";
        }
        echo json_encode($sendvalue);
    }
    public function alertqty(){
        $warehouse_id = $this->input->get('warehouse_id', true);
        $supplier_id = $this->input->get('supplier_id', true);
        $own_company = $this->input->get('own_company', true);
        if($warehouse_id != "" && $supplier_id != ""){
            $rows = $this->purchaseorder_model->product_detail2($warehouse_id, $supplier_id);
            $fed_tax_price = $this->site->fed_tax($q);
            $gst_tax_price = $this->site->gst_tax($q);
            $further_tax_price = $this->site->further_tax($q);
            $own_company_details = $this->site->getown_companiesByID($own_company);
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
                    $row->own_companies_check_strn = $own_company_details->strn;
                    $row->show_further_tax = $further_tax_price->further_tax;
                    unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                    $units = $this->site->getUnitsByBUID($row->base_unit);
                    $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                    $pr[] = array(
                        'id' => sha1($c . $r), 
                        'item_id' => $row->id, 
                        'label' => $row->name . " (" . $row->code . ")",
                        'row' => $row, 
                        'tax_rate' => $tax_rate, 
                        'units' => $units, 
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
    public function bulkuploaditem(){
        $sendvalue['codestatus'] = 'no';
        $sendvalue['products'] = array();
        $warehouse_id = $this->input->post('warehouse');
        $supplier_id = $this->input->post('suppliers');
        if($warehouse_id != ""){
            if($supplier_id != ""){
                if (isset($_FILES["itemsfile"]) && $_FILES["itemsfile"] != "") {
                    if ($_FILES["itemsfile"]['type'] == 'text/csv') {
                        $this->load->library('upload');
                        // print_r($_FILES["itemsfile"]);
                        $config['upload_path'] = 'files/';
                        $config['allowed_types'] = 'csv';
                        $config['overwrite'] = true;
                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('itemsfile')) {
                            $csv = $this->upload->file_name;
                            $arrResult = array();
                            $handle = fopen('files/'. $csv, "r");
                            if ($handle) {
                                while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                                    $arrResult[] = $row;
                                }
                                fclose($handle);
                            }
                            $titles = array_shift($arrResult);
                            $keys = array('product_id', 'qty');
                            $finals = array();
                            foreach ($arrResult as $key => $value) {
                                $finals[] = array_combine($keys, $value);
                            }
                            foreach($finals as $final){
                                $rows = $this->purchaseorder_model->product_detail3($final['product_id'], $warehouse_id, $supplier_id);
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
                                        $row->real_unit_cost = $row->cost;
                                        $row->base_quantity = $final['qty'];
                                        $row->base_unit = $row->unit;
                                        $row->base_unit_cost = $row->cost;
                                        $row->unit = $row->purchase_unit ? $row->purchase_unit : $row->unit;
                                        $row->new_entry = $final['qty'];
                                        $row->expiry = '';
                                        $row->qty = $final['qty'];
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
                                        $row->own_companies_check_strn = $own_company_details->strn;
                                        $row->show_further_tax = $further_tax_price->further_tax;
                                        unset($row->details, $row->product_details, $row->price, $row->file, $row->supplier1price, $row->supplier2price, $row->supplier3price, $row->supplier4price, $row->supplier5price, $row->supplier1_part_no, $row->supplier2_part_no, $row->supplier3_part_no, $row->supplier4_part_no, $row->supplier5_part_no);
                                        $units = $this->site->getUnitsByBUID($row->base_unit);
                                        $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                                        $sendvalue['products'][] = array(
                                            'id' => sha1($c . $r), 
                                            'item_id' => $row->id, 
                                            'label' => $row->name . " (" . $row->code . ")",
                                            'row' => $row, 
                                            'tax_rate' => $tax_rate, 
                                            'units' => $units, 
                                            'options' => 0
                                        );
                                        $r++;
                                    }
                                }
                                else{
                                    $sendvalue['errors']['prducts'][] = $final['product_id'];
                                }
                            }
                            $sendvalue['codestatus'] = 'ok';
                        }
                        else{
                            $sendvalue['codestatus'] = 'Upoading Faild '.$this->upload->display_errors();
                        }
                    }
                    else{
                        $sendvalue['codestatus'] = 'Upload only CSV Excel File.';
                    }
                }
                else{
                    $sendvalue['codestatus'] = 'Please Select File.';
                }
            }
            else{
                $sendvalue['codestatus'] = 'Please Select Supplier.';
            }
        }
        else{
            $sendvalue['codestatus'] = 'Please Select Warehouse.';
        }
        echo json_encode($sendvalue);
    }
    public function deactivateproduct(){
        $sendvalue['codestatus'] = "ok";
        $pid = $this->input->get('pid');
        $this->db->set('status',0);
        $this->db->where('id',$pid);
        $this->db->update('sma_products');
        echo json_encode($sendvalue);
    }
    public function createdPurchase(){
        // echo '<pre>';
        $did = $this->input->get('did');
        $this->data['po_re'] = json_encode($did);
        $lists = array();
        if($did != ""){
            $lists = $did;
        }
        if(count($lists)>0){
            $finaldata =  array();
            $finaldata['po_ris'] = array();
            $finaldata['p']['total'] = 0;
            $finaldata['p']['total_item'] = 0;
            $finaldata['p']['product_discount'] = 0;
            $finaldata['p']['date'] = date('Y-m-d H:i:s');
            $finaldata['p']['order_discount'] = 0;
            $finaldata['p']['total_discount'] = 0;
            $finaldata['p']['order_tax_id'] = "";
            $finaldata['p']['order_tax'] = 0;
            $finaldata['p']['product_tax'] = 0;
            $finaldata['p']['total_tax'] = 0;

            $finaldata['p']['grand_total'] = 0;

            $this->db->select('
                sma_po_received_item_tb.id as pori_id,
                sma_po_received_item_tb.receiving_id as por_id,
                sma_po_received_item_tb.received_qty as por_r_qty,
                sma_po_received_item_tb.batch_code as por_batch,
                sma_po_received_item_tb.expiry_date as por_expiry,
                sma_purchase_order_items_tb.*,
                sma_companies.name as supplier_name,
                sma_products.code as product_code,
                sma_products.name as product_name,
                sma_products.adv_tax_for_purchase as adv_tax_for_purchase,
                sma_purchase_order_tb.reference_no as po_reference_no,
                sma_purchase_order_tb.supplier_id as po_supplier_id,
                sma_purchase_order_tb.warehouse_id as po_warehouse_id,
                sma_purchase_order_tb.note as po_note,
                sma_purchase_order_tb.own_company as po_own_company,
                sma_purchase_order_tb.payemnt_terms as po_payemnt_terms,
                sma_purchase_order_tb.payment_status as po_payment_status,
                sma_purchase_order_tb.status as po_status,
                sma_purchase_order_tb.paid_amount as po_paid_amount,
                sma_purchase_order_tb.shipping as po_shipping,
                sma_purchase_order_tb.order_tax_id as  po_order_tax_id,
                sma_tax_rates.rate as  tax_rate,
                sma_tax_rates.type as  tax_type,
            ');
            $this->db->from('sma_po_received_item_tb');
            $this->db->join('sma_purchase_order_items_tb', 'sma_purchase_order_items_tb.id = sma_po_received_item_tb.po_item_id', 'left');
            $this->db->join('sma_purchase_order_tb', 'sma_purchase_order_tb.id = sma_purchase_order_items_tb.purchase_id', 'left');
            $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
            $this->db->join('sma_companies', 'sma_companies.id = sma_purchase_order_tb.supplier_id', 'left');
            $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_purchase_order_items_tb.tax_id', 'left');

            foreach($lists as $list){
                $this->db->or_where('receiving_id',$list);
            }
            $q = $this->db->get();
            $rows = $q->result();

            $no = 0;
            foreach ($rows as $row) {

                $finaldata['p']['reference_no'] = $row->po_reference_no;
                $finaldata['p']['supplier_id'] = $row->po_supplier_id;
                $finaldata['p']['warehouse_id'] = $row->po_warehouse_id;
                $finaldata['p']['own_company'] = $row->po_own_company;
                $finaldata['p']['note'] = $row->po_note;
                $finaldata['p']['payment_status'] = $row->po_payment_status;
                $finaldata['p']['payment_term'] = $row->po_payemnt_terms;
                $finaldata['p']['status'] = $row->po_status;
                $finaldata['p']['supplier'] = $row->supplier_name;
                $finaldata['p']['surcharge'] = "0.000";
                $finaldata['p']['paid'] = $row->po_paid_amount;
                $finaldata['p']['created_by'] = $this->session->userdata('user_id');
                $finaldata['p']['shipping'] = $row->po_shipping;

                $finaldata['pts'][$no]['po_ri_id'] = $row->pori_id;
                $finaldata['pts'][$no]['product_id'] = $row->product_id;
                $finaldata['po_ris'][$no]['po_ri_id'] = $row->pori_id;
                $finaldata['po_ris'][$no]['po_r_id'] = $row->por_id;
                $finaldata['pts'][$no]['product_code'] = $row->product_code;
                $finaldata['pts'][$no]['product_name'] = $row->product_name;
                $finaldata['pts'][$no]['net_unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['price'] = $row->consignment_price;
                $finaldata['pts'][$no]['dropship'] = $row->dropship_price;
                $finaldata['pts'][$no]['crossdock'] = $row->cross_dock_price;
                $finaldata['pts'][$no]['mrp'] = $row->mrp;
                $finaldata['pts'][$no]['quantity'] = $row->por_r_qty;
                $finaldata['p']['total_item'] += $row->por_r_qty;
                $finaldata['pts'][$no]['warehouse_id'] = $row->po_warehouse_id;
                $finaldata['pts'][$no]['tax_rate'] = $row->tax_rate;
                $finaldata['pts'][$no]['item_tax'] = $row->tax_amount;
                $finaldata['pts'][$no]['tax_rate_id'] = $row->tax_id;
                $finaldata['pts'][$no]['tax'] = $row->tax;
                $finaldata['pts'][$no]['discount'] = ($row->sales_incentive_discount+$row->trade_discount_amount+$row->consumer_discount_amount)*$row->por_r_qty;
                $finaldata['pts'][$no]['item_discount'] = $finaldata['pts'][$no]['discount'];
                $finaldata['pts'][$no]['expiry'] = date_format(date_create($row->por_expiry),"d/m/Y");
                $finaldata['pts'][$no]['batch'] = $row->por_batch;                  ;
                $finaldata['pts'][$no]['quantity_balance'] = $row->por_r_qty;
                $finaldata['pts'][$no]['date'] = date('Y-m-d');
                $finaldata['pts'][$no]['status'] = 'received';
                $finaldata['pts'][$no]['unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['real_unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['quantity_received'] = $row->por_r_qty;
                $finaldata['pts'][$no]['supplier_part_no'] = $row->supplier_part_no;
                $finaldata['pts'][$no]['product_unit_code'] = $row->unit;
                $finaldata['pts'][$no]['unit_quantity'] = $row->por_r_qty;
                $finaldata['pts'][$no]['discount_one'] = $row->sales_incentive_discount;
                $finaldata['pts'][$no]['discount_two'] = $row->trade_discount;
                $finaldata['pts'][$no]['discount_three'] = $row->consumer_discount;
                $finaldata['pts'][$no]['fed_tax'] = $row->fed_tax;
                $finaldata['pts'][$no]['further_tax'] = '0.0000';
                $finaldata['pts'][$no]['adv_tax'] = ($row->purchase_price+$row->tax_rate)/100*$row->adv_tax_for_purchase;
                $finaldata['pts'][$no]['subtotal'] = ($row->purchase_price*$row->por_r_qty)+(($row->tax_amount+$row->fed_tax+$finaldata['pts'][$no]['adv_tax'])*$row->por_r_qty)-$finaldata['pts'][$no]['discount'];

                $finaldata['p']['total'] = $finaldata['p']['total']+($finaldata['pts'][$no]['net_unit_cost']*$finaldata['pts'][$no]['quantity']);
                $finaldata['p']['product_discount'] = $finaldata['p']['product_discount']+$finaldata['pts'][$no]['discount'];
                $finaldata['p']['grand_total'] = $finaldata['p']['grand_total']+$finaldata['pts'][$no]['subtotal'];
                $finaldata['p']['order_discount'] = $finaldata['p']['product_discount'];
                $finaldata['p']['order_tax_id'] = $row->po_order_tax_id;
                $finaldata['p']['product_tax'] = $finaldata['p']['product_tax']+$finaldata['pts'][$no]['item_tax'];

                // if($row->tax_type == 2){
                //     $finaldata['p']['order_tax'] = $finaldata['p']['total']+$row->tax_rate;
                // }
                // else{
                //     $finaldata['p']['order_tax'] = ($finaldata['p']['total']/100)*$row->tax_rate;
                // } 
                $no++;
            }
            $finaldata['p']['order_tax'] = 0;
            $finaldata['p']['total_tax'] = $finaldata['p']['order_tax']+$finaldata['p']['product_tax'];
            // print_r($rows);
            $this->data['finaldata'] = $finaldata;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('purchase'), 'page' => lang('purchase')), array('link' => '#', 'page' => 'Create Purchase'));
            $meta = array('page_title' => 'Create Purchase', 'bc' => $bc);
            $this->page_construct2('purchase_order/create', $meta, $this->data);
        }
        else{
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function created2(){
        $sendvalue['codestatus'] = 'no';
        $did = json_decode($this->input->post('recid'));
        $lists = array();
        if($did != ""){
            $lists = $did;
        }
        if(count($lists)>0){
            
            $finaldata =  array();
            $finaldata['po_ris'] = array();
            $finaldata['p']['total'] = 0;
            $finaldata['p']['product_discount'] = 0;
            $finaldata['p']['order_discount'] = 0;
            $finaldata['p']['total_discount'] = 0;
            $finaldata['p']['order_tax_id'] = "";
            $finaldata['p']['order_tax'] = 0;
            $finaldata['p']['product_tax'] = 0;
            $finaldata['p']['total_adv_tax'] = 0;
            $finaldata['p']['total_tax'] = 0;
            $finaldata['p']['date'] = date('Y-m-d H:i:s');
            $finaldata['p']['grand_total'] = 0;

            $this->db->select('
                sma_po_received_item_tb.id as pori_id,
                sma_po_received_item_tb.receiving_id as por_id,
                sma_po_received_item_tb.received_qty as por_r_qty,
                sma_po_received_item_tb.batch_code as por_batch,
                sma_po_received_item_tb.expiry_date as por_expiry,
                sma_purchase_order_items_tb.*,
                sma_companies.name as supplier_name,
                sma_products.code as product_code,
                sma_products.name as product_name,
                sma_products.adv_tax_for_purchase as adv_tax_for_purchase,
                sma_purchase_order_tb.reference_no as po_reference_no,
                sma_purchase_order_tb.supplier_id as po_supplier_id,
                sma_purchase_order_tb.warehouse_id as po_warehouse_id,
                sma_purchase_order_tb.note as po_note,
                sma_purchase_order_tb.own_company as po_own_company,
                sma_purchase_order_tb.payemnt_terms as po_payemnt_terms,
                sma_purchase_order_tb.payment_status as po_payment_status,
                sma_purchase_order_tb.status as po_status,
                sma_purchase_order_tb.paid_amount as po_paid_amount,
                sma_purchase_order_tb.shipping as po_shipping,
                sma_purchase_order_tb.order_tax_id as  po_order_tax_id,
                sma_tax_rates.rate as  tax_rate,
                sma_tax_rates.type as  tax_type,
            ');
            $this->db->from('sma_po_received_item_tb');
            $this->db->join('sma_purchase_order_items_tb', 'sma_purchase_order_items_tb.id = sma_po_received_item_tb.po_item_id', 'left');
            $this->db->join('sma_purchase_order_tb', 'sma_purchase_order_tb.id = sma_purchase_order_items_tb.purchase_id', 'left');
            $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
            $this->db->join('sma_companies', 'sma_companies.id = sma_purchase_order_tb.supplier_id', 'left');
            $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_purchase_order_items_tb.tax_id', 'left');

            foreach($lists as $list){
                $this->db->or_where('receiving_id',$list);
            }
            $q = $this->db->get();
            $rows = $q->result();

            $no = 0;
            foreach ($rows as $row) {

                $finaldata['p']['reference_no'] = $this->input->post('reference_no');
                $finaldata['p']['supplier_id'] = $row->po_supplier_id;
                $finaldata['p']['warehouse_id'] = $row->po_warehouse_id;
                $finaldata['p']['own_company'] = $row->po_own_company;
                $finaldata['p']['note'] = $row->po_note;
                $finaldata['p']['payment_status'] = $row->po_payment_status;
                $finaldata['p']['payment_term'] = $row->po_payemnt_terms;
                $finaldata['p']['status'] = $row->po_status;
                $finaldata['p']['supplier'] = $row->supplier_name;
                $finaldata['p']['surcharge'] = "0.000";
                $finaldata['p']['paid'] = $row->po_paid_amount;
                $finaldata['p']['created_by'] = $this->session->userdata('user_id');
                $finaldata['p']['shipping'] = $row->po_shipping;

                $finaldata['pts'][$no]['po_ri_id'] = $row->pori_id;
                $finaldata['pts'][$no]['product_id'] = $row->product_id;
                $finaldata['po_ris'][$no]['po_ri_id'] = $row->pori_id;
                $finaldata['po_ris'][$no]['po_r_id'] = $row->por_id;
                $finaldata['pts'][$no]['product_code'] = $row->product_code;
                $finaldata['pts'][$no]['product_name'] = $row->product_name;
                $finaldata['pts'][$no]['net_unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['price'] = $row->consignment_price;
                $finaldata['pts'][$no]['dropship'] = $row->dropship_price;
                $finaldata['pts'][$no]['crossdock'] = $row->cross_dock_price;
                $finaldata['pts'][$no]['mrp'] = $row->mrp;
                $finaldata['pts'][$no]['quantity'] = $row->por_r_qty;
                $finaldata['pts'][$no]['warehouse_id'] = $row->po_warehouse_id;
                $finaldata['pts'][$no]['item_tax'] = $row->tax_amount*$row->por_r_qty;
                $finaldata['pts'][$no]['tax_rate_id'] = $row->tax_id;
                $finaldata['pts'][$no]['tax'] = $row->tax;
                $finaldata['pts'][$no]['discount'] = ($row->sales_incentive_discount+$row->trade_discount_amount+$row->consumer_discount_amount)*$row->por_r_qty;
                $finaldata['pts'][$no]['item_discount'] = $finaldata['pts'][$no]['discount'];
                $finaldata['pts'][$no]['expiry'] = date_format(date_create($row->por_expiry),"d/m/Y");
                $finaldata['pts'][$no]['batch'] = $row->por_batch;                  
                $finaldata['pts'][$no]['quantity_balance'] = $row->por_r_qty;
                $finaldata['pts'][$no]['date'] = date('Y-m-d');
                $finaldata['pts'][$no]['status'] = 'received';
                $finaldata['pts'][$no]['unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['real_unit_cost'] = $row->purchase_price;
                $finaldata['pts'][$no]['quantity_received'] = $row->por_r_qty;
                $finaldata['pts'][$no]['supplier_part_no'] = $row->supplier_part_no;
                $finaldata['pts'][$no]['product_unit_code'] = $row->unit;
                $finaldata['pts'][$no]['unit_quantity'] = $row->por_r_qty;
                $finaldata['pts'][$no]['discount_one'] = $row->sales_incentive_discount;
                $finaldata['pts'][$no]['discount_two'] = $row->trade_discount;
                $finaldata['pts'][$no]['discount_three'] = $row->consumer_discount;
                $finaldata['pts'][$no]['fed_tax'] = $row->fed_tax;
                $finaldata['pts'][$no]['adv_tax'] = (($row->purchase_price+$row->tax_rate)*$row->por_r_qty)/100*$row->adv_tax_for_purchase;
                $finaldata['pts'][$no]['subtotal'] = (($row->purchase_price*$row->por_r_qty)+(($row->tax_amount+$row->fed_tax)*$row->por_r_qty)-$finaldata['pts'][$no]['discount'])+$finaldata['pts'][$no]['adv_tax'];

                $finaldata['p']['total'] = $finaldata['p']['total']+($finaldata['pts'][$no]['net_unit_cost']*$finaldata['pts'][$no]['quantity']);
                $finaldata['p']['total_adv_tax'] = $finaldata['p']['total_adv_tax']+$finaldata['pts'][$no]['adv_tax'];
                $finaldata['p']['product_discount'] = $finaldata['p']['product_discount']+$finaldata['pts'][$no]['discount'];
                $finaldata['p']['grand_total'] = $finaldata['p']['grand_total']+$finaldata['pts'][$no]['subtotal'];
                $finaldata['p']['order_discount'] = $finaldata['p']['product_discount'];
                $finaldata['p']['order_tax_id'] = $row->po_order_tax_id;
                $finaldata['p']['product_tax'] = $finaldata['p']['product_tax']+$finaldata['pts'][$no]['item_tax'];
                // if($row->tax_type == 2){
                //     $finaldata['p']['order_tax'] = $finaldata['p']['total']+$row->tax_rate;
                // }
                // else{
                //     $finaldata['p']['order_tax'] = ($finaldata['p']['total']/100)*$row->tax_rate;
                // } 
                $no++;
            }
            $finaldata['p']['order_tax'] = 0;
            $finaldata['p']['total_tax'] = $finaldata['p']['order_tax']+$finaldata['p']['product_tax'];
            $this->db->insert('sma_purchases', $finaldata['p']);
            $p_id = $this->db->insert_id();
            $sendvalue['p_id'] = $p_id;

            $no2 = 0;
            foreach($finaldata['pts'] as $item){

                $insertdata = $item;
                unset($insertdata["po_ri_id"]);
                $insertdata['purchase_id'] = $p_id;
                $this->db->insert('sma_purchase_items', $insertdata);
                $this->updatewarehouseqty($item['product_id'],$item['warehouse_id'],$item['quantity_received'],$item['net_unit_cost']);
                $this->updateproductqty($item['product_id'],$item['quantity_received']);
                $this->load->admin_model('stores_model');
                $sendvalue['apistatus'] = $this->stores_model->updateStoreQty($item['product_id'],$item['warehouse_id'],0,'Create Purchase By PO');
                $no2++;
            }
            foreach($finaldata['po_ris'] as $row2){
                $setdata['purchase_id'] = $p_id;
                $setdata['purchase_create'] = 'yes';
                $this->db->set($setdata);
                $this->db->where('id', $row2['po_r_id']);
                $this->db->update('sma_po_received_tb');

                $this->db->set($setdata);
                $this->db->where('id', $row2['po_ri_id']);
                $this->db->update('sma_po_received_item_tb');
            }
            $sendvalue['codestatus'] = 'ok';
            $sendvalue['message'] = 'All Purchases Create Successfully';
            echo '<script>alert("'.$sendvalue['message'].'")</script>';
            redirect(admin_url('purchases/view/'.$p_id));
        }
        else{
            $sendvalue['message'] = 'Select delivery';
            echo '<script>alert("'.$sendvalue['message'].'")</script>';
            redirect(admin_url('purchaseorder'));
        }
        // echo json_encode($sendvalue);
    }
}