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
    private $logger;
    private $settings; 
    public function __construct($object = null, $force_auth = false)
    {
        global $DIC;
        $this->logger =$DIC->logger()->root();
        $this->settings = Litello::getInstance()->config();
        $access_key = $this->settings->getValue("access_key");
        $secret_key = $this->settings->getValue("secret_key");
        $customer = $this->settings->getValue("customer");
        $proxy_host = $this->settings->getValue("proxy_host");
        $proxy_port = $this->settings->getValue("proxy_port");
        $this->setUserID();
        $this->object = $object;
        $bookID = $object->getBookID();
        $this->client=  new LitelloClient($access_key, $secret_key, $this->userID, $customer, $proxy_host, $proxy_port,$bookID, $force_auth);
        $this->setWebreader();
        
        
    }

    public function getToken()
    {
        return $this->client->getToken();
    }

    public function getAuthenticatedWebreaderURL(){
        
        return  $this->webreader ."authentication/". $this->getToken();
    }
    public function setUserID($userID = null)
    {
        global $ilUser;
        if (DEVMODE){
            //$this->userID ="jephte";
            $userLogin = $ilUser->getLogin();
            $this->userID = "s.olinger@globus.net";
        }else{
            $userLogin = $ilUser->getLogin();
            $this->userID = $userLogin;
        }
        
    }

    public function getHomeURL(){

    }

    public function setWebreader()
    {
        $webreader = $this->settings->getValue('webreader');
        if ($webreader=='' || $webreader == null){
            $webreader = "https://webreader.litello.com/";
        }
        if (substr($webreader, -1)!= '/'){
            $webreader = $webreader . "/";
        }
        $this->webreader =$webreader;
    }
    public function createBookURI(string $bookID)
    {
        
    }

    public function getProgressData(){
        return $this->client->requestProgress(0, $this->object->getBookID());

    }


}
