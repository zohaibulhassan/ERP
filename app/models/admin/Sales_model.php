<?php defined('BASEPATH') or exit('No direct script access allowed');

class Sales_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getProductNames($term, $warehouse_id, $supplier_id, $limit = 5)
    {
        // Warehouse Query
        $q = $this->db->query('
                                SELECT 
                                    GROUP_CONCAT(sma_products.id) AS product_id,
                                    GROUP_CONCAT(sma_purchase_items.`id`) AS purchase_item_id, 
                                    GROUP_CONCAT(sma_purchase_items.`purchase_id`) AS purchase_id, 
                                    GROUP_CONCAT(sma_purchase_items.`batch`) AS batch, 
                                    GROUP_CONCAT(sma_purchase_items.`expiry`) AS expiry, 
                                    GROUP_CONCAT(sma_purchase_items.`price`) AS product_price, 
                                    GROUP_CONCAT(sma_purchase_items.`dropship`) AS product_dropship, 
                                    GROUP_CONCAT(sma_purchase_items.`crossdock`) AS product_crossdock, 
                                    GROUP_CONCAT(sma_purchase_items.`mrp`) AS product_mrp, 
                                    GROUP_CONCAT(sma_purchase_items.`quantity`) AS product_batch_quantity,
                                    GROUP_CONCAT(sma_purchase_items.`quantity_balance`) AS product_batch_balance_quantity, 
                                    GROUP_CONCAT(sma_purchase_items.`fed_tax`) AS fed_tax_rate, 
                                    sma_products.id, 
                                    sma_products.code, 
                                    sma_products.company_code, 
                                    sma_products.name, 
                                    sma_products.unit, 
                                    sma_products.cost, 
                                    sma_products.price, 
                                    sma_products.dropship, 
                                    sma_products.crossdock, 
                                    sma_products.mrp, 
                                    sma_products.discount_mrp, 
                                    sma_products.alert_quantity, 
                                    sma_products.track_quantity, 
                                    sma_products.quantity, 
                                    sma_products.tax_rate, 
                                    sma_products.type, 
                                    sma_products.warehouse, 
                                    sma_products.tax_method, 
                                    sma_products.company_prices_and_names, 
                                    sma_products.discount_one, 
                                    sma_products.discount_two, 
                                    sma_products.discount_three, 
                                    sma_products.adv_tax_reg, 
                                    sma_products.adv_tax_nonreg, 
                                    sma_products.fed_tax, 
                                    FWP.quantity AS quantity_useless 
                                FROM sma_purchase_items 
                                LEFT JOIN sma_products ON sma_purchase_items.product_id = sma_products.id 
                                LEFT JOIN sma_purchases ON sma_purchase_items.purchase_id = sma_purchases.id 
                                LEFT JOIN( 
                                        SELECT 
                                            product_id, 
                                            warehouse_id, 
                                            quantity 
                                        FROM 
                                            sma_warehouses_products 
                                        GROUP BY 
                                            product_id 
                                    ) FWP ON `FWP`.`product_id` = `sma_products`.`id` 
                                WHERE (
                                        (
                                            `sma_products`.`supplier1` = ' . $supplier_id . ' OR 
                                            `sma_products`.`supplier2` = ' . $supplier_id . ' OR 
                                            `sma_products`.`supplier3` = ' . $supplier_id . ' OR 
                                            `sma_products`.`supplier4` = ' . $supplier_id . ' OR 
                                            `sma_products`.`supplier5` = ' . $supplier_id . '
                                        )
                                    ) AND
                                    (`sma_purchase_items`.`quantity_balance` > 0) AND
                                    (`sma_purchase_items`.`warehouse_id` = ' . $warehouse_id . ' ) AND  
                                    ( 
                                        `sma_products`.`name` LIKE "%' . $term . '%" OR 
                                        `sma_products`.`code` LIKE "%' . $term . '%" 
                                    ) AND 
                                    (
                                        `sma_purchases`.`status` = "received" OR 
                                        `sma_purchases`.`status` = "partial" OR 
                                        `sma_purchase_items`.`status` = "received"
                                    ) 
                                GROUP BY (sma_products.id)
                            ');
        if ($q->num_rows() > 0) {

            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }


    public function check_reference_already_exits($check_reference_already_exits)
    {
        $q = $this->db->query("SELECT * FROM `sma_sales` WHERE reference_no = '" . $check_reference_already_exits . "'");
        if ($q->num_rows() > 0) {
            return true;
        }
        return FALSE;
    }

    public function getAllBatchNumber($product_code)
    {
        $this->db->select();
        return $product_code;
    }

    public function getAllDiscount($product_id, $supplier_id)
    {
        $query = $this->db->query('select * from sma_bulk_discount where (CURDATE() between start_date and end_date) and (find_in_set(' . $supplier_id . ',supplier_id) OR find_in_set(' . $product_id . ',product_id)  <> 0)');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return FALSE;
    }


    public function further_tax($q = NULL)
    {
        $query = $this->db->query('SELECT further_tax from sma_settings');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return FALSE;
    }

    public function getProductComboItems($pid, $warehouse_id = NULL)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name,products.type as type, warehouses_products.quantity as quantity')
            ->join('products', 'products.code=combo_items.item_code', 'left')
            ->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')
            ->group_by('combo_items.id');
        if ($warehouse_id) {
            $this->db->where('warehouses_products.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('combo_items', array('combo_items.product_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function getProductByCode($code)
    {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function syncQuantity($sale_id)
    {
        if ($sale_items = $this->getAllInvoiceItems($sale_id)) {
            foreach ($sale_items as $item) {
                $this->site->syncProductQty($item->product_id, $item->warehouse_id);
                if (isset($item->option_id) && !empty($item->option_id)) {
                    $this->site->syncVariantQty($item->option_id, $item->warehouse_id);
                }
            }
        }
    }

    public function getProductQuantity($product_id, $warehouse)
    {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse), 1);
        if ($q->num_rows() > 0) {
            return $q->row_array();
        }
        return FALSE;
    }

    public function getProductOptions($product_id, $warehouse_id, $all = NULL)
    {
        $wpv = "( SELECT option_id, warehouse_id, quantity from {$this->db->dbprefix('warehouses_products_variants')} WHERE product_id = {$product_id}) FWPV";
        $this->db->select('product_variants.id as id, product_variants.name as name, product_variants.price as price, product_variants.quantity as total_quantity, FWPV.quantity as quantity', FALSE)
            ->join($wpv, 'FWPV.option_id=product_variants.id', 'left')
            ->where('product_variants.product_id', $product_id)
            ->group_by('product_variants.id');

        if (!$this->Settings->overselling && !$all) {
            $this->db->where('FWPV.warehouse_id', $warehouse_id);
            $this->db->where('FWPV.quantity >', 0);
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

    public function getProductVariants($product_id)
    {
        $q = $this->db->get_where('product_variants', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getItemByID($id)
    {

        $q = $this->db->get_where('sale_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function getAllInvoiceItems($sale_id, $return_id = NULL)
    {
        $this->db->select('
            `sma_purchase_items_two`.`unit_quantity` AS 
            `get_selected_product_quantities1`,
            `sma_purchase_items_two`.`quantity_balance` AS `get_selected_product_quantities`,    
            `sma_purchase_items_two`.`batch` AS `sma_purchase_items_batch`,
            `sma_purchase_items_one`.`unit_quantity` AS `get_selected_product_unit_quantity1`,
            `sma_purchase_items_one`.`quantity_balance` AS `get_selected_product_quantity_balance`,    
            `sma_purchase_items_one`.`batch` AS `sma_purchase_items_one_batch`, 
            `sma_companies`.`sales_type` AS `sma_companies_sales_type`, 
            `sma_companies`.`gst_no` AS `gst_no` ,
            `sma_sales`.`id` AS `sma_sales_id`,
            `sma_sales`.`customer_id` AS `sma_sales_customer_id`,
            `sma_sales`.`own_company` AS `sma_sales_own_company`, 
            sale_items.*, 
            sale_items.`batch` as `selected_batch`, 
            sale_items.`expiry` as `selected_expiry`,  
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.id)) as purchase_item_id, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`purchase_id`)) as purchase_id, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`batch`))  as batch, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`expiry`)) as expiry, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`price`)) as product_price, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`dropship`)) as product_dropship, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`crossdock`)) as product_crossdock, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`mrp`)) as product_mrp, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`quantity`)) as product_batch_quantity, 
            tax_rates.code as tax_code, 
            tax_rates.type as tax_type, 
            tax_rates.name as tax_name, 
            tax_rates.rate as tax_rate, 
            products.pack_size, 
            products.carton_size, 
            products.weight, 
            products.image, 
            products.details as details, 
            product_variants.name as variant, 
            products.hsn_code as hsn_code, 
            products.second_name as second_name
        ')
        ->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
        ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
        ->join('sma_sales', 'sma_sales.id=sma_sale_items.sale_id', 'left')
        ->join('sma_companies', 'sma_sales.customer_id=sma_companies.id', 'left')
        ->join('purchase_items as sma_purchase_items_one', 'sma_purchase_items_one.product_id=sma_products.id', 'left')
        ->join('sma_purchase_items as sma_purchase_items_two', 'sma_purchase_items_two.batch = sma_sale_items.batch', 'left')
        ->group_by('sale_items.id')
        ->order_by('product_id', 'asc');
        if ($sale_id && !$return_id) {
            $this->db->where('sma_sale_items.sale_id', $sale_id);
        } elseif ($return_id) {
            $this->db->where('sma_sale_items.sale_id', $return_id);
        }
        $q = $this->db->get('sale_items');

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
    public function getAllInvoiceItemsWithReturn($sale_id, $return_id = NULL)
    {
        $this->db->select('
            `sma_purchase_items_two`.`unit_quantity` AS 
            `get_selected_product_quantities1`,
            `sma_purchase_items_two`.`quantity_balance` AS `get_selected_product_quantities`,    
            `sma_purchase_items_two`.`batch` AS `sma_purchase_items_batch`,
            `sma_purchase_items_one`.`unit_quantity` AS `get_selected_product_unit_quantity1`,
            `sma_purchase_items_one`.`quantity_balance` AS `get_selected_product_quantity_balance`,    
            `sma_purchase_items_one`.`batch` AS `sma_purchase_items_one_batch`, 
            `sma_companies`.`sales_type` AS `sma_companies_sales_type`, 
            `sma_companies`.`gst_no` AS `gst_no` ,
            `sma_sales`.`id` AS `sma_sales_id`,
            `sma_sales`.`customer_id` AS `sma_sales_customer_id`,
            `sma_sales`.`own_company` AS `sma_sales_own_company`, 
            sale_items.*, 
            sale_items.`batch` as `selected_batch`, 
            sale_items.`expiry` as `selected_expiry`,  
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.id)) as purchase_item_id, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`purchase_id`)) as purchase_id, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`batch`))  as batch, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`expiry`)) as expiry, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`price`)) as product_price, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`dropship`)) as product_dropship, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`crossdock`)) as product_crossdock, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`mrp`)) as product_mrp, 
            GROUP_CONCAT(DISTINCT(sma_purchase_items_one.`quantity`)) as product_batch_quantity, 
            tax_rates.code as tax_code, 
            tax_rates.type as tax_type, 
            tax_rates.name as tax_name, 
            tax_rates.rate as tax_rate, 
            products.pack_size, 
            products.carton_size, 
            products.weight, 
            products.image, 
            products.details as details, 
            product_variants.name as variant, 
            products.hsn_code as hsn_code, 
            products.second_name as second_name
        ')
        ->join('products', 'products.id=sale_items.product_id', 'left')
        ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
        ->join('tax_rates', 'tax_rates.id=sale_items.tax_rate_id', 'left')
        ->join('sma_sales', 'sma_sales.id=sma_sale_items.sale_id', 'left')
        ->join('sma_companies', 'sma_sales.customer_id=sma_companies.id', 'left')
        ->join('purchase_items as sma_purchase_items_one', 'sma_purchase_items_one.product_id=sma_products.id', 'left')
        ->join('sma_purchase_items as sma_purchase_items_two', 'sma_purchase_items_two.batch = sma_sale_items.batch', 'left')
        ->group_by('sale_items.id')
        ->order_by('product_id', 'asc');
        if ($sale_id && !$return_id) {
            $this->db->where('sma_sale_items.sale_id', $sale_id);
        } elseif ($return_id) {
            $this->db->where('sma_sale_items.sale_id', $return_id);
        }
        $this->db->where('sale_items.quantity != ', '0');
        $q = $this->db->get('sale_items');

        // echo $this->db->last_query();
        // die();
        // exit();

        if ($q->num_rows() > 0) {
            $data = array();
            foreach (($q->result()) as $row) {
                $returnData = $this->returnData($row->sale_id,$row->product_id,$row->selected_batch);
                if($returnData['status']){
                    $row->unit_quantity = $row->unit_quantity-$returnData['data']->quantity;
                    if($row->unit_quantity > 0){
                        $data[] = $row;
                    }
                }
                else{
                    $data[] = $row;
                }
            }
            return $data;
        }
        return FALSE;
    }
    public function returnData($sid,$pid,$batch){
        $senddata['status'] = false;
        $this->db->select('sma_sale_returns_tb.sale_id,sma_sale_return_items_tb.*');
        $this->db->from('sma_sale_return_items_tb');
        $this->db->join('sma_sale_returns_tb','sma_sale_returns_tb.id = sma_sale_return_items_tb.sale_return_id','left');
        $this->db->where('sma_sale_returns_tb.sale_id',$sid);
        $this->db->where('sma_sale_return_items_tb.product_id',$pid);
        $this->db->where('sma_sale_return_items_tb.batch',$batch);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $senddata['data'] = $q->result()[0];
            $senddata['status'] = true;
        }
        return $senddata;
    }

    public function getAllInvoiceItemsWithDetails($sale_id)
    {
        $this->db->select('sale_items.*, products.details, product_variants.name as variant');
        $this->db->join('products', 'products.id=sale_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=sale_items.option_id', 'left')
            ->group_by('sale_items.id');
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getInvoiceByID($id)
    {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getReturnByID($id)
    {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getReturnBySID($sale_id)
    {
        $q = $this->db->get_where('sales', array('sale_id' => $sale_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductOptionByID($id)
    {
        $q = $this->db->get_where('product_variants', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getProductOptionByID($option_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('product_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function addOptionQuantity($option_id, $quantity)
    {
        if ($option = $this->getProductOptionByID($option_id)) {
            $nq = $option->quantity + $quantity;
            if ($this->db->update('product_variants', array('quantity' => $nq), array('id' => $option_id))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getProductWarehouseOptionQty($option_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateProductOptionQuantity($option_id, $warehouse_id, $quantity, $product_id)
    {
        if ($option = $this->getProductWarehouseOptionQty($option_id, $warehouse_id)) {
            $nq = $option->quantity - $quantity;
            if ($this->db->update('warehouses_products_variants', array('quantity' => $nq), array('option_id' => $option_id, 'warehouse_id' => $warehouse_id))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        } else {
            $nq = 0 - $quantity;
            if ($this->db->insert('warehouses_products_variants', array('option_id' => $option_id, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'quantity' => $nq))) {
                $this->site->syncVariantQty($option_id, $warehouse_id);
                return TRUE;
            }
        }
        return FALSE;
    }




    public function addSale($data = array(), $items = array(), $payment = array(), $si_return = array())
    {
        if (empty($si_return)) {
            // Yaha double value pass karwani hy
            // $cost = $this->site->costing($items);
            //$cost[0] = $items[0]['warehouse_id'];
        }

        $this->db->trans_start();
        
        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            if ($this->site->getReference('so') == $data['reference_no']) {
                $this->site->updateReference('so');
            }


            foreach ($items as $item) {

                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && empty($si_return)) {
                //     $item_costs = $this->site->item_costing($item);
                //     foreach ($item_costs as $item_cost) {
                //         if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
                //             $item_cost['sale_item_id'] = $sale_item_id;
                //             $item_cost['sale_id'] = $sale_id;
                //             $item_cost['date'] = date('Y-m-d', strtotime($data['date']));
                //             if (!isset($item_cost['pi_overselling'])) {
                //                 $this->db->insert('costing', $item_cost);
                //             }
                //         } else {
                //             foreach ($item_cost as $ic) {
                //                 $ic['sale_item_id'] = $sale_item_id;
                //                 $ic['sale_id'] = $sale_id;
                //                 $ic['date'] = date('Y-m-d', strtotime($data['date']));
                //                 if (!isset($ic['pi_overselling'])) {
                //                     $this->db->insert('costing', $ic);
                //                 }
                //             }
                //         }
                //     }
                }
            }



            if ($data['sale_status'] == 'completed') {

                $syncPurchaseItems = $this->site->syncPurchaseItems($cost, $items[0]['warehouse_id']);
                // $this->sma->print_arrays($data, $items, $payment, $si_return, $cost, $item_costs, $syncPurchaseItems);
            }


            if (!empty($si_return)) {
                foreach ($si_return as $return_item) {
                    $product = $this->site->getProductByID($return_item['product_id']);
                    if ($product->type == 'combo') {
                        $combo_items = $this->site->getProductComboItems($return_item['product_id'], $return_item['warehouse_id']);
                        foreach ($combo_items as $combo_item) {
                            $this->UpdateCostingAndPurchaseItem($return_item, $combo_item->id, ($return_item['quantity'] * $combo_item->qty));
                        }
                    } else {
                        $this->UpdateCostingAndPurchaseItem($return_item, $return_item['product_id'], $return_item['quantity']);
                    }
                }
                $this->db->update('sales', array('return_sale_ref' => $data['return_sale_ref'], 'surcharge' => $data['surcharge'], 'return_sale_total' => $data['grand_total'], 'return_id' => $sale_id), array('id' => $data['sale_id']));
            }

            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' && !empty($payment)) {
                if (empty($payment['reference_no'])) {
                    $payment['reference_no'] = $this->site->getReference('pay');
                }
                $payment['sale_id'] = $sale_id;
                if ($payment['paid_by'] == 'gift_card') {
                    $this->db->update('gift_cards', array('balance' => $payment['gc_balance']), array('card_no' => $payment['cc_no']));
                    unset($payment['gc_balance']);
                    $this->db->insert('payments', $payment);
                } else {
                    if ($payment['paid_by'] == 'deposit') {
                        $customer = $this->site->getCompanyByID($data['customer_id']);
                        $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount - $payment['amount'])), array('id' => $customer->id));
                    }
                    $this->db->insert('payments', $payment);
                }
                if ($this->site->getReference('pay') == $payment['reference_no']) {
                    $this->site->updateReference('pay');
                }
                $this->site->syncSalePayments($sale_id);
            }

            $this->site->syncQuantity($sale_id);
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            foreach ($items as $item) {
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item['product_id'],$item['warehouse_id'],0,"Add Sale");
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                log_message('error', 'An errors has been occurred while adding the sale (Add:Sales_model.php)');
            } else {
                return $sale_id;
            }
        }

        return false;
    }

    public function addSaleAgain($data = array(), $items = array(), $payment = array(), $si_return = array())
    {



        if (empty($si_return)) {
            // Yaha double value pass karwani hy
            $cost = $this->site->costing($items);
            // $this->sma->print_arrays($cost);

        }

        if ($this->db->insert('sales', $data)) {
            $sale_id = $this->db->insert_id();
            if ($this->site->getReference('so') == $data['reference_no']) {
                $this->site->updateReference('so');
            }

            foreach ($items as $item) {
                $item['sale_id'] = $sale_id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && empty($si_return)) {
                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
                            $item_cost['sale_item_id'] = $sale_item_id;
                            $item_cost['sale_id'] = $sale_id;
                            $item_cost['date'] = date('Y-m-d', strtotime($data['date']));
                            if (!isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        } else {
                            foreach ($item_cost as $ic) {
                                $ic['sale_item_id'] = $sale_item_id;
                                $ic['sale_id'] = $sale_id;
                                $ic['date'] = date('Y-m-d', strtotime($data['date']));
                                if (!isset($ic['pi_overselling'])) {
                                    $this->db->insert('costing', $ic);
                                }
                            }
                        }
                    }
                }
                $this->load->model('admin/stores_model');
                $this->stores_model->updateStoreQty($item['product_id'],$item['warehouse_id'],0,"Add Sale Again");
            }

            if ($data['sale_status'] == 'completed') {
                $syncPurchaseItems = $this->site->syncPurchaseItems($cost, $items[0]['warehouse_id']);
                // $this->sma->print_arrays($data, $items, $payment, $si_return, $cost, $item_costs, $syncPurchaseItems);
            }

            // echo "123a";

            if (!empty($si_return)) {
                // echo "2";
                foreach ($si_return as $return_item) {
                    // echo "3";
                    $product = $this->site->getProductByID($return_item['product_id']);
                    if ($product->type == 'combo') {
                        // echo "4";
                        $combo_items = $this->site->getProductComboItems($return_item['product_id'], $return_item['warehouse_id']);
                        foreach ($combo_items as $combo_item) {
                            $this->UpdateCostingAndPurchaseItem($return_item, $combo_item->id, ($return_item['quantity'] * $combo_item->qty));
                        }
                    } else {
                        // echo "5";
                        $this->UpdateCostingAndPurchaseItem($return_item, $return_item['product_id'], $return_item['quantity']);
                    }
                }
                $this->db->update('sales', array('return_sale_ref' => $data['return_sale_ref'], 'surcharge' => $data['surcharge'], 'return_sale_total' => $data['grand_total'], 'return_id' => $sale_id), array('id' => $data['sale_id']));
            }

            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid' && !empty($payment)) {
                // echo "6";
                if (empty($payment['reference_no'])) {
                    // echo "7";
                    $payment['reference_no'] = $this->site->getReference('pay');
                }
                $payment['sale_id'] = $sale_id;
                if ($payment['paid_by'] == 'gift_card') {
                    // echo "8";
                    $this->db->update('gift_cards', array('balance' => $payment['gc_balance']), array('card_no' => $payment['cc_no']));
                    unset($payment['gc_balance']);
                    $this->db->insert('payments', $payment);
                } else {
                    // echo "9";
                    if ($payment['paid_by'] == 'deposit') {
                        // echo "10";
                        $customer = $this->site->getCompanyByID($data['customer_id']);
                        $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount - $payment['amount'])), array('id' => $customer->id));
                    }
                    $this->db->insert('payments', $payment);
                }
                if ($this->site->getReference('pay') == $payment['reference_no']) {
                    // echo "11";
                    $this->site->updateReference('pay');
                }
                // echo "12";
                $this->site->syncSalePayments($sale_id);
            }

            // echo "13";
            $this->site->syncQuantity($sale_id);
            // echo "14";
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            // die();
            // exit();
            return $sale_id;
        }

        return false;
    }

    public function CheckDuplicateInvoice($reference)
    {

        $q = $this->db->query('SELECT * FROM sma_sales WHERE reference_no = "' . $reference . '"');
        if ($q->num_rows() > 0) {
            return TRUE;
            // echo "TRUE"; 
        } else {
            return FALSE;
            // echo "FALSE";
        }
    }


    public function myupdateSale($id, $data, $items = [])
    {
        $this->db->trans_start();

        // foreach ($items as $item) {
        //     $old_product_quantity = $this->site->getProductByID($item['product_id']);
        //     $old_product_quantity_in_purchase_item = $this->site->getPurchaseItemsQuantityByBatch($item['product_id'], $item['batch']);
        //     echo $old_product_quantity_in_purchase_item->quantity_balance ;
        // }
        //$this->sma->print_arrays($id, $data, $items);   

        if ($this->db->delete('sale_items', ['sale_id' => $id]) && $this->db->delete('costing', ['sale_id' => $id]) && $this->db->delete('sales', ['id' => $id])) {
            foreach ($items as $item) {


                $old_product_quantity = $this->site->getProductByID($item['product_id']);
                $old_product_quantity_in_purchase_item = $this->site->getPurchaseItemsQuantityByBatch($item['product_id'], $item['batch']);
                $update_quantity = $old_product_quantity->quantity + $item['quantity'];
                $update_quantity_purchase_item = $old_product_quantity_in_purchase_item->quantity_balance + $item['quantity'];

                $old_product_quantity_costing = $this->site->getPurchaseItemsQuantityByBatchCosting($item['product_id'], $item['batch']);

                //print_r("a".$old_product_quantity_costing);
                //die();
                //exit();
                if($update_quantity == ""){
                    $update_quantity = 0;
                }
                if (
                    // Revert Product Quantity in Product, Warehouse, Purchase item
                    $this->db->update('products', array('quantity' => $update_quantity), ['id' => $item['product_id']]) &&
                    $this->db->update('warehouses_products', array('quantity' => $update_quantity), ['product_id' => $item['product_id']]) &&
                    $this->db->update('purchase_items', array('quantity_balance' => $update_quantity_purchase_item), ['product_id' => $item['product_id'], 'batch' => $item['batch']])
                ) {

                    //Add Sales
                    $this->db->insert('sales', $data);
                    $sales_insert_id = $this->db->insert_id();
                    $item['sale_id'] = $sales_insert_id;
                    $this->db->insert('sale_items', $item);
                    $sale_items_insert_id = $this->db->insert_id();

                    $old_product_quantity = $this->site->getProductByID($item['product_id']);
                    $old_product_quantity_in_purchase_item = $this->site->getPurchaseItemsQuantityByBatch($item['product_id'], $item['batch']);
                    $update_quantity = $old_product_quantity->quantity - $item['quantity'];
                    $update_quantity_purchase_item = $item['quantity'];

                    echo $old_product_quantity->quantity . "<br>";
                    echo $old_product_quantity_in_purchase_item->quantity_balance . "<br>";
                    echo $update_quantity_purchase_item . "<br>";

                    if($update_quantity == ""){
                        $update_quantity = 0;
                    }
    

                    // Revert Product Quantity in Product, Warehouse, Purchase item
                    $this->db->update('products', array('quantity' => $update_quantity), ['id' => $item['product_id']]) &&
                        $this->db->update('warehouses_products', array('quantity' => $update_quantity), ['product_id' => $item['product_id']]) &&
                        $this->db->update('purchase_items', array('quantity_balance' => $update_quantity_purchase_item), ['product_id' => $item['product_id'], 'batch' => $item['batch']]);

                    //Add Costing
                    $item_costing['date']                       = date('Y-m-d', strtotime($data['date']));
                    $item_costing['product_id']                 = $item['product_id'];
                    $item_costing['sale_item_id']               = $sale_items_insert_id;
                    $item_costing['sale_id']                    = $sales_insert_id;
                    $item_costing['purchase_item_id']           = date('Y-m-d', strtotime($data['date']));
                    $item_costing['quantity']                   = $item['quantity'];
                    $item_costing['purchase_net_unit_cost']     = $old_product_quantity_in_purchase_item->net_unit_cost;
                    $item_costing['purchase_unit_cost']         = $old_product_quantity_in_purchase_item->net_unit_cost;
                    $item_costing['sale_net_unit_price']        = $old_product_quantity_in_purchase_item->price;
                    $item_costing['sale_unit_price']            = $old_product_quantity_in_purchase_item->price;
                    $item_costing['quantity_balance']           = $old_product_quantity_in_purchase_item->quantity_balance;
                    $item_costing['inventory']                  = "1";
                    $item_costing['overselling']                = "0";
                    $item_costing['batch']                      = $item['batch'];

                    //$this->sma->print_arrays($id, $data, $items, $item_costing);   

                    $this->db->insert('costing', $item_costing);

                    //$this->sma->print_arrays($id, $data, $items);   
                    //echo $items['quantity'];

                }
            }
        }



        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Update:Sales_model.php)');
        } else {
            return true;
        }
        return false;
    }

    // New update Sales
    public function updateSale($id, $data, $items = [])
    {

        //$this->sma->print_arrays($id, $data, $items);

        $this->db->trans_start();
        $resetSaleActions = $this->myResetSaleActions($id, false, true, $items);

        //$this->sma->print_arrays($resetSaleActions);


        if ($data['sale_status'] == 'completed') {
            $this->Settings->overselling = true;
            $cost                        = $this->site->costing($items, true);
        }

        if ($this->db->update('sales', $data, ['id' => $id]) && $this->db->delete('sale_items', ['sale_id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {
            foreach ($items as $item) {
                $item['sale_id'] = $id;
                $this->db->insert('sale_items', $item);
                $sale_item_id = $this->db->insert_id();
                if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {
                    $item_costs = $this->site->item_costing($item);
                    foreach ($item_costs as $item_cost) {
                        if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
                            $item_cost['sale_item_id'] = $sale_item_id;
                            $item_cost['sale_id']      = $id;
                            $item_cost['date']         = date('Y-m-d', strtotime($data['date']));
                            if (!isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        } else {
                            foreach ($item_cost as $ic) {
                                $ic['sale_item_id'] = $sale_item_id;
                                $ic['sale_id']      = $id;
                                $item_cost['date']  = date('Y-m-d', strtotime($data['date']));
                                if (!isset($ic['pi_overselling'])) {
                                    $this->db->insert('costing', $ic);
                                }
                            }
                        }
                    }
                }
            }

            if ($data['sale_status'] == 'completed') {
                $this->site->syncPurchaseItems($cost);
            }

            $this->site->syncSalePayments($id);
            $this->site->syncQuantity($id);
            $sale = $this->getInvoiceByID($id);
            $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $sale->created_by);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the sale (Update:Sales_model.php)');
        } else {
            return true;
        }
        return false;
    }


    // // Old Update Sales
    // public function updateSale($id, $data, $items = array())
    // {

    //     $this->db->trans_start();
    //     $this->resetSaleActions($id, false, true);

    //     // $this->sma->print_arrays($this->resetSaleActions($id, false, true) ,$id, $data, $items);


    //     if ($data['sale_status'] == 'completed') {
    //         $this->Settings->overselling = true;
    //         $cost = $this->site->costing($items, true);
    //     }

    //     if ($this->db->update('sales', $data, ['id' => $id]) && $this->db->delete('sale_items', ['sale_id' => $id]) && $this->db->delete('costing', ['sale_id' => $id])) {
    //         foreach ($items as $item) {
    //             $item['sale_id'] = $id;
    //             $this->db->insert('sale_items', $item);
    //             $sale_item_id = $this->db->insert_id();
    //             if ($data['sale_status'] == 'completed' && $this->site->getProductByID($item['product_id'])) {
    //                 $item_costs = $this->site->item_costing($item);
    //                 foreach ($item_costs as $item_cost) {
    //                     if (isset($item_cost['date']) || isset($item_cost['pi_overselling'])) {
    //                         $item_cost['sale_item_id'] = $sale_item_id;
    //                         $item_cost['sale_id']      = $id;
    //                         $item_cost['date']         = date('Y-m-d', strtotime($data['date']));
    //                         if (!isset($item_cost['pi_overselling'])) {
    //                             $this->db->insert('costing', $item_cost);
    //                         }
    //                     } else {
    //                         foreach ($item_cost as $ic) {
    //                             $ic['sale_item_id'] = $sale_item_id;
    //                             $ic['sale_id']      = $id;
    //                             $item_cost['date']  = date('Y-m-d', strtotime($data['date']));
    //                             if (!isset($ic['pi_overselling'])) {
    //                                 $this->db->insert('costing', $ic);
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         if ($data['sale_status'] == 'completed') {
    //             $this->site->syncPurchaseItems($cost);
    //         }

    //         $this->site->syncSalePayments($id);
    //         $this->site->syncQuantity($id);
    //         $sale = $this->getInvoiceByID($id);
    //         $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $sale->created_by);
    //     }
    //     $this->db->trans_complete();
    //     if ($this->db->trans_status() === false) {
    //         log_message('error', 'An errors has been occurred while adding the sale (Update:Sales_model.php)');
    //     } else {
    //         return true;
    //     }
    //     return false;
    // }

    public function updateStatus($id, $status, $note)
    {

        $this->db->trans_start();
        $sale = $this->getInvoiceByID($id);
        $items = $this->getAllInvoiceItems($id);
        $cost = array();
        if ($status == 'completed' && $sale->sale_status != 'completed') {
            foreach ($items as $item) {
                $items_array[] = (array) $item;
            }
            $cost = $this->site->costing($items_array);
        }
        if ($status != 'completed' && $sale->sale_status == 'completed') {
            $this->resetSaleActions($id);
        }

        if ($this->db->update('sales', array('sale_status' => $status, 'note' => $note), array('id' => $id)) && $this->db->delete('costing', array('sale_id' => $id))) {
            if ($status == 'completed' && $sale->sale_status != 'completed') {
                foreach ($items as $item) {
                    $item = (array) $item;
                    if ($this->site->getProductByID($item['product_id'])) {
                        $item_costs = $this->site->item_costing($item);
                        foreach ($item_costs as $item_cost) {
                            $item_cost['sale_item_id'] = $item['id'];
                            $item_cost['sale_id'] = $id;
                            $item_cost['date'] = date('Y-m-d', strtotime($sale->date));
                            if (!isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        }
                    }
                }
            }

            if (!empty($cost)) {
                $this->site->syncPurchaseItems($cost);
            }
            $this->site->syncQuantity($id);
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                log_message('error', 'An errors has been occurred while adding the sale (UpdataStatus:Sales_model.php)');
            } else {
                return true;
            }
        }
        return false;
    }

    public function deleteSale($id)
    {

        $this->db->trans_start();
        $sale_items = $this->DeleteResetSaleActions($id);

        if (
            $this->db->delete('sale_items', array('sale_id' => $id)) &&
            $this->db->delete('sales', array('id' => $id)) &&
            $this->db->delete('costing', array('sale_id' => $id))
        ) {
            $this->db->delete('sales', array('sale_id' => $id));
            $this->db->delete('payments', array('sale_id' => $id));
            $this->site->syncQuantity(NULL, NULL, $sale_items);

            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                log_message('error', 'An errors has been occurred while adding the sale (Delete:Sales_model.php)');
            } else {
                return true;
            }
            return false;
        }
        return FALSE;
    }


    public function DeleteResetSaleActions($id, $return_id = NULL, $check_return = NULL)
    {



        if ($sale = $this->getInvoiceByID($id)) {
            if ($check_return && $sale->sale_status == 'returned') {
                $this->session->set_flashdata('warning', lang('sale_x_action'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
            }

            // echo "a1";

            if ($sale->sale_status == 'completed') {


                // echo "a2";

                if ($costings = $this->getSaleCosting($id)) {
                    foreach ($costings as $costing) {

                        // echo $costing->purchase_item_id;

                        if ($pi = $this->getPurchaseItemByID($costing->purchase_item_id)) {
                            // echo "1 <pre>";
                            // print_r($pi);
                            // echo $costing->quantity;
                            // echo "</pre>";

                            // die();
                            // exit();

                            $this->site->setPurchaseItemAfterDelete(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                            // $this->site->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        } else {

                            // echo "2 <pre>";
                            // print_r($pi);
                            // echo "</pre>";

                            // die();
                            // exit();

                            // $sale_item = $this->getSaleItemByID($costing->sale_item_id);
                            $pi = $this->site->getPurchasedItem(['product_id' => $costing->product_id, 'option_id' => $costing->option_id ? $costing->option_id : NULL, 'purchase_id' => NULL, 'transfer_id' => NULL, 'warehouse_id' => $sale->warehouse_id]);
                        }
                    }
                }

                // $this->sma->print_arrays($sale);

                $items = $this->getAllInvoiceItems($id);



                // $this->sma->print_arrays($items);



                $this->site->syncQuantity(NULL, NULL, $items);
                $this->sma->update_award_points($sale->grand_total, $sale->customer_id, $sale->created_by, TRUE);
                return $items;
            }
        }
    }

    public function resetSaleActions($id, $return_id = NULL, $check_return = NULL)
    {





        if ($sale = $this->getInvoiceByID($id)) {
            if ($check_return && $sale->sale_status == 'returned') {
                $this->session->set_flashdata('warning', lang('sale_x_action'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
            }


            if ($sale->sale_status == 'completed') {

                if ($costings = $this->getSaleCosting($id)) {



                    foreach ($costings as $costing) {

                        // echo $costing->purchase_item_id;

                        if ($pi = $this->getPurchaseItemByID($costing->purchase_item_id)) {

                            $this->sma->print_arrays($sale, $costings, $pi);


                            // echo "1 <pre>";
                            // print_r($pi);
                            // echo $costing->quantity;
                            // echo "</pre>";

                            // die();
                            // exit();

                            $this->site->setPurchaseItemAfterDelete(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                            // $this->site->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        } else {

                            // echo "2 <pre>";
                            // print_r($pi);
                            // echo "</pre>";

                            // die();
                            // exit();

                            // $sale_item = $this->getSaleItemByID($costing->sale_item_id);
                            $pi = $this->site->getPurchasedItem(['product_id' => $costing->product_id, 'option_id' => $costing->option_id ? $costing->option_id : NULL, 'purchase_id' => NULL, 'transfer_id' => NULL, 'warehouse_id' => $sale->warehouse_id]);

                            // $this->site->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        }
                    }
                }

                // $this->sma->print_arrays($sale);

                $items = $this->getAllInvoiceItems($id);



                // $this->sma->print_arrays($items);



                $this->site->syncQuantity(NULL, NULL, $items);
                $this->sma->update_award_points($sale->grand_total, $sale->customer_id, $sale->created_by, TRUE);
                return $items;
            }
        }
    }

    public function myResetSaleActions($id, $return_id = NULL, $check_return = NULL, $update_quantity)
    {


        if ($sale = $this->getInvoiceByID($id)) {
            if ($check_return && $sale->sale_status == 'returned') {
                $this->session->set_flashdata('warning', lang('sale_x_action'));
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
            }


            if ($sale->sale_status == 'completed') {

                if ($costings = $this->getSaleCosting($id)) {



                    foreach ($costings as $costing) {

                        // echo $costing->purchase_item_id;

                        if ($pi = $this->getPurchaseItemByID($costing->purchase_item_id)) {

                            $this->sma->print_arrays($sale, $costings, $pi, $update_quantity->quantity);


                            // echo "1 <pre>";
                            // print_r($pi);
                            // echo $costing->quantity;
                            // echo "</pre>";

                            // die();
                            // exit();

                            $this->site->setPurchaseItemAfterDelete(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                            // $this->site->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        } else {

                            // echo "2 <pre>";
                            // print_r($pi);
                            // echo "</pre>";

                            // die();
                            // exit();

                            // $sale_item = $this->getSaleItemByID($costing->sale_item_id);
                            $pi = $this->site->getPurchasedItem(['product_id' => $costing->product_id, 'option_id' => $costing->option_id ? $costing->option_id : NULL, 'purchase_id' => NULL, 'transfer_id' => NULL, 'warehouse_id' => $sale->warehouse_id]);

                            // $this->site->setPurchaseItem(['id' => $pi->id, 'product_id' => $pi->product_id, 'option_id' => $pi->option_id], $costing->quantity);
                        }
                    }
                }

                // $this->sma->print_arrays($sale);

                $items = $this->getAllInvoiceItems($id);



                // $this->sma->print_arrays($items);



                $this->site->syncQuantity(NULL, NULL, $items);
                $this->sma->update_award_points($sale->grand_total, $sale->customer_id, $sale->created_by, TRUE);
                return $items;
            }
        }
    }


    public function getPurchaseItemByID($id)
    {
        // echo $id;


        // die();
        // exit();

        $q = $this->db->get_where('purchase_items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function get_product_qty_transfer($get_selected_batch_code, $get_product_bar_code, $warehouse_id)
    {
        $q = $this->db->query('SELECT id, purchase_id as get_selected_purchase_id, transfer_id, product_id, product_name, option_id, net_unit_cost, price as get_selected_product_price, price as get_selected_product_consiment, dropship as get_selected_product_dropship, crossdock as get_selected_product_crossdock, mrp as get_selected_product_mrp, quantity, warehouse_id, item_tax, tax_rate_id, tax, discount, item_discount, expiry as get_selected_expiry, batch as get_selected_batch_code, subtotal, quantity_balance, date, status, unit_cost, real_unit_cost, quantity_received, supplier_part_no, purchase_item_id, product_unit_id, product_unit_code, unit_quantity, gst, cgst, sgst, igst, discount_one, discount_two, discount_three, fed_tax as get_selected_fed_tax_rate FROM sma_purchase_items WHERE warehouse_id = "' . $warehouse_id . '"  AND product_code = "' . $get_product_bar_code . '" and batch = "' . $get_selected_batch_code . '" Limit 1');

        if ($q->num_rows() > 0) {
            return $q->row();
        } else {
            return FALSE;
        }

        //echo $get_selected_batch_code . ' - ' . $get_product_bar_code . ' - ' . $warehouse_id . ' - ' . $customer_id;
    }

    public function get_product_qty($get_selected_batch_code, $get_product_bar_code, $warehouse_id,  $customer_id)
    {
        $get_product_bar_code=str_replace("\r\n","",$get_product_bar_code);
        $customer_sales_type = "";
        $this->db->select('sales_type');
        $this->db->from('sma_companies');
        $this->db->where('id',$customer_id);
        $cq = $this->db->get();
        if($cq->num_rows() > 0){
            $customer_sales_type = $cq->result()[0]->sales_type;
        }
        if ($customer_sales_type === 'cost') {
            $q = $this->db->query('
                SELECT 
                    id, 
                    purchase_id as get_selected_purchase_id, 
                    transfer_id, 
                    product_id, 
                    product_name, 
                    option_id, 
                    net_unit_cost, 
                    net_unit_cost as get_selected_product_price, 
                    price as get_selected_product_consiment, 
                    dropship as get_selected_product_dropship, 
                    crossdock as get_selected_product_crossdock, 
                    mrp as get_selected_product_mrp, 
                    quantity, 
                    warehouse_id, 
                    item_tax, 
                    tax_rate_id, 
                    tax, 
                    discount, 
                    item_discount, 
                    expiry as get_selected_expiry, 
                    batch as get_selected_batch_code, 
                    subtotal, 
                    quantity_balance, 
                    date, 
                    status, 
                    unit_cost, 
                    real_unit_cost, 
                    quantity_received, 
                    supplier_part_no, 
                    purchase_item_id, 
                    product_unit_id, 
                    product_unit_code, 
                    unit_quantity, 
                    gst, 
                    cgst, 
                    sgst, 
                    igst, 
                    discount_one, 
                    discount_two, 
                    discount_three, 
                    fed_tax as get_selected_fed_tax_rate 
                FROM 
                    sma_purchase_items 
                WHERE 
                    warehouse_id = "' . $warehouse_id . '"  AND 
                    product_code = "' . $get_product_bar_code . '" and 
                    batch = "' . $get_selected_batch_code . '"
            ');
            // $q = $this->db->query('SELECT id, purchase_id as get_selected_purchase_id, transfer_id, product_id, product_name, option_id, net_unit_cost, net_unit_cost as get_selected_product_price, price as get_selected_product_consiment, dropship as get_selected_product_dropship, crossdock as get_selected_product_crossdock, mrp as get_selected_product_mrp, quantity, warehouse_id, item_tax, tax_rate_id, tax, discount, item_discount, expiry as get_selected_expiry, batch as get_selected_batch_code, subtotal, quantity_balance, date, status, unit_cost, real_unit_cost, quantity_received, supplier_part_no, purchase_item_id, product_unit_id, product_unit_code, unit_quantity, gst, cgst, sgst, igst, discount_one, discount_two, discount_three, fed_tax as get_selected_fed_tax_rate FROM sma_purchase_items WHERE warehouse_id = "' . $warehouse_id . '"  AND product_code = "' . $get_product_bar_code . '" and id = "' . $get_selected_batch_code . '" Limit 1');
        }
        else if ($customer_sales_type === 'mrp') {
            $q = $this->db->query('
                SELECT 
                    id, 
                    purchase_id as get_selected_purchase_id, 
                    transfer_id, 
                    product_id, 
                    product_name, 
                    option_id, 
                    net_unit_cost, 
                    mrp as get_selected_product_price, 
                    price as get_selected_product_consiment, 
                    dropship as get_selected_product_dropship, 
                    crossdock as get_selected_product_crossdock, 
                    mrp as get_selected_product_mrp, 
                    quantity, 
                    warehouse_id, 
                    item_tax, 
                    tax_rate_id, 
                    tax, 
                    discount, 
                    item_discount, 
                    expiry as get_selected_expiry, 
                    batch as get_selected_batch_code, 
                    subtotal, 
                    quantity_balance, 
                    date, 
                    status, 
                    unit_cost, 
                    real_unit_cost, 
                    quantity_received, 
                    supplier_part_no, 
                    purchase_item_id, 
                    product_unit_id, 
                    product_unit_code, 
                    unit_quantity, 
                    gst, 
                    cgst, 
                    sgst, 
                    igst, 
                    discount_one, 
                    discount_two, 
                    discount_three, 
                    fed_tax as get_selected_fed_tax_rate 
                FROM 
                    sma_purchase_items 
                WHERE 
                    warehouse_id = "' . $warehouse_id . '"  AND 
                    product_code = "' . $get_product_bar_code . '" and 
                    batch = "' . $get_selected_batch_code . '"
            ');
            // $q = $this->db->query('SELECT id, purchase_id as get_selected_purchase_id, transfer_id, product_id, product_name, option_id, net_unit_cost, net_unit_cost as get_selected_product_price, price as get_selected_product_consiment, dropship as get_selected_product_dropship, crossdock as get_selected_product_crossdock, mrp as get_selected_product_mrp, quantity, warehouse_id, item_tax, tax_rate_id, tax, discount, item_discount, expiry as get_selected_expiry, batch as get_selected_batch_code, subtotal, quantity_balance, date, status, unit_cost, real_unit_cost, quantity_received, supplier_part_no, purchase_item_id, product_unit_id, product_unit_code, unit_quantity, gst, cgst, sgst, igst, discount_one, discount_two, discount_three, fed_tax as get_selected_fed_tax_rate FROM sma_purchase_items WHERE warehouse_id = "' . $warehouse_id . '"  AND product_code = "' . $get_product_bar_code . '" and id = "' . $get_selected_batch_code . '" Limit 1');
        }
        else {
            $q = $this->db->query('
                SELECT 
                    id, 
                    purchase_id as get_selected_purchase_id, 
                    transfer_id, product_id, 
                    product_name, option_id, 
                    net_unit_cost, 
                    price as get_selected_product_price, 
                    price as get_selected_product_consiment, 
                    dropship as get_selected_product_dropship, 
                    crossdock as get_selected_product_crossdock, 
                    mrp as get_selected_product_mrp, 
                    quantity, 
                    warehouse_id, 
                    item_tax, 
                    tax_rate_id, 
                    tax, 
                    discount, 
                    item_discount, 
                    expiry as get_selected_expiry, 
                    batch as get_selected_batch_code, 
                    subtotal, 
                    quantity_balance, 
                    date, 
                    status, 
                    unit_cost, 
                    real_unit_cost, 
                    quantity_received, 
                    supplier_part_no, 
                    purchase_item_id, 
                    product_unit_id, 
                    product_unit_code, 
                    unit_quantity, 
                    gst, 
                    cgst, 
                    sgst, 
                    igst, 
                    discount_one, 
                    discount_two, 
                    discount_three, 
                    fed_tax as get_selected_fed_tax_rate 
                FROM 
                    sma_purchase_items 
                WHERE 
                    warehouse_id = "' . $warehouse_id . '"  AND 
                    product_code = "' . $get_product_bar_code . '" and 
                    batch = "' . $get_selected_batch_code . '"
            ');
            // $q = $this->db->query('SELECT id, purchase_id as get_selected_purchase_id, transfer_id, product_id, product_name, option_id, net_unit_cost, price as get_selected_product_price, price as get_selected_product_consiment, dropship as get_selected_product_dropship, crossdock as get_selected_product_crossdock, mrp as get_selected_product_mrp, quantity, warehouse_id, item_tax, tax_rate_id, tax, discount, item_discount, expiry as get_selected_expiry, batch as get_selected_batch_code, subtotal, quantity_balance, date, status, unit_cost, real_unit_cost, quantity_received, supplier_part_no, purchase_item_id, product_unit_id, product_unit_code, unit_quantity, gst, cgst, sgst, igst, discount_one, discount_two, discount_three, fed_tax as get_selected_fed_tax_rate FROM sma_purchase_items WHERE warehouse_id = "' . $warehouse_id . '"  AND product_code = "' . $get_product_bar_code . '" and id = "' . $get_selected_batch_code . '" Limit 1');
        }

        if ($q->num_rows() > 0) {
            $senddata = array();
            $no  = 1;
            $rs = $q->result();
            foreach($rs as $r){
                if($no == 1){
                    $senddata = $r;
                }
                else{
                    $senddata->item_tax = $senddata->item_tax + $r->item_tax;
                    $senddata->quantity = $senddata->quantity + + $r->quantity;
                    $senddata->subtotal = $senddata->subtotal + $r->subtotal;
                    $senddata->quantity_balance = $senddata->quantity_balance + $r->quantity_balance;
                    $senddata->unit_quantity = $senddata->unit_quantity + $r->unit_quantity;

                }
                $no++;
            }
            return $senddata;
        } else {
            return FALSE;
        }

        //echo $get_selected_batch_code . ' - ' . $get_product_bar_code . ' - ' . $warehouse_id . ' - ' . $customer_id;
    }

    public function getPurchaseList($purchase_id, $batch_code)
    {
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id, 'batch' => $batch_code), 1);
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }

    public function get_remain_quantity($batch, $item_id)
    {
        $this->db->select('quantity_balance');
        $q = $this->db->get_where('purchase_items', array('product_id' => $item_id, 'batch' => $batch), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCostingLines($sale_item_id, $product_id, $sale_id = NULL)
    {
        if ($sale_id) {
            $this->db->where('sale_id', $sale_id);
        }
        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->order_by('id', $orderby);
        $q = $this->db->get_where('costing', array('sale_item_id' => $sale_item_id, 'product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSaleItemByID($id)
    {
        $q = $this->db->get_where('sale_items', array('id' => $id), 1);
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

    public function addDelivery($data = array())
    {
        if ($this->db->insert('deliveries', $data)) {
            if ($this->site->getReference('do') == $data['do_reference_no']) {
                $this->site->updateReference('do');
            }
            return true;
        }
        return false;
    }

    public function updateDelivery($id, $data = array())
    {
        if ($this->db->update('deliveries', $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function getDeliveryByID($id)
    {
        $q = $this->db->get_where('deliveries', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getDeliveryBySaleID($sale_id)
    {
        $q = $this->db->get_where('deliveries', array('sale_id' => $sale_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteDelivery($id)
    {
        if ($this->db->delete('deliveries', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getInvoicePayments($sale_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPaymentsForSale($sale_id)
    {
        $this->db->select('payments.id,payments.date, payments.paid_by, payments.amount, payments.cc_no, payments.cheque_no, payments.reference_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addPayment($data = array(), $customer_id = null)
    {
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('pay') == $data['reference_no']) {
                $this->site->updateReference('pay');
            }
            $this->site->syncSalePayments($data['sale_id']);
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['cc_no']);
                $this->db->update('gift_cards', array('balance' => ($gc->balance - $data['amount'])), array('card_no' => $data['cc_no']));
            } elseif ($customer_id && $data['paid_by'] == 'deposit') {
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount - $data['amount'])), array('id' => $customer_id));
            }
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array(), $customer_id = null)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->site->syncSalePayments($data['sale_id']);
            if ($opay->paid_by == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($opay->cc_no);
                $this->db->update('gift_cards', array('balance' => ($gc->balance + $opay->amount)), array('card_no' => $opay->cc_no));
            } elseif ($opay->paid_by == 'deposit') {
                if (!$customer_id) {
                    $sale = $this->getInvoiceByID($opay->sale_id);
                    $customer_id = $sale->customer_id;
                }
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount + $opay->amount)), array('id' => $customer->id));
            }
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['cc_no']);
                $this->db->update('gift_cards', array('balance' => ($gc->balance - $data['amount'])), array('card_no' => $data['cc_no']));
            } elseif ($customer_id && $data['paid_by'] == 'deposit') {
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount - $data['amount'])), array('id' => $customer_id));
            }
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->site->syncSalePayments($opay->sale_id);
            if ($opay->paid_by == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($opay->cc_no);
                $this->db->update('gift_cards', array('balance' => ($gc->balance + $opay->amount)), array('card_no' => $opay->cc_no));
            } elseif ($opay->paid_by == 'deposit') {
                $sale = $this->getInvoiceByID($opay->sale_id);
                $customer = $this->site->getCompanyByID($sale->customer_id);
                $this->db->update('companies', array('deposit_amount' => ($customer->deposit_amount + $opay->amount)), array('id' => $customer->id));
            }
            return true;
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

    /* ----------------- Gift Cards --------------------- */

    public function addGiftCard($data = array(), $ca_data = array(), $sa_data = array())
    {
        if ($this->db->insert('gift_cards', $data)) {
            if (!empty($ca_data)) {
                $this->db->update('companies', array('award_points' => $ca_data['points']), array('id' => $ca_data['customer']));
            } elseif (!empty($sa_data)) {
                $this->db->update('users', array('award_points' => $sa_data['points']), array('id' => $sa_data['user']));
            }
            return true;
        }
        return false;
    }

    public function updateGiftCard($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('gift_cards', $data)) {
            return true;
        }
        return false;
    }

    public function deleteGiftCard($id)
    {
        if ($this->db->delete('gift_cards', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getPaypalSettings()
    {
        $q = $this->db->get_where('paypal', array('id' => 1));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSkrillSettings()
    {
        $q = $this->db->get_where('skrill', array('id' => 1));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getQuoteByID($id)
    {
        $q = $this->db->get_where('quotes', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllQuoteItems($quote_id)
    {
        $q = $this->db->get_where('quote_items', array('quote_id' => $quote_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaff()
    {
        if (!$this->Owner) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $q = $this->db->get('users');
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

    public function getTaxRateByName($name)
    {
        $q = $this->db->get_where('tax_rates', array('name' => $name), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function topupGiftCard($data = array(), $card_data = NULL)
    {
        if ($this->db->insert('gift_card_topups', $data)) {
            $this->db->update('gift_cards', $card_data, array('id' => $data['card_id']));
            return true;
        }
        return false;
    }

    public function getAllGCTopups($card_id)
    {
        $this->db->select("{$this->db->dbprefix('gift_card_topups')}.*, {$this->db->dbprefix('users')}.first_name, {$this->db->dbprefix('users')}.last_name, {$this->db->dbprefix('users')}.email")
            ->join('users', 'users.id=gift_card_topups.created_by', 'left')
            ->order_by('id', 'desc')->limit(10);
        $q = $this->db->get_where('gift_card_topups', array('card_id' => $card_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getItemRack($product_id, $warehouse_id)
    {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            $wh = $q->row();
            return $wh->rack;
        }
        return FALSE;
    }

    public function getSaleCosting($sale_id)
    {
        $q = $this->db->get_where('costing', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function UpdateCostingAndPurchaseItem($return_item, $product_id, $quantity, $batch)
    {

        $bln_quantity = $quantity;
        if ($costings = $this->getCostingLines($return_item['id'], $product_id)) {
            foreach ($costings as $costing) {
                if ($costing->quantity > $bln_quantity && $bln_quantity != 0) {
                    $qty = $costing->quantity - $bln_quantity;
                    $bln = $costing->quantity_balance && $costing->quantity_balance >= $bln_quantity ? $costing->quantity_balance - $bln_quantity : 0;
                    $this->db->update('costing', array('quantity' => $qty, 'quantity_balance' => $bln), array('id' => $costing->id));
                    $bln_quantity = 0;
                    break;
                } elseif ($costing->quantity <= $bln_quantity && $bln_quantity != 0) {
                    $this->db->delete('costing', array('id' => $costing->id));
                    $bln_quantity = ($bln_quantity - $costing->quantity);
                }
            }
        }
        $clause = ['product_id' => $product_id, 'warehouse_id' => $return_item['warehouse_id'], 'purchase_id' => null, 'transfer_id' => null, 'option_id' => $return_item['option_id']];
        $this->site->setPurchaseItem($clause, $quantity);
        $this->site->syncQuantity(null, null, null, $product_id);
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
    // Ismail FSD Code
    public function getAllReturnSale($id){
        $this->db->select('*');
        $this->db->from('sma_sale_returns_tb');
        $this->db->where('sale_id',$id);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            return $q->result()[0];
        }
        else{
            return false;
        }
    }
    public function getAllReturnItems($id){
        
        $this->db->select('
            sma_sale_return_items_tb.*,
            sma_products.name as product_name,
            sma_products.code as product_code,
            sma_products.hsn_code as product_hsn_code,
            sma_products.unit as product_unit_code,
        ');
        $this->db->from('sma_sale_return_items_tb');
        $this->db->join('sma_sale_returns_tb','sma_sale_returns_tb.id = sma_sale_return_items_tb.sale_return_id','left');
        $this->db->join('sma_products','sma_products.id = sma_sale_return_items_tb.product_id','left');
        $this->db->where('sma_sale_returns_tb.sale_id',$id);
        $q = $this->db->get();
        return $q->result();
    }
}
