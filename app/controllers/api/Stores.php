<?php defined('BASEPATH') or exit('No direct script access allowed');
class Stores extends CI_Controller{
    public function orderupdated(){
        $storeid =  $this->input->get('sid');
        $activitynote = "";
        try {
            header('Content-Type: application/json');
            $request = file_get_contents('php://input');
            $req_dump = print_r($request, true);
            $orderdata = json_decode($req_dump);
            if(isset($orderdata->status)){
                if($orderdata->status == 'cancelled'){
                    if(isset($orderdata->id)){
                        $returndata = $this->cancel($orderdata->id); 
                        $activitynote .= "Update Successfully. Return Data: ".$returndata;
                    }
                    else{
                        $activitynote .= "Store Order ID Not Found. ";
                    }
                }
                else{
                    $activitynote .= "Store Order Status Not Cancelled. ";
                }
            }
            else{
                $activitynote .= "Store Order Status Not Found. ";
            }
            $activitynote .= "Order Update: ".$req_dump;
        }
        catch(Exception $e) {
            $activitynote = 'Code Error: '.$e->getMessage();
        }
    }
    public function orderdeleted(){
        $storeid =  $this->input->get('sid');
        $activitynote = "";
        try {
            header('Content-Type: application/json');
            $request = file_get_contents('php://input');
            $req_dump = print_r($request, true);
            $orderdata = json_decode($req_dump);
            if(isset($orderdata->id)){
                $returndata = $this->cancel($orderdata->id); 
                $activitynote .= "Update Successfully. Return Data: ".$returndata;
            }
            else{
                $activitynote .= "Store Order ID Not Found. ";
            }
            $activitynote .= " Order Delete: ".$req_dump;
        }
        catch(Exception $e) {
            $activitynote = 'Code Error: '.$e->getMessage();
        }
    }
    public function orderrestored(){
        $activitynote = "";
        $storeid =  $this->input->get('sid');
        try {
            header('Content-Type: application/json');
            $request = file_get_contents('php://input');
            $req_dump = print_r($request, true);
            $activitynote = "Order Restored: ".$req_dump;
        }
        catch(Exception $e) {
            $activitynote = 'Code Error: '.$e->getMessage();
        }
    }
    public function productupdated(){
        // $activitynote = "";
        // $storeid =  $this->input->get('sid');
        // try {
        //     header('Content-Type: application/json');
        //     $request = file_get_contents('php://input');
        //     $req_dump = print_r($request, true);
        //     $productdata = json_decode($req_dump);
        //     if(isset($productdata->id)){
        //         $this->db->select('sma_stores_tb.*,sma_store_products_tb.product_id as rhocom_product_id');
        //         $this->db->from('sma_store_products_tb');
        //         $this->db->join('sma_stores_tb','sma_stores_tb.id = sma_store_products_tb.store_id','left');
        //         $this->db->where('store_id',$storeid);
        //         $this->db->where('store_product_id',$productdata->id);
        //         $q = $this->db->get();
        //         if($q->num_rows() > 0){
        //             $pdata =  $q->result()[0];
        //             $this->load->model('admin/stores_model');
        //             $this->stores_model->updateStoreQty($pdata->rhocom_product_id,$pdata->warehouse_id,$storeid,"Update Product By API");
        //             $activitynote .= "Product Update Successfully.";
        //         }
        //         else{
        //             $activitynote .= "Product not found in Rhocom360.";
        //         }
        //     }
        //     else{
        //         $activitynote .= "Store Product ID Not Found.";
        //     }
        //     $activitynote .= " Data: ".$req_dump;
        // }
        // catch(Exception $e) {
        //     $activitynote = 'Code Error: '.$e->getMessage();
        // }
    }
    public function productdeleted(){
        $storeid =  $this->input->get('sid');
        $activitynote = "";
        try {
            header('Content-Type: application/json');
            $request = file_get_contents('php://input');
            $req_dump = print_r($request, true);
            $productdata = json_decode($req_dump);
            if(isset($productdata->id)){
                $this->db->select('sma_stores_tb.*,sma_store_products_tb.product_id as rhocom_product_id');
                $this->db->from('sma_store_products_tb');
                $this->db->join('sma_stores_tb','sma_stores_tb.id = sma_store_products_tb.store_id','left');
                $this->db->where('store_id',$storeid);
                $this->db->where('store_product_id',$productdata->id);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $pdata =  $q->result()[0];
                    $this->load->model('admin/stores_model');
                    $this->stores_model->updateStoreQty($pdata->rhocom_product_id,$pdata->warehouse_id,$storeid,"Delete Product By API");
                    $activitynote .= "Product Update Successfully.";
                }
                else{
                    $activitynote .= "Product not found in Rhocom360.";
                }
            }
            else{
                $activitynote .= "Store Product ID Not Found.";
            }
            $activitynote .= " Data: ".$req_dump;
        }
        catch(Exception $e) {
            $activitynote = 'Code Error: '.$e->getMessage();
        }
    }
    public function productrestored(){
        $activitynote = "";
        $storeid =  $this->input->get('sid');
        try {
            header('Content-Type: application/json');
            $request = file_get_contents('php://input');
            $req_dump = print_r($request, true);
            $productdata = json_decode($req_dump);
            if(isset($productdata->id)){
                $this->db->select('sma_stores_tb.*,sma_store_products_tb.product_id as rhocom_product_id');
                $this->db->from('sma_store_products_tb');
                $this->db->join('sma_stores_tb','sma_stores_tb.id = sma_store_products_tb.store_id','left');
                $this->db->where('store_id',$storeid);
                $this->db->where('store_product_id',$productdata->id);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $pdata =  $q->result()[0];
                    $this->load->model('admin/stores_model');
                    $this->stores_model->updateStoreQty($pdata->rhocom_product_id,$pdata->warehouse_id,$storeid,"Restore Product By API");
                    $activitynote .= "Product Update Successfully.";
                }
                else{
                    $activitynote .= "Product not found in Rhocom360.";
                }
            }
            else{
                $activitynote .= "Store Product ID Not Found.";
            }
            $activitynote .= " Data: ".$req_dump;
        }
        catch(Exception $e) {
            $activitynote = 'Code Error: '.$e->getMessage();
        }
    }
    public function cancel($id){
        $sendvalue['codestatus'] = "no";
        $sendvalue['sos'] = array();

        $this->db->select('id');
        $this->db->from('sma_sales_orders_tb');
        $this->db->where('store_order_id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $result = $q->result();
            foreach($result as $row){
                $this->db->select('id');
                $this->db->from('sma_sales_order_complete_items');
                $this->db->where('so_id',$row->id);
                $cq = $this->db->get();
                if($cq->num_rows() == 0){
                    $setdata['cancel_date'] = date('Y-m-d H:i:s');
                    $setdata['status'] = 'cancel';
                    $this->db->set($setdata);
                    $this->db->where('id',$row->id);
                    $this->db->update('sma_sales_orders_tb');
                    $sendvalue['sos'][]['id'] = $row->id;
                }
            }
            $sendvalue['message'] = "Sale Order Canceled";
            $sendvalue['codestatus'] = "ok";
        }
        else{
            $sendvalue['message'] = "Sale Order Not Found";
        }
        return json_encode($sendvalue);
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
                $this->stores_model->updateStoreQty($item->product_id,$item->warehouse_id,0,"All Item Update in API Store");
            }
        }
        else{
        }


    }
    public function updatestore(){
        $this->load->admin_model('stores_model');
        $sendvalue['codestatus'] = false;
        $sec_key = "sfd82r2039jxc9yfd892y3e2d3";
        $get_key = $this->input->get('key');
        $active_product_id = 0;
        $active_store_id = 0;
        if($sec_key == $get_key){
            $this->db->select('id,type,product_id,warehouse_id,store_id,GROUP_CONCAT(id) as ids');
            $this->db->from('sma_store_requests_tb');
            $this->db->where('status','pending');
            $this->db->group_by(array(
                "product_id", 
                "warehouse_id", 
                "store_id",
                "type"
            ));
            $this->db->order_by("created_at", "asc");
            $this->db->limit(1);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $rows = $q->result();
                $sendvalue['ids'] = array();
                $sendvalue['details'] = array();
                foreach($rows as $row){
                    if($row->type == "Qty Update"){
                        $sendvalue['details'][] = $this->stores_model->StoreQtyUpdate($row->product_id,$row->warehouse_id,$row->store_id);
                    }
                    else if($row->type == "Price Update"){
                        $sendvalue['details'][] = $this->stores_model->StorePriceUpdate($row->product_id,$row->warehouse_id);
                    }
                    $where_id = array();
                    $ids  = explode(",",$row->ids);
                    $this->db->set('status','completed');
                    $this->db->set('complete_date',date('Y-m-d H:i:s'));
                    $this->db->where_in('id',$ids);
                    $this->db->update('sma_store_requests_tb');
                    $sendvalue['ids'][] = $ids;
                    $active_product_id = $row->product_id;
                    $active_store_id = $row->store_id;
                }
                $sendvalue['codestatus'] = true;
                $sendvalue['message'] = "Stores Update";
            }
            else{
                $sendvalue['message'] = "No Request";
            }
        }
        else{
            $sendvalue['message'] = "Access Denied";
        }
        $jsondata = json_encode($sendvalue);
        $this->useractivities_model->add([
            'note'=>'. Store Product Update From Bot',
            'product_id'=>$active_product_id,
            'store_id'=>$active_store_id,
            'json_data'=>$jsondata,
            'location'=>'Both->Store->Product->Update',
            'action_by'=>$this->session->userdata('user_id')
        ]);

        echo json_encode($jsondata);
    }
}
