<?php defined('BASEPATH') OR exit('No direct script access allowed'); //Write by Ismail FSD

class Shopify_model extends CI_Model
{
    
    public function __construct(){
        parent::__construct();
    }
    public function update_qyt($link,$token_key,$tocken_secret,$product_id,$qunaity){
        $sendata['codestatus'] = false;
        $product_data = $this->get_product($link,$token_key,$tocken_secret,$product_id,$qunaity);
        if($product_data['codestatus']){
            if(isset($product_data['product']->variants[0]->inventory_item_id)){
                $inventoryitemid = $product_data['product']->variants[0]->inventory_item_id;
                $inventory_data = $this->inventory_levels_detail($link,$token_key,$tocken_secret,$inventoryitemid);
                if($inventory_data['codestatus']){

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $link.'/admin/api/2022-10/inventory_levels/set.json',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_POSTFIELDS =>'{
                        "location_id":'.$inventory_data['inventory']->location_id.',
                        "inventory_item_id":'.$inventory_data['inventory']->inventory_item_id.',
                        "available":'.$qunaity.'
                    }',
                      CURLOPT_HTTPHEADER => array(
                        'X-Shopify-Access-Token: '.$tocken_secret,
                        'Content-Type: application/json',
                        'Cookie: _master_udr=eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaEpJaWt4T1RsaFltTm1OQzFtWldNd0xUUmpNRFl0T0RKbFpDMWxNR1l5TWpZek1UTmpORGtHT2daRlJnPT0iLCJleHAiOiIyMDI0LTExLTA4VDA4OjA1OjE5LjEwNVoiLCJwdXIiOiJjb29raWUuX21hc3Rlcl91ZHIifX0%3D--95c71f75237ab91ded768dc309293a6015c5e274; _secure_admin_session_id=b3ee3ac784cf814d63723925007c497e; _secure_admin_session_id_csrf=b3ee3ac784cf814d63723925007c497e; identity-state=BAhbCkkiJTUxMzM5ODRiNjMwN2NlYTE0OWYxYmQ2NGQ5YTRlZDRmBjoGRUZJIiViMjZlZGFiYjllNWM4ZjQyYTdlMjVlNmU5OTc3NGIxZAY7AEZJIiU4MTk5NDE3Mjk5OWE1ZmUzZDI0YTIxOTllNjFkY2EwYQY7AEZJIiUwNTY2ZGVhMjlkZDU4ZjVjN2E0MWIyZDg3NmM0ZjkwMQY7AEZJIiU2MmY4N2EyNjAwN2M1MzYyY2MzODJhZGVhNzRhZDc4MQY7AEY%3D--543342a25174a76cb1d51545696a8c033675a469; identity-state-0566dea29dd58f5c7a41b2d876c4f901=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhYxNjY3ODk4NTQ5LjAwODA5OEkiCm5vbmNlBjsAVEkiJTY0MTY1YTk1ZWEwYTFjZWQ5Yzg0YzU4M2FkMzMxYWVmBjsARkkiCnNjb3BlBjsAVFsPSSIKZW1haWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9kZXN0aW5hdGlvbnMucmVhZG9ubHkGOwBUSSILb3BlbmlkBjsAVEkiDHByb2ZpbGUGOwBUSSJOaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9wYXJ0bmVycy5jb2xsYWJvcmF0b3ItcmVsYXRpb25zaGlwcy5yZWFkb25seQY7AFRJIjBodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL2JhbmtpbmcubWFuYWdlBjsAVEkiQmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvbWVyY2hhbnQtc2V0dXAtZGFzaGJvYXJkLmdyYXBocWwGOwBUSSI8aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9zaG9waWZ5LWNoYXQuYWRtaW4uZ3JhcGhxbAY7AFRJIjdodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL2Zsb3cud29ya2Zsb3dzLm1hbmFnZQY7AFRJIj5odHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL29yZ2FuaXphdGlvbi1pZGVudGl0eS5tYW5hZ2UGOwBUSSIPY29uZmlnLWtleQY7AFRJIgxkZWZhdWx0BjsAVA%3D%3D--0dd0ec1c4320618d1235eda14f647f0e95ef6cff; identity-state-5133984b6307cea149f1bd64d9a4ed4f=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4MDg5Ljg3NjE5NDdJIgpub25jZQY7AFRJIiU3NWYwNTkyODFhZDg1MjljMmIyMmJkZjYwNTg3YTY3ZgY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--dc8a45903b572030686c0005221eb5ddca689c8f; identity-state-62f87a26007c5362cc382adea74ad781=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4NTUxLjAzNTkwNjhJIgpub25jZQY7AFRJIiUxMGQ3Y2VkODllMDMxYWFiYTYxYmY2NGZlY2Q1MGVmNwY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--9bc988c3bc5117495e3f61149677f6c75ab5ee4a; identity-state-81994172999a5fe3d24a2199e61dca0a=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4Mzg2LjY4NzE1MTdJIgpub25jZQY7AFRJIiU4YmNlMGNlMzQxMzZlODZmYWMyY2U3ZWRhNjE3ZTcyZQY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--a6d36d45fc4817a804fb8cb4bc3be464b7eb91c8; identity-state-b26edabb9e5c8f42a7e25e6e99774b1d=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4Mjc0LjQxMDA2MjZJIgpub25jZQY7AFRJIiU5NDMyZjAwNjlkN2E0NGMzZDhjMWQ4Y2YzYTQ5MWJiZAY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--2348f77365f56be44baa0ee10a87415f37ddb335'
                      ),
                    ));
                    $update_data = curl_exec($curl);
                    
                    curl_close($curl);
                    $update_data = json_decode($update_data);
                    if(isset($update_data->inventory_level->available)){
                        $sendata['message'] = 'Shopify Stock Update Successfully';
                        $sendata['codestatus'] = true;
                    }
                    else{
                        if(isset($update_data->errors)){
                            $sendata['message'] = $update_data->errors;
                            $sendata['error_code'] = 5;
                        }
                        else{
                            $sendata['message'] = 'Shopify Stock Not Update';
                            $sendata['error_code'] = 4;
                        }
                    }
                }
                else{
                    $sendata['message'] = $inventory_data['message'];
                    $sendata['error_code'] = 3;
                }
            }
            else{
                $sendata['message'] = 'Shopify Inventory Item ID Not Found';
                $sendata['error_code'] = 2;
            }
        }
        else{
            $sendata['message'] = $product_data['message'];
            $sendata['error_code'] = 1;
        }
        return $sendata;
    }
    public function get_product($link,$token_key,$tocken_secret,$product_id){
        $link = str_replace("https://","",$link);;
        $sendata['codestatus'] = false;
        $sendata['message'] = 'Shopify Product Not Found2';
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://'.$token_key.':'.$tocken_secret.'@'.$link.'/admin/api/2022-10/products/'.$product_id.'.json',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'X-Shopify-Access-Token: '.$tocken_secret,
            'Cookie: _master_udr=eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaEpJaWt4T1RsaFltTm1OQzFtWldNd0xUUmpNRFl0T0RKbFpDMWxNR1l5TWpZek1UTmpORGtHT2daRlJnPT0iLCJleHAiOiIyMDI0LTExLTA4VDA4OjA1OjE5LjEwNVoiLCJwdXIiOiJjb29raWUuX21hc3Rlcl91ZHIifX0%3D--95c71f75237ab91ded768dc309293a6015c5e274; _secure_admin_session_id=b3ee3ac784cf814d63723925007c497e; _secure_admin_session_id_csrf=b3ee3ac784cf814d63723925007c497e; identity-state=BAhbCkkiJTUxMzM5ODRiNjMwN2NlYTE0OWYxYmQ2NGQ5YTRlZDRmBjoGRUZJIiViMjZlZGFiYjllNWM4ZjQyYTdlMjVlNmU5OTc3NGIxZAY7AEZJIiU4MTk5NDE3Mjk5OWE1ZmUzZDI0YTIxOTllNjFkY2EwYQY7AEZJIiUwNTY2ZGVhMjlkZDU4ZjVjN2E0MWIyZDg3NmM0ZjkwMQY7AEZJIiU2MmY4N2EyNjAwN2M1MzYyY2MzODJhZGVhNzRhZDc4MQY7AEY%3D--543342a25174a76cb1d51545696a8c033675a469; identity-state-0566dea29dd58f5c7a41b2d876c4f901=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhYxNjY3ODk4NTQ5LjAwODA5OEkiCm5vbmNlBjsAVEkiJTY0MTY1YTk1ZWEwYTFjZWQ5Yzg0YzU4M2FkMzMxYWVmBjsARkkiCnNjb3BlBjsAVFsPSSIKZW1haWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9kZXN0aW5hdGlvbnMucmVhZG9ubHkGOwBUSSILb3BlbmlkBjsAVEkiDHByb2ZpbGUGOwBUSSJOaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9wYXJ0bmVycy5jb2xsYWJvcmF0b3ItcmVsYXRpb25zaGlwcy5yZWFkb25seQY7AFRJIjBodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL2JhbmtpbmcubWFuYWdlBjsAVEkiQmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvbWVyY2hhbnQtc2V0dXAtZGFzaGJvYXJkLmdyYXBocWwGOwBUSSI8aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9zaG9waWZ5LWNoYXQuYWRtaW4uZ3JhcGhxbAY7AFRJIjdodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL2Zsb3cud29ya2Zsb3dzLm1hbmFnZQY7AFRJIj5odHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL29yZ2FuaXphdGlvbi1pZGVudGl0eS5tYW5hZ2UGOwBUSSIPY29uZmlnLWtleQY7AFRJIgxkZWZhdWx0BjsAVA%3D%3D--0dd0ec1c4320618d1235eda14f647f0e95ef6cff; identity-state-5133984b6307cea149f1bd64d9a4ed4f=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4MDg5Ljg3NjE5NDdJIgpub25jZQY7AFRJIiU3NWYwNTkyODFhZDg1MjljMmIyMmJkZjYwNTg3YTY3ZgY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--dc8a45903b572030686c0005221eb5ddca689c8f; identity-state-62f87a26007c5362cc382adea74ad781=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4NTUxLjAzNTkwNjhJIgpub25jZQY7AFRJIiUxMGQ3Y2VkODllMDMxYWFiYTYxYmY2NGZlY2Q1MGVmNwY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--9bc988c3bc5117495e3f61149677f6c75ab5ee4a; identity-state-81994172999a5fe3d24a2199e61dca0a=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4Mzg2LjY4NzE1MTdJIgpub25jZQY7AFRJIiU4YmNlMGNlMzQxMzZlODZmYWMyY2U3ZWRhNjE3ZTcyZQY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--a6d36d45fc4817a804fb8cb4bc3be464b7eb91c8; identity-state-b26edabb9e5c8f42a7e25e6e99774b1d=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4Mjc0LjQxMDA2MjZJIgpub25jZQY7AFRJIiU5NDMyZjAwNjlkN2E0NGMzZDhjMWQ4Y2YzYTQ5MWJiZAY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--2348f77365f56be44baa0ee10a87415f37ddb335'
          ),
        ));
        $data = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($data);
        if(isset($data->product)){
            $sendata['product'] = $data->product;
            $sendata['codestatus'] = true;
            $sendata['message'] = 'Shopify Product Found';
        }
        else{
            if(isset($data->errors)){
                $sendata['message'] = $data->errors;
            }
        }
        return $sendata;
    }
    public function inventory_levels_detail($link,$token_key,$tocken_secret,$inventoryitemid){
        $sendata['codestatus'] = false;
        $sendata['message'] = 'Shopify Inventory Not Found';
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $link.'/admin/api/2022-10/inventory_levels.json?inventory_item_ids='.$inventoryitemid,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
        'X-Shopify-Access-Token: '.$tocken_secret,
        'Cookie: _master_udr=eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaEpJaWt4T1RsaFltTm1OQzFtWldNd0xUUmpNRFl0T0RKbFpDMWxNR1l5TWpZek1UTmpORGtHT2daRlJnPT0iLCJleHAiOiIyMDI0LTExLTA4VDA4OjA1OjE5LjEwNVoiLCJwdXIiOiJjb29raWUuX21hc3Rlcl91ZHIifX0%3D--95c71f75237ab91ded768dc309293a6015c5e274; _secure_admin_session_id=b3ee3ac784cf814d63723925007c497e; _secure_admin_session_id_csrf=b3ee3ac784cf814d63723925007c497e; identity-state=BAhbCkkiJTUxMzM5ODRiNjMwN2NlYTE0OWYxYmQ2NGQ5YTRlZDRmBjoGRUZJIiViMjZlZGFiYjllNWM4ZjQyYTdlMjVlNmU5OTc3NGIxZAY7AEZJIiU4MTk5NDE3Mjk5OWE1ZmUzZDI0YTIxOTllNjFkY2EwYQY7AEZJIiUwNTY2ZGVhMjlkZDU4ZjVjN2E0MWIyZDg3NmM0ZjkwMQY7AEZJIiU2MmY4N2EyNjAwN2M1MzYyY2MzODJhZGVhNzRhZDc4MQY7AEY%3D--543342a25174a76cb1d51545696a8c033675a469; identity-state-0566dea29dd58f5c7a41b2d876c4f901=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhYxNjY3ODk4NTQ5LjAwODA5OEkiCm5vbmNlBjsAVEkiJTY0MTY1YTk1ZWEwYTFjZWQ5Yzg0YzU4M2FkMzMxYWVmBjsARkkiCnNjb3BlBjsAVFsPSSIKZW1haWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9kZXN0aW5hdGlvbnMucmVhZG9ubHkGOwBUSSILb3BlbmlkBjsAVEkiDHByb2ZpbGUGOwBUSSJOaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9wYXJ0bmVycy5jb2xsYWJvcmF0b3ItcmVsYXRpb25zaGlwcy5yZWFkb25seQY7AFRJIjBodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL2JhbmtpbmcubWFuYWdlBjsAVEkiQmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvbWVyY2hhbnQtc2V0dXAtZGFzaGJvYXJkLmdyYXBocWwGOwBUSSI8aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9zaG9waWZ5LWNoYXQuYWRtaW4uZ3JhcGhxbAY7AFRJIjdodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL2Zsb3cud29ya2Zsb3dzLm1hbmFnZQY7AFRJIj5odHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL29yZ2FuaXphdGlvbi1pZGVudGl0eS5tYW5hZ2UGOwBUSSIPY29uZmlnLWtleQY7AFRJIgxkZWZhdWx0BjsAVA%3D%3D--0dd0ec1c4320618d1235eda14f647f0e95ef6cff; identity-state-5133984b6307cea149f1bd64d9a4ed4f=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4MDg5Ljg3NjE5NDdJIgpub25jZQY7AFRJIiU3NWYwNTkyODFhZDg1MjljMmIyMmJkZjYwNTg3YTY3ZgY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--dc8a45903b572030686c0005221eb5ddca689c8f; identity-state-62f87a26007c5362cc382adea74ad781=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4NTUxLjAzNTkwNjhJIgpub25jZQY7AFRJIiUxMGQ3Y2VkODllMDMxYWFiYTYxYmY2NGZlY2Q1MGVmNwY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--9bc988c3bc5117495e3f61149677f6c75ab5ee4a; identity-state-81994172999a5fe3d24a2199e61dca0a=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4Mzg2LjY4NzE1MTdJIgpub25jZQY7AFRJIiU4YmNlMGNlMzQxMzZlODZmYWMyY2U3ZWRhNjE3ZTcyZQY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--a6d36d45fc4817a804fb8cb4bc3be464b7eb91c8; identity-state-b26edabb9e5c8f42a7e25e6e99774b1d=BAh7DEkiDnJldHVybi10bwY6BkVUSSI3aHR0cHM6Ly9lY29taWNzdG9yZS5teXNob3BpZnkuY29tL2FkbWluL2F1dGgvbG9naW4GOwBUSSIRcmVkaXJlY3QtdXJpBjsAVEkiQ2h0dHBzOi8vZWNvbWljc3RvcmUubXlzaG9waWZ5LmNvbS9hZG1pbi9hdXRoL2lkZW50aXR5L2NhbGxiYWNrBjsAVEkiEHNlc3Npb24ta2V5BjsAVDoMYWNjb3VudEkiD2NyZWF0ZWQtYXQGOwBUZhcxNjY3ODk4Mjc0LjQxMDA2MjZJIgpub25jZQY7AFRJIiU5NDMyZjAwNjlkN2E0NGMzZDhjMWQ4Y2YzYTQ5MWJiZAY7AEZJIgpzY29wZQY7AFRbD0kiCmVtYWlsBjsAVEkiN2h0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvZGVzdGluYXRpb25zLnJlYWRvbmx5BjsAVEkiC29wZW5pZAY7AFRJIgxwcm9maWxlBjsAVEkiTmh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvcGFydG5lcnMuY29sbGFib3JhdG9yLXJlbGF0aW9uc2hpcHMucmVhZG9ubHkGOwBUSSIwaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9iYW5raW5nLm1hbmFnZQY7AFRJIkJodHRwczovL2FwaS5zaG9waWZ5LmNvbS9hdXRoL21lcmNoYW50LXNldHVwLWRhc2hib2FyZC5ncmFwaHFsBjsAVEkiPGh0dHBzOi8vYXBpLnNob3BpZnkuY29tL2F1dGgvc2hvcGlmeS1jaGF0LmFkbWluLmdyYXBocWwGOwBUSSI3aHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9mbG93LndvcmtmbG93cy5tYW5hZ2UGOwBUSSI%2BaHR0cHM6Ly9hcGkuc2hvcGlmeS5jb20vYXV0aC9vcmdhbml6YXRpb24taWRlbnRpdHkubWFuYWdlBjsAVEkiD2NvbmZpZy1rZXkGOwBUSSIMZGVmYXVsdAY7AFQ%3D--2348f77365f56be44baa0ee10a87415f37ddb335'
        ),
        ));
        $data = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($data);
        if(count($data->inventory_levels) > 0){
            $sendata['inventory'] = $data->inventory_levels[0];
            $sendata['codestatus'] = true;
            $sendata['message'] = 'Shopify Inventory Found';
            
        }
        return $sendata;
    }
    public function updateprice($link,$token_key,$tocken_secret,$product_id,$rp,$sp){
        $sendata['codestatus'] = false;
        $sendata['product_id'] = $product_id;


        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $link.'/admin/api/2022-10/products/'.$product_id.'.json',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'X-Shopify-Access-Token: '.$tocken_secret
          ),
        ));
        $data = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($data);
        if(isset($data->product->variants[0]->id)){
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $link.'/admin/api/2022-10/variants/'.$data->product->variants[0]->id.'.json',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'PUT',
              CURLOPT_POSTFIELDS =>'{
                "variant": {
                    "id": '.$data->product->variants[0]->id.',
                    "price": '.$sp.',
                    "compare_at_price": '.$rp.'
                }
            }',
              CURLOPT_HTTPHEADER => array(
                'X-Shopify-Access-Token: '.$tocken_secret,
                'Content-Type: application/json',
                'Cookie: _master_udr=eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaEpJaWt4T1RsaFltTm1OQzFtWldNd0xUUmpNRFl0T0RKbFpDMWxNR1l5TWpZek1UTmpORGtHT2daRlJnPT0iLCJleHAiOiIyMDI0LTExLTA4VDA4OjA1OjE5LjEwNVoiLCJwdXIiOiJjb29raWUuX21hc3Rlcl91ZHIifX0%3D--95c71f75237ab91ded768dc309293a6015c5e274; _secure_admin_session_id=b3ee3ac784cf814d63723925007c497e; _secure_admin_session_id_csrf=b3ee3ac784cf814d63723925007c497e'
              ),
            ));
            $data = curl_exec($curl);
            
            curl_close($curl);
            $data = json_decode($data);
            echo '<pre>';
            print_r($data);
            $sendata['vid'] = $data->product->variants[0]->id;
            if(isset($data->variant)){
                $sendata['message'] = 'Shopify Stock Update Successfully';
                $sendata['codestatus'] = true;
            }
            else{
                if(isset($data->errors)){
                    $sendata['message'] = $data->errors;
                    $sendata['error_code'] = 5;
                }
                else{
                    $sendata['message'] = 'Shopify Stock Not Update';
                    $sendata['error_code'] = 4;
                }
            }
        }
        else{
            $sendata['message'] = 'Shopify product variat not found';
            $sendata['error_code'] = 4;
            $sendata['data'] = $data;
        }










        return $sendata;
        
    }
    public function getproducts($store,$last,$limit){
        $sendvalue['codestatus'] = false;
        try {
            $last_q = '';
            if($last != 0){
                $last_q = '&last_id='.$last;
            }
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $store->store_url.'/admin/api/2022-10/products.json?limit='.$limit.'&&order=id&direction=next'.$last_q,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'X-Shopify-Access-Token: '.$store->wordpress_wocommerce_consumer_secret,
                'Content-Type: application/json'
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);
            if(isset($data->products)){
                $sendvalue['products'] = $data->products;
                $sendvalue['codestatus'] = true;
                $sendvalue['message'] = "Successfully";
            }
            else{
                $sendvalue['message'] = "Product not found";
            }
        }
        catch(Exception $e) {
            $sendvalue['message'] = "Store Request Failed: ".$e->getMessage();
        }
        return $sendvalue;
    }
    public function getOrders($store,$last,$limit="50",$status="any"){
        $sendvalue['codestatus'] = false;
        try {
            $last_q = 'status='.$status.'&limit='.$limit;
            if($last != ""){
                $last_q = '&last_id='.$last;
            }
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $store->store_url.'/admin/api/2022-10/orders.json?'.$last_q,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'X-Shopify-Access-Token: '.$store->wordpress_wocommerce_consumer_secret,
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);
            if(isset($data->orders)){
                $sendvalue['orders'] = $data->orders;
                $sendvalue['codestatus'] = true;
                $sendvalue['message'] = "Successfully";
            }
            else{
                $sendvalue['message'] = "Orders not found";
            }
        }
        catch(Exception $e) {
            $sendvalue['message'] = "Store Request Failed: ".$e->getMessage();
        }
        return $sendvalue;


        




    }
    public function getOrder($store,$code){
        $sendvalue['codestatus'] = false;
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $store->store_url.'/admin/api/2022-10/orders/'.$code.'.json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'X-Shopify-Access-Token: '.$store->wordpress_wocommerce_consumer_secret,
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);
            if(isset($data->order)){
                $sendvalue['order'] = $data->order;
                $sendvalue['codestatus'] = true;
                $sendvalue['message'] = "Successfully";
            }
            else{
                $sendvalue['message'] = "Orders not found";
            }
        }
        catch(Exception $e) {
            $sendvalue['message'] = "Store Request Failed: ".$e->getMessage();
        }
        return $sendvalue;


        




    }
}