<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transfers_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getProductNames($term, $warehouse_id, $limit = 5)
    {
        $this->db->select('products.id, code, name, warehouses_products.quantity, cost, tax_rate, type, unit, purchase_unit, tax_method')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('products.id');
        if ($this->Settings->overselling) {
            $this->db->where("type = 'standard' AND (name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        } else {
            $this->db->where("type = 'standard' AND warehouses_products.warehouse_id = '" . $warehouse_id . "' AND warehouses_products.quantity > 0 AND "
                . "(name LIKE '%" . $term . "%' OR code LIKE '%" . $term . "%' OR  concat(name, ' (', code, ')') LIKE '%" . $term . "%')");
        }
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }


    public function getAllBatchNumber($product_code){
        $this->db->select();
        return $product_code;
    }

    public function getAllDiscount($product_id, $supplier_id){

        $this->db->save_queries = TRUE;


        $query = $this->db->query('select * from sma_bulk_discount where (CURDATE() between start_date and end_date) and (find_in_set('.$supplier_id.',supplier_id) OR find_in_set('.$product_id.',product_id)  <> 0)');

        // echo 'select * from sma_bulk_discount where (CURDATE() between start_date and end_date) and (find_in_set('.$supplier_id.',supplier_id) OR find_in_set('.$product_id.',product_id)  <> 0)';
        // die();
        // exit();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return FALSE;
    }

    public function getWHProduct($id)
    {
        $this->db->select('products.id, code, name, warehouses_products.quantity, cost, tax_rate')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('products.id');
        $q = $this->db->get_where('products', array('warehouses_products.product_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function addTransfer($data = array(), $items = array())
    {
        $status = $data['status'];
        if ($this->db->insert('transfers', $data)) {
            $transfer_id = $this->db->insert_id();
            if ($this->site->getReference('to') == $data['transfer_no']) {
                $this->site->updateReference('to');
            }
            foreach ($items as $item) {
                $ppid = $item['purchaseitemid'];
                unset($item['purchaseitemid']);
                $item['transfer_id'] = $transfer_id;
                $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : NULL;
                if ($status == 'completed') {
                    $item['date'] = date('Y-m-d');
                    $item['warehouse_id'] = $data['to_warehouse_id'];
                    $item['status'] = 'received';
                    $this->db->insert('purchase_items', $item);

                    $this->db->set('quantity_balance', 'quantity_balance-'.$item['quantity_received'], FALSE);
                    $this->db->set('quantity_received', 'quantity_received-'.$item['quantity_received'], FALSE);
                    $this->db->where('id', $ppid);
                    $this->db->update('purchase_items');
            
                    $this->updateStockQty($item['quantity_received'],$data['from_warehouse_id'],$item['product_id'],'-');
                    $this->updateStockQty($item['quantity_received'],$data['to_warehouse_id'],$item['product_id'],'+');

                } else {
                    $this->db->insert('transfer_items', $item);
                }

                // if ($status == 'sent' || $status == 'completed') {
                //     $this->syncTransderdItem($item['product_id'], $data['from_warehouse_id'], $item['quantity'], $item['option_id'],$ppid);
                // }

                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item['product_id'],$data['from_warehouse_id'],0,"Add Transfer");
                $this->stores_model->updateStoreQty($item['product_id'],$data['to_warehouse_id'],0,"Add Transfer");
                    
            }

            return true;
        }
        return false;
    }
    public function updateStockQty($qty,$w_id,$p_id,$type){


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

    public function updateTransfer($id, $data = array(), $items = array())
    {
        $ostatus = $this->resetTransferActions($id);
        $status = $data['status'];

        if ($this->db->update('transfers', $data, array('id' => $id))) {
            $tbl = $ostatus == 'completed' ? 'purchase_items' : 'transfer_items';
            $this->db->delete($tbl, array('transfer_id' => $id));

            foreach ($items as $item) {
                $item['transfer_id'] = $id;
                $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : NULL;
                if ($status == 'completed') {
                    $item['date'] = date('Y-m-d');
                    $item['warehouse_id'] = $data['to_warehouse_id'];
                    $item['status'] = 'received';
                    $this->db->insert('purchase_items', $item);
                } else {
                    $this->db->insert('transfer_items', $item);
                }

                if ($data['status'] == 'sent' || $data['status'] == 'completed') {
                    $this->syncTransderdItem($item['product_id'], $data['from_warehouse_id'], $item['quantity'], $item['option_id']);
                }

                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item['product_id'],$data['from_warehouse_id'],0,"Update Transfer");
                $this->stores_model->updateStoreQty($item['product_id'],$data['to_warehouse_id'],0,"Update Transfer");

            }

            return true;
        }

        return false;
    }

    public function updateStatus($id, $status, $note)
    {
        $ostatus = $this->resetTransferActions($id);
        $transfer = $this->getTransferByID($id);
        $items = $this->getAllTransferItems($id, $transfer->status);

        if ($this->db->update('transfers', array('status' => $status, 'note' => $note), array('id' => $id))) {
            $tbl = $ostatus == 'completed' ? 'purchase_items' : 'transfer_items';
            $this->db->delete($tbl, array('transfer_id' => $id));

            foreach ($items as $item) {
                $item = (array) $item;
                $item['transfer_id'] = $id;
                $item['option_id'] = !empty($item['option_id']) && is_numeric($item['option_id']) ? $item['option_id'] : NULL;
                unset($item['id'], $item['variant'], $item['unit'], $item['hsn_code'], $item['second_name']);
                if ($status == 'completed') {
                    $item['date'] = date('Y-m-d');
                    $item['warehouse_id'] = $transfer->to_warehouse_id;
                    $item['status'] = 'received';
                    $this->db->insert('purchase_items', $item);
                } else {
                    $this->db->insert('transfer_items', $item);
                }

                if ($status == 'sent' || $status == 'completed') {
                    $this->syncTransderdItem($item['product_id'], $transfer->from_warehouse_id, $item['quantity'], $item['option_id']);
                } else {
                    $this->site->syncQuantity(NULL, NULL, NULL, $item['product_id']);
                }
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item['product_id'],$transfer->from_warehouse_id,0,"Update Transfer Status");
                $this->stores_model->updateStoreQty($item['product_id'],$transfer->to_warehouse_id,0,"Update Transfer Status");
            }
            return true;
        }
        return false;
    }

    public function getProductWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchaseProductDetails($product_id, $item_batch)
    {
        // $this->db->save_queries = TRUE;

        //$q = $this->db->get_where('purchase_items', array('product_id' => $product_id, 'batch' => $item_batch, 'transfer_id is NULL'));

        $q = $this->db->query('SELECT * FROM `sma_purchase_items` WHERE `product_id` = "'.$product_id.'" AND `batch` = "'.$item_batch.'"');

        //echo $this->db->last_query();
        // die();
        // exit();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        

    }

    

    public function getProductByCategoryID($id)
    {

        $q = $this->db->get_where('products', array('category_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return true;
        }

        return FALSE;
    }

    public function getProductQuantity($product_id, $warehouse = DEFAULT_WAREHOUSE)
    {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse), 1);
        if ($q->num_rows() > 0) {
            return $q->row_array(); //$q->row();
        }
        return FALSE;
    }

    public function insertQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($this->db->insert('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $quantity))) {
            $this->site->syncProductQty($product_id, $warehouse_id);
            $this->load->model('admin/stores_model');
            $this->stores_model->updateStoreQty($product_id, $warehouse_id,0,"Insert Quantity Transfer");
        return true;
        }
        return false;
    }

    public function updateQuantity($product_id, $warehouse_id, $quantity)
    {
        if ($this->db->update('warehouses_products', array('quantity' => $quantity), array('product_id' => $product_id, 'warehouse_id' => $warehouse_id))) {
            $this->site->syncProductQty($product_id, $warehouse_id);
            $this->load->model('admin/stores_model');
            $this->stores_model->updateStoreQty($product_id, $warehouse_id,0,"Update Quantity Transfer");
            return true;
        }
        return false;
    }

    public function getProductByCode($code)
    {

        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getProductByName($name)
    {

        $q = $this->db->get_where('products', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getTransferByID($id)
    {

        $q = $this->db->get_where('transfers', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getAllTransferItems($transfer_id, $status)
    {
        if ($status == 'completed') {
            $this->db->select('purchase_items.*, product_variants.name as variant, products.unit, products.hsn_code as hsn_code, products.second_name as second_name')
                ->from('purchase_items')
                ->join('products', 'products.id=purchase_items.product_id', 'left')
                ->join('product_variants', 'product_variants.id=purchase_items.option_id', 'left')
                ->group_by('purchase_items.id')
                ->where('transfer_id', $transfer_id);
        } else {
            $this->db->select('transfer_items.*, product_variants.name as variant, products.unit, products.hsn_code as hsn_code, products.second_name as second_name')
                ->from('transfer_items')
                ->join('products', 'products.id=transfer_items.product_id', 'left')
                ->join('product_variants', 'product_variants.id=transfer_items.option_id', 'left')
                ->group_by('transfer_items.id')
                ->where('transfer_id', $transfer_id);
        }
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getWarehouseProduct($warehouse_id, $product_id, $variant_id)
    {
        if ($variant_id) {
            return $this->getProductWarehouseOptionQty($variant_id, $warehouse_id);
        } else {
            return $this->getWarehouseProductQuantity($warehouse_id, $product_id);
        }
        return FALSE;
    }

    public function getWarehouseProductQuantity($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', array('warehouse_id' => $warehouse_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function resetTransferActions($id, $delete = NULL)
    {
        $otransfer = $this->getTransferByID($id);
        $oitems = $this->getAllTransferItems($id, $otransfer->status);

        // $this->sma->print_arrays($otransfer, $oitems);

        $ostatus = $otransfer->status;
        if ($ostatus == 'sent' || $ostatus == 'completed') {
            foreach ($oitems as $item) {
                $option_id = (isset($item->option_id) && ! empty($item->option_id)) ? $item->option_id : NULL;
                $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $item->product_id, 'warehouse_id' => $otransfer->from_warehouse_id, 'option_id' => $option_id, 'batch' => $item->batch );
                
                // print_r($clause);
                // die();
                // exit();

                // $this->site->setPurchaseItem($clause, $item->quantity);
                $this->site->setPurchaseItemAfterTransferDelete($clause, $item->quantity);
                
                // echo $delete;
                // die();
                // exit();
                
                if ($delete) {
                    $option_id = (isset($item->option_id) && ! empty($item->option_id)) ? $item->option_id : NULL;
                    $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $item->product_id, 'warehouse_id' => $otransfer->to_warehouse_id, 'option_id' => $option_id);
                    // $this->site->setPurchaseItem($clause, ($item->quantity_balance - $item->quantity));
                    $this->site->setPurchaseItemAfterDelete($clause, ($item->quantity_balance - $item->quantity));
                }
            }
        }
        return $ostatus;
    }

    public function deleteTransfer($id)
    {
        $ostatus = $this->resetTransferActions($id, 1);
        $oitems = $this->getAllTransferItems($id, $ostatus);
        $tbl = $ostatus == 'completed' ? 'purchase_items' : 'transfer_items';
        if ($this->db->delete('transfers', array('id' => $id)) && $this->db->delete($tbl, array('transfer_id' => $id))) {
            foreach ($oitems as $item) {
                $this->site->syncQuantity(NULL, NULL, NULL, $item->product_id);
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item->product_id,$item->warehouse_id,0,"Delete Transfer");
            }
            return true;
        }
        return FALSE;
    }

    public function getProductOptions($product_id, $warehouse_id, $zero_check = TRUE)
    {
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.cost as cost, product_variants.quantity as total_quantity, warehouses_products_variants.quantity as quantity')
            ->join('warehouses_products_variants', 'warehouses_products_variants.option_id=product_variants.id', 'left')
            ->where('product_variants.product_id', $product_id)
            ->where('warehouses_products_variants.warehouse_id', $warehouse_id)
            ->group_by('product_variants.id');
        if ($zero_check) {
            $this->db->where('warehouses_products_variants.quantity >', 0);
        }
        $q = $this->db->get('product_variants');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getProductComboItems($pid, $warehouse_id)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->where('warehouses_products.warehouse_id', $warehouse_id)
            ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', array('combo_items.product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function getProductVariantByName($name, $product_id)
    {
        $q = $this->db->get_where('product_variants', array('name' => $name, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function check_purchase_transfer($id)
    {
        // $this->db->save_queries = TRUE;

        $q = $this->db->get_where('purchase_items', array('transfer_id' => $id));
        
        // echo $this->db->last_query();
        
        // die();
        // exit();
        
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }

    public function check_sales($id, $batch, $warehouse_id)
    {
        //foreach ($items as $key => $value) {
            // $this->db->save_queries = TRUE;

            $q = $this->db->get_where('sale_items', array('batch' => $batch, 'product_id' => $id, 'warehouse_id' => $warehouse_id), 1);
            
            // echo $this->db->last_query();
            
            // die();
            // exit();
            if ($q->num_rows() > 0) {
                return $q->num_rows();
            }
            return false;
        //}
    }

    public function syncTransderdItem($product_id, $warehouse_id, $quantity, $option_id = NULL,$ppid)
    {
        if ($pis = $this->site->getPurchasedItems($product_id, $warehouse_id, $option_id)) {
            $balance_qty = $quantity;
            foreach ($pis as $pi) {
                if ($balance_qty <= $quantity && $quantity > 0) {
                    if ($pi->quantity_balance >= $quantity) {
                        $balance_qty = $pi->quantity_balance - $quantity;
                        $this->db->update('purchase_items', array('quantity_balance' => $balance_qty), array('id' => $pi->id));
                        $quantity = 0;
                    } elseif ($quantity > 0) {
                        $quantity = $quantity - $pi->quantity_balance;
                        $balance_qty = $quantity;
                        $this->db->update('purchase_items', array('quantity_balance' => 0), array('id' => $pi->id));
                    }
                }
                if ($quantity == 0) { break; }
            }
        } else {
            $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'option_id' => $option_id);
            $this->site->setPurchaseItem($clause, (0-$quantity));
        }
        $this->site->syncQuantity(NULL, NULL, NULL, $product_id);
    }

    public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

}
