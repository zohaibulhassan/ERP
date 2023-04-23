<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD

class Stores_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('admin/wordpresswoocommerce_model','wp');
        $this->load->model('admin/shopify_model','shopify');
        $this->load->admin_model('daraz_model');
        $this->load->admin_model('darazAPI_model');
    }
    public function data(){
        $sendvalue = array();
        $this->db->select('
            sma_stores_tb.id as id,
            sma_stores_tb.name as name,
            sma_stores_tb.types as types,
            sma_stores_tb.warehouse_id as warehouse_id,
            sma_warehouses.name as warehouse_name,
            sma_warehouses.code as warehouse_code,
            sma_stores_tb.update_qty_in as update_qty_in,
            sma_stores_tb.update_price as update_price,
            sma_stores_tb.created_at as created_at,
            sma_users.first_name as created_by,
            sma_stores_tb.status as status,
        ');
        $this->db->from('sma_stores_tb');
        $this->db->join('sma_users', 'sma_users.id = sma_stores_tb.created_by', 'left');
        $this->db->join('sma_warehouses', 'sma_warehouses.id = sma_stores_tb.warehouse_id', 'left');
        $q = $this->db->get();
        $sendvalue = $q->result();
        return $sendvalue;
    }
    public function detail($id){
        $sendvalue = array();
        $this->db->select('
            sma_stores_tb.*
        ');
        $this->db->from('sma_stores_tb');
        $this->db->join('sma_users', 'sma_users.id = sma_stores_tb.created_by', 'left');
        $this->db->where('sma_stores_tb.id',$id);
        $q = $this->db->get();
        $result = $q->result();
        if(count($result)>0){
            $sendvalue = $result[0];
            return $sendvalue;
        }
        else{
            return false;
        }

    }
    public function products($store_id){
        $sendvalue = array();
            // (SELECT * FROM sma_store_products_tb WHERE store_id = '.$store_id.' AND product_id = sma_products.id) as spid,

        $this->db->select("
            sma_store_products_tb.id as id,
            sma_products.id as pid,
            sma_store_products_tb.update_in as update_in,
            sma_store_products_tb.product_name as pname,
            sma_store_products_tb.store_product_id as spid,
            sma_store_products_tb.update_qty_in,
            sma_store_products_tb.price_type,
            sma_store_products_tb.warehouse_id,
            sma_warehouses.name as warehouse_name,
            sma_store_products_tb.discount,
            IF(sma_store_products_tb.status='active', sma_store_products_tb.status,'deactive') as status,

        ");
        $this->db->from('sma_store_products_tb');
        $this->db->join('sma_products', 'sma_products.id = sma_store_products_tb.product_id', 'left');
        $this->db->join('sma_warehouses', 'sma_warehouses.id = sma_store_products_tb.warehouse_id', 'left');
        $this->db->where('sma_store_products_tb.store_id',$store_id);
        // $this->db->where('sma_products.status',1);
        
        $q = $this->db->get();
        $products = $q->result();
        foreach($products as $product){
            $setdata = $product;
            if($product->discount == "no"){
                $setdata->discountname = 'No Discount';
            }
            else if($product->discount == "d1"){
                $setdata->discountname = 'Discount 1';
            }
            else if($product->discount == "d2"){
                $setdata->discountname = 'Discount 2';
            }
            else if($product->discount == "d3"){
                $setdata->discountname = 'Discount 3';
            }
            else{
                $setdata->discountname = $this->getdiscountname($product->discount);
            }

            $sendvalue[] = $setdata;
        }
        return $sendvalue;
    }
    public function getdiscountname($id){
        $sendvalue = "Invalid Discount";
        $this->db->select('');
        $this->db->from('sma_bulk_discount');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows()){
            $sendvalue = $q->result()[0]->discount_name;
        }
        return $sendvalue;
    }
    public function getQty($pid,$wid = 1){
        $sendvalue = 0;
        $this->db->select('quantity');
        $this->db->from('warehouses_products');
        $this->db->where('product_id',$pid);
        $this->db->where('warehouse_id', $wid);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $result = $q->result();
            $sendvalue = $result[0]->quantity;

        }
        return $sendvalue;
    }
    public function getProductDetail($pid){
        $this->db->select('
            stores_tb.id as store_id,
            store_products_tb.product_id,
            store_products_tb.store_product_id,
            store_products_tb.update_qty_in as pupdate_qty_in,
            store_products_tb.price_type as pprice_type,
            store_products_tb.discount as pdiscount,
            stores_tb.name as store_name,
            stores_tb.integration_type,
            stores_tb.update_type,
            stores_tb.default_category,
            stores_tb.store_url,
            stores_tb.types,
            stores_tb.warehouse_id,
            stores_tb.update_qty_in,
            stores_tb.update_price,
            stores_tb.discount,
            stores_tb.daraz_store_id,
            stores_tb.daraz_api_key,
            stores_tb.wordpress_wocommerce_consumer_key,
            stores_tb.wordpress_wocommerce_consumer_secret,
            products.name,
            products.product_details,
            products.pack_size,
            products.carton_size,
            products.cost as cost,
            products.price as consiment, 
            products.dropship as dropship,
            products.crossdock as crossdock,
            products.mrp as mrp,
            products.status as status,
            tax_rates.name as tax_name,
            tax_rates.code as tax_code,
            tax_rates.rate as tax_rate,
            tax_rates.type as tax_type,
            products.discount_one as discount_one,
            products.discount_two as discount_two,
            products.discount_three as discount_three,
        ');
        $this->db->from('store_products_tb');
        $this->db->join('stores_tb','stores_tb.id = store_products_tb.store_id');
        $this->db->join('products','products.id = store_products_tb.product_id');
        $this->db->join('tax_rates','tax_rates.id = products.tax_rate');
        $this->db->where('store_products_tb.product_id',$pid);
        $this->db->where('store_products_tb.status','active');
        $this->db->where('stores_tb.status','active');
        $q =  $this->db->get();
        return $q->result();
    }
    public function createProduct($id){
        $this->db->select('*');
        $this->db->from('sma_stores_tb');  
        $this->db->where('status','active');
        $this->db->where('integration_type','full');    
        $store_q = $this->db->get();  
        if($store_q->num_rows()){
            $stories = $store_q->result();
            foreach($stories as $store){
                $product = $this->getProduct($id,$store->update_price,$store->update_qty_in);
                $senddata['store_url'] = $store->store_url;
                $senddata['wordpress_wocommerce_consumer_key'] = $store->wordpress_wocommerce_consumer_key;
                $senddata['wordpress_wocommerce_consumer_secret'] = $store->wordpress_wocommerce_consumer_secret;
                $senddata['product']['name'] = $product['name'];
                $senddata['product']['type'] = 'simple';
                $senddata['product']['regular_price'] = (string)$product['regular_price'];
                $senddata['product']['categories'][0]['id'] = '15';
                $senddata['product']['short_description'] = $product['short_description'];
                $senddata['product']['images'] = '';
                $returndata = $this->wp->createProduct($senddata);
                $insertdata['store_id'] = $store->id;
                $insertdata['product_id'] = $id;
                $insertdata['store_product_id'] = $returndata['productdata']->id;
                $insertdata['created_by'] = $this->session->userdata('user_id');
                $insertdata['status'] = 'active';
                $this->db->insert('store_products_tb',$insertdata);
            }
        }
    }
    public function updateProduct($product_id){
        $sendvalue['codestatus'] = "no";
        $codes = $this->getProductDetail($product_id);
        foreach ($codes as $key => $code) {
            $updateprice = 0;
            $updatemrpprice = 0;
            if($code->update_price == "cost"){$updateprice = $code->cost;}
            else if($code->update_price == "consiment"){
                $ptax = 0;
                if($code->tax_type == "1"){
                    $ptax = ($code->consiment/100)*$code->tax_rate;

                }
                else{
                    $ptax = $code->tax_rate;
                }
                $updateprice = $code->consiment+$ptax;
            }
            else if($code->update_price == "dropship"){$updateprice = $code->dropship;}
            else if($code->update_price == "crossdock"){$updateprice = $code->crossdock;}
            else if($code->update_price == "mrp"){$updateprice = $code->mrp;}
            $qty = $this->getQty($product_id,$code->warehouse_id);
            $updateqty = 0;
            if($code->update_qty_in == "single"){
                $updateqty = $qty;
                $updatemrpprice = $code->mrp;
            }
            else if($code->update_qty_in == "pack"){
                if($code->pack_size > 0){$updateqty =  $qty/$code->pack_size;}
                $updateprice = $updateprice*$code->pack_size;
                $updatemrpprice = $code->mrp*$code->pack_size;
            }
            else if($code->update_qty_in == "carton"){
                if($code->carton_size > 0){$updateqty =  $qty/$code->carton_size;}
                $updateprice = $updateprice*$code->carton_size;
                $updatemrpprice = $code->mrp*$code->carton_size;
            }
            $codes[$key]->updateqty = (int)$updateqty;
            $codes[$key]->updateprice = $updateprice;
            $codes[$key]->updatemrpprice = $updatemrpprice;
            if($code->types == "Wordpress (Wocommerce)"){
                $returndata = $this->wp->updateProduct($code);
                $sendvalue['productstatus'][] = $returndata;
            }
        }
        return $sendvalue;
    }
    public function getProduct($id,$update_price="mrp",$update_qty_in="single"){
        $sendvalue['codestatus'] = "no";
        $this->db->select('
            products.name,
            products.product_details,
            products.pack_size,
            products.carton_size,
            products.cost as cost,
            products.price as consiment, 
            products.dropship as dropship,
            products.crossdock as crossdock,
            products.mrp as mrp,
            tax_rates.name as tax_name,
            tax_rates.code as tax_code,
            tax_rates.rate as tax_rate,
            tax_rates.type as tax_type,
        ');
        $this->db->from('products');
        $this->db->join('tax_rates','tax_rates.id = products.tax_rate');
        $this->db->where('products.id',$id);
        $query =  $this->db->get();
        if($query->num_rows() > 0 ){
            $result = $query->result();
            $result = $result[0];
            $sendvalue['name'] = $result->name;
            $sendvalue['short_description'] = $result->product_details;
            $updateprice = 0;
            if($update_price == "cost"){$updateprice = $result->cost;}
            else if($update_price == "consiment"){
                $ptax = 0;
                if($result->tax_type == "1"){
                    $ptax = ($result->consiment/100)*$result->tax_rate;

                }
                else{
                    $ptax = $result->tax_rate;
                }
                $updateprice = $result->consiment+$ptax;
            }
            else if($update_price == "dropship"){$updateprice = $result->dropship;}
            else if($update_price == "crossdock"){$updateprice = $result->crossdock;}
            else if($update_price == "mrp"){$updateprice = $result->mrp;}
            if($update_qty_in == "pack"){
                $updateprice = $updateprice*$result->pack_size;
            }
            else if($update_qty_in == "carton"){
                $updateprice = $updateprice*$result->carton_size;
            }
            $updatepriceint = (int)$updateprice;
            if($updateprice>$updatepriceint){
                $updatepriceint += 1;
            }
            $sendvalue['regular_price'] = $updatepriceint;
        }
        return $sendvalue;
    }
    public function newProduct($res){
        $returndata = $this->wp->newProduct($res);
        return $returndata;
    }
    public function updateProductDetail($res,$spid){
        $returndata = $this->wp->updateProductDetail($res,$spid);
        return $returndata;
    }
    public function deleteProduct($res,$spid){
        $returndata = $this->wp->deleteProduct($res,$spid);
        return $returndata;
    }
    public function updateStoreQty($pid,$wid = 0,$sid = 0,$note=""){
        $insert['product_id'] = $pid;
        $insert['warehouse_id'] = $wid;
        $insert['store_id'] = $sid;
        $insert['type'] = 'Qty Update';
        $insert['status'] = 'pending';
        $insert['created_at'] = date('Y-m-d H:i:s');
        $insert['note'] = $note;
        $this->db->insert('sma_store_requests_tb',$insert);
    }
    public function UpdatePrice($pid,$wid = 0,$sid = 0,$note=""){
        $insert['product_id'] = $pid;
        $insert['warehouse_id'] = $wid;
        $insert['store_id'] = $sid;
        $insert['type'] = 'Price Update';
        $insert['status'] = 'pending';
        $insert['created_at'] = date('Y-m-d H:i:s');
        $insert['note'] = $note;
        $this->db->insert('sma_store_requests_tb',$insert);
    }
    public function countStock($pid,$wid){
        $query = 'SELECT SUM(quantity_balance) AS qty FROM sma_purchase_items WHERE product_id = '.$pid.' AND quantity_balance != "0.0000"';
        if($wid != "all" && $wid != ""){
            $query .= ' AND warehouse_id = '.$wid;
        }

        $query = $this->db->query($query);
        $r = $query->result()[0];
        return $r->qty;

    }
    public function calPrice($gpid = "", $gpricetype = "", $gdiscount = "", $gstocktype = "", $gwarehouse_id = ""){
        $sendvalue['price'] = 0;
        $sendvalue['tax'] = 0;
        $sendvalue['discount'] = 0;
        $sendvalue['total'] = 0;
        $sendvalue['codestatus'] = 'no';
        $pid = $gpid;
        $pricetype = $gpricetype;
        $discount = $gdiscount;
        $stocktype = $gstocktype;
        $warehouse_id = $gwarehouse_id;
        $this->db->select('
            sma_products.name as product_name,
            sma_products.quantity,
            sma_products.product_details,
            sma_products.mrp,
            sma_products.cost,
            sma_products.price,
            sma_products.dropship,
            sma_products.crossdock,
            sma_products.discount_one,
            sma_products.discount_two,
            sma_products.discount_three,
            sma_products.discount_mrp,
            sma_products.pack_size,
            sma_products.carton_size,
            sma_tax_rates.rate,
            sma_tax_rates.type
        ');
        $this->db->from('sma_products');
        $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_products.tax_rate');
        $this->db->where('sma_products.id',$pid);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $product = $q->result()[0];
            $sendvalue['product_name'] = $product->product_name;
            if($pricetype == "mrp"){
                $sendvalue['price'] = $product->mrp;
                $sendvalue['discount'] = $this->calDiscount($product->price,$discount,$product);
            }
            else if($pricetype == "consiment"){
                $sendvalue['price'] = $product->price;
                if($product->type == "1"){
                    $sendvalue['tax'] = ($product->price/100)*$product->rate;
                }
                else{
                    $sendvalue['tax'] = $product->rate;
                }
                $sendvalue['discount'] = $this->calDiscount($product->price,$discount,$product);
            }
            else if($pricetype == "dropship"){
                $sendvalue['price'] = $product->price;
            }
            else if($pricetype == "crossdock"){
                $sendvalue['price'] = $product->crossdock;
            }
            else if($pricetype == "cost"){
                $sendvalue['price'] = $product->cost;
                if($product->type == "1"){
                    $sendvalue['tax'] = ($product->cost/100)*$product->rate;
                }
                else{
                    $sendvalue['tax'] = $product->rate;
                }
            }
            $qty = 1;
            if($stocktype == "pack"){
                $qty = (int)$product->pack_size;
            }
            else if($stocktype == "carton"){
                $qty = (int)$product->carton_size;
            }
            else{
                $qty = 1;
            }
            $countStock = $this->countStock($pid,$warehouse_id);
            if($countStock != "" && $countStock != 0 && $countStock != '0.0000'){
                $store_hold_qty = $this->getPendingItemsInSO($warehouse_id,$pid);
                $sendvalue['store_hold_qty'] = $store_hold_qty;
                $sendvalue['countStock'] = $countStock;
                $countStock = $countStock-$store_hold_qty;
                $stock = (int)$countStock/$qty;
            }
            else{
                $stock = 0;
            }
            if($stock<0){
                $stock = 0;
            }
            $sendvalue['stock'] = (int)$stock;
            $sendvalue['product_details'] = $product->product_details;
            $sendvalue['mrp'] = $product->mrp*$qty;
            $sendvalue['price'] = decimalallow($sendvalue['price']*$qty,4);
            $sendvalue['tax'] = decimalallow($sendvalue['tax']*$qty,4);
            $sendvalue['discount'] = decimalallow($sendvalue['discount']*$qty,4);
            $sendvalue['codestatus'] = 'ok';
        }
        else{
            $sendvalue['codestatus'] = 'Invalid Product';
        }
        $sendvalue['total'] = decimalallow($sendvalue['price']+$sendvalue['tax']-$sendvalue['discount'],4);
        return $sendvalue;
    }
    public function calDiscount($price,$discount,$product){
        $sendvalue = 0;
        if($discount == "d1"){
            $sendvalue = ($price/100)*$product->discount_one;
        }
        else if($discount == "d2"){
            $sendvalue = ($price/100)*$product->discount_two;
        }
        else if($discount == "d3"){
            $sendvalue = ($price/100)*$product->discount_three;
        }
        else if($discount == "mrp"){
            $sendvalue = ($price/100)*$product->discount_mrp;
        }
        else if($discount == "no"){
            $sendvalue = 0;
        }
        else{
            $sendvalue = ($price/100)*$this->get_discount_rate($discount);
        }
        return $sendvalue;
    }
    public function get_discount_rate($id){
        $rate = 0;
        $this->db->select('*');
        $this->db->from('sma_bulk_discount');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $discount = $q->result()[0];
            $rate = $discount->percentage;
        }
        return $rate;
    }
    public function countStorePendingQty($sid,$spid){
        $senddvalue = 0;
        if($sid != ""){
            $this->db->select('*');
            $this->db->from('sma_stores_tb');
            $this->db->where('id',$sid);
            $storeq = $this->db->get();
            if($storeq->num_rows()>0){
                $store = $storeq->result()[0];
                $page = 1;
                // Get Proccessing Orders
                $orders_processing = $this->wp->getorder($store,$page,$spid,'processing');
                if($orders_processing['codestatus']){
                    foreach($orders_processing['orders'] as $row){
                        $items = $row->line_items;
                        foreach($items as $item){
                            if($item->product_id == $spid){
                                $senddvalue += $item->quantity;
                            }
                        }
                    }
                }
                // Get Hold Orders
                $orders_hold = $this->wp->getorder($store,$page,$spid,'on-hold');
                if($orders_hold['codestatus']){
                    foreach($orders_hold['orders'] as $row){
                        $items = $row->line_items;
                        foreach($items as $item){
                            if($item->product_id == $spid){
                                $senddvalue += $item->quantity;
                            }
                        }
                    }
                }
            }
        }
        return $senddvalue;
    }
    public function getPendingItemsInSO($wid,$pid){
        $data['soiqty'] = 0;
        $data['sociqty'] = 0;
        $this->db->select('customer_id');
        $this->db->from('sma_stores_tb');
        $this->db->where('status','active');
        $this->db->group_by('customer_id');
        $cidq = $this->db->get();
        $cids = $cidq->result();
        $sno = 0;
        $where = '';
        foreach($cids as $cid){
            if($cid->customer_id != 0){
                $sno++;
                if($sno == 1){
                    $where .= 'sma_sales_orders_tb.customer_id = '.$cid->customer_id;
                }
                else{
                    $where .= ' OR sma_sales_orders_tb.customer_id = '.$cid->customer_id;
                }
            }
        }
        $this->db->select('
            IFNULL(SUM(sma_sales_order_items.quantity),0) AS qty
        ');
        $this->db->from('sma_sales_orders_tb');
        $this->db->join('sma_sales_order_items','sma_sales_order_items.so_id = sma_sales_orders_tb.id');
        if($where != ""){
            $this->db->where('('.$where.')');
        }
        $this->db->where('sma_sales_orders_tb.warehouse_id',$wid);
        $this->db->where('sma_sales_order_items.product_id',$pid);
        $this->db->where('sma_sales_order_items.status = "pending"');
        $this->db->where('sma_sales_orders_tb.status != ','cancel');
        $this->db->where('sma_sales_orders_tb.status != ','close');
        $q = $this->db->get();
        if($q->num_rows()>0){
            $result = $q->result()[0];
            $data['soiqty'] = $result->qty;
        }
        else{
            $data['soiqty'] = 0;
        }
        $this->db->select('
            IFNULL(SUM(sma_sales_order_complete_items.quantity),0) AS qty
        ');
        $this->db->from('sma_sales_orders_tb');
        $this->db->join('sma_sales_order_complete_items','sma_sales_order_complete_items.so_id = sma_sales_orders_tb.id');
        if($where != ""){
            $this->db->where('('.$where.')');
        }
        $this->db->where('sma_sales_orders_tb.warehouse_id',$wid);
        $this->db->where('sma_sales_order_complete_items.product_id',$pid);
        $this->db->where('sma_sales_orders_tb.status != ','cancel');
        $this->db->where('sma_sales_orders_tb.status != ','close');
        $q = $this->db->get();
        if($q->num_rows()>0){
            $result = $q->result()[0];
            $data['sociqty'] = $result->qty;
        }
        else{
            $data['sociqty'] = 0;
        }
        $sendvalue = $data['soiqty']-$data['sociqty'];
        return $sendvalue;
    }
    public function StoreQtyUpdate($pid,$wid = 0,$sid = 0){
        $senddata['codestatus'] = "no";
        $this->db->select('
            sma_stores_tb.id as store_id,
            sma_stores_tb.store_url,
            sma_stores_tb.wordpress_wocommerce_consumer_key,
            sma_stores_tb.wordpress_wocommerce_consumer_secret,
            sma_stores_tb.daraz_store_id,
            sma_stores_tb.daraz_api_key,
            sma_stores_tb.types,
            sma_stores_tb.stock_margin,
            sma_store_products_tb.warehouse_id,
            sma_store_products_tb.update_qty_in,
            sma_store_products_tb.price_type,
            sma_store_products_tb.discount,
            sma_store_products_tb.store_product_id,
            sma_products.pack_size,
            sma_products.carton_size,
            sma_products.id,
            sma_products.hold_stock,
        ');
        $this->db->from('sma_store_products_tb');
        $this->db->join('sma_stores_tb','sma_stores_tb.id =  sma_store_products_tb.store_id','left');
        $this->db->join('sma_products','sma_products.id =  sma_store_products_tb.product_id','left');
        $this->db->where('sma_store_products_tb.product_id',$pid);
        if($wid != 0){
            $this->db->where('sma_store_products_tb.warehouse_id',$wid);
        }
        if($sid != 0){
            $this->db->where('sma_stores_tb.id',$sid);
        }
        $this->db->where('sma_stores_tb.status','active');
        $this->db->where('sma_store_products_tb.status','active');
        $this->db->where('(sma_store_products_tb.update_in = "qty" OR sma_store_products_tb.update_in = "priceqty" OR sma_store_products_tb.update_in = "detailnqty" OR sma_store_products_tb.update_in = "full")');
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $products = $q->result();
            $senddata['details'] = array();
            foreach($products as $product){
                $othernote = "";
                if($product->types=="Wordpress (Wocommerce)"){
                    $getData = $this->calculateStockAndPrice($pid,$product->price_type,$product->discount,$product->update_qty_in,$product->warehouse_id,$product->stock_margin);
                    $wodata['store_url'] = $product->store_url;
                    $wodata['wordpress_wocommerce_consumer_key'] = $product->wordpress_wocommerce_consumer_key;
                    $wodata['wordpress_wocommerce_consumer_secret'] = $product->wordpress_wocommerce_consumer_secret;
                    $wodata['product']['manage_stock'] = true;
                    if($getData['stock'] > 0){
                        $wodata['product']['stock_status'] = 'instock';
                        $wodata['product']['stock_quantity'] = (int)$getData['stock'];
                    }
                    else{
                        $wodata['product']['stock_status'] = 'outofstock';
                        $wodata['product']['stock_quantity'] = 0;
                    }
                    $returndata['updatedata'] = $wodata;
                    $returndata['apirespose'] = $this->wp->updateProductDetail($wodata,$product->store_product_id);
                }
                else if($product->types=="Daraz"){
       
                    $countStock = $this->countStock($pid,$product->warehouse_id);
                    $store_hold_qty = $this->getPendingItemsInSO($product->warehouse_id,$pid);
                    $countStock = $countStock-$product->hold_stock;
                    $countStock = $countStock-$store_hold_qty;
                     if($countStock != "" && $countStock != 0 && $countStock != '0.0000'){
                        if($product->update_qty_in == "pack"){
                            $stock = (int)$countStock/$product->pack_size;
                        }
                        else if($product->update_qty_in == "carton"){
                            $stock = $countStock/$product->carton_size;
                        }
                        else{
                            $stock = $countStock;
                        }
                        $stock = (int)$stock;
                    }
                    else{
                        
                        $stock = 0;
                    }
                    $countstockmargin = ($product->stock_margin/100) * $stock;
                    $darazdata['daraz_api_key'] = $product->daraz_api_key;
                    $darazdata['daraz_username'] = $product->daraz_store_id;
                    $darazdata['quantity'] = (int)$countstockmargin;
                    $darazdata['store_product_sku'] = $product->store_product_id;
                    
                    $insertdata['store_product_id'] = $product->store_product_id;
                    // $returndata = $this->daraz_model->get_update_product_daraz_api($darazdata);
                    $returndata['updatedata'] = $darazdata;
                    $returndata['apirespose'] = $this->darazAPI_model->updateProduct($darazdata);
                }
                else if($product->types=="Shopify"){
                    $getData = $this->calculateStockAndPrice($pid,$product->price_type,$product->discount,$product->update_qty_in,$product->warehouse_id,$product->stock_margin);
                    $this->load->model('admin/shopify_model','shopify');
                    $returndata['apirespose'] = $this->shopify->update_qyt($product->store_url,$product->wordpress_wocommerce_consumer_key,$product->wordpress_wocommerce_consumer_secret,$product->store_product_id,$getData['stock']);
                }
                else{
                    $returndata['codestatus']="Invalid Store ID";
                }
                $senddata['details'] = $returndata;
            }
            $senddata['codestatus'] = "No Of Products: ".count($products);
        }
        else{
            $senddata['codestatus'] = "Products not Found";

        }
        return $senddata;
        
    }
    public function StorePriceUpdate($pid,$wid = 0,$sid = 0){
        $senddata['message'] = "";
        $this->db->select('
            sma_stores_tb.id as storeid,
            sma_stores_tb.store_url,
            sma_stores_tb.types,
            sma_stores_tb.wordpress_wocommerce_consumer_key,
            sma_stores_tb.wordpress_wocommerce_consumer_secret,
            sma_store_products_tb.warehouse_id,
            sma_store_products_tb.price_type,
            sma_store_products_tb.discount,
            sma_store_products_tb.update_qty_in,
            sma_store_products_tb.store_product_id
        ');
        $this->db->from('sma_store_products_tb');
        $this->db->join('sma_stores_tb','sma_stores_tb.id =  sma_store_products_tb.store_id','left');
        $this->db->where('sma_store_products_tb.product_id',$pid);
        $this->db->where('sma_stores_tb.status','active');
        $this->db->where('sma_store_products_tb.status','active');
        $this->db->where('(sma_store_products_tb.update_in = "qty" OR sma_store_products_tb.update_in = "priceqty" OR sma_store_products_tb.update_in = "detailnqty" OR sma_store_products_tb.update_in = "full")');
        // if($wid != 0){
        //     $this->db->where('sma_store_products_tb.warehouse_id',$wid);
        // }
        // if($sid != 0){
        //     $this->db->where('sma_stores_tb.id',$sid);
        // }
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $products = $q->result();
            $senddata['store_resposnse'] = array();
            foreach($products as $product){
                if($product->types=="Wordpress (Wocommerce)"){
                    $productdetail = $this->calPrice($pid, $product->price_type, $product->discount, $product->update_qty_in, $product->warehouse_id);
                    $wodata['store_url'] = $product->store_url;
                    $wodata['wordpress_wocommerce_consumer_key'] = $product->wordpress_wocommerce_consumer_key;
                    $wodata['wordpress_wocommerce_consumer_secret'] = $product->wordpress_wocommerce_consumer_secret;
                    if($productdetail['mrp'] == $productdetail['total']){
                        $wodata['product']['regular_price'] = (string)$productdetail['mrp'];
                        $wodata['product']['sale_price'] = '';
                    }
                    else{
                        $wodata['product']['regular_price'] = (string)$productdetail['mrp'];
                        $wodata['product']['sale_price'] = (string)$productdetail['total'];
                    }
                    $returndata = $this->wp->updateProductDetail($wodata,$product->store_product_id);
                }
                else if($product->types=="Shopify"){
                    $productdetail = $this->calPrice($pid, $product->price_type, $product->discount, $product->update_qty_in, $product->warehouse_id);
                    $this->load->model('admin/shopify_model','shopify');
                    $returndata = $this->shopify->updateprice($product->store_url,$product->wordpress_wocommerce_consumer_key,$product->wordpress_wocommerce_consumer_secret,$product->store_product_id,$productdetail['mrp'],$productdetail['total']);
    

                }

                $senddata['store_resposnse'][] = $returndata;
            }
            $senddata['message'] = "Total Products: ".count($products);
        }
        else{
            $senddata['message'] = "Store Not Found";
        }
        return $senddata;
        
    }
    public function calculateStockAndPrice($pid = "", $pricetype = "", $discount = "", $stocktype = "", $warehouse_id = "", $stock_margin = ""){
        $sendvalue['codestatus'] = 'no';
        $this->db->select('
            sma_products.name as product_name,
            sma_products.quantity,
            sma_products.product_details,
            sma_products.mrp,
            sma_products.cost,
            sma_products.price,
            sma_products.dropship,
            sma_products.crossdock,
            sma_products.supplier1,
            sma_products.discount_one,
            sma_products.discount_two,
            sma_products.discount_three,
            sma_products.discount_mrp,
            sma_products.pack_size,
            sma_products.carton_size,
            sma_tax_rates.rate,
            sma_tax_rates.type
        ');
        $this->db->from('sma_products');
        $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_products.tax_rate');
        $this->db->where('sma_products.id',$pid);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $product = $q->result()[0];
            $sendvalue['product_name'] = $product->product_name;
            // Get Regular Price and Discount and Product Tax
            if($pricetype == "mrp"){
                $sendvalue['price'] = $product->mrp;
                $sendvalue['tax'] = 0;
                $sendvalue['discount'] = $this->calDiscount($product->price,$discount,$product);
            }
            else if($pricetype == "consiment"){
                $sendvalue['price'] = $product->price;
                if($product->type == "1"){
                    $sendvalue['tax'] = ($product->price/100)*$product->rate;
                }
                else{
                    $sendvalue['tax'] = $product->rate;
                }
                $sendvalue['discount'] = $this->calDiscount($product->price,$discount,$product);
            }
            else if($pricetype == "dropship"){
                $sendvalue['price'] = $product->price;
            }
            else if($pricetype == "crossdock"){
                $sendvalue['price'] = $product->crossdock;
            }
            else if($pricetype == "cost"){
                $sendvalue['price'] = $product->cost;
                if($product->type == "1"){
                    $sendvalue['tax'] = ($product->cost/100)*$product->rate;
                }
                else{
                    $sendvalue['tax'] = $product->rate;
                }
            }
            // Get Package Size
            $pacakgesize = 1;
            if($stocktype == "pack"){
                $pacakgesize = (int)$product->pack_size;
            }
            else if($stocktype == "carton"){
                $pacakgesize = (int)$product->carton_size;
            }
            else{
                $pacakgesize = 1;
            }
            $sendvalue['pacakgesize'] = $pacakgesize;
            // Get Rhocom Balance Quantity and minus SO demend qty
            $countStock = $this->countStock($pid,$warehouse_id);
            $sendvalue['rhocomqty'] = $countStock;
            if($countStock != "" && $countStock != 0 && $countStock != '0.0000'){
                $store_hold_qty = $this->getPendingItemsInSO($warehouse_id,$pid);
                $sendvalue['store_hold_qty'] = $store_hold_qty;
                $countStock = $countStock-$store_hold_qty;
                $stock = (int)$countStock/$pacakgesize;
            }
            else{
                $stock = 0;
            }
            if($stock<0){
                $stock = 0;
            }

            $stock_margin = !empty($stock_margin) ? $stock_margin : 100;
            $sendvalue['stock_margin'] = $stock_margin;
            $countstockmargin = ($stock/100) * $stock_margin;
            $sendvalue['stock'] = (int)$countstockmargin;
            
            $sendvalue['product_details'] = $product->product_details;
            $sendvalue['mrp'] = $product->mrp;
            $sendvalue['price'] = decimalallow($sendvalue['price'],4);
            $sendvalue['tax'] = decimalallow($sendvalue['tax'],4);
            $sendvalue['discount'] = decimalallow($sendvalue['discount'],4);
            $sendvalue['supplier1'] = $product->supplier1;
            $sendvalue['codestatus'] = 'ok';
            $sendvalue['total'] = decimalallow($sendvalue['price']+$sendvalue['tax']-$sendvalue['discount'],4);
        }
        else{
            $sendvalue['codestatus'] = 'Invalid Product';
        }
        return $sendvalue;
    }
    public function shopifyPriceUpdate($pid,$warehouseid,$sid){
        $this->db->select('
            sma_stores_tb.id as store_id,
            sma_stores_tb.store_url,
            sma_stores_tb.wordpress_wocommerce_consumer_key,
            sma_stores_tb.wordpress_wocommerce_consumer_secret,
            sma_stores_tb.daraz_store_id,
            sma_stores_tb.daraz_api_key,
            sma_stores_tb.types,
            sma_stores_tb.stock_margin,
            sma_store_products_tb.warehouse_id,
            sma_store_products_tb.update_qty_in,
            sma_store_products_tb.price_type,
            sma_store_products_tb.discount,
            sma_store_products_tb.store_product_id,
            sma_products.pack_size,
            sma_products.carton_size,
            sma_products.id,
            sma_products.hold_stock,
        ');
        $this->db->from('sma_store_products_tb');
        $this->db->join('sma_stores_tb','sma_stores_tb.id =  sma_store_products_tb.store_id','left');
        $this->db->join('sma_products','sma_products.id =  sma_store_products_tb.product_id','left');
        $this->db->where('sma_store_products_tb.product_id',$pid);
        if($warehouseid != 0){
            $this->db->where('sma_store_products_tb.warehouse_id',$warehouseid);
        }
        if($sid != 0){
            $this->db->where('sma_stores_tb.id',$sid);
        }
        $this->db->where('sma_stores_tb.status','active');
        $this->db->where('sma_store_products_tb.status','active');
        $this->db->where('(sma_store_products_tb.update_in = "qty" OR sma_store_products_tb.update_in = "priceqty" OR sma_store_products_tb.update_in = "detailnqty" OR sma_store_products_tb.update_in = "full")');
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $products = $q->result();
            foreach($products as $product){
                $productdetail = $this->calPrice($pid, $product->price_type, $product->discount, $product->update_qty_in, $product->warehouse_id);
                $this->shopify->updateprice($product->store_url,$product->wordpress_wocommerce_consumer_key,$product->wordpress_wocommerce_consumer_secret,$product->store_product_id,$productdetail['mrp'],$productdetail['total']);

            }

        }
    }
}