<?php

// Set the namespace defined in your config file
namespace STPH\surveyQueueApi;

require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

// Declare your module class, which must extend AbstractExternalModule 
class surveyQueueApi extends \ExternalModules\AbstractExternalModule {

    private $moduleName = "Survey Queue API";  
    private $JWTtoken = "";

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
    * Hooks Survey Queue API module to redcap_every_page_top
    *
    */
    public function redcap_every_page_top($project_id = null) {
        $this->renderModule();
    }

   /**
    * Renders the module
    *
    */
    private function renderModule() {
        
        print '<p class="survey-queue-api">'.$this->helloFrom_surveyQueueApi().'<p>';

    }

    public function helloFrom_surveyQueueApi() {

        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

        
        $jwt = JWT::encode($payload, $key);
        $decoded = JWT::decode($jwt, $key, array('HS256'));

                
        return 'Token: '.$jwt;
        

    }

    
}