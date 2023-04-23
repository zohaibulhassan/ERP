<?php defined('BASEPATH') or exit('No direct script access allowed');
class Booker_targets extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    
    public function lists(){
        $m = strtolower(date('M'));
        $t = date('Y');
        $user_id = $this->input->post('user_id');

        $this->db->select('
            a.target_orders,
            (select COALESCE(count(so.id),0) from sma_sales as s join sma_sales_orders_tb as so on so.id = s.so_id where DATE_FORMAT(s.date,"%Y") = "'.$t.'" AND DATE_FORMAT(s.date,"%b") = "'.$m.'" AND so.created_by = "'.$user_id.'") as achieved_orders,
            ROUND((select COALESCE(count(so.id),0) from sma_sales as s join sma_sales_orders_tb as so on so.id = s.so_id where DATE_FORMAT(s.date,"%Y") = "'.$t.'" AND DATE_FORMAT(s.date,"%b") = "'.$m.'" AND so.created_by = "'.$user_id.'") / a.target_orders * 100,2) as order_percentage,

            a.target_amount,
            (select COALESCE(SUM(si.subtotal),0) from sma_sale_items as si join sma_sales as s on s.id = si.sale_id join sma_sales_orders_tb as so on so.id = s.so_id where DATE_FORMAT(s.date,"%Y") = "'.$t.'" AND DATE_FORMAT(s.date,"%b") = "'.$m.'" AND so.created_by = "'.$user_id.'") as achieved_amount,
            ROUND((select COALESCE(SUM(si.subtotal),0) from sma_sale_items as si join sma_sales as s on s.id = si.sale_id join sma_sales_orders_tb as so on so.id = s.so_id where DATE_FORMAT(s.date,"%Y") = "'.$t.'" AND DATE_FORMAT(s.date,"%b") = "'.$m.'" AND so.created_by = "'.$user_id.'")/a.target_amount * 100,2) as amount_percentafe,
            a.target_shop,
            (select count(t.id) from sma_users_tracker as t where DATE_FORMAT(t.created_at,"%Y") = "'.$t.'" AND DATE_FORMAT(t.created_at,"%b") = "'.$m.'" AND t.user_id = "'.$user_id.'" AND t.status = "0") as visited_shops,
            ROUND((select count(t.id) from sma_users_tracker as t where DATE_FORMAT(t.created_at,"%Y") = "'.$t.'" AND DATE_FORMAT(t.created_at,"%b") = "'.$m.'" AND t.user_id = "'.$user_id.'" AND t.status = "0") / a.target_shop * 100,2) as shops_percentage,
        ');
        $this->db->from('sma_booker_targets as a');
        $this->db->where('a.user_id',$user_id);
        $this->db->where('a.month',$m);
        $this->db->where('a.year',$t);
        $q = $this->db->get();
        // die('asdasd');
        // echo $this->db->last_query();
        // die();
        $this->data['targets'] = $q->row();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
}
