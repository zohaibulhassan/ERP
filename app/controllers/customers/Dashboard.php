<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            admin_redirect('login');
        }

        if ($this->Customer || $this->Supplier) {
            redirect('/');
        }

        $this->load->library('form_validation');
        $this->load->admin_model('db_model');
    }

    public function index()
    {

        $user_id = $this->session->userdata('user_id');

        // $this->data['user_details'] = $this->db_model->getUsersDetails($user_id);
        // $user_details = $this->data['user_details'][0]->biller_id;

        // $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        // $this->data['sales'] = $this->db_model->getLatestSales($user_details);
        // $this->data['quotes'] = $this->db_model->getLastestQuotes();
        // $this->data['purchases'] = $this->db_model->getLatestPurchases($user_details);
        // $this->data['transfers'] = $this->db_model->getLatestTransfers();
        // $this->data['customers'] = $this->db_model->getLatestCustomers();
        // $this->data['suppliers'] = $this->db_model->getLatestSuppliers();
        // $this->data['chatData'] = $this->db_model->getChartData($user_details);
        // $this->data['stock'] = $this->db_model->getStockValue();
        // $this->data['bs'] = $this->db_model->getBestSeller($user_details);
        
        // $lmsdate = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
        // $lmedate = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
        // $this->data['lmbs'] = $this->db_model->getBestSeller($user_details, $lmsdate, $lmedate);
        // $bc = array(array('link' => '#', 'page' => lang('dashboard')));
        // $meta = array('page_title' => lang('dashboard'), 'bc' => $bc);

        // $this->page_construct2('dashboard', $meta, $this->data);

    }


}
