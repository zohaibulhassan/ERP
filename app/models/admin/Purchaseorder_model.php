<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD

class Purchaseorder_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }
    // New Code
    public function add_po($podata, $items){
        $sendvalue['purchase_id'] = 0;
        $sendvalue['status'] = false;
        $sendvalue['message'] = '';
        $this->db->select('id');
        $this->db->from('sma_purchase_order_tb');
        $this->db->where('reference_no',$podata['reference_no']);
        $q = $this->db->get();
        if ($q->num_rows() == 0) {
            $this->db->insert('sma_purchase_order_tb', $podata);
            $po_id = $this->db->insert_id();
            foreach($items as $item){
                $item['purchase_id'] = $po_id;
                $this->db->insert('sma_purchase_order_items_tb', $item);
            }
            $sendvalue['purchase_id'] = $po_id;
            $sendvalue['message'] = 'Purchase Order Create Successfully';
            $sendvalue['status'] = true;
        }
        else{
            $sendvalue['message'] = 'Reference number already available.';
        }
        return $sendvalue;
    }


    // Old Code
    public function product_detail($term, $warehouse_id, $supplier_id, $limit = 15){
        $data = array();
        
        $this->db->select('
            sma_products.*,
            sma_warehouses_products.quantity as warehouse_qty
        ');
        $this->db->join('sma_warehouses_products', 'sma_warehouses_products.product_id = products.id AND sma_warehouses_products.warehouse_id = '.$warehouse_id, 'left');
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
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return $data;
    }
    public function data($req = array()){
        $data = array();
        $this->db->select('
            sma_purchase_order_tb.id,
            sma_purchase_order_tb.reference_no,
            sma_purchase_order_tb.supplier_id,
            sma_purchase_order_tb.warehouse_id,
            sma_purchase_order_tb.receiving_date,
            sma_purchase_order_tb.received_date,
            sma_purchase_order_tb.created_at,
            sma_purchase_order_tb.payment_status,
            sma_purchase_order_tb.status,
            sma_companies.name as supplier_name,
            sma_warehouses.code as warehouse_code,
            sma_users.first_name as first_name,
            sma_users.last_name as last_name,
            (SELECT SUM(COALESCE(sma_purchase_order_items_tb.qty,0)) FROM sma_purchase_order_items_tb WHERE sma_purchase_order_items_tb.purchase_id=sma_purchase_order_tb.id) AS count_qty,
            (SELECT SUM(COALESCE(sma_po_received_item_tb.received_qty,0)) FROM sma_po_received_item_tb WHERE sma_po_received_item_tb.po_id=sma_purchase_order_tb.id) AS count_receving,
        ');
        $this->db->from('sma_purchase_order_tb');
        $this->db->join('sma_companies', 'sma_companies.id = sma_purchase_order_tb.supplier_id', 'left');
        $this->db->join('sma_warehouses', 'sma_warehouses.id = sma_purchase_order_tb.warehouse_id', 'left');
        $this->db->join('sma_users', 'sma_users.id = sma_purchase_order_tb.created_by', 'left');
        if($this->session->userdata('group_id') == 10){
            $this->db->where('sma_purchase_order_tb.warehouse_id',$this->session->userdata('warehouse_id'));
        }
        else if($this->session->userdata('warehouse_id') != 0){
            $this->db->where('sma_purchase_order_tb.warehouse_id',$this->session->userdata('warehouse_id'));
        }
        else{
            if($req['wid'] != ""){
                $this->db->where('sma_purchase_order_tb.warehouse_id',$req['wid']);
            }
        }
        if($req['sid'] != ""){
            $this->db->where('sma_purchase_order_tb.supplier_id',$req['sid']);
        }
        if($req['status'] != ""){
            $this->db->where('sma_purchase_order_tb.status',$req['status']);
        }
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->count_receving == ""){$row->count_receving = 0;}
                if($row->count_qty == ""){$row->count_qty = 0;}
                $row->persentage = $this->sma->formatDecimal(($row->count_receving/$row->count_qty)*100);
                $data[] = $row;
            }
            // return $data;
        }
        return $data;
        
    }
    public function details($id){
        $data = array();
        $data['codestatus'] = "no";
        if($id != ""){
            // Get Purchase Order Data
            $this->db->select('*');
            $this->db->from('sma_purchase_order_tb');
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
                // Get warehosue details
                $this->db->select('*');
                $this->db->from('sma_warehouses');
                $this->db->where('id',$result[0]->warehouse_id);
                $q3 = $this->db->get();
                if ($q3->num_rows() > 0) {
                    $warehouse = json_decode(json_encode($q3->result()));
                    $data['details']->warehouse = $warehouse[0];
                }
                // Get Own Company details
                $this->db->select('*');
                $this->db->from('sma_own_companies');
                $this->db->where('id',$result[0]->own_company);
                $q4 = $this->db->get();
                if ($q4->num_rows() > 0) {
                    $own_company = json_decode(json_encode($q4->result()));
                    $data['details']->own_company_detail = $own_company[0];
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
                // Get Purchase Order Items
                $this->db->select('
                    sma_purchase_order_items_tb.*,
                    sma_products.id as product_id,
                    sma_products.name as product_name,
                    (SELECT SUM(COALESCE(sma_po_received_item_tb.received_qty,0)) FROM sma_po_received_item_tb WHERE sma_po_received_item_tb.po_item_id=sma_purchase_order_items_tb.id) AS count_receving
                ');
                $this->db->from('sma_purchase_order_items_tb');
                $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
                $this->db->where('purchase_id',$result[0]->id);
                $q5 = $this->db->get();
                $data['details']->items = array();
                $no = 0;
                if ($q5->num_rows() > 0) {
                    $items = json_decode(json_encode($q5->result()));
                    foreach($items as $item){
                        if($item->count_receving == ""){
                            $item->count_receving = 0;
                        }

                        $data['details']->items[$no] = $item;
                        $no++;
                    }
                }
                $data['codestatus'] = "ok";
            }
            else{
                $data['codestatus'] = "Invalid Purachase";
            }
        }
        else{
            $data['codestatus'] = "Something Wrong";
        }
        return $data;
    }
    public function getitem($id){
        $data = array();
        $this->db->select('
            sma_purchase_order_items_tb.id,
            sma_purchase_order_items_tb.purchase_id,
            sma_purchase_order_items_tb.product_id,
            sma_purchase_order_items_tb.qty,
            sma_purchase_order_items_tb.qty_received,
            sma_products.name as product_name,
            (SELECT COALESCE(SUM(sma_po_received_item_tb.received_qty),0) FROM sma_po_received_item_tb WHERE sma_po_received_item_tb.po_item_id=sma_purchase_order_items_tb.id) AS count_receving
        ');
        $this->db->from('sma_purchase_order_items_tb');
        $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
        $this->db->where('sma_purchase_order_items_tb.id',$id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $resulth = $q->result();
            $resulth = json_decode(json_encode($resulth),true);
            $data = $resulth[0];
            $data['codestatus'] = "ok";
        }
        else{
            $data['codestatus'] = "Invalid Item";
        }
        return $data;
    }
    public function getreceivingitems($id){
        $data = array();
        $this->db->select('
            sma_purchase_order_items_tb.id,
            sma_purchase_order_items_tb.purchase_id,
            sma_purchase_order_items_tb.product_id,
            sma_purchase_order_items_tb.qty,
            sma_purchase_order_items_tb.qty_received,
            sma_products.name as product_name,
            sma_products.code as code,
            (SELECT COALESCE(SUM(sma_po_received_item_tb.received_qty),0) FROM sma_po_received_item_tb WHERE sma_po_received_item_tb.po_item_id=sma_purchase_order_items_tb.id) AS count_receving
        ');
        $this->db->from('sma_purchase_order_items_tb');
        $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
        $this->db->where('purchase_id',$id);
        $q5 = $this->db->get();
        if ($q5->num_rows() > 0) {
            $data['items'] = json_decode(json_encode($q5->result()));
            $data['codestatus'] = "ok";
        }
        else{
            $data['codestatus'] = "Receveing not found";
        }
        return $data;
    }
    public function addreceiving2($items,$porid){
        $po_id = 0;
        $no = 0;
        $rid = 0;
        foreach($items as $item){
            if($item['received_qty'] != 0 && $item['expiry_date'] != ""){
                $itemdetails = $this->getitem($item['po_item_id']);
                $po_id = $item['po_id'];
                $setdata['qty_received'] = $itemdetails['count_receving']+$item['received_qty'];
                if($setdata['qty_received'] == $itemdetails['qty']){
                     $setdata['status'] = 'received';
                }
                else{
                    $setdata['status'] = 'partial';
                }
                $this->db->set($setdata);
                $this->db->where('id', $item['po_item_id']);
                $this->db->update('sma_purchase_order_items_tb');
                $item['receiving_id'] = $porid;
                $this->db->insert('po_received_item_tb', $item);
            }
        }
        if($po_id != 0){
            $this->checkqty($po_id);
        }
    }
    public function addreceiving($items){
        $po_id = 0;
        $no = 0;
        $rid = 0;
        foreach($items as $item){
            if($item['received_qty'] != 0 && $item['expiry_date'] != ""){
                $itemdetails = $this->getitem($item['po_item_id']);
                $po_id = $item['po_id'];

                $setdata['qty_received'] = $itemdetails['count_receving']+$item['received_qty'];
                // $setdata['batch'] = $item['batch_code'];
                // $setdata['expiry_date'] = $item['expiry_date'];

                if($setdata['qty_received'] == $itemdetails['qty']){
                     $setdata['status'] = 'received';
                }
                else{
                    $setdata['status'] = 'partial';
                }

                $this->db->set($setdata);
                $this->db->where('id', $item['po_item_id']);
                $this->db->update('sma_purchase_order_items_tb');
                $insert['po_id'] = $item['po_id'];
                // $insert['batch_code'] = $item['batch_code'];
                $insert['created_by'] = $item['created_by'];
                if($no == 0){
                    $this->db->insert('sma_po_received_tb', $insert);
                    $no++;
                    $rid = $this->db->insert_id();
                }
                $item['receiving_id'] = $rid;
                // $this->updatestatus($item['po_id']);
                $this->db->insert('po_received_item_tb', $item);
                
            }
        }
        if($po_id != 0){
            $this->checkqty($po_id);
        }
        
    }
    public function checkqty($id){
        $this->db->select('*');
        $this->db->from('sma_purchase_order_tb');
        $this->db->where('id',$id);
        $q = $this->db->get();
        $qty = 0;
        $reqty = 0;
        if ($q->num_rows() > 0) {
            $resulth = $q->result();

            // Get Purchase Order Items
            $this->db->select('*');
            $this->db->from('sma_purchase_order_items_tb');
            $this->db->where('purchase_id',$resulth[0]->id);
            $q5 = $this->db->get();
            if ($q5->num_rows() > 0) {
                $items = json_decode(json_encode($q5->result()),true);
                foreach ($items as $item) {
                    $qty += $item['qty'];
                    $reqty += $item['qty_received'];
                }
            }
        }
        if($qty == $reqty){
            $setdata['status'] = 'received';
            $this->db->set($setdata);
            $this->db->where('id',$id);
            $this->db->update('sma_purchase_order_tb');
        }
        else if($reqty > 0){
            $setdata['status'] = 'partial';
            $this->db->set($setdata);
            $this->db->where('id',$id);
            $this->db->update('sma_purchase_order_tb');
        }
    }
    public function deliveries($id){
        $data = array();
        $this->db->select('sma_po_received_tb.*,sma_users.first_name,sma_users.last_name');
        $this->db->from('sma_po_received_tb');
        $this->db->join('sma_users', 'sma_users.id = sma_po_received_tb.created_by', 'left');
        $this->db->where('sma_po_received_tb.po_id',$id);
        $q = $this->db->get();
        $data = $q->result();
        return $data;

    }
    public function purchase_create($id){
        $this->db->select('id');
        $this->db->from('sma_po_received_tb');
        $this->db->where('po_id',$id);
        $this->db->where('purchase_create','no');
        $q = $this->db->get();
        return $q->num_rows();

    }
    public function create_purchase($id){
        $data = array();
        $sendvalue = array();
        $this->db->select('
            sma_purchase_order_tb.*,
            sma_po_received_tb.id as recevied_id,
            sma_po_received_tb.po_id as recevied_po_id,
            sma_po_received_tb.batch_code as recevied_batch_code,
            sma_po_received_tb.created_at as recevied_created_at,
            sma_po_received_tb.purchase_create as recevied_purchase_create,
            sma_companies.name as supplier_name,
        ');
        $this->db->from('sma_po_received_tb');
        $this->db->join('sma_purchase_order_tb', 'sma_purchase_order_tb.id = sma_po_received_tb.po_id', 'left');
        $this->db->join('sma_companies', 'sma_companies.id = sma_purchase_order_tb.supplier_id', 'left');
        $this->db->where('sma_po_received_tb.id',$id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $resulth = $q->result();
            $data['details'] = $resulth[0];

            $this->db->select('
                sma_purchase_order_items_tb.*,
                sma_po_received_item_tb.id as received_item_id,
                sma_po_received_item_tb.receiving_id as received_item_receiving_id,
                sma_po_received_item_tb.po_id as received_item_po_id,
                sma_po_received_item_tb.po_item_id as received_item_po_item_id,
                sma_po_received_item_tb.product_id as received_item_product_id,
                sma_po_received_item_tb.received_qty as received_item_received_qty,
                sma_po_received_item_tb.batch_code as received_item_batch_code,
                sma_po_received_item_tb.expiry_date as received_item_expiry_date,
                sma_po_received_item_tb.location as received_item_location,
                sma_po_received_item_tb.created_at as received_item_created_at,
                sma_po_received_item_tb.created_by as received_item_created_by,
                sma_po_received_item_tb.updated_at as received_item_updated_at,
                sma_po_received_item_tb.updated_by as received_item_updated_by,
                sma_po_received_item_tb.purchase_create as received_item_purchase_create,
                sma_products.name as product_name,
                sma_products.code as product_code,
            ');
            $this->db->from('sma_po_received_item_tb');
            $this->db->join('sma_purchase_order_items_tb', 'sma_purchase_order_items_tb.id = sma_po_received_item_tb.po_item_id', 'left');
            $this->db->join('sma_products', 'sma_products.id = sma_purchase_order_items_tb.product_id', 'left');
            $this->db->where('sma_po_received_item_tb.receiving_id',$resulth[0]->recevied_id);
            $q = $this->db->get();
            $resulth = $q->result();
            $data['items'] = $resulth;
            $sendvalue = $this->cp($data);

        }
        else{
            $sendvalue['codestatus'] = "Invalid Recoard";
        }
        return $sendvalue;
    }
    public function cp($data){
        $sendvalue = array();
        $insert_p['reference_no'] = $data['details']->reference_no;
        $insert_p['supplier_id'] = $data['details']->supplier_id;
        $insert_p['supplier'] = $data['details']->supplier_name;
        $insert_p['warehouse_id'] = $data['details']->warehouse_id;
        $insert_p['own_company'] = $data['details']->own_company;
        $insert_p['note'] = $data['details']->note;
        $insert_p['total'] = $data['details']->total;
        $insert_p['product_discount'] = $data['details']->items_discount;
        $insert_p['order_discount'] = $data['details']->items_discount;
        $insert_p['total_discount'] = $data['details']->items_discount;
        $insert_p['product_tax'] = $data['details']->items_tax;
        $insert_p['order_tax_id'] = $data['details']->order_tax_id;
        $insert_p['order_tax'] = $data['details']->order_tax;
        $insert_p['total_tax'] = $insert_p['product_tax']+$insert_p['order_tax'];
        $insert_p['shipping'] = $data['details']->shipping;
        $insert_p['grand_total'] = $data['details']->grand_total;
        $insert_p['paid'] = $data['details']->paid_amount;
        $insert_p['status'] = $data['details']->status;
        $insert_p['payment_status'] = $data['details']->payment_status;
        $insert_p['created_by'] = $data['details']->created_by;
        $insert_p['updated_at'] = $data['details']->updated_at;
        $insert_p['payment_term'] = $data['details']->payemnt_terms;
        $insert_p['surcharge'] = '0.000';

        $this->db->insert('sma_purchases', $insert_p);
        $p_id = $this->db->insert_id();
        $sendvalue['p_id'] = $p_id;
        $this->db->set('purchase_create','yes');
        $this->db->where('id', $data['details']->recevied_id);
        $this->db->update('sma_po_received_tb');

        foreach($data['items'] as $item){

            $isnert_pi['po_ri_id '] = $item->received_item_id;
            $isnert_pi['purchase_id '] = $p_id;
            $isnert_pi['product_id '] = $item->product_id;
            $isnert_pi['product_code'] = $item->product_code;
            $isnert_pi['product_name'] = $item->product_name;
            $isnert_pi['net_unit_cost'] = $item->purchase_price;
            $isnert_pi['price'] = $item->consignment_price;
            $isnert_pi['dropship'] = $item->dropship_price;
            $isnert_pi['crossdock'] = $item->cross_dock_price;
            $isnert_pi['mrp'] = $item->mrp;
            $isnert_pi['quantity'] = $item->qty;
            $isnert_pi['warehouse_id'] = $data['details']->warehouse_id;
            $isnert_pi['item_tax'] = $item->tax_amount;
            $isnert_pi['tax_rate_id'] = $item->tax_id;
            $isnert_pi['tax'] = $item->tax;
            $isnert_pi['discount'] = '0';
            $isnert_pi['item_discount'] = '0.000';
            $isnert_pi['expiry'] = $item->received_item_expiry_date;
            $isnert_pi['batch'] = $item->received_item_batch_code;
            $isnert_pi['subtotal'] = $item->sub_total;
            $isnert_pi['quantity_balance'] = $item->qty;
            $isnert_pi['date'] = date('Y-m-d');
            $isnert_pi['status'] = $item->status;
            $isnert_pi['unit_cost'] = $item->purchase_price;
            $isnert_pi['real_unit_cost'] = $item->purchase_price;
            $isnert_pi['quantity_received'] = $item->received_item_received_qty;
            $isnert_pi['supplier_part_no'] = '';
            $isnert_pi['product_unit_code'] = $item->unit;
            $isnert_pi['unit_quantity'] = $item->qty;
            $isnert_pi['discount_one'] = $item->sales_incentive_discount_amount;
            $isnert_pi['discount_two'] = $item->trade_discount_amount;
            $isnert_pi['discount_three'] = $item->consumer_discount_amount;
            $isnert_pi['fed_tax'] = $item->fed_tax;
            $this->db->insert('sma_purchase_items', $isnert_pi);

            $this->db->set('purchase_create','yes');
            $this->db->where('id', $item->received_item_id);
            $this->db->update('sma_po_received_item_tb');

            $this->load->model('admin/stores_model');
            $this->stores_model->updateStoreQty($item->product_id,$data['details']->warehouse_id,0,"Purchase Create By PO");

        }
        $sendvalue['codestatus'] = 'ok';
        return $sendvalue;
    }
    public function updatestatus($poid){
        $this->db->select('
            sma_purchase_order_tb.id,
            (SELECT SUM(COALESCE(sma_purchase_order_items_tb.qty,0)) FROM sma_purchase_order_items_tb WHERE sma_purchase_order_items_tb.purchase_id=sma_purchase_order_tb.id) AS count_qty,
            (SELECT SUM(COALESCE(sma_po_received_item_tb.received_qty,0)) FROM sma_po_received_item_tb WHERE sma_po_received_item_tb.po_id=sma_purchase_order_tb.id) AS count_receving,
        ');
        $this->db->from('sma_purchase_order_tb');
        $this->db->where('id',$poid);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $res = $q->result();
            $res = $res[0];
            if($res->count_receving == ""){$res->count_receving = 0;}
            if($res->count_qty == ""){$res->count_qty = 0;}
            $status = "";
            if($res->count_receving == 0){
                $status = "pending";
            }
            else if($res->count_receving == $res->count_qty){
                $status = "received";
            }
            else{
                $status = "partial";
            }

            $this->db->set('status', $status);
            $this->db->where('id',$poid);
            $this->db->update('sma_purchase_order_tb');
        }
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
    public function product_detail2($warehouse_id, $supplier_id){
        $data = array();
        
        $this->db->select('
            sma_products.*,
            sma_warehouses_products.quantity as warehouse_qty
        ');
        $this->db->from('sma_products');
        $this->db->join('sma_warehouses_products', 'sma_warehouses_products.product_id = products.id AND sma_warehouses_products.warehouse_id = '.$warehouse_id, 'left');
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
            sma_products.alert_quantity >= sma_warehouses_products.quantity

        ");
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return $data;
    }
    public function product_detail3($product_id, $warehouse_id, $supplier_id){
        $data = array();
        
        $this->db->select('
            sma_products.*,
            sma_warehouses_products.quantity as warehouse_qty
        ');
        $this->db->from('sma_products');
        $this->db->join('sma_warehouses_products', 'sma_warehouses_products.product_id = products.id AND sma_warehouses_products.warehouse_id = '.$warehouse_id, 'left');
        $this->db->where("
            (
                `sma_products`.`supplier1` = '". $supplier_id . "' OR 
                `sma_products`.`supplier2` = '". $supplier_id . "' OR 
                `sma_products`.`supplier3` = '". $supplier_id . "' OR 
                `sma_products`.`supplier4` = '". $supplier_id . "' OR 
                `sma_products`.`supplier5` = '". $supplier_id . "'
            ) AND 
            `sma_products`.`id` = '".$product_id."' AND 
            sma_products.status = 1");
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return $data;
    }
    public function getreceiving($id){
        $data['items'] = array();

        $this->db->select('
            sma_purchase_order_items_tb.id,
            sma_purchase_order_items_tb.purchase_id,
            sma_purchase_order_items_tb.product_id,
            sma_purchase_order_items_tb.qty,
            sma_purchase_order_items_tb.qty_received,
            sma_products.name as product_name,
            sma_products.code as code,
            (
                SELECT 
                    COALESCE(SUM(sma_po_received_item_tb.received_qty),0) 
                FROM 
                    sma_po_received_item_tb 
                WHERE 
                    sma_po_received_item_tb.po_item_id=sma_purchase_order_items_tb.id
            ) AS count_receving,
            sma_po_received_item_tb.id as pori_id,
            sma_po_received_item_tb.batch_code,
            sma_po_received_item_tb.expiry_date,
            sma_po_received_item_tb.received_qty
        ');
        $this->db->from('sma_po_received_item_tb');
        $this->db->join('sma_products', 'sma_products.id = sma_po_received_item_tb.product_id', 'left');
        // $this->db->join('sma_po_received_item_tb', 'sma_po_received_item_tb.po_item_id = sma_purchase_order_items_tb.id', 'left');
        $this->db->join('sma_purchase_order_items_tb', 'sma_po_received_item_tb.po_item_id = sma_purchase_order_items_tb.id', 'left');
        $this->db->where('sma_po_received_item_tb.receiving_id',$id);
        $q5 = $this->db->get();
        if ($q5->num_rows() > 0) {
            $data['items'] = json_decode(json_encode($q5->result()));
            $data['codestatus'] = "ok";
        }
        else{
            $data['codestatus'] = "Receveing not found";
        }
        return $data;
    }
}
