 <?php
/*
 * Created on Apr 6, 2012
 * 
 * We need to load the HTTP_Request2 package
 *
 */
 
//require_once '/Applications/MAMP/bin/php/php5.3.6/share/pear/HTTP/Request2.php';
//require_once '/Applications/MAMP/bin/php/php5.3.6/share/pear/HTTP/Request2/Response.php';
//require_once '/Applications/MAMP/bin/php/php5.3.6/share/pear/HTTP/Request2/CookieJar.php';

require_once 'HTTP/Request2.php';
require_once 'HTTP/Request2/Response.php';
require_once 'HTTP/Request2/CookieJar.php';

class RESTConnector {
	
    private $root_url = "";
    private $curr_url = "";
    private $httperror = "";
    private $exception = "";
    private $headers = "";
    private $responseBody = "";
    private $cookieJar = "";
    private $req = null;
    private $res = null;
    
    
    public function __construct($root_url = "") {
        $this->root_url = $this->curr_url = $root_url;
        if ($root_url != "") {
            $this->createRequest("GET");
            $this->sendRequest();
        }
        return true;
    }
    
    public function createRequest($url, $method, $body = null, $mycookies, $user, $pass) {
        $this->curr_url = $url;
        $this->req = new HTTP_Request2($url);

        $this->req->setHeader("User-Agent", "com.xsilva.kevin.cstmreporter/1.0"); // custom report widget

        // We also need to send our App Private ID in a header called X-PAPPID
        $this->req->setHeader("X-PAPPID", "fa0bfafd-42cd-4da5-8e00-7cc9e40ce8cc"); // custom report widget
        $this->req->setHeader("Accept-Encoding", "gzip");

        // A valid username and password must also be sent to connect correctly
        $this->req->setAuth($user, $pass);   //custom reporter
        $this->req->setAuth("api", "apiuser0");   //custom reporter

        // LightSpeed Server uses a custom ssl certificate, so we disable this verification.
        $this->req->setConfig('ssl_verify_peer', false);

        // Establish the Cookie Jar
        $this->req->setCookieJar();
    	    		
        // If a cookie already exists, add it to the Cookie Jar to maintain the session
        if ($mycookies!=null){
        	$this->req->addCookie($mycookies['name'], $mycookies['value']);
        }
        
        // Get current Cookie Jar
        $this->cookieJar = $this->req->getCookieJar();
        
    	switch($method) {
        	case "GET":
                $this->req->setMethod(HTTP_Request2::METHOD_GET);
                break;
            case "POST":
            	//echo "Post?" . "<br><br>";
                $this->req->setMethod(HTTP_Request2::METHOD_POST);
                $this->setPostBody($body);
                break;
            case "PUT":
                $this->req->setMethod(HTTP_Request2::METHOD_PUT);
                $this->setPostBody($body);              
                break;
            case "DELETE":
                $this->req->setMethod(HTTP_Request2::METHOD_DELETE);
                // to-do
                break;
            case "LOCK":
                $this->req->setMethod("LOCK");
                break;
            case "UNLOCK":
                $this->req->setMethod("UNLOCK");                
                break;
            /*default: 
            	$this->req->setMethod($method);
            	break;
            */
        }
    }
    
    private function setPostBody($data) {
        if ($data != null) {
            $this->req->setBody($data);
        }
    }
    
    public function addHeader($header, $value) {
    	if ($header != null &&  $value != null) {
    		$this->req->setHeader($header, $value);
    	}
    }
    
    public function sendRequest() {
    	try {
    		$this->res = $this->req->send();
    		if (200 <= $this->res->getStatus() && 206 >= $this->res->getStatus()) {
    			$this->responseBody = $this->res->getBody();
    		}
    		else {
    			$this->httperror = "Unexpected HTTP Status: " . $this->res->getStatus() . " " . $this->res->getReasonPhrase();
    		}
                //$this->headers = $this->res->getHeader();
    	}
    	catch (HTTP_Request2_Exception $e) {
    		$this->exception = "Error: " . $e->getMessage();
    	}
    }

    public function getResponse() {
        return $this->responseBody;
    }
    
    public function getError() {
        return $this->httperror;
    }
    
    public function getException() {
        return $this->exception;
    }
    
    public function getCookies() {
    	return $this->cookieJar->getAll();
    }
}

?>
