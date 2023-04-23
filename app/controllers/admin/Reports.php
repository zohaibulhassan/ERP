<?php defined('BASEPATH') or exit('No direct script access allowed');
class Reports extends MY_Controller
{
    function __construct(){
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('reports_model');
        $this->load->admin_model('general_model');
        $this->data['pb'] = array(
            'cash' => lang('cash'),
            'CC' => lang('CC'),
            'Cheque' => lang('Cheque'),
            'paypal_pro' => lang('paypal_pro'),
            'stripe' => lang('stripe'),
            'gift_card' => lang('gift_card'),
            'deposit' => lang('deposit'),
            'authorize' => lang('authorize')
        );
    }
    public function registers(){
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Register Report'));
        $meta = array('page_title' => 'Register Report', 'bc' => $bc);
        $this->page_construct2('reports/registers', $meta, $this->data);
    }
    public function registers_ajax(){
        echo $this->reports_model->registers();
    }
    public function registers_detail($id){

        $this->data['register'] = $this->db->select('*')->from('pos_register')->where('id',$id)->get()->row();

        $this->db->select('id,date,reference_no,customer,paid,grand_total');
        $this->db->from('sales');
        $this->db->where('created_by',$this->data['register']->user_id);
        $this->db->where('date >= ',$this->data['register']->date);
        if($this->data['register']->status == "close"){
            $this->db->where('date <= ',$this->data['register']->closed_at);
        }
        $sq = $this->db->get();
        $this->data['sales'] = $sq->result();


        $this->db->select('
            payments.date,
            payments.reference_no,
            payments.paid_by,
            sales.id as invoice_id,
            sales.reference_no as invoice_no,
            payments.amount,
            payments.note
        ');
        $this->db->from('payments');
        $this->db->join('sales','sales.id = payments.sale_id','left');
        $this->db->where('payments.created_by',$this->data['register']->user_id);
        $this->db->where('payments.date >= ',$this->data['register']->date);
        if($this->data['register']->status == "close"){
            $this->db->where('payments.date <= ',$this->data['register']->closed_at);
        }
        $pq = $this->db->get();
        $this->data['payments'] = $pq->result();



        $this->db->select('
            sales.id,
            return.date,
            sales.reference_no,
            sales.customer,
            return.grand_total
        ');
        $this->db->from('sma_sale_returns_tb as return');
        $this->db->join('sales','sales.id = return.sale_id','left');
        $this->db->where('return.created_by',$this->data['register']->user_id);
        $this->db->where('return.date >= ',$this->data['register']->date);
        if($this->data['register']->status == "close"){
            $this->db->where('return.date <= ',$this->data['register']->closed_at);
        }
        $sq = $this->db->get();
        $this->data['returns'] = $sq->result();



                                    
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => 'Detail Register Report'));
        $meta = array('page_title' => 'Detail Register Report', 'bc' => $bc);
        $this->page_construct2('reports/register_detail', $meta, $this->data);
    }







