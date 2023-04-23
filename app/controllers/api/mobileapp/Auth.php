<?php defined('BASEPATH') or exit('No direct script access allowed');
class Auth extends App_Controller{
    function __construct(){
        parent::__construct();
        $this->load->config('ion_auth', TRUE);
        $this->tables = $this->config->item('tables', 'ion_auth');
        //initialize data
        $this->identity_column = $this->config->item('identity', 'ion_auth');
        $this->store_salt = $this->config->item('store_salt', 'ion_auth');
        $this->salt_length = $this->config->item('salt_length', 'ion_auth');
        $this->join = $this->config->item('join', 'ion_auth');
        //initialize hash method options (Bcrypt)
        $this->hash_method = $this->config->item('hash_method', 'ion_auth');
        $this->default_rounds = $this->config->item('default_rounds', 'ion_auth');
        $this->random_rounds = $this->config->item('random_rounds', 'ion_auth');
        $this->min_rounds = $this->config->item('min_rounds', 'ion_auth');
        $this->max_rounds = $this->config->item('max_rounds', 'ion_auth');
         //initialize messages and error
         $this->messages = array();
         $this->errors = array();
         $delimiters_source = $this->config->item('delimiters_source', 'ion_auth');
        //initialize our hooks object
        $this->_ion_hooks = new stdClass;
        //load the bcrypt class if needed
        if ($this->hash_method == 'bcrypt') {
            if($this->random_rounds){
                $rand = rand($this->min_rounds, $this->max_rounds);
                $rounds = array('rounds' => $rand);
            }
            else{
                $rounds = array('rounds' => $this->default_rounds);
            }
            $this->load->library('bcrypt', $rounds);
        }
    }
    public function trigger_events($events){
        if (is_array($events) && !empty($events)) {
            foreach ($events as $event) {
                $this->trigger_events($event);
            }
        } else {
            if (isset($this->_ion_hooks->$events) && !empty($this->_ion_hooks->$events)) {
                foreach ($this->_ion_hooks->$events as $name => $hook) {
                    $this->_call_hook($events, $name);
                }
            }
        }
    }
    public function salt(){
        return substr(md5(uniqid(rand(), true)), 0, $this->salt_length);
    }
    public function hash_password($password, $salt = false, $use_sha1_override = FALSE){
        if(empty($password)){
            return FALSE;
        }
        //bcrypt
        if($use_sha1_override === FALSE && $this->hash_method == 'bcrypt') {
            return $this->bcrypt->hash($password);
        }
        if($this->store_salt && $salt) {
            return sha1($password . $salt);
        }
        else{
            $salt = $this->salt();
            return $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }
    }
    public function hash_password_db($id, $password, $use_sha1_override = FALSE){
        if (empty($id) || empty($password)) {
            return FALSE;
        }
        $this->trigger_events('extra_where');
        $query = $this->db->select('password, salt')
            ->where('id', $id)
            ->limit(1)
            ->get($this->tables['users']);
        $hash_password_db = $query->row();
        if ($query->num_rows() !== 1) {
            return FALSE;
        }
        // bcrypt
        if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt') {
            if ($this->bcrypt->verify($password, $hash_password_db->password)) {
                return TRUE;
            }
            return FALSE;
        }
        // sha1
        if ($this->store_salt) {
            $db_password = sha1($password . $hash_password_db->salt);
        }
        else {
            $salt = substr($hash_password_db->password, 0, $this->salt_length);
            $db_password = $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }
        if ($db_password == $hash_password_db->password) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    public function login(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if($username != "" && $password != ""){
            $this->db->select('
                id,
                username,
                email,
                active,
                first_name,
                last_name,
                company,
                phone,
                password,
                gender,
                group_id,
                warehouse_id,
                biller_id,
                company_id,
                show_cost,
                show_price,
                award_points,
                view_right,
                edit_right,
                allow_discount
            ');
            $this->db->from('users');
            $this->db->where('username',$username);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $user = $q->result()[0];
                // $ip_address = $this->_prepare_ip($this->input->ip_address());
                $salt = $this->store_salt ? $this->salt() : FALSE;
                $password = $this->hash_password_db($user->id,$password);
                if($user->password == $password){
                    if($user->active == 1){
                        unset($user->password);
                        $this->data['login_data'] = $user;
                        $this->data['code_status'] = true;
                        $this->data['message'] = "Success!";
                    }
                    else{
                        $this->data['message'] = "Your account deactive!";
                        $this->data['error_code'] = '004';
                    }
                }
                else{
                    $this->data['message'] = "Password not match!";
                    $this->data['error_code'] = '004';
                }
            }
            else{
                $this->data['message'] = "Username not found!";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
    public function check_permission(){
        $id = $this->input->post('user_id');
        if($id != ""){
            $this->db->select('
                id,
                username,
                email,
                active,
                first_name,
                last_name,
                company,
                phone,
                password,
                gender,
                group_id,
                warehouse_id,
                biller_id,
                company_id,
                show_cost,
                show_price,
                award_points,
                view_right,
                edit_right,
                allow_discount
            ');
            $this->db->from('users');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $user = $q->result()[0];
                if($user->active == 1){
                    $this->data['user'] = $user;
                    $this->db->select('permissions.*,groups.name');
                    $this->db->from('permissions');
                    $this->db->join('groups','groups.id = permissions.group_id','left');
                    $q2 = $this->db->get();
                    if($q2->num_rows()>0){
                        $this->data['group_permission'] = $q2->result()[0];
                        $this->data['code_status'] = true;
                        $this->data['message'] = "Success!";
                    }
                    else{
                        $this->data['message'] = "Group permission not found!";
                        $this->data['error_code'] = '004';
                    }
                }
                else{
                    $this->data['message'] = "Your account deactive!";
                    $this->data['error_code'] = '004';
                }
            }
            else{
                $this->data['message'] = "Invalid user!";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
    public function tracker(){
        $user_id = $this->input->post('user_id');
        $gps_latitude = $this->input->post('gps_latitude');
        $gps_longitude = $this->input->post('gps_longitude');
        $battery = $this->input->post('battery');
        if($battery == ""){
            $battery = 0;
        }
        if($user_id != ""){
            $this->db->select('id');
            $this->db->from('users');
            $this->db->where('id',$user_id);
            $q = $this->db->get();
            if($q->num_rows() > 0 ){

                // $insert['user_id'] = $user_id;
                $set['latitude'] = $gps_latitude;
                $set['longitude'] = $gps_longitude;
                $set['mobile_battery'] = $battery;
                $this->db->set($set);
                $this->db->where('id',$user_id);
                $this->db->update('users');
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Invalid user!";
                $this->data['error_code'] = '004';
            }
        }
        else{
            $this->data['message'] = "Required Query Parameter Null!";
            $this->data['error_code'] = '003';
        }
        $this->responsedata();
    }
    public function punched(){
        $user_id = $this->input->post('user_id');
        $gps_latitude = $this->input->post('gps_latitude');
        $gps_longitude = $this->input->post('gps_longitude');
        $gsm_latitude = $this->input->post('gsm_latitude');
        $gsm_longitude = $this->input->post('gsm_longitude');
        $battery = $this->input->post('battery');
        $route_id = $this->input->post('route_id');
        $shop_id = $this->input->post('shop_id');
        $type = $this->input->post('type');
        $status = $this->input->post('status');
        if($battery == ""){
            $battery = 0;
        }
        if($user_id != ""){
            $this->db->select('id');
            $this->db->from('users');
            $this->db->where('id',$user_id);
            $q = $this->db->get();
            if($q->num_rows() > 0 ){

                $insert['user_id'] = $user_id;
                $insert['gps_latitude'] = $gps_latitude;
                $insert['gps_longitude'] = $gps_longitude;
                $insert['gsm_latitude'] = $gsm_latitude;
                $insert['gsm_longitude'] = $gsm_longitude;
                $insert['battery'] = $battery;
                $insert['route_id'] = $route_id;
                $insert['shop_id'] = $shop_id;
                $insert['type'] = $type;
                $insert['status'] = $status;
                $this->db->insert('users_tracker',$insert);
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Invalid user!";
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
        $user_id = $this->input->post('user_id');
        if($user_id != ""){
            $this->db->select('id,username,email,first_name,last_name,active,company,phone,gender,group_id,warehouse_id,company_id,latitude,longitude,mobile_battery');
            $this->db->from('users');
            $this->db->where('id',$user_id);
            $q = $this->db->get();
            if($q->num_rows() > 0 ){
                $user = $q->result()[0];
                $this->data['user'] = $user;
                $this->data['code_status'] = true;
                $this->data['message'] = "Success!";
            }
            else{
                $this->data['message'] = "Invalid user!";
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
