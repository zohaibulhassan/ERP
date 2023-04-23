<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD
class DarazAPI_model extends CI_Model
{
    public function updateProduct($darazdata){
        $sendvalue['codestatus'] = 'ok';
        try {
            if($darazdata['store_product_sku'] && $darazdata['daraz_username'] && $darazdata['daraz_api_key']) {
                // Generate XML File
                $xmlfilename = 'update-product-'.date("Y").date("m").date("d").date("H").date("i").date("s").'_'.rand(1000,9999).'.xml';
                $dom = new DOMDocument('1.0');
                $dom->encoding = 'utf-8';
                $dom->xmlVersion = '1.0';
                $dom->formatOutput = true;
                $xml_file_name = "files/xml/".$xmlfilename;
                $root = $dom->createElement('Request');
                $roots = $dom->createElement('Product');
                $rootss = $dom->createElement('Skus');
                $movie_node = $dom->createElement('Sku');
                $child_node_title = $dom->createElement('SellerSku', $darazdata['store_product_sku']);
                $movie_node->appendChild($child_node_title);
                $child_node_quantity = $dom->createElement('Quantity', $darazdata['quantity']);
                $movie_node->appendChild($child_node_quantity);
                $rootss->appendChild($movie_node);
                $roots->appendChild($rootss);
                $root->appendChild($roots);
                $dom->appendChild($root);
                
                /*$xmlTxt = "<?xml version='1.0' encoding='UTF-8'?>";
                $xmlTxt .= "<Request>";
                    $xmlTxt .= "<Product>";
                        $xmlTxt .= "<Skus>";
                            $xmlTxt .= "<Sku>";
                                $xmlTxt .= "<SellerSku>".$darazdata['store_product_sku']."</SellerSku>";
                                $xmlTxt .= "<Quantity>".$darazdata['quantity']."</Quantity>";
                                $xmlTxt .= "<Price/>";
                                $xmlTxt .= "<SalePrice/>";
                                $xmlTxt .= "<SaleStartDate/>";
                                $xmlTxt .= "<SaleEndDate/>";
                            $xmlTxt .= "</Sku>";
                        $xmlTxt .= "</Skus>";
                    $xmlTxt .= "</Product>";
                $xmlTxt .= "</Request>";
                $dom->loadXML($xmlTxt);*/

                $dom->save($xml_file_name);
                // Set Request Variable
                $username = $darazdata['daraz_username'];
                date_default_timezone_set("UTC");
                $now = new DateTime();
                $requests = array(
                    'Action' => 'UpdatePriceQuantity',
                    'Format' => 'JSON',
                    'Timestamp' => $now->format(DateTime::ISO8601),
                    'UserID' => $username,
                    'Version' => '1.0'
                );
                ksort($requests);
                $encoded = array();
                foreach ($requests as $name => $value) {
                    $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
                }
                $concatenated = implode('&', $encoded);
                $apikey = $darazdata['daraz_api_key']; 
                $apisignature = rawurlencode(hash_hmac('sha256', $concatenated, $apikey, false));
                $requests['Signature'] = $apisignature;
                // Send Request To Daraz
                $filename = $xml_file_name;
                $url = "https://api.sellercenter.daraz.pk/";
                $queryString = http_build_query($requests, '', '&', PHP_QUERY_RFC3986);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url."?".$queryString);
                curl_setopt($ch, CURLOPT_PUT,1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);//new
                // curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_INFILESIZE,filesize($filename));
                curl_setopt($ch, CURLOPT_INFILE,($in=fopen($filename, 'r')));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type:application/x-www-form-urlencoded' ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                // Close Curl connection
                curl_close($ch);
                $response = json_decode($response);
                if(isset($response->SuccessResponse)){
                    $sendvalue['codestatus'] = "ok";
                    $sendvalue['darazresponse'] = $response;
                }
                else{
                    $sendvalue['codestatus'] = $response->ErrorResponse->Head->ErrorMessage.'. '.$response->ErrorResponse->Body->Errors[0]->Message;
                }
            }
            else{
                if($darazdata['store_product_sku']) {
                    $sendvalue['codestatus'] = "Store Product SKU Not Found";
                }
                else if($darazdata['daraz_username']) {
                    $sendvalue['codestatus'] = "Daraz Username Not Found";
                }
                else if($darazdata['daraz_api_key']) {
                    $sendvalue['codestatus'] = "Daraz API Key Not Found";
                }
            }

        }
        catch(Exception $e) {
            $sendvalue['codestatus'] = "Store Request Failed: ".$e->getMessage();
        }
        return $sendvalue;
    }
    public function getproduct($darazdata){
        $sendvalue['codestatus'] = 'ok';
        try {
            if($darazdata['store_product_sku'] && $darazdata['daraz_username'] && $darazdata['daraz_api_key']) {
                $username = $darazdata['daraz_username'];
                date_default_timezone_set("UTC");
                $now = new DateTime();
                $requests = array(
                    'Action' => 'GetProducts',
                    'Format' => 'JSON',
                    'Timestamp' => $now->format(DateTime::ISO8601),
                    'UserID' => $username,
                    'SkuSellerList' => json_encode(array($darazdata['store_product_sku'])),
                    'Version' => '1.0'
                );
                ksort($requests);
                $encoded = array();
                foreach ($requests as $name => $value) {
                    $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
                }
                $concatenated = implode('&', $encoded);
                $apikey = $darazdata['daraz_api_key']; 
                $apisignature = rawurlencode(hash_hmac('sha256', $concatenated, $apikey, false));
                $requests['Signature'] = $apisignature;
                // Send Request To Daraz
                $url = "https://api.sellercenter.daraz.pk/";
                $queryString = http_build_query($requests, '', '&', PHP_QUERY_RFC3986);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url."?".$queryString);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);//new
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
                $product = json_decode($response, true);
                // $sendvalue['tproduct'] = $product['SuccessResponse']; 
                if(isset($product['SuccessResponse'])){
                    if(count($product['SuccessResponse']['Body']['Products']) > 0){
                        $sendvalue['product'] = $product['SuccessResponse']['Body']['Products'][0]; 
                    }
                    else{
                        $sendvalue['codestatus'] = "SKU not found";
                    }
                }
                else{
                    $sendvalue['codestatus'] = "SKU not found";
                }
            }
        }
        catch(Exception $e) {
            $sendvalue['codestatus'] = "Store Request Failed: ".$e->getMessage();
        }
        return $sendvalue;
    }
}