    function index(){
        $this->sma->checkPermissions();
        $data['error']               = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->reports_model->getChartData();
        $this->data['stock']         = $this->reports_model->getStockValue();
        $bc                          = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('reports')
            )
        );
        $meta                        = array(
            'page_title' => lang('reports'),
            'bc' => $bc
        );
        $this->page_construct('reports/index', $meta, $this->data);
    }
    function ledger_reports(){
        $this->data['supplier'] = $this->site->GetAllSupplierList();
        $this->session->set_userdata('user_csrf', $value);
        $this->data['csrf'] = $this->session->userdata('user_csrf');

        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('ledger_reports')
            )
        );
        $meta = array(
            'page_title' => lang('ledger_reports'),
            'bc' => $bc
        );
        $this->page_construct('reports/ledger_reports', $meta, $this->data);
    }
    public function getLedgerReporting($xls = Null){

        $xls = ($this->uri->slash_segment(5) == 'xls/') ? $this->uri->slash_segment(5) : "";

        $this->sma->checkPermissions('products', TRUE);

        $start_date  = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $start_dates = substr($start_date, 0, 2);
        $start_month = substr($start_date, 3, 2);
        $start_year  = substr($start_date, 6, 4);
        $start_date  = "$start_year-$start_month-$start_dates";
        $end_date  = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $end_dates = substr($end_date, 0, 2);
        $end_month = substr($end_date, 3, 2);
        $end_year  = substr($end_date, 6, 4);
        $end_date  = "$end_year-$end_month-$end_dates";
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;

        $qry = "SELECT sma_purchases.date, sma_purchases.supplier as 'purchases_supplier', sma_purchases.reference_no as 'purchases_reference_no', sma_purchases.total as 'purchases_total', sma_purchases.product_tax as 'purchases_product_tax', sma_purchases.grand_total as 'purchases_grand_total', sma_payments.amount as 'payment_amount', ((sma_purchases.grand_total) - (sma_purchases.paid)) as balance, sma_payments.date as 'payment_date', sma_payments.cheque_no as 'payment_cheque_no', sma_purchases.payment_status as 'purchases_purchases_status', sma_payments.reference_no as 'payment_reference_no', sma_payments.paid_by as 'payment_paid_by', sma_payments.note as 'payment_note' FROM `sma_purchases` LEFT JOIN `sma_payments` ON `sma_payments`.`purchase_id` = `sma_purchases`.`id` ";

        if (!empty($supplier)) {
            $qry .= "where sma_purchases.supplier_id = $supplier";
        }

        if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
            $qry .= " and sma_purchases.date >= '" . $start_date . "' and sma_purchases.date <= '" . $end_date . " 23:59:59'";
            // $this->db->where($where);
        }

        // if (!empty($start_date) && !empty($end_date)) {
        //     $qry .= " and sma_purchases.date >= '" . $start_date . "' and sma_purchases.date <= '" . $end_date . " 23:59:59'";
        //     //$this->db->where($where);
        // }

        // echo $qry;
        // die;

        $q = $this->db->query($qry);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
        } else {
            $data = NULL;
        }

        // print_r($data);

        // die;

        if ($pdf || $xls) {
            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('ledger_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('Date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('Supplier'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('Invoice_No'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('Value_Ex_Tax'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('Tax'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('Value_Inc_Tax'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('Paid_Amount'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('Balance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('Payment_Date'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('Cheque_No'));
                $this->excel->getActiveSheet()->SetCellValue('K1', lang('Payment_Status'));
                $this->excel->getActiveSheet()->SetCellValue('L1', lang('Payment_Reference_No'));
                $this->excel->getActiveSheet()->SetCellValue('M1', lang('Paid_By'));
                $this->excel->getActiveSheet()->SetCellValue('N1', lang('Note'));

                $row = 2;

                $own_company           = 0;
                $customer_ntn          = 0;
                $invoice_no            = 0;
                $date                  = 0;
                $po_number             = 0;
                $customer_name         = 0;
                $product_name          = 0;
                $hsn_code              = 0;
                $qty_order             = 0;
                $uom                   = 0;
                $price_ex_tax          = 0;
                $selling_price         = 0;
                $tax                   = 0;
                $item_tax              = 0;
                $frther_tax            = 0;
                $fed_tax               = 0;
                $total_include_all_tax = 0;
                $sale_inc              = 0;
                $trade_dis             = 0;
                $consumer_dis          = 0;
                $total_dis             = 0;
                $subtotal              = 0;
                $remarks               = 0;
                $mrp                   = 0;
                $expiry_date           = 0;
                $batch                 = 0;
                $brand                 = 0;
                $carton_size                 = 0;

                foreach ($data as $data_row) {

                    $source = $data_row->date;
                    $date   = new DateTime($source);

                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->date);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->purchases_supplier);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->purchases_reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->purchases_total);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->purchases_product_tax);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->purchases_grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->payment_amount);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->balance);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $date->payment_date);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->payment_cheque_no);
                    $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->purchases_purchases_status);
                    $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->payment_reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->payment_paid_by);
                    $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->payment_note);

                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(25);

                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'ledger_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->db->save_queries = TRUE;
        $this->load->library('datatables');

        // $this->datatables->select("
        //     {$this->db->dbprefix('purchases')}.date,
        //     {$this->db->dbprefix('purchases')}.supplier as 'purchases_supplier',
        //     {$this->db->dbprefix('purchases')}.reference_no as 'purchases_reference_no', 
        //     {$this->db->dbprefix('purchases')}.total as 'purchases_total', 
        //     {$this->db->dbprefix('purchases')}.product_tax as 'purchases_product_tax', 
        //     {$this->db->dbprefix('purchases')}.grand_total as 'purchases_grand_total', 
        //     {$this->db->dbprefix('payments')}.amount as 'payment_amount', 
        //     (({$this->db->dbprefix('purchases')}.grand_total) - ({$this->db->dbprefix('purchases')}.paid)) as balance,
        //     {$this->db->dbprefix('payments')}.date as 'payment_date',
        //     {$this->db->dbprefix('payments')}.cheque_no as 'payment_cheque_no', 
        //     {$this->db->dbprefix('purchases')}.payment_status as 'purchases_purchases_status',
        //     {$this->db->dbprefix('payments')}.reference_no as 'payment_reference_no',
        //     {$this->db->dbprefix('payments')}.paid_by as 'payment_paid_by',
        //     {$this->db->dbprefix('payments')}.note as 'payment_note'", FALSE)
        //     ->from('payments')
        //     ->join('purchases', 'payments.purchase_id = purchases.id', 'left');
        // if (!empty($supplier)) {
        //     $this->datatables->where("{$this->db->dbprefix('purchases')}.supplier_id = ", $supplier);
        // }
        // if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
        //     $where = "sma_purchases.date >= '" . $start_date . "' and sma_purchases.date <= '" . $end_date . " 23:59:59'";
        //     $this->datatables->where($where);
        // }


        $this->datatables->select("
            {$this->db->dbprefix('purchases')}.date,
            {$this->db->dbprefix('purchases')}.supplier as 'purchases_supplier',
            {$this->db->dbprefix('purchases')}.reference_no as 'purchases_reference_no', 
            {$this->db->dbprefix('purchases')}.total as 'purchases_total', 
            {$this->db->dbprefix('purchases')}.product_tax as 'purchases_product_tax', 
            {$this->db->dbprefix('purchases')}.grand_total as 'purchases_grand_total', 
            {$this->db->dbprefix('payments')}.amount as 'payment_amount', 
            (({$this->db->dbprefix('purchases')}.grand_total) - ({$this->db->dbprefix('purchases')}.paid)) as balance,
            {$this->db->dbprefix('payments')}.date as 'payment_date',
            {$this->db->dbprefix('payments')}.cheque_no as 'payment_cheque_no', 
            {$this->db->dbprefix('purchases')}.payment_status as 'purchases_purchases_status',
            {$this->db->dbprefix('payments')}.reference_no as 'payment_reference_no',
            {$this->db->dbprefix('payments')}.paid_by as 'payment_paid_by',
            {$this->db->dbprefix('payments')}.note as 'payment_note'", FALSE)
            ->from('purchases')
            ->join('payments', 'payments.purchase_id = purchases.id', 'left');
        if (!empty($supplier)) {
            $this->datatables->where("{$this->db->dbprefix('purchases')}.supplier_id = ", $supplier);
        }
        if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
            $where = "sma_purchases.date >= '" . $start_date . "' and sma_purchases.date <= '" . $end_date . " 23:59:59'";
            $this->datatables->where($where);
        }
        echo $this->datatables->generate();
        // echo $this->db->last_query();
    }
    function detail_reports(){

        $this->data['own_company'] = $this->site->getAllown_companies();
        $this->data['billers']     = $this->site->getAllCompanies('biller');
        $this->session->set_userdata('user_csrf', $value);
        $this->data['csrf'] = $this->session->userdata('user_csrf');

        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('reports')
            )
        );
        $meta = array(
            'page_title' => lang('reports'),
            'bc' => $bc
        );
        $this->page_construct('reports/detail_reports', $meta, $this->data);
        // echo '<pre>';print_r($_POST); echo '</pre>';
        //echo json_encode($this->data);


    }
    function getListReporting($xls = Null){
        $pdf = false;        
        $xls = ($this->uri->slash_segment(5) == 'xls/') ? $this->uri->slash_segment(5) : "";
        //$this->sma->checkPermissions('products', TRUE);
        $report_type = $this->input->get('report_type') ? $this->input->get('report_type') : NULL;
        $own_company = $this->input->get('own_company') ? $this->input->get('own_company') : NULL;
        $biller      = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $product     = $this->input->get('product') ? $this->input->get('product') : NULL;
        $category    = $this->input->get('category') ? $this->input->get('category') : NULL;
        $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : NULL;
        $brand       = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        $warehouse   = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $start_date  = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $start_dates = substr($start_date, 0, 2);
        $start_month = substr($start_date, 3, 2);
        $start_year  = substr($start_date, 6, 4);
        $start_date  = "$start_year-$start_month-$start_dates";

        $end_date  = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $end_dates = substr($end_date, 0, 2);
        $end_month = substr($end_date, 3, 2);
        $end_year  = substr($end_date, 6, 4);
        $end_date  = "$end_year-$end_month-$end_dates";

        // echo 'I m here';
        // echo $report_type;

        // die;
        if ($report_type) {
            //$query = "select * from ";
            if ($report_type == "1") {

                $qry = "SELECT
                sma_own_companies.companyname,
                sma_products.hsn_code,                
                sma_purchase_items.item_tax,
                sma_purchase_items.product_unit_code,
                sma_purchase_items.tax,
                IF(
                    sma_tax_rates.type = '1',
                    'GST',
                    IF(
                        sma_tax_rates.code = 'exp',
                        'Exempted',
                        '3rd Schdule'
                    )
                ) AS 'Remarks',
                sma_companies.company,
                sma_companies.cf1,
                sma_companies.gst_no,
                sma_purchases.date,
                sma_purchases.reference_no,
                sma_purchase_items.product_id,
                sma_purchase_items.product_name,
                sma_purchase_items.quantity,
                sma_purchase_items.quantity_received,
                sma_purchase_items.expiry,
                sma_purchase_items.batch,
                sma_purchase_items.net_unit_cost,
                (
                    sma_purchase_items.quantity_received * sma_purchase_items.net_unit_cost
                ) AS 'Total_Price_excluding_Tax',
                sma_purchase_items.discount,
                sma_purchase_items.subtotal,
                sma_purchase_items.discount_one,
                sma_purchase_items.discount_two,
                sma_purchase_items.discount_three,
                sma_purchase_items.further_tax,
                sma_purchase_items.fed_tax,
                sma_brands.name,
                sma_purchases.warehouse_id,
                sma_products.carton_size,
                IF(
                    sma_tax_rates.type = '1',
                    0,
                    IF(
                        sma_tax_rates.code = 'exp',
                        0,
                        sma_purchase_items.mrp / 1.17
                    )
                ) AS 'mrp_excl_tax',
                IF(sma_tax_rates.type = '1', 0, IF(sma_tax_rates.code = 'exp', 0, (sma_purchase_items.mrp / 1.17)*sma_purchase_items.quantity)) AS 'value_third_sch',   
                sma_products.company_code,
                sma_purchase_items.mrp
                FROM
                    sma_purchases
                RIGHT JOIN sma_purchase_items ON sma_purchases.id = sma_purchase_items.purchase_id
                LEFT JOIN sma_companies ON sma_purchases.supplier_id = sma_companies.id
                LEFT JOIN sma_tax_rates ON sma_purchase_items.tax_rate_id = sma_tax_rates.id
                LEFT JOIN sma_own_companies ON sma_purchases.own_company = sma_own_companies.id
                LEFT JOIN sma_products ON sma_products.id = sma_purchase_items.product_id
                LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand";

                if (($start_date !== '' && $start_date !== '--') || ($end_date !== '' && $end_date !== '--') || ($warehouse !== '')) {
                    $qry .= " WHERE 1=1 ";
                }

                if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
                    // $qry .=  " where sma_sales.date BETWEEN '".$start_date."' and '".$end_date."'";
                    $qry .= " AND sma_purchases.date >= '" . $start_date . "' and sma_purchases.date <= '" . $end_date . " 23:59:59'";
                }

                if (($warehouse !== '')) {
                    // $qry .=  " where sma_sales.date BETWEEN '".$start_date."' and '".$end_date."'";
                    $qry .= " AND sma_purchases.warehouse_id >= '" . $warehouse . "'";
                }


                // SHow Some Duplicate Entry
                // $qry = "SELECT sma_own_companies.companyname, sma_products.hsn_code, sma_purchase_items.item_tax, sma_purchase_items.tax, IF( sma_tax_rates.type = '1', 'GST', IF( sma_tax_rates.code = 'exp', 'Exempted', '3rd Schdule') ) AS 'Remarks', sma_companies.company, sma_companies.cf1, sma_companies.gst_no, sma_purchases.date, sma_purchases.reference_no, sma_purchase_items.product_name, sma_purchase_items.quantity, sma_purchase_items.quantity_received, sma_purchase_items.expiry, sma_purchase_items.batch, sma_purchase_items.net_unit_cost,(sma_purchase_items.quantity_received * sma_purchase_items.net_unit_cost) AS 'Total_Price_excluding_Tax', sma_purchase_items.discount, sma_purchase_items.subtotal, sma_purchase_items.discount_one, sma_purchase_items.discount_two, sma_purchase_items.discount_three, sma_purchase_items.further_tax, sma_purchase_items.fed_tax, sma_brands.name, sma_payments.paid_by, sma_payments.cheque_no, sma_payments.amount, sma_payments.note, sma_payments.pos_paid, sma_payments.pos_balance, sma_payments.created_by FROM sma_purchases LEFT JOIN sma_purchase_items ON sma_purchases.id = sma_purchase_items.purchase_id LEFT JOIN sma_companies ON sma_purchases.supplier_id = sma_companies.id LEFT JOIN sma_tax_rates ON sma_purchase_items.tax_rate_id = sma_tax_rates.id LEFT JOIN sma_own_companies ON sma_purchases.own_company = sma_own_companies.id LEFT JOIN sma_products ON sma_products.id = sma_purchase_items.product_id LEFT JOIN sma_payments ON sma_payments.purchase_id = sma_purchases.id LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand";

                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }
                if ($pdf || $xls) {

                    if (!empty($data)) {

                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                        $this->excel->getActiveSheet()->setTitle(lang('purchase_report'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('Own Comapny Name'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('NTN'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('GST Number'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('Refrence No'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('Company'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('Date'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('Product ID'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('Product Name'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('HSN Code'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('Quantity'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('Quantity Recieved'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('UOM'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('Net Unit Cost'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('Total Price Ex. Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('Item Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('Q1', lang('Further Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('R1', lang('Fed Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('S1', lang('Total Taxes'));
                        $this->excel->getActiveSheet()->SetCellValue('T1', lang('Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('U1', lang('Sub Total'));
                        $this->excel->getActiveSheet()->SetCellValue('V1', lang('Sales Incentive'));
                        $this->excel->getActiveSheet()->SetCellValue('W1', lang('Trade Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('X1', lang('Consumer Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('Y1', lang('Remarks'));
                        $this->excel->getActiveSheet()->SetCellValue('Z1', lang('MRP Ex. Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('AA1', lang('MRP Ex. Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('AB1', lang('MRP'));
                        $this->excel->getActiveSheet()->SetCellValue('AC1', lang('Expiry'));
                        $this->excel->getActiveSheet()->SetCellValue('AD1', lang('Batch'));
                        $this->excel->getActiveSheet()->SetCellValue('AE1', lang('Brand'));
                        $this->excel->getActiveSheet()->SetCellValue('AF1', lang('Carton Size'));
                        $this->excel->getActiveSheet()->SetCellValue('AG1', lang('Company Code'));
                        $this->excel->getActiveSheet()->SetCellValue('AH1', lang('Warehouse ID'));



                        $row = 2;

                        $own_company           = 0;
                        $customer_ntn          = 0;
                        $invoice_no            = 0;
                        $date                  = 0;
                        $po_number             = 0;
                        $customer_name         = 0;
                        $product_name          = 0;
                        $hsn_code              = 0;
                        $qty_order             = 0;
                        $uom                   = 0;
                        $price_ex_tax          = 0;
                        $selling_price         = 0;
                        $tax                   = 0;
                        $item_tax              = 0;
                        $frther_tax            = 0;
                        $fed_tax               = 0;
                        $total_include_all_tax = 0;
                        $sale_inc              = 0;
                        $trade_dis             = 0;
                        $consumer_dis          = 0;
                        $total_dis             = 0;
                        $subtotal              = 0;
                        $remarks               = 0;
                        $mrp                   = 0;
                        $expiry_date           = 0;
                        $batch                 = 0;
                        $brand                 = 0;
                        $carton_size           = 0;
                        $mrp_excl_tax           = 0;
                        $company_code           = 0;
                        $warehouse_id           = 0;
                        $mrp                    = 0;


                        foreach ($data as $data_row) {

                            $source = $data_row->date;
                            $date   = new DateTime($source);

                            // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->companyname);
                            // $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->hsn_code);
                            // $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->item_tax);
                            // $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->tax);
                            // $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->Remarks);
                            // $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->company);
                            // $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->cf1);
                            // $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->gst_no);
                            // $this->excel->getActiveSheet()->SetCellValue('I' . $row, $date->format('d-M-Y'));
                            // $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->reference_no);
                            // $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->product_id);
                            // $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->product_name);
                            // $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->quantity); // Correct karna hy
                            // $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->quantity_received);
                            // $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->expiry);
                            // $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->batch);
                            // $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->net_unit_cost);
                            // $this->excel->getActiveSheet()->SetCellValue('R' . $row, $data_row->Total_Price_excluding_Tax); // Correct karna hy
                            // $this->excel->getActiveSheet()->SetCellValue('S' . $row, $data_row->discount);
                            // $this->excel->getActiveSheet()->SetCellValue('T' . $row, $data_row->subtotal);
                            // $this->excel->getActiveSheet()->SetCellValue('U' . $row, $data_row->discount_one);
                            // $this->excel->getActiveSheet()->SetCellValue('V' . $row, $data_row->discount_two);
                            // $this->excel->getActiveSheet()->SetCellValue('W' . $row, $data_row->discount_three);
                            // $this->excel->getActiveSheet()->SetCellValue('X' . $row, $data_row->further_tax); // Correct karna hy
                            // $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $data_row->fed_tax);
                            // $this->excel->getActiveSheet()->SetCellValue('Z' . $row, $data_row->name);
                            // $this->excel->getActiveSheet()->SetCellValue('AA' . $row, $data_row->carton_size);
                            // $this->excel->getActiveSheet()->SetCellValue('AB' . $row, $data_row->mrp_excl_tax);
                            // $this->excel->getActiveSheet()->SetCellValue('AC' . $row, $data_row->value_third_sch);
                            // $this->excel->getActiveSheet()->SetCellValue('AD' . $row, $data_row->company_code);
                            // $this->excel->getActiveSheet()->SetCellValue('AE' . $row, $data_row->warehouse_id);
                            // $this->excel->getActiveSheet()->SetCellValue('AF' . $row, $data_row->mrp);

                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->companyname);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->cf1);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->gst_no);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->company);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $date->format('d-M-Y'));
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->product_id);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->product_name);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->hsn_code);
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->quantity);
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->quantity_received);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->product_unit_code);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->net_unit_cost);
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->Total_Price_excluding_Tax); // Correct karna hy
                            $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->tax);
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->item_tax);
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->further_tax);
                            $this->excel->getActiveSheet()->SetCellValue('R' . $row, $data_row->fed_tax);
                            $this->excel->getActiveSheet()->SetCellValue('S' . $row, $data_row->item_tax + $data_row->further_tax + $data_row->fed_tax); // Correct karna hy
                            $this->excel->getActiveSheet()->SetCellValue('T' . $row, $data_row->discount);
                            $this->excel->getActiveSheet()->SetCellValue('U' . $row, $data_row->subtotal);
                            $this->excel->getActiveSheet()->SetCellValue('V' . $row, $data_row->discount_one);
                            $this->excel->getActiveSheet()->SetCellValue('W' . $row, $data_row->discount_two);
                            $this->excel->getActiveSheet()->SetCellValue('X' . $row, $data_row->discount_three);
                            $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $data_row->Remarks);
                            $this->excel->getActiveSheet()->SetCellValue('Z' . $row, $data_row->mrp_excl_tax); // Correct karna hy
                            $this->excel->getActiveSheet()->SetCellValue('AA' . $row, $data_row->value_third_sch);
                            $this->excel->getActiveSheet()->SetCellValue('AB' . $row, $data_row->mrp);
                            $this->excel->getActiveSheet()->SetCellValue('AC' . $row, $data_row->expiry);
                            $this->excel->getActiveSheet()->SetCellValue('AD' . $row, $data_row->batch);
                            $this->excel->getActiveSheet()->SetCellValue('AE' . $row, $data_row->name);
                            $this->excel->getActiveSheet()->SetCellValue('AF' . $row, $data_row->carton_size);
                            $this->excel->getActiveSheet()->SetCellValue('AG' . $row, $data_row->company_code);
                            $this->excel->getActiveSheet()->SetCellValue('AH' . $row, $data_row->warehouse_id);

                            $row++;
                        }

                        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AB')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AD')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AE')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AG')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AH')->setWidth(25);

                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                        $filename = 'purchase_report';
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                    $this->session->set_flashdata('error', lang('nothing_found'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }


                // $this->db->save_queries = TRUE;
                $this->load->library('datatables');
                $this->datatables->select("
                            {$this->db->dbprefix('own_companies')}.companyname, 
                            {$this->db->dbprefix('products')}.hsn_code, 
                            {$this->db->dbprefix('companies')}.company,
                            {$this->db->dbprefix('purchases')}.date,
                            {$this->db->dbprefix('purchases')}.reference_no,
                            {$this->db->dbprefix('purchase_items')}.product_id,
                            {$this->db->dbprefix('purchase_items')}.product_name,
                            {$this->db->dbprefix('purchase_items')}.quantity,
                            {$this->db->dbprefix('purchase_items')}.quantity_received,
                            {$this->db->dbprefix('purchase_items')}.expiry,
                            {$this->db->dbprefix('purchase_items')}.batch,
                            {$this->db->dbprefix('purchase_items')}.net_unit_cost,
                            {$this->db->dbprefix('purchase_items')}.discount,
                            {$this->db->dbprefix('purchase_items')}.subtotal,
                            {$this->db->dbprefix('purchase_items')}.discount_one,
                            {$this->db->dbprefix('purchase_items')}.discount_two,
                            {$this->db->dbprefix('purchase_items')}.discount_three,
                            {$this->db->dbprefix('purchase_items')}.further_tax,
                            {$this->db->dbprefix('purchase_items')}.fed_tax,
                            {$this->db->dbprefix('brands')}.name,
                            {$this->db->dbprefix('products')}.carton_size
                    ")->from('purchases')->join('purchase_items', 'purchases.id = purchase_items.purchase_id', 'left')->join('companies', 'purchases.supplier_id = companies.id', 'left')->join('tax_rates', 'purchase_items.tax_rate_id = tax_rates.id', 'left')->join('own_companies', 'purchases.own_company = own_companies.id', 'left')->join('products', 'products.id = purchase_items.product_id', 'left')->join('brands', 'brands.id = products.brand', 'left');
                echo $this->datatables->generate();
                // echo $this->db->last_query();

            } else if ($report_type == "2") {
                if ($_SESSION['user_id'] === 51 || $_SESSION['user_id'] === '51') {
                    //  $qry = " WHERE sma_sales.supplier_id = '5' AND sma_sales.date >= '" . $start_date . "' AND sma_sales.date <= '" . $end_date . "'";
                    $qry = "SELECT sma_own_companies.companyname, sma_companies.cf1, sma_sales.reference_no, sma_sales.date, sma_sales.po_number, sma_companies.name, sma_sale_items.product_id, sma_sale_items.product_name, sma_products.hsn_code, sma_sale_items.quantity, sma_sale_items.product_unit_code, sma_sale_items.net_unit_price, IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship))) AS 'sale_price', ( IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship)))*sma_sale_items.quantity ) AS 'value_excl_tax', sma_sale_items.tax, sma_sale_items.item_tax, sma_sale_items.further_tax, sma_sale_items.fed_tax, ( IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship)))*sma_sale_items.quantity + (sma_sale_items.tax + sma_sale_items.further_tax + sma_sale_items.fed_tax) ) AS total_tax, sma_sale_items.discount_one, sma_sale_items.discount_two, sma_sale_items.discount_three, sma_sale_items.discount, sma_sale_items.subtotal, IF(sma_tax_rates.type = '1', 'GST', IF(sma_tax_rates.code = 'exp', 'Exempted', '3rd Schdule')) AS 'remarks', IF(sma_tax_rates.type = '1', 0, IF(sma_tax_rates.code = 'exp', 0, sma_sale_items.mrp / 1.17)) AS 'mrp_excl_tax', IF(sma_tax_rates.type = '1', 0, IF(sma_tax_rates.code = 'exp', 0, ( sma_sale_items.mrp / 1.17 ) *sma_sale_items.quantity)) AS 'value_third_sch', sma_sale_items.mrp, sma_sale_items.expiry, sma_sale_items.batch, sma_brands.name AS 'brand', sma_sale_items.warehouse_id AS 'warehouse_id', sma_products.carton_size, sma_products.company_code FROM sma_sales LEFT JOIN sma_sale_items ON sma_sales.id = sma_sale_items.sale_id LEFT JOIN sma_companies ON sma_companies.id = sma_sales.customer_id LEFT JOIN sma_tax_rates ON sma_sale_items.tax_rate_id = sma_tax_rates.id LEFT JOIN sma_own_companies ON sma_sales.own_company = sma_own_companies.id LEFT JOIN sma_products ON sma_products.id = sma_sale_items.product_id LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand WHERE sma_sales.supplier_id = '5' AND sma_sales.date >= ' $start_date' AND sma_sales.date <= '$end_date'";

                    // echo $qry;
                    // die;
                } else {

                    $qry = "SELECT sma_own_companies.companyname, (SELECT `name` AS supplier_name FROM `sma_companies` WHERE id = sma_sales.supplier_id) AS supplier_name, sma_companies.cf1,sma_sales.reference_no,sma_sales.date,sma_sales.po_number,sma_companies.name,sma_sale_items.product_id,sma_sale_items.product_name,sma_products.hsn_code,sma_sale_items.quantity,sma_sale_items.product_unit_code,sma_sale_items.net_unit_price,IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship))) AS 'sale_price',(IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship)))*sma_sale_items.quantity) as 'value_excl_tax',sma_sale_items.tax,sma_sale_items.item_tax,sma_sale_items.further_tax,sma_sale_items.fed_tax,(IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship)))*sma_sale_items.quantity + (sma_sale_items.tax + sma_sale_items.further_tax + sma_sale_items.fed_tax))  AS total_tax,sma_sale_items.discount_one,sma_sale_items.discount_two,sma_sale_items.discount_three,sma_sale_items.discount,sma_sale_items.subtotal,IF(sma_tax_rates.type = '1','GST',IF(sma_tax_rates.code = 'exp','Exempted','3rd Schdule')) AS 'remarks',IF(sma_tax_rates.type = '1',0,IF(sma_tax_rates.code = 'exp',0,sma_sale_items.mrp/1.17)) AS 'mrp_excl_tax',IF(sma_tax_rates.type = '1',0,IF(sma_tax_rates.code = 'exp',0,(sma_sale_items.mrp/1.17)*sma_sale_items.quantity)) AS 'value_third_sch',sma_sale_items.mrp,sma_sale_items.expiry,sma_sale_items.batch,sma_brands.name AS 'brand', sma_sale_items.warehouse_id AS 'warehouse_id', sma_products.carton_size,sma_products.company_code FROM sma_sales LEFT JOIN sma_sale_items ON sma_sales.id = sma_sale_items.sale_id  LEFT JOIN sma_companies ON sma_companies.id = sma_sales.customer_id  LEFT JOIN sma_tax_rates ON sma_sale_items.tax_rate_id = sma_tax_rates.id  LEFT JOIN sma_own_companies ON sma_sales.own_company = sma_own_companies.id  LEFT JOIN sma_products ON sma_products.id = sma_sale_items.product_id  LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand";
                    if (($start_date !== '' && $start_date !== '--') || ($end_date !== '' && $end_date !== '--') || ($warehouse !== '')) {
                        $qry .= " WHERE 1=1 ";
                    }

                    if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
                        $qry .= " AND sma_sales.date >= '" . $start_date . "' and sma_sales.date <= '" . $end_date . " 23:59:59'";
                    }

                    if (($warehouse !== '')) {
                        // $qry .=  " where sma_sales.date BETWEEN '".$start_date."' and '".$end_date."'";
                        $qry .= " AND sma_sale_items.warehouse_id >= '" . $warehouse . "'";
                    }
                }
                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }
                if ($pdf || $xls) {

                    if (!empty($data)) {

                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                        $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('Own Company'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('Customer NTN'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('Invoice No'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('Date'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('P.O Number'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('Customer Name'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('Product ID'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('Product Name'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('HSN Code'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('Qty Order'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('UOM'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('Price excluding Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('Selling Price'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('Value Excluding Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('Item Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('Q1', lang('Further Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('R1', lang('FED Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('S1', lang('Sales Value'));
                        $this->excel->getActiveSheet()->SetCellValue('T1', lang('Total including all taxes'));
                        $this->excel->getActiveSheet()->SetCellValue('U1', lang('Sales Incentive'));
                        $this->excel->getActiveSheet()->SetCellValue('V1', lang('Trade Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('W1', lang('Consumer Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('X1', lang('Total Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('Y1', lang('Subtotal'));
                        $this->excel->getActiveSheet()->SetCellValue('Z1', lang('Remarks'));
                        $this->excel->getActiveSheet()->SetCellValue('AA1', lang('M.R.P Excluding Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('AB1', lang('M.R.P Third Schedule'));
                        $this->excel->getActiveSheet()->SetCellValue('AC1', lang('Mrp'));
                        $this->excel->getActiveSheet()->SetCellValue('AD1', lang('Expiry Date'));
                        $this->excel->getActiveSheet()->SetCellValue('AE1', lang('Batch'));
                        $this->excel->getActiveSheet()->SetCellValue('AF1', lang('Brand'));
                        $this->excel->getActiveSheet()->SetCellValue('AG1', lang('Carton Size'));
                        $this->excel->getActiveSheet()->SetCellValue('AH1', lang('company_code'));
                        $this->excel->getActiveSheet()->SetCellValue('AI1', lang('warehouse_id'));
                        $this->excel->getActiveSheet()->SetCellValue('AJ1', lang('Supplier Name'));




                        $row = 2;

                        // $own_company = 0;
                        // $customer_ntn = 0;
                        // $invoice_no = 0;
                        // $date = 0;
                        // $po_number = 0;
                        // $customer_name = 0;
                        // $product_id = 0;
                        // $product_name = 0;
                        // $hsn_code = 0;
                        // $qty_order = 0;
                        // $uom = 0;
                        // $price_ex_tax = 0;
                        // $selling_price = 0;
                        // $value_excl_tax = 0;
                        // $tax = 0;
                        // $item_tax = 0;
                        // $frther_tax = 0;
                        // $fed_tax = 0;
                        // $total_include_all_tax = 0;
                        // $sale_inc = 0;
                        // $trade_dis = 0;
                        // $consumer_dis = 0;
                        // $total_dis = 0;
                        // $subtotal = 0;
                        // $remarks = 0;
                        // $mrp_excl_tax = 0;
                        // $value_third_sch = 0;
                        // $mrp = 0;
                        // $expiry_date = 0;
                        // $batch = 0;
                        // $brand = 0;

                        foreach ($data as $data_row) {

                            $source = $data_row->date;
                            $date   = new DateTime($source);

                            $discount_two   = ($data_row->value_excl_tax * $data_row->discount_two) / 100;
                            $discount_three = ($data_row->value_excl_tax * $data_row->discount_three) / 100;

                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->companyname);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->cf1);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $date->format('d-M-Y'));
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->po_number);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->name);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->product_id);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->product_name);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->hsn_code);
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->quantity);
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->product_unit_code);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->net_unit_price);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->sale_price); // Correct karna hy
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->value_excl_tax);
                            $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->tax);
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->item_tax);
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->further_tax);
                            $this->excel->getActiveSheet()->SetCellValue('R' . $row, $data_row->fed_tax);
                            $totalsalevalue = $data_row->value_excl_tax+$data_row->item_tax+$data_row->further_tax+$data_row->fed_tax;
                            $this->excel->getActiveSheet()->SetCellValue('S' . $row, $totalsalevalue); 
                            $this->excel->getActiveSheet()->SetCellValue('T' . $row, $data_row->fed_tax); // Correct karna hy
                            $this->excel->getActiveSheet()->SetCellValue('U' . $row, $data_row->discount_one);
                            $this->excel->getActiveSheet()->SetCellValue('V' . $row, $discount_two);
                            $this->excel->getActiveSheet()->SetCellValue('W' . $row, $discount_three);
                            $this->excel->getActiveSheet()->SetCellValue('X' . $row, $data_row->discount);
                            $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $data_row->subtotal);
                            $this->excel->getActiveSheet()->SetCellValue('Z' . $row, $data_row->remarks); // Correct karna hy
                            $this->excel->getActiveSheet()->SetCellValue('AA' . $row, $data_row->mrp_excl_tax);
                            $this->excel->getActiveSheet()->SetCellValue('AB' . $row, $data_row->value_third_sch);
                            $this->excel->getActiveSheet()->SetCellValue('AC' . $row, $data_row->mrp);
                            $this->excel->getActiveSheet()->SetCellValue('AD' . $row, $data_row->expiry);
                            $this->excel->getActiveSheet()->SetCellValue('AE' . $row, $data_row->batch);
                            $this->excel->getActiveSheet()->SetCellValue('AF' . $row, $data_row->brand);
                            $this->excel->getActiveSheet()->SetCellValue('AG' . $row, $data_row->carton_size);
                            $this->excel->getActiveSheet()->SetCellValue('AH' . $row, $data_row->company_code);
                            $this->excel->getActiveSheet()->SetCellValue('AI' . $row, $data_row->warehouse_id);
                            $this->excel->getActiveSheet()->SetCellValue('AJ' . $row, $data_row->supplier_name);



                            $row++;
                        }

                        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AB')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AD')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AE')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AG')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AH')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AI')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('AJ')->setWidth(25);

                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                        $filename = 'sales_report';
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                    $this->session->set_flashdata('error', lang('nothing_found'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                // $this->db->save_queries = TRUE;
                $this->load->library('datatables');
                $this->datatables->select("
                        {$this->db->dbprefix('own_companies')}.companyname, 
                        {$this->db->dbprefix('products')}.hsn_code, 
                        {$this->db->dbprefix('companies')}.company,
                        {$this->db->dbprefix('sales')}.date,
                        {$this->db->dbprefix('sales')}.reference_no,
                        {$this->db->dbprefix('sale_items')}.product_id,
                        {$this->db->dbprefix('sale_items')}.product_name,
                        {$this->db->dbprefix('sale_items')}.quantity,
                        {$this->db->dbprefix('sale_items')}.expiry,
                        {$this->db->dbprefix('sale_items')}.batch,
                        {$this->db->dbprefix('sale_items')}.mrp,
                        {$this->db->dbprefix('sale_items')}.discount,
                        {$this->db->dbprefix('sale_items')}.subtotal,
                        {$this->db->dbprefix('sale_items')}.discount_one,
                        {$this->db->dbprefix('sale_items')}.discount_two,
                        {$this->db->dbprefix('sale_items')}.discount_three,
                        {$this->db->dbprefix('sale_items')}.further_tax,
                        {$this->db->dbprefix('sale_items')}.fed_tax,
                        {$this->db->dbprefix('brands')}.name,
                        {$this->db->dbprefix('products')}.carton_size, 
                ")->from('sales')->join('sale_items', 'sales.id = sale_items.sale_id', 'left')->join('companies', 'companies.id = sales.customer_id', 'left')->join('tax_rates', 'sale_items.tax_rate_id = tax_rates.id', 'left')->join('own_companies', 'sales.own_company = own_companies.id', 'left')->join('products', 'products.id = sale_items.product_id', 'left')->join('brands', 'brands.id = products.brand', 'left');
                if ($_SESSION['user_id'] === 51 || $_SESSION['user_id'] === '51') {
                    $this->datatables->where('sales.supplier_id = "5"');
                    $this->datatables->where("sma_sales.date >= ' $start_date' AND sma_sales.date <= '$end_date'");
                    ///$this->datatables->where('sales.supplier_id = "5"');
                }



                echo $this->datatables->generate();
            } else if ($report_type == "3") {

                $qry = "SELECT * FROM `sma_products`";

                if (($start_date !== '' && $start_date !== '--') || ($end_date !== '' && $end_date !== '--') || ($warehouse !== '')) {
                    $qry .= " WHERE 1=1 ";
                }

                if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
                    // $qry .=  " where sma_sales.date BETWEEN '".$start_date."' and '".$end_date."'";
                    $qry .= " AND sma_purchases.date >= '" . $start_date . "' and sma_purchases.date <= '" . $end_date . " 23:59:59'";
                }

                if (($warehouse !== '')) {
                    // $qry .=  " where sma_sales.date BETWEEN '".$start_date."' and '".$end_date."'";
                    $qry .= " AND sma_purchases.warehouse_id >= '" . $warehouse . "'";
                }


                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }


                // echo "<pre>";
                // print_r($data);
                // echo "</pre>";

                if ($pdf || $xls) {


                    if (!empty($data)) {

                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                        $this->excel->getActiveSheet()->setTitle(lang('products_report'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('Name'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('Price excluding Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('Consignment'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('dropship'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('crossdock'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('mrp'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('discount_mrp'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('quantity'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('hsn_code'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('discount_one'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('discount_two'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('discount_three'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('fed_tax'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('carton_size'));
                        $row = 2;
                        foreach ($data as $data_row) {

                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->name);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->cost);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->price);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->dropship);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->crossdock);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->mrp);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->discount_mrp);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->quantity);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->hsn_code);
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->discount_one);
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->discount_two);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->discount_three);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->fed_tax);
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->carton_size);


                            $row++;
                        }

                        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(25);

                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                        $filename = 'products_report';
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                    $this->session->set_flashdata('error', lang('nothing_found'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else if ($report_type == "6") {
                $qry = "
                    SELECT 
                        sma_brands.name as brand_name, 
                        sma_warehouses.name as warehousename,
                        IF(
                            `sma_purchases`.`date` IS null , 
                            '2000-12-12' , 
                            `sma_purchases`.`date`
                        ) as 'purchase_date' , 
                        `sma_purchase_items`.`id`, 
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
                            sma_tax_rates.type = '1', 
                            'GST', IF( 
                                sma_tax_rates.code = 'exp', 
                                'Exempted', '3rd Schdule' 
                            )
                        ) AS 'Remarks', 
                        sma_tax_rates.rate, 
                        IF(
                            sma_tax_rates.type = '1', 
                            (sma_purchase_items.net_unit_cost * sma_tax_rates.rate) / 100, 
                            IF(
                                sma_tax_rates.code = 'exp', 
                                0, 
                                sma_tax_rates.rate 
                            )
                        ) AS 'tax_rate_value', 
                        sma_products.company_code 
                    FROM `sma_purchase_items` 
                    LEFT JOIN sma_products ON sma_products.id = sma_purchase_items.product_id 
                    LEFT JOIN sma_tax_rates ON sma_purchase_items.tax_rate_id = sma_tax_rates.id 
                    LEFT JOIN sma_companies ON sma_companies.id = sma_products.supplier1 
                    LEFT JOIN sma_purchases ON sma_purchases.id = sma_purchase_items.purchase_id 
                    LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand
                    LEFT JOIN sma_warehouses ON sma_warehouses.id = sma_purchase_items.warehouse_id
                    WHERE 
                        `sma_products`.`status` = '1' 
                ";

                if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
                    $qry .= " AND sma_purchases.date >= '" . $start_date . "' and sma_purchases.date <= '" . $end_date . " 23:59:59'";
                }

                if (($warehouse !== '')) {
                    $qry .= " AND sma_purchases.warehouse_id >= '" . $warehouse . "'";
                }
                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }

                $qry2 = '
                    SELECT 
                        sma_brands.name as brand_name, 
                        sma_warehouses.name as warehousename,
                        IF(
                            `sma_transfers`.`date` IS null , 
                            "2000-12-12" , 
                            `sma_transfers`.`date`
                        ) as "purchase_date" , 
                        `sma_purchase_items`.`id`, 
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
                            "GST", IF( 
                                sma_tax_rates.code = "exp", 
                                "Exempted", "3rd Schdule" 
                            )
                        ) AS "Remarks", 
                        sma_tax_rates.rate, 
                        IF(
                            sma_tax_rates.type = "1", 
                            (sma_purchase_items.net_unit_cost * sma_tax_rates.rate) / 100, 
                            IF(
                                sma_tax_rates.code = "exp", 
                                0, 
                                sma_tax_rates.rate 
                            )
                        ) AS "tax_rate_value", 
                        sma_products.company_code
                    FROM `sma_purchase_items` 
                    LEFT JOIN sma_products ON sma_products.id = sma_purchase_items.product_id 
                    LEFT JOIN sma_tax_rates ON sma_purchase_items.tax_rate_id = sma_tax_rates.id 
                    LEFT JOIN sma_companies ON sma_companies.id = sma_products.supplier1 
                    LEFT JOIN sma_transfers ON sma_transfers.id = sma_purchase_items.transfer_id 
                    LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand
                    LEFT JOIN sma_warehouses ON sma_warehouses.id = sma_purchase_items.warehouse_id
                    WHERE 
                        sma_purchase_items.transfer_id != ""
                ';
                if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
                    $qry2 .= " AND sma_transfers.date >= '" . $start_date . "' and sma_transfers.date <= '" . $end_date . " 23:59:59'";
                }
                if (($warehouse != '')) {
                    $qry2 .= ' AND sma_transfers.to_warehouse_id == "' . $warehouse . '"';
                }
                $q2 = $this->db->query($qry2);
                if ($q2->num_rows() > 0) {
                    foreach (($q2->result()) as $row2) {
                        $data[] = $row2;
                    }
                } 
                
                if ($pdf || $xls) {

                    if (!empty($data)) {

                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                        $this->excel->getActiveSheet()->setTitle(lang('batch_wise_reporting'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('Purchase Date'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('Product id'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('Name'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('Price excluding Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('Consignment'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('dropship'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('crossdock'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('mrp'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('Tax Rate Value'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('quantity balance'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('expiry'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('batch'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('discount_one'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('discount_two'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('discount_three'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('fed_tax'));
                        $this->excel->getActiveSheet()->SetCellValue('Q1', lang('Company'));
                        $this->excel->getActiveSheet()->SetCellValue('R1', lang('Tax Type'));
                        $this->excel->getActiveSheet()->SetCellValue('S1', lang('Warehouse ID'));
                        $this->excel->getActiveSheet()->SetCellValue('T1', lang('Warehouse Name'));
                        $this->excel->getActiveSheet()->SetCellValue('U1', lang('Carton Size'));
                        $this->excel->getActiveSheet()->SetCellValue('V1', lang('company_code'));
                        $this->excel->getActiveSheet()->SetCellValue('W1', lang('Brand'));



                        $row = 2;
                        foreach ($data as $data_row) {


                            $source = $data_row->purchase_date;
                            $date   = new DateTime($source);

                            // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->purchase_date);

                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->product_id);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->product_name);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->net_unit_cost);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->price);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->dropship);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->crossdock);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->mrp);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->tax_rate_value);
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->quantity_balance);
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->expiry);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->batch);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->discount_one);
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->discount_two);
                            $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->discount_three);
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->fed_tax);
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->company);
                            $this->excel->getActiveSheet()->SetCellValue('R' . $row, $data_row->Remarks);
                            $this->excel->getActiveSheet()->SetCellValue('S' . $row, $data_row->warehouse_id);
                            $this->excel->getActiveSheet()->SetCellValue('T' . $row, $data_row->warehousename);
                            $this->excel->getActiveSheet()->SetCellValue('U' . $row, $data_row->carton_size);
                            $this->excel->getActiveSheet()->SetCellValue('V' . $row, $data_row->company_code);
                            $this->excel->getActiveSheet()->SetCellValue('W' . $row, $data_row->brand_name);



                            $row++;
                        }

                        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(25);

                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                        $filename = 'batch_wise_reporting';
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                    $this->session->set_flashdata('error', lang('nothing_found'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }



                // $this->db->save_queries = TRUE;
                $this->load->library('datatables');
                $this->datatables->select("product_name, net_unit_cost, price, dropship, crossdock, mrp, quantity_balance, expiry, batch, discount_one, discount_two, discount_three, fed_tax, further_tax, warehouse_id ")->from('purchase_items');
                $this->datatables->where('`sma_purchase_items`.`quantity_balance` != 0');

                if ($warehouse != 0) {
                    $this->datatables->where('warehouse_id =', $warehouse);
                }

                echo $this->datatables->generate();
            } else if ($report_type == "7") {
                $qry = "SELECT sma_own_companies.companyname,sma_companies.gst_no,sma_companies.cf1,sma_sales.reference_no,sma_sales.date,sma_sales.po_number,sma_companies.name,sma_sale_items.product_id,sma_sale_items.product_name,sma_products.hsn_code,sma_sale_items.quantity,sma_sale_items.product_unit_code,sma_sale_items.net_unit_price,IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship))) AS 'sale_price',(IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship)))*sma_sale_items.quantity) as 'value_excl_tax',sma_sale_items.tax,sma_sale_items.item_tax,sma_sale_items.further_tax,sma_sale_items.fed_tax,(IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship)))*sma_sale_items.quantity + (sma_sale_items.tax + sma_sale_items.further_tax + sma_sale_items.fed_tax))  AS total_tax,sma_sale_items.discount_one,sma_sale_items.discount_two,sma_sale_items.discount_three,sma_sale_items.discount,sma_sale_items.subtotal,IF(sma_tax_rates.type = '1','GST',IF(sma_tax_rates.code = 'exp','Exempted','3rd Schdule')) AS 'remarks',IF(sma_tax_rates.type = '1',0,IF(sma_tax_rates.code = 'exp',0,sma_sale_items.mrp/1.17)) AS 'mrp_excl_tax',IF(sma_tax_rates.type = '1',0,IF(sma_tax_rates.code = 'exp',0,(sma_sale_items.mrp/1.17)*sma_sale_items.quantity)) AS 'value_third_sch',sma_sale_items.mrp,sma_sale_items.expiry,sma_sale_items.batch,sma_brands.name AS 'brand', sma_sale_items.warehouse_id AS 'warehouse_id', sma_products.carton_size,sma_products.company_code FROM sma_sales LEFT JOIN sma_sale_items ON sma_sales.id = sma_sale_items.sale_id  LEFT JOIN sma_companies ON sma_companies.id = sma_sales.customer_id  LEFT JOIN sma_tax_rates ON sma_sale_items.tax_rate_id = sma_tax_rates.id  LEFT JOIN sma_own_companies ON sma_sales.own_company = sma_own_companies.id  LEFT JOIN sma_products ON sma_products.id = sma_sale_items.product_id  LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand where sma_sales.date >= '" . $start_date . "' and sma_sales.date <= '" . $end_date . "' AND sma_companies.sales_type = 'consignment'";

                // echo $qry;


                // die;

                // if (($start_date !== '' && $start_date !== '--') || ($end_date !== '' && $end_date !== '--') || ($warehouse !== '')) {
                //     $qry .= " WHERE 1=1 ";
                // }

                // if (($start_date !== '' && $start_date !== '--') && ($end_date !== '' && $end_date !== '--')) {
                //     // $qry .=  " where sma_sales.date BETWEEN '".$start_date."' and '".$end_date."'";
                //     $qry .= " AND sma_sales.date >= '" . $start_date . "' and sma_sales.date <= '" . $end_date . "' AND sma_companies.sales_type = 'consignment' ";
                // }

                // if (($warehouse !== '')) {
                //     // $qry .=  " where sma_sales.date BETWEEN '".$start_date."' and '".$end_date."'";
                //     $qry .= " AND sma_sale_items.warehouse_id >= '" . $warehouse . "' AND sma_companies.sales_type = 'consignment' ";
                // }

                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }

                if ($pdf || $xls) {

                    if (!empty($data)) {

                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                        $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('Sr.'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('Buyer NTN'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('Buyer CNIC'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('Buyer Name'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('Buyer Type'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('Sale Origination Province of Supplier'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('Document Type'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('Document Number'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('Document Date'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('Sale Type'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('Rate'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('Description'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('Quantity'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('UOM'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('Value of Sales Ex. Sales Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('Fixed/Notified value or Retail Price'));
                        $this->excel->getActiveSheet()->SetCellValue('Q1', lang('Sales Tax / FED in ST Mode'));
                        $this->excel->getActiveSheet()->SetCellValue('R1', lang('Extra Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('S1', lang('ST Withheld at Source'));
                        $this->excel->getActiveSheet()->SetCellValue('T1', lang('SRO No. / Schedule No.'));
                        $this->excel->getActiveSheet()->SetCellValue('U1', lang('Item Sr. No.'));
                        $this->excel->getActiveSheet()->SetCellValue('V1', lang('Further Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('W1', lang('Total Values Of Sales'));
                        $this->excel->getActiveSheet()->SetCellValue('X1', lang('Test Invoice #'));

                        $row = 2;
                        $sr = 1;

                        $alphabet = ['A', 'B', 'C', 'D', 'E', 'F'];
                        $alphabet_count = 0;

                        foreach ($data as $data_row) {

                            $source = $data_row->date;
                            $date   = new DateTime($source);

                            $discount_two   = ($data_row->value_excl_tax * $data_row->discount_two) / 100;
                            $discount_three = ($data_row->value_excl_tax * $data_row->discount_three) / 100;

                            $buyer_type = "";
                            if (empty($data_row->gst_no)) {
                                $buyer_type = "Unregistered";
                            } else {
                                $buyer_type = "Registered";
                            }

                            // Calculations
                            if ($sr == 1) {
                                $new_invoice_no = "";
                                $new_hsn_code = "";
                            }
                            $old_invoice_no = $data_row->reference_no;
                            $old_hsn_code = $data_row->hsn_code;

                            $test_var = "";

                            if (($data_row->remarks === '3rd Schdule') && ($old_invoice_no === $new_invoice_no) &&  ($old_hsn_code === $new_hsn_code)) {
                                //if($old_invoice_no === $new_invoice_no){
                                $test_var = $old_invoice_no . $alphabet[$alphabet_count - 1];
                            } else if (($data_row->remarks === '3rd Schdule') && ($old_invoice_no === $new_invoice_no) && ($old_hsn_code != $new_hsn_code)) {
                                $test_var = $old_invoice_no . $alphabet[$alphabet_count - 1];
                                $alphabet_count++;
                            } else if (($data_row->remarks === 'GST') && ($old_invoice_no === $new_invoice_no) && ($old_hsn_code === $new_hsn_code)) {
                                //$test_var = 'Invoice #' . $old_invoice_no . $alphabet[$alphabet_count - 1] . '  =  HSN Code#' . $old_hsn_code;
                                $test_var = $old_invoice_no . $alphabet[$alphabet_count - 1];
                                $alphabet_count++;
                            } else if (($data_row->remarks === 'Exempted') && ($old_invoice_no === $new_invoice_no) && ($old_hsn_code != $new_hsn_code)) {
                                //$test_var = 'Invoice #' . $old_invoice_no . $alphabet[$alphabet_count - 1] . '  =  HSN Code#' . $old_hsn_code;
                                $test_var = $old_invoice_no . $alphabet[$alphabet_count - 1];
                                $alphabet_count++;
                            } else {
                                $test_var = $old_invoice_no . $alphabet[$alphabet_count - 1];
                                $alphabet_count = -1;
                            }

                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $sr);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->cf1);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, " "); // Blank
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->name);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $buyer_type);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, ' ');
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $test_var);
                            //$this->excel->getActiveSheet()->SetCellValue('G' . $row, 'Sl');
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $date->format('d-M-Y'));
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->remarks);
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->tax);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->hsn_code);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->quantity);
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->product_unit_code);
                            $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->value_excl_tax);
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->value_third_sch);
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->item_tax);
                            $this->excel->getActiveSheet()->SetCellValue('R' . $row, " "); // Blank
                            $this->excel->getActiveSheet()->SetCellValue('S' . $row, " ");  // Blank
                            $this->excel->getActiveSheet()->SetCellValue('T' . $row, " "); // Blank
                            $this->excel->getActiveSheet()->SetCellValue('U' . $row, " "); // Blank
                            $this->excel->getActiveSheet()->SetCellValue('V' . $row, $data_row->further_tax);
                            $this->excel->getActiveSheet()->SetCellValue('W' . $row, " "); // Blank
                            $this->excel->getActiveSheet()->SetCellValue('X' . $row, $test_var);
                            $row++;
                            $sr++;
                            $new_invoice_no = $data_row->reference_no;
                            $new_hsn_code = $data_row->hsn_code;
                        }

                        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(70);
                        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(50);
                        $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(50);
                        $this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(50);
                        $this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(50);
                        $this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(50);
                        $this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(50);
                        $this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(50);

                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                        $filename = 'sales_report';
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                    $this->session->set_flashdata('error', lang('nothing_found'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }


                $this->load->library('datatables');
                $this->datatables->select("
                        {$this->db->dbprefix('own_companies')}.companyname, 
                        {$this->db->dbprefix('products')}.hsn_code, 
                        {$this->db->dbprefix('companies')}.company,
                        {$this->db->dbprefix('sales')}.date,
                        {$this->db->dbprefix('sales')}.reference_no,
                        {$this->db->dbprefix('sale_items')}.product_id,
                        {$this->db->dbprefix('sale_items')}.product_name,
                        {$this->db->dbprefix('sale_items')}.quantity,
                        {$this->db->dbprefix('sale_items')}.expiry,
                        {$this->db->dbprefix('sale_items')}.batch,
                        {$this->db->dbprefix('sale_items')}.mrp,
                        {$this->db->dbprefix('sale_items')}.discount,
                        {$this->db->dbprefix('sale_items')}.subtotal,
                        {$this->db->dbprefix('sale_items')}.discount_one,
                        {$this->db->dbprefix('sale_items')}.discount_two,
                        {$this->db->dbprefix('sale_items')}.discount_three,
                        {$this->db->dbprefix('sale_items')}.further_tax,
                        {$this->db->dbprefix('sale_items')}.fed_tax,
                        {$this->db->dbprefix('brands')}.name,
                        {$this->db->dbprefix('products')}.carton_size, 
                ")->from('sales')->join('sale_items', 'sales.id = sale_items.sale_id', 'left')->join('companies', 'companies.id = sales.customer_id', 'left')->join('tax_rates', 'sale_items.tax_rate_id = tax_rates.id', 'left')->join('own_companies', 'sales.own_company = own_companies.id', 'left')->join('products', 'products.id = sale_items.product_id', 'left')->join('brands', 'brands.id = products.brand', 'left');


                echo $this->datatables->generate();
            } else {
                $query = "purchas_payment_reporting";
            }
        }
    }
    function gettranferdetails($warehouse,$name,$batch){
        $qry2 = '
            SELECT 
                sma_purchase_items.id,
                sma_purchase_items.purchase_id,
                sma_purchase_items.transfer_id, 
                sma_purchase_items.product_name, 
                sma_purchase_items.batch 
            FROM `sma_purchase_items` 
            LEFT JOIN sma_products ON sma_products.id = sma_purchase_items.product_id 
            LEFT JOIN sma_purchases ON sma_purchases.id = sma_purchase_items.purchase_id 
            WHERE 
                `sma_products`.`status` = "1" 
                AND sma_purchase_items.batch = "'.$batch.'"
        ';
        if (($warehouse != '')) {
            $qry2 .= ' AND sma_purchases.warehouse_id >= "' . $warehouse . '"';
        }
        $q = $this->db->query($qry2);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
        } else {
            $data = NULL;
        }
        // $data = NULL;
        return $data;
    }
    function getListReportingSupplier($xls = Null){

        $xls = ($this->uri->slash_segment(5) == 'xls/') ? $this->uri->slash_segment(5) : "";

        $this->sma->checkPermissions('products', TRUE);

        $report_type = $this->input->get('report_type') ? $this->input->get('report_type') : NULL;
        $supplier    = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        // $biller = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        // $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        // $category = $this->input->get('category') ? $this->input->get('category') : NULL;
        // $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : NULL;
        // $brand = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        // $warehouse = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        // $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        // $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;


        if ($report_type) {
            if ($report_type == "1") {
                $qry = "SELECT `sma_sales`.`date`, `sma_companies`.`name`, `sma_own_companies`.`companyname`, `sma_sales`.`reference_no`, `sma_sales`.`po_number`, `sma_sales`.`customer`, `sma_sales`.`biller`, `sma_sales`.`product_discount`, `sma_sales`.`total_discount`, `sma_sales`.`order_discount`, `sma_sales`.`product_tax`, `sma_sales`.`order_tax`, `sma_sales`.`total_tax`, `sma_sales`.`grand_total`, `sma_sales`.`sale_status`, `sma_sales`.`payment_status` FROM `sma_sales` LEFT JOIN `sma_companies` ON `sma_companies`.`id`=`sma_sales`.`supplier_id` LEFT JOIN `sma_own_companies` ON `sma_own_companies`.`id`=`sma_sales`.`own_company`";

                if ($supplier != 0) {
                    $qry .= "WHERE `supplier_id` = $supplier";
                }

                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }
                if ($pdf || $xls) {
                    if (!empty($data)) {
                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                        $this->excel->getActiveSheet()->setTitle(lang('Supplier Reporting'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('Date'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('Supplier'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('Own Company'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('Reference No'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('PO Number'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('Customer'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('Biller'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('Product Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('Total Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('Order Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('Product Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('Order Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('Total Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('Grand Total'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('Sale Status'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('Payment Status'));
                        $row = 2;
                        foreach ($data as $data_row) {


                            $source = $data_row->date;
                            $date   = new DateTime($source);
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->companyname);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->po_number);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->customer);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->biller);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->product_discount);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_discount);
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->order_discount);
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->product_tax);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->order_tax);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->total_tax);
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->grand_total);
                            $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->sale_status);
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->payment_status);
                            $row++;
                        }

                        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(35);

                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                        $filename = 'Supplier Reporting';
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                    $this->session->set_flashdata('error', lang('nothing_found'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                // $this->db->save_queries = TRUE;

                $this->load->library('datatables');
                $this->datatables->select("
                            {$this->db->dbprefix('sales')}.date,
                            {$this->db->dbprefix('companies')}.name,
                            {$this->db->dbprefix('own_companies')}.companyname,
                            {$this->db->dbprefix('sales')}.reference_no, 
                            {$this->db->dbprefix('sales')}.po_number,
                            {$this->db->dbprefix('sales')}.customer,
                            {$this->db->dbprefix('sales')}.biller,
                            {$this->db->dbprefix('sales')}.product_discount,
                            {$this->db->dbprefix('sales')}.total_discount,
                            {$this->db->dbprefix('sales')}.order_discount,
                            {$this->db->dbprefix('sales')}.product_tax,
                            {$this->db->dbprefix('sales')}.order_tax,
                            {$this->db->dbprefix('sales')}.total_tax,
                            {$this->db->dbprefix('sales')}.grand_total,
                            {$this->db->dbprefix('sales')}.sale_status,
                            {$this->db->dbprefix('sales')}.payment_status 
                        ")->from('sales')->join('companies', 'companies.id=sales.supplier_id', 'left')->join('own_companies', 'own_companies.id=sales.own_company', 'left');

                if ($supplier != 0) {
                    $this->datatables->where('supplier_id =', $supplier);
                }
                echo $this->datatables->generate();
                // echo $this->db->last_query();


            }
            if ($report_type == "2") {
                $qry = "SELECT `sma_sales`.`date`, `sma_companies`.`name`, `sma_own_companies`.`companyname`, `sma_sales`.`reference_no`, `sma_sales`.`po_number`, `sma_sales`.`customer`, `sma_sales`.`biller`, `sma_sales`.`product_discount`, `sma_sales`.`total_discount`, `sma_sales`.`order_discount`, `sma_sales`.`product_tax`, `sma_sales`.`order_tax`, `sma_sales`.`total_tax`, `sma_sales`.`grand_total`, `sma_sales`.`sale_status`, `sma_sales`.`payment_status` FROM `sma_sales` LEFT JOIN `sma_companies` ON `sma_companies`.`id`=`sma_sales`.`supplier_id` LEFT JOIN `sma_own_companies` ON `sma_own_companies`.`id`=`sma_sales`.`own_company`";

                if ($supplier != 0) {
                    $qry .= "WHERE `supplier_id` = $supplier";
                }

                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }
                if ($pdf || $xls) {
                    if (!empty($data)) {
                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                        $this->excel->getActiveSheet()->setTitle(lang('Supplier Reporting'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('Date'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('Supplier'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('Own Company'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('Reference No'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('PO Number'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('Customer'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('Biller'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('Product Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('Total Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('Order Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('Product Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('Order Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('Total Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('Grand Total'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('Sale Status'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('Payment Status'));
                        $row = 2;
                        foreach ($data as $data_row) {


                            $source = $data_row->date;
                            $date   = new DateTime($source);
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->companyname);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->po_number);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->customer);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->biller);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->product_discount);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_discount);
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->order_discount);
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->product_tax);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->order_tax);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->total_tax);
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->grand_total);
                            $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->sale_status);
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->payment_status);
                            $row++;
                        }

                        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(35);

                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                        $filename = 'Supplier Reporting';
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                    $this->session->set_flashdata('error', lang('nothing_found'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                // $this->db->save_queries = TRUE;

                $this->load->library('datatables');
                $this->datatables->select("
                            {$this->db->dbprefix('sales')}.date,
                            {$this->db->dbprefix('companies')}.name,
                            {$this->db->dbprefix('own_companies')}.companyname,
                            {$this->db->dbprefix('sales')}.reference_no, 
                            {$this->db->dbprefix('sales')}.po_number,
                            {$this->db->dbprefix('sales')}.customer,
                            {$this->db->dbprefix('sales')}.biller,
                            {$this->db->dbprefix('sales')}.product_discount,
                            {$this->db->dbprefix('sales')}.total_discount,
                            {$this->db->dbprefix('sales')}.order_discount,
                            {$this->db->dbprefix('sales')}.product_tax,
                            {$this->db->dbprefix('sales')}.order_tax,
                            {$this->db->dbprefix('sales')}.total_tax,
                            {$this->db->dbprefix('sales')}.grand_total,
                            {$this->db->dbprefix('sales')}.sale_status,
                            {$this->db->dbprefix('sales')}.payment_status 
                        ")->from('sales')->join('companies', 'companies.id=sales.supplier_id', 'left')->join('own_companies', 'own_companies.id=sales.own_company', 'left');

                if ($supplier != 0) {
                    $this->datatables->where('supplier_id =', $supplier);
                }
                echo $this->datatables->generate();
                // echo $this->db->last_query();


            }
        }
    }
    function suppliers_reports(){

        $this->data['supplier'] = $this->site->GetAllSupplierList();
        $this->session->set_userdata('user_csrf', $value);
        $this->data['csrf'] = $this->session->userdata('user_csrf');

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('reports')
            )
        );
        $meta = array(
            'page_title' => lang('reports'),
            'bc' => $bc
        );
        $this->page_construct('reports/suppliers_reports', $meta, $this->data);
    }
    function getList($category_id = NULL){
        if ($rows = $this->reports_model->getList($category_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
        echo $data;
    }
    public function custom_best_sellers($warehouse_id = NULL){
        $this->session->set_userdata('user_csrf', $value);
        $this->data['csrf'] = $this->session->userdata('user_csrf');

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('best_sellers')
            )
        );
        $meta = array(
            'page_title' => lang('custom_best_sellers'),
            'bc' => $bc
        );
        $this->page_construct('reports/custom_best_sellers', $meta, $this->data);
    }
    function getListReportingBestSeller($xls = Null){

        $xls = ($this->uri->slash_segment(5) == 'xls/') ? $this->uri->slash_segment(5) : "";

        $this->sma->checkPermissions('products', TRUE);

        $report_type       = $this->input->get('report_type') ? $this->input->get('report_type') : NULL;
        $buyer_or_supplier = $this->input->get('buyer_or_supplier') ? $this->input->get('buyer_or_supplier') : NULL;
        $start_date        = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date          = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        if ($report_type) {
            // Brand Wise
            if ($report_type == "1") {
                // $qry = "SELECT `sma_sales`.`date`, `sma_companies`.`name`, `sma_own_companies`.`companyname`, `sma_sales`.`reference_no`, `sma_sales`.`po_number`, `sma_sales`.`customer`, `sma_sales`.`biller`, `sma_sales`.`product_discount`, `sma_sales`.`total_discount`, `sma_sales`.`order_discount`, `sma_sales`.`product_tax`, `sma_sales`.`order_tax`, `sma_sales`.`total_tax`, `sma_sales`.`grand_total`, `sma_sales`.`sale_status`, `sma_sales`.`payment_status` FROM `sma_sales` LEFT JOIN `sma_companies` ON `sma_companies`.`id`=`sma_sales`.`supplier_id` LEFT JOIN `sma_own_companies` ON `sma_own_companies`.`id`=`sma_sales`.`own_company`";

                // if($supplier != 0) {
                //     $qry .= "WHERE `supplier_id` = $supplier";
                // }     

                // $q = $this->db->query($qry);
                // if ($q->num_rows() > 0) {
                //     foreach (($q->result()) as $row) {
                //         $data[] = $row;
                //     }
                // } else {
                //     $data = NULL;
                // }
                // if ($pdf || $xls) {
                //     if (!empty($data)) {
                //         $this->load->library('excel');
                //         $this->excel->setActiveSheetIndex(0);
                //         $this->excel->getActiveSheet()->setTitle(lang('Supplier Reporting'));
                //         $this->excel->getActiveSheet()->SetCellValue('A1', lang('Date'));
                //         $this->excel->getActiveSheet()->SetCellValue('B1', lang('Supplier'));
                //         $this->excel->getActiveSheet()->SetCellValue('C1', lang('Own Company'));
                //         $this->excel->getActiveSheet()->SetCellValue('D1', lang('Reference No'));
                //         $this->excel->getActiveSheet()->SetCellValue('E1', lang('PO Number'));
                //         $this->excel->getActiveSheet()->SetCellValue('F1', lang('Customer'));
                //         $this->excel->getActiveSheet()->SetCellValue('G1', lang('Biller'));
                //         $this->excel->getActiveSheet()->SetCellValue('H1', lang('Product Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('I1', lang('Total Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('J1', lang('Order Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('K1', lang('Product Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('L1', lang('Order Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('M1', lang('Total Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('N1', lang('Grand Total'));
                //         $this->excel->getActiveSheet()->SetCellValue('O1', lang('Sale Status'));
                //         $this->excel->getActiveSheet()->SetCellValue('P1', lang('Payment Status'));
                //         $row = 2;
                //         foreach ($data as $data_row) {


                //             $source = $data_row->date;
                //             $date = new DateTime($source);



                //             $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                //             $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                //             $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->companyname);
                //             $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->reference_no);
                //             $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->po_number);
                //             $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->customer);
                //             $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->biller);
                //             $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->product_discount);
                //             $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_discount);
                //             $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->order_discount);
                //             $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->product_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->order_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->total_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->grand_total);
                //             $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->sale_status);
                //             $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->payment_status);
                //             $row++;
                //         }

                //         $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(35);

                //         $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                //         $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                //         $filename = 'Supplier Reporting';
                //         $this->load->helper('excel');
                //         create_excel($this->excel, $filename);
                //     }
                //     $this->session->set_flashdata('error', lang('nothing_found'));
                //     redirect($_SERVER["HTTP_REFERER"]);

                // }

                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');

                // $this->db->save_queries = TRUE;

                $this->load->library('datatables');
                $this->datatables->select("{$this->db->dbprefix('sale_items')}.product_name, sum({$this->db->dbprefix('sale_items')}.quantity)")->from('sales')->where('sma_products.brand = ', $buyer_or_supplier);

                if ($start_date != "") {
                    $this->datatables->where('date >=', $start_date);
                }
                if ($end_date != "") {
                    $this->datatables->where('date <=', $end_date);
                }

                $this->datatables->group_by("{$this->db->dbprefix('sale_items')}.product_name");
                $this->datatables->join('sale_items', 'sma_sale_items.sale_id = sma_sales.id', 'left');
                $this->datatables->join('sma_products', 'sma_sale_items.product_id = sma_products.id', 'left');

                echo $this->datatables->generate();
                // echo $this->db->last_query();


                // SELECT
                //     sma_products.name,
                //     sma_products.brand,
                //     sma_sale_items.product_name,
                //     SUM(sma_sale_items.quantity)
                // FROM
                //     `sma_sales`
                // LEFT JOIN `sma_sale_items` ON `sma_sale_items`.`sale_id` = `sma_sales`.`id`
                // LEFT JOIN `sma_products` ON `sma_sale_items`.`product_id` = `sma_products`.`id`
                // WHERE
                //     `sma_products`.`brand` = '30' AND `date` >= '0000-00-00 00:00:00' AND `date` <= '2019-12-30'
                // GROUP BY
                //     `sma_sale_items`.`product_name`




            }

            // Buyer Wise
            if ($report_type == "2") {
                // $qry = "SELECT sma_sales.id, sma_sales.biller_id, sma_sale_items.product_name, sum(sma_sale_items.quantity) FROM `sma_sales` LEFT JOIN sma_sale_items ON sma_sale_items.sale_id = sma_sales.id WHERE sma_sales.biller_id = ".$buyer_or_supplier." GROUP BY sma_sale_items.product_name ORDER BY `sma_sale_items`.`product_name` DESC";

                // $q = $this->db->query($qry);
                // if ($q->num_rows() > 0) {
                //     foreach (($q->result()) as $row) {
                //         $data[] = $row;
                //     }
                // } else {
                //     $data = NULL;
                // }
                // if ($pdf || $xls) {
                //     if (!empty($data)) {
                //         $this->load->library('excel');
                //         $this->excel->setActiveSheetIndex(0);
                //         $this->excel->getActiveSheet()->setTitle(lang('Supplier Reporting'));
                //         $this->excel->getActiveSheet()->SetCellValue('A1', lang('Date'));
                //         $this->excel->getActiveSheet()->SetCellValue('B1', lang('Supplier'));
                //         $this->excel->getActiveSheet()->SetCellValue('C1', lang('Own Company'));
                //         $this->excel->getActiveSheet()->SetCellValue('D1', lang('Reference No'));
                //         $this->excel->getActiveSheet()->SetCellValue('E1', lang('PO Number'));
                //         $this->excel->getActiveSheet()->SetCellValue('F1', lang('Customer'));
                //         $this->excel->getActiveSheet()->SetCellValue('G1', lang('Biller'));
                //         $this->excel->getActiveSheet()->SetCellValue('H1', lang('Product Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('I1', lang('Total Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('J1', lang('Order Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('K1', lang('Product Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('L1', lang('Order Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('M1', lang('Total Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('N1', lang('Grand Total'));
                //         $this->excel->getActiveSheet()->SetCellValue('O1', lang('Sale Status'));
                //         $this->excel->getActiveSheet()->SetCellValue('P1', lang('Payment Status'));
                //         $row = 2;
                //         foreach ($data as $data_row) {


                //             $source = $data_row->date;
                //             $date = new DateTime($source);



                //             $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                //             $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                //             $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->companyname);
                //             $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->reference_no);
                //             $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->po_number);
                //             $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->customer);
                //             $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->biller);
                //             $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->product_discount);
                //             $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_discount);
                //             $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->order_discount);
                //             $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->product_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->order_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->total_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->grand_total);
                //             $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->sale_status);
                //             $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->payment_status);
                //             $row++;
                //         }

                //         $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(35);

                //         $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                //         $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                //         $filename = 'Supplier Reporting';
                //         $this->load->helper('excel');
                //         create_excel($this->excel, $filename);
                //     }
                //     $this->session->set_flashdata('error', lang('nothing_found'));
                //     redirect($_SERVER["HTTP_REFERER"]);

                // }


                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');

                // $this->db->save_queries = TRUE;

                $this->load->library('datatables');
                $this->datatables->select("{$this->db->dbprefix('sale_items')}.product_name, sum({$this->db->dbprefix('sale_items')}.quantity)")->from('sales')->where('sma_sales.biller_id = ', $buyer_or_supplier);

                if ($start_date != "") {
                    $this->datatables->where('date >=', $start_date);
                }
                if ($end_date != "") {
                    $this->datatables->where('date <=', $end_date);
                }

                $this->datatables->group_by("{$this->db->dbprefix('sale_items')}.product_name");
                $this->datatables->join('sale_items', 'sma_sale_items.sale_id = sma_sales.id', 'left');

                echo $this->datatables->generate();
                // echo $this->db->last_query();

            }

            // Supplier Wise
            // if($report_type == "3") {


            //     // $buyer_or_supplier;

            //     // SELECT sma_sales.id, sma_sales.supplier_id, sma_sale_items.product_name, sma_sale_items.quantity FROM `sma_sales` LEFT JOIN sma_sale_items ON sma_sale_items.sale_id = sma_sales.id  ORDER BY `sma_sale_items`.`product_name` DESC WHERE sma_sales.supplier_id = $buyer_or_supplier;

            //     //     $qry = $this->db->select("product_name, product_code")->select_sum('quantity')
            //     // ->join('sales', 'sales.id = sale_items.sale_id', 'left')
            //     // ->where('date >=', $start_date)->where('date <=', $end_date)
            //     // ->group_by('product_name, product_code')->order_by('sum(quantity)', 'desc')->limit(10);




            //     // SELECT sma_sales.id, sma_sales.supplier_id, sma_sale_items.product_name, sum(sma_sale_items.quantity) FROM `sma_sales` LEFT JOIN `sma_sale_items` ON `sma_sale_items`.`sale_id` = `sma_sales`.`id` WHERE `sma_sales`.`supplier_id` = '1' GROUP BY sma_sale_items.product_name ORDER BY `sma_sale_items`.`product_name` DESC



            //     $qry = "SELECT sma_sales.id, sma_sales.supplier_id, sma_sale_items.product_name, sma_sale_items.quantity FROM `sma_sales` LEFT JOIN sma_sale_items ON sma_sale_items.sale_id = sma_sales.id WHERE sma_sales.supplier_id = ".$buyer_or_supplier."  
            //     ORDER BY `sma_sale_items`.`product_name`  DESC";

            //     $q = $this->db->query($qry);
            //     if ($q->num_rows() > 0) {
            //         foreach (($q->result()) as $row) {
            //             $data[] = $row;
            //         }
            //     } else {
            //         $data = NULL;
            //     }
            //     if ($pdf || $xls) {
            //         if (!empty($data)) {
            //             $this->load->library('excel');
            //             $this->excel->setActiveSheetIndex(0);
            //             $this->excel->getActiveSheet()->setTitle(lang('Supplier Reporting'));
            //             $this->excel->getActiveSheet()->SetCellValue('A1', lang('Date'));
            //             $this->excel->getActiveSheet()->SetCellValue('B1', lang('Supplier'));
            //             $this->excel->getActiveSheet()->SetCellValue('C1', lang('Own Company'));
            //             $this->excel->getActiveSheet()->SetCellValue('D1', lang('Reference No'));
            //             $this->excel->getActiveSheet()->SetCellValue('E1', lang('PO Number'));
            //             $this->excel->getActiveSheet()->SetCellValue('F1', lang('Customer'));
            //             $this->excel->getActiveSheet()->SetCellValue('G1', lang('Biller'));
            //             $this->excel->getActiveSheet()->SetCellValue('H1', lang('Product Discount'));
            //             $this->excel->getActiveSheet()->SetCellValue('I1', lang('Total Discount'));
            //             $this->excel->getActiveSheet()->SetCellValue('J1', lang('Order Discount'));
            //             $this->excel->getActiveSheet()->SetCellValue('K1', lang('Product Tax'));
            //             $this->excel->getActiveSheet()->SetCellValue('L1', lang('Order Tax'));
            //             $this->excel->getActiveSheet()->SetCellValue('M1', lang('Total Tax'));
            //             $this->excel->getActiveSheet()->SetCellValue('N1', lang('Grand Total'));
            //             $this->excel->getActiveSheet()->SetCellValue('O1', lang('Sale Status'));
            //             $this->excel->getActiveSheet()->SetCellValue('P1', lang('Payment Status'));
            //             $row = 2;
            //             foreach ($data as $data_row) {


            //                 $source = $data_row->date;
            //                 $date = new DateTime($source);



            //                 $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
            //                 $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
            //                 $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->companyname);
            //                 $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->reference_no);
            //                 $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->po_number);
            //                 $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->customer);
            //                 $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->biller);
            //                 $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->product_discount);
            //                 $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_discount);
            //                 $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->order_discount);
            //                 $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->product_tax);
            //                 $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->order_tax);
            //                 $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->total_tax);
            //                 $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->grand_total);
            //                 $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->sale_status);
            //                 $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->payment_status);
            //                 $row++;
            //             }

            //             $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
            //             $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(35);

            //             $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //             $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
            //             $filename = 'Supplier Reporting';
            //             $this->load->helper('excel');
            //             create_excel($this->excel, $filename);
            //         }
            //         $this->session->set_flashdata('error', lang('nothing_found'));
            //         redirect($_SERVER["HTTP_REFERER"]);

            //     }

            //     // $this->db->save_queries = TRUE;

            //     $this->load->library('datatables');
            //     $this->datatables
            //     ->select("sma_sales.id, sma_sales.supplier_id, sma_sale_items.product_name, sma_sale_items.quantity");
            //     $this->datatables->from('sales');
            //     $this->datatables->where('sma_sales.supplier_id = ', $buyer_or_supplier);
            //     $this->datatables->join('sale_items', 'sma_sale_items.sale_id = sma_sales.id', 'left');

            //     echo $this->datatables->generate();

            //     // echo $this->db->last_query();


            // } 

        }
    }
    function general_stock_reports(){

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('General Stock Report')
            )
        );
        $meta = array(
            'page_title' => lang('General Stock Report'),
            'bc' => $bc
        );
        $this->page_construct('reports/general_stock_reports', $meta, $this->data);
    }
    function warehouse_stock($warehouse = NULL){
        $this->sma->checkPermissions('index', TRUE);
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        }

        $this->data['stock']        = $warehouse ? $this->reports_model->getWarehouseStockValue($warehouse) : $this->reports_model->getStockValue();
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $this->data['warehouse_id'] = $warehouse;
        $this->data['warehouse']    = $warehouse ? $this->site->getWarehouseByID($warehouse) : NULL;
        $this->data['totals']       = $this->reports_model->getWarehouseTotals($warehouse);
        $bc                         = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('reports')
            )
        );
        $meta                       = array(
            'page_title' => lang('reports'),
            'bc' => $bc
        );
        $this->page_construct('reports/warehouse_stock', $meta, $this->data);
    }
    function expiry_alerts($warehouse_id = NULL){
        $this->sma->checkPermissions('expiry_alerts');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $user                       = $this->site->getUser();
            $this->data['warehouses']   = NULL;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse']    = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : NULL;
        }

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('product_expiry_alerts')
            )
        );
        $meta = array(
            'page_title' => lang('product_expiry_alerts'),
            'bc' => $bc
        );
        $this->page_construct('reports/expiry_alerts', $meta, $this->data);
    }
    function getExpiryAlerts($warehouse_id = NULL){
        $this->sma->checkPermissions('expiry_alerts', TRUE);
        $date = date('Y-m-d', strtotime('+3 months'));

        if (!$this->Owner && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        $this->load->library('datatables');
        if ($warehouse_id) {
            $this->datatables->select("image, product_code, product_name, quantity_balance, warehouses.name, expiry")->from('purchase_items')->join('products', 'products.id=purchase_items.product_id', 'left')->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')->where('warehouse_id', $warehouse_id)->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')->where('quantity_balance >', 0)->where('expiry <', $date);
        } else {
            $this->datatables->select("image, product_code, product_name, quantity_balance, warehouses.name, expiry")->from('purchase_items')->join('products', 'products.id=purchase_items.product_id', 'left')->join('warehouses', 'warehouses.id=purchase_items.warehouse_id', 'left')->where('expiry !=', NULL)->where('expiry !=', '0000-00-00')->where('quantity_balance >', 0)->where('expiry <', $date);
        }
        echo $this->datatables->generate();
    }
    function quantity_alerts($warehouse_id = NULL){
        $this->sma->checkPermissions('quantity_alerts');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses']   = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse']    = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $user                       = $this->site->getUser();
            $this->data['warehouses']   = NULL;
            $this->data['warehouse_id'] = $user->warehouse_id;
            $this->data['warehouse']    = $user->warehouse_id ? $this->site->getWarehouseByID($user->warehouse_id) : NULL;
        }
        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('product_quantity_alerts')
            )
        );
        $meta = array(
            'page_title' => lang('product_quantity_alerts'),
            'bc' => $bc
        );
        $this->page_construct('reports/quantity_alerts', $meta, $this->data);
    }
    function getQuantityAlerts($warehouse_id = NULL, $pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('quantity_alerts', TRUE);
        if (!$this->Owner && !$warehouse_id) {
            $user         = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }

        if ($pdf || $xls) {

            if ($warehouse_id) {
                $this->db->select('products.image as image, products.code, products.name, warehouses_products.quantity, alert_quantity')->from('products')->join('warehouses_products', 'warehouses_products.product_id=products.id', 'left')->where('alert_quantity > warehouses_products.quantity', NULL)->where('warehouse_id', $warehouse_id)->where('track_quantity', 1)->order_by('products.code desc');
            } else {
                $this->db->select('image, code, name, quantity, alert_quantity')->from('products')->where('alert_quantity > quantity', NULL)->where('track_quantity', 1)->order_by('code desc');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('product_quantity_alerts'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('quantity'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('alert_quantity'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->quantity);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->alert_quantity);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'product_quantity_alerts';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            // // $this->db->save_queries = TRUE;


            $this->load->library('datatables');
            if ($warehouse_id) {
                $this->datatables->select('image, code, name, wp.quantity, alert_quantity')->from('products')->join("( SELECT * from {$this->db->dbprefix('warehouses_products')} WHERE warehouse_id = {$warehouse_id}) wp", 'products.id=wp.product_id', 'left')->where('alert_quantity > wp.quantity', NULL)->or_where('wp.quantity', NULL)->where('track_quantity', 1)->group_by('products.id');
            } else {
                $this->datatables->select('image, code, name, quantity, alert_quantity')->from('products')->where('alert_quantity > quantity', NULL)->where('track_quantity', 1);
            }

            echo $this->datatables->generate();
            //     $str = $this->db->last_query();
            // echo $str;

        }
    }
    function suggestions(){
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1) {
            die();
        }

        $rows = $this->reports_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array(
                    'id' => $row->id,
                    'label' => $row->name . " (" . $row->code . ")"
                );
            }
            $this->sma->send_json($pr);
        } else {
            echo FALSE;
        }
    }
    public function best_sellers($warehouse_id = NULL){
        $this->sma->checkPermissions('products');

        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $y1                       = date('Y', strtotime('-1 month'));
        $m1                       = date('m', strtotime('-1 month'));
        $m1sdate                  = $y1 . '-' . $m1 . '-01 00:00:00';
        $m1edate                  = $y1 . '-' . $m1 . '-' . days_in_month($m1, $y1) . ' 23:59:59';
        $this->data['m1']         = date('M Y', strtotime($y1 . '-' . $m1));
        $this->data['m1bs']       = $this->reports_model->getBestSeller($m1sdate, $m1edate, $warehouse_id);
        $y2                       = date('Y', strtotime('-2 months'));
        $m2                       = date('m', strtotime('-2 months'));
        $m2sdate                  = $y2 . '-' . $m2 . '-01 00:00:00';
        $m2edate                  = $y2 . '-' . $m2 . '-' . days_in_month($m2, $y2) . ' 23:59:59';
        $this->data['m2']         = date('M Y', strtotime($y2 . '-' . $m2));
        $this->data['m2bs']       = $this->reports_model->getBestSeller($m2sdate, $m2edate, $warehouse_id);
        $y3                       = date('Y', strtotime('-3 months'));
        $m3                       = date('m', strtotime('-3 months'));
        $m3sdate                  = $y3 . '-' . $m3 . '-01 23:59:59';
        $this->data['m3']         = date('M Y', strtotime($y3 . '-' . $m3)) . ' - ' . $this->data['m1'];
        $this->data['m3bs']       = $this->reports_model->getBestSeller($m3sdate, $m1edate, $warehouse_id);
        $y4                       = date('Y', strtotime('-12 months'));
        $m4                       = date('m', strtotime('-12 months'));
        $m4sdate                  = $y4 . '-' . $m4 . '-01 23:59:59';
        $this->data['m4']         = date('M Y', strtotime($y4 . '-' . $m4)) . ' - ' . $this->data['m1'];
        $this->data['m4bs']       = $this->reports_model->getBestSeller($m4sdate, $m1edate, $warehouse_id);
        // $this->sma->print_arrays($this->data['m1bs'], $this->data['m2bs'], $this->data['m3bs'], $this->data['m4bs']);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc                       = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('best_sellers')
            )
        );
        $meta                     = array(
            'page_title' => lang('best_sellers'),
            'bc' => $bc
        );
        $this->page_construct('reports/best_sellers', $meta, $this->data);
    }
    function products(){
        $this->sma->checkPermissions();
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('products_report')
            )
        );
        $meta = array(
            'page_title' => lang('products_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/products', $meta, $this->data);
    }
    function getProductsReport($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('products', TRUE);

        $product     = $this->input->get('product') ? $this->input->get('product') : NULL;
        $category    = $this->input->get('category') ? $this->input->get('category') : NULL;
        $brand       = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        $subcategory = $this->input->get('subcategory') ? $this->input->get('subcategory') : NULL;
        $warehouse   = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $cf1         = $this->input->get('cf1') ? $this->input->get('cf1') : NULL;
        $cf2         = $this->input->get('cf2') ? $this->input->get('cf2') : NULL;
        $cf3         = $this->input->get('cf3') ? $this->input->get('cf3') : NULL;
        $cf4         = $this->input->get('cf4') ? $this->input->get('cf4') : NULL;
        $cf5         = $this->input->get('cf5') ? $this->input->get('cf5') : NULL;
        $cf6         = $this->input->get('cf6') ? $this->input->get('cf6') : NULL;
        $start_date  = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date    = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $pp = "( SELECT product_id, SUM(CASE WHEN pi.purchase_id IS NOT NULL THEN quantity ELSE 0 END) as purchasedQty, SUM(quantity_balance) as balacneQty, SUM( unit_cost * quantity_balance ) balacneValue, SUM( (CASE WHEN pi.purchase_id IS NOT NULL THEN (pi.subtotal) ELSE 0 END) ) totalPurchase from {$this->db->dbprefix('purchase_items')} pi LEFT JOIN {$this->db->dbprefix('purchases')} p on p.id = pi.purchase_id WHERE p.status != 'pending' AND p.status != 'ordered' ";
        $sp = "( SELECT si.product_id, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from " . $this->db->dbprefix('sales') . " s JOIN " . $this->db->dbprefix('sale_items') . " si on s.id = si.sale_id ";
        if ($start_date || $warehouse) {
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " AND p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $sp .= " AND ";
                }
            }
            if ($warehouse) {
                $pp .= " AND pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        $pp .= " GROUP BY pi.product_id ) PCosts";
        $sp .= " GROUP BY si.product_id ) PSales";
        if ($pdf || $xls) {

            $this->db->select($this->db->dbprefix('products') . ".code, " . $this->db->dbprefix('products') . ".name,
                COALESCE( PCosts.purchasedQty, 0 ) as PurchasedQty,
                COALESCE( PSales.soldQty, 0 ) as SoldQty,
                COALESCE( PCosts.balacneQty, 0 ) as BalacneQty,
                COALESCE( PCosts.totalPurchase, 0 ) as TotalPurchase,
                COALESCE( PCosts.balacneValue, 0 ) as TotalBalance,
                COALESCE( PSales.totalSale, 0 ) as TotalSales,
                (COALESCE( PSales.totalSale, 0 ) - COALESCE( PCosts.totalPurchase, 0 )) as Profit", FALSE)->from('products')->join($sp, 'products.id = PSales.product_id', 'left')->join($pp, 'products.id = PCosts.product_id', 'left')->order_by('products.name');

            if ($product) {
                $this->db->where($this->db->dbprefix('products') . ".id", $product);
            }
            if ($cf1) {
                $this->db->where($this->db->dbprefix('products') . ".cf1", $cf1);
            }
            if ($cf2) {
                $this->db->where($this->db->dbprefix('products') . ".cf2", $cf2);
            }
            if ($cf3) {
                $this->db->where($this->db->dbprefix('products') . ".cf3", $cf3);
            }
            if ($cf4) {
                $this->db->where($this->db->dbprefix('products') . ".cf4", $cf4);
            }
            if ($cf5) {
                $this->db->where($this->db->dbprefix('products') . ".cf5", $cf5);
            }
            if ($cf6) {
                $this->db->where($this->db->dbprefix('products') . ".cf6", $cf6);
            }
            if ($category) {
                $this->db->where($this->db->dbprefix('products') . ".category_id", $category);
            }
            if ($subcategory) {
                $this->db->where($this->db->dbprefix('products') . ".subcategory_id", $subcategory);
            }
            if ($brand) {
                $this->db->where($this->db->dbprefix('products') . ".brand", $brand);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('products_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('product_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('product_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('profit_loss'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('stock_in_hand'));

                $row  = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $bQty = 0;
                $bAmt = 0;
                $pl   = 0;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->BalacneQty);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->Profit);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->TotalBalance);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $bQty += $data_row->BalacneQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $bAmt += $data_row->TotalBalance;
                    $pl += $data_row->Profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":I" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $bQty);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $pl);
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $bAmt);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'products_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->load->library('datatables');
            $this->datatables->select($this->db->dbprefix('products') . ".code, " . $this->db->dbprefix('products') . ".name,
                CONCAT(COALESCE( PCosts.purchasedQty, 0 ), '__', COALESCE( PCosts.totalPurchase, 0 )) as purchased,
                CONCAT(COALESCE( PSales.soldQty, 0 ), '__', COALESCE( PSales.totalSale, 0 )) as sold,
                (COALESCE( PSales.totalSale, 0 ) - COALESCE( PCosts.totalPurchase, 0 )) as Profit,
                CONCAT(COALESCE( PCosts.balacneQty, 0 ), '__', COALESCE( PCosts.balacneValue, 0 )) as balance, {$this->db->dbprefix('products')}.id as id", FALSE)->from('products')->join($sp, 'products.id = PSales.product_id', 'left')->join($pp, 'products.id = PCosts.product_id', 'left')->group_by('products.code, PSales.soldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase, PCosts.balacneQty, PCosts.balacneValue');

            if ($product) {
                $this->datatables->where($this->db->dbprefix('products') . ".id", $product);
            }
            if ($cf1) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf1", $cf1);
            }
            if ($cf2) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf2", $cf2);
            }
            if ($cf3) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf3", $cf3);
            }
            if ($cf4) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf4", $cf4);
            }
            if ($cf5) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf5", $cf5);
            }
            if ($cf6) {
                $this->datatables->where($this->db->dbprefix('products') . ".cf6", $cf6);
            }
            if ($category) {
                $this->datatables->where($this->db->dbprefix('products') . ".category_id", $category);
            }
            if ($subcategory) {
                $this->datatables->where($this->db->dbprefix('products') . ".subcategory_id", $subcategory);
            }
            if ($brand) {
                $this->datatables->where($this->db->dbprefix('products') . ".brand", $brand);
            }

            echo $this->datatables->generate();
        }
    }
    function categories(){
        $this->sma->checkPermissions('products');
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('categories_report')
            )
        );
        $meta = array(
            'page_title' => lang('categories_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/categories', $meta, $this->data);
    }
    function getCategoriesReport($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('products', TRUE);
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $category   = $this->input->get('category') ? $this->input->get('category') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $pp = "( SELECT pp.category_id as category, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi ON pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id ";
        $sp = "( SELECT sp.category_id as category, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from {$this->db->dbprefix('products')} sp
                left JOIN " . $this->db->dbprefix('sale_items') . " si ON sp.id = si.product_id
                left join " . $this->db->dbprefix('sales') . " s ON s.id = si.sale_id ";
        if ($start_date || $warehouse) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        $pp .= " GROUP BY pp.category_id ) PCosts";
        $sp .= " GROUP BY sp.category_id ) PSales";

        if ($pdf || $xls) {

            $this->db->select($this->db->dbprefix('categories') . ".code, " . $this->db->dbprefix('categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)->from('categories')->join($sp, 'categories.id = PSales.category', 'left')->join($pp, 'categories.id = PCosts.category', 'left')->group_by('categories.id, categories.code, categories.name')->order_by('categories.code', 'asc');

            if ($category) {
                $this->db->where($this->db->dbprefix('categories') . ".id", $category);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('categories_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('category_code'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('category_name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('profit_loss'));

                $row  = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl   = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->code);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("C" . $row . ":G" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'categories_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {


            $this->load->library('datatables');
            $this->datatables->select($this->db->dbprefix('categories') . ".id as cid, " . $this->db->dbprefix('categories') . ".code, " . $this->db->dbprefix('categories') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)->from('categories')->join($sp, 'categories.id = PSales.category', 'left')->join($pp, 'categories.id = PCosts.category', 'left');

            if ($category) {
                $this->datatables->where('categories.id', $category);
            }
            $this->datatables->group_by('categories.id, categories.code, categories.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
            $this->datatables->unset_column('cid');
            echo $this->datatables->generate();
        }
    }
    function brands(){
        $this->sma->checkPermissions('products');
        $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        $this->data['brands']     = $this->site->getAllBrands();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        if ($this->input->post('start_date')) {
            $dt = "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
        } else {
            $dt = "Till " . $this->input->post('end_date');
        }
        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('brands_report')
            )
        );
        $meta = array(
            'page_title' => lang('brands_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/brands', $meta, $this->data);
    }
    function getBrandsReport($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('products', TRUE);
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $brand      = $this->input->get('brand') ? $this->input->get('brand') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $pp = "( SELECT pp.brand as brand, SUM( pi.quantity ) purchasedQty, SUM( pi.subtotal ) totalPurchase from {$this->db->dbprefix('products')} pp
                left JOIN " . $this->db->dbprefix('purchase_items') . " pi ON pp.id = pi.product_id
                left join " . $this->db->dbprefix('purchases') . " p ON p.id = pi.purchase_id ";
        $sp = "( SELECT sp.brand as brand, SUM( si.quantity ) soldQty, SUM( si.subtotal ) totalSale from {$this->db->dbprefix('products')} sp
                left JOIN " . $this->db->dbprefix('sale_items') . " si ON sp.id = si.product_id
                left join " . $this->db->dbprefix('sales') . " s ON s.id = si.sale_id ";
        if ($start_date || $warehouse) {
            $pp .= " WHERE ";
            $sp .= " WHERE ";
            if ($start_date) {
                $start_date = $this->sma->fld($start_date);
                $end_date   = $end_date ? $this->sma->fld($end_date) : date('Y-m-d');
                $pp .= " p.date >= '{$start_date}' AND p.date < '{$end_date}' ";
                $sp .= " s.date >= '{$start_date}' AND s.date < '{$end_date}' ";
                if ($warehouse) {
                    $pp .= " AND ";
                    $sp .= " AND ";
                }
            }
            if ($warehouse) {
                $pp .= " pi.warehouse_id = '{$warehouse}' ";
                $sp .= " si.warehouse_id = '{$warehouse}' ";
            }
        }
        $pp .= " GROUP BY pp.brand ) PCosts";
        $sp .= " GROUP BY sp.brand ) PSales";

        if ($pdf || $xls) {

            $this->db->select($this->db->dbprefix('brands') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)->from('brands')->join($sp, 'brands.id = PSales.brand', 'left')->join($pp, 'brands.id = PCosts.brand', 'left')->group_by('brands.id, brands.name')->order_by('brands.code', 'asc');

            if ($brand) {
                $this->db->where($this->db->dbprefix('brands') . ".id", $brand);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('brands_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('brands'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('purchased'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sold'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchased_amount'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('sold_amount'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('profit_loss'));

                $row  = 2;
                $sQty = 0;
                $pQty = 0;
                $sAmt = 0;
                $pAmt = 0;
                $pl   = 0;
                foreach ($data as $data_row) {
                    $profit = $data_row->TotalSales - $data_row->TotalPurchase;
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->PurchasedQty);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->SoldQty);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->TotalPurchase);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->TotalSales);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $profit);
                    $pQty += $data_row->PurchasedQty;
                    $sQty += $data_row->SoldQty;
                    $pAmt += $data_row->TotalPurchase;
                    $sAmt += $data_row->TotalSales;
                    $pl += $profit;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("B" . $row . ":F" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $pQty);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $sQty);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $pAmt);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $sAmt);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $pl);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                $filename = 'brands_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {


            $this->load->library('datatables');
            $this->datatables->select($this->db->dbprefix('brands') . ".id as id, " . $this->db->dbprefix('brands') . ".name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)->from('brands')->join($sp, 'brands.id = PSales.brand', 'left')->join($pp, 'brands.id = PCosts.brand', 'left');

            if ($brand) {
                $this->datatables->where('brands.id', $brand);
            }
            $this->datatables->group_by('brands.id, brands.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
            $this->datatables->unset_column('id');
            echo $this->datatables->generate();
        }
    }
    function profit($date = NULL, $warehouse_id = NULL, $re = NULL){
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }
        if (!$date) {
            $date = date('Y-m-d');
        }
        $this->data['costing']    = $this->reports_model->getCosting($date, $warehouse_id);
        $this->data['discount']   = $this->reports_model->getOrderDiscount($date, $warehouse_id);
        $this->data['expenses']   = $this->reports_model->getExpenses($date, $warehouse_id);
        $this->data['returns']    = $this->reports_model->getReturns($date, $warehouse_id);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['swh']        = $warehouse_id;
        $this->data['date']       = $date;
        if ($re) {
            echo $this->load->view($this->theme . 'reports/profit', $this->data, TRUE);
            exit();
        }
        $this->load->view($this->theme . 'reports/profit', $this->data);
    }
    function monthly_profit($year, $month, $warehouse_id = NULL, $re = NULL){
        if (!$this->Owner) {
            $this->session->set_flashdata('error', lang('access_denied'));
            $this->sma->md();
        }

        $this->data['costing']    = $this->reports_model->getCosting(NULL, $warehouse_id, $year, $month);
        $this->data['discount']   = $this->reports_model->getOrderDiscount(NULL, $warehouse_id, $year, $month);
        $this->data['expenses']   = $this->reports_model->getExpenses(NULL, $warehouse_id, $year, $month);
        $this->data['returns']    = $this->reports_model->getReturns(NULL, $warehouse_id, $year, $month);
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['swh']        = $warehouse_id;
        $this->data['year']       = $year;
        $this->data['month']      = $month;
        $this->data['date']       = date('F Y', strtotime($year . '-' . $month . '-' . '01'));
        if ($re) {
            echo $this->load->view($this->theme . 'reports/monthly_profit', $this->data, TRUE);
            exit();
        }
        $this->load->view($this->theme . 'reports/monthly_profit', $this->data);
    }
    function daily_sales($warehouse_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $user_id = NULL){
        $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config              = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => admin_url('reports/daily_sales/' . ($warehouse_id ? $warehouse_id : 0)),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $user_id ? $this->reports_model->getStaffDailySales($user_id, $year, $month, $warehouse_id) : $this->reports_model->getDailySales($year, $month, $warehouse_id);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($sale->discount) . "</td></tr><tr><td>" . lang("shipping") . "</td><td>" . $this->sma->formatMoney($sale->shipping) . "</td></tr><tr><td>" . lang("product_tax") . "</td><td>" . $this->sma->formatMoney($sale->tax1) . "</td></tr><tr><td>" . lang("order_tax") . "</td><td>" . $this->sma->formatMoney($sale->tax2) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total) . "</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        $this->data['year']     = $year;
        $this->data['month']    = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/daily', $this->data, true);
            $name = lang("daily_sales") . "_" . $year . "_" . $month . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->site->getAllWarehouses();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc                          = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('daily_sales_report')
            )
        );
        $meta                        = array(
            'page_title' => lang('daily_sales_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/daily', $meta, $this->data);
    }
    function monthly_sales($warehouse_id = NULL, $year = NULL, $pdf = NULL, $user_id = NULL){
        $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year']  = $year;
        $this->data['sales'] = $user_id ? $this->reports_model->getStaffMonthlySales($user_id, $year, $warehouse_id) : $this->reports_model->getMonthlySales($year, $warehouse_id);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/monthly', $this->data, true);
            $name = lang("monthly_sales") . "_" . $year . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->site->getAllWarehouses();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc                          = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('monthly_sales_report')
            )
        );
        $meta                        = array(
            'page_title' => lang('monthly_sales_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/monthly', $meta, $this->data);
    }
    function stock_report_old(){
        $this->sma->checkPermissions('sales');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $bc                       = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('sales_report')
            )
        );
        $meta                     = array(
            'page_title' => lang('sales_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/stock_report', $meta, $this->data);
    }
    function getStockReport($pdf = NULL, $xls = NULL){

        $user                 = $this->site->getUser();
        $company_id_sale_show = $user->biller_id;

        // print_r($this->site->getUser());
        // print_r($_SESSION);
        // die;


        $this->sma->checkPermissions('sales', TRUE);
        $product      = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user         = $this->input->get('user') ? $this->input->get('user') : NULL;
        $customer     = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller       = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial       = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {


            $this->db->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('sale_items') . ".product_name, ' (', " . $this->db->dbprefix('sale_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, payment_status", FALSE)->from('sales')->join('sale_items', 'sale_items.sale_id=sales.id', 'left')->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')->group_by('sales.id')->order_by('sales.date desc');

            if ($user) {
                $this->db->where('sales.created_by', $user);
            }
            if ($product) {
                $this->db->where('sale_items.product_id', $product);
            }
            if ($serial) {
                $this->db->like('sale_items.serial_no', $serial);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('payment_status'));

                $row     = 2;
                $total   = 0;
                $paid    = 0;
                $balance = 0;
                foreach ($data as $data_row) {

                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));

                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($data_row->grand_total - $data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, lang($data_row->payment_status));
                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $balance += ($data_row->grand_total - $data_row->paid);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'sales_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            // $this->db->save_queries = TRUE;
            // $si = "( SELECT sale_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('sale_items')}.product_name, '__', {$this->db->dbprefix('sale_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('sale_items')} ";
            // if ($product || $serial) { $si .= " WHERE "; }
            // if ($product) {
            //     $si .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
            // }
            // if ($product && $serial) { $si .= " AND "; }
            // if ($serial) {
            //     $si .= " {$this->db->dbprefix('sale_items')}.serial_no LIKe '%{$serial}%' ";
            // }
            // $si .= " GROUP BY {$this->db->dbprefix('sale_items')}.sale_id ) FSI";

            $this->load->library('datatables');

            $this->datatables
                // ->select("DATE_FORMAT(date, '%Y-%m-%d') as date, reference_no, biller, customer, FSI.item_nane as iname, grand_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('sales')}.id as id", FALSE)

                // ->from('sales')

                // ->join($si, 'FSI.sale_id=sales.id', 'left')

                // ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')

                // SELECT sma_purchases.supplier_id, sma_purchase_items.product_name, sma_purchase_items.batch, sma_purchase_items.expiry, sma_purchase_items.quantity FROM `sma_purchase_items` LEFT JOIN `sma_purchases` ON `sma_purchases`.`id` = sma_purchase_items.purchase_id WHERE sma_purchases.supplier_id = 2

                // ->select("{$this->db->dbprefix('purchases')}.supplier_id, {$this->db->dbprefix('purchase_items')}.product_name, {$this->db->dbprefix('purchase_items')}.batch, {$this->db->dbprefix('purchase_items')}.expiry, {$this->db->dbprefix('purchase_items')}.quantity")
                ->select("{$this->db->dbprefix('purchase_items')}.product_name, {$this->db->dbprefix('purchase_items')}.batch, {$this->db->dbprefix('purchase_items')}.expiry, {$this->db->dbprefix('purchase_items')}.quantity_balance")->from('purchase_items')->join('sma_purchases', 'purchases.id=purchase_items.purchase_id', 'left')->where('purchases.supplier_id', $company_id_sale_show)->where("purchase_items.quantity_balance > 0")->where("purchase_items.status != 'ordered'");

            // if ($user) {
            //     $this->datatables->where('sales.created_by', $user);
            // }
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }



            echo $this->datatables->generate();
            //echo $this->db->last_query();

        }
    }
    function sales(){
        $this->sma->checkPermissions('sales');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $bc                       = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('sales_report')
            )
        );
        $meta                     = array(
            'page_title' => lang('sales_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/sales', $meta, $this->data);
    }
    function getSalesReport_v2(){

        $userId = $_SESSION['user_id'];

        $biller = $this->db->query("select biller_id from sma_users where id = '$userId'")->result_array();

        $biller_id =  $biller[0]['biller_id'];

        $qry1 = "SELECT t1.id, t1.reference_no,  'RhoCom' AS biller, t1.customer, t1.grand_total FROM `sma_sales` AS t1  LEFT JOIN `sma_sale_items` AS t2  ON t1.id = t2.sale_id  WHERE supplier_id = '$biller_id'";

        $getDataQ1 = $this->db->query($qry1)->result_array();

        // $qry1 = "SELECT t1.reference_no,  'RhoCom' AS biller, t1.customer, t1.grand_total FROM `sma_sales` AS t1  LEFT JOIN `sma_sale_items` AS t2  ON t1.id = t2.sale_id  WHERE supplier_id = '$biller_id'";
        // $get_sale_id_products = $this->db->query($qry)->result_array();
        $prod = [];
        foreach ($getDataQ1 as $key => $val) {
            $id = $val['id'];
            $q2 = "Select product_name, round(quantity) as quanitity from sma_sale_items where sale_id = '$id'";
            $getDataQ2 = $this->db->query($q2)->result_array();

            foreach ($getDataQ2 as $key => $val2) {

                $prod[$val['id']][] = $val2['product_name'] . ' (x' . $val2['quanitity'] . ')';
            }
        }
        // date, reference_no, biller = rohcom , name, quantity, grand total
        echo '<prev>';
        print_r($prod);
        echo '</prev>';
    }
    function getSalesReport($pdf = NULL, $xls = NULL){
        $user = $this->site->getUser();
        $company_id_sale_show = $user->biller_id;
        $check_user_group_id  = $user->group_id;



        $this->sma->checkPermissions('sales', TRUE);
        $product      = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user         = $this->input->get('user') ? $this->input->get('user') : NULL;
        $customer     = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller       = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial       = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            if ($company_id_sale_show == "2" || $company_id_sale_show == 2) {
                $qry = "SELECT sma_sales.reference_no, sma_sales.date, sma_sales.po_number, sma_companies.name, sma_sale_items.product_name, sma_sale_items.quantity, ( sma_products.unit_weight / 1000 ) AS litre_pcs, ( sma_sale_items.quantity * (sma_products.unit_weight / 1000) ) AS total_sales_in_ltr, sma_sale_items.product_unit_code, sma_sale_items.net_unit_price, IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship))) AS 'sale_price', ( IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship)))*sma_sale_items.quantity  ) AS 'value_excl_tax', sma_sale_items.tax, sma_sale_items.item_tax, sma_sale_items.further_tax, sma_sale_items.fed_tax, ( IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship)))*sma_sale_items.quantity + (sma_sale_items.tax + sma_sale_items.further_tax + sma_sale_items.fed_tax) ) AS total_tax, sma_sale_items.discount_one, sma_sale_items.discount_three, sma_sale_items.discount, sma_sale_items.subtotal, IF(sma_tax_rates.type = '1', 'GST', IF(sma_tax_rates.code = 'exp', 'Exempted', '3rd Schdule')) AS 'remarks', IF(sma_tax_rates.type = '1', 0, IF(sma_tax_rates.code = 'exp', 0, sma_sale_items.mrp / 1.17)) AS 'mrp_excl_tax', sma_sale_items.mrp, sma_sale_items.expiry, sma_sale_items.batch, sma_brands.name AS 'brand' FROM sma_sales LEFT JOIN sma_sale_items ON sma_sales.id = sma_sale_items.sale_id LEFT JOIN sma_companies ON sma_companies.id = sma_sales.customer_id LEFT JOIN sma_tax_rates ON sma_sale_items.tax_rate_id = sma_tax_rates.id LEFT JOIN sma_own_companies ON sma_sales.own_company = sma_own_companies.id LEFT JOIN sma_products ON sma_products.id = sma_sale_items.product_id LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand WHERE sma_sales.supplier_id = 2";

                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }

                $file_name = 'sale_report_' . date('Ymd') . '.csv';
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$file_name");
                header("Content-Type: application/csv;");

                // get data 
                $student_data = $this->db->query($qry)->result_array();

                // file creation 
                $file = fopen('php://output', 'w');

                $header = array('Invoice No', 'Date', 'P.O Number', 'Customer Name', 'Product Name', 'Qty Order', 'Litre/PC', 'Total Sales In Ltr', 'UOM', 'Price excluding Tax', 'Selling Price', 'Value Excluding Tax', 'Tax', 'Item Tax', 'Further Tax', 'FED Tax', 'Total including all Taxes', 'Sales Incentive', 'Trade Discount', 'Consumer Discount', 'Total Discount', 'Subtotal', 'Remarks', 'Mrp', 'Expiry Date', 'Batch', 'Brand');
                fputcsv($file, $header);
                foreach ($student_data as $key => $value) {
                    fputcsv($file, $value);
                }
                fclose($file);
                // exit;
                // die;

                // if ($pdf || $xls) {

                //     if (!empty($data)) {

                //         $this->load->library('excel');
                //         $this->excel->setActiveSheetIndex(0);
                //         $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                //         // $this->excel->getActiveSheet()->SetCellValue('A1', lang('Own Company'));
                //         // $this->excel->getActiveSheet()->SetCellValue('B1', lang('Customer NTN'));
                //         $this->excel->getActiveSheet()->SetCellValue('A1', lang('Invoice No'));
                //         $this->excel->getActiveSheet()->SetCellValue('B1', lang('Date'));
                //         $this->excel->getActiveSheet()->SetCellValue('C1', lang('P.O Number'));
                //         $this->excel->getActiveSheet()->SetCellValue('D1', lang('Customer Name'));
                //         $this->excel->getActiveSheet()->SetCellValue('E1', lang('Product Name'));
                //         // $this->excel->getActiveSheet()->SetCellValue('H1', lang('HSN Code'));
                //         $this->excel->getActiveSheet()->SetCellValue('F1', lang('Qty Order'));
                //         $this->excel->getActiveSheet()->SetCellValue('G1', lang('Litre/PC'));
                //         $this->excel->getActiveSheet()->SetCellValue('H1', lang('Total Sales In Ltr'));
                //         $this->excel->getActiveSheet()->SetCellValue('I1', lang('UOM'));
                //         $this->excel->getActiveSheet()->SetCellValue('J1', lang('Price excluding Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('K1', lang('Selling Price'));
                //         $this->excel->getActiveSheet()->SetCellValue('L1', lang('Value Excluding Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('M1', lang('Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('N1', lang('Item Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('O1', lang('Further Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('P1', lang('FED Tax'));
                //         $this->excel->getActiveSheet()->SetCellValue('Q1', lang('Total including all taxes'));
                //         $this->excel->getActiveSheet()->SetCellValue('R1', lang('Sales Incentive'));
                //         $this->excel->getActiveSheet()->SetCellValue('S1', lang('Trade Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('T1', lang('Consumer Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('U1', lang('Total Discount'));
                //         $this->excel->getActiveSheet()->SetCellValue('V1', lang('Subtotal'));
                //         $this->excel->getActiveSheet()->SetCellValue('W1', lang('Remarks'));

                //         // $this->excel->getActiveSheet()->SetCellValue('Y1', lang('M.R.P Excluding Tax'));
                //         // $this->excel->getActiveSheet()->SetCellValue('Z1', lang('M.R.P Third Schedule'));
                //         $this->excel->getActiveSheet()->SetCellValue('X1', lang('Mrp'));
                //         $this->excel->getActiveSheet()->SetCellValue('Y1', lang('Expiry Date'));
                //         $this->excel->getActiveSheet()->SetCellValue('Z1', lang('Batch'));
                //         $this->excel->getActiveSheet()->SetCellValue('AA1', lang('Brand'));

                //         $row = 2;

                //         $own_company   = 0;
                //         $customer_ntn  = 0;
                //         $invoice_no    = 0;
                //         $date          = 0;
                //         $po_number     = 0;
                //         $customer_name = 0;
                //         $product_name  = 0;
                //         $hsn_code      = 0;
                //         $qty_order     = 0;
                //         $uom           = 0;
                //         $price_ex_tax  = 0;
                //         $selling_price = 0;

                //         $value_excl_tax = 0;

                //         $tax                   = 0;
                //         $item_tax              = 0;
                //         $frther_tax            = 0;
                //         $fed_tax               = 0;
                //         $total_include_all_tax = 0;
                //         $sale_inc              = 0;
                //         $trade_dis             = 0;
                //         $consumer_dis          = 0;
                //         $total_dis             = 0;
                //         $subtotal              = 0;

                //         $remarks = 0;


                //         $mrp_excl_tax    = 0;
                //         $value_third_sch = 0;


                //         $mrp         = 0;
                //         $expiry_date = 0;
                //         $batch       = 0;
                //         $brand       = 0;

                //         foreach ($data as $data_row) {

                //             $source = $data_row->date;
                //             $date   = new DateTime($source);

                //             $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->reference_no);
                //             $this->excel->getActiveSheet()->SetCellValue('B' . $row, $date->format('d-M-Y'));
                //             $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->po_number);
                //             $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->name);
                //             $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->product_name);
                //             $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->quantity);
                //             $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->litre_pcs);
                //             $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->total_sales_in_ltr);
                //             $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->product_unit_code);
                //             $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->net_unit_price);
                //             $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->sale_price); // Correct karna hy
                //             $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->value_excl_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->tax);
                //             $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->item_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->further_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->fed_tax);
                //             $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->fed_tax); // Correct karna hy
                //             $this->excel->getActiveSheet()->SetCellValue('R' . $row, $data_row->discount_one);
                //             $this->excel->getActiveSheet()->SetCellValue('S' . $row, $data_row->discount_two);
                //             $this->excel->getActiveSheet()->SetCellValue('T' . $row, $data_row->discount_three);
                //             $this->excel->getActiveSheet()->SetCellValue('U' . $row, $data_row->discount);
                //             $this->excel->getActiveSheet()->SetCellValue('V' . $row, $data_row->subtotal);
                //             $this->excel->getActiveSheet()->SetCellValue('W' . $row, $data_row->remarks); // Correct karna hy
                //             $this->excel->getActiveSheet()->SetCellValue('X' . $row, $data_row->mrp);
                //             $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $data_row->expiry);
                //             $this->excel->getActiveSheet()->SetCellValue('Z' . $row, $data_row->batch);
                //             $this->excel->getActiveSheet()->SetCellValue('AA' . $row, $data_row->brand);

                //             $row++;
                //         }

                //         // $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                //         // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                //         $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
                //         $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                //         $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                //         $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                //         // $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);

                //         $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);


                //         $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(25);

                //         // $this->excel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
                //         // $this->excel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);


                //         $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
                //         $this->excel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);

                //         $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                //         $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                //         $filename = 'sales_report';
                //         $this->load->helper('excel');
                //         create_excel($this->excel, $filename);
                //     }

                //      $this->session->set_flashdata('error', lang('nothing_found'));
                //      redirect($_SERVER["HTTP_REFERER"]);
                // }


            } else {
                $qry = "SELECT sma_own_companies.companyname,sma_companies.cf1,sma_sales.reference_no,sma_sales.date,sma_sales.po_number,sma_companies.name,sma_sale_items.product_name,sma_products.hsn_code,sma_sale_items.quantity,sma_sale_items.product_unit_code,sma_sale_items.net_unit_price,IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship))) AS 'sale_price',(IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship)))*sma_sale_items.quantity) as 'value_excl_tax',sma_sale_items.tax,sma_sale_items.item_tax,sma_sale_items.further_tax,sma_sale_items.fed_tax,(IF(sma_companies.sales_type = 'consignment',sma_sale_items.unit_price,IF(sma_companies.sales_type = 'crossdock',sma_sale_items.crossdock,IF(sma_companies.sales_type = 'dropship',sma_sale_items.dropship,sma_sale_items.dropship)))*sma_sale_items.quantity + (sma_sale_items.tax + sma_sale_items.further_tax + sma_sale_items.fed_tax))  AS total_tax,sma_sale_items.discount_one,sma_sale_items.discount_two,sma_sale_items.discount_three,sma_sale_items.discount,sma_sale_items.subtotal,IF(sma_tax_rates.type = '1','GST',IF(sma_tax_rates.code = 'exp','Exempted','3rd Schdule')) AS 'remarks',IF(sma_tax_rates.type = '1',0,IF(sma_tax_rates.code = 'exp',0,sma_sale_items.mrp/1.17)) AS 'mrp_excl_tax',IF(sma_tax_rates.type = '1',0,IF(sma_tax_rates.code = 'exp',0,(sma_sale_items.mrp/1.17)*sma_sale_items.quantity)) AS 'value_third_sch',sma_sale_items.mrp,sma_sale_items.expiry,sma_sale_items.batch,sma_brands.name AS 'brand'  FROM sma_sales LEFT JOIN sma_sale_items ON sma_sales.id = sma_sale_items.sale_id  LEFT JOIN sma_companies ON sma_companies.id = sma_sales.customer_id  LEFT JOIN sma_tax_rates ON sma_sale_items.tax_rate_id = sma_tax_rates.id  LEFT JOIN sma_own_companies ON sma_sales.own_company = sma_own_companies.id  LEFT JOIN sma_products ON sma_products.id = sma_sale_items.product_id  LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand where sma_sales.supplier_id = " . $company_id_sale_show . "";

                $q = $this->db->query($qry);
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as $row) {
                        $data[] = $row;
                    }
                } else {
                    $data = NULL;
                }

                if ($pdf || $xls) {

                    if (!empty($data)) {

                        $this->load->library('excel');
                        $this->excel->setActiveSheetIndex(0);
                        $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                        // $this->excel->getActiveSheet()->SetCellValue('A1', lang('Own Company'));
                        // $this->excel->getActiveSheet()->SetCellValue('B1', lang('Customer NTN'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('Invoice No'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('Date'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('P.O Number'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('Customer Name'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('Product Name'));
                        // $this->excel->getActiveSheet()->SetCellValue('H1', lang('HSN Code'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('Qty Order'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('UOM'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('Price excluding Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('Selling Price'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('Value Excluding Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('Item Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('Further Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('FED Tax'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('Total including all taxes'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('Sales Incentive'));
                        $this->excel->getActiveSheet()->SetCellValue('Q1', lang('Trade Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('R1', lang('Consumer Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('S1', lang('Total Discount'));
                        $this->excel->getActiveSheet()->SetCellValue('T1', lang('Subtotal'));
                        $this->excel->getActiveSheet()->SetCellValue('U1', lang('Remarks'));

                        // $this->excel->getActiveSheet()->SetCellValue('Y1', lang('M.R.P Excluding Tax'));
                        // $this->excel->getActiveSheet()->SetCellValue('Z1', lang('M.R.P Third Schedule'));


                        $this->excel->getActiveSheet()->SetCellValue('V1', lang('Mrp'));
                        $this->excel->getActiveSheet()->SetCellValue('W1', lang('Expiry Date'));
                        $this->excel->getActiveSheet()->SetCellValue('X1', lang('Batch'));
                        $this->excel->getActiveSheet()->SetCellValue('Y1', lang('Brand'));

                        $row = 2;

                        $own_company   = 0;
                        $customer_ntn  = 0;
                        $invoice_no    = 0;
                        $date          = 0;
                        $po_number     = 0;
                        $customer_name = 0;
                        $product_name  = 0;
                        $hsn_code      = 0;
                        $qty_order     = 0;
                        $uom           = 0;
                        $price_ex_tax  = 0;
                        $selling_price = 0;

                        $value_excl_tax = 0;

                        $tax                   = 0;
                        $item_tax              = 0;
                        $frther_tax            = 0;
                        $fed_tax               = 0;
                        $total_include_all_tax = 0;
                        $sale_inc              = 0;
                        $trade_dis             = 0;
                        $consumer_dis          = 0;
                        $total_dis             = 0;
                        $subtotal              = 0;

                        $remarks = 0;


                        $mrp_excl_tax    = 0;
                        $value_third_sch = 0;


                        $mrp         = 0;
                        $expiry_date = 0;
                        $batch       = 0;
                        $brand       = 0;

                        foreach ($data as $data_row) {

                            $source = $data_row->date;
                            $date   = new DateTime($source);

                            // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->companyname);
                            // $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->cf1);
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $date->format('d-M-Y'));
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->po_number);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->name);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->product_name);
                            // $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->hsn_code);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->quantity);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->product_unit_code);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->net_unit_price);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->sale_price); // Correct karna hy

                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->value_excl_tax);

                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->tax);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $data_row->item_tax);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $data_row->further_tax);
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $data_row->fed_tax);
                            $this->excel->getActiveSheet()->SetCellValue('O' . $row, $data_row->fed_tax); // Correct karna hy
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $data_row->discount_one);
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $data_row->discount_two);
                            $this->excel->getActiveSheet()->SetCellValue('R' . $row, $data_row->discount_three);
                            $this->excel->getActiveSheet()->SetCellValue('S' . $row, $data_row->discount);
                            $this->excel->getActiveSheet()->SetCellValue('T' . $row, $data_row->subtotal);
                            $this->excel->getActiveSheet()->SetCellValue('U' . $row, $data_row->remarks); // Correct karna hy


                            // $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $data_row->mrp_excl_tax);
                            // $this->excel->getActiveSheet()->SetCellValue('Z' . $row, $data_row->value_third_sch);

                            $this->excel->getActiveSheet()->SetCellValue('V' . $row, $data_row->mrp);
                            $this->excel->getActiveSheet()->SetCellValue('W' . $row, $data_row->expiry);
                            $this->excel->getActiveSheet()->SetCellValue('X' . $row, $data_row->batch);
                            $this->excel->getActiveSheet()->SetCellValue('Y' . $row, $data_row->brand);

                            $row++;
                        }

                        // $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                        // $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                        // $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);

                        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);


                        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(25);

                        // $this->excel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
                        // $this->excel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);


                        $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
                        $this->excel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);

                        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('C2:G' . $row)->getAlignment()->setWrapText(true);
                        $filename = 'sales_report';
                        $this->load->helper('excel');
                        create_excel($this->excel, $filename);
                    }
                    $this->session->set_flashdata('error', lang('nothing_found'));
                    redirect($_SERVER["HTTP_REFERER"]);
                }
            }
        } else {

            $si = "( SELECT sale_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('sale_items')}.product_name, '__', {$this->db->dbprefix('sale_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('sale_items')} ";
            if ($product || $serial) {
                $si .= " WHERE ";
            }
            if ($product) {
                $si .= " {$this->db->dbprefix('sale_items')}.product_id = {$product} ";
            }
            if ($product && $serial) {
                $si .= " AND ";
            }
            if ($serial) {
                $si .= " {$this->db->dbprefix('sale_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $si .= " GROUP BY {$this->db->dbprefix('sale_items')}.sale_id ) FSI";

            $this->load->library('datatables');

            $this->datatables->select("DATE_FORMAT(date, '%Y-%m-%d') as date, reference_no, biller, customer, FSI.item_nane as iname, grand_total, paid, (grand_total-paid) as balance, payment_status, {$this->db->dbprefix('sales')}.id as id", FALSE)->from('sales')->join($si, 'FSI.sale_id=sales.id', 'left')->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left');



            // echo $check_user_group_id;

            // die();
            // exit();

            if (($check_user_group_id != 1) and ($check_user_group_id != 2)) {
                if(!empty($company_id_sale_show)){
                $si .= $this->datatables->where($this->db->dbprefix('sales') . ".supplier_id", $company_id_sale_show);
            }
         }



            if ($user) {
                $this->datatables->where('sales.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FSI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FSI.serial_no', $serial);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('sales.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('sales.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }



            echo $this->datatables->generate();

            // echo $this->db->last_query();
            // die();
            // exit();

        }


        // die();
        // exit();

    }
    function getQuotesReport($pdf = NULL, $xls = NULL){

        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('customer')) {
            $customer = $this->input->get('customer');
        } else {
            $customer = NULL;
        }
        if ($this->input->get('biller')) {
            $biller = $this->input->get('biller');
        } else {
            $biller = NULL;
        }
        if ($this->input->get('warehouse')) {
            $warehouse = $this->input->get('warehouse');
        } else {
            $warehouse = NULL;
        }
        if ($this->input->get('reference_no')) {
            $reference_no = $this->input->get('reference_no');
        } else {
            $reference_no = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if ($pdf || $xls) {

            $this->db->select("date, reference_no, biller, customer, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('quote_items') . ".product_name, ' (', " . $this->db->dbprefix('quote_items') . ".quantity, ')') SEPARATOR '<br>') as iname, grand_total, status", FALSE)->from('quotes')->join('quote_items', 'quote_items.quote_id=quotes.id', 'left')->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')->group_by('quotes.id');

            if ($user) {
                $this->db->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->db->where('quote_items.product_id', $product);
            }
            if ($biller) {
                $this->db->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->db->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('quotes') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('quotes_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('customer'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->customer);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->status);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'quotes_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $qi = "( SELECT quote_id, product_id, GROUP_CONCAT(CONCAT({$this->db->dbprefix('quote_items')}.product_name, '__', {$this->db->dbprefix('quote_items')}.quantity) SEPARATOR '___') as item_nane from {$this->db->dbprefix('quote_items')} ";
            if ($product) {
                $qi .= " WHERE {$this->db->dbprefix('quote_items')}.product_id = {$product} ";
            }
            $qi .= " GROUP BY {$this->db->dbprefix('quote_items')}.quote_id ) FQI";
            $this->load->library('datatables');
            $this->datatables->select("date, reference_no, biller, customer, FQI.item_nane as iname, grand_total, status, {$this->db->dbprefix('quotes')}.id as id", FALSE)->from('quotes')->join($qi, 'FQI.quote_id=quotes.id', 'left')->join('warehouses', 'warehouses.id=quotes.warehouse_id', 'left')->group_by('quotes.id');

            if ($user) {
                $this->datatables->where('quotes.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FQI.product_id', $product, FALSE);
            }
            if ($biller) {
                $this->datatables->where('quotes.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('quotes.customer_id', $customer);
            }
            if ($warehouse) {
                $this->datatables->where('quotes.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('quotes.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('quotes') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }
    function getTransfersReport($pdf = NULL, $xls = NULL){
        if ($this->input->get('product')) {
            $product = $this->input->get('product');
        } else {
            $product = NULL;
        }

        if ($pdf || $xls) {

            $this->db->select($this->db->dbprefix('transfers') . ".date, transfer_no, (CASE WHEN " . $this->db->dbprefix('transfers') . ".status = 'completed' THEN  GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '<br>') ELSE GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('transfer_items') . ".product_name, ' (', " . $this->db->dbprefix('transfer_items') . ".quantity, ')') SEPARATOR '<br>') END) as iname, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, grand_total, " . $this->db->dbprefix('transfers') . ".status")->from('transfers')->join('transfer_items', 'transfer_items.transfer_id=transfers.id', 'left')->join('purchase_items', 'purchase_items.transfer_id=transfers.id', 'left')->group_by('transfers.id')->order_by('transfers.date desc');
            if ($product) {
                $this->db->where($this->db->dbprefix('purchase_items') . ".product_id", $product);
                $this->db->or_where($this->db->dbprefix('transfer_items') . ".product_id", $product);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('transfers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('transfer_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('warehouse') . ' (' . lang('from') . ')');
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('warehouse') . ' (' . lang('to') . ')');
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('status'));

                $row = 2;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->transfer_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->fname . ' (' . $data_row->fcode . ')');
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->tname . ' (' . $data_row->tcode . ')');
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->status);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                $filename = 'transfers_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->load->library('datatables');
            $this->datatables->select("{$this->db->dbprefix('transfers')}.date, transfer_no, (CASE WHEN {$this->db->dbprefix('transfers')}.status = 'completed' THEN  GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___') ELSE GROUP_CONCAT(CONCAT({$this->db->dbprefix('transfer_items')}.product_name, '__', {$this->db->dbprefix('transfer_items')}.quantity) SEPARATOR '___') END) as iname, from_warehouse_name as fname, from_warehouse_code as fcode, to_warehouse_name as tname,to_warehouse_code as tcode, grand_total, {$this->db->dbprefix('transfers')}.status, {$this->db->dbprefix('transfers')}.id as id", FALSE)->from('transfers')->join('transfer_items', 'transfer_items.transfer_id=transfers.id', 'left')->join('purchase_items', 'purchase_items.transfer_id=transfers.id', 'left')->group_by('transfers.id');
            if ($product) {
                $this->datatables->where(" (({$this->db->dbprefix('purchase_items')}.product_id = {$product}) OR ({$this->db->dbprefix('transfer_items')}.product_id = {$product})) ", NULL, FALSE);
            }
            $this->datatables->edit_column("fname", "$1 ($2)", "fname, fcode")->edit_column("tname", "$1 ($2)", "tname, tcode")->unset_column('fcode')->unset_column('tcode');
            echo $this->datatables->generate();
        }
    }
    function purchases(){
        $this->sma->checkPermissions('purchases');
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']      = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $bc                       = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('purchases_report')
            )
        );
        $meta                     = array(
            'page_title' => lang('purchases_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/purchases', $meta, $this->data);
    }
    function getPurchasesReport($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('purchases', TRUE);

        $user                 = $this->site->getUser();
        $company_id_sale_show = $user->biller_id;

        $check_user_group_id = $user->group_id;

        $product      = $this->input->get('product') ? $this->input->get('product') : NULL;
        $user         = $this->input->get('user') ? $this->input->get('user') : NULL;
        $supplier     = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $this->db->select("" . $this->db->dbprefix('purchases') . ".date, reference_no, " . $this->db->dbprefix('warehouses') . ".name as wname, supplier, GROUP_CONCAT(CONCAT(" . $this->db->dbprefix('purchase_items') . ".product_name, ' (', " . $this->db->dbprefix('purchase_items') . ".quantity, ')') SEPARATOR '\n') as iname, grand_total, paid, " . $this->db->dbprefix('purchases') . ".status", FALSE)->from('purchases')->join('purchase_items', 'purchase_items.purchase_id=purchases.id', 'left')->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')->group_by('purchases.id')->order_by('purchases.date desc');

            if ($user) {
                $this->db->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->db->where('purchase_items.product_id', $product);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('purchase_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('supplier'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('product_qty'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('grand_total'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('status'));

                $row     = 2;
                $total   = 0;
                $paid    = 0;
                $balance = 0;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wname);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->supplier);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->iname);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->grand_total);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->paid);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, ($data_row->grand_total - $data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->status);
                    $total += $data_row->grand_total;
                    $paid += $data_row->paid;
                    $balance += ($data_row->grand_total - $data_row->paid);
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row . ":H" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $paid);
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $balance);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'purchase_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            // $this->db->save_queries = TRUE;

            $pi = "( SELECT purchase_id, product_id, (GROUP_CONCAT(CONCAT({$this->db->dbprefix('purchase_items')}.product_name, '__', {$this->db->dbprefix('purchase_items')}.quantity) SEPARATOR '___')) as item_nane from {$this->db->dbprefix('purchase_items')} ";
            if ($product) {
                $pi .= " WHERE {$this->db->dbprefix('purchase_items')}.product_id = {$product} ";
            }
            $pi .= " GROUP BY {$this->db->dbprefix('purchase_items')}.purchase_id ) FPI";

            $this->load->library('datatables');
            $this->datatables->select("DATE_FORMAT({$this->db->dbprefix('purchases')}.date, '%Y-%m-%d') as date, reference_no, supplier, (FPI.item_nane) as iname, grand_total, paid, (grand_total-paid) as balance, {$this->db->dbprefix('purchases')}.status, {$this->db->dbprefix('purchases')}.id as id", FALSE)->from('purchases')->join($pi, 'FPI.purchase_id=purchases.id', 'left')->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');

            if (($check_user_group_id != 1) and ($check_user_group_id != 2)) {
                if(!empty($company_id_sale_show)){ 
                $si .= $this->datatables->where($this->db->dbprefix('purchases') . ".supplier_id", $company_id_sale_show);
                }
            }
            // ->where($this->db->dbprefix('purchases') . ".supplier_id", $company_id_sale_show);

            // ->group_by('purchases.id');

            if ($user) {
                $this->datatables->where('purchases.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FPI.product_id', $product, FALSE);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('purchases.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('purchases.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
            // echo $this->db->last_query();
        }
    }
    function payments(){
        $this->sma->checkPermissions('payments');
        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users']        = $this->reports_model->getStaff();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['pos_settings'] = POS ? $this->reports_model->getPOSSetting('biller') : FALSE;
        $bc                         = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('payments_report')
            )
        );
        $meta                       = array(
            'page_title' => lang('payments_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/payments', $meta, $this->data);
    }
    function getPaymentsReport($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('payments', TRUE);

        $user           = $this->input->get('user') ? $this->input->get('user') : NULL;
        $supplier       = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        $customer       = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $biller         = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $payment_ref    = $this->input->get('payment_ref') ? $this->input->get('payment_ref') : NULL;
        $paid_by        = $this->input->get('paid_by') ? $this->input->get('paid_by') : NULL;
        $sale_ref       = $this->input->get('sale_ref') ? $this->input->get('sale_ref') : NULL;
        $purchase_ref   = $this->input->get('purchase_ref') ? $this->input->get('purchase_ref') : NULL;
        $card           = $this->input->get('card') ? $this->input->get('card') : NULL;
        $cheque         = $this->input->get('cheque') ? $this->input->get('cheque') : NULL;
        $transaction_id = $this->input->get('tid') ? $this->input->get('tid') : NULL;
        $start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fsd($start_date);
            $end_date   = $this->sma->fsd($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }
        if ($pdf || $xls) {

            $this->db->select("" . $this->db->dbprefix('payments') . ".date, " . $this->db->dbprefix('payments') . ".reference_no as payment_ref, " . $this->db->dbprefix('sales') . ".reference_no as sale_ref, " . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, type")->from('payments')->join('sales', 'payments.sale_id=sales.id', 'left')->join('purchases', 'payments.purchase_id=purchases.id', 'left')->group_by('payments.id')->order_by('payments.date desc');

            if ($user) {
                $this->db->where('payments.created_by', $user);
            }
            if ($card) {
                $this->db->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->db->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->db->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->db->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->db->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->db->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->db->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->db->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->db->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($purchase_ref) {
                $this->db->like('purchases.reference_no', $purchase_ref, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('payments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('payments_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('payment_reference'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('sale_reference'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('purchase_reference'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('paid_by'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('type'));

                $row   = 2;
                $total = 0;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->payment_ref);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->sale_ref);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->purchase_ref);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, lang($data_row->paid_by));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->type);
                    if ($data_row->type == 'returned' || $data_row->type == 'sent') {
                        $total -= $data_row->amount;
                    } else {
                        $total += $data_row->amount;
                    }
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("F" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $total);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'payments_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->load->library('datatables');
            $this->datatables->select("DATE_FORMAT({$this->db->dbprefix('payments')}.date, '%Y-%m-%d') as date, " . $this->db->dbprefix('payments') . ".reference_no as payment_ref, " . $this->db->dbprefix('sales') . ".reference_no as sale_ref, " . $this->db->dbprefix('purchases') . ".reference_no as purchase_ref, paid_by, amount, type, {$this->db->dbprefix('payments')}.id as id")->from('payments')->join('sales', 'payments.sale_id=sales.id', 'left')->join('purchases', 'payments.purchase_id=purchases.id', 'left')->group_by('payments.id');

            if ($user) {
                $this->datatables->where('payments.created_by', $user);
            }
            if ($card) {
                $this->datatables->like('payments.cc_no', $card, 'both');
            }
            if ($cheque) {
                $this->datatables->where('payments.cheque_no', $cheque);
            }
            if ($transaction_id) {
                $this->datatables->where('payments.transaction_id', $transaction_id);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($supplier) {
                $this->datatables->where('purchases.supplier_id', $supplier);
            }
            if ($biller) {
                $this->datatables->where('sales.biller_id', $biller);
            }
            if ($customer) {
                $this->datatables->where('sales.customer_id', $customer);
            }
            if ($payment_ref) {
                $this->datatables->like('payments.reference_no', $payment_ref, 'both');
            }
            if ($paid_by) {
                $this->datatables->where('payments.paid_by', $paid_by);
            }
            if ($sale_ref) {
                $this->datatables->like('sales.reference_no', $sale_ref, 'both');
            }
            if ($purchase_ref) {
                $this->datatables->like('purchases.reference_no', $purchase_ref, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('payments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }
    function customers(){
        $this->sma->checkPermissions('customers');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('customers_report')
            )
        );
        $meta = array(
            'page_title' => lang('customers_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/customers', $meta, $this->data);
    }
    function getCustomers($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('customers', TRUE);

        if ($pdf || $xls) {

            $this->db->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count(" . $this->db->dbprefix('sales') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)->from("companies")->join('sales', 'sales.customer_id=companies.id')->where('companies.group_name', 'customer')->order_by('companies.company asc')->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('customers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_sales'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($data_row->total_amount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney($data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatMoney($data_row->balance));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'customers_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $s = "( SELECT customer_id, count(" . $this->db->dbprefix('sales') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('sales')} GROUP BY {$this->db->dbprefix('sales')}.customer_id ) FS";

            $this->load->library('datatables');
            $this->datatables->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, FS.total, FS.total_amount, FS.paid, FS.balance", FALSE)->from("companies")->join($s, 'FS.customer_id=companies.id')->where('companies.group_name', 'customer')->group_by('companies.id')->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/customer_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")->unset_column('id');
            echo $this->datatables->generate();
        }
    }
    function customer_report($user_id = NULL){
        $this->sma->checkPermissions('customers', TRUE);
        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_customer_selected"));
            admin_redirect('reports/customers');
        }

        $this->data['sales']         = $this->reports_model->getSalesTotals($user_id);
        $this->data['total_sales']   = $this->reports_model->getCustomerSales($user_id);
        $this->data['total_quotes']  = $this->reports_model->getCustomerQuotes($user_id);
        $this->data['total_returns'] = $this->reports_model->getCustomerReturns($user_id);
        $this->data['users']         = $this->reports_model->getStaff();
        $this->data['warehouses']    = $this->site->getAllWarehouses();
        $this->data['billers']       = $this->site->getAllCompanies('biller');

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc                    = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('customers_report')
            )
        );
        $meta                  = array(
            'page_title' => lang('customers_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/customer_report', $meta, $this->data);
    }
    function suppliers(){
        $this->sma->checkPermissions('suppliers');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('suppliers_report')
            )
        );
        $meta = array(
            'page_title' => lang('suppliers_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/suppliers', $meta, $this->data);
    }
    function getSuppliers($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('suppliers', TRUE);

        if ($pdf || $xls) {

            $this->db->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, count({$this->db->dbprefix('purchases')}.id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance", FALSE)->from("companies")->join('purchases', 'purchases.supplier_id=companies.id')->where('companies.group_name', 'supplier')->order_by('companies.company asc')->group_by('companies.id');

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('suppliers_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('company'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('phone'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('total_purchases'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('total_amount'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('paid'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('balance'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->phone);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->email);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($data_row->total));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($data_row->total_amount));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($data_row->paid));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($data_row->balance));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'suppliers_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $p = "( SELECT supplier_id, count(" . $this->db->dbprefix('purchases') . ".id) as total, COALESCE(sum(grand_total), 0) as total_amount, COALESCE(sum(paid), 0) as paid, ( COALESCE(sum(grand_total), 0) - COALESCE(sum(paid), 0)) as balance from {$this->db->dbprefix('purchases')} GROUP BY {$this->db->dbprefix('purchases')}.supplier_id ) FP";

            $this->load->library('datatables');
            $this->datatables->select($this->db->dbprefix('companies') . ".id as id, company, name, phone, email, FP.total, FP.total_amount, FP.paid, FP.balance", FALSE)->from("companies")->join($p, 'FP.supplier_id=companies.id')->where('companies.group_name', 'supplier')->group_by('companies.id')->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/supplier_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")->unset_column('id');
            echo $this->datatables->generate();
        }
    }
    function supplier_report($user_id = NULL){
        $this->sma->checkPermissions('suppliers', TRUE);
        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_supplier_selected"));
            admin_redirect('reports/suppliers');
        }

        $this->data['purchases']       = $this->reports_model->getPurchasesTotals($user_id);
        $this->data['total_purchases'] = $this->reports_model->getSupplierPurchases($user_id);
        $this->data['users']           = $this->reports_model->getStaff();
        $this->data['warehouses']      = $this->site->getAllWarehouses();

        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

        $this->data['user_id'] = $user_id;
        $bc                    = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('suppliers_report')
            )
        );
        $meta                  = array(
            'page_title' => lang('suppliers_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/supplier_report', $meta, $this->data);
    }
    function users(){
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('staff_report')
            )
        );
        $meta                = array(
            'page_title' => lang('staff_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/users', $meta, $this->data);
    }
    function getUsers(){
        $this->load->library('datatables');
        $this->datatables->select($this->db->dbprefix('users') . ".id as id, first_name, last_name, email, company, " . $this->db->dbprefix('groups') . ".name, active")->from("users")->join('groups', 'users.group_id=groups.id', 'left')->group_by('users.id')->where('company_id', NULL);
        if (!$this->Owner) {
            $this->datatables->where('group_id !=', 1);
        }
        $this->datatables->edit_column('active', '$1__$2', 'active, id')->add_column("Actions", "<div class='text-center'><a class=\"tip\" title='" . lang("view_report") . "' href='" . admin_url('reports/staff_report/$1') . "'><span class='label label-primary'>" . lang("view_report") . "</span></a></div>", "id")->unset_column('id');
        echo $this->datatables->generate();
    }
    function staff_report($user_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $cal = 0){

        if (!$user_id) {
            $this->session->set_flashdata('error', lang("no_user_selected"));
            admin_redirect('reports/users');
        }
        $this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $this->data['purchases']  = $this->reports_model->getStaffPurchases($user_id);
        $this->data['sales']      = $this->reports_model->getStaffSales($user_id);
        $this->data['billers']    = $this->site->getAllCompanies('biller');
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        if (!$year) {
            $year = date('Y');
        }
        if (!$month || $month == '#monthly-con') {
            $month = date('m');
        }
        if ($pdf) {
            if ($cal) {
                $this->monthly_sales($year, $pdf, $user_id);
            } else {
                $this->daily_sales($year, $month, $pdf, $user_id);
            }
        }
        $config = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => admin_url('reports/staff_report/' . $user_id),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable reports-table">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th class="text-center"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th class="text-center" colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th class="text-center"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $sales = $this->reports_model->getStaffDailySales($user_id, $year, $month);

        if (!empty($sales)) {
            foreach ($sales as $sale) {
                $daily_sale[$sale->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($sale->discount) . "</td></tr><tr><td>" . lang("product_tax") . "</td><td>" . $this->sma->formatMoney($sale->tax1) . "</td></tr><tr><td>" . lang("order_tax") . "</td><td>" . $this->sma->formatMoney($sale->tax2) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($sale->total) . "</td></tr></table>";
            }
        } else {
            $daily_sale = array();
        }
        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_sale);
        if ($this->input->get('pdf')) {
        }
        $this->data['year']    = $year;
        $this->data['month']   = $month;
        $this->data['msales']  = $this->reports_model->getStaffMonthlySales($user_id, $year);
        $this->data['user_id'] = $user_id;
        $bc                    = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('staff_report')
            )
        );
        $meta                  = array(
            'page_title' => lang('staff_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/staff_report', $meta, $this->data);
    }
    function getUserLogins($id = NULL, $pdf = NULL, $xls = NULL){
        if ($this->input->get('start_date')) {
            $login_start_date = $this->input->get('start_date');
        } else {
            $login_start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $login_end_date = $this->input->get('end_date');
        } else {
            $login_end_date = NULL;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date   = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        if ($pdf || $xls) {

            $this->db->select("login, ip_address, time")->from("user_logins")->where('user_id', $id)->order_by('time desc');
            if ($login_start_date) {
                $this->db->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", NULL, FALSE);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('staff_login_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('email'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('ip_address'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('time'));

                $row = 2;
                foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->login);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->ip_address);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrld($data_row->time));
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('C2:C' . $row)->getAlignment()->setWrapText(true);
                $filename = 'staff_login_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->load->library('datatables');
            $this->datatables->select("login, ip_address, DATE_FORMAT(time, '%Y-%m-%d') as time")->from("user_logins")->where('user_id', $id);
            if ($login_start_date) {
                $this->datatables->where("time BETWEEN '{$login_start_date}' and '{$login_end_date}'", NULL, FALSE);
            }
            echo $this->datatables->generate();
        }
    }
    function getCustomerLogins($id = NULL){
        if ($this->input->get('login_start_date')) {
            $login_start_date = $this->input->get('login_start_date');
        } else {
            $login_start_date = NULL;
        }
        if ($this->input->get('login_end_date')) {
            $login_end_date = $this->input->get('login_end_date');
        } else {
            $login_end_date = NULL;
        }
        if ($login_start_date) {
            $login_start_date = $this->sma->fld($login_start_date);
            $login_end_date   = $login_end_date ? $this->sma->fld($login_end_date) : date('Y-m-d H:i:s');
        }
        $this->load->library('datatables');
        $this->datatables->select("login, ip_address, time")->from("user_logins")->where('customer_id', $id);
        if ($login_start_date) {
            $this->datatables->where('time BETWEEN "' . $login_start_date . '" and "' . $login_end_date . '"');
        }
        echo $this->datatables->generate();
    }
    function profit_loss($start_date = NULL, $end_date = NULL){
        $this->sma->checkPermissions('profit_loss');
        if (!$start_date) {
            $start      = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end      = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $this->data['total_purchases']       = $this->reports_model->getTotalPurchases($start, $end);
        $this->data['total_sales']           = $this->reports_model->getTotalSales($start, $end);
        $this->data['total_return_sales']    = $this->reports_model->getTotalReturnSales($start, $end);
        $this->data['total_expenses']        = $this->reports_model->getTotalExpenses($start, $end);
        $this->data['total_paid']            = $this->reports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received']        = $this->reports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash']   = $this->reports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc']     = $this->reports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->reports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp']    = $this->reports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->reports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned']        = $this->reports_model->getTotalReturnedAmount($start, $end);
        $this->data['start']                 = urldecode($start_date);
        $this->data['end']                   = urldecode($end_date);

        $warehouses = $this->site->getAllWarehouses();
        foreach ($warehouses as $warehouse) {
            $total_purchases     = $this->reports_model->getTotalPurchases($start, $end, $warehouse->id);
            $total_sales         = $this->reports_model->getTotalSales($start, $end, $warehouse->id);
            $total_returns       = $this->reports_model->getTotalReturnSales($start, $end, $warehouse->id);
            $total_expenses      = $this->reports_model->getTotalExpenses($start, $end, $warehouse->id);
            $warehouses_report[] = array(
                'warehouse' => $warehouse,
                'total_purchases' => $total_purchases,
                'total_sales' => $total_sales,
                'total_returns' => $total_returns,
                'total_expenses' => $total_expenses
            );
        }
        $this->data['warehouses_report'] = $warehouses_report;

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('profit_loss')
            )
        );
        $meta = array(
            'page_title' => lang('profit_loss'),
            'bc' => $bc
        );
        $this->page_construct('reports/profit_loss', $meta, $this->data);
    }
    function profit_loss_pdf($start_date = NULL, $end_date = NULL){
        $this->sma->checkPermissions('profit_loss');
        if (!$start_date) {
            $start      = $this->db->escape(date('Y-m') . '-1');
            $start_date = date('Y-m') . '-1';
        } else {
            $start = $this->db->escape(urldecode($start_date));
        }
        if (!$end_date) {
            $end      = $this->db->escape(date('Y-m-d H:i'));
            $end_date = date('Y-m-d H:i');
        } else {
            $end = $this->db->escape(urldecode($end_date));
        }

        $this->data['total_purchases']       = $this->reports_model->getTotalPurchases($start, $end);
        $this->data['total_sales']           = $this->reports_model->getTotalSales($start, $end);
        $this->data['total_expenses']        = $this->reports_model->getTotalExpenses($start, $end);
        $this->data['total_paid']            = $this->reports_model->getTotalPaidAmount($start, $end);
        $this->data['total_received']        = $this->reports_model->getTotalReceivedAmount($start, $end);
        $this->data['total_received_cash']   = $this->reports_model->getTotalReceivedCashAmount($start, $end);
        $this->data['total_received_cc']     = $this->reports_model->getTotalReceivedCCAmount($start, $end);
        $this->data['total_received_cheque'] = $this->reports_model->getTotalReceivedChequeAmount($start, $end);
        $this->data['total_received_ppp']    = $this->reports_model->getTotalReceivedPPPAmount($start, $end);
        $this->data['total_received_stripe'] = $this->reports_model->getTotalReceivedStripeAmount($start, $end);
        $this->data['total_returned']        = $this->reports_model->getTotalReturnedAmount($start, $end);
        $this->data['start']                 = urldecode($start_date);
        $this->data['end']                   = urldecode($end_date);

        $warehouses = $this->site->getAllWarehouses();
        foreach ($warehouses as $warehouse) {
            $total_purchases     = $this->reports_model->getTotalPurchases($start, $end, $warehouse->id);
            $total_sales         = $this->reports_model->getTotalSales($start, $end, $warehouse->id);
            $warehouses_report[] = array(
                'warehouse' => $warehouse,
                'total_purchases' => $total_purchases,
                'total_sales' => $total_sales
            );
        }
        $this->data['warehouses_report'] = $warehouses_report;

        $html = $this->load->view($this->theme . 'reports/profit_loss_pdf', $this->data, true);
        $name = lang("profit_loss") . "-" . str_replace(array(
            '-',
            ' ',
            ':'
        ), '_', $this->data['start']) . "-" . str_replace(array(
            '-',
            ' ',
            ':'
        ), '_', $this->data['end']) . ".pdf";
        $this->sma->generate_pdf($html, $name, false, false, false, false, false, 'L');
    }
    function register(){
        $this->sma->checkPermissions('register');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $bc                  = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('register_report')
            )
        );
        $meta                = array(
            'page_title' => lang('register_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/register', $meta, $this->data);
    }
    function getRrgisterlogs($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('register', TRUE);
        if ($this->input->get('user')) {
            $user = $this->input->get('user');
        } else {
            $user = NULL;
        }
        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            $start_date = NULL;
        }
        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            $end_date = NULL;
        }
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {

            $this->db->select("date, closed_at, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, ' (', users.email, ')') as user, cash_in_hand, total_cc_slips, total_cheques, total_cash, total_cc_slips_submitted, total_cheques_submitted,total_cash_submitted, note", FALSE)->from("pos_register")->join('users', 'users.id=pos_register.user_id', 'left')->order_by('date desc');
            //->where('status', 'close');

            if ($user) {
                $this->db->where('pos_register.user_id', $user);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('register_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('open_time'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('close_time'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('user'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('cash_in_hand'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('cc_slips'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('cheques'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('total_cash'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('cc_slips_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('cheques_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('total_cash_submitted'));
                $this->excel->getActiveSheet()->SetCellValue('K1', lang('note'));

                $row = 2;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->closed_at);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->user);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->cash_in_hand);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->total_cc_slips);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->total_cheques);
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $data_row->total_cash);
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $data_row->total_cc_slips_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $data_row->total_cheques_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $data_row->total_cash_submitted);
                    $this->excel->getActiveSheet()->SetCellValue('K' . $row, $data_row->note);
                    if ($data_row->total_cash_submitted < $data_row->total_cash || $data_row->total_cheques_submitted < $data_row->total_cheques || $data_row->total_cc_slips_submitted < $data_row->total_cc_slips) {
                        $this->excel->getActiveSheet()->getStyle('A' . $row . ':K' . $row)->applyFromArray(array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array(
                                    'rgb' => 'F2DEDE'
                                )
                            )
                        ));
                    }
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(35);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'register_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->load->library('datatables');
            $this->datatables->select("date, closed_at, CONCAT(" . $this->db->dbprefix('users') . ".first_name, ' ', " . $this->db->dbprefix('users') . ".last_name, '<br>', " . $this->db->dbprefix('users') . ".email) as user, cash_in_hand, CONCAT(total_cc_slips, ' (', total_cc_slips_submitted, ')'), CONCAT(total_cheques, ' (', total_cheques_submitted, ')'), CONCAT(total_cash, ' (', total_cash_submitted, ')'), note", FALSE)->from("pos_register")->join('users', 'users.id=pos_register.user_id', 'left');

            if ($user) {
                $this->datatables->where('pos_register.user_id', $user);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }
    public function expenses($id = null){
        $this->sma->checkPermissions();
        $this->data['error']      = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['users']      = $this->reports_model->getStaff();
        $this->data['categories'] = $this->reports_model->getExpenseCategories();
        $bc                       = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('expenses')
            )
        );
        $meta                     = array(
            'page_title' => lang('expenses'),
            'bc' => $bc
        );
        $this->page_construct('reports/expenses', $meta, $this->data);
    }
    public function getExpensesReport($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('expenses');

        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $category     = $this->input->get('category') ? $this->input->get('category') : NULL;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $note         = $this->input->get('note') ? $this->input->get('note') : NULL;
        $user         = $this->input->get('user') ? $this->input->get('user') : NULL;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {

            $this->db->select("date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)->from('expenses')->join('users', 'users.id=expenses.created_by', 'left')->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')->group_by('expenses.id');

            if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
                $this->db->where('created_by', $this->session->userdata('user_id'));
            }

            if ($note) {
                $this->db->like('note', $note, 'both');
            }
            if ($reference_no) {
                $this->db->like('reference', $reference_no, 'both');
            }
            if ($category) {
                $this->db->where('category_id', $category);
            }
            if ($warehouse) {
                $this->db->where('expenses.warehouse_id', $warehouse);
            }
            if ($user) {
                $this->db->where('created_by', $user);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('expenses_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('category'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('amount'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('created_by'));

                $row   = 2;
                $total = 0;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->category);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->amount);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $data_row->note);
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->created_by);
                    $total += $data_row->amount;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("D" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $total);

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $filename = 'expenses_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->load->library('datatables');
            $this->datatables->select("DATE_FORMAT(date, '%Y-%m-%d') as date, reference, {$this->db->dbprefix('expense_categories')}.name as category, amount, note, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, attachment, {$this->db->dbprefix('expenses')}.id as id", false)->from('expenses')->join('users', 'users.id=expenses.created_by', 'left')->join('expense_categories', 'expense_categories.id=expenses.category_id', 'left')->group_by('expenses.id');

            if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
                $this->datatables->where('created_by', $this->session->userdata('user_id'));
            }

            if ($note) {
                $this->datatables->like('note', $note, 'both');
            }
            if ($reference_no) {
                $this->datatables->like('reference', $reference_no, 'both');
            }
            if ($category) {
                $this->datatables->where('category_id', $category);
            }
            if ($warehouse) {
                $this->datatables->where('expenses.warehouse_id', $warehouse);
            }
            if ($user) {
                $this->datatables->where('created_by', $user);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }
    function daily_purchases($warehouse_id = NULL, $year = NULL, $month = NULL, $pdf = NULL, $user_id = NULL){
        $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $config              = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => admin_url('reports/daily_purchases/' . ($warehouse_id ? $warehouse_id : 0)),
            'month_type' => 'long',
            'day_type' => 'long'
        );

        $config['template'] = '{table_open}<div class="table-responsive"><table border="0" cellpadding="0" cellspacing="0" class="table table-bordered dfTable">{/table_open}
        {heading_row_start}<tr>{/heading_row_start}
        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th colspan="{colspan}" id="month_year">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
        {heading_row_end}</tr>{/heading_row_end}
        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td class="cl_wday">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}
        {cal_row_start}<tr class="days">{/cal_row_start}
        {cal_cell_start}<td class="day">{/cal_cell_start}
        {cal_cell_content}
        <div class="day_num">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content}
        {cal_cell_content_today}
        <div class="day_num highlight">{day}</div>
        <div class="content">{content}</div>
        {/cal_cell_content_today}
        {cal_cell_no_content}<div class="day_num">{day}</div>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="day_num highlight">{day}</div>{/cal_cell_no_content_today}
        {cal_cell_blank}&nbsp;{/cal_cell_blank}
        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}
        {table_close}</table></div>{/table_close}';

        $this->load->library('calendar', $config);
        $purchases = $user_id ? $this->reports_model->getStaffDailyPurchases($user_id, $year, $month, $warehouse_id) : $this->reports_model->getDailyPurchases($year, $month, $warehouse_id);

        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $daily_purchase[$purchase->date] = "<table class='table table-bordered table-hover table-striped table-condensed data' style='margin:0;'><tr><td>" . lang("discount") . "</td><td>" . $this->sma->formatMoney($purchase->discount) . "</td></tr><tr><td>" . lang("shipping") . "</td><td>" . $this->sma->formatMoney($purchase->shipping) . "</td></tr><tr><td>" . lang("product_tax") . "</td><td>" . $this->sma->formatMoney($purchase->tax1) . "</td></tr><tr><td>" . lang("order_tax") . "</td><td>" . $this->sma->formatMoney($purchase->tax2) . "</td></tr><tr><td>" . lang("total") . "</td><td>" . $this->sma->formatMoney($purchase->total) . "</td></tr></table>";
            }
        } else {
            $daily_purchase = array();
        }

        $this->data['calender'] = $this->calendar->generate($year, $month, $daily_purchase);
        $this->data['year']     = $year;
        $this->data['month']    = $month;
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/daily', $this->data, true);
            $name = lang("daily_purchases") . "_" . $year . "_" . $month . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->site->getAllWarehouses();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc                          = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('daily_purchases_report')
            )
        );
        $meta                        = array(
            'page_title' => lang('daily_purchases_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/daily_purchases', $meta, $this->data);
    }
    function monthly_purchases($warehouse_id = NULL, $year = NULL, $pdf = NULL, $user_id = NULL){
        $this->sma->checkPermissions();
        if (!$this->Owner && !$this->Admin && $this->session->userdata('warehouse_id')) {
            $warehouse_id = $this->session->userdata('warehouse_id');
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user_id = $this->session->userdata('user_id');
        }
        $this->load->language('calendar');
        $this->data['error']     = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['year']      = $year;
        $this->data['purchases'] = $user_id ? $this->reports_model->getStaffMonthlyPurchases($user_id, $year, $warehouse_id) : $this->reports_model->getMonthlyPurchases($year, $warehouse_id);
        if ($pdf) {
            $html = $this->load->view($this->theme . 'reports/monthly', $this->data, true);
            $name = lang("monthly_purchases") . "_" . $year . ".pdf";
            $html = str_replace('<p class="introtext">' . lang("reports_calendar_text") . '</p>', '', $html);
            $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'L');
        }
        $this->data['warehouses']    = $this->site->getAllWarehouses();
        $this->data['warehouse_id']  = $warehouse_id;
        $this->data['sel_warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        $bc                          = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('monthly_purchases_report')
            )
        );
        $meta                        = array(
            'page_title' => lang('monthly_purchases_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/monthly_purchases', $meta, $this->data);
    }
    function adjustments($warehouse_id = NULL){
        $this->sma->checkPermissions('products');

        $this->data['users']      = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('adjustments_report')
            )
        );
        $meta                = array(
            'page_title' => lang('adjustments_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/adjustments', $meta, $this->data);
    }
    function getAdjustmentReport($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('products', TRUE);

        $product      = $this->input->get('product') ? $this->input->get('product') : NULL;
        $warehouse    = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $user         = $this->input->get('user') ? $this->input->get('user') : NULL;
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : NULL;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        $serial       = $this->input->get('serial') ? $this->input->get('serial') : NULL;

        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        if ($pdf || $xls) {

            $ai = "( SELECT adjustment_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('products')}.name, ' (', (CASE WHEN {$this->db->dbprefix('adjustment_items')}.type  = 'subtraction' THEN (0-{$this->db->dbprefix('adjustment_items')}.quantity) ELSE {$this->db->dbprefix('adjustment_items')}.quantity END), ')') SEPARATOR '\n') as item_nane from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id GROUP BY {$this->db->dbprefix('adjustment_items')}.adjustment_id ) FAI";

            $this->db->select("DATE_FORMAT(date, '%Y-%m-%d') as date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, FAI.item_nane as iname, {$this->db->dbprefix('adjustments')}.id as id", FALSE)->from('adjustments')->join($ai, 'FAI.adjustment_id=adjustments.id', 'left')->join('users', 'users.id=adjustments.created_by', 'left')->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left');

            if ($user) {
                $this->db->where('adjustments.created_by', $user);
            }
            if ($product) {
                $this->db->where('FAI.product_id', $product);
            }
            if ($serial) {
                $this->db->like('FAI.serial_no', $serial);
            }
            if ($warehouse) {
                $this->db->where('adjustments.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->db->like('adjustments.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->db->where($this->db->dbprefix('adjustments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {

                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('adjustments_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('created_by'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('products'));

                $row = 2;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->wh_name);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->created_by);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->decode_html($data_row->note));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $data_row->iname);
                    $row++;
                }

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $this->excel->getActiveSheet()->getStyle('F2:F' . $row)->getAlignment()->setWrapText(true);
                $filename = 'adjustments_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $ai = "( SELECT adjustment_id, product_id, serial_no, GROUP_CONCAT(CONCAT({$this->db->dbprefix('products')}.name, '__', (CASE WHEN {$this->db->dbprefix('adjustment_items')}.type  = 'subtraction' THEN (0-{$this->db->dbprefix('adjustment_items')}.quantity) ELSE {$this->db->dbprefix('adjustment_items')}.quantity END)) SEPARATOR '___') as item_nane from {$this->db->dbprefix('adjustment_items')} LEFT JOIN {$this->db->dbprefix('products')} ON {$this->db->dbprefix('products')}.id={$this->db->dbprefix('adjustment_items')}.product_id ";
            if ($product || $serial) {
                $ai .= " WHERE ";
            }
            if ($product) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.product_id = {$product} ";
            }
            if ($product && $serial) {
                $ai .= " AND ";
            }
            if ($serial) {
                $ai .= " {$this->db->dbprefix('adjustment_items')}.serial_no LIKe '%{$serial}%' ";
            }
            $ai .= " GROUP BY {$this->db->dbprefix('adjustment_items')}.adjustment_id ) FAI";
            $this->load->library('datatables');
            $this->datatables->select("DATE_FORMAT(date, '%Y-%m-%d') as date, reference_no, warehouses.name as wh_name, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note, FAI.item_nane as iname, {$this->db->dbprefix('adjustments')}.id as id", FALSE)->from('adjustments')->join($ai, 'FAI.adjustment_id=adjustments.id', 'left')->join('users', 'users.id=adjustments.created_by', 'left')->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left');

            if ($user) {
                $this->datatables->where('adjustments.created_by', $user);
            }
            if ($product) {
                $this->datatables->where('FAI.product_id', $product);
            }
            if ($serial) {
                $this->datatables->like('FAI.serial_no', $serial);
            }
            if ($warehouse) {
                $this->datatables->where('adjustments.warehouse_id', $warehouse);
            }
            if ($reference_no) {
                $this->datatables->like('adjustments.reference_no', $reference_no, 'both');
            }
            if ($start_date) {
                $this->datatables->where($this->db->dbprefix('adjustments') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }
    function get_deposits($company_id = NULL){
        $this->sma->checkPermissions('customers', TRUE);
        $this->load->library('datatables');
        $this->datatables->select("date, amount, paid_by, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note", FALSE)->from("deposits")->join('users', 'users.id=deposits.created_by', 'left')->where($this->db->dbprefix('deposits') . '.company_id', $company_id);
        echo $this->datatables->generate();
    }
    function tax(){
        $this->sma->checkPermissions();
        $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : NULL;
        $end_date   = $this->input->post('end_date') ? $this->input->post('end_date') : NULL;
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }
        $this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['warehouses']   = $this->site->getAllWarehouses();
        $this->data['billers']      = $this->site->getAllCompanies('biller');
        $this->data['sale_tax']     = $this->reports_model->getSalesTax($start_date, $end_date);
        $this->data['purchase_tax'] = $this->reports_model->getPurchasesTax($start_date, $end_date);
        $bc                         = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => admin_url('reports'),
                'page' => lang('reports')
            ),
            array(
                'link' => '#',
                'page' => lang('tax_report')
            )
        );
        $meta                       = array(
            'page_title' => lang('tax_report'),
            'bc' => $bc
        );
        $this->page_construct('reports/tax', $meta, $this->data);
    }
    function get_sale_taxes($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('tax', TRUE);
        $biller     = $this->input->get('biller') ? $this->input->get('biller') : NULL;
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {

            $this->db->select("date, reference_no, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, biller, igst, cgst, sgst, product_tax, order_tax, grand_total, paid, payment_status")->from('sales')->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')->order_by('date desc');

            if ($biller) {
                $this->db->where('biller_id', $biller);
            }
            if ($warehouse) {
                $this->db->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('biller'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('igst'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('cgst'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('sgst'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('product_tax'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('order_tax'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('grand_total'));

                $row   = 2;
                $total = $order_tax = $product_tax = $igst = $cgst = $sgst = 0;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->warehouse);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->biller);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($data_row->igst));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($data_row->cgst));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($data_row->sgst));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($data_row->product_tax));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($data_row->order_tax));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($data_row->grand_total));
                    $igst += $data_row->igst;
                    $cgst += $data_row->cgst;
                    $sgst += $data_row->sgst;
                    $product_tax += $data_row->product_tax;
                    $order_tax += $data_row->order_tax;
                    $total += $data_row->grand_total;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("E" . $row . ":J" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($igst));
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($cgst));
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($sgst));
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($product_tax));
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($order_tax));
                $this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($total));

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'sale_tax_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->load->library('datatables');
            $this->datatables->select("DATE_FORMAT(date, '%Y-%m-%d') as date, reference_no, sale_status, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, biller, " . ($this->Settings->indian_gst ? "igst, cgst, sgst," : "") . " product_tax, order_tax, grand_total, {$this->db->dbprefix('sales')}.id as id", FALSE)->from('sales')->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left');
            if ($biller) {
                $this->datatables->where('biller_id', $biller);
            }
            if ($warehouse) {
                $this->datatables->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }
    function get_purchase_taxes($pdf = NULL, $xls = NULL){
        $this->sma->checkPermissions('tax', TRUE);
        $supplier   = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        $warehouse  = $this->input->get('warehouse') ? $this->input->get('warehouse') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;
        if ($start_date) {
            $start_date = $this->sma->fld($start_date);
            $end_date   = $this->sma->fld($end_date);
        }

        if ($pdf || $xls) {

            $this->db->select("date, reference_no, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, supplier, igst, cgst, sgst, product_tax, order_tax, grand_total, paid")->from('purchases')->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')->order_by('purchases.date desc');

            if ($supplier) {
                $this->db->where('supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->db->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->db->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('sales_report'));
                $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                $this->excel->getActiveSheet()->SetCellValue('D1', lang('supplier'));
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('igst'));
                $this->excel->getActiveSheet()->SetCellValue('F1', lang('cgst'));
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('sgst'));
                $this->excel->getActiveSheet()->SetCellValue('H1', lang('product_tax'));
                $this->excel->getActiveSheet()->SetCellValue('I1', lang('order_tax'));
                $this->excel->getActiveSheet()->SetCellValue('J1', lang('grand_total'));

                $row   = 2;
                $total = $order_tax = $product_tax = $igst = $cgst = $sgst = 0;
                foreach ($data as $data_row) {
                    $source = $data_row->date;
                    $date   = new DateTime($source);
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $date->format('d-M-Y'));
                    // $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($data_row->date));
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->reference_no);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $data_row->warehouse);
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $data_row->supplier);
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($data_row->igst));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($data_row->cgst));
                    $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($data_row->sgst));
                    $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($data_row->product_tax));
                    $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($data_row->order_tax));
                    $this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($data_row->grand_total));
                    $igst += $data_row->igst;
                    $cgst += $data_row->cgst;
                    $sgst += $data_row->sgst;
                    $product_tax += $data_row->product_tax;
                    $order_tax += $data_row->order_tax;
                    $total += $data_row->grand_total;
                    $row++;
                }
                $this->excel->getActiveSheet()->getStyle("E" . $row . ":J" . $row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($igst));
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($cgst));
                $this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($sgst));
                $this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($product_tax));
                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($order_tax));
                $this->excel->getActiveSheet()->SetCellValue('J' . $row, $this->sma->formatDecimal($total));

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                $filename = 'purchase_tax_report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->load->library('datatables');
            $this->datatables->select("DATE_FORMAT(date, '%Y-%m-%d') as date, reference_no, status, CONCAT({$this->db->dbprefix('warehouses')}.name, ' (', {$this->db->dbprefix('warehouses')}.code, ')') as warehouse, supplier, " . ($this->Settings->indian_gst ? "igst, cgst, sgst," : "") . " product_tax, order_tax, grand_total, {$this->db->dbprefix('purchases')}.id as id", FALSE)->from('purchases')->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');
            if ($supplier) {
                $this->datatables->where('supplier_id', $supplier);
            }
            if ($warehouse) {
                $this->datatables->where('warehouse_id', $warehouse);
            }
            if ($start_date) {
                $this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
            }

            echo $this->datatables->generate();
        }
    }
    public function expiry_reports(){
        $this->data['own_company'] = $this->site->GetAllSupplierList2();

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('reports')
            )
        );

        $supplier_id  = $_REQUEST['own_company'];
        $range_date = $_REQUEST['start_date'];
        $date = date('Y-m-d', strtotime(str_replace('/', '-', $range_date)));

        if (!empty($supplier_id) && !empty($range_date)) {
            $this->data['data'] = $this->db->query("select sma_purchase_items.product_id, sma_purchase_items.product_name, sma_purchase_items.batch, sma_purchase_items.quantity_balance, sma_purchase_items.net_unit_cost, sma_purchase_items.price, sma_purchase_items.dropship, sma_purchase_items.crossdock, sma_purchase_items.mrp, STR_TO_DATE(sma_purchase_items.`expiry`,'%d/%m/%Y') AS `expiry_date`, DATEDIFF(STR_TO_DATE(sma_purchase_items.`expiry`,'%d/%m/%Y'), CURDATE()) AS expiry_in_days, sma_purchases.`supplier` from `sma_purchase_items` left join `sma_purchases` on sma_purchase_items.purchase_id = sma_purchases.id where sma_purchases.`supplier_id` = '$supplier_id' and sma_purchase_items.quantity_balance != '0' and date(STR_TO_DATE(sma_purchase_items.`expiry`,'%d/%m/%Y')) between DATE('2000-01-01') and  DATE(' $date')")->result_array();
        }

        $this->page_construct('reports/expiry_report', $meta, $this->data);
    }
    public function fbr_sale_report(){

        $this->data['own_company'] = $this->site->GetAllSupplierList2();

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('reports')
            )
        );

        $range_date = $_REQUEST['start_date'];
        $range_date2 = $_REQUEST['end_date'];
        $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $range_date)));
        $end_date = date('Y-m-d', strtotime(str_replace('/', '-', $range_date2)));

        $query = $this->db->query("SELECT sma_own_companies.companyname, sma_companies.gst_no, sma_companies.cf1, sma_sales.reference_no, sma_sales.date, sma_sales.po_number, sma_companies.name, sma_sale_items.product_id, sma_sale_items.product_name, sma_products.hsn_code, sma_sale_items.quantity, sma_sale_items.product_unit_code, sma_sale_items.net_unit_price, IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship))) AS 'sale_price', ( IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship)))*sma_sale_items.quantity ) AS 'value_excl_tax', sma_sale_items.tax, sma_sale_items.item_tax, sma_sale_items.further_tax, sma_sale_items.fed_tax, ( IF(sma_companies.sales_type = 'consignment', sma_sale_items.unit_price, IF(sma_companies.sales_type = 'crossdock', sma_sale_items.crossdock, IF(sma_companies.sales_type = 'dropship', sma_sale_items.dropship, sma_sale_items.dropship)))*sma_sale_items.quantity + (sma_sale_items.tax + sma_sale_items.further_tax + sma_sale_items.fed_tax) ) AS total_tax, sma_sale_items.discount_one, sma_sale_items.discount_two, sma_sale_items.discount_three, sma_sale_items.discount, sma_sale_items.subtotal, IF(sma_tax_rates.type = '1', 'GST', IF(sma_tax_rates.code = 'exp', 'Exempted', '3rd Schdule')) AS 'remarks', IF(sma_tax_rates.type = '1', 0, IF(sma_tax_rates.code = 'exp', 0, sma_sale_items.mrp / 1.17)) AS 'mrp_excl_tax', IF(sma_tax_rates.type = '1', 0, IF(sma_tax_rates.code = 'exp', 0, ( sma_sale_items.mrp / 1.17 ) *sma_sale_items.quantity)) AS 'value_third_sch', sma_sale_items.mrp, sma_sale_items.expiry, sma_sale_items.batch, sma_brands.name AS 'brand', sma_sale_items.warehouse_id AS 'warehouse_id', sma_products.carton_size, sma_products.company_code FROM sma_sales LEFT JOIN sma_sale_items ON sma_sales.id = sma_sale_items.sale_id LEFT JOIN sma_companies ON sma_companies.id = sma_sales.customer_id LEFT JOIN sma_tax_rates ON sma_sale_items.tax_rate_id = sma_tax_rates.id LEFT JOIN sma_own_companies ON sma_sales.own_company = sma_own_companies.id LEFT JOIN sma_products ON sma_products.id = sma_sale_items.product_id LEFT JOIN sma_brands ON sma_brands.id = sma_products.brand WHERE sma_sales.date >= '$start_date' AND sma_sales.date <= '$end_date' AND sma_companies.sales_type = 'consignment'")->result_array();

        $this->db->truncate('sma_fbr_sale_report');

        //  echo '<pre>';

        foreach ($query as $data_row) {

            $date   = $data_row['date'];

            $discount_two   = ($data_row['value_excl_tax'] * $data_row['discount_two']) / 100;
            $discount_three = ($data_row['value_excl_tax'] * $data_row['discount_three']) / 100;

            $insert_data = [
                'own_company' => $data_row['companyname'],
                'customer_ntn' => $data_row['cf1'],
                'invoice_no' => $data_row['reference_no'],
                'sale_date' => $date,
                'po_number' => $data_row['po_number'],
                'customer_name' => $data_row['name'],
                'product_id' => $data_row['product_id'],
                'product_name' => $data_row['product_name'],
                'hsn_code' => $data_row['hsn_code'],
                'quantity' => $data_row['quantity'],
                'uom' => $data_row['product_unit_code'],
                'price_exc_tax' => $data_row['net_unit_price'],
                'selling_price' => $data_row['sale_price'],
                'value_ex_tax' => $data_row['value_excl_tax'],
                'tax' => $data_row['tax'],
                'item_tax' => $data_row['item_tax'],
                'further_tax' => $data_row['further_tax'],
                'fed_tax' => $data_row['fed_tax'],
                'total_incl_all_taxes' => "",
                'sales_incentive' => $data_row['discount_one'],
                'trade_discount' => $data_row['discount_two'],
                'customer_discount' => $data_row['discount_three'],
                'total_discount' => $data_row['discount'],
                'sub_total' => $data_row['subtotal'],
                'remarks' => $data_row['remarks'],
                'mrp_excluding_tax' => $data_row['mrp_excl_tax'],
                'mrp_third_schedule' => $data_row['value_third_sch'],
                'mrp' => $data_row['mrp'],
                'expiry_date' => $data_row['expiry'],
                'batch' => $data_row['batch'],
                'brand' => $data_row['brand'],
                'gst_no' => $data_row['gst_no'],
            ];

            $this->db->insert('sma_fbr_sale_report', $insert_data);
        }

        $get_all_invoices = $this->db->query("SELECT DISTINCT(invoice_no)  FROM `sma_fbr_sale_report`")->result_array();

        $all_data = [];
        $all_data_2 = [];
        $all_data_3 = [];

        foreach ($get_all_invoices as $inv) {

            $invoice_no = $inv["invoice_no"];
            $get_data_1 = $this->db->query("SELECT id AS sr, tax, sale_date, customer_ntn AS buyer_ntn, `customer_name` AS company_name, IF(`gst_no` = ' ', 'Unregistered', 'Registered') AS buyer_type, product_id, invoice_no, remarks AS sale_type, hsn_code, uom, quantity FROM `sma_fbr_sale_report` WHERE invoice_no = '$invoice_no' GROUP BY remarks,hsn_code")->result_array();

            array_push($all_data, $get_data_1);
        }

        $i = 1;
        foreach ($all_data as $item) {

            foreach ($item as $item_2) {

                $invoice_no = $item_2['invoice_no'];
                $sale_type = $item_2['sale_type'];
                $hsn_code = $item_2['hsn_code'];

                $get_total_ex_tax = $this->db->query("SELECT SUM(value_ex_tax) as value_of_sales_excluding_tax FROM `sma_fbr_sale_report` WHERE invoice_no = '$invoice_no' AND remarks = '$sale_type' AND hsn_code = '$hsn_code'")->result_array();
                $value_of_sales_excluding_tax = $get_total_ex_tax[0]['value_of_sales_excluding_tax'];

                $get_total_qty = $this->db->query("SELECT SUM(quantity) AS total_quantity FROM `sma_fbr_sale_report` WHERE invoice_no = '$invoice_no' AND remarks = '$sale_type' AND hsn_code = '$hsn_code'")->result_array();
                $total_quantity = $get_total_qty[0]['total_quantity'];


                $get_total_item_tax_query = $this->db->query("SELECT SUM(item_tax) AS total_item_tax FROM `sma_fbr_sale_report` WHERE invoice_no = '$invoice_no' AND remarks = '$sale_type' AND hsn_code = '$hsn_code'")->result_array();
                $total_item_tax = $get_total_item_tax_query[0]['total_item_tax'];

                $get_further_tax = $this->db->query("SELECT SUM(further_tax) AS total_further_tax FROM `sma_fbr_sale_report` WHERE invoice_no = '$invoice_no' AND remarks = '$sale_type' AND hsn_code = '$hsn_code'")->result_array();
                $total_further_tax = $get_further_tax[0]['total_further_tax'];

                $mrp_third_schedule = "";
                if ($sale_type === '3rd Schdule') {
                    $mrp_third_query = $this->db->query("SELECT SUM(mrp_third_schedule) AS total_mrp_third_schedule FROM `sma_fbr_sale_report` WHERE invoice_no = '$invoice_no' AND remarks = '$sale_type' AND hsn_code = '$hsn_code'")->result_array();
                    $mrp_third_schedule = $mrp_third_query[0]['total_mrp_third_schedule'];
                } else {
                    $mrp_third_schedule = 0;
                }

                $tbl_data = [
                    'sr' => $i,
                    'buyer_ntn' => $item_2['buyer_ntn'],
                    'buyer_cnic' => '',
                    'buyer_name' => $item_2['company_name'],
                    'buyer_type' => $item_2['buyer_type'],
                    'sales_origin_province' => 'Sindh',
                    'document_type' => ' ',
                    'document_number' => $invoice_no,
                    'date' => $item_2['sale_date'],
                    'sale_type' => $sale_type,
                    'rate' => $item_2['tax'],
                    'description' => $hsn_code,
                    'quantity' => $total_quantity,
                    'uom' => $item_2['uom'],
                    'value_excl_tax' => $value_of_sales_excluding_tax,
                    'fixed_notified_val' => $mrp_third_schedule,
                    'sales_tax_fed' => $total_item_tax,
                    'extra_tax' => " ",
                    'st_witheld' => " ",
                    'sr_no' => " ",
                    'item_sr_no' => " ",
                    'further_tax' => $total_further_tax,
                    'total_Values_of_sales' => " "
                ];
                array_push($all_data_3, $tbl_data);
                $i++;
            }
        }


        $this->data['data'] = $all_data_3;
        //  echo '</pre>';
        $this->page_construct('reports/fbr_sale_report', $meta, $this->data);
    }
    public function product_ledger_report(){

        $this->data['billers']     = $this->site->getAllCompanies('biller');
        $this->session->set_userdata('user_csrf', $value);
        $this->data['csrf'] = $this->session->userdata('user_csrf');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['brands']     = $this->site->GetAllSupplierList();

        $start_date = $this->sma->fld($this->input->post('start_date'));
        $end_date = $this->sma->fld($this->input->post('end_date'));

        $before_date = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
        $end_date_2 = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));
        $first_start_date = '2001-01-01';


        $warehouse_id = $this->input->post('warehouse');
        $product_id = $this->input->post('product');

        $inputs = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'warehouse_id' => $this->input->post('warehouse'),
            'product_id' => $this->input->post('product'),
        ];

        $bc   = array(
            array(
                'link' => base_url(),
                'page' => lang('home')
            ),
            array(
                'link' => '#',
                'page' => lang('reports')
            )
        );
        $meta = array(
            'page_title' => lang('reports'),
            'bc' => $bc
        );


        $whr = "";
        $whr_2 = "";

        if (!empty($warehouse_id)) {
            $whr = "AND sma_purchases.warehouse_id = '$warehouse_id'";
            $whr_2 = "AND sma_sales.warehouse_id = '$warehouse_id'";
        }

        $whr_prodocut_id = "";
        $whr_2_prodocut_id = "";

        if (!empty($product_id)) {
            $whr_prodocut_id = "AND sma_purchase_items.product_id = '$product_id'";
            $whr_2_prodocut_id = "AND sma_sale_items.product_id = '$product_id'";
        }

        $qry_purchase_total = "SELECT 'Purchase' AS 'type', sum( sma_purchase_items.`quantity_received`) as total_purchase_quantity FROM `sma_purchases` LEFT JOIN `sma_purchase_items` ON sma_purchases.id = sma_purchase_items.purchase_id WHERE  DATE(sma_purchases.created_at) >= '$first_start_date' AND DATE(sma_purchases.created_at) <= '$before_date' " . $whr_prodocut_id . $whr . " ";
        $purchase_qry_total = $this->db->query($qry_purchase_total)->result_array();

        $qry_purchase_total_adj = "SELECT SUM(quantity) AS addition_quantity FROM `sma_adjustment_items` WHERE product_id = '$product_id' AND warehouse_id = '$warehouse_id'";
        $purchase_qry_total_ajd = $this->db->query($qry_purchase_total_adj)->result_array();


        $qry_purchase_1 = "SELECT 'Purchase' AS 'type', '-' AS 'po_number', sma_purchases.reference_no, DATE(sma_purchases.created_at) AS `date`, sma_purchase_items.`product_id`, sma_purchase_items.`product_name`, sma_purchase_items.`batch`, sma_purchases.`supplier` AS 'customer/supplier', sma_purchase_items.`quantity_received` as qtys FROM `sma_purchases` LEFT JOIN `sma_purchase_items` ON sma_purchases.id = sma_purchase_items.purchase_id WHERE  DATE(sma_purchases.created_at) >= '$start_date' AND DATE(sma_purchases.created_at) <= '$end_date' " . $whr_prodocut_id . $whr . " ORDER BY sma_purchases.created_at ASC";
        $purchase_qry = $this->db->query($qry_purchase_1)->result_array();

        $qry_sale_total = "SELECT 'Sale' AS 'type',  sum(sma_sale_items.`quantity`) as total_sale_quantity FROM sma_sales LEFT JOIN sma_sale_items ON sma_sales.id = sma_sale_items.sale_id WHERE DATE(sma_sales.date) >= '$first_start_date' AND DATE(sma_sales.date) <= '$before_date' " . $whr_2_prodocut_id . $whr_2 . "";
        $sales_qry_total = $this->db->query($qry_sale_total)->result_array();

        $qry_sale_1 = "SELECT 'Sale' AS 'type', sma_sales.po_number, sma_sales.reference_no, DATE(sma_sales.date) AS `date`, sma_sale_items.`product_id`, sma_sale_items.`product_name`, sma_sale_items.`batch`, sma_sales.`customer` AS 'customer/supplier', sma_sale_items.`quantity` as qtys FROM sma_sale_items LEFT JOIN sma_sales ON sma_sales.id = sma_sale_items.sale_id WHERE sma_sales.date >= '$start_date' AND sma_sales.date <= '$end_date_2' " . $whr_2_prodocut_id . $whr_2 . " ORDER BY `date` ASC";
        //$qry_sale_1 = "SELECT 'Sale' AS 'type', DATE(sma_sales.date) AS `date`, sma_sale_items.`product_id`, sma_sale_items.`product_name`, sma_sale_items.`batch`, sma_sales.`customer` AS 'customer/supplier', sma_sale_items.`quantity` as qtys FROM sma_sale_items LEFT JOIN sma_sales ON sma_sales.id = sma_sale_items.sale_id WHERE DATE(sma_sales.date) BETWEEN '$start_date' AND '$end_date' " . $whr_2_prodocut_id . $whr_2 . " ORDER BY `date` ASC";
        $sales_qry = $this->db->query($qry_sale_1)->result_array();

        $merge_data =  array_merge($purchase_qry, $sales_qry);
        $this->data['data2'] = array_merge($purchase_qry_total, $sales_qry_total, $purchase_qry_total_ajd);

        $this->db->truncate('sma_product_ledger_report');

        foreach ($merge_data as $key => $item) {

            $batch_no = $item['batch'];
            $type = $item['type'];

            $arr = [
                'type_of' => $type,
                'invoice_no' => $item['reference_no'],
                'po_number' => $item['po_number'],
                'ddate' => $item['date'],
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'batch' => $batch_no,
                'customer_supplier' => $item['customer/supplier'],
                'quantity' => $item['qtys'],
                'balance' => 0
            ];

            $this->db->insert('sma_product_ledger_report', $arr);
        }

        $data_tbl_qry = "SELECT * from sma_product_ledger_report ORDER BY `ddate` ASC";
        $data_tbl = $this->db->query($data_tbl_qry)->result_array();

        $this->data['data'] = $data_tbl;

        $this->page_construct('reports/product_ledger_report', $meta, $this->data);
    }
    // ---------------------New Code By Ismail--------------------------- //
    public function customer_legder(){
        $this->data['suppliers'] = $this->reports_model->suppliers();
        $this->data['supplier_id'] = $this->input->get('supplier');
        $this->data['customers'] = $this->reports_model->customers();
        $this->data['companies'] = $this->reports_model->companies();
        $this->data['wht'] = $this->input->get('wht') == "" ? 'hide' : $this->input->get('wht');
        $this->data['company_id'] = $this->input->get('company');
        $this->data['customer_id'] = $this->input->get('customer');
        $this->data['start'] = $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date');
        $this->data['sortingtype'] = $this->input->get('sortingtype');
        $this->data['rows'] = $this->reports_model->customerledger($this->data['customer_id'],$this->data['supplier_id'],$this->data['company_id'],$this->data['start'],$this->data['end'],$this->data['sortingtype']);

        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Customer/Sale Ledger')
            );
        $meta = array('page_title' => 'Customer/Sale Legder','bc' => $bc);
        $this->page_construct('new_reports/customer_sales_legder', $meta, $this->data);
    }
    public function customer_wht_legder(){
        $this->data['suppliers'] = $this->reports_model->suppliers();
        $this->data['supplier_id'] = $this->input->get('supplier');
        $this->data['customers'] = $this->reports_model->customers();
        $this->data['companies'] = $this->reports_model->companies();
        $this->data['wht'] = $this->input->get('wht') == "" ? 'hide' : $this->input->get('wht');
        $this->data['company_id'] = $this->input->get('company');
        $this->data['customer_id'] = $this->input->get('customer');
        $this->data['start'] = $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date');
        $this->data['sortingtype'] = $this->input->get('sortingtype');
        $this->data['rows'] = $this->reports_model->customer_wht_legder($this->data['customer_id'],$this->data['supplier_id'],$this->data['company_id'],$this->data['start'],$this->data['end'],$this->data['sortingtype']);

        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Customer WHT Ledger')
            );
        $meta = array('page_title' => 'Customer WHT Legder','bc' => $bc);
        $this->page_construct('new_reports/customer_wht_legder', $meta, $this->data);
    }
    public function supplier_legder(){
        $this->data['suppliers'] = $this->reports_model->suppliers();
        $this->data['supplier_id'] = $this->input->get('supplier');
        $this->data['companies'] = $this->reports_model->companies();
        // $this->data['wht'] = $this->input->get('wht') == "" ? 'hide' : $this->input->get('wht');
        $this->data['company_id'] = $this->input->get('company');
        $this->data['start'] = $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date');
        $this->data['sortingtype'] = $this->input->get('sortingtype');
        $this->data['rows'] = $this->reports_model->supplierlegder($this->data['supplier_id'],$this->data['company_id'],$this->data['start'],$this->data['end'],$this->data['sortingtype']);

        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Supplier Ledger')
            );
        $meta = array('page_title' => 'Supplier Legder','bc' => $bc);
        $this->page_construct('new_reports/supplier_legder', $meta, $this->data);
    }
    public function dc_report(){
        if ($this->data['Owner'] || $this->data['Admin'] || $this->data['GP']['batchwise_report']) {
            $start = $this->input->get('start_date');
            $end = $this->input->get('end_date');
            if($start == ""){
                $start = date('Y-m-d');
                if($end != ""){
                    $start = $end;
                }
            }
            if($end == ""){
                $end = date('Y-m-d');
            }
            $this->data['start'] = $start;
            $this->data['end'] = $end;
            $this->data['rows'] = $this->reports_model->dc_report(['start'=>$start,'end'=>$end]);
            $bc = array(
                    array('link' => base_url(),'page' => lang('home')),
                    array('link' => '#','page' => 'New Reports'),
                    array('link' => '#','page' => 'DC Report')
                );
            $meta = array('page_title' => 'DC Report','bc' => $bc);
            $this->page_construct('new_reports/dc_report', $meta, $this->data);
        }
        else{
            admin_redirect();
        }
    }
    public function batchwise(){
        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');
        $this->data['start'] = $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date');
        $this->data['own_company'] = $this->input->get('own_company') == "" ? 'all' : $this->input->get('own_company');
        $this->data['csupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['scategory'] = $this->input->get('category') == "" ? 'all' : $this->input->get('category');
        $this->data['cbrand'] = $this->input->get('brand') == "" ? 'all' : $this->input->get('brand');
        if($this->data['user_warehouses'] == "" || $this->data['user_warehouses'] == 0){
            $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        }
        else{
            $this->data['swarehouse'] = $this->data['user_warehouses'];
        }
        $this->data['own_companies'] = $this->reports_model->get_own_companies();
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['categories'] = $this->reports_model->get_categories();
        $this->data['brands']     = $this->reports_model->get_brands();
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $this->data['rows'] = $this->reports_model->batchwise([
            'company' => $this->input->get('own_company'),
            'supplier' => $this->input->get('supplier'),
            'category' => $this->input->get('category'),
            'warehouse' => $this->data['swarehouse'],
            'brand' => $this->input->get('brand'),
            'start' => $this->data['start'],
            'end' => $this->data['end'],
            'price_permission' => 1,
        ]);
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Batch Wise')
            );
        $meta = array('page_title' => 'Batch Wise','bc' => $bc);
        $this->page_construct2('reports/batch_wise', $meta, $this->data);
    }
    public function update_batch_price(){
        $sendvalue['status'] = false;
        $purchaseitemid = $this->input->post('purchaseitemid');
        $selling1 = $this->input->post('selling1');
        $selling2 = $this->input->post('selling2');
        $selling3 = $this->input->post('selling3');
        $mrp = $this->input->post('mrp');
        $set['price'] = $selling1;
        $set['dropship'] = $selling2;
        $set['crossdock'] = $selling3;
        $set['mrp'] = $mrp;
        $this->db->set($set);
        $this->db->where('id',$purchaseitemid);
        $this->db->update('sma_purchase_items');
        $sendvalue['message'] = "Price update successfully";
        $sendvalue['status'] = true;
        echo json_encode($sendvalue);
    }
    public function old_stock_report(){
        $this->data['csupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');

        $this->data['rows'] = array();
        $date = $newdate = date("Y-m-d", strtotime("-1 months"));;

        $this->db->select('
            sma_purchase_items.product_id as pid,
            sma_purchase_items.product_name as pname,
            sma_purchase_items.date as date,
            sma_purchase_items.batch as batch,
            sma_purchase_items.expiry as expire,
            sma_purchase_items.quantity_balance as qty
        ');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
        $this->db->join('sma_purchases','sma_purchases.id = sma_purchase_items.purchase_id','left');
        $this->db->where('sma_purchase_items.quantity_balance !=','0.0000');
        if($this->data['csupplier'] != "all"){
            $this->db->where('sma_purchases.supplier_id =',$this->data['csupplier']);
        }
        if($this->data['swarehouse'] != "all"){
            $this->db->where('sma_purchase_items.warehouse_id =',$this->data['swarehouse']);
        }
        // sma_purchases.date >= '" . $start_date . "
        $this->db->where('sma_purchases.date <=',$date);
        $this->db->where('sma_products.status =','1');
        $this->db->where('sma_products.status =','1');
        $q = $this->db->get();
        $this->data['rows'] = $q->result();


        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Old Stock')
            );
        $meta = array('page_title' => 'Old Stock','bc' => $bc);
        $this->page_construct('new_reports/old_stock', $meta, $this->data);
    }
    public function get_old_stock_report(){
        $supplier = $this->input->get('supplier');
        $warehouse = $this->input->get('warehouse');



        echo json_encode($return);
    }
    public function salesreport(){
        $this->data['start'] = $this->input->get('start_date') == "" ? date("Y-m-d", strtotime("-1 months")) : $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date') == "" ? date('Y-m-d') : $this->input->get('end_date');
        $owncompany = $this->input->get('own_company');
        if($this->input->get('own_company') == ""){
            $owncompany = array();
        }
        $this->data['own_company'] = count($owncompany) == 0 ? 'all' : $owncompany;

        $supplier = $this->input->get('supplier');
        if($this->input->get('supplier') == ""){
            $supplier = array();
        }
        $this->data['csupplier'] = count($supplier) == 0 ? 'all' : $supplier;



        $customer = $this->input->get('customer');
        if($this->input->get('customer') == ""){
            $customer = array();
        }
        $this->data['ccustomer'] = count($customer) == 0 ? 'all' : $customer;


        $warehouse = $this->input->get('warehouse');
        if($this->input->get('warehouse') == ""){
            $warehouse = array();
        }
        $this->data['swarehouse'] = count($warehouse) == 0 ? 'all' : $warehouse;
        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');

        $this->data['own_companies'] = $this->reports_model->get_own_companies();
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['customers']     = $this->reports_model->get_companies('customer');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'Reports'),
                array('link' => '#','page' => 'Sales Report')
            );
        $meta = array('page_title' => 'Sales Report','bc' => $bc);
        $this->page_construct2('reports/salesreport', $meta, $this->data);
        // $this->page_construct('new_reports/salesreport', $meta, $this->data);
    }
    public function salesreport_ajax(){
        // echo $this->reports_model->registers();
        echo $this->reports_model->salesreport_ajax();
    }
    public function expected_soldout(){
        $this->data['csupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        $this->data['sbrand'] = $this->input->get('brand') == "" ? 'all' : $this->input->get('brand');

        $this->data['rows'] = array();
        $date = $newdate = date("Y-m-d", strtotime("-1 months"));;

        $this->db->select('
            sma_purchase_items.product_id as pid,
            sma_purchase_items.product_name as pname,
            sma_purchase_items.date as date,
            sma_purchase_items.batch as batch,
            sma_purchase_items.expiry as expire,
            sma_purchase_items.quantity_balance as qty,
            sma_warehouses.name as warehouse_name,
            sma_purchases.supplier as supplier_name,
            sma_products.carton_size,
            sma_brands.name as brand_name
        ');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_purchases','sma_purchases.id = sma_purchase_items.purchase_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_items.warehouse_id','left');
        $this->db->where('sma_purchase_items.quantity_balance > ','0');
        if($this->data['csupplier'] != "all"){
            $this->db->where('sma_purchases.supplier_id =',$this->data['csupplier']);
        }
        if($this->data['swarehouse'] != "all"){
            $this->db->where('sma_purchase_items.warehouse_id =',$this->data['swarehouse']);
        }
        if($this->data['sbrand'] != "all"){
            $this->db->where('sma_products.brand =',$this->data['sbrand']);
        }
        $this->db->where('DATE(sma_purchases.date) <= DATE_SUB(CURDATE(), INTERVAL sma_products.es_durration DAY)');
        $this->db->where('sma_products.status =','1');
        $q = $this->db->get();
        $this->data['rows'] = $q->result();


        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $this->data['brands'] = $this->site->getAllBrands();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Cross Expected Sold-Out Days')
            );
        $meta = array('page_title' => 'Cross Expected Sold-Out','bc' => $bc);
        $this->page_construct('new_reports/expected_soldout', $meta, $this->data);

    }
    public function shortexpiry_stock(){
        $this->data['csupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        $this->data['sbrand'] = $this->input->get('brand') == "" ? 'all' : $this->input->get('brand');

        $this->data['rows'] = array();
        $date = $newdate = date("Y-m-d", strtotime("-1 months"));;

        $this->db->select('
            sma_purchase_items.product_id as pid,
            sma_purchase_items.product_name as pname,
            sma_purchase_items.date as date,
            sma_purchase_items.batch as batch,
            sma_purchase_items.expiry as expire,
            sma_purchase_items.quantity_balance as qty,
            sma_products.short_expiry_duration,
            sma_warehouses.name as warehouse_name,
            sma_purchases.supplier_id as supplier_id,
            sma_purchases.supplier as supplier_name,
            sma_products.carton_size,
            sma_brands.name as brand_name
        ');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_purchases','sma_purchases.id = sma_purchase_items.purchase_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_items.warehouse_id','left');
        $this->db->where('sma_purchase_items.quantity_balance >','0');
        if($this->data['csupplier'] != "all"){
            $this->db->where('sma_purchases.supplier_id =',$this->data['csupplier']);
        }
        if($this->data['swarehouse'] != "all"){
            $this->db->where('sma_purchase_items.warehouse_id =',$this->data['swarehouse']);
        }
        if($this->data['sbrand'] != "all"){
            $this->db->where('sma_products.brand =',$this->data['sbrand']);
        }
        $this->db->where('sma_products.status =','1');
        $q = $this->db->get();
        $this->data['rows'] = $q->result();


        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $this->data['brands'] = $this->site->getAllBrands();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Short Expiry Stock')
            );
        $meta = array('page_title' => 'Short Expiry Stock','bc' => $bc);
        $this->page_construct('new_reports/shortexpiry_stock', $meta, $this->data);

    }
    public function expired_stock(){
        $this->data['csupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        $this->data['sbrand'] = $this->input->get('brand') == "" ? 'all' : $this->input->get('brand');

        $this->data['rows'] = array();
        $date = $newdate = date("Y-m-d", strtotime("-1 months"));

        $this->db->select('
            sma_purchase_items.product_id as pid,
            sma_purchase_items.product_name as pname,
            sma_purchase_items.date as date,
            sma_purchase_items.batch as batch,
            sma_purchase_items.expiry as expire,
            sma_purchase_items.quantity_balance as qty,
            sma_warehouses.name as warehouse_name,
            sma_purchases.supplier as supplier_name,
            sma_products.carton_size,
            sma_brands.name as brand_name
        ');
        $this->db->from('sma_purchase_items');
        $this->db->join('sma_products','sma_products.id = sma_purchase_items.product_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_purchases','sma_purchases.id = sma_purchase_items.purchase_id','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_purchase_items.warehouse_id','left');
        $this->db->where('sma_purchase_items.quantity_balance > ','0');
        if($this->data['csupplier'] != "all"){
            $this->db->where('sma_purchases.supplier_id =',$this->data['csupplier']);
        }
        if($this->data['swarehouse'] != "all"){
            $this->db->where('sma_purchase_items.warehouse_id =',$this->data['swarehouse']);
        }
        if($this->data['sbrand'] != "all"){
            $this->db->where('sma_products.brand =',$this->data['sbrand']);
        }
        $this->db->where('sma_products.status =','1');
        $q = $this->db->get();
        $this->data['rows'] = $q->result();

        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $this->data['brands'] = $this->site->getAllBrands();
        $bc = array(
            array('link' => base_url(),'page' => lang('home')),
            array('link' => '#','page' => 'New Reports'),
            array('link' => '#','page' => 'Expired Stock')
        );
        $meta = array('page_title' => 'Expired Stock','bc' => $bc);
        $this->page_construct('new_reports/expired_stock', $meta, $this->data);

    }
    public function salessummary(){
        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');
        $this->data['start'] = $this->input->get('start_date') == "" ? date("Y-m-d", strtotime("-1 months")) : $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date') == "" ? date('Y-m-d') : $this->input->get('end_date');
        $this->data['own_company'] = $this->input->get('own_company') == "" ? 'all' : $this->input->get('own_company');
        $this->data['csupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['ccustomer'] = $this->input->get('customer') == "" ? 'all' : $this->input->get('customer');
        if($this->data['user_warehouses'] == "" || $this->data['user_warehouses'] == 0){
            $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        }
        else{
            $this->data['swarehouse'] = $this->data['user_warehouses'];
        }
        $this->data['own_companies'] = $this->reports_model->get_own_companies();
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['customers']     = $this->reports_model->get_companies('customer');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Sales Summary')
            );
        $meta = array('page_title' => 'Sales Report','bc' => $bc);
        $this->page_construct('new_reports/salessummary', $meta, $this->data);
    }
    public function salessummary_ajax(){
        $return['data'] = $this->reports_model->salessummary([
            'own_company' => $this->input->get('own_company'),
            'supplier' => $this->input->get('supplier'),
            'customer' => $this->input->get('customer'),
            'warehouse' => $this->input->get('warehouse'),
            'start' => $this->input->get('start'),
            'end' => $this->input->get('end')
        ]);
        echo json_encode($return);
    }
    public function monthly_items_demand(){
        $this->data['swarehouse'] = count($this->input->get('warehouse')) == 0 ? 'all' : $this->input->get('warehouse');
        $this->data['scutomer'] = count($this->input->get('cutomer')) == 0 ? 'all' : $this->input->get('cutomer');
        $this->data['sbrand'] = count($this->input->get('brand')) == 0 ? 'all' : $this->input->get('brand');
        $this->data['rows'] = array();

        $this->db->select('
            YEAR(sma_sales_orders_tb.created_at) AS d_year,
            MONTH(sma_sales_orders_tb.created_at) AS d_month,
            sma_sales_order_items.product_id as pid,
            sma_products.name as pname,
            sma_products.mrp as pmrp,
            sma_products.cost as cost,
            sma_products.tax_rate as tax_rate,
            sma_products.carton_size as carton_size,
            sma_warehouses.name as wid,
            sma_brands.name AS brand,
            SUM(sma_sales_order_items.quantity) as quantity,
            sma_tax_rates.rate as tax_rate,
            sma_tax_rates.type as tax_type,
            sma_companies.name as customer,
            IFNULL(
                sma_product_groups.id,
                "Unknown Group"
            ) as group_id,
            IFNULL(
                sma_product_groups.name,
                "Unknown Group"
            ) as group_name,

        ');
        $this->db->from('sma_sales_order_items');
        $this->db->join('sma_sales_orders_tb','sma_sales_orders_tb.id = sma_sales_order_items.so_id','left');
        $this->db->join('sma_products','sma_products.id = sma_sales_order_items.product_id','left');
        $this->db->join('sma_product_groups','sma_product_groups.id = sma_products.group_id','left');
        $this->db->join('sma_brands','sma_brands.id = sma_products.brand','left');
        $this->db->join('sma_tax_rates','sma_tax_rates.id = sma_products.tax_rate','left');
        $this->db->join('sma_warehouses','sma_warehouses.id = sma_sales_order_items.warehouse_id','left');
        $this->db->join('sma_companies','sma_companies.id = sma_sales_orders_tb.customer_id','left');
        $this->db->group_by(array(
            "YEAR(sma_sales_orders_tb.created_at)", 
            "MONTH(sma_sales_orders_tb.created_at)", 
            "sma_sales_order_items.product_id",
            "sma_sales_order_items.warehouse_id",
            "sma_sales_orders_tb.customer_id"
        ));
        if($this->data['swarehouse'] != "all" && $this->data['swarehouse'] != ""){
            $wwhere = "(";
            $firwno = 0;
            foreach($this->data['swarehouse'] as $wu){
                $firwno++;
                if($firwno == 1){
                    $wwhere .= "sma_sales_order_items.warehouse_id = ".$wu;
                }
                else{
                    $wwhere .= " OR sma_sales_order_items.warehouse_id = ".$wu;
                }
            }
            $wwhere .= ") ";
            $this->db->where($wwhere);
        }
        if($this->data['sbrand'] != "all" && $this->data['sbrand'] != ""){
            $bwhere = "(";
            $firbno = 0;
            foreach($this->data['sbrand'] as $bu){
                $firbno++;
                if($firbno == 1){
                    $bwhere .= "sma_products.brand = ".$bu;
                }
                else{
                    $bwhere .= " OR sma_products.brand = ".$bu;
                }
            }
            $bwhere .= ") ";
            $this->db->where($bwhere);
        }
        if($this->data['scutomer'] != "all" && $this->data['scutomer'] != ""){
            $cwhere = "(";
            $fircno = 0;
            foreach($this->data['scutomer'] as $cu){
                $fircno++;
                if($fircno == 1){
                    $cwhere .= "sma_sales_orders_tb.customer_id = ".$cu;
                }
                else{
                    $cwhere .= " OR sma_sales_orders_tb.customer_id = ".$cu;
                }
            }
            $cwhere .= ") ";
            $this->db->where($cwhere);
        }
        $this->db->where('sma_products.status =','1');
        $q = $this->db->get();
        $this->data['rows'] = $q->result();

        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $this->data['brands'] = $this->site->getAllBrands();
        $this->data['customers'] = $this->site->getAllCompanies('customer');
        $bc = array(
            array('link' => base_url(),'page' => lang('home')),
            array('link' => '#','page' => 'New Reports'),
            array('link' => '#','page' => 'Monthly Items Demand')
        );
        $meta = array('page_title' => 'Monthly Items Demand','bc' => $bc);
        $this->page_construct('new_reports/monthly_items_demand', $meta, $this->data);

    }
    public function creadits(){
        $this->data['scustomer'] = $this->input->get('customer') == "" ? 'all' : $this->input->get('customer');
        $this->data['ssupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['suppliers'] = $this->reports_model->get_companies('supplier');
        $this->data['customers'] = $this->reports_model->get_companies('customer');
        $this->db->select('
            sma_customer_limits.*,
            0 as testing_date,
            supplier.name as sname,
            customer.name as cname,
            (
                SELECT 
                    SUM(grand_total)-SUM(paid)
                FROM 
                    sma_sales 
                WHERE 
                    supplier_id = sma_customer_limits.supplier_id AND 
                    customer_id = sma_customer_limits.customer_id AND
                    (
                        payment_status = "due" OR 
                        payment_status = "pending" OR 
                        payment_status = "partial"
                    )
            ) AS due_amount
        ');
        $this->db->from('sma_customer_limits');
        $this->db->join('sma_companies as supplier','supplier.id = sma_customer_limits.supplier_id','left');
        $this->db->join('sma_companies as customer','customer.id = sma_customer_limits.customer_id','left');
        if($this->data['scustomer'] != "all"){
            $this->db->where('customer_id',$this->data['scustomer']);
        }
        if($this->data['ssupplier'] != "all"){
            $this->db->where('supplier_id',$this->data['ssupplier']);
        }
        $q = $this->db->get();
        $this->data['rows'] = $q->result();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Creadits')
            );
        $meta = array('page_title' => 'Creadit Limits Report','bc' => $bc);
        $this->page_construct('new_reports/customer_creadits', $meta, $this->data);
    }
    public function due_invoices(){
        $this->data['scustomer'] = $this->input->get('customer') == "" ? 'all' : $this->input->get('customer');
        $this->data['ssupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');

        $this->data['suppliers'] = $this->reports_model->get_companies('supplier');
        $this->data['customers'] = $this->reports_model->get_companies('customer');

        $this->db->select('sma_sales.*,supplier.name as sname');
        $this->db->from('sma_sales');
        $this->db->join('sma_companies as supplier','supplier.id = sma_sales.supplier_id','left');
        $where = '(payment_status = "due" OR payment_status = "pending" OR payment_status = "partial")';
        if($this->data['scustomer'] != "all"){
            $where .= ' AND customer_id = '.$this->data['scustomer'];
        }
        if($this->data['ssupplier'] != "all"){
            $where .= ' AND supplier_id = '.$this->data['ssupplier'];
        }
        $this->db->where($where);
        $q = $this->db->get();
        $this->data['rows'] = $q->result();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Due Invoice')
            );
        $meta = array('page_title' => 'Due Invoice Report','bc' => $bc);
        $this->page_construct('new_reports/due_invice', $meta, $this->data);
    }
    public function so_items_wise(){
        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');
        $this->data['start'] = $this->input->get('start_date') == "" ? date("Y-m-d", strtotime("-7 days")) : $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date') == "" ? date('Y-m-d') : $this->input->get('end_date');
        $this->data['csupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        $this->data['ccustomer'] = $this->input->get('customer') == "" ? 'all' : $this->input->get('customer');
        if($this->data['user_warehouses'] == "" || $this->data['user_warehouses'] == 0){
            $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        }
        else{
            $this->data['swarehouse'] = $this->data['user_warehouses'];
        }
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['customers']     = $this->reports_model->get_companies('customer');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'SO Items Wise')
            );
        $meta = array('page_title' => 'SO Items Waise','bc' => $bc);
        $this->page_construct('new_reports/so_items_wise', $meta, $this->data);
    }
    public function so_items_wise_ajax(){
        $return['data'] = $this->reports_model->so_items_wise([
            'supplier' => $this->input->get('supplier'),
            'customer' => $this->input->get('customer'),
            'warehouse' => $this->input->get('warehouse'),
            'start' => $this->input->get('start'),
            'end' => $this->input->get('end')
        ]);
        echo json_encode($return);
    }
    public function po_items_wise(){
        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');
        $this->data['start'] = $this->input->get('start_date') == "" ? date("Y-m-d", strtotime("-1 months")) : $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date') == "" ? date('Y-m-d') : $this->input->get('end_date');
        $this->data['csupplier'] = $this->input->get('supplier') == "" ? 'all' : $this->input->get('supplier');
        if($this->data['user_warehouses'] == "" || $this->data['user_warehouses'] == 0){
            $this->data['swarehouse'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        }
        else{
            $this->data['swarehouse'] = $this->data['user_warehouses'];
        }
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'PO Items Wise')
            );
        $meta = array('page_title' => 'PO Items Waise','bc' => $bc);
        $this->page_construct('new_reports/po_items_wise', $meta, $this->data);
    }
    public function po_items_wise_ajax(){
        $return['data'] = $this->reports_model->po_items_wise([
            'supplier' => $this->input->get('supplier'),
            'warehouse' => $this->input->get('warehouse'),
            'start' => $this->input->get('start'),
            'end' => $this->input->get('end')
        ]);
        echo json_encode($return);
    }
    public function ledger_summery(){
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $this->data['customers'] = $this->reports_model->customers();
        $this->data['companies'] = $this->reports_model->companies();
        $this->data['swarehouse'] = $this->input->get('warehouse');
        $this->data['company_id'] = $this->input->get('company');
        $this->data['customer_id'] = $this->input->get('customer');
        $this->data['start'] = $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date');
        $this->data['recivable_rows'] = $this->reports_model->ledger_summery_recivable($this->data['swarehouse'],$this->data['customer_id'],$this->data['company_id'],$this->data['start'],$this->data['end']);
        $this->data['due_rows'] = $this->reports_model->ledger_summery_due($this->data['swarehouse'],$this->data['customer_id'],$this->data['company_id'],$this->data['start'],$this->data['end']);
        // $this->data['dwarehosue'] = $this->site->getWarehouseByID($this->data['swarehouse']);
        $this->data['showwarehouses'] = array();
        if($this->data['swarehouse'] != "" && count($this->data['swarehouse']) > 0){
            $this->db->select('*');
            $this->db->from('sma_warehouses');
            $this->db->where_in('id', $this->data['swarehouse']);
            $q = $this->db->get();
            $this->data['showwarehouses'] = $q->result();
        }
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Ledger Summary')
            );
        $meta = array('page_title' => 'Ledger Summary','bc' => $bc);
        $this->page_construct('new_reports/ledger_sumery', $meta, $this->data);
    }
    public function products_ledger(){
        $this->data['pname'] = $this->input->get('sproduct');
        $this->data['pid'] = $this->input->get('product');
        // $this->data['wid'] = $this->input->get('warehouse');
        $this->data['start_date'] = $this->input->get('start_date');
        $this->data['end_date'] = $this->input->get('end_date');
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');
        if($this->data['user_warehouses'] == ""){
            $this->data['wid'] = $this->input->get('warehouse') == "" ? 'all' : $this->input->get('warehouse');
        }
        else{
            $this->data['wid'] = $this->data['user_warehouses'];
        }



        $this->data['rows'] = $this->reports_model->products_ledger($this->data['pid'],$this->data['wid'],$this->data['start_date'],$this->data['end_date']);

        // echo '<pre>';
        // print_r($this->data['rows']);
        // exit();

        $bc = array(
            array('link' => base_url(),'page' => lang('home')),
            array('link' => '#','page' => 'New Reports'),
            array('link' => '#','page' => 'Ledger Summary')
        );
        $meta = array('page_title' => 'Ledger Summary','bc' => $bc);
        $this->page_construct('new_reports/products_ledger', $meta, $this->data);

    }
    public function remarks(){
        $sendvalue['codestatus'] = 'no';
        $payid = $this->input->get('payid');
        $sale_id = $this->input->get('sale_id');
        $remarks = $this->input->get('remarks');
        if($remarks != ""){
            if($payid != 0 && $payid != ""){
                $this->db->set('remarks',$remarks);
                $this->db->where('id',$payid);
                $this->db->update('sma_payments');
            }
            else{
                $this->db->set('remarks',$remarks);
                $this->db->where('id',$sale_id);
                $this->db->update('sma_sales');
            }
            $sendvalue['codestatus'] = 'ok';

        }
        else{
            $sendvalue['codestatus'] = 'Enter Reason';
        }
        echo json_encode($sendvalue);
    }
    public function purchasereport(){
        $this->data['start'] = $this->input->get('start_date') == "" ? date("Y-m-d", strtotime("-6 months")) : $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date') == "" ? date('Y-m-d') : $this->input->get('end_date');

        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');

        if($this->input->get('own_company') != ""){
            $this->data['own_company'] = count($this->input->get('own_company')) == 0 ? array() : $this->input->get('own_company');
        }
        else{
            $this->data['own_company'] = array();
        }
        if($this->input->get('csupplier') != ""){
            $this->data['csupplier'] = count($this->input->get('supplier')) == 0 ? array() : $this->input->get('supplier');
        }
        else{
            $this->data['csupplier'] = array();
        }
        if($this->data['user_warehouses'] == "" || $this->data['user_warehouses'] == 0){
            if($this->input->get('swarehouse') != ""){
                $this->data['swarehouse'] = count($this->input->get('warehouse')) == 0 ? array() : $this->input->get('warehouse');
            }
            else{
                $this->data['swarehouse'] = array();
            }
        }
        else{
            $this->data['swarehouse'] = array();
        }

        $this->data['own_companies'] = $this->reports_model->get_own_companies();
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Purchase Report')
            );
        $meta = array('page_title' => 'Purchase Report','bc' => $bc);
        $this->page_construct('new_reports/purchasereport', $meta, $this->data);
    }
    public function purchasereport_ajax(){
        $permission = 0;
        if ($this->data['Owner'] || $this->data['Admin'] || $this->data['GP']['batchwise_price_report']) {
            $permission = 1;
        }
        $return['data'] = $this->reports_model->purchasereport([
            'own_company' => $this->input->get('own_company'),
            'supplier' => $this->input->get('supplier'),
            'warehouse' => $this->input->get('warehouse'),
            'start' => $this->input->get('start'),
            'end' => $this->input->get('end'),
            'price_permission' => $permission,
        ]);
        echo json_encode($return);
    }
    public function batch_wise_true_false(){
        $this->data['sproduct'] = $this->input->get('product');
        $this->data['ssid'] = $this->input->get('sid');
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Batch Waise True & False')
            );
        $meta = array('page_title' => 'Batch Waise True & False','bc' => $bc);
        $this->page_construct('new_reports/truefalse_batchwise', $meta, $this->data);
    }
    public function batch_wise_true_false_ajax(){
        $return['data'] = $this->reports_model->batch_wise_true_false([
            'ssid' => $this->input->get('ssid'),
            'product_id' => $this->input->get('product_id')
        ]);
        echo json_encode($return);
    }
    public function product_wise_true_false(){
        $this->data['csupplier'] = $this->input->get('supplier');
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Product Waise True & False')
        );
        $meta = array('page_title' => 'Product Waise True & False','bc' => $bc);
        $this->page_construct('new_reports/truefalse_productwise', $meta, $this->data);
    }
    public function product_wise_true_false_ajax(){
        $return['data'] = $this->reports_model->product_wise_true_false([
            'supplier' => $this->input->get('supplier')
        ]);
        echo json_encode($return);
    }
    public function so_hold_quantity(){
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'SO Hold Stock')
            );
        $meta = array('page_title' => 'SO Hold Stock','bc' => $bc);
        $this->page_construct('new_reports/so_hold_stock', $meta, $this->data);
    }
    public function so_hold_quantity_ajax(){
        $return['data'] = $this->reports_model->so_hold_quantity();
        echo json_encode($return);
    }
    public function salesreturnremarksreport(){

        $query ="
            SELECT
                s.date AS sale_date,
                s.reference_no,
                s.po_number,
                s.customer,
                oc.companyname AS own_company,
                w.name AS warehouse_name,
                p.id AS product_id,
                p.company_code,
                p.name AS product_name,
                si.quantity,
                si.expiry,
                si.net_unit_price,
                si.mrp,
                si.tax,
                si.subtotal,
                a.note
            FROM
                sma_user_activities AS a 
            LEFT JOIN sma_sale_items AS si ON si.product_id = a.product_id AND a.sale_id = si.sale_id
            LEFT JOIN sma_sales AS s ON s.id = a.sale_id
            LEFT JOIN sma_products AS p ON p.id = a.product_id
            LEFT JOIN sma_own_companies AS oc ON oc.id = s.own_company
            LEFT JOIN sma_warehouses AS w ON w.id = s.warehouse_id
            WHERE
                a.sale_id IS NOT NULL AND 
                a.product_id IS NOT NULL
        ";
        $q = $this->db->query($query);
        $this->data['rows'] = $q->result();

        $bc = array(
            array('link' => base_url(),'page' => lang('home')),
            array('link' => '#','page' => 'New Reports'),
            array('link' => '#','page' => 'Sale Return Item Report With Remarks')
        );
        $meta = array('page_title' => 'Sale Return Item Report With Remarks','bc' => $bc);
        $this->page_construct('new_reports/salesreturnremarksreport', $meta, $this->data);


    }
    public function purchasereturn(){
        $this->data['start'] = $this->input->get('start_date') == "" ? date("Y-m-d", strtotime("-6 months")) : $this->input->get('start_date');
        $this->data['end'] = $this->input->get('end_date') == "" ? date('Y-m-d') : $this->input->get('end_date');

        $this->data['user_warehouses'] = $this->session->userdata('warehouse_id');

        if($this->input->get('own_company') != ""){
            $this->data['own_company'] = count($this->input->get('own_company')) == 0 ? array() : $this->input->get('own_company');
        }
        else{
            $this->data['own_company'] = array();
        }
        if($this->input->get('csupplier') != ""){
            $this->data['csupplier'] = count($this->input->get('supplier')) == 0 ? array() : $this->input->get('supplier');
        }
        else{
            $this->data['csupplier'] = array();
        }
        if($this->data['user_warehouses'] == "" || $this->data['user_warehouses'] == 0){
            if($this->input->get('swarehouse') != ""){
                $this->data['swarehouse'] = count($this->input->get('warehouse')) == 0 ? array() : $this->input->get('warehouse');
            }
            else{
                $this->data['swarehouse'] = array();
            }
        }
        else{
            $this->data['swarehouse'] = array();
        }

        $this->data['own_companies'] = $this->reports_model->get_own_companies();
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['warehouses'] = $this->reports_model->get_warehosues();
        $bc = array(
                array('link' => base_url(),'page' => lang('home')),
                array('link' => '#','page' => 'New Reports'),
                array('link' => '#','page' => 'Purchase Return')
            );
        $meta = array('page_title' => 'Purchase Return','bc' => $bc);
        $this->page_construct('new_reports/purchasereturn', $meta, $this->data);

    }
    public function purchasereturn_ajax(){
        $permission = 0;
        if ($this->data['Owner'] || $this->data['Admin'] || $this->data['GP']['batchwise_price_report']) {
            $permission = 1;
        }
        $return['data'] = $this->reports_model->purchasereturn([
            'own_company' => $this->input->get('own_company'),
            'supplier' => $this->input->get('supplier'),
            'warehouse' => $this->input->get('warehouse'),
            'start' => $this->input->get('start'),
            'end' => $this->input->get('end'),
            'price_permission' => $permission,
        ]);
        echo json_encode($return);

    }
    public function stock_report(){
        $user = $this->site->getUser();
		$company_firstname = $user->first_name;
        $company_id_sale_show = $user->biller_id;

        $this->data['supplier_id'] = $this->input->get('supplier');
        $this->data['swarehouse'] = $this->input->get('warehouse');
        $this->data['date_from'] = $this->input->get('date_from');
        $this->data['date_to'] = $this->input->get('date_to');
        $this->data['show_type'] = $this->input->get('show_type');
        if($this->data['date_from'] == ""){
            $this->data['date_from'] = date('d-m-Y');
        }
        if($this->data['date_to'] == ""){
            $this->data['date_to'] = date('d-m-Y');
        }

        $this->data['warehouses'] = $this->general_model->GetAllWarehouses();
        $this->data['suppliers']     = $this->reports_model->get_companies('supplier');
        $this->data['rows'] = array();
        if($this->data['supplier_id'] != "" && $this->data['swarehouse'] != ""){

            $date_from = $m = date_format(date_create($this->data['date_from']),"Y-m-d");
            $date_to = $m = date_format(date_create($this->data['date_to']),"Y-m-d");
    
            $this->db->select('
                p.id,
                p.code,
                p.name,
                p.carton_size,
                b.name as brand,
                c.name as category,
            ');
            $m = date_format(date_create($this->data['date_from']),"m");
            if($m<=6){
                $year1 = date_format(date_create($this->data['date_from']),"Y")-1;
                $year2 = date_format(date_create($this->data['date_from']),"Y");
            }
            else{
                $year1 = date_format(date_create($this->data['date_from']),"Y");
                $year2 = date_format(date_create($this->data['date_from']),"Y")+1;
            }
            $supplierid = $this->data['supplier_id'];
            $this->db->from('sma_products as p');
            $this->db->join('sma_brands as b','b.id = p.brand','left');
            $this->db->join('sma_categories as c','c.id = p.category_id ','left');
            $this->db->where('p.status',1);
            $this->db->where('(p.supplier1 = "'.$supplierid.'" OR p.supplier2 = "'.$supplierid.'" OR  p.supplier3 = "'.$supplierid.'" OR  p.supplier4 = "'.$supplierid.'" OR  p.supplier5 = "'.$supplierid.'")');
            $products =  $this->db->get()->result();
            foreach($products as $pkey => $product){
                if($this->data['show_type'] == 1){
                    $master_size = $product->carton_size;
                }
                else{
                    $master_size = 1;
                }
                $temp['sku_code'] = $product->code;
                $temp['name'] = $product->name;
                $temp['product_id'] = $product->id;
                if($date_from == $date_to){
                    $temp['date'] = $date_from;
                }
                else{
                    $temp['date'] = $date_from.' TO '.$date_to;
                }
                $temp['month'] = date_format(date_create($this->data['date_from']),"M-y");
                $temp['fical_year'] = $year1."-".$year2;
                $temp['brand'] = $product->brand;
                $temp['variant'] = '';
                $temp['sku'] = '';
                $temp['category'] = $product->category;
                $temp['city'] = "Karachi";
                
                // Opening Balance
                $this->db->select('SUM(purchase_items.quantity_received) as total,purchase_items.date,purchase_items.product_id ');
                $this->db->from('purchase_items as purchase_items');
                $this->db->where('purchase_items.date < "'.$date_from.'"');
                if($this->data['swarehouse'] != "0"){
                    $this->db->where('purchase_items.warehouse_id',$this->data['swarehouse']);
                }
                $this->db->where('purchase_items.product_id',$product->id);
                $o1 = $this->db->get()->row();
                if($o1->total == ""){
                    $o1 = 0;
                }
                else{
                    $o1 = $o1->total;
                }
                $this->db->select('SUM(pri.quantity) as total');
                $this->db->from('sma_purchase_return_items_tb as pri');
                $this->db->join('sma_purchase_return_tb as pr','pr.id = pri.purchase_return_id','left');
                $this->db->join('sma_purchases as p','p.id = pr.purchase_id','left');
                $this->db->where('pr.return_date < "'.$date_from.'"');
                if($this->data['swarehouse'] != "0"){
                    $this->db->where('p.warehouse_id',$this->data['swarehouse']);
                }
                $this->db->where('pri.product_id',$product->id);
                $o2 = $this->db->get()->row();
                if($o2->total == ""){
                    $o2 = 0;
                }
                else{
                    $o2 = $o2->total;
                }
                $this->db->select('SUM(sale_items.quantity) as total,sales.date,sale_items.product_id ');
                $this->db->from('sale_items as sale_items');
                $this->db->join('sales as sales','sales.id = sale_items.sale_id','left');
                $this->db->where('sales.date < "'.$date_from.'"');
                if($this->data['swarehouse'] != "0"){
                    $this->db->where('sales.warehouse_id',$this->data['swarehouse']);
                }
                $this->db->where('sale_items.product_id',$product->id);
                $o3 = $this->db->get()->row();
    
                if($o3->total == ""){
                    $o3 = 0;
                }
                else{
                    $o3 = $o3->total;
                }
                $this->db->select('SUM(sri.quantity) as total');
                $this->db->from('sma_sale_return_items_tb as sri');
                $this->db->join('sma_sale_returns_tb as sr','sr.id = sri.sale_return_id','left');
                $this->db->where('sr.date < "'.$date_from.'"');
                if($this->data['swarehouse'] != "0"){
                    $this->db->where('sri.warehouse_id',$this->data['swarehouse']);
                }
                $this->db->where('sri.product_id',$product->id);
                $o4 = $this->db->get()->row();
    
                if($o4->total == ""){
                    $o4 = 0;
                }
                else{
                    $o4 = $o4->total;
                }
                $temp['date_from'] = $date_from;
                $temp['product_id'] = $product->id;
                $temp['date_to'] = $date_to;
                $temp['swarehouse'] = $this->data['swarehouse'];
                $temp['o1'] = decimalallow($o1/$master_size,2);
                $temp['o2'] = decimalallow($o2/$master_size,2);
                $temp['o3'] = decimalallow($o3/$master_size,2);
                $temp['o4'] = decimalallow($o4/$master_size,2);
                $openbalance = ($temp['o1']-$temp['o2'])-($temp['o3']-$temp['o4']);
                $temp['opening'] = decimalallow($openbalance,2);
                // $temp['opening'] = $o1."-".$o2;
                // Primary
                $this->db->select('SUM(purchase_items.quantity_received) as total,purchase_items.date,purchase_items.product_id ');
                $this->db->from('purchase_items as purchase_items');
                $this->db->where('purchase_items.date >= ',$date_from." 00:00:00");
                $this->db->where('purchase_items.date <= ',$date_to." 23:59:59");
                if($this->data['swarehouse'] != "0"){
                    $this->db->where('purchase_items.warehouse_id',$this->data['swarehouse']);
                }
                $this->db->where('purchase_items.product_id',$product->id);
                $primary = $this->db->get()->row();
    
                if($primary->total == ""){
                    $temp['primary'] = 0;
                }
                else{
                    $temp['primary'] = decimalallow($primary->total/$master_size,2);
                }
                $this->db->select('SUM(sale_items.quantity) as total,sales.date,sale_items.product_id ');
                $this->db->from('sale_items as sale_items');
                $this->db->join('sales as sales','sales.id = sale_items.sale_id','left');
                $this->db->where('sales.date >= ',$date_from." 00:00:00");
                $this->db->where('sales.date <= ',$date_to." 23:59:59");
                if($this->data['swarehouse'] != "0"){
                    $this->db->where('sales.warehouse_id',$this->data['swarehouse']);
                }
                else{

                }
                $this->db->where('sale_items.product_id',$product->id);
                $secondary = $this->db->get()->row();
                if($secondary->total == ""){
                    $temp['secondary'] = 0;
                }
                else{
                    $temp['secondary'] = decimalallow($secondary->total/$master_size,2);
                }
    
                $temp['closing'] = $temp['opening']+($temp['primary']-$temp['secondary']);
    
                $this->data['rows'][] = $temp;        
            }
        }
        $bc = array(
            array('link' => base_url(),'page' => lang('home')),
            array('link' => '#','page' => 'Reports'),
            array('link' => '#','page' => 'Stock Report')
        );
        $meta = array('page_title' => 'Stock Report','bc' => $bc);
        $this->page_construct2('reports/store_report', $meta, $this->data);

    }



}
