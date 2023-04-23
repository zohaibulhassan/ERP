<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class Daraz_model extends CI_Model
{
    public function getproducts($req, $page = 1,$limit = 100){
        $startoffset = ($limit * $page) - $limit;
        $sendvalue['codestatus'] = false;
        try {
            // The current time. Needed to create the Timestamp parameter below.
            $now = new DateTime();
            // The parameters for the GET request. These will get signed.
            $parameters = array(
                // The ID of the user making the call.
                'UserID' => $req->daraz_store_id,
                // The API version. Currently must be 1.0
                'Version' => '1.0',
                // The API method to call.
                'Action' => 'GetProducts',
                // The format of the result.
                'Format' => 'JSON',
                // The current time in ISO8601 format
                // The filter of the result.
                'Filter' => 'live',
                // The limit of the result.
                'Limit' => $limit,
                // The offset of the result.
                'Offset' => $startoffset,
                'Timestamp' => $now->format(DateTime::ISO8601)
            );
            // Sort parameters by name.
            ksort($parameters);
            // URL encode the parameters.
            $encoded = array();
            foreach ($parameters as $name => $value) {
                $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
            }
            // Concatenate the sorted and URL encoded parameters into a string.
            $concatenated = implode('&', $encoded);
            // The API key for the user as generated in the Seller Center GUI.
            // Must be an API key associated with the UserID parameter.
            $api_key = $req->daraz_api_key;
            // Compute signature and add it to the parameters.
            $parameters['Signature'] =
            rawurlencode(hash_hmac('sha256', $concatenated, $api_key, false));
            // ...continued from above
            // Replace with the URL of your API host.
            $url = "https://api.sellercenter.daraz.pk/";
            // Build Query String
            $queryString = http_build_query($parameters, '', '&',
            PHP_QUERY_RFC3986);
            // Open cURL connection
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."?".$queryString);
            // Save response to the variable $data
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $data = curl_exec($ch);
            $getInfo = json_decode($data, true);
            //$getInfo['SuccessResponse']['Body']['Products'][0]['Skus'][0]['quantity'];
            // return $getInfo;
            $sendvalue['darazproducts'] = $getInfo['SuccessResponse']['Body'];
            $sendvalue['codestatus'] = true;
            $sendvalue['message'] = "Successfully";
        }
        //catch exception
        catch(Exception $e) {
            $sendvalue['message'] = "Store Request Failed: ".$e->getMessage();
        }
        return $sendvalue;
        // Close Curl connection
        curl_close($ch);
    }
    public function get_update_product_daraz_api($darazdata = null, Type $var = null){
        $sendvalue['codestatus'] = 'ok';
        try {
            $check_index = 0;
            if($darazdata['store_product_sku'] && $darazdata['daraz_username']) {
                // Random File Name Generate
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $GenerateFilename = 'update-product-'.substr(str_shuffle($permitted_chars), 0, 16).'.xml';
                /*********************
                // Create XML FILE USING PHP
                **********************/
                $dom = new DOMDocument('1.0');
                $dom->encoding = 'utf-8';
                $dom->xmlVersion = '1.0';
                $dom->formatOutput = true;
                $xml_file_name = "files/".$GenerateFilename;
                $root = $dom->createElement('Request');
                $roots = $dom->createElement('Product');
                $rootss = $dom->createElement('Skus');
                $movie_node = $dom->createElement('Sku');
                $child_node_title = $dom->createElement('SellerSku', $darazdata['store_product_sku']);
                $movie_node->appendChild($child_node_title);
                $child_node_quantity = $dom->createElement('Quantity', $darazdata['quantity']);
                $movie_node->appendChild($child_node_quantity);
                $rootss->appendChild($movie_node);
                /*********************
                // Create XML FILE USING PHP
                **********************/
                $roots->appendChild($rootss);
                $root->appendChild($roots);
                $dom->appendChild($root);
                $dom->save($xml_file_name);
                //print_r($xml_file_name);
                /*********************
                // Create XML FILE USING PHP
                **********************/
                $url= array();
                $username= array();
                $apikey= array();
                $filename= array();
                $url[] .= "https://api.sellercenter.daraz.pk/";
                $username[] .= $darazdata['daraz_username']; 
                $apikey[] .= $darazdata['daraz_api_key']; 
                $filename[] .= $xml_file_name;
            }
            $sendvalue['daraz_output'] = $this->Hit_Api_Daraz($url , $username, $apikey, sizeof($darazdata), $filename);
        }
        //catch exception
        catch(Exception $e) {
            $sendvalue['codestatus'] = "Store Request Failed: ".$e->getMessage();
        }
        return $sendvalue;  
    }
    public function Hit_Api_Daraz($urls, $username , $apikey, $loop_value, $filename){    
        $returndata['daraz_reponse'] = array();	
        $returndata['daraz_request'] = array();
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
            $returndata['daraz_reponse'][] = curl_multi_exec($mh, $stillRunning);
        } while ($stillRunning);
        //Loop through the requests that we executed.
        foreach($requests as $k => $request){
            //Remove the handle from the multi handle.
            curl_multi_remove_handle($mh, $request['curl_handle']);
            //Get the response content and the HTTP status code.
            $requests[$k]['content'] = curl_multi_getcontent($request['curl_handle']);
            $requests[$k]['http_code'] = curl_getinfo($request['curl_handle'], CURLINFO_HTTP_CODE);
            //Close the handle.
            $returndata['daraz_request'][] = $requests[$k];	
            curl_close($requests[$k]['curl_handle']);
        }
        //Close the multi handle.
        curl_multi_close($mh);
        $path_file_name = FCPATH."files/";
        $files = glob($path_file_name.'*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)){
                if (!unlink($file)) {
                    // echo ($file. " cannot be deleted due to an error <br>");  
                }  
                else {  
                    // echo ($file. "has been deleted <br>");  
                } 
            }
        }

        return json_encode($returndata);
    }
}
