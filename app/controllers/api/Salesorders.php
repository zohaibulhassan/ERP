<?php defined('BASEPATH') or exit('No direct script access allowed');
class Salesorders extends CI_Controller{
    public function create_customer($store_id,$order){
        $sendvalue['id'] = 0;
        $sendvalue['name'] = "";
        $this->db->select('*');
        $this->db->from('companies');
        $this->db->where('ecom_store_id',$store_id);
        $this->db->where('ecom_customer_id',$order->customer_id);
        $q = $this->db->get();
        if($q->num_rows() == 0){
            $data = array(
                'sales_type' => 'mrp',
                'name' => $order->billing->first_name.' '.$order->billing->last_name,
                'company' => $order->billing->first_name.' '.$order->billing->last_name,
                'phone' => $order->billing->phone,
                'email' => $order->billing->email,
                'address' => $order->billing->address_1.' '.$order->billing->address_2,
                'postal_code' => $order->billing->postcode,
                'cnic' => '',
                'vat_no' => '',
                'city' => $order->billing->city,
                'state' => $order->billing->state,
                'country' => $order->billing->country,
                'cf1' => '',
                'gst_no' => '',
                'linces' => '',
                'group_id' => '3',
                'group_name' => 'customer'
            );
            $this->db->insert('companies',$data);
            $insert_id = $this->db->insert_id();
            $sendvalue['id'] = $insert_id;
            $sendvalue['name'] = $order->billing->first_name.' '.$order->billing->last_name;
        }
        else{
            $data = $q->result()[0];
            $sendvalue['id'] = $data->id;
            $sendvalue['name'] = $data->name;
        }
        return $sendvalue;
    }
    public function createso(){
        $soid = 0;
        $storeid =  $this->input->get('sid');
        $activitynote = "";
        try {
            header('Content-Type: application/json');
            $request = file_get_contents('php://input');
            $req_dump = print_r($request, true);
            $orderdata = json_decode($req_dump);
            if(isset($orderdata->line_items)){
                $customer = $this->create_customer($storeid,$orderdata);
                $insert['date'] = date('Y-m-d H:i:s');
                $insert['reference_no'] = date('Y').''.date('m').''.date('d').''.date('H').''.date('i').''.date('s').''.$customer['id'].'4'.rand(100,999);
                $insert['customer_id'] = $customer['id'];
                $insert['customer'] = $customer['name'];
                $insert['count'] = 0;
                $insert['order_discount_id'] = 0;
                $insert['shipping'] = 0;
                $insert['total'] = 0;
                $insert['biller_id'] = 48;
                $insert['warehouse_id'] = 4;
                $insertitems = array();
                $products = $orderdata->line_items;
                foreach($products as $key => $row){
                    $pq = $this->db->select('products.*')
                            ->from('products')
                            ->join('store_products_tb','store_products_tb.product_id = products.id AND store_products_tb.store_id = '.$storeid,'LEFT')
                            ->where('store_products_tb.store_product_id',$row->product_id)
                            ->get();
                    if($pq->num_rows() > 0){
                        $product = $pq->result()[0];
                        $insert['count']++;
                        $temp['product_id '] = $product->id;
                        $temp['product_code'] = $product->code;
                        $temp['company_code'] = $product->company_code;
                        $temp['product_name'] = $product->name;
                        $temp['net_unit_price'] = $product->mrp;
                        $temp['unit_price'] = $product->price;
                        $temp['dropship'] = $product->dropship;
                        $temp['crossdock'] = $product->crossdock;
                        $temp['mrp'] = $product->mrp;
                        $temp['quantity'] = $row->quantity;
                        $temp['subtotal'] = $row->quantity*$product->mrp;
                        $temp['real_unit_price'] = $product->price;
                        $temp['unit_quantity'] = $row->quantity;
                        $temp['product_price'] = $product->mrp;
                        $temp['expiry'] = "";
                        $temp['batch'] = "";
                        $temp['warehouse_id'] = 4;
                        $temp['item_discount'] = 0;
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
                $insert['order_discount_id'] = 0;
                $insert['shipping'] = 0;
                $insert['total'] = $insert['total']+$insert['shipping']-$insert['order_discount_id'];
                if(count($insertitems) > 0){
                    $this->db->insert('suspended_bills',$insert);
                    echo $insert_id = $this->db->insert_id();
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
                    $sendvalue['message'] = "Bill on hold successfully"; 
                    $sendvalue['status'] = true; 
                }
                else{
                    $sendvalue['message'] = "Please select product"; 
                }
                $activitynote = $sendvalue['message'];
            }
            else{
                $activitynote = 'Order Item Not Found';
            }
            $activitynote .= ' Order Data: '.$req_dump;
            $this->useractivities_model->add([
                'note'=>$activitynote,
                'location'=>'API->Auto SO->Add->Submit',
                'store_id'=>$storeid,
                'action_by'=>77
            ]);
        }
        catch(Exception $e) {
            $insert['content2'] = 'Code Error';
            $activitynote = 'Code Error';
        }
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
    public function separateItemsBySupplier($items,$sid){
        $sendvalue = array();
        foreach($items as $item){
            $data['store_product_id'] = $item->product_id;
            $data['quantity'] = $item->quantity;
            $supplier = $this->getProductSupplierDetail($item->product_id,$sid);
            if($supplier){
                $data = $supplier; 
                $data['store_product_id'] = $item->product_id;
                $data['quantity'] = $item->quantity;
                $sendvalue[$supplier['supplier_name']][] = $data;
            }
            else{
                // echo 'separateItemsBySupplier<br>';
                return false;
            }
        }
        return $sendvalue;
    }
    public function getProductSupplierDetail($spid,$sid){
        $this->db->select('
            sma_products.id as product_id,
            sma_products.pack_size,
            sma_products.carton_size,
            sma_companies.id,
            sma_companies.name,
            sma_store_products_tb.warehouse_id as warehouse_id,
            sma_stores_tb.customer_id as customer_id,
            sma_store_products_tb.update_qty_in as update_qty_in,
            sma_stores_tb.name as store_name
        ');
        $this->db->from('sma_store_products_tb');
        $this->db->join('sma_products','sma_products.id = sma_store_products_tb.product_id');
        $this->db->join('sma_companies','sma_companies.id = sma_store_products_tb.supplier_id');
        $this->db->join('sma_stores_tb','sma_stores_tb.id = sma_store_products_tb.store_id');
        $this->db->where('sma_store_products_tb.store_product_id',$spid);
        $this->db->where('sma_store_products_tb.store_id',$sid);
        $this->db->where('sma_stores_tb.auto_so','yes');
        $q =  $this->db->get();
        if($q->num_rows()){
            $result = $q->result()[0];
            $sendvalue['product_id'] = $result->product_id;
            $sendvalue['supplier_id'] = $result->id;
            $sendvalue['supplier_name'] = $result->name;
            $sendvalue['warehouse_id'] = $result->warehouse_id;
            $sendvalue['customer_id'] = $result->customer_id;
            $sendvalue['store_name'] = $result->store_name;
            $sendvalue['update_qty_in'] = $result->update_qty_in;
            $sendvalue['pack_size'] = $result->pack_size;
            $sendvalue['carton_size'] = $result->carton_size;
            return $sendvalue;
        }
        else{
            // echo 'Store Product ID: '.$spid.' Store ID '.$sid.' getProductSupplierDetail<br>';
            return false;
        }

    }

}