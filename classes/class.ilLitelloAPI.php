<?php 

require_once "./Customizing/global/plugins/Services/Repository/RepositoryObject/Litello/classes/litello/client.php";

use minervis\Litello\Repository as Litello;

/**
 * Class ilLitelloAPI
 *
 *
 * @author Minervis GmbH <jephte.abijuru@minervis.com>
 */
class ilLitelloAPI
{
    
    private $client;
    private $webreader;
    public function __construct()
    {
        $settings = Litello::getInstance()->config();
        $access_key = $settings->getValue("access_key");
        $secret_key = $settings->getValue("secret_key");
        $customer = $settings->getValue("customer");
        $userID = "Demouser";
        $proxy_host = $settings->getValue("proxy_host");
        $proxy_port = $settings->getValue("proxy_port");
        $this->client=  new LitelloClient($access_key, $secret_key, $userID, $customer, $proxy_host, $proxy_port);
        $this->webreader="https://webreader.litello.com/";
        
    }

    public function getToken()
    {
        return $this->client->getToken();
    }
    public function authenticateWebreader(){
        $token=$this->getToken();
        $this->client->buildRequest(
            "GET",
            "https://webreader.litello.com/authenticate/". $token,
        );
        $this->client->send();
    }

    public function getHomeURL(){

    }
    public function setWebreader(string $webreader)
    {
        $this->webreader=$webreader;
    }
    public function getWebreader()
    {
        return $this->webreader;
    }
    public function createBookURI(string $bookID)
    {
        
    }

}
