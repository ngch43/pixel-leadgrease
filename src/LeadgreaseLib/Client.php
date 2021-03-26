<?php

namespace App\LeadgreaseLib;
class Client
{
    private $url;
    private $method;
    // private $pixel;


    public function setUrl(string $url)
    {
        $this->url = $url;
    }
    public function getUrl()
    {
        return $this->url;
    }
    public function setMethod(string $method)
    {
        $this->method = $method;
    }
    public function getMethod()
    {
        return $this->method;
    }

    /* public function setPixel($pixel)
    {
        $this->pixel = $pixel;
    } */
    /* public function getPixel()
    {
        return $this->pixel;
    } */

    public function getPixelResponse($pixel_percent)
    {
        $random = rand(0,100);
        $pixel_response = "ko_pixel";
        if($pixel_percent >= $random){
            $pixel_response = "ok_pixel";
        }

        return $pixel_response;

    }

    public function getHeaders()
    {
        $headers = getallheaders();
        unset($headers['Host']);
        return $headers;
    }

    public function getFields()
    {
        if(!empty($_POST)){
            $fields = $_POST;
        }else {
            $json = file_get_contents('php://input');
            $fields = json_decode($json,true);
        }

        return ($fields) ? $fields:[];
    }


    public function getQuery()
    {
        $query = [];
        if(!empty($_GET))
            $query = $_GET;
        
        return $query;
    }

    public function getRequestMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    

    public function getInfo()
    {   
        $info = [];

        $info['headers'] = $this->getHeaders();
        $info['body'] = $this->getFields();
        $info['query'] = $this->getQuery();
        $info['method'] = $this->getRequestMethod();
        
        return $info;
    }

    public function sendInfo($url, $info){
        $curl_cliente = curl_init();
        curl_setopt($curl_cliente, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_cliente, CURLOPT_VERBOSE, true);
        curl_setopt($curl_cliente, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_cliente, CURLOPT_SSL_VERIFYPEER, 0);

        $method = strtoupper($info['method']);
        $body = $info['body'];
        $query = $info['query'];
        $headers = $info['headers'];

        $query = http_build_query($query);
        if($query)
            $url = $url.'?'.$query;
        
        curl_setopt($curl_cliente, CURLOPT_CUSTOMREQUEST, $method); 
        if ($method == 'POST'){
            curl_setopt($curl_cliente, CURLOPT_POST, 1);
        }

        //var_dump($headers);
        if(array_key_exists ('Content-Type', $headers) && $headers['Content-Type'] == 'application/x-www-form-urlencoded'){
            $body = http_build_query($body);
            curl_setopt($curl_cliente, CURLOPT_POSTFIELDS, $body);
            $headers['Content-Length'] = strlen($body); 
        }else{
            $json = json_encode($body);
            curl_setopt($curl_cliente, CURLOPT_POSTFIELDS,$json);
            $headers['Content-Length'] = strlen($json);  
        } 
        
         
        $request_headers = [];
        foreach ($headers as $key => $value) {
                array_push($request_headers, $key.": ".$value);
        }
        curl_setopt($curl_cliente, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($curl_cliente, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl_cliente, CURLOPT_URL, $url);
        // curl_setopt($curl_cliente,CURLINFO_HEADER_OUT);
        $response['body'] = curl_exec($curl_cliente);
        $response['effective_url'] = curl_getinfo($curl_cliente, CURLINFO_EFFECTIVE_URL);
        $response['code'] = curl_getinfo($curl_cliente, CURLINFO_HTTP_CODE);
        // $response['info'] = curl_getinfo($curl_cliente);


        if(!$response['code']){
            $response['code'] = 500;
        }
        curl_close($curl_cliente);

        return $response;
    }

    /* public function sendInfoTest($data){

        $curl_cliente = curl_init();
        curl_setopt($curl_cliente, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_cliente, CURLOPT_VERBOSE, true);
        curl_setopt($curl_cliente, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_cliente, CURLOPT_SSL_VERIFYPEER, 0);

       
        $headers = $data['headers'];
        $url = $data['url'];
        $method = strtoupper($data['method']);
        
        
        $pixel = $this->getPixelResponse();

        // $data['fields']['pixel'] = $this->getPixelResponse();
        $query = http_build_query([
            'client_response' => json_encode($data['fields']),
            'pixel' => $pixel
        ]);
        
        if( $method == 'GET'){
            $url = $data['url'].'?'.$query;
            // unset($headers['Content-Type']);
            
        }else{
            if ($method == 'POST'){
                curl_setopt($curl_cliente, CURLOPT_POST, 1);
            }else if($method == 'PUT'){
                curl_setopt($curl_cliente, CURLOPT_CUSTOMREQUEST, "PUT"); 
            }

            if($headers['Content-Type'] == 'application/x-www-form-urlencoded'){
                curl_setopt($curl_cliente, CURLOPT_POSTFIELDS,$query);
                $headers['Content-Length'] = strlen($query); 
            }else{
                $json = json_encode([
                    'client_response' => $data['fields'],
                    'pixel' => $pixel
                ]);
                curl_setopt($curl_cliente, CURLOPT_POSTFIELDS,$json);
                $headers['Content-Length'] = strlen($json);  
            }
            
        }
         
        $request_headers = [];
        foreach ($headers as $key => $value) {
            array_push($request_headers, $key.": ".$value);
        }
        curl_setopt($curl_cliente, CURLOPT_HTTPHEADER, $request_headers);

        curl_setopt($curl_cliente, CURLOPT_URL, $url);
        $response = curl_exec($curl_cliente);
        curl_close($curl_cliente);

        return $response;
    } */

}
