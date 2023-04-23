<?php defined('BASEPATH') or exit('No direct script access allowed');
class Customers extends App_Controller{
    function __construct(){
        parent::__construct();
    }
    public function lists(){
        $text = $this->input->post('text');
        $page = $this->input->post('page');
        $limit = $this->input->post('limit');
        $route_id = $this->input->post('route_id');
        $type = $this->input->post('type');
        $direct_customer  = $this->input->post('direct_customer ');
        $distributor_id = $this->input->post('distributor_id');
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
            id,
            sales_type,
            name,
            company,
            phone,
            email,
            cnic,
            latitude,
            longitude,
        ');
        $this->db->from('sma_companies');
        $this->db->where('group_name','customer');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        if($text != ""){
            $this->db->where('(name LIKE "%'.$text.'%" OR company LIKE "%'.$text.'%")');
        }
        if($route_id != ""){
            $this->db->where('route_id',$route_id);
        }
        if($type != ""){
            $this->db->where('customer_type',$type);
        }
        if($direct_customer != ""){
            $this->db->where('direct_customer',$direct_customer);
        }
        if($distributor_id != ""){
            $this->db->where('distributor_id',$distributor_id);
        }
        $q = $this->db->get();
        $this->data['customers'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }
    public function lists_with_distance(){
        $text = $this->input->post('text');
        $page = $this->input->post('page');
        $limit = $this->input->post('limit');
        $route_id = $this->input->post('route_id');
        $type = $this->input->post('type');
        $direct_customer  = $this->input->post('direct_customer ');
        $distributor_id = $this->input->post('distributor_id');
        $user_longitude = $this->input->post('user_longitude');
        if($user_longitude == ""){
            $user_longitude == 0;
        }
        $user_latitude = $this->input->post('user_latitude');
        if($user_latitude == ""){
            $user_latitude == 0;
        }
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
            id,
            sales_type,
            name,
            company,
            phone,
            email,
            cnic,
            111.111 * DEGREES(
                ACOS(LEAST(
                    1.0, COS(RADIANS(latitude))
                    * COS(RADIANS("'.$user_latitude.'"))
                    * COS(RADIANS(longitude - "'.$user_longitude.'"))
                    + SIN(RADIANS(latitude))
                    * SIN(RADIANS("'.$user_latitude.'"))
                ))
            ) AS distance_in_km
        ');
        $this->db->from('sma_companies');
        $this->db->where('group_name','customer');
        if($start == 0){
            $this->db->limit($end);
        }
        else{
            $this->db->limit($start,$end);
        }
        if($text != ""){
            $this->db->where('(name LIKE "%'.$text.'%" OR company LIKE "%'.$text.'%")');
        }
        if($route_id != ""){
            $this->db->where('route_id',$route_id);
        }
        if($type != ""){
            $this->db->where('customer_type',$type);
        }
        if($direct_customer != ""){
            $this->db->where('direct_customer',$direct_customer);
        }
        if($distributor_id != ""){
            $this->db->where('distributor_id',$distributor_id);
        }
        $q = $this->db->get();
        $this->data['customers'] = $q->result();
        $this->data['code_status'] = true;
        $this->data['message'] = "Success!";
        $this->responsedata();
    }

    public function create(){
        $customer_type = $this->input->post('customer_type');
        $direct_customer = $this->input->post('direct_customer');
        $distributor_id = $this->input->post('distributor_id');
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $cnic = $this->input->post('cnic');
        $address = $this->input->post('address');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $postal = $this->input->post('postal');
        $vat = $this->input->post('vat');
        $gst = $this->input->post('gst');
        $ntn = $this->input->post('ntn');
        $route_id = $this->input->post('route_id');
        $lati = $this->input->post('lati');
        if($lati == ""){ $lati = 0; }
        $long = $this->input->post('long');
        if($long == ""){ $long = 0; }
        if($customer_type != "" && $direct_customer != "" &&  $name != "" &&  $phone != "" &&  $cnic != "" &&  $address != "" &&  $city != ""  &&  $country != ""  &&  $postal != "" && $route_id != ""){
            $insert['group_id'] = 3;
            $insert['group_name'] = 'customer';
            $insert['sales_type'] = 'consignment';
            $insert['customer_type'] = $customer_type;
            $insert['direct_customer'] = $direct_customer;
            $insert['distributor_id'] = $distributor_id;
            $insert['name'] = $name; //a
            $insert['company'] = $name; //a
            $insert['vat_no'] = $vat; //a
            $insert['address'] = $address;
            $insert['city'] = $city;
            $insert['state'] = $state;
            $insert['postal_code'] = $postal;
            $insert['country'] = $country;
            $insert['phone'] = $phone; //a
            $insert['cnic'] = $cnic; //a
            $insert['email'] = $email;
            $insert['gst_no'] = $gst; //a
            $insert['cf1'] = $ntn; //a
            $insert['route_id'] = $route_id;
            $insert['longitude'] = $long;
            $insert['latitude'] = $lati;
            $insert['payment_term'] = 0;
            $insert['logo'] = 'logo.png';
            $insert['award_points'] = 0;
            $this->db->select('id');
            $this->db->from('sma_companies');
            $where = 'name = "'.$name.'"';
            if($vat != ""){
                $where .= ' OR vat_no = "'.$vat.'"';
            }
            $where .= ' OR phone = "'.$phone.'"';
            $where .= ' OR cnic = "'.$cnic.'"';
            if($ntn != ""){
                $where .= ' OR cf1 = "'.$ntn.'"';
            }
            if($gst != ""){
                $where .= ' OR gst_no = "'.$gst.'"';
            }
            $this->db->where($where);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $cdata = $q->result()[0];
                if($cdata->name == $name){
                    $this->data['message'] = "Name Already Available";
                }
                else if($cdata->phone == $phone){
                    $this->data['message'] = "Phone Already Available";
                }
                else if($cdata->cnic == $cnic){
                    $this->data['message'] = "CNIC Already Available";
                }
                else if($cdata->gst_no == $gst_no){
                    $this->data['message'] = "GST No Already Available";
                }
                else if($cdata->vat_no == $vat){
                    $this->data['message'] = "VAT No Already Available";
                }
                else if($cdata->cf1 == $ntn){
                    $this->data['message'] = "NTN Already Available";
                }

                $this->data['error_code'] = '004';
            }
            else{
                $this->db->insert('sma_companies',$insert);
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
                }
        }
        else{
            $m = "Required Query Parameter Null!";

            $this->data['message'] = $m;
            $this->data['error_code'] = '003';
        }
        $this->responsedata();

    }
    public function coordinates_update(){
        $customer_id = $this->input->post('customer_id');
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        if($latitude == ""){ $latitude = 0; }
        if($longitude == ""){ $longitude = 0; }
        if($customer_id != "" && $latitude != "" && $longitude != ""){
            $this->db->select('id');
            $this->db->from('sma_companies');
            $this->db->where('id',$customer_id);
            $q = $this->db->get();
            if($q->num_rows() > 0 ){
                $set['longitude'] = $longitude;
                $set['latitude'] = $latitude;
                $this->db->set($set);
                $this->db->where('id',$customer_id);
                $this->db->update('sma_companies');
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Invalid customer!";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
    public function data(){
        $customer_id = $this->input->post('customer_id');
        if($customer_id != ""){
            $this->db->select('*');
            $this->db->from('sma_companies');
            $this->db->where('group_name','customer');
            $this->db->where('id',$customer_id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['customers'] = $q->result();
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Invalid customer!";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }




        $this->responsedata();
    }
    public function data_with_distance(){
        $customer_id = $this->input->post('customer_id');
        $user_longitude = $this->input->post('user_longitude');
        if($user_longitude == ""){
            $user_longitude == 0;
        }
        $user_latitude = $this->input->post('user_latitude');
        if($user_latitude == ""){
            $user_latitude == 0;
        }
        $this->data['user_longitude'] = $user_longitude;
        $this->data['user_latitude'] = $user_latitude;
        if($customer_id != ""){
            $this->db->select('
                *,
                111.111 *
                DEGREES(
                    ACOS(LEAST(
                        1.0, COS(RADIANS(latitude))
                        * COS(RADIANS("'.$user_latitude.'"))
                        * COS(RADIANS(longitude - "'.$user_longitude.'"))
                        + SIN(RADIANS(latitude))
                        * SIN(RADIANS("'.$user_latitude.'"))
                    ))
                ) AS distance_in_km
            ');
            $this->db->from('sma_companies');
            $this->db->where('group_name','customer');
            $this->db->where('id',$customer_id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['customers'] = $q->result();
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Invalid customer!";
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
