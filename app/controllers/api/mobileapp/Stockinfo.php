<?php defined('BASEPATH') or exit('No direct script access allowed');
class Stockinfo extends App_Controller{
    function __construct(){
        parent::__construct();
    }

    function stocks(){
        $customer_id = $this->input->post('customer_id');
        $this->db->select('
            a.product_id, 
            pro.name as product_name,
            pro.image as product_image,
            COALESCE((select c.quantity from sma_sales_order_items as c join sma_sales_orders_tb as d on d.id = c.so_id where c.product_id = a.product_id and d.customer_id ="'.$customer_id.'" order by c.id DESC limit 1),0) as order_quanity,
            COALESCE((select e.quantity from sma_stock_items_tb as e join sma_stock_info_tb as f on f.id = e.stock_info_id where e.product_id = a.product_id and f.customer_id = "'.$customer_id.'" order by e.id DESC limit 1),0) as stock_quanity,
            ');
        $this->db->where('b.customer_id',$customer_id);
        $this->db->from('sma_sales_order_items as a');
        $this->db->join('sma_sales_orders_tb as b','b.id = a.so_id');
        $this->db->join('sma_products as pro','pro.id = a.product_id');
        $this->db->group_by('a.product_id');

        // $this->db->select('a.product_id, SUM(a.quantity) as order_quantity, COALESCE((select SUM(p.quantity) from sma_stock_items_tb as p join sma_stock_info_tb as q on q.id = p.stock_info_id where a.product_id = p.product_id and b.customer_id = q.customer_id ),0) as stock_quantity, b.customer_id');
        // $this->db->from('sma_sales_order_items as a');
        // $this->db->join('sma_sales_orders_tb as b','b.id = a.so_id');
        // $this->db->group_by('a.product_id');
        $q = $this->db->get();

        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->data['stocks'] = $q->result();
        $this->responsedata();
    }

    public function add(){
        $customer_id = $this->input->post('customer_id');
        $created_by = $this->input->post('created_by');
        $items = $this->input->post('items');
        if($items != "" && $created_by != "" && $customer_id != ""){
            $items = json_decode($items);
            $insert['customer_id'] = $customer_id;
            $insert['created_by'] = $created_by;
            $insert_items = array();
            foreach($items as $item){
                $this->db->select('id,cost,price,mrp');
                $this->db->from('sma_products');
                $this->db->where('id',$item->product_id);
                $q = $this->db->get();
                if($q->num_rows() > 0){
                    $product = $q->result()[0];
                    $insert_items[] = array(
                        'stock_info_id'=>0,
                        'product_id'=>$item->product_id,
                        'quantity'=>$item->quantity,
                        'cost'=>$product->cost,
                        'consignment'=>$product->price,
                        'mrp'=>$product->mrp
                    );
                }
            }
            if(count($insert_items) > 0){
                $this->db->insert('stock_info_tb',$insert);
                $stock_id = $this->db->insert_id();
                foreach($insert_items as $row){
                    $row['stock_info_id'] = $stock_id;
                    $this->db->insert('stock_items_tb',$row);
                }
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Invalid Products";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
    public function lists(){
        $page = $this->input->post('page');
        $limit = $this->input->post('limit');
        $user_id = $this->input->post('user_id');
        if($page == "" || $page <= 0){
            $page = 1;
        }
        if($limit == "" || $limit > 20){
            $limit = 20;
        }
        else if($limit <= 5){
            $limit = 5;
        }
        $end=$limit*$page;
        $start=$end-$limit;
        $this->db->select('
            s.*,
            c.name as customer,
            (
                SELECT COUNT(si.product_id) FROM sma_stock_items_tb as si WHERE si.stock_info_id = s.id
            ) as products,
            (
                SELECT SUM(si.quantity) FROM sma_stock_items_tb as si WHERE si.stock_info_id = s.id
            ) as qty

        ');
        $this->db->from('sma_stock_info_tb as s');
        // $this->db->join('sma_stock_items_tb as si','si.stock_info_id = si.id','left');
        $this->db->join('sma_companies as c','c.id = s.customer_id','left');
        if($user_id != 0 && $user_id != ""){
            $this->db->where('s.created_by'.$user_id);
        }
        $this->db->group_by('s.id');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        $q = $this->db->get();
        $this->data['stocks'] = $q->result();
        $this->responsedata();
    }
    public function detail(){
        $id = $this->input->post('id');
        if($id != "" && $id != 0){
            $this->db->select('
                s.*,
                c.name as customer,
            ');
            $this->db->from('sma_stock_info_tb as s');
            $this->db->join('sma_companies as c','c.id = s.customer_id','left');
            $this->db->where('s.id'.$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['stock_info'] = $q->result()[0];
                $this->db->select('
                    si.*,
                    p.name as product_name
                ');
                $this->db->from('sma_stock_items_tb as si');
                $this->db->join('sma_products as p','p.id = si.product_id','left');
                $this->db->where('si.stock_info_id',$id);
                $this->data['items'] = $this->db->get()->result();
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";

            }
            else{
                $this->data['message'] = "Invalid Stock Info ID";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();


    }

}
