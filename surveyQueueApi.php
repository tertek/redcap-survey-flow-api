<?php

// Set the namespace defined in your config file
namespace STPH\surveyQueueApi;

use RestUtility;

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

// Declare your module class, which must extend AbstractExternalModule 
class surveyQueueApi extends \ExternalModules\AbstractExternalModule {

    private $moduleName = "Survey Queue API";  
    private $JWTtoken = "";

    private $request;
    private $post;
    private $jwt;


   /**
    * Constructs the class
    *
    */
    public function __construct()
    {        
        parent::__construct();
       // Other code to run when object is instantiated
    }

   /**
    * Renders the module
    *
    */

    public function generateToken() {

        $key = "example_key";

        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

        
        $jwt = JWT::encode($payload, $key);
        $this->jwt = $jwt;

        //$decoded = JWT::decode($jwt, $key, array('HS256'));

                
        return 'Token: '.$jwt;
        

    }

    # Process Survey Queue API request as REDCap API request 
    # without REDCap API token (false) since we're using our own token
    public function processTestingRequest() {

        $this->request = RestUtility::processRequest(false);
        $this->post = $this->request->getRequestVars();

        //$this->checkAuthentication();
        $this->handleEndpoint();
        //$this->handleResponse();
    }

    protected function handleEndpoint() {
        if(!isset($this->post['content']) || !isset($this->post['action'])) {
            RestUtility::sendResponse(400, "No content and/or action set.");
        }

        # Include endpoint to generate response
        //require ("endpoints/" . $this->post['content'] . "/" . $this->post['action']. ".php");

        $this->response = "Hello World";

        # Return response
        RestUtility::sendResponse(200, json_encode($this->response), 'json');

    }    

    
}