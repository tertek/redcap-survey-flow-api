<?php

// Set the namespace defined in your config file
namespace STPH\surveyQueueApi;

use RestUtility;
use Survey;
use Project;

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

// Declare your module class, which must extend AbstractExternalModule 
class surveyQueueApi extends \ExternalModules\AbstractExternalModule {

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
    public function processSurveyQueueRequest() {  

        $this->request = RestUtility::processRequest(false);
        $this->post = $this->request->getRequestVars();

        //  Check if request is valid
        if(!isset($this->post['project_id'])){
            RestUtility::sendResponse(400, "No project_id set.");
        }

        if(!isset($this->post['content']) || !isset($this->post['action'])) {
            RestUtility::sendResponse(400, "No content and/or action set.");
        }

        //  IMPORTANT: Set project id, constants and globals that are necessary within REDCap Classes (e.g. Survey)
        $this->project_id = $this->post['project_id'];
        $GLOBALS["Proj"]  = new Project($this->project_id);
        define("PROJECT_ID", $this->project_id);

        //$this->checkAuthentication();
        $this->handleEndpoint();
        //$this->handleResponse();
    }

    protected function handleEndpoint() {

        $res = Survey::getSurveyQueueForRecord(1);

        //$test = Survey::displaySurveyQueueForRecord(1);

        # Include endpoint to generate response
        //require ("endpoints/" . $this->post['content'] . "/" . $this->post['action']. ".php");

        //  For Development on Localhost 
        header('Access-Control-Allow-Origin: *'); 
        header("Access-Control-Allow-Headers: Content-Type");

        # Return response
        RestUtility::sendResponse(200, json_encode($res), 'json');

    }    

    
}