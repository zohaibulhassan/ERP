<?php defined('BASEPATH') or exit('No direct script access allowed');
class Salesorders extends App_Controller{
    function __construct(){
        parent::__construct();
        $this->load->admin_model('salesorder_model');
    }
    public function orders(){
        $page = $this->input->post('page');
        $limit = $this->input->post('limit');
        $text = $this->input->post('text');
        $created_by = $this->input->post('created_by');
        if($page == "" || $page <= 0){
            $page = 1;
        }
        if($limit == "" || $limit > 20){
            $limit = 20;
        }
        else if($limit <= 5){
            $limit = 5;
        }
        $end=$limit*$page;
        $start=$end-$limit;
        $this->db->select('
            so.id as order_id,
            so.ref_no as order_no,
            so.date as order_date,
            supplier.name as supplier_name,
            0 as total_amount,
            (
                SELECT
                    COUNT(id)
                FROM
                    sma_sales_order_items AS soi
                WHERE
                    soi.so_id = so.id
            ) as total_items,
            so.status as status
        ');
        $this->db->from('sma_sales_orders_tb as so');
        $this->db->join('sma_companies as supplier','supplier.id = so.supplier_id','left');
        if($text != ""){
            $this->db->where('so.ref_no LIKE "%'.$text.'%"');
        }
        if($created_by != ""){
            $this->db->where('so.created_by',$created_by);
        }
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        $q = $this->db->get();
        $this->data['bookings'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
    public function order(){
        $id = $this->input->post('id');
        if($id != ""){
            $this->db->select('
                so.id as order_id,
                so.date as order_date,
                so.ref_no as order_no,
                so.po_number as po_number,
                so.po_date as po_date,
                so.delivery_date as delivery_date,
                so.cancel_date as cancel_date,
                so.status as order_status,
                supplier.id as supplier_id,
                supplier.name as supplier_name,
                customer.id as customer_id,
                customer.name as customer_name,
                customer.phone as customer_phone,
                customer.email as customer_email,
                customer.gst_no as customer_gst_no,
                warehouse.id as warehouse_id,
                warehouse.name as warehouse_name,
                "" as items

            ');
            $this->db->from('sma_sales_orders_tb as so');
            $this->db->join('sma_companies as supplier','supplier.id = so.supplier_id');
            $this->db->join('sma_companies as customer','customer.id = so.customer_id');
            $this->db->join('sma_warehouses as warehouse','warehouse.id = so.warehouse_id');
            $this->db->where('so.id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $order = $q->result()[0];
                $this->db->select('
                    soi.id as item_id,
                    soi.product_id,
                    product.code as product_barcode,
                    product.name as product_name,
                    "no_image" as product_image,
                    soi.quantity,
                    soi.status
                ');
                $this->db->from('sma_sales_order_items as soi');
                $this->db->join('sma_products as product','product.id = soi.product_id');
                $this->db->where('soi.so_id',$id);
                $q2 = $this->db->get();
                $order->items = $q2->result();
                $this->data['order'] = $order;
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Order Not Found";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Invaild Order ID!";
            $this->data['error_code'] = '003';
        }
       $this->responsedata();
    }
    public function create(){
        
        $sale_date = date('Y-m-d H:i:s');
        $warehouse_id = $this->input->post('warehouse_id');
        $po_number = $this->input->post('po_number');
        $po_date = $this->input->post('po_date');
        $customer_id = $this->input->post('customer_id');
        // $delivery_date = $this->input->post('delivery_date');
        $delivery_date = date("Y-m-d",strtotime(date('Y-m-d')." +7 day"));
        $supplier_id = $this->input->post('supplier_id');
        $user_id = $this->input->post('user_id');
        $items = $this->input->post('items');
        $items = json_decode($items);
        $soitems = array();
        foreach($items as $item){

            $itemsdetail['product_id'] = $item->product_id;
            $itemsdetail['supplier_id'] = $supplier_id;
            $itemsdetail['warehouse_id'] = $warehouse_id;
            $itemsdetail['quantity'] = $item->quantity;
            $itemsdetail['unit'] = 'Pieces';
            $itemsdetail['unit_code'] = 'pcs';
            $itemsdetail['status'] = 'pending';
            $soitems[] = $itemsdetail;

        }
        $so['ref_no'] = $this->generate_ref();
        $so['date'] = $sale_date;
        $so['warehouse_id'] = $warehouse_id;
        $so['po_number'] = $po_number;
        $so['po_date'] = $po_date;
        $so['delivery_date'] = $delivery_date;
        $so['supplier_id'] = $supplier_id;
        $so['customer_id'] = $customer_id;
        $so['sale_note'] = '';
        $so['created_by'] = $user_id;
        $this->salesorder_model->add_so($so,$soitems);
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
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
            $sendvalue = 'ASO-'.sprintf("%05d", $refresult[0]->AUTO_INCREMENT);
        }
        return $sendvalue;

    }
    public function cancel(){
        $cancel_date = date('Y-m-d H:i:s');
        $order_id = $this->input->post('order_id');
        $user_id = $this->input->post('user_id');
        if($order_id != "" && $user_id != ""){
            $this->db->select('*');
            $this->db->from('sales_orders_tb');
            $this->db->where('id',$order_id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $order = $q->result()[0];
                $orderdate = dateformate($order->date);
                if($orderdate == date('Y-m-d')){
                    if($order->operation_team_stauts== "pending"){
                        
                        $setdata['cancel_date'] = date('Y-m-d H:i:s');
                        $setdata['status'] = 'cancel';
                        $this->db->set($setdata);
                        $this->db->where('id',$order_id);
                        $this->db->update('sales_orders_tb');

                        $this->data['code_status'] = true;
                        $this->data['message'] = "Success!";
                    }
                    else{
                        $this->data['message'] = "Permission Denied. This order process in dispatch";
                        $this->data['error_code'] = '004';
                    }
                }
                else{
                    $this->data['message'] = "Permission Denied. Only Cancel Current Date Order.";
                    $this->data['error_code'] = '004';
                }
            }
            else{
                $this->data['message'] = "Invalid Order";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
}