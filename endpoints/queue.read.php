<?php

    //  Check if request is valid
    if( !$this->isValid($this->post['project_id']) ){
        RestUtility::sendResponse(400, "Bad Request - project_id is required.");
    }

    //  IMPORTANT: Set project id, constants and globals that are necessary within REDCap Classes (e.g. Survey)
    $this->project_id = htmlspecialchars($this->post['project_id']);
    $GLOBALS["Proj"]  = new Project($this->project_id);
    define("PROJECT_ID", $this->project_id);

    //  Get record

    $res = Survey::getSurveyQueueForRecord(1);