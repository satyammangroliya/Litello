<?php

require_once __DIR__ . "./../../vendor/autoload.php";
use Aws\Signature\SignatureV4;
use Aws\Credentials\Credentials;
use GuzzleHttp\Psr7\Request;

use GuzzleHttp\Client;

use function Aws\filter;

/**
 * Class LitelloClient
 *
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
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
        int $proxy_port = null,
        int $bookID = 0,
        bool $request_auth_token = false
    )
    {        
        $this->credentials = new Credentials($access_key, $secret_key);
        $this->proxy_host = $proxy_host;
        $this->proxy_port = $proxy_port;
        $this->customer = $customer;

        if($request_auth_token){
            $this->requestAuthToken($userID, $bookID);
        }
        
         
    }
    public function getToken()
    {
        return $this->token;
    }

    public function withUserId($user_id){
        $this->user_id = $user_id;
        return $this;
    }
    public function withBookId($book_id){
        $this->book_id = $book_id;
        return $this;
    }

    public function buildRequest($req_pars)
    {
        $http_request = new Request($req_pars["method"], $req_pars["uri"], $req_pars["headers"], $req_pars["body"]);
        $signature = new SignatureV4("execute-api","eu-central-1");
        $req = $signature->signRequest($http_request, $this->credentials);
        $client_options = [];
         if ($this->proxy_host!=null){
            $client_options =[ "proxy" => "$this->proxy_host:$this->proxy_port" ];
         }
         $client = new Client($client_options);        
         $response= $client->send($req);
         if($response->getStatusCode() == 200){
            $response=json_decode($response->getBody());
            return $response;
            
         }else{
            throw new Exception('Request Failed! please inform the administrator');
         }          
    }
    public function requestAuthToken($userID, $bookID){
        $body = array( 
            "userId" => $userID,
            "bookId" => $bookID
        );
        $headers=[
            "content-type"=>"application/json",
            //"API-Key" => "lIKPG122e1aB0wZVS9qBV7e6Ce7bLpU1aZTreOnv"
        ];
        $req_params = array(
            "method" => "POST",
            "uri" => "https://api.litello.com/$this->customer/token",
            "headers"=> $headers,
            "body" => json_encode($body)
        );
        $response = $this->buildRequest($req_params);
        $this->token = $response->token;        
    }
    public function requestProgress($userID = 0, $bookID = 0)
    {
        $filter = '';
        if($bookID != 0){
            $filter .= $bookID . "/";
            if($userID != 0){
                $filter .= $userID . "/";
            }
        }
        $headers=[
            "content-type"=>"application/json",
            "API-Key" => "lIKPG122e1aB0wZVS9qBV7e6Ce7bLpU1aZTreOnv"
        ];
        $req_params = array(
            "method" => "GET",
            "uri" => "https://api.litello.com/data/book/progress/$filter",
            "headers"=> $headers
        );
        $response = $this->buildRequest($req_params, $headers);
        return $response;        
    }

    public function createRequest($method, $uri, array $headers=[], $body=null){
        $this->request= new Request($method, $uri, $headers, $body);
    }
    public function setHTTPClient($client){
        $this->client=$client;
    }
    public function getHTTPClient(){
        return  $this->client;
    }
    
    
    
}
