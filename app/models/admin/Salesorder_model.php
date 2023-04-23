<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD

class Salesorder_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }
    public function product_detail($term, $warehouse_id, $supplier_id, $limit = 15){
        $data = array();
        if($warehouse_id == ""){

        }
        else{
            $this->db->select('
                sma_products.*,
                0 as warehouse_qty,
                '. $supplier_id . ' as selected_supplier,
                '. $warehouse_id . ' as selected_warehouse,
                (
                    SELECT
                        SUM(sma_purchase_items.quantity_balance)
                    FROM 
                        sma_purchase_items 
                    WHERE 
                        sma_purchase_items.product_id = sma_products.id AND
                        sma_purchase_items.warehouse_id = '.$warehouse_id.'

                ) AS qbalance
            ');
            $this->db->from('sma_products');
            $this->db->where("
                sma_products.type = 'standard' AND 
                (
                    `sma_products`.`supplier1` = '". $supplier_id . "' OR 
                    `sma_products`.`supplier2` = '". $supplier_id . "' OR 
                    `sma_products`.`supplier3` = '". $supplier_id . "' OR 
                    `sma_products`.`supplier4` = '". $supplier_id . "' OR 
                    `sma_products`.`supplier5` = '". $supplier_id . "'
                ) AND
                (`sma_products`.`status` = 1) AND 
                (
                    name LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                    code LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                    supplier1_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                    supplier2_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                    supplier3_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                    supplier4_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                    supplier5_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR  
                    concat(name, ' (', code, ')') LIKE '%" . $this->db->escape_like_str($term) . "%'
                )
            ");
            $this->db->limit($limit);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                return $data;
            }
        }
        return $data;
    }
    public function add_so($sodata, $items){
        $sendvalue['status'] = false;
        $sendvalue['so_id'] = 0;
        $sendvalue['message'] = 'no';

        $this->db->select('id');
        $this->db->from('sma_sales_orders_tb');
        $this->db->where('ref_no',$sodata['ref_no']);
        $q = $this->db->get();
        if ($q->num_rows() == 0) {
            $this->db->insert('sma_sales_orders_tb', $sodata);
            $so_id = $this->db->insert_id();
            foreach($items as $item){
                $item['so_id'] = $so_id;
                if($this->db->insert('sma_sales_order_items', $item)){
                    if(isset($item['product_id']) && isset($item['warehouse_id'])){
                        $this->load->model('admin/stores_model');
                        $this->stores_model->updateStoreQty($item['product_id'],$item['warehouse_id'],0,"Add SO");
                    }
                }
            }
            $sendvalue['so_id'] = $so_id;
            $sendvalue['message'] = 'Sale Order Create Successfully';
            $sendvalue['status'] = true;
        }
        else{
            $sendvalue['message'] = 'Reference number already available.';
        }
        return $sendvalue;
    }
    public function data($req = array()){
        $data = array();
        $this->db->select('
            sma_sales_orders_tb.id,
            sma_sales_orders_tb.date,
            sma_sales_orders_tb.ref_no,
            sma_sales_orders_tb.warehouse_id,
            sma_sales_orders_tb.po_number,
            sma_sales_orders_tb.delivery_date,
            sma_sales_orders_tb.created_at,
            sma_sales_orders_tb.accounts_team_status,
            sma_sales_orders_tb.operation_team_stauts,
            sma_sales_orders_tb.status,
            supplier_detail.name as supplier_name,
            customer_detail.name as customer_name,
            sma_warehouses.name as warehouse_name,
            sma_warehouses.code as warehouse_code,
            sma_users.first_name as first_name,
            sma_users.last_name as last_name,
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
        ');
        $this->db->from('sma_sales_orders_tb');
        $this->db->join('sma_companies as customer_detail', 'customer_detail.id = sma_sales_orders_tb.customer_id', 'left');
        $this->db->join('sma_companies as supplier_detail', 'supplier_detail.id = sma_sales_orders_tb.supplier_id', 'left');
        $this->db->join('sma_warehouses', 'sma_warehouses.id = sma_sales_orders_tb.warehouse_id', 'left');
        $this->db->join('sma_users', 'sma_users.id = sma_sales_orders_tb.created_by', 'left');
        // if($this->session->userdata('group_id') == 10){
            $user_warehouse_id = $this->session->userdata('warehouse_id');
            if($user_warehouse_id != 0 && $user_warehouse_id != ''){
                $this->db->where('sma_sales_orders_tb.warehouse_id',$user_warehouse_id);
            }
        // }
        if($req['otstatus'] != "" && $req['otstatus'] != "all"){
            $this->db->where('sma_sales_orders_tb.operation_team_stauts',$req['otstatus']);
        }
        if($req['atstatus'] != "" && $req['atstatus'] != "all"){
            $this->db->where('sma_sales_orders_tb.accounts_team_status',$req['atstatus']);
        }
        if($req['status'] != "" && $req['status'] != "all"){
            $this->db->where('sma_sales_orders_tb.status',$req['status']);
        }
        $this->db->order_by("id", "desc");
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->complete_qty == ""){$row->complete_qty = 0;}
                if($row->total_qty == ""){$row->total_qty = 0;}
                $row->persentages = (int)$this->sma->formatDecimal(($row->complete_qty/$row->total_qty)*100);
                $data[] = $row;
            }
        }
        return $data;
        
    }
    public function details($id){
        $data = array();
        $data['codestatus'] = "no";
        if($id != ""){
            // Get Purchase Order Data
            $this->db->select('*');
            $this->db->from('sma_sales_orders_tb');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $result = $q->result();
                $data['details'] = $result[0];
                // Get supplier details
                $this->db->select('*');
                $this->db->from('sma_companies');
                $this->db->where('id',$result[0]->supplier_id);
                $q2 = $this->db->get();
                if ($q2->num_rows() > 0) {
                    $supplier = json_decode(json_encode($q2->result()));
                    $data['details']->supplier = $supplier[0];
                }
                // Get Customer Address
                $this->db->select('*');
                $this->db->from('sma_addresses');
                $this->db->where('company_id',$result[0]->customer_id);
                $addresslistq = $this->db->get();
                $data['details']->customeraddress = $addresslistq->result();
                // Get Customer details
                $this->db->select('*');
                $this->db->from('sma_companies');
                $this->db->where('id',$result[0]->customer_id);
                $q2 = $this->db->get();
                if ($q2->num_rows() > 0) {
                    $customer = json_decode(json_encode($q2->result()));
                    $data['details']->customer = $customer[0];
                    // Get Customer Address
                    $data['details']->deliveryaddress = $data['details']->customer->address;
                    if($data['details']->customer_address_id != 0){
                        $this->db->select('*');
                        $this->db->from('sma_addresses');
                        $this->db->where('id',$result[0]->customer_address_id);
                        $addresq = $this->db->get();
                        if ($addresq->num_rows() > 0) {
                            $addres = json_decode(json_encode($addresq->result()));
                            $customeraddress = $addres[0];
                            $data['details']->deliveryaddress = $customeraddress->line1.'<br>'.$customeraddress->line2;
                            
                        }
                    }
                }
                // Get warehosue details
                $this->db->select('*');
                $this->db->from('sma_warehouses');
                $this->db->where('id',$result[0]->warehouse_id);
                $q3 = $this->db->get();
                if ($q3->num_rows() > 0) {
                    $warehouse = json_decode(json_encode($q3->result()));
                    $data['details']->warehouse = $warehouse[0];
                }
                // Get Create User details
                $this->db->select('id,first_name,last_name');
                $this->db->from('sma_users');
                $this->db->where('id',$result[0]->created_by);
                $q5 = $this->db->get();
                if ($q5->num_rows() > 0) {
                    $create_user = json_decode(json_encode($q5->result()));
                    $data['details']->create_user = $create_user[0];
                }
                // Get Sale Order Items
                $this->db->select('
                    sma_sales_order_items.*,
                    sma_products.price as product_price,
                    sma_tax_rates.rate as product_tax_value,
                    sma_tax_rates.type as product_tax_type,
                    sma_products.id as product_id,
                    sma_products.name as product_name,
                    sma_products.code as product_code,
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
                    ) AS expected_complete_qty,
                    (
                        SELECT 
                            SUM(sma_purchase_items.quantity_balance) 
                        FROM 
                            sma_product_groups 
                        LEFT JOIN sma_products as p2 ON p2.group_id = sma_product_groups.id
                        LEFT JOIN sma_purchase_items ON sma_purchase_items.product_id = p2.id
                        WHERE sma_product_groups.id = sma_products.group_id AND sma_purchase_items.warehouse_id = '.$result[0]->warehouse_id.'
                    ) AS group_sku_expected_qty
                ');
                $this->db->from('sma_sales_order_items');
                $this->db->join('sma_products', 'sma_products.id = sma_sales_order_items.product_id', 'left');
                $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_products.tax_rate', 'left');
                $this->db->where('so_id',$result[0]->id);
                $q5 = $this->db->get();
                $data['details']->items = array();
                $no = 0;
                if ($q5->num_rows() > 0) {
                    $items = json_decode(json_encode($q5->result()));
                    foreach($items as $item){
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
                        $data['details']->items[$no] = $item;
                        $no++;
                    }
                }
                $this->db->select('
                    sma_sales_order_complete_items.*,
                    sma_products.id as product_id,
                    sma_products.name as product_name,
                    sma_products.code as product_code,
                    (
                        SELECT 
                            sma_purchase_items.expiry
                        FROM 
                            sma_purchase_items 
                        WHERE 
                            sma_purchase_items.product_id = sma_sales_order_complete_items.product_id AND
                            sma_purchase_items.batch = sma_sales_order_complete_items.batch
                        LIMIT 1
                    ) AS product_expiry

                ');
                $this->db->from('sma_sales_order_complete_items');
                $this->db->join('sma_products', 'sma_products.id = sma_sales_order_complete_items.product_id', 'left');
                $this->db->where('sma_sales_order_complete_items.so_id = '.$result[0]->id.' AND sma_sales_order_complete_items.status = "pending"');
                $q5 = $this->db->get();
                $data['details']->citems = array();
                $no = 0;
                if ($q5->num_rows() > 0) {
                    $citems = json_decode(json_encode($q5->result()));
                    foreach($citems as $item){
                        $data['details']->citems[$no] = $item;
                        $no++;
                    }
                }
                $this->db->select('
                    sma_sales_order_complete_tb.*,
                    sma_sales.reference_no,
                    sma_sales.po_number,
                    sma_sales.po_date,
                ');
                $this->db->from('sma_sales_order_complete_tb');
                $this->db->join('sma_sales', 'sma_sales.id = sma_sales_order_complete_tb.dc_id', 'left');
                $this->db->where('sma_sales_order_complete_tb.so_id = '.$result[0]->id.' AND sma_sales_order_complete_tb.status = "complete"');
                $q6 = $this->db->get();
                $data['details']->cso = json_decode(json_encode($q6->result()));

                $data['details']->invoice_id = 0;
                $this->db->select('id');
                $this->db->from('sma_sales');
                $this->db->where('so_id',$result[0]->id);
                $q6 = $this->db->get();
                if($q6->num_rows() > 0){
                    $data['details']->invoice_id = $q6->result()[0]->id;
                }

                $data['codestatus'] = "ok";
            }
            else{
                $data['codestatus'] = "Invalid Sale";
            }
        }
        else{
            $data['codestatus'] = "Something Wrong";
        }
        return $data;
    }
    public function productslist($term, $limit = 10, $supplier_id, $warehouse_id, $suown_companypplier_id){
        $this->db->select("id,name as text");
        $this->db->where("
            type = 'standard' AND 
            (
                `sma_products`.`supplier1` = '". $supplier_id . "' OR 
                `sma_products`.`supplier2` = '". $supplier_id . "' OR 
                `sma_products`.`supplier3` = '". $supplier_id . "' OR 
                `sma_products`.`supplier4` = '". $supplier_id . "' OR 
                `sma_products`.`supplier5` = '". $supplier_id . "'
            ) AND 
            (`sma_products`.`status` = 1) AND 
            (
                name LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                code LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                supplier1_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                supplier2_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                supplier3_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                supplier4_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR 
                supplier5_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR  
                concat(name, ' (', code, ')') LIKE '%" . $this->db->escape_like_str($term) . "%'
            )
        ");
        $this->db->limit($limit);
        $q = $this->db->get('products');




        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }
    public function createdata($id){
        $data = array();
        $data['codestatus'] = "no";
        if($id != ""){
            $this->db->select('
                sales_orders_tb.*,
                warehouses.name as warehouse_name,
                warehouses.code as warehouse_code,
                companies.name as customer_name,
                companies.sales_type as customer_sales_type,
                etaliers.name as etalier_name,
                CONCAT(line1," ",line2) AS customer_addres,
                sma_customer_limits.durration as payment_terms
            ');
            // CONCAT(addresses.line1,addresses.line2) as customer_addres,
            $this->db->from('sales_orders_tb');
            $this->db->join('warehouses','warehouses.id = sales_orders_tb.warehouse_id');
            $this->db->join('companies','companies.id = sales_orders_tb.customer_id');
            $this->db->join('companies as etaliers','etaliers.id = sales_orders_tb.etalier_id','left');
            $this->db->join('addresses','addresses.id = sales_orders_tb.customer_address_id','LEFT');
            $this->db->join('sma_customer_limits','sma_customer_limits.customer_id = sales_orders_tb.customer_id AND sma_customer_limits.supplier_id = sales_orders_tb.supplier_id','LEFT');
            $this->db->where('sales_orders_tb.id',$id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) { 
                $data = json_decode(json_encode($q->result()[0]),true);
                if($data['payment_terms'] != ""){
                    $data['payment_terms'] .= " Days.";
                }
                $data['items'] = array();
                $this->db->select('
                    sma_sales_order_complete_items.id,
                    sma_products.id as product_id,
                    sma_products.name as product_name,
                    sma_sales_order_complete_items.batch,
                    sma_sales_order_complete_items.quantity,
                    sma_purchase_items.net_unit_cost,
                    sma_purchase_items.price,
                    sma_purchase_items.dropship,
                    sma_purchase_items.crossdock,
                    sma_purchase_items.mrp,
                    sma_purchase_items.discount,
                    sma_purchase_items.item_discount,
                    sma_purchase_items.expiry,
                    sma_purchase_items.unit_cost,
                    sma_purchase_items.real_unit_cost,
                    sma_purchase_items.gst,
                    sma_purchase_items.cgst,
                    sma_purchase_items.sgst,
                    sma_purchase_items.igst,
                    sma_products.discount_one,
                    sma_products.discount_two,
                    sma_products.discount_three,
                    sma_products.adv_tax_reg,
                    sma_products.adv_tax_nonreg,
                    sma_purchase_items.further_tax,
                    sma_purchase_items.fed_tax,
                    sma_purchase_items.gst_tax,
                    sma_tax_rates.id as tax_rate_id,
                    sma_tax_rates.name as tax_rate_name,
                    sma_tax_rates.rate as tax_rate_rate,
                    sma_tax_rates.code as tax_rate_code,
                    sma_tax_rates.type as tax_rate_type,
                    sma_companies.gst_no as customer_gst_no,
                    sma_companies.sales_type as customer_sales_type,
                ');
                $this->db->from('sma_sales_order_complete_items');
                $this->db->join('sma_sales_orders_tb', 'sma_sales_orders_tb.id = sma_sales_order_complete_items.so_id', 'left');
                $this->db->join('sma_companies', 'sma_companies.id = sma_sales_orders_tb.customer_id', 'left');
                $this->db->join('sma_products', 'sma_products.id = sma_sales_order_complete_items.product_id', 'left');
                $this->db->join('sma_purchase_items', 'sma_purchase_items.product_id = sma_sales_order_complete_items.product_id AND sma_purchase_items.batch = sma_sales_order_complete_items.batch AND `sma_purchase_items`.`warehouse_id` = `sma_sales_order_complete_items`.`warehouse_id`', 'left');
                $this->db->join('sma_tax_rates', 'sma_tax_rates.id = sma_purchase_items.tax_rate_id', 'left');
                $this->db->where('sma_sales_order_complete_items.status = "pending" AND sma_sales_order_complete_items.so_id = '.$id);
                $this->db->group_by("sma_sales_order_complete_items.id");
                $settingfurther = $this->further_tax()->further_tax;
                $q2 = $this->db->get();
                if ($q2->num_rows() > 0) {
                    $items = json_decode(json_encode($q2->result()));
                    foreach($items as $item){
                        $tdata = $item;
                        $query = $this->db->query('
                            select
                                id,
                                discount_code as name,
                                percentage
                            from 
                                sma_bulk_discount 
                            where 
                                (CURDATE() between start_date and end_date) and 
                                (find_in_set(' . $data['supplier_id'] . ',supplier_id) OR 
                                find_in_set(' . $item->product_id . ',product_id)  <> 0)
                        ');
                        if($item->customer_sales_type == "cost"){
                            $tdata->selling_price = $item->net_unit_cost;
                        }
                        else if($item->customer_sales_type == "mrp"){
                            $tdata->selling_price = $item->mrp;
                        }
                        else{
                            $tdata->selling_price = $item->price;
                        }
                        $tdata->settingfurther = 0;
                        if($item->tax_rate_type == 1){
                            if($item->customer_gst_no == ""){
                                $tdata->settingfurther = $settingfurther;
                            }
                        }
                        $tdata->customer_type = false;
                        
                        if($tdata->selling_price == $tdata->price){
                            $tdata->customer_type = 'consignment';
                        }

                
                        $tdata->discounts = $query->result();
                        $data['items'][] = $tdata;
                    }
                }
               $data['codestatus'] = "ok";
            }
            else{
                $data['codestatus'] = "Invalid Sale";
            }
        }
        else{
            $data['codestatus'] = "Something Wrong";
        }
        return $data;
    }
    public function further_tax($q = NULL){
        $query = $this->db->query('SELECT further_tax from sma_settings');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return FALSE;
    }
    public function salecreated($sodata,$items){
        $sendvalue['sid'] = 0;
        $this->db->select('id');
        $this->db->from('sma_sales');
        $this->db->where('reference_no',$sodata['reference_no']);
        $q = $this->db->get();
        if ($q->num_rows() == 0) {
            $this->db->insert('sma_sales', $sodata);
            $sale_id = $this->db->insert_id();
            foreach($items as $item){
                $item['sale_id'] = $sale_id;
                if($this->db->insert('sma_sale_items', $item)){
                }
            }

            $setdata['status'] = 'completed';
            $this->db->set($setdata);
            $this->db->where('id',$sodata['so_id']);
            $this->db->update('sma_sales_orders_tb');

            $set2['dc_id'] = $sale_id;
            $set2['status'] = 'complete';
            $this->db->set($set2);
            $this->db->where('so_id',$sodata['so_id']);
            $this->db->where('status','pending');
            $this->db->update('sma_sales_order_complete_tb');
            $set3['status'] = 'complete';
            $this->db->set($set3);
            $this->db->where('so_id',$sodata['so_id']);
            $this->db->update('sma_sales_order_complete_items');


            $sendvalue['sid'] = $sale_id;
            $sendvalue['codestatus'] = 'Sale Create Successfully';


        }
        else{
            $sendvalue['codestatus'] = 'Reference number already available ('.$sodata['reference_no'].').';
        }
        return $sendvalue;
    }
}
