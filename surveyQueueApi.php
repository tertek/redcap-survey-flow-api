<?php

// Set the namespace defined in your config file
namespace STPH\surveyQueueApi;



// Declare your module class, which must extend AbstractExternalModule 
class surveyQueueApi extends \ExternalModules\AbstractExternalModule {

    private $moduleName = "Survey Queue API";  

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
        
        $this->includeJavascript();
        
        
        $this->includeCSS();
        

        print '<p class="survey-queue-api">'.$this->helloFrom_surveyQueueApi().'<p>';

    }

    public function helloFrom_surveyQueueApi() {

                
        return 'Hello from '.$this->moduleName;
        

    }

    
   /**
    * Include JavaScript files
    *
    */
    private function includeJavascript() {
        ?>
        <script src="<?php print $this->getUrl('js/main.js'); ?>"></script>
        <script> 
            $(function() {
                $(document).ready(function(){
                    STPH_surveyQueueApi.init();
                })
            });
        </script>
        <?php
    }
    

    
   /**
    * Include Style files
    *
    */
    private function includeCSS() {
        ?>
        <link rel="stylesheet" href="<?= $this->getUrl('style.css')?>">
        <?php
    }
    
}