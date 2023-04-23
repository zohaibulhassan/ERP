<?php defined('BASEPATH') or exit('No direct script access allowed');

class Site extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_total_qty_alerts()
    {
        $this->db->where('quantity < alert_quantity', NULL, FALSE)->where('track_quantity', 1);
        return $this->db->count_all_results('products');
    }
    public function getPrinterByID($id)
    {
        $q = $this->db->get_where('printers', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getPrinterByUser($id)
    {
        $q = $this->db->get_where('printers', ['user_id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function get_expiring_qty_alerts()
    {
        $date = date('Y-m-d', strtotime('+3 months'));
        $this->db->select('SUM(quantity_balance) as alert_num')
            ->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')
            ->where('expiry <', $date);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $res = $q->row();
            return (int) $res->alert_num;
        }
        return FALSE;
    }

    public function get_shop_sale_alerts()
    {
        $this->db->join('deliveries', 'deliveries.sale_id=sales.id', 'left')
            ->where('sales.shop', 1)->where('sales.sale_status', 'completed')->where('sales.payment_status', 'paid')
            ->group_start()->where('deliveries.status !=', 'delivered')->or_where('deliveries.status IS NULL', NULL)->group_end();
        return $this->db->count_all_results('sales');
    }

    public function get_shop_payment_alerts()
    {
        $this->db->where('shop', 1)->where('attachment !=', NULL)->where('payment_status !=', 'paid');
        return $this->db->count_all_results('sales');
    }

    public function get_setting()
    {
        $q = $this->db->get('settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getDateFormat($id)
    {
        $q = $this->db->get_where('date_format', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getAllCompaniesBiller($group_name)
    {
        $q = $this->db->get_where('companies', array('group_name' => $group_name, 'name' => 'Rhocom'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getAllCompanies($group_name)
    {
        $q = $this->db->get_where('companies', array('group_name' => $group_name));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function GetAllSupplierList()
    {
        $this->db->select('*');
        $q = $this->db->get_where('companies', array('group_name' => 'supplier'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function GetAllSupplierList2()
    {
        $this->db->select('id, name');
        $q = $this->db->get_where('companies', array('group_name' => 'supplier'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getCompanyByID($id)
    {
        $q = $this->db->get_where('companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCustomerGroupByID($id)
    {
        $q = $this->db->get_where('customer_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getUser($id = NULL)
    {
        if (!$id) {
            $id = $this->session->userdata('user_id');
        }
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductByID($id)
    {
        $q = $this->db->get_where('products', array('id' => $id), 1);

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getPurchaseItemsQuantityByBatch($id, $batch)
    {
        $q = $this->db->get_where('purchase_items', array('product_id' => $id, 'batch' => $batch), 1);

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchaseItemsQuantityByBatchCosting($id, $batch)
    {
        //$q = $this->db->get_where('costing', array('product_id' => $id, 'batch' => $batch), 1);
        $q = $this->db->query("SELECT * FROM `sma_costing` where `product_id` = '" . $id . "' and `batch` = '$batch' ");
        echo "SELECT * FROM `sma_costing` where `product_id` = '" . $id . "' and `batch` = '$batch' ";

        // echo "<pre>";
        // print_r($q);

        // die();
        // exit();

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchaseProductByID($id, $batch)
    {

        // $this->db->save_queries = TRUE;


        $q = $this->db->query("SELECT sma_products.*, sma_purchase_items.* FROM `sma_products` LEFT JOIN `sma_purchase_items` ON `sma_purchase_items`.`product_id` = `sma_products`.`id` where `product_id` = '" . $id . "' and `sma_purchase_items`.`batch` = '$batch' ");

        // $this->db->select('products.* , purchase_items.*')
        // ->join('products', 'products.code=purchase_items.product_code', 'left')
        // $q = $this->db->get_where('products', array('id' => $pid));


        // $q = $this->`db->get_where('purchase_items', array('product_id' => $id));

        // $str = $this->db->last_query();
        // echo $str;

        // die();
        // exit();


        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
            // $this->sma->print_arrays($q->row());
            // return $q->row();
        }
        return FALSE;
    }

    public function getAllCurrencies()
    {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCurrencyByCode($code)
    {
        $q = $this->db->get_where('currencies', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllTaxRates()
    {
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTaxRateByID($id)
    {
        $q = $this->db->get_where('tax_rates', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllWarehouses()
    {
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getWarehouseByID($id)
    {
        $q = $this->db->get_where('warehouses', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllCategories()
    {
        $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getAllProductGroups()
    {
        $q = $this->db->get("sma_product_groups");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSubCategories($parent_id)
    {
        $this->db->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get("categories");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getCategoryByID($id)
    {
        $q = $this->db->get_where('categories', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCardByID($id)
    {
        $q = $this->db->get_where('gift_cards', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getGiftCardByNO($no)
    {
        $q = $this->db->get_where('gift_cards', array('card_no' => $no), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateInvoiceStatus()
    {
        $date = date('Y-m-d');
        $q = $this->db->get_where('invoices', array('status' => 'unpaid'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if ($row->due_date < $date) {
                    $this->db->update('invoices', array('status' => 'due'), array('id' => $row->id));
                }
            }
            $this->db->update('settings', array('update' => $date), array('setting_id' => '1'));
            return true;
        }
    }

    public function modal_js()
    {
        return '<script type="text/javascript">' . file_get_contents($this->data['assets'] . 'js/modal.js') . '</script>';
    }

    public function getReference($field)
    {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            switch ($field) {
                case 'so':
                    $prefix = $this->Settings->sales_prefix;
                    break;
                case 'pos':
                    $prefix = isset($this->Settings->sales_prefix) ? $this->Settings->sales_prefix . '/POS' : '';
                    break;
                case 'qu':
                    $prefix = $this->Settings->quote_prefix;
                    break;
                case 'po':
                    $prefix = $this->Settings->purchase_prefix;
                    break;
                case 'to':
                    $prefix = $this->Settings->transfer_prefix;
                    break;
                case 'do':
                    $prefix = $this->Settings->delivery_prefix;
                    break;
                case 'pay':
                    $prefix = $this->Settings->payment_prefix;
                    break;
                case 'ppay':
                    $prefix = $this->Settings->ppayment_prefix;
                    break;
                case 'ex':
                    $prefix = $this->Settings->expense_prefix;
                    break;
                case 're':
                    $prefix = $this->Settings->return_prefix;
                    break;
                case 'rep':
                    $prefix = $this->Settings->returnp_prefix;
                    break;
                case 'qa':
                    $prefix = $this->Settings->qa_prefix;
                    break;
                default:
                    $prefix = '';
            }

            $ref_no = $prefix;

            if ($this->Settings->reference_format == 1) {
                $ref_no .= date('Y') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 2) {
                $ref_no .= date('Y') . "/" . date('m') . "/" . sprintf("%04s", $ref->{$field});
            } elseif ($this->Settings->reference_format == 3) {
                $ref_no .= sprintf("%04s", $ref->{$field});
            } else {
                $ref_no .= $this->getRandomReference();
            }

            return $ref_no;
        }
        return FALSE;
    }

    public function getRandomReference($len = 12)
    {
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= mt_rand(0, 9);
        }

        if ($this->getSaleByReference($result)) {
            $this->getRandomReference();
        }

        return $result;
    }

    public function getSaleByReference($ref)
    {
        $this->db->like('reference_no', $ref, 'both');
        $q = $this->db->get('sales', 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateReference($field)
    {
        $q = $this->db->get_where('order_ref', array('ref_id' => '1'), 1);
        if ($q->num_rows() > 0) {
            $ref = $q->row();
            $this->db->update('order_ref', array($field => $ref->{$field} + 1), array('ref_id' => '1'));
            return TRUE;
        }
        return FALSE;
    }

    public function checkPermissions()
    {
        $q = $this->db->get_where('permissions', array('group_id' => $this->session->userdata('group_id')), 1);
        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }

    public function getNotifications()
    {
        $date = date('Y-m-d H:i:s', time());
        $this->db->where("from_date <=", $date);
        $this->db->where("till_date >=", $date);
        if (!$this->Owner) {
            if ($this->Supplier) {
                $this->db->where('scope', 4);
            } elseif ($this->Customer) {
                $this->db->where('scope', 1)->or_where('scope', 3);
            } elseif (!$this->Customer && !$this->Supplier) {
                $this->db->where('scope', 2)->or_where('scope', 3);
            }
        }
        $q = $this->db->get("notifications");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getUpcomingEvents()
    {
        $dt = date('Y-m-d');
        $this->db->where('start >=', $dt)->order_by('start')->limit(5);
        if ($this->Settings->restrict_calendar) {
            $this->db->where('user_id', $this->session->userdata('user_id'));
        }

        $q = $this->db->get('calendar');

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUserGroup($user_id = false)
    {
        if (!$user_id) {
            $user_id = $this->session->userdata('user_id');
        }
        $group_id = $this->getUserGroupID($user_id);
        $q = $this->db->get_where('groups', array('id' => $group_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getUserGroupID($user_id = false)
    {
        $user = $this->getUser($user_id);
        return $user->group_id;
    }

    public function getWarehouseProductsVariants($option_id, $warehouse_id = NULL)
    {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products_variants', array('option_id' => $option_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPurchasedItem($clause)
    {

        // $stack = array("banana", "orange", "apple", "raspberry");

        // unset($stack[2]); // remove item at index 0
        // $foo2 = array_values($stack); // 'reindex' array

        // print_r($foo2);


        // die();
        // exit();


        // print_r($clause);
        // echo "<br>";

        // die();
        // exit();
        $this->db->save_queries = TRUE;

        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        // if (!isset($clause['option_id']) || empty($clause['option_id'])) {
        //     $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        // }
        $q = $this->db->get_where('purchase_items', $clause);


        // $str = $this->db->last_query();
        // echo $str;

        // die();
        // exit();


        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchasedItemAfterTransfer($clause)
    {

        unset($clause["purchase_id"]); // remove item at index 0
        unset($clause["transfer_id"]); // remove item at index 0
        unset($clause["option_id"]); // remove item at index 0        

        // $this->db->save_queries = TRUE;

        $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        // if (!isset($clause['option_id']) || empty($clause['option_id'])) {
        //     $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        // }
        $q = $this->db->get_where('purchase_items', $clause);

        // $str = $this->db->last_query();
        // echo $str;

        // print_r($clause);
        // echo "<br>";

        // die();
        // exit();

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function setPurchaseItem($clause, $qty)
    {
        if ($product = $this->getProductByID($clause['product_id'])) {

            //$this->sma->print_arrays($product, $qty);
            if ($pi = $this->getPurchasedItem($clause)) {
                $quantity_balance = $pi->quantity_balance - $qty;
                return $this->db->update('purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id));
            } else {
                $unit = $this->getUnitByID($product->unit);
                $clause['product_unit_id'] = $product->unit;
                $clause['product_unit_code'] = $unit->code;
                $clause['product_code'] = $product->code;
                $clause['product_name'] = $product->name;
                $clause['purchase_id'] = $clause['transfer_id'] = $clause['item_tax'] = NULL;
                $clause['net_unit_cost'] = $clause['real_unit_cost'] = $clause['unit_cost'] = $product->cost;
                $clause['quantity_balance'] = $clause['quantity'] = $clause['unit_quantity'] = $clause['quantity_received'] = $qty;

                $clause['net_unit_cost'] = $product->cost;
                $clause['price'] = $product->price;
                $clause['dropship'] = $product->dropship;
                $clause['crossdock'] = $product->crossdock;
                $clause['mrp'] = $product->mrp;
                $clause['discount_one'] = $product->discount_one;
                $clause['discount_two'] = $product->discount_two;
                $clause['discount_three'] = $product->discount_three;

                $clause['batch'] = 'opening';
                $clause['expiry'] = '30/12/2019 19:55';

                $clause['subtotal'] = ($product->cost * $qty);
                if (isset($product->tax_rate) && $product->tax_rate != 0) {
                    $tax_details = $this->site->getTaxRateByID($product->tax_rate);
                    $ctax = $this->calculateTax($product, $tax_details, $product->cost);
                    $item_tax = $clause['item_tax'] = $ctax['amount'];
                    $tax = $clause['tax'] = $ctax['tax'];
                    $clause['tax_rate_id'] = $tax_details->id;
                    if ($product->tax_method != 1) {
                        $clause['net_unit_cost'] = $product->cost - $item_tax;
                        $clause['unit_cost'] = $product->cost;
                    } else {
                        $clause['net_unit_cost'] = $product->cost;
                        $clause['unit_cost'] = $product->cost + $item_tax;
                    }
                    $pr_item_tax = $this->sma->formatDecimal($item_tax * $clause['unit_quantity'], 4);
                    if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                        $clause['gst'] = $gst_data['gst'];
                        $clause['cgst'] = $gst_data['cgst'];
                        $clause['sgst'] = $gst_data['sgst'];
                        $clause['igst'] = $gst_data['igst'];
                    }
                    $clause['subtotal'] = (($clause['net_unit_cost'] * $clause['unit_quantity']) + $pr_item_tax);
                }
                $clause['status'] = 'received';
                $clause['date'] = date('Y-m-d');
                $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;


                return $this->db->insert('purchase_items', $clause);
            }
        }
        return FALSE;
    }

    public function setPurchaseItemAfterTransferDelete($clause, $qty)
    {

        if ($product = $this->getProductByID($clause['product_id'])) {

            // $pi = $this->getPurchasedItemAfterTransfer($clause);
            // echo "123";
            // $this->sma->print_arrays($pi);

            if ($pi = $this->getPurchasedItemAfterTransfer($clause)) {

                // echo $pi->batch;
                // die();
                // exit();

                //$this->sma->print_arrays($pi->quantity_balance, $qty);

                // $quantity_balance = $pi->quantity_balance+$qty;
                $quantity_balance = $pi->quantity_balance + $qty;
                // echo $quantity_balance; 
                // die();
                // exit();
                // $this->sma->print_arrays($clause, $qty, $product, $pi, $quantity_balance);

                return $this->db->update('purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id, 'batch' => $pi->batch));
            } else {
                $unit = $this->getUnitByID($product->unit);
                $clause['product_unit_id'] = $product->unit;
                $clause['product_unit_code'] = $unit->code;
                $clause['product_code'] = $product->code;
                $clause['product_name'] = $product->name;
                $clause['purchase_id'] = $clause['transfer_id'] = $clause['item_tax'] = NULL;
                $clause['net_unit_cost'] = $clause['real_unit_cost'] = $clause['unit_cost'] = $product->cost;
                $clause['quantity_balance'] = $clause['quantity'] = $clause['unit_quantity'] = $clause['quantity_received'] = $qty;

                $clause['net_unit_cost'] = $product->cost;
                $clause['price'] = $product->price;
                $clause['dropship'] = $product->dropship;
                $clause['crossdock'] = $product->crossdock;
                $clause['mrp'] = $product->mrp;
                $clause['discount_one'] = $product->discount_one;
                $clause['discount_two'] = $product->discount_two;
                $clause['discount_three'] = $product->discount_three;

                $clause['batch'] = 'opening';
                $clause['expiry'] = '30/12/2019 19:55';

                $clause['subtotal'] = ($product->cost * $qty);
                if (isset($product->tax_rate) && $product->tax_rate != 0) {
                    $tax_details = $this->site->getTaxRateByID($product->tax_rate);
                    $ctax = $this->calculateTax($product, $tax_details, $product->cost);
                    $item_tax = $clause['item_tax'] = $ctax['amount'];
                    $tax = $clause['tax'] = $ctax['tax'];
                    $clause['tax_rate_id'] = $tax_details->id;
                    if ($product->tax_method != 1) {
                        $clause['net_unit_cost'] = $product->cost - $item_tax;
                        $clause['unit_cost'] = $product->cost;
                    } else {
                        $clause['net_unit_cost'] = $product->cost;
                        $clause['unit_cost'] = $product->cost + $item_tax;
                    }
                    $pr_item_tax = $this->sma->formatDecimal($item_tax * $clause['unit_quantity'], 4);
                    if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                        $clause['gst'] = $gst_data['gst'];
                        $clause['cgst'] = $gst_data['cgst'];
                        $clause['sgst'] = $gst_data['sgst'];
                        $clause['igst'] = $gst_data['igst'];
                    }
                    $clause['subtotal'] = (($clause['net_unit_cost'] * $clause['unit_quantity']) + $pr_item_tax);
                }
                $clause['status'] = 'received';
                $clause['date'] = date('Y-m-d');
                $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;


                return $this->db->insert('purchase_items', $clause);
            }
        }
        return FALSE;
    }


    public function setPurchaseItemAfterDelete($clause, $qty)
    {

        //$this->sma->print_arrays($clause, $qty);

        if ($product = $this->getProductByID($clause['product_id'])) {

            if ($pi = $this->getPurchasedItem($clause)) {

                //$this->sma->print_arrays($pi->quantity_balance, $qty);

                // $quantity_balance = $pi->quantity_balance+$qty;
                $quantity_balance = $pi->quantity_balance + $qty;

                // $this->sma->print_arrays($clause, $qty, $product, $pi, $quantity_balance);

                return $this->db->update('purchase_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id, 'batch' => $pi->batch));
            } else {
                $unit = $this->getUnitByID($product->unit);
                $clause['product_unit_id'] = $product->unit;
                $clause['product_unit_code'] = $unit->code;
                $clause['product_code'] = $product->code;
                $clause['product_name'] = $product->name;
                $clause['purchase_id'] = $clause['transfer_id'] = $clause['item_tax'] = NULL;
                $clause['net_unit_cost'] = $clause['real_unit_cost'] = $clause['unit_cost'] = $product->cost;
                $clause['quantity_balance'] = $clause['quantity'] = $clause['unit_quantity'] = $clause['quantity_received'] = $qty;

                $clause['net_unit_cost'] = $product->cost;
                $clause['price'] = $product->price;
                $clause['dropship'] = $product->dropship;
                $clause['crossdock'] = $product->crossdock;
                $clause['mrp'] = $product->mrp;
                $clause['discount_one'] = $product->discount_one;
                $clause['discount_two'] = $product->discount_two;
                $clause['discount_three'] = $product->discount_three;

                $clause['batch'] = 'opening';
                $clause['expiry'] = '30/12/2019 19:55';

                $clause['subtotal'] = ($product->cost * $qty);
                if (isset($product->tax_rate) && $product->tax_rate != 0) {
                    $tax_details = $this->site->getTaxRateByID($product->tax_rate);
                    $ctax = $this->calculateTax($product, $tax_details, $product->cost);
                    $item_tax = $clause['item_tax'] = $ctax['amount'];
                    $tax = $clause['tax'] = $ctax['tax'];
                    $clause['tax_rate_id'] = $tax_details->id;
                    if ($product->tax_method != 1) {
                        $clause['net_unit_cost'] = $product->cost - $item_tax;
                        $clause['unit_cost'] = $product->cost;
                    } else {
                        $clause['net_unit_cost'] = $product->cost;
                        $clause['unit_cost'] = $product->cost + $item_tax;
                    }
                    $pr_item_tax = $this->sma->formatDecimal($item_tax * $clause['unit_quantity'], 4);
                    if ($this->Settings->indian_gst && $gst_data = $this->gst->calculteIndianGST($pr_item_tax, ($this->Settings->state == $supplier_details->state), $tax_details)) {
                        $clause['gst'] = $gst_data['gst'];
                        $clause['cgst'] = $gst_data['cgst'];
                        $clause['sgst'] = $gst_data['sgst'];
                        $clause['igst'] = $gst_data['igst'];
                    }
                    $clause['subtotal'] = (($clause['net_unit_cost'] * $clause['unit_quantity']) + $pr_item_tax);
                }
                $clause['status'] = 'received';
                $clause['date'] = date('Y-m-d');
                $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;


                return $this->db->insert('purchase_items', $clause);
            }
        }
        return FALSE;
    }
    public function syncVariantQty($variant_id, $warehouse_id, $product_id = NULL)
    {
        $balance_qty = $this->getBalanceVariantQuantity($variant_id);
        $wh_balance_qty = $this->getBalanceVariantQuantity($variant_id, $warehouse_id);
        if ($this->db->update('product_variants', array('quantity' => $balance_qty), array('id' => $variant_id))) {
            if ($this->getWarehouseProductsVariants($variant_id, $warehouse_id)) {
                $this->db->update('warehouses_products_variants', array('quantity' => $wh_balance_qty), array('option_id' => $variant_id, 'warehouse_id' => $warehouse_id));
            } else {
                if ($wh_balance_qty) {
                    $this->db->insert('warehouses_products_variants', array('quantity' => $wh_balance_qty, 'option_id' => $variant_id, 'warehouse_id' => $warehouse_id, 'product_id' => $product_id));
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    public function getWarehouseProducts($product_id, $warehouse_id = NULL)
    {
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    // public function syncProductQty($product_id, $warehouse_id, $batch) {
    public function syncProductQty($product_id, $warehouse_id)
    {


        // echo "Product Id ".$product_id;
        // echo "<br><br>";
        // echo "Wherehouse Id ".$warehouse_id;
        // echo "<br><br>";


        // echo $batch;
        // die();
        // exit();

        // $balance_qty = $this->getBalanceQuantity($product_id, NULL, $batch);
        $balance_qty = $this->getBalanceQuantity($product_id, NULL);
        // $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id, $batch);
        $wh_balance_qty = $this->getBalanceQuantity($product_id, $warehouse_id);

        // echo "Balance qty ".$balance_qty;
        // echo "<br><br>";
        // echo "Wh Balance Qty ".$wh_balance_qty;
        // echo "<br><br>";

        // die();
        // exit();
        if($balance_qty == ""){
            $balance_qty = 0;
        }

        if ($this->db->update('products', array('quantity' => $balance_qty), array('id' => $product_id))) {

            // echo "111";

            if ($this->getWarehouseProducts($product_id, $warehouse_id)) {

                // echo "222";

                $this->db->update('warehouses_products', array('quantity' => $wh_balance_qty), array('product_id' => $product_id, 'warehouse_id' => $warehouse_id));
            } else {
                if (!$wh_balance_qty) {
                    $wh_balance_qty = 0;
                }
                $product = $this->site->getProductByID($product_id);
                $this->db->insert('warehouses_products', array('quantity' => $wh_balance_qty, 'product_id' => $product_id, 'warehouse_id' => $warehouse_id, 'avg_cost' => $product->cost));
            }

            // echo "Balance qty ".$balance_qty;
            // echo "<br><br>";
            // echo "Wh Balance Qty ".$wh_balance_qty;




            return TRUE;
        }
        return FALSE;
    }

    public function getSaleByID($id)
    {
        $this->db->select('sma_sales.*,IFNULL(sma_customer_limits.durration,0) as credit_durration');
        $this->db->from('sma_sales');
        $this->db->join('sma_customer_limits','sma_customer_limits.customer_id = sma_sales.customer_id AND sma_customer_limits.supplier_id = sma_sales.supplier_id','left');
        $this->db->where('sma_sales.id',$id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result()[0];
        }
        return FALSE;
    }

    public function getSalePayments($sale_id)
    {
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncSalePayments($id)
    {
        $sale = $this->getSaleByID($id);
        $due_detail = $this->getDueDate($sale->date,$sale->customer_id,$sale->supplier_id);
        if ($payments = $this->getSalePayments($id)) {
            $paid = 0;
            // $grand_total = $sale->grand_total + $sale->rounding;
            $grand_total = $sale->grand_total;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
            $payment_status = $paid == 0 ? 'pending' : $sale->payment_status;
            if ($this->sma->formatDecimal($grand_total) == $this->sma->formatDecimal($paid)) {
                $payment_status = 'paid';
            } elseif ($this->sma->formatDecimal($grand_total) < $this->sma->formatDecimal($paid)) {
                $payment_status = 'excess';
            } elseif ($due_detail['due_date'] <= date('Y-m-d') && !$sale->sale_id) {
                $payment_status = 'due';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }
            if ($this->db->update('sales', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        } else {
            $payment_status = ($due_detail['due_date'] <= date('Y-m-d')) ? 'due' : 'pending';
            if ($this->db->update('sales', array('paid' => 0, 'payment_status' => $payment_status), array('id' => $id))) {
                return true;
            }
        }

        return FALSE;
    }
    public function getDueDate($date,$cid,$sid){
        $this->db->select('durration');
        $this->db->from('sma_customer_limits');
        $this->db->where('supplier_id',$sid);
        $this->db->where('customer_id',$cid);
        $q = $this->db->get();
        if($q->num_rows() > 0){
            $re = $q->result()[0];
            $sendvalue['due_date'] = date('Y-m-d', strtotime($date. ' + '.$re->durration.' days'));
            $sendvalue['durration'] = $re->durration;
        }
        else{
            $sendvalue['due_date'] = date('Y-m-d', strtotime($date. ' + 90 days'));
            $sendvalue['durration'] = 90;
        }
        return $sendvalue;
    }
    public function getSupplierDueDate($date,$sid){
        $sendvalue['due_date'] = date('Y-m-d', strtotime($date. ' + 1000 days'));
        $sendvalue['durration'] = 1000;
        return $sendvalue;
    }

    public function getPurchaseByID($id)
    {
        $q = $this->db->get_where('purchases', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPurchasePayments($purchase_id)
    {
        $q = $this->db->get_where('payments', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncPurchasePayments($id)
    {
        $purchase = $this->getPurchaseByID($id);
        $paid = 0;
        if ($payments = $this->getPurchasePayments($id)) {
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
        }

        $payment_status = $paid <= 0 ? 'pending' : $purchase->payment_status;
        if ($this->sma->formatDecimal($purchase->grand_total) > $this->sma->formatDecimal($paid) && $paid > 0) {
            $payment_status = 'partial';
        } elseif ($this->sma->formatDecimal($purchase->grand_total) <= $this->sma->formatDecimal($paid)) {
            $payment_status = 'paid';
        }

        if ($this->db->update('purchases', array('paid' => $paid, 'payment_status' => $payment_status), array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    // private function getBalanceQuantity($product_id, $warehouse_id = NULL, $batch = NULL) {
    private function getBalanceQuantity($product_id, $warehouse_id = NULL)
    {

        // $this->db->save_queries = TRUE;

        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', false);
        $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        // $this->db->where('batch' , $batch);
        $q = $this->db->get('purchase_items');


        $getBalanceQuantity_last_query = $this->db->last_query();
        // echo $getBalanceQuantity_last_query."<br><br><br>";

        // die();
        // exit();


        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;


        // // $this->db->save_queries = TRUE;

        // $this->db->select('quantity_balance as stock', False);
        // $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0);
        // if ($warehouse_id) {
        //     $this->db->where('warehouse_id', $warehouse_id);
        // }
        // $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        // $this->db->where('batch' , $batch);
        // $q = $this->db->get('purchase_items');


        // $str = $this->db->last_query();
        // echo $str."<br><br><br>";

        // die();
        // exit();

        // if ($q->num_rows() > 0) {
        //     $data = $q->row();
        //     return $data->stock;
        // }
        // return 0;
    }

    private function getBalanceVariantQuantity($variant_id, $warehouse_id = NULL)
    {
        $this->db->select('SUM(COALESCE(quantity_balance, 0)) as stock', False);
        $this->db->where('option_id', $variant_id)->where('quantity_balance !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data->stock;
        }
        return 0;
    }

    public function calculateAVCost($product_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $product_name, $option_id, $item_quantity)
    {
        $product = $this->getProductByID($product_id);
        $real_item_qty = $quantity;
        $wp_details = $this->getWarehouseProduct($warehouse_id, $product_id);
        $con = $wp_details ? $wp_details->avg_cost : $product->cost;
        $tax_rate = $this->getTaxRateByID($product->tax_rate);
        $ctax = $this->calculateTax($product, $tax_rate, $con);
        if ($product->tax_method) {
            $avg_net_unit_cost = $con;
            $avg_unit_cost = ($con + $ctax['amount']);
        } else {
            $avg_unit_cost = $con;
            $avg_net_unit_cost = ($con - $ctax['amount']);
        }

        if ($pis = $this->getPurchasedItems($product_id, $warehouse_id, $option_id)) {

            $cost_row = array();
            $quantity = $item_quantity;
            $balance_qty = $quantity;
            foreach ($pis as $pi) {
                if (!empty($pi) && $pi->quantity > 0 && $balance_qty <= $quantity && $quantity != 0) {
                    if ($pi->quantity_balance >= $quantity && $quantity != 0) {
                        $balance_qty = $pi->quantity_balance - $quantity;
                        $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id);
                        $quantity = 0;
                    } elseif ($quantity != 0) {
                        $quantity = $quantity - $pi->quantity_balance;
                        $balance_qty = $quantity;
                        $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id);
                    }
                }
                if (empty($cost_row)) {
                    break;
                }
                $cost[] = $cost_row;
                if ($quantity == 0) {
                    break;
                }
            }
        }
        if ($quantity > 0 && !$this->Settings->overselling) {
            $this->session->set_flashdata('error', sprintf(lang("quantity_out_of_stock_for_%s"), ($pi->product_name ? $pi->product_name : $product_name)));
            redirect($_SERVER["HTTP_REFERER"]);
        } elseif ($quantity != 0) {
            $cost[] = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $real_item_qty, 'purchase_net_unit_cost' => $avg_net_unit_cost, 'purchase_unit_cost' => $avg_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => NULL, 'overselling' => 1, 'inventory' => 1);
            $cost[] = array('pi_overselling' => 1, 'product_id' => $product_id, 'quantity_balance' => (0 - $quantity), 'warehouse_id' => $warehouse_id, 'option_id' => $option_id);
        }
        return $cost;
    }

    public function calculateCost($product_id, $warehouse_id, $net_unit_price, $unit_price, $quantity, $product_name, $option_id, $item_quantity, $batch)
    {

        /*
       
        // // $pis = $this->getPurchaseProductByID($item['product_id'], $item['batch']);

        // $pis           = $this->getPurchasedItems($product_id, $warehouse_id, $option_id);

        // // foreach ($batch as $batchs) {
        //     // $pis = $this->getPurchasedItems($product_id, $warehouse_id, $option_id, $batch);
        // // }
       

        // // $pis = $this->getPurchasedItemsCalculateCost($product_id, $warehouse_id, $option_id, $batch);

        // echo "<pre>";
        // print_r($pis);
        // echo "</pre>";


        // $real_item_qty = $quantity;
        // $quantity = $item_quantity;
        // $balance_qty = $quantity;


        // // echo $product_id."<br><br>"; 
        // // echo $warehouse_id."<br><br>"; 
        // // echo $net_unit_price."<br><br>";
        // // echo $unit_price."<br><br>";
        // // echo $quantity."<br><br>";
        // // echo $product_name."<br><br>";
        // // echo $option_id."<br><br>";
        // // echo $item_quantity."<br><br>";
        // // echo $batch."<br><br>";
        // // echo $real_item_qty."<br><br>";
        // // echo $quantity."<br><br>";
        // // echo $balance_qty."<br><br>";

        // foreach ($pis as $pi) {
        //     $cost_row = NULL;
        //     if (!empty($pi) && $balance_qty <= $quantity && $quantity != 0) {
        //         $purchase_unit_cost = $pi->unit_cost ? $pi->unit_cost : ($pi->net_unit_cost + ($pi->item_tax / $pi->quantity));
        //         if ($pi->quantity_balance >= $quantity && $quantity != 0) {
        //             $balance_qty = $pi->quantity_balance - $quantity;
        //             $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id, 'batch' => $batch);
        //             $quantity = 0;
        //         } elseif ($quantity != 0) {
        //             $quantity = $quantity - $pi->quantity_balance;
        //             $balance_qty = $quantity;
        //             $cost_row = array('date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id, 'batch' => $batch);
        //         }
        //     }
        //     $cost[] = $cost_row;
        //     if ($quantity == 0) {
        //         break;
        //     }
        // }





        // echo "123";

        // $this->sma->print_arrays($cost);

        


        // if ($quantity > 0) {
        //     $this->session->set_flashdata('error', sprintf(lang("quantity_out_of_stock_for_%s"), (isset($pi->product_name) ? $pi->product_name : $product_name)));
        //     redirect($_SERVER["HTTP_REFERER"]);
        // }
        // return $cost;

        // $pis           = $this->getPurchasedItems($product_id, $warehouse_id, $option_id);

        */


        $pis = $this->getPurchasedItemsCalculateCost($product_id, $warehouse_id, $option_id, $batch);

        $real_item_qty = $quantity;
        $quantity      = $item_quantity;
        $balance_qty   = $quantity;
        foreach ($pis as $pi) {
            $cost_row = null;

            if (!empty($pi) && $balance_qty <= $quantity && $quantity != 0) {
                $purchase_unit_cost = $pi->unit_cost ? $pi->unit_cost : ($pi->net_unit_cost + ($pi->item_tax / $pi->quantity));
                if ($pi->quantity_balance >= $quantity && $quantity != 0) {
                    $balance_qty = $pi->quantity_balance - $quantity;
                    $cost_row    = ['date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $quantity, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => $balance_qty, 'inventory' => 1, 'option_id' => $option_id, 'batch' => $batch];
                    $quantity    = 0;
                } elseif ($quantity != 0) {
                    $quantity    = $quantity - $pi->quantity_balance;
                    $balance_qty = $quantity;
                    $cost_row    = ['date' => date('Y-m-d'), 'product_id' => $product_id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => $pi->id, 'quantity' => $pi->quantity_balance, 'purchase_net_unit_cost' => $pi->net_unit_cost, 'purchase_unit_cost' => $purchase_unit_cost, 'sale_net_unit_price' => $net_unit_price, 'sale_unit_price' => $unit_price, 'quantity_balance' => 0, 'inventory' => 1, 'option_id' => $option_id, 'batch' => $batch];
                }
            }
            $cost[] = $cost_row;
            if ($quantity == 0) {
                break;
            }
        }

        // $this->sma->print_arrays($quantity, $cost);

        if ($quantity > 0) {
            $this->session->set_flashdata('error', sprintf(lang('quantity_out_of_stock_for_%s'), (isset($pi->product_name) ? $pi->product_name : $product_name)));
            redirect($_SERVER['HTTP_REFERER']);
        }
        return $cost;
    }






    public function getPurchasedItems($product_id, $warehouse_id, $option_id = null, $nonPurchased = false)
    {
        // $this->db->save_queries = TRUE;

        $orderby = empty($this->Settings->accounting_method) ? 'asc' : 'desc';
        $this->db->select('id, quantity, quantity_balance, net_unit_cost, unit_cost, item_tax');
        $this->db->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('quantity_balance !=', 0);
        if (!isset($option_id) || empty($option_id)) {
            $this->db->group_start()->where('option_id', null)->or_where('option_id', 0)->group_end();
        } else {
            $this->db->where('option_id', $option_id);
        }
        if ($nonPurchased) {
            $this->db->group_start()->where('purchase_id !=', null)->or_where('transfer_id !=', null)->group_end();
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $this->db->group_by('id');
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        $q = $this->db->get('purchase_items');

        // $str = $this->db->last_query();
        // echo $str;

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            // print_r($data);
            // die();
            // exit();
            return $data;
        }
        return false;
    }

    // public function getPurchasedItems($product_id, $warehouse_id, $option_id = NULL) {

    //     // $this->db->save_queries = TRUE;


    //     $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
    //     $this->db->select('id, quantity, quantity_balance, net_unit_cost, unit_cost, item_tax');
    //     // $this->db->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('quantity_balance !=', 0);
    //     $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0);
    //     if (!isset($option_id) || empty($option_id)) {
    //         $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
    //     } else {
    //         $this->db->where('option_id', $option_id);
    //     }
    //     $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
    //     $this->db->group_by('id');
    //     $this->db->order_by('date', $orderby);
    //     $this->db->order_by('purchase_id', $orderby);
    //     $q = $this->db->get('purchase_items');



    //     // $errNo   = $this->db->_error_number();
    //     // $errMess = $this->db->_error_message();

    //     // $str = $this->db->last_query();
    //     // echo $str;

    //     // die();
    //     // exit();


    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as $row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return FALSE;
    // }




    public function getPurchasedItemsCalculateCost($product_id, $warehouse_id, $option_id = NULL, $batch)
    {





        // $this->db->save_queries = TRUE;

        $orderby = empty($this->Settings->accounting_method) ? 'asc' : 'desc';
        $this->db->select('id, quantity, quantity_balance, net_unit_cost, unit_cost, item_tax, batch');
        $this->db->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('quantity_balance !=', 0)->where('batch =', $batch);
        if (!isset($option_id) || empty($option_id)) {
            $this->db->group_start()->where('option_id', null)->or_where('option_id', 0)->group_end();
        } else {
            $this->db->where('option_id', $option_id);
        }
        if ($nonPurchased) {
            $this->db->group_start()->where('purchase_id !=', null)->or_where('transfer_id !=', null)->group_end();
        }
        $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        $this->db->group_by('id');
        $this->db->order_by('date', $orderby);
        $this->db->order_by('purchase_id', $orderby);
        $q = $this->db->get('purchase_items');

        // $str = $this->db->last_query();
        // echo $str;

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            // print_r($data);
            // die();
            // exit();
            return $data;
        }
        return false;












        // // $this->db->save_queries = TRUE;
        // $orderby = ($this->Settings->accounting_method == 1) ? 'asc' : 'desc';
        // $this->db->select('id, quantity, quantity_balance, net_unit_cost, unit_cost, item_tax, batch');
        // // $this->db->where('product_id', $product_id)->where('warehouse_id', $warehouse_id)->where('quantity_balance !=', 0);
        // $this->db->where('product_id', $product_id)->where('quantity_balance !=', 0)->where('batch !=', $batch);
        // if (!isset($option_id) || empty($option_id)) {
        //     $this->db->group_start()->where('option_id', NULL)->or_where('option_id', 0)->group_end();
        // } else {
        //     $this->db->where('option_id', $option_id);
        // }
        // $this->db->group_start()->where('status', 'received')->or_where('status', 'partial')->group_end();
        // $this->db->group_by('id');
        // $this->db->order_by('date', $orderby);
        // $this->db->order_by('purchase_id', $orderby);
        // $q = $this->db->get('purchase_items');


        // // $str = $this->db->last_query();
        // // echo $str;

        // // die();
        // // exit();

        // if ($q->num_rows() > 0) {
        //     foreach (($q->result()) as $row) {
        //         $data[] = $row;
        //     }

        //     print_r($data);
        //     die();
        //     exit();
        //     return $data;
        // }
        // return FALSE;
    }




    public function getProductComboItems($pid, $warehouse_id = NULL)
    {
        $this->db->select('products.id as id, combo_items.item_code as code, combo_items.quantity as qty, products.name as name, products.type as type, combo_items.unit_price as unit_price, warehouses_products.quantity as quantity')
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

    public function item_costing($item, $pi = NULL)
    {

        // $item_quantity = $pi ? '' : $item['quantity']; --> add main $item_quantity nahi mil rahi thi
        $item_quantity = $pi ? $item['aquantity'] : $item['quantity'];
        if (!isset($item['option_id']) || empty($item['option_id']) || $item['option_id'] == 'null') {
            $item['option_id'] = NULL;
        }

        if ($this->Settings->accounting_method != 2 && !$this->Settings->overselling) {

            // echo "a1";

            if ($this->getProductByID($item['product_id'])) {

                // echo "a2";

                if ($item['product_type'] == 'standard') {

                    // echo "a3";

                    // $this->sma->print_arrays($item);

                    $unit = $this->getUnitByID($item['product_unit_id']);

                    //    $this->sma->print_arrays($item);

                    // $this->sma->print_arrays($item);

                    $item['net_unit_price'] = $this->convertToBase($unit, $item['net_unit_price']);

                    $item['unit_price'] = $this->convertToBase($unit, $item['unit_price']);

                    $cost = $this->calculateCost(
                        $item['product_id'],
                        $item['warehouse_id'],
                        $item['net_unit_price'],
                        $item['unit_price'],
                        $item['quantity'],
                        $item['product_name'],
                        $item['option_id'],
                        $item_quantity,
                        $item['batch']
                    );
                } elseif ($item['product_type'] == 'combo') {
                    $combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getProductByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price = $combo_item->unit_price;
                            } else {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price = $combo_item->unit_price;
                        }
                        if ($pr->type == 'standard') {
                            $cost[] = $this->calculateCost($pr->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $pr->name, NULL, $item_quantity);
                        } else {
                            $cost[] = array(array('date' => date('Y-m-d'), 'product_id' => $pr->id, 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => ($combo_item->qty * $item['quantity']), 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $combo_item->unit_price, 'sale_unit_price' => $combo_item->unit_price, 'quantity_balance' => NULL, 'inventory' => NULL));
                        }
                    }
                } else {
                    $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
                }
            } elseif ($item['product_type'] == 'manual') {
                $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
            }
        } else {


            // echo "4321";

            if ($this->getProductByID($item['product_id'])) {
                if ($item['product_type'] == 'standard') {

                    // echo "4321";

                    $cost = $this->calculateAVCost($item['product_id'], $item['warehouse_id'], $item['net_unit_price'], $item['unit_price'], $item['quantity'], $item['product_name'], $item['option_id'], $item_quantity);
                } elseif ($item['product_type'] == 'combo') {
                    $combo_items = $this->getProductComboItems($item['product_id'], $item['warehouse_id']);
                    foreach ($combo_items as $combo_item) {
                        $pr = $this->getProductByCode($combo_item->code);
                        if ($pr->tax_rate) {
                            $pr_tax = $this->getTaxRateByID($pr->tax_rate);
                            if ($pr->tax_method) {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / (100 + $pr_tax->rate));
                                $net_unit_price = $combo_item->unit_price - $item_tax;
                                $unit_price = $combo_item->unit_price;
                            } else {
                                $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $pr_tax->rate) / 100);
                                $net_unit_price = $combo_item->unit_price;
                                $unit_price = $combo_item->unit_price + $item_tax;
                            }
                        } else {
                            $net_unit_price = $combo_item->unit_price;
                            $unit_price = $combo_item->unit_price;
                        }
                        $cost[] = $this->calculateAVCost($combo_item->id, $item['warehouse_id'], $net_unit_price, $unit_price, ($combo_item->qty * $item['quantity']), $item['product_name'], $item['option_id'], $item_quantity);
                    }
                } else {
                    $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
                }
            } elseif ($item['product_type'] == 'manual') {
                $cost = array(array('date' => date('Y-m-d'), 'product_id' => $item['product_id'], 'sale_item_id' => 'sale_items.id', 'purchase_item_id' => NULL, 'quantity' => $item['quantity'], 'purchase_net_unit_cost' => 0, 'purchase_unit_cost' => 0, 'sale_net_unit_price' => $item['net_unit_price'], 'sale_unit_price' => $item['unit_price'], 'quantity_balance' => NULL, 'inventory' => NULL));
            }
        }

        // $this->sma->print_arrays($cost);

        return $cost;
    }

    public function costing($items)
    {

        // $this->sma->print_arrays($items);


        $citems = array();
        foreach ($items as $item) {
            $option = (isset($item['option_id']) && !empty($item['option_id']) && $item['option_id'] != 'null' && $item['option_id'] != 'false') ? $item['option_id'] : '';
            // $pr = $this->getProductByID($item['product_id']);

            $prs = $this->getPurchaseProductByID($item['product_id'], $item['batch']);
            // $this->sma->print_arrays($prs);

            // echo "<pre>";
            //     Print_r($prs);
            // echo "<pre>";



            foreach ($prs as $pr) {
                $item['option_id'] = $option;
                if ($pr && $pr->type == 'standard') {

                    $citems['p' . $item['product_id'] . 'o' . $item['option_id'] . 'b' . $item['batch']] = $item;
                    $citems['p' . $item['product_id'] . 'o' . $item['option_id'] . 'b' . $item['batch']]['aquantity'] = $item['quantity'];

                    // if (isset($citems['p' . $item['product_id'] . 'o' . $item['option_id']])) {
                    //     $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'] += $item['quantity'];
                    // } else {
                    //     $citems['p' . $item['product_id'] . 'o' . $item['option_id']] = $item;
                    //     $citems['p' . $item['product_id'] . 'o' . $item['option_id']]['aquantity'] = $item['quantity'];
                    // }
                } elseif ($pr && $pr->type == 'combo') {
                    $wh = $this->Settings->overselling ? NULL : $item['warehouse_id'];
                    $combo_items = $this->getProductComboItems($item['product_id'], $wh);
                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard') {
                            if (isset($citems['p' . $combo_item->id . 'o' . $item['option_id']])) {
                                $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] += ($combo_item->qty * $item['quantity']);
                            } else {
                                $cpr = $this->getProductByID($combo_item->id);
                                if ($cpr->tax_rate) {
                                    $cpr_tax = $this->getTaxRateByID($cpr->tax_rate);
                                    if ($cpr->tax_method) {
                                        $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / (100 + $cpr_tax->rate));
                                        $net_unit_price = $combo_item->unit_price - $item_tax;
                                        $unit_price = $combo_item->unit_price;
                                    } else {
                                        $item_tax = $this->sma->formatDecimal((($combo_item->unit_price) * $cpr_tax->rate) / 100);
                                        $net_unit_price = $combo_item->unit_price;
                                        $unit_price = $combo_item->unit_price + $item_tax;
                                    }
                                } else {
                                    $net_unit_price = $combo_item->unit_price;
                                    $unit_price = $combo_item->unit_price;
                                }
                                $cproduct = array('product_id' => $combo_item->id, 'product_name' => $cpr->name, 'product_type' => $combo_item->type, 'quantity' => ($combo_item->qty * $item['quantity']), 'net_unit_price' => $net_unit_price, 'unit_price' => $unit_price, 'warehouse_id' => $item['warehouse_id'], 'item_tax' => $item_tax, 'tax_rate_id' => $cpr->tax_rate, 'tax' => ($cpr_tax->type == 1 ? $cpr_tax->rate . '%' : $cpr_tax->rate), 'option_id' => NULL, 'product_unit_id' => $cpr->unit);
                                $citems['p' . $combo_item->id . 'o' . $item['option_id']] = $cproduct;
                                $citems['p' . $combo_item->id . 'o' . $item['option_id']]['aquantity'] = ($combo_item->qty * $item['quantity']);
                            }
                        }
                    }
                }
            }
        }

        // $this->sma->print_arrays($citems);

        $cost = array();
        foreach ($citems as $item) {
            $item['aquantity'] = $citems['p' . $item['product_id'] . 'o' . $item['option_id'] . 'b' . $item['batch']]['aquantity'];

            // $this->sma->print_arrays($item);



            $cost[] = $this->item_costing($item, TRUE);
        }

        // echo "123";
        //$this->sma->print_arrays($cost);

        // die();
        // exit();
        return $cost;
    }

    public function syncQuantity($sale_id = NULL, $purchase_id = NULL, $oitems = NULL, $product_id = NULL)
    {

        // echo "1";

        if ($sale_id) {


            // echo "2";


            $sale_items = $this->getAllSaleItems($sale_id);
            foreach ($sale_items as $item) {
                if ($item->product_type == 'standard') {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }
                } elseif ($item->product_type == 'combo') {
                    $wh = $this->Settings->overselling ? NULL : $item->warehouse_id;
                    $combo_items = $this->getProductComboItems($item->product_id, $wh);
                    foreach ($combo_items as $combo_item) {
                        if ($combo_item->type == 'standard') {
                            $this->syncProductQty($combo_item->id, $item->warehouse_id);
                        }
                    }
                }
            }
        } elseif ($purchase_id) {

            // echo "3";

            $purchase_items = $this->getAllPurchaseItems($purchase_id);
            foreach ($purchase_items as $item) {
                $this->syncProductQty($item->product_id, $item->warehouse_id);
                if (isset($item->option_id) && !empty($item->option_id)) {
                    $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                }
            }
        } elseif ($oitems) {

            // echo "4";

            foreach ($oitems as $keys => $item) {




                if (isset($item->product_type)) {
                    if ($item->product_type == 'standard') {

                        // echo "<pre>";
                        // print_r($item);
                        // echo $item->selected_batch;
                        // echo "</pre>";

                        //    $this->syncProductQty($item->product_id, $item->warehouse_id, $item->selected_batch);
                        $this->syncProductQty($item->product_id, $item->warehouse_id);


                        // if( $keys == 2) {
                        //     die();
                        //     exit();   
                        // }



                        if (isset($item->option_id) && !empty($item->option_id)) {
                            $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                        }
                    } elseif ($item->product_type == 'combo') {
                        $combo_items = $this->getProductComboItems($item->product_id, $item->warehouse_id);
                        foreach ($combo_items as $combo_item) {
                            if ($combo_item->type == 'standard') {
                                $this->syncProductQty($combo_item->id, $item->warehouse_id);
                            }
                        }
                    }
                } else {
                    $this->syncProductQty($item->product_id, $item->warehouse_id);
                    if (isset($item->option_id) && !empty($item->option_id)) {
                        $this->syncVariantQty($item->option_id, $item->warehouse_id, $item->product_id);
                    }
                }
            }
        } elseif ($product_id) {
            $warehouses = $this->getAllWarehouses();
            foreach ($warehouses as $warehouse) {
                $this->syncProductQty($product_id, $warehouse->id);
                if ($product_variants = $this->getProductVariants($product_id)) {
                    foreach ($product_variants as $pv) {
                        $this->syncVariantQty($pv->id, $warehouse->id, $product_id);
                    }
                }
            }
        }
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

    public function getAllSaleItems($sale_id)
    {
        $q = $this->db->get_where('sale_items', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllPurchaseItems($purchase_id)
    {
        $q = $this->db->get_where('purchase_items', array('purchase_id' => $purchase_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function syncPurchaseItems($data = array(), $warehouse_id)
    {

    
        if (!empty($data)) {
            foreach ($data as $items) {
                foreach ($items as $item) {

                    // $this->sma->print_arrays($item);
                    // echo "3";
                    if (isset($item['pi_overselling'])) {
                        // echo "4";
                        // $this->sma->print_arrays($data);
                        unset($item['pi_overselling']);
                        $option_id = (isset($item['option_id']) && !empty($item['option_id'])) ? $item['option_id'] : NULL;
                        $clause = array('purchase_id' => NULL, 'transfer_id' => NULL, 'product_id' => $item['product_id'], 'warehouse_id' => $item['warehouse_id'], 'option_id' => $option_id);

                        if ($pi = $this->getPurchasedItem($clause)) {
                            // echo "5";
                            // $this->sma->print_arrays($data);
                            // $quantity_balance = $pi->quantity_balance - $item['quantity_balance'];


                            $quantity_balance = $pi->quantity_balance + $item['quantity_balance'];
                            echo  $quantity_balance;
                            echo 'Test Case 1 Fail';
                            die;

                            $this->db->update('purchasae_items', array('quantity_balance' => $quantity_balance), array('id' => $pi->id));


                        } else {
                            // echo "6";
                            // $this->sma->print_arrays($data);
                            $clause['quantity'] = 0;
                            $clause['item_tax'] = 0;
                            $clause['quantity_balance'] = $item['quantity_balance'];
                            $clause['status'] = 'received';
                            $clause['option_id'] = !empty($clause['option_id']) && is_numeric($clause['option_id']) ? $clause['option_id'] : NULL;

                            echo 'Test Case 2 Fail';
                            die;

                            $this->db->insert('purchase_items', $clause);
                        }
                    } else {
                        // echo "7";
                        if ($item['inventory']) {
                            // // $this->db->save_queries = TRUE;
                            // $this->db->update('purchase_items', array('quantity_balance' => $item['quantity_balance']), array('id' => $item['purchase_item_id'], 'batch' => $item['batch']));

                           // echo 'Test Case 3 Fail';
                            
                            // $this->db->update('purchase_items', array('quantity_balance' => $item['quantity_balance']), array(
                            //     'batch' => $item['batch'], 
                            //     'product_id' => $item['product_id'],
                            //     'warehouse_id' => $warehouse_id,
                            //     ));
                            $this->db->update('purchase_items', array('quantity_balance' => $item['quantity_balance']), array(
                                'id' => $item['purchase_item_id'] 
                                )); 
                            //die;
                            // $str = $this->db->last_query();
                            // echo $str;
                            // $this->sma->print_arrays($item);
                        }
                    }
                }
            }

            // die();
            // exit();

            return TRUE;
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

    public function check_customer_deposit($customer_id, $amount)
    {
        $customer = $this->getCompanyByID($customer_id);
        return $customer->deposit_amount >= $amount;
    }

    public function getWarehouseProduct($warehouse_id, $product_id)
    {
        $q = $this->db->get_where('warehouses_products', array('product_id' => $product_id, 'warehouse_id' => $warehouse_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllBaseUnits()
    {
        $q = $this->db->get_where("units", array('base_unit' => NULL));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUnitsByBUID($base_unit)
    {
        $this->db->where('id', $base_unit)->or_where('base_unit', $base_unit)
            ->group_by('id')->order_by('id asc');
        $q = $this->db->get("units");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getUnitByID($id)
    {
        $q = $this->db->get_where("units", array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getPriceGroupByID($id)
    {
        $q = $this->db->get_where('price_groups', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductGroupPrice($product_id, $group_id)
    {
        $q = $this->db->get_where('product_prices', array('price_group_id' => $group_id, 'product_id' => $product_id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllBrands()
    {
        $q = $this->db->get("brands");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getBrandByID($id)
    {
        $q = $this->db->get_where('brands', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSupplierByID($id)
    {
        $q = $this->db->get_where('companies', array('group_name' => 'supplier', 'id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getLedgerReport()
    {
        $q = $this->db->get("own_companies");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }


    public function getAllown_companies()
    {
        $q = $this->db->get("own_companies");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    

    public function getown_companiesByID($id)
    {
        $q = $this->db->get_where('own_companies', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function convertToBase($unit, $value)
    {
        switch ($unit->operator) {
            case '*':
                return $value / $unit->operation_value;
                break;
            case '/':
                return $value * $unit->operation_value;
                break;
            case '+':
                return $value - $unit->operation_value;
                break;
            case '-':
                return $value + $unit->operation_value;
                break;
            default:
                return $value;
        }
    }

    function calculateTax($product_details = NULL, $tax_details, $custom_value = NULL, $c_on = NULL)
    {
        $value = $custom_value ? $custom_value : (($c_on == 'cost') ? $product_details->cost : $product_details->price);




        $tax_amount = 0;
        $tax = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
            // echo "a";
            if ($product_details && $product_details->tax_method == 1) {
                // echo "b";

                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / 100, 4);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            } else {
                // echo "c" + $tax_details->rate;

                $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                // $tax_amount = $this->sma->formatDecimal((($value) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                $tax = $this->sma->formatDecimal($tax_details->rate, 0) . "%";
            }
        } elseif ($tax_details && $tax_details->type == 2) {
            // echo "d";

            $tax_amount = $this->sma->formatDecimal($tax_details->rate);
            $tax = $this->sma->formatDecimal($tax_details->rate, 0);
        }

        return array('id' => $tax_details->id, 'tax' => $tax, 'amount' => $tax_amount);
    }

    public function getAddressByID($id)
    {
        return $this->db->get_where('addresses', ['id' => $id], 1)->row();
    }

    public function checkSlug($slug, $type = NULL)
    {
        if (!$type) {
            return $this->db->get_where('products', ['slug' => $slug], 1)->row();
        } elseif ($type == 'category') {
            return $this->db->get_where('categories', ['slug' => $slug], 1)->row();
        } elseif ($type == 'brand') {
            return $this->db->get_where('brands', ['slug' => $slug], 1)->row();
        }
        return FALSE;
    }

    public function calculateDiscount($discount = NULL, $amount)
    {
        if ($discount && $this->Settings->product_discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {
                $pds = explode("%", $discount);
                return $this->sma->formatDecimal(((($this->sma->formatDecimal($amount)) * (float) ($pds[0])) / 100), 4);
            } else {
                return $this->sma->formatDecimal($discount, 4);
            }
        }
        return 0;
    }

    public function calculateOrderTax($order_tax_id = NULL, $amount)
    {
        if ($this->Settings->tax2 != 0 && $order_tax_id) {
            if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                if ($order_tax_details->type == 1) {
                    return $this->sma->formatDecimal((($amount * $order_tax_details->rate) / 100), 4);
                } else {
                    return $this->sma->formatDecimal($order_tax_details->rate, 4);
                }
            }
        }
        return 0;
    }

    public function getSmsSettings()
    {
        $q = $this->db->get('sms_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function fed_tax($q = NULL)
    {
        $query = $this->db->query('SELECT fed_tax from sma_settings');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return FALSE;
    }


    public function gst_tax($q = NULL)
    {
        $query = $this->db->query('SELECT gst_tax from sma_settings');
        if ($query->num_rows() > 0) {
            return $query->row();
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
}
