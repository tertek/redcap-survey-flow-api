<?php

// Set the namespace defined in your config file
namespace STPH\surveyFlowApi;

use RestUtility;
use Survey;
use Project;

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

// Declare your module class, which must extend AbstractExternalModule 
class surveyFlowApi extends \ExternalModules\AbstractExternalModule {

    private $project_id;
    private $request;
    private $post;
    private $record;

    private $JWTtoken;
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

    // For Testing only

    function redcap_every_page_top() {
        //print_r(Survey::getProjectSurveyQueue());
        //print_r(Survey::getSurveyQueueForRecord(1));
    }

    private function generateToken($user) {

       /**  PHP package for JWT
        *   https://github.com/firebase/php-jwt
        *   
        */

        $secret = "example_key";

        $payload = array(
            "iss" => "STPH",
            "aud" => "STPH",
            "user" => $user
        );
        
        $jwt = JWT::encode($payload, $secret);
        //$this->jwt = $jwt;
        //$decoded = JWT::decode($jwt, $key, array('HS256'));
                
        return $jwt;
        

    }

    private function isValid($var) {
        if( isset($var) && !empty($var)) {
            return true;
        }
        return false;
    }


    # Process Survey Flow API request as REDCap API request 
    # without REDCap API token (false) since we're using our own token
    public function processSurveyFlowRequest() {  

        global $returnFormat; // Need to set this as global for RestUtility
        $returnFormat = "json";

        $this->request = RestUtility::processRequest(false);

        //  To Do: Escape && Sanitize vars before use
        $this->post = $this->request->getRequestVars();

        if( !$this->isValid($this->post['node']) ) {
            RestUtility::sendResponse(400, "Bad Request - node is required");
        }

        if( !$this->isValid($this->post['action']) ) {
            RestUtility::sendResponse(400, "Bad Request - action is required");
        }


        //$this->checkAuthentication();
        $this->handleEndpoint();
        //$this->handleResponse();
    }

    protected function handleEndpoint() {
        
        //  For Development on Localhost 
        header('Access-Control-Allow-Origin: *'); 
        header("Access-Control-Allow-Headers: Content-Type");

        //$test = Survey::displaySurveyQueueForRecord(1);

        # Include endpoint to generate response
        require ("endpoints/" . $this->post['node'] . "." . $this->post['action']. ".php");

        # Return response
        RestUtility::sendResponse(200, json_encode($res), 'json');

    }    

    
}