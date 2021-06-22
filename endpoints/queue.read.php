<?php

    //  Check if request is valid
    if( !$this->isValid($this->post['project_id']) ){
        RestUtility::sendResponse(400, "Bad Request - project_id is required.");
    }


    $this->response["queue"] = Survey::getSurveyQueueForRecord(1);

