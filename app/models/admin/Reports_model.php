<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Reports_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }
    // New Code
    public function registers(){
        // Count Total Rows
        $this->db->from('pos_register');
        $totalq = $this->db->get();
        $this->registers_query('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->registers_query();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = "";
            $button .= '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/reports/registers_detail/".$row->id).'" >Detail</a>';
            $status = ucwords($row->status);
            $data[] = array(
                $row->date,
                $row->closed_at,
                $row->created_by,
                $row->cash_in_hand,
                $row->total_cash_submitted,
                $row->total_cash,
                $row->status,
                $button
            );
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $totalq->num_rows(),
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );
        // Output to JSON format
        echo json_encode($output);
    }
    public function registers_query($onlycoun = "no"){
        $column_search = array();
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('register.id as id');
        }
        else{
            $this->db->select('
                register.*,
                CONCAT(u.first_name," ",u.last_name) as created_by,
            ');
        }
        $this->db->from('pos_register as register');
        $this->db->join('users as u', 'u.id = register.user_id', 'left');
        $i = 0;
        // loop searchable columns 
        if($onlycoun != "yes"){
            foreach($column_search as $item){
                // if datatable send POST for search
                if($_POST['search']['value']){
                    // first loop
                    if($i===0){
                        // open bracket
                        $this->db->group_start();
                        $this->db->like($item, $_POST['search']['value']);
                    }else{
                        $this->db->or_like($item, $_POST['search']['value']);
                    }
                    // last loop
                    if(count($column_search) - 1 == $i){
                        // close bracket
                        $this->db->group_end();
                    }
                }
                $i++;
            }
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }
    public function salesreport_ajax(){
        // Count Total Rows
        $this->db->from('pos_register');
        $totalq = $this->db->get();
        $this->registers_query('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->registers_query();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();

        $data = array();
        foreach($rows as $row){
            $button = "";
            $button .= '<a class="md-btn md-btn-success md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/reports/registers_detail/".$row->id).'" >Detail</a>';
            $status = ucwords($row->status);
            $data[] = array(
                $row->date,
                $row->closed_at,
                $row->created_by,
                $row->cash_in_hand,
                $row->total_cash_submitted,
                $row->total_cash,
                $row->status,
                $button
            );
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $totalq->num_rows(),
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );
        // Output to JSON format
        echo json_encode($output);
    }
    public function salesreport_ajax_query($onlycoun = "no"){
        $column_search = array();
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('register.id as id');
        }
        else{
            $this->db->select('
                register.*,
                CONCAT(u.first_name," ",u.last_name) as created_by,
            ');
        }
        $this->db->from('pos_register as register');
        $this->db->join('users as u', 'u.id = register.user_id', 'left');
        $i = 0;
        // loop searchable columns 
        if($onlycoun != "yes"){
            foreach($column_search as $item){
                // if datatable send POST for search
                if($_POST['search']['value']){
                    // first loop
                    if($i===0){
                        // open bracket
                        $this->db->group_start();
                        $this->db->like($item, $_POST['search']['value']);
                    }else{
                        $this->db->or_like($item, $_POST['search']['value']);
                    }
                    // last loop
                    if(count($column_search) - 1 == $i){
                        // close bracket
                        $this->db->group_end();
                    }
                }
                $i++;
            }
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }


    // Old Coce
    public function getListReporting($report_type, $own_company, $biller, $category, $subcategory, $brand, $warehouse, $start_date, $end_date){
        /* Show Below Detail 
            Report Type
            Company Name
            Item Code
            Item Batch
            Quantity
            Supplier
            Brand
            Purchase Price
            Consignment Price
            Expiry
        */
        // if($report_type == '0') {
        //     $q = $this->db->query("SELECT * FROM `sma_products`"); 
        //     if ($q->num_rows() > 0) {
        //         return $q->row();
        //     }
        // }
        // else if($report_type == '1') {
        //     $this->db->query("SELECT 
        //     sma_sales.`id`,
        //     sma_sales.`date`,
        //     sma_sales.`reference_no`,
        //     sma_sales.`customer_id`,
        //     sma_sales.`own_company`,
        //     sma_sales.`po_number`,
        //     sma_sales.`customer`,
        //     sma_sales.`biller_id`,
        //     sma_sales.`biller`,
        //     sma_sales.`warehouse_id`,
        //     sma_sales.`note`,
        //     sma_sales.`staff_note`,
        //     sma_sales.`total`,
        //     sma_sales.`product_discount`,
        //     sma_sales.`order_discount_id`,
        //     sma_sales.`total_discount`,
        //     sma_sales.`order_discount`,
        //     sma_sales.`product_tax`,
        //     sma_sales.`order_tax_id`,
        //     sma_sales.`order_tax`,
        //     sma_sales.`total_tax`,
        //     sma_sales.`shipping`,
        //     sma_sales.`grand_total`,
        //     sma_sales.`sale_status`,
        //     sma_sales.`payment_status`,
        //     sma_sales.`payment_term`,
        //     sma_sales.`due_date`,
        //     sma_sales.`created_by`,
        //     sma_sales.`updated_by`,
        //     sma_sales.`updated_at`,
        //     sma_sales.`total_items`,
        //     sma_sales.`pos`,
        //     sma_sales.`paid`,
        //     sma_sales.`return_id`,
        //     sma_sales.`surcharge`,
        //     sma_sales.`attachment`,
        //     sma_sales.`return_sale_ref`,
        //     sma_sales.`sale_id`,
        //     sma_sales.`return_sale_total`,
        //     sma_sales.`rounding`,
        //     sma_sales.`suspend_note`,
        //     sma_sales.`api`,
        //     sma_sales.`shop`,
        //     sma_sales.`address_id`,
        //     sma_sales.`reserve_id`,
        //     sma_sales.`hash`,
        //     sma_sales.`manual_payment`,
        //     sma_sales.`cgst`,
        //     sma_sales.`sgst`,
        //     sma_sales.`igst`,
        //     sma_sales.`payment_method`,
            
        //     sma_sale_items.`id`,
        //     sma_sale_items.`sale_id`,
        //     sma_sale_items.`product_id`,
        //     sma_sale_items.`product_code`,
        //     sma_sale_items.`company_code`,
        //     sma_sale_items.`product_name`,
        //     sma_sale_items.`product_type`,
        //     sma_sale_items.`option_id`,
        //     sma_sale_items.`net_unit_price`,
        //     sma_sale_items.`unit_price`,
        //     sma_sale_items.`dropship`,
        //     sma_sale_items.`crossdock`,
        //     sma_sale_items.`mrp`,
        //     sma_sale_items.`expiry`,
        //     sma_sale_items.`batch`,
        //     sma_sale_items.`quantity`,
        //     sma_sale_items.`warehouse_id`,
        //     sma_sale_items.`item_tax`,
        //     sma_sale_items.`tax_rate_id`,
        //     sma_sale_items.`tax`,
        //     sma_sale_items.`discount`,
        //     sma_sale_items.`item_discount`,
        //     sma_sale_items.`subtotal`,
        //     sma_sale_items.`serial_no`,
        //     sma_sale_items.`real_unit_price`,
        //     sma_sale_items.`sale_item_id`,
        //     sma_sale_items.`product_unit_id`,
        //     sma_sale_items.`product_unit_code`,
        //     sma_sale_items.`unit_quantity`,
        //     sma_sale_items.`comment`,
        //     sma_sale_items.`gst`,
        //     sma_sale_items.`cgst`,
        //     sma_sale_items.`sgst`,
        //     sma_sale_items.`igst`,
        //     sma_sale_items.`discount_one`,
        //     sma_sale_items.`discount_two`,
        //     sma_sale_items.`discount_three`,
        //     sma_sale_items.`product_price`,
        //     sma_sale_items.`further_tax`
        //     FROM sma_sales left join sma_sale_items ON sma_sales.id = sma_sale_items.sale_id 
        // ");
        // } else {
        //     $this->db->query("select * from purchase");
        // }
        
    }
    public function getProductNames($term, $limit = 5){
        $this->db->select('id, code, name')
            ->like('name', $term, 'both')->or_like('code', $term, 'both');
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getStaff(){
        if ($this->Admin) {
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
    public function getSalesTotals($customer_id){

        $this->db->select('SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
            ->where('customer_id', $customer_id);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCustomerSales($customer_id){
        $this->db->from('sales')->where('customer_id', $customer_id);
        return $this->db->count_all_results();
    }
    public function getCustomerQuotes($customer_id){
        $this->db->from('quotes')->where('customer_id', $customer_id);
        return $this->db->count_all_results();
    }
    public function getCustomerReturns($customer_id){
        $this->db->from('sales')->where('customer_id', $customer_id)->where('sale_status', 'returned');
        return $this->db->count_all_results();
    }
    public function getStockValue(){
        $q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select COALESCE(sum(" . $this->db->dbprefix('warehouses_products') . ".quantity), 0)*price as by_price, COALESCE(sum(" . $this->db->dbprefix('warehouses_products') . ".quantity), 0)*cost as by_cost FROM " . $this->db->dbprefix('products') . " JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id GROUP BY " . $this->db->dbprefix('products') . ".id )a");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getWarehouseStockValue($id){
        $q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0))*price as by_price, sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0))*cost as by_cost FROM " . $this->db->dbprefix('products') . " JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id WHERE " . $this->db->dbprefix('warehouses_products') . ".warehouse_id = ? GROUP BY " . $this->db->dbprefix('products') . ".id )a", array($id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getChartData(){
        $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE( P.purchases, 0 ) as purchases,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE(S.tax2, 0) as tax2,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(product_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S
            LEFT JOIN ( SELECT  date_format(date, '%Y-%m') Month,
                        SUM(product_tax) ptax,
                        SUM(order_tax) otax,
                        SUM(total) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
                        GROUP BY date_format(date, '%Y-%m')) P
            ON S.Month = P.Month
            ORDER BY S.Month";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getDailySales($year, $month, $warehouse_id = NULL){
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getMonthlySales($year, $warehouse_id = NULL){
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getStaffDailySales($user_id, $year, $month, $warehouse_id = NULL){
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales')." WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getStaffMonthlySales($user_id, $year, $warehouse_id = NULL){
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getPurchasesTotals($supplier_id){
        $this->db->select('SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
            ->where('supplier_id', $supplier_id);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getSupplierPurchases($supplier_id){
        $this->db->from('purchases')->where('supplier_id', $supplier_id);
        return $this->db->count_all_results();
    }
    public function getStaffPurchases($user_id){
        $this->db->select('count(id) as total, SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
            ->where('created_by', $user_id);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getStaffSales($user_id){
        $this->db->select('count(id) as total, SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
            ->where('created_by', $user_id);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalSales($start, $end, $warehouse_id = NULL){
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', FALSE)
            ->where('sale_status !=', 'pending')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalReturnSales($start, $end, $warehouse_id = NULL){
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', FALSE)
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('returns');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalPurchases($start, $end, $warehouse_id = NULL){
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', FALSE)
            ->where('status !=', 'pending')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalExpenses($start, $end, $warehouse_id = NULL){
        $this->db->select('count(id) as total, sum(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalPaidAmount($start, $end){
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'sent')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalReceivedAmount($start, $end){
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalReceivedCashAmount($start, $end){
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'cash')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalReceivedCCAmount($start, $end){
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'CC')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalReceivedChequeAmount($start, $end){
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'Cheque')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalReceivedPPPAmount($start, $end){
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'ppp')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalReceivedStripeAmount($start, $end){
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'received')->where('paid_by', 'stripe')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getTotalReturnedAmount($start, $end){
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
            ->where('type', 'returned')
            ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getWarehouseTotals($warehouse_id = NULL){
        $this->db->select('sum(quantity) as total_quantity, count(id) as total_items', FALSE);
        $this->db->where('quantity !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getCosting($date, $warehouse_id = NULL, $year = NULL, $month = NULL){
        $this->db->select('SUM( COALESCE( purchase_unit_cost, 0 ) * quantity ) AS cost, SUM( COALESCE( sale_unit_price, 0 ) * quantity ) AS sales, SUM( COALESCE( purchase_net_unit_cost, 0 ) * quantity ) AS net_cost, SUM( COALESCE( sale_net_unit_price, 0 ) * quantity ) AS net_sales', FALSE);
        if ($date) {
            $this->db->where('costing.date', $date);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('costing.date >=', $year.'-'.$month.'-01 00:00:00');
            $this->db->where('costing.date <=', $year.'-'.$month.'-'.$last_day.' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->join('sales', 'sales.id=costing.sale_id')
            ->where('sales.warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('costing');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getExpenses($date, $warehouse_id = NULL, $year = NULL, $month = NULL){
        $sdate = $date.' 00:00:00';
        $edate = $date.' 23:59:59';
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE);
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year.'-'.$month.'-01 00:00:00');
            $this->db->where('date <=', $year.'-'.$month.'-'.$last_day.' 23:59:59');
        }


        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getReturns($date, $warehouse_id = NULL, $year = NULL, $month = NULL){
        $sdate = $date.' 00:00:00';
        $edate = $date.' 23:59:59';
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total', FALSE)
        ->where('sale_status', 'returned');
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year.'-'.$month.'-01 00:00:00');
            $this->db->where('date <=', $year.'-'.$month.'-'.$last_day.' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function getOrderDiscount($date, $warehouse_id = NULL, $year = NULL, $month = NULL){
        $sdate = $date.' 00:00:00';
        $edate = $date.' 23:59:59';
        $this->db->select('SUM( COALESCE( order_discount, 0 ) ) AS order_discount', FALSE);
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year.'-'.$month.'-01 00:00:00');
            $this->db->where('date <=', $year.'-'.$month.'-'.$last_day.' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
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
    public function getDailyPurchases($year, $month, $warehouse_id = NULL){
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getMonthlyPurchases($year, $warehouse_id = NULL){
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getStaffDailyPurchases($user_id, $year, $month, $warehouse_id = NULL){
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases')." WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getStaffMonthlyPurchases($user_id, $year, $warehouse_id = NULL){
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id = {$warehouse_id} AND ";
        }
        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function getBestSeller($start_date, $end_date, $warehouse_id = NULL){
        $this->db
            ->select("product_name, product_code")->select_sum('quantity')
            ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            ->where('date >=', $start_date)->where('date <=', $end_date)
            ->group_by('product_name, product_code')->order_by('sum(quantity)', 'desc')->limit(10);
        if ($warehouse_id) {
            $this->db->where('sale_items.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    function getPOSSetting(){
        $q = $this->db->get('pos_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    function getSalesTax($start_date = null, $end_date = null){
        $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('paid');
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    function getPurchasesTax($start_date = null, $end_date = null){
        $this->db->select_sum('igst')->select_sum('cgst')->select_sum('sgst')
            ->select_sum('product_tax')->select_sum('order_tax')
            ->select_sum('grand_total')->select_sum('paid');
        if ($start_date) {
            $this->db->where('date >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('date <=', $end_date);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getList($subcategory_id){
        if($subcategory_id == 1) {

            $this->db->select('id as id, name as text');
            $q = $this->db->get("brands");
        } else if($subcategory_id == 2) {
            $this->db->select('id as id, name as text');
            $this->db->where('group_name =', "biller");
            $q = $this->db->get("companies");

        } else {
            $this->db->select('id as id, name as text');
            $this->db->where('group_name =', "supplier");
            $q = $this->db->get("companies");
        }

        // // $this->db->save_queries = TRUE;

       
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }

        //  echo $this->db->last_query();

        return FALSE;
    }
    public function getPurchaseLedgerReport(){

        //     $myQuery = "SELECT
        //     `sma_payments`.`id` as 'payment_id',
        //     `sma_payments`.`date` as 'payment_date',
        //     `sma_payments`.`sale_id` as 'payment_sale_id',
        //     `sma_payments`.`return_id` as 'payment_return_id',
        //     `sma_payments`.`purchase_id` as 'payment_purchase_id',
        //     `sma_payments`.`reference_no` as 'payment_reference_no',
        //     `sma_payments`.`transaction_id` as 'payment_transaction_id',
        //     `sma_payments`.`paid_by` as 'payment_paid_by',
        //     `sma_payments`.`cheque_no` as 'payment_cheque_no',
        //     `sma_payments`.`cc_no` as 'payment_cc_no',
        //     `sma_payments`.`cc_holder` as 'payment_cc_holder',
        //     `sma_payments`.`cc_month` as 'payment_cc_month',
        //     `sma_payments`.`cc_year` as 'payment_cc_year',
        //     `sma_payments`.`cc_type` as 'payment_cc_type',
        //     `sma_payments`.`amount` as 'payment_amount',
        //     `sma_payments`.`currency` as 'payment_currency',
        //     `sma_payments`.`created_by` as 'payment_created_by',
        //     `sma_payments`.`attachment` as 'payment_attachment',
        //     `sma_payments`.`type` as 'payment_type',
        //     `sma_payments`.`note` as 'payment_note',
        //     `sma_payments`.`pos_paid` as 'payment_pos_paid',
        //     `sma_payments`.`pos_balance` as 'payment_pos_balance',
        //     `sma_payments`.`approval_code` as 'payment_approval_code',
            
        //     `sma_purchases`.`id` as 'purchases_id',
        //     `sma_purchases`.`reference_no` as 'purchases_reference_no',
        //     `sma_purchases`.`date` as 'purchases_date',
        //     `sma_purchases`.`supplier_id` as 'purchases_supplier_id',
        //     `sma_purchases`.`supplier` as 'purchases_supplier',
        //     `sma_purchases`.`warehouse_id` as 'purchases_warehouse_id',
        //     `sma_purchases`.`own_company` as 'purchases_own_company',
        //     `sma_purchases`.`note` as 'purchases_note',
        //     `sma_purchases`.`total` as 'purchases_total',
        //     `sma_purchases`.`product_discount` as 'purchases_product_discount',
        //     `sma_purchases`.`order_discount_id` as 'purchases_order_discount_id',
        //     `sma_purchases`.`order_discount` as 'purchases_order_discount',
        //     `sma_purchases`.`total_discount` as 'purchases_total_discount',
        //     `sma_purchases`.`product_tax` as 'purchases_product_tax',
        //     `sma_purchases`.`order_tax_id` as 'purchases_order_tax_id',
        //     `sma_purchases`.`order_tax` as 'purchases_order_tax',
        //     `sma_purchases`.`total_tax` as 'purchases_total_tax',
        //     `sma_purchases`.`shipping` as 'purchases_shipping',
        //     `sma_purchases`.`grand_total` as 'purchases_grand_total',
        //     `sma_purchases`.`paid` as 'purchases_paid',
        //     `sma_purchases`.`status` as 'purchases_status',
        //     `sma_purchases`.`payment_status` as 'purchases_purchases_status',
        //     `sma_purchases`.`payment_term` as 'purchases_payment_term',
        //     `sma_purchases`.`due_date` as 'purchases_due_date'
        // FROM
        //     `sma_payments`
        // LEFT JOIN `sma_purchases`
        // ON sma_payments.purchase_id = sma_purchases.id  
        // ORDER BY `sma_payments`.`date`  ASC";



        $myQuery = "SELECT
            `sma_purchases`.`date` as 'purchases_date',
            `sma_purchases`.`supplier` as 'purchases_supplier',
            `sma_purchases`.`reference_no` as 'purchases_reference_no',
            
            `sma_purchases`.`total` as 'purchases_total',
            `sma_purchases`.`product_discount` as 'purchases_product_discount',
            `sma_purchases`.`order_discount_id` as 'purchases_order_discount_id',
            `sma_purchases`.`order_discount` as 'purchases_order_discount',
            `sma_purchases`.`total_discount` as 'purchases_total_discount',
            `sma_purchases`.`product_tax` as 'purchases_product_tax',
            `sma_purchases`.`order_tax_id` as 'purchases_order_tax_id',
            `sma_purchases`.`order_tax` as 'purchases_order_tax',
            `sma_purchases`.`total_tax` as 'purchases_total_tax',
            `sma_purchases`.`shipping` as 'purchases_shipping',
            `sma_purchases`.`grand_total` as 'purchases_grand_total',
            

            `sma_payments`.`amount` as 'payment_amount',
            `sma_payments`.`date` as 'payment_date',

            `sma_payments`.`type` as 'payment_type',
            `sma_payments`.`cheque_no` as 'payment_cheque_no',
            `sma_purchases`.`paid` as 'purchases_paid',
            `sma_purchases`.`status` as 'purchases_status',
            `sma_purchases`.`payment_status` as 'purchases_purchases_status',

            
            `sma_payments`.`return_id` as 'payment_return_id',
            `sma_payments`.`purchase_id` as 'payment_purchase_id',
            `sma_payments`.`reference_no` as 'payment_reference_no',
            `sma_payments`.`transaction_id` as 'payment_transaction_id',
            `sma_payments`.`paid_by` as 'payment_paid_by',
            
            `sma_payments`.`cc_no` as 'payment_cc_no',
            `sma_payments`.`cc_holder` as 'payment_cc_holder',
            `sma_payments`.`cc_month` as 'payment_cc_month',
            `sma_payments`.`cc_year` as 'payment_cc_year',
            `sma_payments`.`cc_type` as 'payment_cc_type',
            `sma_payments`.`currency` as 'payment_currency',
            `sma_payments`.`created_by` as 'payment_created_by',
            `sma_payments`.`attachment` as 'payment_attachment',
        
            `sma_payments`.`note` as 'payment_note',

            
            `sma_purchases`.`id` as 'purchases_id',
            
            `sma_purchases`.`supplier_id` as 'purchases_supplier_id',
            `sma_purchases`.`warehouse_id` as 'purchases_warehouse_id',
            `sma_purchases`.`own_company` as 'purchases_own_company',
            `sma_purchases`.`note` as 'purchases_note',
            
            
            
            `sma_purchases`.`payment_term` as 'purchases_payment_term',
            `sma_purchases`.`due_date` as 'purchases_due_date'
        FROM
            `sma_payments`
        LEFT JOIN `sma_purchases`
        ON sma_payments.purchase_id = sma_purchases.id  
        ORDER BY `sma_payments`.`date`  ASC";



        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    // ---------------------New Code By Ismail FSD--------------------------- //
    public function customers(){
        $this->db->select('id,name as name');
        $this->db->from('companies');
        $this->db->where('group_name', 'customer');
        $q = $this->db->get();
        return $q->result();
    }
    public function suppliers(){
        $this->db->select('id,name as name');
        $this->db->from('companies');
        $this->db->where('group_name', 'supplier');
        $q = $this->db->get();
        return $q->result();
    }
    public function companies(){
        $this->db->select('id,companyname as name');
        $this->db->from('sma_own_companies');
        $q = $this->db->get();
        return $q->result();
    }
    public function customer_wht_legder($customer,$supplier,$company,$start,$end,$sorttype){
        $finaeldata = array();
        $sendvalue = array();
        $bcredit = 0;
        $bdebit = 0;
        $bdue = 0;
        if($customer != "" && $company != ""){

            $prebalance = 0;
            $data = array();
            
            $this->db->select('
                sma_sales.payment_status as salestatus,
                sma_sales.date as date,
                sma_sales.customer_id as customer_id,
                sma_sales.supplier_id as supplier_id,
                sma_sales.reference_no as particular,
                sma_companies.name as supplier,
                sma_sales.total_discount,
                sma_sales.grand_total,
                sma_sales.paid,
                sma_sales.id,
                sma_sales.remarks
            ');
            $this->db->from('sma_sales');
            $this->db->join('sma_companies', 'sma_companies.id = sma_sales.supplier_id', 'left');
            $this->db->where('sma_sales.customer_id',$customer);
            if($company != "" && $company != 0){
                $this->db->where('sma_sales.own_company',$company);
            }
            if($supplier != "" && $supplier != 0){
                $this->db->where('sma_sales.supplier_id',$supplier);
            }
            $q = $this->db->get();
            $sales = $q->result();
            foreach($sales as $sale){
                $data = $sale;
                $due_detail = $this->site->getDueDate($sale->date,$sale->customer_id,$sale->supplier_id);
                $data->due_amount = 0;
                $aging = "0 Days";
                if($sale->salestatus != "paid" && $sale->salestatus != "excise"){
                    if ($due_detail['due_date'] <= date('Y-m-d')) {
                        $data->due_amount = $sale->grand_total-$sale->paid;
                        $date1 = new DateTime($sale->date);
                        $date2 = new DateTime(date("Y-m-d"));
                        $interval = $date1->diff($date2);
                        $colaging = $interval->days-$due_detail['durration'];
                        $aging = $colaging." Days";
                    }
                }
                $data->aging = $aging;
                $data->debit = 0;
                $data->pay_id = 0;
                $data->sale_id = $sale->id;
                $data->pay_status = $sale->salestatus;
                $data->credit = $data->grand_total;
                $data->status = 1;
                $data->tref = '-';
                $data->paid_by = '-';
                $data->note = '';
                // $data->remarks = $sale->remarks;
                $data->balance = 0;
                if(($start <= $sale->date || $start == "") && ($sale->date <= $end || $end == "")){
                    $sendvalue[] = $data;
                }
                else{
                    if($start > $sale->date){
                        $bcredit += $data->credit;
                        $bdebit += $data->debit;
                    }
                }
                $this->db->select('id as pay_id,date, reference_no as tref, amount,hold_amount,status,paid_by,note,remarks');
                $this->db->from('sma_payments');
                $this->db->where('sale_id',$sale->id);
                $q = $this->db->get();
                $payments = $q->result();
                foreach($payments as $payment){
                    $pdata = $payment;
                    $pdata->sale_id = $sale->id;
                    $pdata->pay_status = $sale->salestatus;
                    $pdata->aging = $aging;
                    $pdata->debit = $pdata->amount;
                    $pdata->due_amount = 0;
                    $pdata->supplier = $sale->supplier;
                    $pdata->particular = $sale->particular;
                    $pdata->credit = 0;
                    $pdata->balance = 0;
                    if(($start <= $payment->date || $start == "") && ($payment->date <= $end || $end == "")){
                        $sendvalue[] = $pdata;
                    }
                    else{
                        if($start > $payment->date){
                            $bcredit += $pdata->credit;
                            $bdebit += $pdata->debit;
                        }
                    }
                }
                // sale_id
            }
        }

        $object = new stdClass();
        $object->date = date('Y-m-d', strtotime('-1 day', strtotime($start)));
        $object->particular = 'Opening';
        $object->supplier = 'Opening';
        $object->total_discount = 0;
        $object->grand_total = 0;
        $object->id = '0';
        $object->sale_id = '0';
        $object->pay_id = '0';
        $object->due_amount = 0;
        $object->debit = $bdebit;
        $object->credit = $bcredit;
        $object->status = 0;
        $object->tref = 'Opening';
        $object->aging = 'Opening';
        $object->paid_by = 'Opening';
        $object->note = 'Opening';
        $object->remarks = 'Opening';
        $object->pay_status = 'paid';
        $object->balance = 0;
        $sendvalue[] = $object;
        // Sorting
        if($sorttype == "" || $sorttype == "date"){
            $ord = array();
            foreach ($sendvalue as $key => $value){
                $ord[] = strtotime($value->date);
            }
            array_multisort($ord, SORT_ASC, $sendvalue);
        }

        //Calculate Balance
        $prebalance = 0;
        $preduebalance = 0;
        $data = array();
        foreach($sendvalue as $row){
            if($row->paid_by == "withholdingtax" || $row->paid_by == "-"){
                $data = $row;
                $prebalance = ($data->credit+$prebalance)-$data->debit;
                $data->balance = $prebalance;
                $data->due = $data->due_amount+$preduebalance;
                // $data->aging = '0 Days';
                $preduebalance = $data->due;
                $finaeldata[] = $data;
            }
        }
        return $finaeldata;
    }
    public function customerledger($customer,$supplier,$company,$start,$end,$sorttype){
        $finaeldata = array();
        $sendvalue = array();
        $bcredit = 0;
        $bdebit = 0;
        $bdue = 0;
        if($customer != "" && $company != ""){

            $prebalance = 0;
            $data = array();
            
            $this->db->select('
                sma_sales.payment_status as salestatus,
                sma_sales.date as date,
                sma_sales.customer_id as customer_id,
                sma_sales.supplier_id as supplier_id,
                sma_sales.reference_no as particular,
                sma_companies.name as supplier,
                sma_sales.total_discount,
                sma_sales.grand_total,
                sma_sales.paid,
                sma_sales.id,
                sma_sales.remarks
            ');
            $this->db->from('sma_sales');
            $this->db->join('sma_companies', 'sma_companies.id = sma_sales.supplier_id', 'left');
            $this->db->where('sma_sales.customer_id',$customer);
            if($company != "" && $company != 0){
                $this->db->where('sma_sales.own_company',$company);
            }
            if($supplier != "" && $supplier != 0){
                $this->db->where('sma_sales.supplier_id',$supplier);
            }
            $q = $this->db->get();
            $sales = $q->result();
            foreach($sales as $sale){
                $data = $sale;
                $due_detail = $this->site->getDueDate($sale->date,$sale->customer_id,$sale->supplier_id);
                $data->due_amount = 0;
                $aging = "0 Days";
                if($sale->salestatus != "paid" && $sale->salestatus != "excise"){
                    if ($due_detail['due_date'] <= date('Y-m-d')) {
                        $data->due_amount = $sale->grand_total-$sale->paid;
                        $date1 = new DateTime($sale->date);
                        $date2 = new DateTime(date("Y-m-d"));
                        $interval = $date1->diff($date2);
                        $colaging = $interval->days-$due_detail['durration'];
                        $aging = $colaging." Days";
                    }
                }
                $data->aging = $aging;
                $data->debit = 0;
                $data->pay_id = 0;
                $data->sale_id = $sale->id;
                $data->pay_status = $sale->salestatus;
                $data->credit = $data->grand_total;
                $data->status = 1;
                $data->tref = '-';
                $data->paid_by = '-';
                $data->note = '';
                // $data->remarks = $sale->remarks;
                $data->balance = 0;
                if(($start <= $sale->date || $start == "") && ($sale->date <= $end || $end == "")){
                    $sendvalue[] = $data;
                }
                else{
                    if($start > $sale->date){
                        $bcredit += $data->credit;
                        $bdebit += $data->debit;
                    }
                }
                $this->db->select('id as pay_id,date, reference_no as tref, amount,hold_amount,status,paid_by,note,remarks');
                $this->db->from('sma_payments');
                $this->db->where('sale_id',$sale->id);
                $q = $this->db->get();
                $payments = $q->result();
                foreach($payments as $payment){
                    $pdata = $payment;
                    $pdata->sale_id = $sale->id;
                    $pdata->pay_status = $sale->salestatus;
                    $pdata->aging = $aging;
                    $pdata->debit = $pdata->amount;
                    $pdata->due_amount = 0;
                    $pdata->supplier = $sale->supplier;
                    $pdata->particular = $sale->particular;
                    $pdata->credit = 0;
                    $pdata->balance = 0;
                    if(($start <= $payment->date || $start == "") && ($payment->date <= $end || $end == "")){
                        $sendvalue[] = $pdata;
                    }
                    else{
                        if($start > $payment->date){
                            $bcredit += $pdata->credit;
                            $bdebit += $pdata->debit;
                        }
                    }
                }
                // sale_id
            }
        }

        $object = new stdClass();
        $object->date = date('Y-m-d', strtotime('-1 day', strtotime($start)));
        $object->particular = 'Opening';
        $object->supplier = 'Opening';
        $object->total_discount = 0;
        $object->grand_total = 0;
        $object->id = '0';
        $object->sale_id = '0';
        $object->pay_id = '0';
        $object->due_amount = 0;
        $object->debit = $bdebit;
        $object->credit = $bcredit;
        $object->status = 0;
        $object->tref = 'Opening';
        $object->aging = 'Opening';
        $object->paid_by = 'Opening';
        $object->note = 'Opening';
        $object->remarks = 'Opening';
        $object->pay_status = 'paid';
        $object->balance = 0;
        $sendvalue[] = $object;
        // Sorting
        if($sorttype == "" || $sorttype == "date"){
            $ord = array();
            foreach ($sendvalue as $key => $value){
                $ord[] = strtotime($value->date);
            }
            array_multisort($ord, SORT_ASC, $sendvalue);
        }

        //Calculate Balance
        $prebalance = 0;
        $preduebalance = 0;
        $data = array();
        foreach($sendvalue as $row){
            $data = $row;
            $prebalance = ($data->credit+$prebalance)-$data->debit;
            $data->balance = $prebalance;
            $data->due = $data->due_amount+$preduebalance;
            // $data->aging = '0 Days';
            $preduebalance = $data->due;
            $finaeldata[] = $data;
        }
        // echo '<pre>';
        // print_r($finaeldata);
        // exit();
        return $finaeldata;
    }
    public function supplierlegder($supplier,$company,$start,$end,$sorttype){
        $finaeldata = array();
        $sendvalue = array();
        $bcredit = 0;
        $bdebit = 0;
        $bdue = 0;
        if($supplier != "" && $supplier != ""){
            $prebalance = 0;
            $data = array();
            
            $this->db->select('
                sma_purchases.payment_status as purchasestatus,
                sma_purchases.date as date,
                sma_purchases.supplier_id as supplier_id,
                sma_purchases.reference_no as particular,
                sma_companies.name as supplier,
                sma_purchases.total_discount,
                sma_purchases.grand_total,
                sma_purchases.paid,
                sma_purchases.id,
                sma_purchases.remarks
            ');
            $this->db->from('sma_purchases');
            $this->db->join('sma_companies', 'sma_companies.id = sma_purchases.supplier_id', 'left');
            $this->db->where('sma_purchases.supplier_id',$supplier);
            if($company != ""){
                $this->db->where('sma_purchases.own_company',$company);
            }
            $q = $this->db->get();
            $purchases = $q->result();
            foreach($purchases as $purchase){
                $data = $purchase;
                $due_detail = $this->site->getSupplierDueDate($purchase->date,$purchase->supplier_id);
                $data->due_amount = 0;
                $aging = "0 Days";
                if($purchase->purchasestatus != "paid" && $purchase->purchasestatus != "excise"){
                    if ($due_detail['due_date'] <= date('Y-m-d')) {
                        $data->due_amount = $purchase->grand_total-$purchase->paid;
                        $date1 = new DateTime($purchase->date);
                        $date2 = new DateTime(date("Y-m-d"));
                        $interval = $date1->diff($date2);
                        $colaging = $interval->days-$due_detail['durration'];
                        $aging = $colaging." Days";
                    }
                }
                $data->aging = $aging;
                $data->debit = 0;
                $data->pay_id = 0;
                $data->purchase_id = $purchase->id;
                $data->pay_status = $purchase->purchasestatus;
                $data->credit = $data->grand_total;
                $data->status = 1;
                $data->tref = '-';
                $data->paid_by = '-';
                $data->note = '';
                $data->balance = 0;
                if(($start <= $purchase->date || $start == "") && ($purchase->date <= $end || $end == "")){
                    $sendvalue[] = $data;
                }
                else{
                    if($start > $purchase->date){
                        $bcredit += $data->credit;
                        $bdebit += $data->debit;
                    }
                }
                $this->db->select('id as pay_id,date, reference_no as tref, amount,hold_amount,status,paid_by,note,remarks');
                $this->db->from('sma_payments');
                $this->db->where('purchase_id',$purchase->id);
                $q = $this->db->get();
                $payments = $q->result();
                foreach($payments as $payment){
                    $pdata = $payment;
                    $pdata->purchase_id = $purchase->id;
                    $pdata->pay_status = $purchase->purchasestatus;
                    $pdata->aging = $aging;
                    $pdata->debit = $pdata->amount;
                    $pdata->due_amount = 0;
                    $pdata->supplier = $purchase->supplier;
                    $pdata->particular = $purchase->particular;
                    $pdata->credit = 0;
                    $pdata->balance = 0;
                    if(($start <= $payment->date || $start == "") && ($payment->date <= $end || $end == "")){
                        $sendvalue[] = $pdata;
                    }
                    else{
                        if($start > $payment->date){
                            $bcredit += $pdata->credit;
                            $bdebit += $pdata->debit;
                        }
                    }
                }
            }
        }

        $object = new stdClass();
        $object->date = date('Y-m-d', strtotime('-1 day', strtotime($start)));
        $object->particular = 'Opening';
        $object->supplier = 'Opening';
        $object->total_discount = 0;
        $object->grand_total = 0;
        $object->id = '0';
        $object->purchase_id = '0';
        $object->pay_id = '0';
        $object->due_amount = 0;
        $object->debit = $bdebit;
        $object->credit = $bcredit;
        $object->status = 0;
        $object->tref = 'Opening';
        $object->aging = 'Opening';
        $object->paid_by = 'Opening';
        $object->note = 'Opening';
        $object->remarks = 'Opening';
        $object->pay_status = 'paid';
        $object->balance = 0;
        $sendvalue[] = $object;
        // Sorting
        if($sorttype == "" || $sorttype == "date"){
            $ord = array();
            foreach ($sendvalue as $key => $value){
                $ord[] = strtotime($value->date);
            }
            array_multisort($ord, SORT_ASC, $sendvalue);
        }

        //Calculate Balance
        $prebalance = 0;
        $preduebalance = 0;
        $data = array();
        foreach($sendvalue as $row){
            $data = $row;
            $prebalance = ($data->credit+$prebalance)-$data->debit;
            $data->balance = $prebalance;
            $data->due = $data->due_amount+$preduebalance;
            // $data->aging = '0 Days';
            $preduebalance = $data->due;
            $finaeldata[] = $data;
        }
        return $finaeldata;

    }
    public function dc_report($req = null){
        $sendvalue = array();
        $this->db->select('
            sales.id,
            sales.date,
            sale_items.product_name,
            sale_items.quantity,
            sales.reference_no as ref_no,
            sale_items.subtotal as total,

        ');
        $this->db->from('sale_items');
        $this->db->join('sales','sales.id = sale_items.sale_id','left');
        // $this->db->where('date2 >=', $req['start']);
        // $this->db->where('date <=', $req['end']);
        $this->db->where('sales.date BETWEEN "'.$req["start"].' 00:00:00" AND "'.$req["end"].' 23:59:59"');
        $q = $this->db->get();
        return $q->result();
    }
    public function get_own_companies()
    {
        $this->db->select('id,companyname');
        $this->db->from('own_companies');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function get_companies($group_name){
        $this->db->select('id,name,company');
        $this->db->from('companies');
        $this->db->where('group_name',$group_name);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function get_visible_warehosues(){
        $this->db->select('id,name');
        $q = $this->db->from('warehouses')->where('visibility',1)->get();
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function get_categories(){
        $this->db->select('id,name');
        $this->db->from('categories');
        $this->db->where('parent_id', NULL)->or_where('parent_id', 0)->order_by('name');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function get_brands(){
        $this->db->select('id,name');
        $this->db->from('brands');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function get_warehosues(){
        $this->db->select('id,name');
        $q = $this->db->get('warehouses');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }
    public function batchwise($req = null){
        $sendvalue = array();
        $data1 = $this->get_purchase_batchwise($req);
        $data2 = $this->get_transfer_batchwise($req);
        $data3 = $this->get_adjbatch_batchwise($req);
        $data = array_merge($data1,$data2,$data3);
        return $data;
    }
    public function get_purchase_batchwise($req = null){
        $this->db->select("
            sma_purchases.warehouse_id as puwid,
            sma_purchases.id as puid,
            sma_brands.name as brand_name, 
            sma_warehouses.name as warehousename, 
            IF( 
                `sma_purchases`.`date` IS null , 
                '2000-12-12' , 
                `sma_purchases`.`date` 
            ) as 'purchase_date' , 
            `sma_purchases`.`id`, 
            `sma_purchase_items`.`id` as piid, 
            `sma_purchase_items`.`purchase_id`, 
            `sma_purchase_items`.`transfer_id`, 
            `sma_purchase_items`.`product_id`, 
            `sma_purchase_items`.`product_code`, 
            `sma_purchase_items`.`product_name`, 
            `sma_purchase_items`.`option_id`, 
            `sma_purchase_items`.`net_unit_cost`, 
            `sma_purchase_items`.`price`, 
            `sma_purchase_items`.`dropship`, 
            `sma_purchase_items`.`crossdock`, 
            `sma_purchase_items`.`mrp`, 
            `sma_purchase_items`.`quantity`, 
            `sma_purchases`.`warehouse_id`, 
            `sma_purchase_items`.`item_tax`, 
            `sma_purchase_items`.`tax_rate_id`, 
            `sma_purchase_items`.`tax`, 
            `sma_purchase_items`.`discount`, 
            `sma_purchase_items`.`item_discount`, 
            `sma_purchase_items`.`expiry`, 
            `sma_purchase_items`.`batch`, 
            `sma_purchase_items`.`subtotal`, 
            `sma_purchase_items`.`quantity_balance`, 
            `sma_purchase_items`.`date`, 
            `sma_purchase_items`.`status`, 
            `sma_purchase_items`.`unit_cost`, 
            `sma_purchase_items`.`real_unit_cost`, 
            `sma_purchase_items`.`quantity_received`, 
            `sma_purchase_items`.`supplier_part_no`, 
            `sma_purchase_items`.`purchase_item_id`, 
            `sma_purchase_items`.`product_unit_id`, 
            `sma_purchase_items`.`product_unit_code`, 
            `sma_purchase_items`.`unit_quantity`, 
            `sma_purchase_items`.`gst`, 
            `sma_purchase_items`.`cgst`, 
            `sma_purchase_items`.`sgst`, 
            `sma_purchase_items`.`igst`, 
            `sma_purchase_items`.`discount_one`, 
            `sma_purchase_items`.`discount_two`, 
            `sma_purchase_items`.`discount_three`, 
            `sma_purchase_items`.`further_tax`, 
            `sma_purchase_items`.`fed_tax`, 
            `sma_purchase_items`.`gst_tax`, 
            `sma_products`.`carton_size`, 
            `sma_products`.`tax_rate`, 
            `sma_products`.tax_method, 
            `sma_products`.`supplier1`, 
            `sma_companies`.`company`, 
            IF(
                sma_tax_rates.type = '1', 
                'GST', 
                IF(
                    sma_tax_rates.code = 'exp', 
                    'Exempted', 
                    '3rd Schdule'
                )
            ) AS 'Remarks', 
            sma_tax_rates.rate, 
            IF(
                sma_tax_rates.type = '1', 
                (
                    sma_purchase_items.net_unit_cost * sma_tax_rates.rate
                ) / 100, 
                IF( 
                    sma_tax_rates.code = 'exp', 
                    0, 
                    sma_tax_rates.rate 
                )
            ) AS 'tax_rate_value', 
            sma_products.company_code,
            sma_products.status as product_status, 
            sma_products.group_id as product_group_id,
            (
                SELECT name FROM sma_product_groups WHERE id = sma_products.group_id
            ) as product_group_name,
            'Normal Batch' as data_type,
            category.name as category,
            subcategory.name as subcategory

        ");
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
        $this->db->join('sma_tax_rates','sma_purchase_items.tax_rate_id = sma_tax_rates.id','left');
        $this->db->join('sma_companies','sma_companies.id = sma_products.supplier1','left');
        $this->db->join('sma_purchases','sma_purchases.id = sma_purchase_items.purchase_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_items.warehouse_id ','left');
        $this->db->join('sma_categories as category','category.id = sma_products.category_id','left');
        $this->db->join('sma_categories as subcategory','subcategory.id = sma_products.subcategory_id','left');
        // $this->db->where('sma_products.status','1');
        $this->db->where('sma_purchase_items.quantity_balance > 0');
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where('sma_purchases.warehouse_id',$req['warehouse']);
        }
        else{
            $this->db->where('sma_purchases.warehouse_id !=','');
        }
        if($req['company'] != "" && $req['company'] != "all"){
            $this->db->where('sma_purchases.own_company',$req['company']);
        }
        if($req['supplier'] != "" && $req['supplier'] != "all"){
            $this->db->where('sma_purchases.supplier_id',$req['supplier']);
        }
        if($req['category'] != "" && $req['category'] != "all"){
            $this->db->where('sma_products.category_id ',$req['category']);
        }
        if($req['brand'] != "" && $req['brand'] != "all"){
            $this->db->where('sma_products.brand',$req['brand']);
        }
        if($req['start'] != "" && $req['start'] != ""){
            $this->db->where('sma_purchases.date BETWEEN "'.$req["start"].' 00:00:00" AND "'.$req["end"].' 23:59:59"');
        }
        else{
            if($req['start'] != ""){
                $this->db->where('sma_purchases.date >=',$req['start']);
            }
            if($req['end'] != ""){
                $this->db->where('sma_purchases.date <=',$req['end']);
            }
        }
        $q = $this->db->get();
        return $q->result();
    }
    public function get_transfer_batchwise($req = null){
        $this->db->select('
            sma_brands.name as brand_name, 
            sma_warehouses.name as warehousename, 
            IF( 
                `sma_transfers`.`date` IS null , 
                "2000-12-12" , 
                `sma_transfers`.`date`
            ) as "purchase_date" , 
            `sma_purchase_items`.`id` as piid, 
            `sma_purchase_items`.`purchase_id`, 
            `sma_purchase_items`.`transfer_id`, 
            `sma_purchase_items`.`product_id`, 
            `sma_purchase_items`.`product_code`, 
            `sma_purchase_items`.`product_name`, 
            `sma_purchase_items`.`option_id`, 
            `sma_purchase_items`.`net_unit_cost`, 
            `sma_purchase_items`.`price`, 
            `sma_purchase_items`.`dropship`, 
            `sma_purchase_items`.`crossdock`, 
            `sma_purchase_items`.`mrp`, 
            `sma_purchase_items`.`quantity`, 
            `sma_purchase_items`.`warehouse_id`, 
            `sma_purchase_items`.`item_tax`, 
            `sma_purchase_items`.`tax_rate_id`, 
            `sma_purchase_items`.`tax`, 
            `sma_purchase_items`.`discount`, 
            `sma_purchase_items`.`item_discount`, 
            `sma_purchase_items`.`expiry`, 
            `sma_purchase_items`.`batch`, 
            `sma_purchase_items`.`subtotal`, 
            `sma_purchase_items`.`quantity_balance`, 
            `sma_purchase_items`.`date`, 
            `sma_purchase_items`.`status`, 
            `sma_purchase_items`.`unit_cost`, 
            `sma_purchase_items`.`real_unit_cost`, 
            `sma_purchase_items`.`quantity_received`, 
            `sma_purchase_items`.`supplier_part_no`, 
            `sma_purchase_items`.`purchase_item_id`, 
            `sma_purchase_items`.`product_unit_id`, 
            `sma_purchase_items`.`product_unit_code`, 
            `sma_purchase_items`.`unit_quantity`, 
            `sma_purchase_items`.`gst`, 
            `sma_purchase_items`.`cgst`, 
            `sma_purchase_items`.`sgst`, 
            `sma_purchase_items`.`igst`, 
            `sma_purchase_items`.`discount_one`, 
            `sma_purchase_items`.`discount_two`,
            `sma_purchase_items`.`discount_three`, 
            `sma_purchase_items`.`further_tax`, 
            `sma_purchase_items`.`fed_tax`, 
            `sma_purchase_items`.`gst_tax`, 
            `sma_products`.`carton_size`, 
            `sma_products`.`tax_rate`, 
            `sma_products`.tax_method, 
            `sma_products`.`supplier1`, 
            `sma_companies`.`company`, 
            IF( 
                sma_tax_rates.type = "1", 
                "GST", 
                IF( 
                    sma_tax_rates.code = "exp", 
                    "Exempted", 
                    "3rd Schdule" 
                ) 
            ) AS "Remarks", 
            sma_tax_rates.rate, 
            IF( 
                sma_tax_rates.type = "1", 
                (sma_purchase_items.net_unit_cost * sma_tax_rates.rate) / 100, 
                IF( sma_tax_rates.code = "exp", 0, sma_tax_rates.rate )
            ) AS "tax_rate_value", 
            sma_products.company_code,
            sma_products.status as product_status, 
            sma_products.group_id as product_group_id,
            (
                SELECT name FROM sma_product_groups WHERE id = sma_products.group_id
            ) as product_group_name,
            "Transfer Batch" as data_type,
            category.name as category,
            subcategory.name as subcategory
        ');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
        $this->db->join('sma_products as child_product','child_product.parent_product_id = sma_purchase_items.product_id','left');
        $this->db->join('sma_tax_rates','sma_purchase_items.tax_rate_id = sma_tax_rates.id','left');
        $this->db->join('sma_companies','sma_companies.id = sma_products.supplier1','left');
        $this->db->join('sma_transfers','sma_transfers.id = sma_purchase_items.transfer_id','left');
        $this->db->join('sma_categories as category','category.id = sma_products.category_id','left');
        $this->db->join('sma_categories as subcategory','subcategory.id = sma_products.subcategory_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_items.warehouse_id ','left');
        $this->db->where('sma_purchase_items.transfer_id !=','');
        $this->db->where('sma_purchase_items.quantity_balance > 0');
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where('sma_transfers.to_warehouse_id',$req['warehouse']);
        }
        if($req['category'] != "" && $req['category'] != "all"){
            $this->db->where('sma_products.category_id ',$req['category']);
        }
        if($req['brand'] != "" && $req['brand'] != "all"){
            $this->db->where('sma_products.brand',$req['brand']);
        }
        if($req['start'] != "" && $req['start'] != ""){
            $this->db->where('sma_transfers.date BETWEEN "'.$req["start"].' 00:00:00" AND "'.$req["end"].' 23:59:59"');
        }
        else{
            if($req['start'] != ""){
                $this->db->where('sma_transfers.date >=',$req['start']);
            }
            if($req['end'] != ""){
                $this->db->where('sma_transfers.date <=',$req['end']);
            }
        }
        $q = $this->db->get();
        return $q->result();
    }
    public function get_adjbatch_batchwise($req = null){
        $this->db->select('
            sma_brands.name as brand_name, 
            sma_warehouses.name as warehousename, 
            IF( 
                `sma_purchase_item_adjs`.`date` IS null , 
                "2000-12-12" , 
                `sma_purchase_item_adjs`.`date`
            ) as "purchase_date" , 
            `sma_purchase_items`.`id` as piid, 
            `sma_purchase_items`.`purchase_id`, 
            `sma_purchase_items`.`transfer_id`, 
            `sma_purchase_items`.`product_id`, 
            `sma_purchase_items`.`product_code`, 
            `sma_purchase_items`.`product_name`, 
            `sma_purchase_items`.`option_id`, 
            `sma_purchase_items`.`net_unit_cost`, 
            `sma_purchase_items`.`price`, 
            `sma_purchase_items`.`dropship`, 
            `sma_purchase_items`.`crossdock`, 
            `sma_purchase_items`.`mrp`, 
            `sma_purchase_items`.`quantity`, 
            `sma_purchase_items`.`warehouse_id`, 
            `sma_purchase_items`.`item_tax`, 
            `sma_purchase_items`.`tax_rate_id`, 
            `sma_purchase_items`.`tax`, 
            `sma_purchase_items`.`discount`, 
            `sma_purchase_items`.`item_discount`, 
            `sma_purchase_items`.`expiry`, 
            `sma_purchase_items`.`batch`, 
            `sma_purchase_items`.`subtotal`, 
            `sma_purchase_items`.`quantity_balance`, 
            `sma_purchase_items`.`date`, 
            `sma_purchase_items`.`status`, 
            `sma_purchase_items`.`unit_cost`, 
            `sma_purchase_items`.`real_unit_cost`, 
            `sma_purchase_items`.`quantity_received`, 
            `sma_purchase_items`.`supplier_part_no`, 
            `sma_purchase_items`.`purchase_item_id`, 
            `sma_purchase_items`.`product_unit_id`, 
            `sma_purchase_items`.`product_unit_code`, 
            `sma_purchase_items`.`unit_quantity`, 
            `sma_purchase_items`.`gst`, 
            `sma_purchase_items`.`cgst`, 
            `sma_purchase_items`.`sgst`, 
            `sma_purchase_items`.`igst`, 
            `sma_purchase_items`.`discount_one`, 
            `sma_purchase_items`.`discount_two`,
            `sma_purchase_items`.`discount_three`, 
            `sma_purchase_items`.`further_tax`, 
            `sma_purchase_items`.`fed_tax`, 
            `sma_purchase_items`.`gst_tax`, 
            `sma_products`.`carton_size`, 
            `sma_products`.`tax_rate`, 
            `sma_products`.tax_method, 
            `sma_products`.`supplier1`, 
            `sma_companies`.`company`, 
            IF( 
                sma_tax_rates.type = "1", 
                "GST", 
                IF( 
                    sma_tax_rates.code = "exp", 
                    "Exempted", 
                    "3rd Schdule" 
                ) 
            ) AS "Remarks", 
            sma_tax_rates.rate, 
            IF( 
                sma_tax_rates.type = "1", 
                (sma_purchase_items.net_unit_cost * sma_tax_rates.rate) / 100, 
                IF( sma_tax_rates.code = "exp", 0, sma_tax_rates.rate )
            ) AS "tax_rate_value", 
            sma_products.company_code,
            sma_products.status as product_status, 
            sma_products.group_id as product_group_id,
            (
                SELECT name FROM sma_product_groups WHERE id = sma_products.group_id
            ) as product_group_name,
            "Adjustment Batch" as data_type,
            category.name as category,
            subcategory.name as subcategory
        ');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
        $this->db->join('sma_products as child_product','child_product.parent_product_id = sma_purchase_items.product_id','left');
        $this->db->join('sma_tax_rates','sma_purchase_items.tax_rate_id = sma_tax_rates.id','left');
        $this->db->join('sma_companies','sma_companies.id = sma_products.supplier1','left');
        $this->db->join('sma_purchase_item_adjs','sma_purchase_item_adjs.id = sma_purchase_items.batch_adj_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_items.warehouse_id ','left');
        $this->db->join('sma_categories as category','category.id = sma_products.category_id','left');
        $this->db->join('sma_categories as subcategory','subcategory.id = sma_products.subcategory_id','left');
        $this->db->where('sma_purchase_items.batch_adj_id !=','');
        $this->db->where('sma_purchase_items.quantity_balance > 0');
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where('sma_purchase_items.warehouse_id',$req['warehouse']);
        }
        if($req['category'] != "" && $req['category'] != "all"){
            $this->db->where('sma_products.category_id ',$req['category']);
        }
        if($req['brand'] != "" && $req['brand'] != "all"){
            $this->db->where('sma_products.brand',$req['brand']);
        }
        if($req['start'] != "" && $req['start'] != ""){
            $this->db->where('sma_purchase_item_adjs.date BETWEEN "'.$req["start"].' 00:00:00" AND "'.$req["end"].' 23:59:59"');
        }
        else{
            if($req['start'] != ""){
                $this->db->where('sma_purchase_item_adjs.date >=',$req['start']);
            }
            if($req['end'] != ""){
                $this->db->where('sma_purchase_item_adjs.date <=',$req['end']);
            }
        }
        $q = $this->db->get();
        return $q->result();
    }
    public function salessummary($req = null){
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $sendvalue = array();
        $this->db->select('
            sma_sales.date,
            sma_sales.reference_no,
            sma_sales.customer,
            sma_sales.customer,
            sma_sales.total,
            sma_sales.product_discount,
            sma_sales.product_tax,
            sma_own_companies.companyname,
            sma_warehouses.name as warehosue_name,
            supplier.name as supplier_name,
        ');
        $this->db->from('sma_sales');
        $this->db->join('sma_own_companies','sma_own_companies.id = sma_sales.own_company','left');
        $this->db->join('sma_companies as supplier','supplier.id = sma_sales.supplier_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_sales.warehouse_id','left');
        $this->db->where("sma_sales.date >= '".$start." 00:00:00' AND sma_sales.date <= '".$end." 23:59:59'");
        if($req['own_company'] != "" && $req['own_company'] != "all"){
            $this->db->where("sma_sales.own_company = ".$req['own_company']);
        }
        if($req['supplier'] != "" && $req['supplier'] != "all"){
            $this->db->where("sma_sales.supplier_id = ".$req['supplier']);
        }
        if($req['customer'] != "" && $req['customer'] != "all"){
            $this->db->where("sma_sales.customer_id = ".$req['customer']);
        }
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where("sma_sales.warehouse_id = ".$req['warehouse']);
        }
        $query = $this->db->get();
        $sales = $query->result();
        foreach($sales as $sale){
            $data = array();
            array_push($data,$sale->date);
            array_push($data,$sale->warehosue_name);
            array_push($data,$sale->companyname);
            array_push($data,$sale->reference_no);
            array_push($data,$sale->customer);
            array_push($data,$sale->supplier_name);
            array_push($data,decimalallow($sale->total-$sale->product_tax+$sale->product_discount,2));
            array_push($data,decimalallow($sale->product_tax,2));
            array_push($data,decimalallow($sale->product_discount,2));
            array_push($data,decimalallow($sale->total,2));
            $sendvalue[] = $data;
        }

        return $sendvalue;
    }
    public function salesreport($req = null){
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $sendvalue = array();
        $this->db->select('
            sma_own_companies.companyname as own_company,
            customer.cnic as customer_cnic,
            customer.cf1 as customer_ntn,
            customer.gst_no as gst_no,
            sma_sales.reference_no as invoice,
            sma_sales.date as sale_date,
            sma_sales.po_number as po_number,
            customer.name as customer_name,
            etalier.name as etalier_name,
            sma_sale_items.product_id,
            sma_sale_items.product_name,
            sma_products.hsn_code,
            sma_products.code as barcode,
            sma_sale_items.quantity,
            sma_sale_items.product_unit_code,
            sma_sale_items.net_unit_price,
            sma_sale_items.consignment,
            IF(
                customer.sales_type = "consignment",
                sma_sale_items.unit_price,
                IF(
                    customer.sales_type = "crossdock",
                    sma_sale_items.crossdock,
                    IF(
                        customer.sales_type = "dropship",
                        sma_sale_items.dropship,
                        sma_sale_items.unit_price
                    )
                )
            ) AS sale_price,
            (
                IF(
                    customer.sales_type = "consignment",
                    sma_sale_items.unit_price,
                    IF(
                        customer.sales_type = "crossdock",
                        sma_sale_items.crossdock,
                        IF(
                            customer.sales_type = "dropship",
                            sma_sale_items.dropship,
                            sma_sale_items.unit_price
                        )
                    )
                )*sma_sale_items.quantity
            ) as "value_excl_tax",
            sma_sale_items.tax,
            sma_sale_items.item_tax,
            sma_sale_items.adv_tax,
            sma_sale_items.further_tax,
            sma_sale_items.fed_tax,
            (
                IF(
                    customer.sales_type = "consignment",
                    sma_sale_items.unit_price,
                    IF(
                        customer.sales_type = "crossdock",
                        sma_sale_items.crossdock,
                        IF(
                            customer.sales_type = "dropship",
                            sma_sale_items.dropship,
                            sma_sale_items.unit_price
                        )
                    )
                )*sma_sale_items.quantity + (sma_sale_items.tax + sma_sale_items.further_tax + sma_sale_items.fed_tax)
            )  AS total_tax,
            sma_sale_items.discount_one,
            sma_sale_items.discount_two,
            sma_sale_items.discount_three,
            sma_sale_items.discount,
            sma_sale_items.subtotal,
            IF(
                sma_tax_rates.type = "1",
                "GST",
                IF(
                    sma_tax_rates.code = "exp",
                    "Exempted","3rd Schdule"
                )
            ) AS "remarks",
            IF(
                sma_tax_rates.type = "1",
                0,
                IF(
                    sma_tax_rates.code = "exp",
                    0,
                    sma_sale_items.mrp/1.17
                )
            ) AS "mrp_excl_tax",
            IF(
                sma_tax_rates.type = "1",
                0,
                IF(
                    sma_tax_rates.code = "exp",
                    0,
                    (sma_sale_items.mrp/1.17)*sma_sale_items.quantity
                )
            ) AS "value_third_sch",
            sma_sale_items.mrp,
            sma_sale_items.expiry,
            sma_sale_items.batch,
            sma_brands.name AS "brand", 
            sma_sale_items.warehouse_id AS "warehouse_id",
            sma_warehouses.name AS "warehouse_name",
            sma_products.carton_size,
            sma_products.company_code,
            supplier.name as supplier_name,
            IFNULL(sma_product_groups.id,"Unknown Group") as group_id,
            IFNULL(sma_product_groups.name,"Unknown Group") as group_name
        ');
        $this->db->from('sma_sales');
        $this->db->join('sma_own_companies','sma_own_companies.id = sma_sales.own_company','left');
        $this->db->join('sma_companies as customer','customer.id = sma_sales.customer_id','left');
        $this->db->join('sma_companies as etalier','etalier.id = sma_sales.etalier_id','left');
        $this->db->join('sma_sale_items','sma_sale_items.sale_id = sma_sales.id','left');
        $this->db->join('sma_products','sma_products.id = sma_sale_items.product_id','left');
        $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_sale_items.tax_rate_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_companies as supplier','supplier.id = sma_sales.supplier_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_sales.warehouse_id','left');
        $this->db->join('sma_product_groups','sma_product_groups.id = sma_products.group_id','left');
        $this->db->where("sma_sales.date >= '".$start." 00:00:00' AND sma_sales.date <= '".$end." 23:59:59'");

        if($req['own_company'] != "" && $req['own_company'] != "all"){
            $this->db->where_in("sma_sales.own_company",$req['own_company']);
        }
        if($req['supplier'] != "" && $req['supplier'] != "all"){
            $this->db->where_in("sma_sales.supplier_id",$req['supplier']);
        }
        if($req['customer'] != "" && $req['customer'] != "all"){
            $this->db->where_in("sma_sales.customer_id",$req['customer']);
        }
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where_in("sma_sales.warehouse_id",$req['warehouse']);
        }
        $query = $this->db->get();
        $sales = $query->result();
        foreach($sales as $sale){
            $discount_one   = ($sale->value_excl_tax * $sale->discount_one) / 100;
            $discount_two   = ($sale->value_excl_tax * $sale->discount_two) / 100;
            $discount_three = ($sale->value_excl_tax * $sale->discount_three) / 100;

            $data = array();
            array_push($data,$sale->own_company);
            array_push($data,$sale->customer_cnic);
            array_push($data,$sale->customer_ntn);
            array_push($data,$sale->invoice);
            array_push($data,date_format(date_create($sale->sale_date),"d/m/Y"));
            array_push($data,$sale->po_number);
            array_push($data,$sale->customer_name);
            array_push($data,$sale->etalier_name);
            if($sale->gst_no == ""){
                array_push($data,'Not Registered');
            }
            else{
                array_push($data,'Registered');
            }
            array_push($data,$sale->product_id);
            array_push($data,$sale->company_code);
            array_push($data,$sale->barcode);
            array_push($data,$sale->brand);
            array_push($data,$sale->hsn_code);
            array_push($data,$sale->product_name);
            array_push($data,decimalallow($sale->carton_size,2));
            array_push($data,decimalallow($sale->mrp,2));
            array_push($data,decimalallow($sale->quantity,0));
            array_push($data,$sale->product_unit_code);
            $qtyincarton = $sale->quantity/$sale->carton_size;
            array_push($data,decimalallow($qtyincarton,2));
            array_push($data,decimalallow($sale->consignment,2));
            array_push($data,decimalallow($sale->sale_price,2));
            array_push($data,decimalallow($sale->value_excl_tax,2));
            array_push($data,decimalallow($sale->tax,2));
            array_push($data,decimalallow($sale->item_tax,2));
            array_push($data,decimalallow($sale->adv_tax,2));
            array_push($data,decimalallow($sale->further_tax,2));
            array_push($data,decimalallow($sale->fed_tax,2));
            array_push($data,decimalallow($sale->value_excl_tax+$sale->item_tax+$sale->further_tax+$sale->fed_tax+$sale->adv_tax,2));
            array_push($data,decimalallow($sale->item_tax+$sale->further_tax+$sale->fed_tax+$sale->adv_tax,2));
            array_push($data,decimalallow($discount_one,2));
            array_push($data,decimalallow($sale->discount_one,2));
            array_push($data,decimalallow($discount_two,2));
            array_push($data,decimalallow($sale->discount_two,2));
            array_push($data,decimalallow($discount_three,2));
            array_push($data,decimalallow($sale->discount_three,2));
            array_push($data,decimalallow($sale->discount,2));
            array_push($data,decimalallow($sale->subtotal,2));
            array_push($data,$sale->expiry);
            array_push($data,$sale->batch);
            array_push($data,$sale->warehouse_name);
            array_push($data,$sale->supplier_name);
            array_push($data,$sale->remarks);
            array_push($data,decimalallow($sale->mrp_excl_tax,2));
            array_push($data,decimalallow($sale->value_third_sch,2));
            array_push($data,$sale->group_id);
            array_push($data,$sale->group_name);
            $sendvalue[] = $data;
        }
        return $sendvalue;
    }
    public function so_items_wise($req = null){
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $sendvalue = array();
        $this->db->select('
            sma_sales_orders_tb.date,
            sma_sales_orders_tb.ref_no,
            sma_sales_orders_tb.po_number,
            sma_sales_order_items.product_id,
            sma_products.name,
            supplier.name AS supplier_name,
            customer.name AS customer_name,
            sma_warehouses.name AS warehouse,
            sma_sales_order_items.quantity,
            (
                SELECT 
                    SUM(sma_sales_order_complete_items.quantity) 
                FROM 
                    sma_sales_order_complete_items 
                WHERE 
                    sma_sales_order_complete_items.soi_id = sma_sales_order_items.id
            ) AS complete_qty,
            sma_products.price AS consinment_price_without_tax,
            sma_tax_rates.type AS tax_type,
            sma_tax_rates.rate AS tax_rate,
            CONCAT (sma_users.first_name, " ", sma_users.last_name) AS create_by,
            sma_sales_orders_tb.accounts_team_status,
            sma_sales_orders_tb.operation_team_stauts,
            sma_sales_orders_tb.status,
            sma_products.carton_size as carton_size,
            sma_products.group_id as gid,
            sma_product_groups.name as gname
        ');
        $this->db->from('sma_sales_order_items');
        $this->db->join('sma_products','sma_products.id = sma_sales_order_items.product_id','left');
        $this->db->join('sma_product_groups','sma_product_groups.id = sma_products.group_id','left');
        $this->db->join('sma_sales_orders_tb','sma_sales_orders_tb.id = sma_sales_order_items.so_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_sales_orders_tb.warehouse_id','left');
        $this->db->join('sma_companies AS supplier','supplier.id = sma_sales_orders_tb.supplier_id','left');
        $this->db->join('sma_companies AS customer','customer.id = sma_sales_orders_tb.customer_id','left');
        $this->db->join('sma_users','sma_users.id = sma_sales_orders_tb.created_by','left');
        $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_products.tax_rate','left');
        $this->db->where("sma_sales_orders_tb.date >= '".$start." 00:00:00' AND sma_sales_orders_tb.date <= '".$end." 23:59:59'");
        if($req['supplier'] != "" && $req['supplier'] != "all"){
            $this->db->where("sma_sales_orders_tb.supplier_id = ".$req['supplier']);
        }
        if($req['customer'] != "" && $req['customer'] != "all"){
            $this->db->where("sma_sales_orders_tb.customer_id = ".$req['customer']);
        }
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where("sma_sales_orders_tb.warehouse_id = ".$req['warehouse']);
        }
        $query = $this->db->get();
        $sales = $query->result();
        foreach($sales as $sale){
            $data = array();
            array_push($data,$sale->date);
            array_push($data,$sale->ref_no);
            array_push($data,$sale->po_number);
            array_push($data,$sale->product_id);
            array_push($data,$sale->name);
            array_push($data,$sale->supplier_name);
            array_push($data,$sale->customer_name);
            array_push($data,$sale->warehouse);
            array_push($data,$sale->quantity);
            if($sale->complete_qty == ""){
                array_push($data,0);
            }
            else{
                array_push($data,$sale->complete_qty);
            }
            array_push($data,$sale->carton_size);
            array_push($data,decimalallow($sale->quantity/$sale->carton_size,2));
            if($sale->complete_qty == ""){
                array_push($data,0);
            }
            else{
                array_push($data,decimalallow($sale->complete_qty/$sale->carton_size,2));
            }
            $consinment_price_with_tax = 0;
            if($sale->tax_type == 2){
                $consinment_price_with_tax = $sale->consinment_price_without_tax+$sale->tax_rate;
            }
            else{
                $consinment_price_with_tax = $sale->consinment_price_without_tax+($sale->consinment_price_without_tax/100*$sale->tax_rate);
            }
            array_push($data,$consinment_price_with_tax);
            array_push($data,$sale->create_by);
            array_push($data,$sale->accounts_team_status);
            array_push($data,$sale->operation_team_stauts);
            array_push($data,$sale->status);
            array_push($data,$sale->gid);
            array_push($data,$sale->gname);
            $sendvalue[] = $data;
        }
        return $sendvalue;
    }
    public function po_items_wise($req = null){
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $sendvalue = array();
        $this->db->select('
            sma_purchase_order_tb.created_at as date,
            sma_purchase_order_tb.reference_no as ref_no,
            sma_purchase_order_items_tb.product_id as product_id,
            sma_products.name as name,
            supplier.name as supplier_name,
            sma_warehouses.name as warehouse,
            sma_purchase_order_items_tb.qty as quantity,
            CONCAT (sma_users.first_name, " ", sma_users.last_name) AS create_by,
            sma_purchase_order_tb.status as status,
            sma_products.carton_size as carton_size,
            sma_products.group_id as groupid,
            sma_product_groups.name as groupname,
            IFNULL((
                SELECT SUM(sma_po_received_item_tb.received_qty) FROM sma_po_received_item_tb WHERE sma_po_received_item_tb.po_item_id = sma_purchase_order_items_tb.id
            ),0) as received_qty
        ');
        $this->db->from('sma_purchase_order_items_tb');
        $this->db->join('sma_purchase_order_tb','sma_purchase_order_tb.id = sma_purchase_order_items_tb.purchase_id','left');
        $this->db->join('sma_products','sma_products.id = sma_purchase_order_items_tb.product_id','left');
        $this->db->join('sma_product_groups','sma_product_groups.id = sma_products.group_id','left');
        $this->db->join('sma_companies AS supplier','supplier.id = sma_purchase_order_tb.supplier_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_order_tb.warehouse_id','left');
        $this->db->join('sma_users','sma_users.id = sma_purchase_order_tb.created_by','left');


        $this->db->where("sma_purchase_order_tb.created_at >= '".$start." 00:00:00' AND sma_purchase_order_tb.created_at <= '".$end." 23:59:59'");
        if($req['supplier'] != "" && $req['supplier'] != "all"){
            $this->db->where("sma_purchase_order_tb.supplier_id = ".$req['supplier']);
        }
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where("sma_purchase_order_tb.warehouse_id = ".$req['warehouse']);
        }
        $query = $this->db->get();
        $sales = $query->result();
        foreach($sales as $sale){
            $data = array();
            array_push($data,$sale->date);
            array_push($data,$sale->ref_no);
            array_push($data,$sale->product_id);
            array_push($data,$sale->name);
            array_push($data,$sale->supplier_name);
            array_push($data,$sale->warehouse);
            array_push($data,$sale->quantity);
            array_push($data,$sale->received_qty);
            array_push($data,$sale->quantity-$sale->received_qty);
            array_push($data,$sale->carton_size);
            array_push($data,$sale->quantity/$sale->carton_size);
            array_push($data,$sale->received_qty/$sale->carton_size);
            array_push($data,($sale->quantity-$sale->received_qty)/$sale->carton_size);
            array_push($data,$sale->create_by);
            array_push($data,$sale->status);
            array_push($data,$sale->groupid);
            array_push($data,$sale->groupname);
            $sendvalue[] = $data;
        }
        return $sendvalue;
    }
    public function ledger_summery_recivable($warehouse,$customers,$companies,$start,$end){
        $sendvalue['warehouses'] = array();
        $sendvalue['thead'] = array();
        $sendvalue['tbody'] = array();
        $sendvalue['codestatus'] = "no";
        if($warehouse != "" && $customers != ""){
            $this->db->select('id,company');
            $this->db->from('sma_companies');
            $this->db->where_in('id', $customers);
            $q = $this->db->get();
            $gcustomers = $q->result();
            $this->db->select('id,companyname');
            $this->db->from('sma_own_companies');
            $this->db->where_in('id', $companies);
            $q = $this->db->get();
            $gcompanies = $q->result();
            foreach($gcompanies as $gcompany){
                $temp = array();
                $temp['id'] = $gcompany->id;
                $temp['name'] = $gcompany->companyname;
                $sendvalue['thead'][] = $temp;
            }
            foreach($gcustomers as $gcustomer){
                $temp = array();
                $temp['customer_id'] = $gcustomer->id;
                $temp['customer_name'] = $gcustomer->company;
                foreach($gcompanies as $gcompany){
                    $ctemp = array();
                    $ctemp['id'] = $gcompany->id;
                    $ctemp['name'] = $gcompany->companyname;
                    // echo $gcustomer->company; echo $gcompany->companyname;
                    $ctemp['value'] = $this->calAmount($warehouse,$gcustomer->id,$gcompany->id,$start,$end);
                    $temp['companies'][] = $ctemp;
                }
                $sendvalue['tbody'][] = $temp;
            }
        }
        return $sendvalue;
    }
    public function ledger_summery_due($warehouse,$customers,$companies,$start,$end){
        $sendvalue['thead'] = array();
        $sendvalue['tbody'] = array();
        $sendvalue['codestatus'] = "no";
        $this->db->select('id,company');
        $this->db->from('sma_companies');
        $this->db->where_in('id', $customers);
        $q = $this->db->get();
        $gcustomers = $q->result();
        $this->db->select('id,companyname');
        $this->db->from('sma_own_companies');
        $this->db->where_in('id', $companies);
        $q = $this->db->get();
        $gcompanies = $q->result();
        foreach($gcompanies as $gcompany){
            $temp = array();
            $temp['id'] = $gcompany->id;
            $temp['name'] = $gcompany->companyname;
            $sendvalue['thead'][] = $temp;
        }
        foreach($gcustomers as $gcustomer){
            $temp = array();
            $temp['customer_id'] = $gcustomer->id;
            $temp['customer_name'] = $gcustomer->company;
            foreach($gcompanies as $gcompany){
                $ctemp = array();
                $ctemp['id'] = $gcompany->id;
                $ctemp['name'] = $gcompany->companyname;
                $ctemp['value'] = 0;
                $temp['companies'][] = $ctemp;
            }
            $sendvalue['tbody'][] = $temp;
        }
        return $sendvalue;
    }
    public function calAmount($warehouses,$customers,$companies,$start,$end){
        $this->db->select('IFNULL(sum(grand_total),0) as gtotal, IFNULL(sum(paid),0) as ptotal');
        // $this->db->select('grand_total, paid, payment_status, (grand_total - paid) as balance,');
        $this->db->from('sma_sales');
        $this->db->where_in('customer_id',$customers);
        $this->db->where_in('warehouse_id',$warehouses);
        $this->db->where_in('own_company',$companies);
        $this->db->where('payment_status != "paid"');
        $q = $this->db->get();
        $sales = $q->num_rows();
        $data = $q->result()[0];
        return $data->gtotal-$data->ptotal;
        
        // $sales = $q->result();
        // echo '<pre>';        
        // print_r($sales);
        // exit();
        // return $sales;
    }
    public function products_ledger($pid,$wid,$start=null,$end=null){
        if($start != null){
            $start = date_format(date_create($start),"Y-m-d");
        }
        if($end != null){
            $end = date_format(date_create($end),"Y-m-d");
        }
        // echo $start;
        // echo '<br>';
        // echo $end;
        // exit();
        $finaeldata[] = array(
            'type' => "Opening", 
            'ref' => "Opening", 
            'po' => "Opening", 
            'date' => "0000-00-00", 
            'product_id' => $pid, 
            'batch' => "Opening", 
            'customer_supplier' => "Opening", 
            'qty' => 0, 
            'balance' => 0
        );
        $lists = array();
        $rows = array();
        // Purchases List
        $this->db->select('DATE_FORMAT(sma_purchases.date, "%Y-%m-%d"),sma_purchases.reference_no,sma_purchases.supplier,sma_purchase_items.*');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_purchases','sma_purchases.id = sma_purchase_items.purchase_id','right');
        $this->db->where('sma_purchase_items.product_id',$pid);
        if($wid != ""){
            $this->db->where('sma_purchase_items.warehouse_id',$wid);
        }
        $q = $this->db->get();
        $purchases = $q->result();
        foreach($purchases as $purchase){
            $temp = array();
            $temp['type'] = "Purchase";
            $temp['ref'] = $purchase->reference_no;
            $temp['po'] = "-";
            $temp['date'] = date_format(date_create($purchase->date),"Y-m-d");
            $temp['product_id'] = $purchase->product_id;
            $temp['batch'] = $purchase->batch;
            $temp['customer_supplier'] = $purchase->supplier;
            $temp['qty'] = $purchase->quantity;
            $temp['balance'] = 0;
            $rows[] = $temp;
        }
        // Transfer In List
        $this->db->select('DATE_FORMAT(sma_transfers.date, "%Y-%m-%d"),sma_transfers.transfer_no as reference_no,sma_transfers.from_warehouse_name as supplier,sma_purchase_items.*');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_transfers','sma_transfers.id = sma_purchase_items.transfer_id','right');
        $this->db->where('sma_purchase_items.product_id',$pid);
        if($wid != ""){
            $this->db->where('sma_transfers.to_warehouse_id',$wid);
        }
        $q = $this->db->get();
        $transfers = $q->result();
        foreach($transfers as $transfer){
            $temp = array();
            $temp['type'] = "Transfer In";
            $temp['ref'] = $transfer->reference_no;
            $temp['po'] = "-";
            $temp['date'] = date_format(date_create($transfer->date),"Y-m-d");
            $temp['product_id'] = $transfer->product_id;
            $temp['batch'] = $transfer->batch;
            $temp['customer_supplier'] = $transfer->supplier;
            $temp['qty'] = $transfer->quantity;
            $temp['balance'] = 0;
            $rows[] = $temp;
        }
        // Transfer Out List
        $this->db->select('DATE_FORMAT(sma_transfers.date, "%Y-%m-%d"),sma_transfers.transfer_no as reference_no,sma_transfers.to_warehouse_name as supplier,sma_purchase_items.*');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_transfers','sma_transfers.id = sma_purchase_items.transfer_id','right');
        $this->db->where('sma_purchase_items.product_id',$pid);
        if($wid != ""){
            $this->db->where('sma_transfers.from_warehouse_id',$wid);
        }
        $q = $this->db->get();
        $transfers = $q->result();
        foreach($transfers as $transfer){
            $temp = array();
            $temp['type'] = "Transfer Out";
            $temp['ref'] = $transfer->reference_no;
            $temp['po'] = "-";
            $temp['date'] = date_format(date_create($transfer->date),"Y-m-d");
            $temp['product_id'] = $transfer->product_id;
            $temp['batch'] = $transfer->batch;
            $temp['customer_supplier'] = $transfer->supplier;
            $temp['qty'] = $transfer->quantity;
            $temp['balance'] = 0;
            $rows[] = $temp;
        }
        // Sales List
        $this->db->select('sma_sales.date,sma_sales.reference_no,sma_sales.po_number,sma_sales.customer as supplier,sma_sale_items.*');
        $this->db->from('sma_sale_items');
        $this->db->join('sma_sales','sma_sales.id = sma_sale_items.sale_id','right');
        $this->db->where('sma_sale_items.product_id',$pid);
        if($wid != ""){
            $this->db->where('sma_sale_items.warehouse_id',$wid);
        }
        $q = $this->db->get();
        $sales = $q->result();
        foreach($sales as $sale){
            $temp = array();
            $temp['type'] = "Sale";
            $temp['ref'] = $sale->reference_no;
            $temp['po'] = $sale->po_number;
            $temp['date'] = date_format(date_create($sale->date),"Y-m-d");
            $temp['product_id'] = $sale->product_id;
            $temp['batch'] = $sale->batch;
            $temp['customer_supplier'] = $sale->supplier;
            $temp['qty'] = $sale->quantity;
            $temp['balance'] = 0;
            $rows[] = $temp;
        }
        // SO Pending Items List
        $this->db->select('
            sma_sales_orders_tb.date,
            sma_sales_orders_tb.ref_no,
            sma_sales_orders_tb.po_number,
            sma_companies.name as supplier,
            sma_sales_order_complete_items.*
        ');
        $this->db->from('sma_sales_order_complete_items');
        $this->db->join('sma_sales_orders_tb','sma_sales_orders_tb.id = sma_sales_order_complete_items.so_id','left');
        $this->db->join('sma_companies','sma_companies.id = sma_sales_orders_tb.customer_id','left');
        $this->db->where('sma_sales_order_complete_items.product_id',$pid);
        if($wid != ""){
            $this->db->where('sma_sales_orders_tb.warehouse_id',$wid);
        }
        $this->db->where('sma_sales_order_complete_items.status','pending');
        $q = $this->db->get();
        $sos = $q->result();
        foreach($sos as $so){
            $temp = array();
            $temp['type'] = "SO Hold";
            $temp['ref'] = $so->reference_no;
            $temp['po'] = $so->po_number;
            $temp['date'] = date_format(date_create($so->date),"Y-m-d");
            $temp['product_id'] = $so->product_id;
            $temp['batch'] = $so->batch;
            $temp['customer_supplier'] = $so->supplier;
            $temp['qty'] = $so->quantity;
            $temp['balance'] = 0;
            $rows[] = $temp;
        }
        // Sales Return List
        $this->db->select('
            sma_sale_returns_tb.date,
            sma_sales.reference_no,
            sma_sales.po_number,
            sma_companies.name as supplier,
            sma_sale_return_items_tb.*
        ');
        $this->db->from('sma_sale_return_items_tb');

        $this->db->join('sma_sale_returns_tb','sma_sale_returns_tb.id = sma_sale_return_items_tb.sale_return_id','left');
        $this->db->join('sma_sales','sma_sales.id = sma_sale_returns_tb.sale_id','left');
        $this->db->join('sma_companies','sma_companies.id = sma_sales.customer_id','left');
        $this->db->where('sma_sale_return_items_tb.product_id',$pid);
        if($wid != ""){
            $this->db->where('sma_sale_return_items_tb.warehouse_id',$wid);
        }
        $q = $this->db->get();
        $sos = $q->result();
        foreach($sos as $so){
            $temp = array();
            $temp['type'] = "Sale Return";
            $temp['ref'] = $so->reference_no;
            $temp['po'] = $so->po_number;
            $temp['date'] = date_format(date_create($so->date),"Y-m-d");
            $temp['product_id'] = $so->product_id;
            $temp['batch'] = $so->batch;
            $temp['customer_supplier'] = $so->supplier;
            $temp['qty'] = $so->quantity;
            $temp['balance'] = 0;
            $rows[] = $temp;
        }
        // Purchases Adjustment List
        $this->db->select('DATE_FORMAT(sma_purchase_item_adjs.date, "%Y-%m-%d"),"-" as reference_no,"-" as supplier,sma_purchase_items.*');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_purchase_item_adjs','sma_purchase_item_adjs.id = sma_purchase_items.batch_adj_id','right');
        $this->db->where('sma_purchase_items.product_id',$pid);
        if($wid != ""){
            $this->db->where('sma_purchase_items.warehouse_id',$wid);
        }
        $q = $this->db->get();
        $purchases = $q->result();
        foreach($purchases as $purchase){
            $temp = array();
            $temp['type'] = "Batch Adjustment";
            $temp['ref'] = $purchase->reference_no;
            $temp['po'] = "-";
            $temp['date'] = date_format(date_create($purchase->date),"Y-m-d");
            $temp['product_id'] = $purchase->product_id;
            $temp['batch'] = $purchase->batch;
            $temp['customer_supplier'] = $purchase->supplier;
            $temp['qty'] = $purchase->quantity;
            $temp['balance'] = 0;
            $rows[] = $temp;
        }
        // Purchase Return List
        $this->db->select('
            sma_purchase_return_tb.return_date as date,
            sma_purchases.reference_no,
            "" as po_number,
            sma_companies.name as supplier,
            sma_purchase_return_items_tb.*
        ');
        $this->db->from('sma_purchase_return_items_tb');

        $this->db->join('sma_purchase_return_tb','sma_purchase_return_tb.id = sma_purchase_return_items_tb.purchase_return_id','left');
        $this->db->join('sma_purchases','sma_purchases.id = sma_purchase_return_tb.purchase_id','left');
        $this->db->join('sma_companies','sma_companies.id = sma_purchases.supplier_id','left');
        $this->db->where('sma_purchase_return_items_tb.product_id',$pid);
        if($wid != ""){
            $this->db->where('sma_purchase_return_items_tb.warehouse_id',$wid);
        }
        $q = $this->db->get();
        $sos = $q->result();
        foreach($sos as $so){
            $temp = array();
            $temp['type'] = "Purchase Return";
            $temp['ref'] = $so->reference_no;
            $temp['po'] = $so->po_number;
            $temp['date'] = date_format(date_create($so->date),"Y-m-d");
            $temp['product_id'] = $so->product_id;
            $temp['batch'] = $so->batch;
            $temp['customer_supplier'] = $so->supplier;
            $temp['qty'] = $so->quantity;
            $temp['balance'] = 0;
            $rows[] = $temp;
        }



        // Sorting
        $ord = array();
        foreach ($rows as $key => $value){
            $ord[] = strtotime($value['date']);
        }
        array_multisort($ord, SORT_ASC, $rows);
        $balance = 0;
        //Calculate Balance
        foreach($rows as $row){
            $data = $row;
            if($data['type'] == "Sale" || $data['type'] == "Transfer Out" || $data['type'] == "SO Hold" || $data['type'] == "Purchase Return"){
                $balance = $balance-$data['qty'];
            }
            else if($data['type'] == "Batch Adjustment"){
                $balance = $balance;
            }
            else{
                $balance = $balance+$data['qty'];
            }
            $data['balance'] = $balance;

            $lists[] = $data;
        }
        $qty_in = 0;
        $qty_out = 0;
        foreach($lists as $list){
            if(($start <= $list['date'] || $start == "") && ($end >= $list['date'] || $end == "")){
                $finaeldata[] = $list;
            }
            else{
                if(($start > $list['date'] || $start != "")){
                    if($list['type'] == "Sale" || $list['type'] == "Transfer Out" || $list['type'] == "SO Hold"){
                        $qty_out = $qty_out+$list['qty'];
                    }
                    else if($list['type'] == "Batch Adjustment"){
                    }
                    else{
                        $qty_in = $qty_in+$list['qty'];
                    }
                }
            }
        }
        $finaeldata[0]['date'] = 'Opening';
        $finaeldata[0]['balance'] = $qty_in - $qty_out;

        return $finaeldata;
    }
    public function purchasereport($req = null){
        $start = $req['start'];
        $end = $req['end'];
        $sendvalue = array();
        $this->db->select('
            sma_own_companies.companyname as own_company,
            supplier.cf1 as ntnno,
            supplier.gst_no as gst_no,
            sma_purchases.reference_no,
            sma_purchases.date as purchase_date,
            supplier.company,
            sma_brands.name as brand_name,
            sma_products.hsn_code,
            sma_products.carton_size,
            sma_products.company_code,
            IF(
                sma_tax_rates.type = "1",
                "GST",
                IF(
                    sma_tax_rates.code = "exp",
                    "Exempted",
                    "3rd Schdule"
                )
            ) AS remarks,
            IF(
                sma_tax_rates.type = "1",
                0,
                IF(
                    sma_tax_rates.code = "exp",
                    0,
                    sma_purchase_items.mrp / 1.17
                )
            ) AS mrp_excl_tax,
            IF(
                sma_tax_rates.type = "1", 
                0, 
                IF(
                    sma_tax_rates.code = "exp", 
                    0, 
                    (sma_purchase_items.mrp / 1.17)*sma_purchase_items.quantity
                )
            ) AS value_third_sch,   
            sma_warehouses.name as warehouse_name,
            sma_products.group_id,
            IFNULL(sma_product_groups.name,"Unknown Group") as group_name,
            sma_tax_rates.rate as tax_rate,
            sma_purchase_items.*
        ');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_purchases','sma_purchases.id = sma_purchase_items.purchase_id','left');
        $this->db->join('sma_companies as supplier','supplier.id = sma_purchases.supplier_id','left');
        $this->db->join('sma_own_companies','sma_own_companies.id = sma_purchases.own_company','left');
        $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_purchase_items.tax_rate_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_items.warehouse_id','left');
        $this->db->join('sma_product_groups','sma_product_groups.id = sma_products.group_id','left');
        if($start != ""){
            $this->db->where('sma_purchases.date >= ',$start);
        }
        if($end != ""){
            $end = date('Y-m-d', strtotime($end . ' +1 day'));
            $this->db->where('sma_purchases.date <= ',$end);
        }
        if($req['own_company'] != "" && $req['own_company'] != "all"){
            $this->db->where_in("sma_purchases.own_company",$req['own_company']);
        }
        if($req['supplier'] != "" && $req['supplier'] != "all"){
            $this->db->where_in("sma_purchases.supplier_id",$req['supplier']);
        }
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where_in("sma_purchases.warehouse_id",$req['warehouse']);
        }
        $query = $this->db->get();
        $purchases = $query->result();
        foreach($purchases as $purchase){
            $data = array();

            array_push($data,$purchase->own_company);
            array_push($data,$purchase->ntnno);
            array_push($data,$purchase->gst_no);
            array_push($data,$purchase->reference_no);
            array_push($data,$purchase->company);
            array_push($data,date_format(date_create($purchase->purchase_date),"d/m/Y"));
            array_push($data,$purchase->brand_name);
            array_push($data,$purchase->product_id);
            array_push($data,$purchase->product_name);
            array_push($data,decimalallow($purchase->mrp,2));
            array_push($data,$purchase->hsn_code);
            array_push($data,$purchase->quantity);
            // array_push($data,$purchase->quantity_received);
            array_push($data,decimalallow($purchase->product_unit_code,2));
            array_push($data,decimalallow($purchase->net_unit_cost,2));
            array_push($data,decimalallow($purchase->quantity*$purchase->net_unit_cost,2));
            array_push($data,$purchase->tax_rate);
            array_push($data,decimalallow($purchase->item_tax,2));
            array_push($data,decimalallow($purchase->further_tax,2));
            array_push($data,decimalallow($purchase->fed_tax,2));
            array_push($data,decimalallow($purchase->adv_tax,2));
            array_push($data,decimalallow($purchase->item_tax+$purchase->further_tax+$purchase->fed_tax+$purchase->adv_tax,2));
            array_push($data,decimalallow($purchase->discount,2));
            array_push($data,decimalallow($purchase->subtotal,2));
            array_push($data,$purchase->remarks);
            array_push($data,decimalallow($purchase->mrp_excl_tax,2));
            array_push($data,decimalallow($purchase->value_third_sch,2));
            array_push($data,$purchase->expiry);
            array_push($data,$purchase->batch);
            array_push($data,$purchase->carton_size);
            array_push($data,$purchase->company_code);
            array_push($data,$purchase->warehouse_id);
            array_push($data,$purchase->warehouse_name);
            array_push($data,$purchase->group_id);
            array_push($data,$purchase->group_name);
            array_push($data,$purchase->discount_one == "" ? '0.00' : decimalallow($purchase->discount_one,2));
            array_push($data,$purchase->discount_two == "" ? '0.00' : decimalallow($purchase->discount_two,2));
            array_push($data,$purchase->discount_three == "" ? '0.00' : decimalallow($purchase->discount_three,2));

            $sendvalue[] = $data;
        }
        
        return $sendvalue;
    }
    public function purchasereturn($req = null){
        $start = $req['start'];
        $end = $req['end'];
        $sendvalue = array();

        $this->db->select('
            pri.*,
            p.reference_no,
            p.date as purchase_date,
            pr.return_date,
            sma_brands.name as brand_name,
            sma_products.name as product_name,
            sma_products.hsn_code,
            sma_products.carton_size,
            sma_products.company_code,
            sma_warehouses.name as warehouse_name,
        
        ');
        $this->db->from('sma_purchase_return_items_tb as pri');
        $this->db->join('sma_purchase_return_tb as pr','pr.id = pri.purchase_return_id','left');
        $this->db->join('sma_purchases as p','p.id = pr.purchase_id','left');

        $this->db->join('sma_companies as supplier','supplier.id = p.supplier_id','left');
        $this->db->join('sma_own_companies','sma_own_companies.id = p.own_company','left');
        $this->db->join('sma_products','sma_products.id = pri.product_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_tax_rates','sma_tax_rates.id = pri.item_tax_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = pri.warehouse_id','left');
        $this->db->join('sma_product_groups','sma_product_groups.id = sma_products.group_id','left');
        if($start != ""){
            $this->db->where('pr.return_date >= ',$start);
        }
        if($end != ""){
            $end = date('Y-m-d', strtotime($end . ' +1 day'));
            $this->db->where('pr.return_date <= ',$end);
        }
        if($req['own_company'] != "" && $req['own_company'] != "all"){
            $this->db->where_in("p.own_company",$req['own_company']);
        }
        if($req['supplier'] != "" && $req['supplier'] != "all"){
            $this->db->where_in("p.supplier_id",$req['supplier']);
        }
        if($req['warehouse'] != "" && $req['warehouse'] != "all"){
            $this->db->where_in("p.warehouse_id",$req['warehouse']);
        }
        $query = $this->db->get();
        $purchases = $query->result();
        foreach($purchases as $purchase){
            $data = array();
            array_push($data,$purchase->reference_no);
            array_push($data,$purchase->purchase_date);
            array_push($data,$purchase->return_date);
            array_push($data,$purchase->brand_name);
            array_push($data,$purchase->warehouse_id);
            array_push($data,$purchase->warehouse_name);
            array_push($data,$purchase->product_id);
            array_push($data,$purchase->product_name);
            array_push($data,$purchase->hsn_code);
            array_push($data,$purchase->company_code);
            array_push($data,$purchase->carton_size);
            array_push($data,$purchase->expiry);
            array_push($data,$purchase->batch);
            array_push($data,$purchase->quantity);
            array_push($data,$purchase->mrp);
            array_push($data,$purchase->net_unit_cost);
            array_push($data,$purchase->item_tax);
            array_push($data,$purchase->further_tax);
            array_push($data,$purchase->fed_tax);
            array_push($data,$purchase->total_tax);
            array_push($data,$purchase->subtotal);
            array_push($data,$purchase->reason);
            $sendvalue[] = $data;
        }
        
        return $sendvalue;
    }
    public function batch_wise_true_false($req = null){
        $sendvalue = array();
        if($req['product_id'] != ""){
            $this->db->select('
                sma_purchase_items.id as purchase_item_id,
                sma_purchase_items.transfer_id,
                sma_purchase_items.purchase_id,
                sma_purchase_items.batch_adj_id,
                sma_purchase_items.product_id,
                sma_purchase_items.product_name AS product_name,
                sma_purchase_items.batch AS batch,
                sma_warehouses.name as warehouse_name,
                (
                    SELECT
                        COALESCE(SUM(piqty2.quantity_balance), 0) 
                    FROM sma_purchase_items AS piqty2
                    WHERE 
                        piqty2.product_id = sma_purchase_items.product_id AND
                        piqty2.warehouse_id = sma_purchase_items.warehouse_id AND
                        piqty2.batch = sma_purchase_items.batch AND
                        piqty2.quantity_balance != 0
                ) AS quantity_balance,
                (
                    SELECT
                        COALESCE(SUM(piqty.quantity_received), 0) 
                    FROM sma_purchase_items AS piqty
                    WHERE 
                        piqty.product_id = sma_purchase_items.product_id AND
                        piqty.warehouse_id = sma_purchase_items.warehouse_id AND
                        piqty.batch = sma_purchase_items.batch
                ) AS purchase_qty,
                (
                    SELECT
                        COALESCE(SUM(sma_purchase_return_items_tb.quantity), 0)
                    FROM sma_purchase_return_items_tb
                    WHERE  
                        sma_purchase_return_items_tb.product_id = sma_purchase_items.product_id AND
                        sma_purchase_return_items_tb.warehouse_id = sma_purchase_items.warehouse_id AND
                        sma_purchase_return_items_tb.batch = sma_purchase_items.batch
                ) AS purchase_return_qty,

                (
                    SELECT
                        COALESCE(SUM(sma_sale_items.quantity), 0) 
                    FROM sma_sale_items 
                    WHERE 
                        sma_sale_items.product_id = sma_purchase_items.product_id AND
                        sma_sale_items.warehouse_id = sma_purchase_items.warehouse_id AND
                        sma_sale_items.batch = sma_purchase_items.batch
                ) AS sale_qty,
                (
                    SELECT
                        COALESCE(SUM(sma_sale_return_items_tb.quantity), 0)
                    FROM sma_sale_return_items_tb
                    WHERE  
                        sma_sale_return_items_tb.product_id = sma_purchase_items.product_id AND
                        sma_sale_return_items_tb.warehouse_id = sma_purchase_items.warehouse_id AND
                        sma_sale_return_items_tb.batch = sma_purchase_items.batch
                ) AS sale_return_qty,
                (
                    SELECT
                        COALESCE(SUM(sma_sales_order_complete_items.quantity), 0)
                    FROM sma_sales_order_complete_items
                    WHERE  
                        sma_sales_order_complete_items.product_id = sma_purchase_items.product_id AND 
                        sma_sales_order_complete_items.warehouse_id = sma_purchase_items.warehouse_id AND 
                        sma_sales_order_complete_items.batch = sma_purchase_items.batch AND 
                        sma_sales_order_complete_items.status = "pending"
                ) AS so_qty
            ');
            $this->db->from('sma_purchase_items');
            $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_items.warehouse_id','left');
            if($req['ssid'] != ""){
                $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
                $this->db->where('sma_products.supplier1',$req['ssid']);
            }
            else{
                $this->db->where('sma_purchase_items.product_id',$req['product_id']);
            }
            $q = $this->db->get();
            $rows = $q->result();
            foreach($rows as $row){
                $data = array();
                array_push($data,$row->purchase_item_id);
                array_push($data,$row->product_id);
                array_push($data,$row->product_name);
                // array_push($data,decimalallow($row->pqty,0));
                array_push($data,$row->batch);
                if($row->transfer_id != "" && $row->transfer_id != 0){
                    array_push($data,'Transfer Batch');
                }
                else if($row->batch_adj_id != "" && $row->batch_adj_id != 0){
                    array_push($data,'Adjustment Batch');
                }
                else{
                    array_push($data,'Purchase Batch');
                }
                array_push($data,$row->warehouse_name);
                array_push($data,$row->quantity_balance);
                array_push($data,$row->purchase_qty);
                array_push($data,$row->purchase_return_qty);
                array_push($data,$row->sale_qty);
                array_push($data,$row->sale_return_qty);
                array_push($data,$row->so_qty);

                $actual_qty = $row->purchase_qty - $row->purchase_return_qty - ($row->sale_qty - $row->sale_return_qty)  - $row->so_qty;
                array_push($data,$actual_qty);
                $status  = 'FALSE';
                if($actual_qty == $row->quantity_balance){
                    $status  = 'TRUE';
                }
                array_push($data,$status);
                // array_push($data,$row->product_id);
    
                $sendvalue[] = $data;
            }

        }

        return $sendvalue;
    }
    public function product_wise_true_false($req = null){
        $sendvalue = array();
        if($req['supplier'] != "" ){
            $this->db->select('
                sma_products.id as pid,
                sma_products.name as pname,
                sma_products.quantity as pqty,
                (
                    SELECT
                        SUM(sma_warehouses_products.quantity)
                    FROM
                        sma_warehouses_products
                    WHERE
                        sma_warehouses_products.product_id = sma_products.id AND sma_warehouses_products.quantity > 0
                ) as wqty,
                (
                    SELECT
                        SUM(sma_purchase_items.quantity_balance)
                    FROM
                        sma_purchase_items
                    WHERE
                        sma_purchase_items.product_id = sma_products.id AND
                        sma_purchase_items.quantity_balance != 0
                ) as bqty,
                (
                    SELECT 
                        COALESCE(SUM(sma_purchase_items.quantity_received), 0) 
                    FROM sma_purchase_items 
                    WHERE sma_purchase_items.product_id = sma_products.id
                ) AS purchase_qty,
                (
                    SELECT
                        COALESCE(SUM(sma_purchase_return_items_tb.quantity), 0)
                    FROM sma_purchase_return_items_tb
                    WHERE  
                        sma_purchase_return_items_tb.product_id = sma_products.id
                ) AS purchase_return_qty,
                (
                    SELECT
                        COALESCE(SUM(sma_sale_items.quantity), 0) 
                    FROM sma_sale_items 
                    WHERE sma_sale_items.product_id = sma_products.id
                ) AS sale_qty,
                (
                    SELECT
                        COALESCE(SUM(sma_sale_return_items_tb.quantity), 0)
                    FROM sma_sale_return_items_tb
                    WHERE  
                        sma_sale_return_items_tb.product_id = sma_products.id
                ) AS sale_return_qty,
                (
                    SELECT
                        COALESCE(SUM(sma_sales_order_complete_items.quantity), 0)
                    FROM sma_sales_order_complete_items
                    WHERE  
                        sma_sales_order_complete_items.product_id = sma_products.id AND 
                        sma_sales_order_complete_items.status = "pending"
                ) AS so_qty
            ');
            $this->db->from('sma_products');
            if($req['supplier'] != "all" ){
                $this->db->where('sma_products.supplier1',$req['supplier']);
            }
            $q = $this->db->get();
            $products = $q->result();
            foreach($products as $product){
                $data = array();
                array_push($data,$product->pid);
                array_push($data,$product->pname);
                array_push($data,decimalallow($product->pqty,0));
                array_push($data,decimalallow($product->wqty,0));
                array_push($data,decimalallow($product->bqty,0));
                array_push($data,decimalallow($product->purchase_qty,0));
                array_push($data,decimalallow($product->purchase_return_qty,0));
                array_push($data,decimalallow($product->sale_qty,0));
                array_push($data,decimalallow($product->sale_return_qty,0));
                array_push($data,decimalallow($product->so_qty,0));
                $actual_qty = $product->purchase_qty - $product->purchase_return_qty - ($product->sale_qty - $product->sale_return_qty)  - $product->so_qty;
                array_push($data,$actual_qty);
                $status  = 'FALSE';
                $note = "";
                if($actual_qty == $product->bqty){
                    if($actual_qty == $product->wqty){
                        if($actual_qty == $product->pqty){
                            $status  = 'TRUE';
                        }
                        else{
                            $note = "Issue in batch quantity";
                        }
                    }
                    else{
                        $note = "Issue in warehouse quantity";
                    }
                }
                else{
                    $note = "Issue in product quantity";
                }
                array_push($data,$status);
                array_push($data,$note);

                $sendvalue[] = $data;
            }
        }
        return $sendvalue;
    }
    public function so_hold_quantity($req = null){
        $sendvalue = array();

        $this->db->select('
            sma_sales_orders_tb.date,
            sma_sales_orders_tb.ref_no,
            sma_sales_order_complete_items.product_id,
            sma_products.name as product_name,
            sma_products.group_id,
            sma_sales_order_complete_items.batch,
            sma_sales_order_complete_items.quantity,
            sma_warehouses.name as warehouse_name,
            sma_companies.name as customer
        ');
        $this->db->from('sma_sales_order_complete_items');
        $this->db->join('sma_sales_orders_tb','sma_sales_orders_tb.id = sma_sales_order_complete_items.so_id','left');
        $this->db->join('sma_products','sma_products.id = sma_sales_order_complete_items.product_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_sales_orders_tb.warehouse_id','left');
        $this->db->join('sma_companies','sma_companies.id = sma_sales_orders_tb.customer_id','left');
        $this->db->where('sma_sales_order_complete_items.status','pending');
        $q = $this->db->get();
        $sos = $q->result();
        foreach($sos as $so){
            $data = array();
            array_push($data,date_format(date_create($so->date),"d/m/Y"));
            array_push($data,$so->ref_no);
            array_push($data,$so->product_id);
            array_push($data,$so->group_id);
            array_push($data,$so->product_name);
            array_push($data,$so->customer);
            array_push($data,$so->warehouse_name);
            array_push($data,$so->batch);
            array_push($data,$so->quantity);
            $sendvalue[] = $data;

        }

        return $sendvalue;
    }
}
