<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author    : Dabeer ul Hasan
 *  Email     : dabeer.hasan@gmail.com
 *  For       : Stock Manager Advance
 *  Web       : http://rholab.net
 *  ==============================================================================
 */

class Daraz_API
{

    /**
	 * Reference to the CodeIgniter instance
	 *
	 * @var object
	 */
	protected $CI;


    public function __construct()
    {

        $this->CI =& get_instance();
        $this->CI->load->helper('url');
        $this->CI->config->item('base_url');
        $this->CI->load->database();
    }

    public function get_update_product_daraz_api(Type $var = null)
    {
        $this->CI->db->select('GROUP_CONCAT(`id`) as id , GROUP_CONCAT(`code`) as code, GROUP_CONCAT(`company_code`) as company_code, GROUP_CONCAT(`name`) as name, GROUP_CONCAT(`cost`) as cost, GROUP_CONCAT(`price`) as price, GROUP_CONCAT(`dropship`) as dropship, GROUP_CONCAT(`crossdock`) as crossdock, GROUP_CONCAT(`mrp`) as mrp, GROUP_CONCAT(`quantity`) as quantity, username, apikey, GROUP_CONCAT(`sku_code`) as sku_code, GROUP_CONCAT(`pack_size`) as pack_size');
        $this->CI->db->from('sma_products');
        $this->CI->db->order_by("apikey", "asc");
        $this->CI->db->group_by("apikey");
        
        $check_index = 0;
        

        foreach (($this->CI->db->get()->result_array()) as $row) {
            if($row['sku_code'] && $row['username']) {


                // Random File Name Generate
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $GenerateFilename = 'update-product-'.substr(str_shuffle($permitted_chars), 0, 16).'.xml';

                $id = explode(",",$row['id']);
                $code = explode(",",$row['code']);
                $company_code = explode(",",$row['company_code']);
                $name = explode(",",$row['name']);
                $cost = explode(",",$row['cost']);
                $price = explode(",",$row['price']);
                $dropship = explode(",",$row['dropship']);
                $crossdock = explode(",",$row['crossdock']);
                $mrp = explode(",",$row['mrp']);
                $quantity = explode(",",$row['quantity']);
                $sku_code = explode(",",$row['sku_code']);
                $pack_size = explode(",",$row['pack_size']);

                /*********************
                // Create XML FILE USING PHP
                **********************/

                $dom = new DOMDocument();
                $dom->encoding = 'utf-8';
                $dom->xmlVersion = '1.0';
                $dom->formatOutput = true;
                $xml_file_name = "uploads/".$GenerateFilename;
                $root = $dom->createElement('Request');
                $roots = $dom->createElement('Product');
                $rootss = $dom->createElement('Skus');

                for ($i=0; $i < sizeof($id) ; $i++) { 
                    $movie_node = $dom->createElement('Sku');
                    $child_node_title = $dom->createElement('SellerSku', $sku_code[$i]);
                    $movie_node->appendChild($child_node_title);
                    $child_node_quantity = $dom->createElement('Quantity', floor($quantity[$i]/$pack_size[$i]));
                    $movie_node->appendChild($child_node_quantity);
                    $rootss->appendChild($movie_node);
                }

                /*********************
                // Create XML FILE USING PHP
                **********************/
                $roots->appendChild($rootss);
                $root->appendChild($roots);
                $dom->appendChild($root);
                $dom->save($xml_file_name);
                /*********************
                // Create XML FILE USING PHP
                **********************/
                $url[] .= "https://api.sellercenter.daraz.pk";

                $username[] .= $row['username']; 
                $apikey[] .= $row['apikey']; 
                $filename[] .= $xml_file_name;
            }
        }
        $this->Hit_Api_Daraz($url , $username, $apikey, sizeof($id), $filename);
    }


    public function Hit_Api_Daraz($urls, $username , $apikey, $loop_value, $filename)
    {

        //An array that will contain all of the information
        //relating to each request.
        $requests = array();
         
        //Initiate a multiple cURL handle
        $mh = curl_multi_init();
         
        //Loop through each URL.
        foreach($urls as $k => $url){

            $requests[$k] = array();
            $encoded = array();
            $concatenated = null;
            // $apikey = null;
            $requests[$k] = null;
            // $url = null;
            $queryString = null;
            $target = null;
            $tmpFile = null;
            $queryString = null;

            date_default_timezone_set("UTC");
            $now = new DateTime();
            $requests[$k] = array(
            'UserID' => $username[$k],
            'Version' => '1.0',
            'Action' => 'UpdatePriceQuantity',
            'Format' => 'JSON',
            'Limit' => '100',
            'Offset' => '0',
            'Timestamp' => $now->format(DateTime::ISO8601)
            );
            ksort($requests[$k]);
            $encoded = array();

            foreach ($requests[$k] as $name => $value) {
                $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
            }
            $concatenated = implode('&', $encoded);

            $requests[$k]['Signature'] = rawurlencode(hash_hmac('sha256', $concatenated, $apikey[$k], false));
            $requests[$k]['queryString'] = http_build_query($requests[$k], '', '&', PHP_QUERY_RFC3986);
            $requests[$k]['target'] = $url."?".$requests[$k]['queryString'];
            $requests[$k]['tmpFile'] = $filename[$k];

            //Create a normal cURL handle for this particular request.
            $requests[$k]['curl_handle'] = curl_init($url);
            //Configure the options for this request.

            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_PUT, 1 );
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_HEADER, true);
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_INFILESIZE, filesize($requests[$k]['tmpFile']) );
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_INFILE, ($in=fopen($requests[$k]['tmpFile'], 'r')) );
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_CUSTOMREQUEST, 'POST' );
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_HTTPHEADER, [ 'Content-Type:application/x-www-form-urlencoded' ] );
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_URL, $requests[$k]['target'] );
            curl_setopt( $requests[$k]['curl_handle'], CURLOPT_RETURNTRANSFER, 1 );
            curl_multi_add_handle($mh, $requests[$k]['curl_handle']);
        }
         
        //Execute our requests using curl_multi_exec.
        $stillRunning = false;
        do {
            curl_multi_exec($mh, $stillRunning);
        } while ($stillRunning);
         
        //Loop through the requests that we executed.
        foreach($requests as $k => $request){
            //Remove the handle from the multi handle.
            curl_multi_remove_handle($mh, $request['curl_handle']);
            //Get the response content and the HTTP status code.
            $requests[$k]['content'] = curl_multi_getcontent($request['curl_handle']);
            $requests[$k]['http_code'] = curl_getinfo($request['curl_handle'], CURLINFO_HTTP_CODE);
            //Close the handle.
            curl_close($requests[$k]['curl_handle']);
        }
        //Close the multi handle.
        curl_multi_close($mh);
        
        $path_file_name = FCPATH."uploads/";
        $files = glob($path_file_name.'*'); // get all file names
        foreach($files as $file){ // iterate files
        if(is_file($file))
            if (!unlink($file)) {  
                // echo ($file. " cannot be deleted due to an error <br>");  
            }  
            else {  
                // echo ($file. "has been deleted <br>");  
            } 
        }
        
    }


}
