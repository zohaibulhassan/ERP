<?php defined('BASEPATH') OR exit('No direct script access allowed');

class App_Controller extends CI_Controller {

    public $data = array();
    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
        $secret_key = "jfds3=jsldf38&r923m-cjowscdlsdfi03";
        $this->data['code_status'] = false;
        $this->data['message'] = "Access Denied!";
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $get_secret_key = $this->input->post('secret_key');
            if($get_secret_key == $secret_key){

            }
            else{
                $this->data['message'] = "Access Denied!";
                $this->data['error_code'] = '002';
                $this->responsedata();
            }
        }
        else{
            $this->data['message'] = "Access Denied!";
            $this->data['error_code'] = '001';
            $this->responsedata();
        }
    }
    public function responsedata($json = true){
        if($json){
            echo json_encode($this->data);
        }
        else{
            print_r($this->data);
        }
        exit();
    }



}
