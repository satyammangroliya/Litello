<?php

require_once __DIR__ . "./../../vendor/autoload.php";
use Aws\Signature\SignatureV4;
use Aws\Credentials\Credentials;
use GuzzleHttp\Psr7\Request;

use GuzzleHttp\Client;

class LitelloClient
{
    private $signature;
    private $request;
    private $credentials;
    private $token ='';
    
    
    public function __construct(
        string $access_key,
        string $secret_key,
        string $userID,
        string $customer,
        string $proxy_host = null,
        int $proxy_port = null
    )
    {
        
        $credentials = new Credentials($access_key, $secret_key);
        $body = array( "userId" => $userID);
        $headers=[
            "content-type"=>"application/json",
        ];
        $req=array(
            "method" => "POST",
            "uri" => "https://api.litello.com/$customer/token",
            "headers"=> $headers,
            "body" => json_encode($body)
        );
        $request =new Request(
            $req["method"],
            $req["uri"],
            $req["headers"],
            $req["body"]
         );
         $signature=new SignatureV4("execute-api","eu-central-1");
         $this->request=$signature->signRequest($request,$credentials, "execute-api");
         $client_options=[];
         if ($proxy_host!=null){
             $client_options =[ "proxy" => "$proxy_host:$proxy_port" ];
         }
         $this->client=new Client($client_options);        
         $response= $this->client->send($this->request);
         if($response->getStatusCode()==200){
            $response=json_decode($response->getBody());
            $this->token=$response->token;
            
         }        
         
    }
    public function getToken()
    {
        return $this->token;
    }
    public function getRequest()
    {
        return $this->request;
    }

    public function send($client=null)
    {
        $response='';
        if($client==null){
            $response=$this->client->send($this->request);
        }else{
            $response=$client->send($this->request);
        }
        return $response;

    }
    public function buildRequest($method, $uri, array $headers=[], $body=null){
        $this->request= new Request($method, $uri, $headers, $body);
                

    }
    public function setHTTPClient($client){
        $this->client=$client;
    }
    public function getHTTPClient(){
        return  $this->client;
    }
    
    
    
}
