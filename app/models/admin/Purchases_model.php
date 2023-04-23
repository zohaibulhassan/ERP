<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }
    public function check_batch($item_code, $batch_number){
        $q = $this->db->query("SELECT * FROM `sma_purchase_items` WHERE product_code = '".$item_code."' and batch = '".$batch_number."'");
        if ($q->num_rows() > 0) {
            return true;
        }
        return FALSE;
    }
    public function getProductNames($term, $warehouse_id, $supplier_id, $limit = 15){
        // $this->db->save_queries = TRUE;
        
        $this->db->where("type = 'standard' AND (`sma_products`.`supplier1` = '". $supplier_id . "' OR `sma_products`.`supplier2` = '". $supplier_id . "' OR `sma_products`.`supplier3` = '". $supplier_id . "' OR `sma_products`.`supplier4` = '". $supplier_id . "' OR `sma_products`.`supplier5` = '". $supplier_id . "') AND (`sma_products`.`status` = 1) AND (name LIKE '%" . $this->db->escape_like_str($term) . "%' OR code LIKE '%" . $this->db->escape_like_str($term) . "%' OR supplier1_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR supplier2_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR supplier3_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR supplier4_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR supplier5_part_no LIKE '%" . $this->db->escape_like_str($term) . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $this->db->escape_like_str($term) . "%')");
        $this->db->limit($limit);
        $q = $this->db->get('products');
        
        // echo $this->db->last_query();
        

        // die();
        // exit();
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAllProducts(){
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getProductByID($id){
        $q = $this->db->get_where('products', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getProductsByCode($code){
        $this->db->select('*')->from('products')->like('code', $code, 'both');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getProductByCode($code){
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getProductByName($name){
        $q = $this->db->get_where('products', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getAllPurchases(){
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getAllPurchaseItemsCompany($id){
        $q = $this->db->get_where('own_companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getAllPurchaseItems($purchase_id){

        // $this->db->save_queries = TRUE;

        $this->db->select('purchase_items.*, tax_rates.code as tax_code, tax_rates.type as tax_type, tax_rates.name as tax_name, tax_rates.rate as tax_rate, products.unit, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('products', 'products.id=purchase_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=purchase_items.option_id', 'left')
            ->join('tax_rates', 'tax_rates.id=purchase_items.tax_rate_id', 'left')
            ->group_by('purchase_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            $str = $this->db->last_query();
            // echo $str;

            // die();

            // exit();


            return $data;
        }
        return FALSE;
    }
    public function getItemByID($id){
        $q = $this->db->get_where('purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTaxRateByName($name){
        $q = $this->db->get_where('tax_rates', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getPurchaseByID($id){
        $q = $this->db->get_where('purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getProductOptionByID($id){
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getProductWarehouseOptionQty($option_id, $warehouse_id){
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function addProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id){
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                return TRUE;
            }
        } else {
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
                return TRUE;
            }
        }
        return FALSE;
    }
    public function resetProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id){
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                return TRUE;
            }
        }
        return FALSE;
    }
    public function getOverSoldCosting($product_id){
        $q = $this->db->get_where('costing', array('overselling' => 1));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function addPurchase($data, $items){
        $this->db->trans_start();
        if ($this->db->insert('purchases', $data)) {
            $purchase_id = $this->db->insert_id();
            if ($this->site->getReference('po') == $data['reference_no']) {
                $this->site->updateReference('po');
            }
            foreach ($items as $item) {
                $item['purchase_id'] = $purchase_id;
                $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : NULL;
                $this->db->insert('purchase_items', $item);
                if ($this->Settings->update_cost) {
                    $this->db->update('products', array('cost' => $item['real_unit_cost']), array('id' => $item['product_id']));
                }
                if($item['option_id']) {
                    $this->db->update('product_variants', array('cost' => $item['real_unit_cost']), array('id' => $item['option_id'], 'product_id' => $item['product_id']));
                }
                if ($data['status'] == 'received' || $data['status'] == 'returned') {
                    $this->updateAVCO(array('product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['quantity'], 'cost' => $item['real_unit_cost']));
                }
    
            }

            if ($data['status'] == 'returned') {
                $this->db->update('purchases', array('return_purchase_ref' => $data['return_purchase_ref'], 'surcharge' => $data['surcharge'],'return_purchase_total' => $data['grand_total'], 'return_id' => $purchase_id), array('id' => $data['purchase_id']));
            }

            if ($data['status'] == 'received' || $data['status'] == 'returned') {
                $this->site->syncQuantity(NULL, $purchase_id);
                foreach ($items as $item) {
                    $this->load->model('admin/stores_model');
                    $this->stores_model->updateStoreQty($item['product_id'],$item['warehouse_id'],0,"Add Purchase");
    
                }
            }
                $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                log_message('error', 'An errors has been occurred while adding the sale (Add:Purchases_model.php)');
            } else {
                return true;
            }
        }
        return false;
    }
    public function check_sales($id, $batch){
        //foreach ($items as $key => $value) {
            //// $this->db->save_queries = TRUE;

            $q = $this->db->get_where('sale_items', array('batch' => $batch, 'product_id' => $id), 1);
            
            //echo $this->db->last_query();
            
            //die();
            //exit();
            if ($q->num_rows() > 0) {
                return $q->num_rows();
            }
            return false;
        //}
    }
    public function updatePurchase($id, $data, $items = array()){
        $this->db->trans_start();
        $opurchase = $this->getPurchaseByID($id);
        $oitems = $this->getAllPurchaseItems($id);

        
        
        if ($this->db->update('purchases', $data, array('id' => $id)) && $this->db->delete('purchase_items', array('purchase_id' => $id))) {
        // if ($this->db->update('purchases', $data, array('id' => $id)) && $this->db->delete('purchase_items', array('purchase_id' => $id))) {
            $purchase_id = $id;
            foreach ($items as $item) {
                $item['purchase_id'] = $id;
                $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : NULL;
                $this->db->insert('purchase_items', $item);
                $purchase_items_insert_id = $this->db->insert_id();
                $this->db->update('costing', array('purchase_item_id' => $purchase_items_insert_id), array('batch' => $item['batch'], 'product_id' => $item['product_id']));

                if ($data['status'] == 'received' || $data['status'] == 'partial') {
                    $this->updateAVCO(array('product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'quantity' => $item['quantity'], 'cost' => $item['real_unit_cost']));
                }

            }
            $this->site->syncQuantity(NULL, NULL, $oitems);
            foreach ($oitems as $oitem) {
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($oitem->product_id,$oitem->warehouse_id,0,"Update Purchase");
            }
            if ($data['status'] == 'received' || $data['status'] == 'partial') {
                $this->site->syncQuantity(NULL, $id);
                foreach ($oitems as $oitem) {
                    $this->updateAVCO(array('product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => (0-$oitem->quantity), 'cost' => $oitem->real_unit_cost));
                }
            }
            $this->site->syncPurchasePayments($id);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Update:Purchases_model.php)');
        } else {
            return true;
        }
        return false;
    }
    public function updateStatus($id, $status, $note){
        $this->db->trans_start();
        $purchase = $this->getPurchaseByID($id);
        $items = $this->site->getAllPurchaseItems($id);

        if ($this->db->update('purchases', array('status' => $status, 'note' => $note), array('id' => $id))) {
            if (($purchase->status != 'received' || $purchase->status != 'partial') && ($status == 'received' || $status == 'partial')) {
                foreach ($items as $item) {
                    $qb = $status == 'received' ? ($item->quantity_balance + ($item->quantity - $item->quantity_received)) : $item->quantity_balance;
                    $qr = $status == 'received' ? $item->quantity : $item->quantity_received;
                    $this->db->update('purchase_items', array('status' => $status, 'quantity_balance' => $qb, 'quantity_received' => $qr), array('id' => $item->id));
                    $this->updateAVCO(array('product_id' => $item->product_id, 'warehouse_id' => $item->warehouse_id, 'quantity' => $item->quantity, 'cost' => $item->real_unit_cost));
                }
                $this->site->syncQuantity(NULL, NULL, $items);
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item->product_id,$item->warehouse_id,0,"Update Purchase Status");
            } else if (($purchase->status == 'received' || $purchase->status == 'partial') && ($status == 'ordered' || $status == 'pending') ) {
                foreach ($items as $item) {
                    $qb = 0;
                    $qr = 0;
                    $this->db->update('purchase_items', array('status' => $status, 'quantity_balance' => $qb, 'quantity_received' => $qr), array('id' => $item->id));
                    $this->updateAVCO(array('product_id' => $item->product_id, 'warehouse_id' => $item->warehouse_id, 'quantity' => $item->quantity, 'cost' => $item->real_unit_cost));
                }
                $this->site->syncQuantity(NULL, NULL, $items);
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item->product_id,$item->warehouse_id,0,"Update Purchase Status");
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (UpdateStatus:Purchases_model.php)');
        } else {
            return true;
        }
        return false;
    }
    public function deletePurchase($id){
        $this->db->trans_start();
        $purchase = $this->getPurchaseByID($id);
        $purchase_items = $this->site->getAllPurchaseItems($id);
        if ($this->db->delete('purchase_items', array('purchase_id' => $id)) && $this->db->delete('purchases', array('id' => $id))) {
            $this->db->delete('payments', array('purchase_id' => $id));
            if ($purchase->status == 'received' || $purchase->status == 'partial') {
                foreach ($purchase_items as $oitem) {
                    $this->updateAVCO(array('product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'quantity' => (0-$oitem->quantity), 'cost' => $oitem->real_unit_cost));
                    $received = $oitem->quantity_received ? $oitem->quantity_received : $oitem->quantity;
                    if ($oitem->quantity_balance < $received) {
                        $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $oitem->product_id, 'warehouse_id' => $oitem->warehouse_id, 'option_id' => $oitem->option_id);
                        $this->site->setPurchaseItem($clause, ($oitem->quantity_balance - $received));
                    }
                }
            }
            $this->site->syncQuantity(NULL, NULL, $purchase_items);
            foreach ($purchase_items as $oitem) {
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($oitem->product_id,$oitem->warehouse_id,0,"Delete Purchase");
            }


            $setdata['purchase_id'] = 0;
            $setdata['purchase_create'] = 'no';
            $this->db->set($setdata);
            $this->db->where('purchase_id', $id);
            $this->db->update('sma_po_received_tb');

            $this->db->set($setdata);
            $this->db->where('purchase_id', $id);
            $this->db->update('sma_po_received_item_tb');

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Delete:Purchases_model.php)');
        } else {
            return true;
        }
        return FALSE;
    }
    public function getWarehouseProductQuantity($warehouse_id, $product_id){
        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getPurchasePayments($purchase_id){
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getPaymentByID($id){
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
    public function getPaymentsForPurchase($purchase_id){
        $this->db->select('payments.date, payments.paid_by, payments.amount, payments.reference_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function addPayment($data = array()){
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('ppay') == $data['reference_no']) {
                $this->site->updateReference('ppay');
            }
            $this->site->syncPurchasePayments($data['purchase_id']);
            return true;
        }
        return false;
    }
    public function updatePayment($id, $data = array()){
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->site->syncPurchasePayments($data['purchase_id']);
            return true;
        }
        return false;
    }
    public function deletePayment($id){
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->site->syncPurchasePayments($opay->purchase_id);
            return true;
        }
        return FALSE;
    }
    public function getProductOptions($product_id){
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getProductVariantByName($name, $product_id){
        $q = $this->db->get_where('product_variants', array('name' => $name, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getExpenseByID($id){
        $q = $this->db->get_where('expenses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function addExpense($data = array()){
        if ($this->db->insert('expenses', $data)) {
            if ($this->site->getReference('ex') == $data['reference']) {
                $this->site->updateReference('ex');
            }
            return true;
        }
        return false;
    }
    public function updateExpense($id, $data = array()){
        if ($this->db->update('expenses', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }
    public function deleteExpense($id){
        if ($this->db->delete('expenses', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
    public function getQuoteByID($id){
        $q = $this->db->get_where('quotes', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getAllQuoteItems($quote_id){
        $q = $this->db->get_where('quote_items', array('quote_id' => $quote_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getReturnByID($id){
        $q = $this->db->get_where('return_purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getAllReturnItems($return_id){
        $this->db->select('return_purchase_items.*, products.details as details, product_variants.name as variant, products.hsn_code as hsn_code, products.second_name as second_name')
            ->join('products', 'products.id=return_purchase_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=return_purchase_items.option_id', 'left')
            ->group_by('return_purchase_items.id')
            ->order_by('id', 'asc');
        $q = $this->db->get_where('return_purchase_items', array('return_id' => $return_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getPurcahseItemByID($id){
        $q = $this->db->get_where('purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function returnPurchase($data = array(), $items = array()){
        return false;
        exit();
        // $purchase_items = $this->site->getAllPurchaseItems($data['purchase_id']);

        // if ($this->db->insert('return_purchases', $data)) {
        //     $return_id = $this->db->insert_id();
        //     if ($this->site->getReference('rep') == $data['reference_no']) {
        //         $this->site->updateReference('rep');
        //     }
        //     foreach ($items as $item) {
        //         $item['return_id'] = $return_id;
        //         $this->db->insert('return_purchase_items', $item);

        //         if ($purchase_item = $this->getPurcahseItemByID($item['purchase_item_id'])) {
        //             if ($purchase_item->quantity == $item['quantity']) {
        //                 // $this->db->delete('purchase_items', array('id' => $item['purchase_item_id']));
        //             } else {
        //                 $nqty = $purchase_item->quantity - $item['quantity'];
        //                 $bqty = $purchase_item->quantity_balance - $item['quantity'];
        //                 $rqty = $purchase_item->quantity_received - $item['quantity'];
        //                 $tax = $purchase_item->unit_cost - $purchase_item->net_unit_cost;
        //                 $discount = $purchase_item->item_discount / $purchase_item->quantity;
        //                 $item_tax = $tax * $nqty;
        //                 $item_discount = $discount * $nqty;
        //                 $subtotal = $purchase_item->unit_cost * $nqty;
        //                 // $this->db->update('purchase_items', array('quantity' => $nqty, 'quantity_balance' => $bqty, 'quantity_received' => $rqty, 'item_tax' => $item_tax, 'item_discount' => $item_discount, 'subtotal' => $subtotal), array('id' => $item['purchase_item_id']));
        //             }
        
        //         }
        //     }
        //     $this->calculatePurchaseTotals($data['purchase_id'], $return_id, $data['surcharge']);
        //     $this->site->syncQuantity(NULL, NULL, $purchase_items);
        //     $this->site->syncQuantity(NULL, $data['purchase_id']);
        //     return true;
        // }
        // return false;
    }
    public function calculatePurchaseTotals($id, $return_id, $surcharge){
        $purchase = $this->getPurchaseByID($id);
        $items = $this->getAllPurchaseItems($id);
        if (!empty($items)) {
            $total = 0;
            $product_tax = 0;
            $order_tax = 0;
            $product_discount = 0;
            $order_discount = 0;
            foreach ($items as $item) {
                $product_tax += $item->item_tax;
                $product_discount += $item->item_discount;
                $total += $item->net_unit_cost * $item->quantity;
            }
            if ($purchase->order_discount_id) {
                $percentage = '%';
                $order_discount_id = $purchase->order_discount_id;
                $opos = strpos($order_discount_id, $percentage);
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = (($total + $product_tax) * (Float)($ods[0])) / 100;
                } else {
                    $order_discount = $order_discount_id;
                }
            }
            if ($purchase->order_tax_id) {
                $order_tax_id = $purchase->order_tax_id;
                if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                    if ($order_tax_details->type == 2) {
                        $order_tax = $order_tax_details->rate;
                    }
                    if ($order_tax_details->type == 1) {
                        $order_tax = (($total + $product_tax - $order_discount) * $order_tax_details->rate) / 100;
                    }
                }
            }
            $total_discount = $order_discount + $product_discount;
            $total_tax = $product_tax + $order_tax;
            $grand_total = $total + $total_tax + $purchase->shipping - $order_discount + $surcharge;
            $data = array(
                'total' => $total,
                'product_discount' => $product_discount,
                'order_discount' => $order_discount,
                'total_discount' => $total_discount,
                'product_tax' => $product_tax,
                'order_tax' => $order_tax,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'return_id' => $return_id,
                'surcharge' => $surcharge
            );

            if ($this->db->update('purchases', $data, array('id' => $id))) {
                return true;
            }
        } else {
            $this->db->delete('purchases', array('id' => $id));
        }
        return FALSE;
    }
    public function getExpenseCategories(){
        $q = $this->db->get('expense_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getExpenseCategoryByID($id){
        $q = $this->db->get_where("expense_categories", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function check_batch_already_sale($item_code, $batch_number){
        $q = $this->db->query("SELECT * FROM `sma_purchase_items` WHERE product_code = '".$item_code."' and batch = '".$batch_number."'");
        if ($q->num_rows() > 0) {
            return true;
        }
        return FALSE;
    }
    public function check_reference_already_exits($check_reference_already_exits){
        $q = $this->db->query("SELECT * FROM `sma_purchases` WHERE reference_no = '".$check_reference_already_exits."'");
        if ($q->num_rows() > 0) {
            return true;
        }
        return FALSE;
    }
    public function updateAVCO($data){
        if ($wp_details = $this->getWarehouseProductQuantity($data['warehouse_id'], $data['product_id'])) {
            $total_cost = (($wp_details->quantity * $wp_details->avg_cost) + ($data['quantity'] * $data['cost']));
            $total_quantity = $wp_details->quantity + $data['quantity'];
            if (!empty($total_quantity)) {
                $avg_cost = ($total_cost / $total_quantity);
                $this->db->update('warehouses_products', array('avg_cost' => $avg_cost), array('product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id']));
            }
        } else {
            $this->db->insert('warehouses_products', array('product_id' => $data['product_id'], 'warehouse_id' => $data['warehouse_id'], 'avg_cost' => $data['cost'], 'quantity' => 0));
        }
    }
    public function getitems($pid){
        $sendvalue = array();

        $this->db->select('*');
        $this->db->from('sma_purchase_items');
        $this->db->where('quantity_balance >','0.0000');
        $this->db->where('purchase_id',$pid);
        $q = $this->db->get();
        $sendvalue = $q->result();
        
        return $sendvalue;
    }
    // ------------------------New Code By Ismail FSD-----------------------//
    public function reuturns(){
        $sendvalue = array();
        $this->db->select('
            sma_purchase_return_tb.id,
            sma_purchase_return_tb.return_date,
            sma_purchases.reference_no as p_reference_no,
            sma_purchases.supplier as supplier_name,
            sma_purchase_return_tb.reference_no,
            sma_purchase_return_tb.subtotal as total,
            sma_purchase_return_tb.surcharge as surecharge,
            sma_purchase_return_tb.grand_total as grand_total,
            sma_purchase_return_tb.reference_no as paid,
            sma_purchase_return_tb.reference_no as balance,
            sma_purchase_return_tb.reference_no as payment_status,
        ');
        $this->db->from('sma_purchase_return_tb');
        $this->db->join('sma_purchases', 'sma_purchases.id = sma_purchase_return_tb.purchase_id', 'left');
        $q = $this->db->get();
        $sendvalue = $q->result();
        return $sendvalue;
    }
    public function reuturn_data($id){
        $sendvalue->codestats = "no";
        $this->db->select('
            sma_purchase_return_tb.*,
            sma_purchases.supplier_id as p_supplier_id,
            sma_purchases.reference_no as p_reference_no,
            sma_purchases.warehouse_id as p_warehouse_id,
            sma_companies.name as supper_name,
            sma_companies.address as supper_address,
            sma_companies.phone as supper_phone,
            sma_companies.email as supper_email,
            sma_warehouses.name as warehouse_name,
            sma_warehouses.code as warehouse_code,
            sma_warehouses.address as warehouse_address,
            sma_warehouses.phone as warehouse_phone,
            sma_warehouses.email as warehouse_email,
            sma_users.first_name as creater_fname,
            sma_users.last_name as creater_lname,
        ');
        $this->db->from('sma_purchase_return_tb');
        $this->db->join('sma_purchases', 'sma_purchases.id = sma_purchase_return_tb.purchase_id', 'left');
        $this->db->join('sma_companies', 'sma_companies.id = sma_purchases.supplier_id', 'left');
        $this->db->join('sma_warehouses', 'sma_warehouses.id = sma_purchases.warehouse_id', 'left');
        $this->db->join('sma_users', 'sma_users.id = sma_purchase_return_tb.created_by', 'left');
        $this->db->where('sma_purchase_return_tb.id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $res = $q->result();
            $sendvalue = $res[0];

            $this->db->select('sma_purchase_return_items_tb.*,sma_products.name,sma_products.code,sma_purchase_items.expiry');
            $this->db->from('sma_purchase_return_items_tb');
            $this->db->join('sma_products', 'sma_products.id = sma_purchase_return_items_tb.product_id', 'left');
            $this->db->join('sma_purchase_items', 'sma_purchase_items.id = sma_purchase_return_items_tb.purchase_item_id', 'left');
            $this->db->where('purchase_return_id',$res[0]->id);
            $q = $this->db->get();
            $sendvalue->items = $q->result();
            $sendvalue->codestats = "ok";
        }
        // print_r($sendvalue);
        // exit();
        return $sendvalue;
    }
    public function getPurchaseItemData($id){
        $this->db->select('*');
        $this->db->from('sma_purchase_items');
        $this->db->where('id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            return $q->result()[0];
        }
        else{
            return false;
        }
    }
    public function addreturn($insert,$items){
        $sendvalue['status'] = false;
        $this->db->insert('sma_purchase_return_tb', $insert);
        $return_id = $this->db->insert_id();
        foreach($items as $item){
            $item['purchase_return_id'] = $return_id;
            $this->db->insert('sma_purchase_return_items_tb', $item);
            $this->updateQty($item['product_id'],$item['warehouse_id'],$item['purchase_item_id'],$item['quantity'],'-');


        }
        $sendvalue['status'] = true;
        $sendvalue['message'] = "Return Successfully";
        return $sendvalue;
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
    public function returnItems($id){
        $this->db->select('
            sma_products.code as product_code,
            sma_products.name as product_name,
            sma_products.price,
            sma_tax_rates.code as tax_code,
            sma_tax_rates.type as tax_type,
            sma_tax_rates.rate as tax_rate,
            sma_purchase_return_items_tb.*
        ');
        $this->db->from('sma_purchase_return_items_tb');
        $this->db->join('sma_purchase_return_tb','sma_purchase_return_tb.id = sma_purchase_return_items_tb.purchase_return_id');
        $this->db->join('sma_products','sma_products.id = sma_purchase_return_items_tb.product_id');
        $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_purchase_return_items_tb.item_tax_id');
        $this->db->where('sma_purchase_return_tb.purchase_id',$id);
        $q = $this->db->get();
        return  $q->result();
    }
    public function changetotal($id){
        $purchaseq = $this->db->select('*')->from('purchases')->where('id',$id)->get();
        if($purchaseq->num_rows() > 0){
            $purchase = $purchaseq->result()[0];
            $items = $this->db->select('
                net_unit_cost,
                quantity,
                item_tax,
                adv_tax,
                discount,
                item_discount,
                subtotal,
                further_tax,
                fed_tax,
                gst_tax
            ')->from('purchase_items')->where('purchase_id',$id)->get()->result();
            $set['total'] = 0;
            $set['product_discount'] = 0;
            $set['total_discount'] = 0;
            
            $set['product_tax'] = 0;
            $set['total_adv_tax'] = 0;
            $set['total_tax'] = 0;
            $set['shipping'] = 0;
            $set['grand_total'] = 0;

            foreach($items as $item){
                $set['total'] += $item->quantity*$item->net_unit_cost;
                $set['product_discount'] += $item->item_discount;
                $set['product_tax'] += $item->item_tax;
                $set['total_adv_tax'] += $item->adv_tax;
            }
            $set['total_discount'] = $purchase->order_discount+$set['product_discount'];
            $set['total_tax'] = $purchase->order_tax+$set['total_adv_tax']+$set['product_tax'];
            $set['grand_total'] = $this->sma->formatDecimal(($set['total'] + $set['total_tax'] + $this->sma->formatDecimal($purchase->shipping) - $set['total_discount']), 4);
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('purchases');
        }
    }
}
