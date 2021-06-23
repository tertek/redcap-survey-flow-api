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

    private $request;
    private $response = array();
    private $project_id;
    private $config;
    private $post;
    private $record;

    private $JWTtoken;
    private $jwt;
    private $userInfo;

    private $test;
    private $pass;


   /**
    * Constructs the class
    *
    */
    public function __construct()
    {        
        parent::__construct();
       // Other code to run when object is instantiated
    }

    function redcap_every_page_top() {
        //print_r(Survey::getProjectSurveyQueue());
        //print_r(Survey::getSurveyQueueForRecord(1));
    }

    # Process Survey Flow API request as REDCap API request 
    # without REDCap API token (false) since we're using our own token
    public function processSurveyFlowRequest() { 

        $this->prepareRequest();
        $this->prepareModuleConfig();        
        $this->handleEndpoint();

    }

    private function prepareRequest() {

        global $returnFormat; // Need to set this as global for RestUtility
        $returnFormat = "json";

        $this->request = RestUtility::processRequest(false);
        $this->post = $this->request->getRequestVars();

        if( !$this->isValid($this->post['pid']) ) {
            RestUtility::sendResponse(400, "Bad Request - pid is required");
        }

        //  IMPORTANT: Set project id necessary within REDCap Classes (e.g. Survey)
        $this->project_id = htmlspecialchars($this->post['pid']);
        define("PROJECT_ID", $this->project_id);
        //$GLOBALS["Proj"]  = new Project($this->project_id);

        if( !$this->isValid($this->post['node']) ) {
            RestUtility::sendResponse(400, "Bad Request - node is required");
        }

        if( !$this->isValid($this->post['action']) ) {
            RestUtility::sendResponse(400, "Bad Request - action is required");
        }

    }

    private function prepareModuleConfig() {
        //  Check if module is configured
        if( !$this->isValid( $this->getProjectSetting("id-field") )) {
            RestUtility::sendResponse(500, "Invalid module configuration - ID field is required.");
        }

        if( !$this->isValid( $this->getProjectSetting("pass-field") )) {
            RestUtility::sendResponse(500, "Invalid module configuration - pass field is required.");
        }

        if( !$this->isValid( $this->getProjectSetting("firstname-field") )) {
            RestUtility::sendResponse(500, "Invalid module configuration - firstname field is required.");
        }

        if( !$this->isValid( $this->getProjectSetting("lastname-field") )) {
            RestUtility::sendResponse(500, "Invalid module configuration - lastname field is required.");
        }

        $this->config['id'] = $this->getProjectSetting("id-field");
        $this->config['pass'] = $this->getProjectSetting("pass-field");
        $this->config['firstname'] = $this->getProjectSetting("firstname-field");
        $this->config['lastname'] = $this->getProjectSetting("lastname-field");
    }

    private function isValid($var) {
        if( isset($var) && !empty($var)) {
            return true;
        }
        return false;
    }

    private function generateToken(){

        $secret = "EXAMPLE_KEY";

        $payload = array(
            "record_id" => $this->record[ "record_id" ],
            "user_id" => $this->record[ $this->config['id'] ],
            "firstname" => $this->record[ $this->config['firstname'] ],
            "lastname" => $this->record[ $this->config['lastname'] ],
            "exp" => time() + (60 * 60 * 24), //Expire the JWT after 24 hour from now

        );
    
        $this->jwt = JWT::encode( $payload, $secret, 'HS256' );
    }

    private function checkAuth($id_value, $pass_value) {

        //  Fetch record from database
        $sql = 'SELECT project_id, record, field_name, value FROM redcap_data WHERE project_id = ? 
                AND record = (SELECT DISTINCT d.record FROM redcap_data d WHERE d.field_name  = ? AND d.VALUE = ?)
                AND field_name IN ("record_id", ?, ?, ?, ?)';

        $result = $this->query($sql, 
            [
                $this->project_id,
                $this->config['id'],
                $id_value,
                $this->config['pass'],
                $this->config['firstname'],
                $this->config['lastname'],
                $this->config['id']
            ]
        );

        while( $row = $result->fetch_assoc()){
            $this->record[$row['field_name']] = $row["value"];
        }

        //  Check if ID exists or if password is incorrect
        if( $result->num_rows <= 0 || $this->record[ $this->config['pass'] ] !== $pass_value) {
            RestUtility::sendResponse(403, "Forbidden - Invalid credentials for authentication.");
        }

    }

    protected function handleEndpoint() {
        
        //  For Development on Localhost 
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Content-Type");

        //$test = Survey::displaySurveyQueueForRecord(1);

        $endpoint_url = "endpoints/" . htmlspecialchars($this->post['node']) . "." . htmlspecialchars($this->post['action']). ".php";

        //  Check if Endpoint is valid
        if(file_exists(__DIR__ . "/" . $endpoint_url)) {
            # Include endpoint to generate response
            require ($endpoint_url);
        } else {
            RestUtility::sendResponse(400, "Bad Request - Invalid Endpoint.");
        }

        # Return response
        RestUtility::sendResponse(200, json_encode($this->response), 'json');

    }    
    
}