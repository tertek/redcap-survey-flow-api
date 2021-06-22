<?php

    //  Check if request params are valid
    if( !$this->isValid($this->post['id']) ) {
        RestUtility::sendResponse(400, "Bad Request - id is required");
    }

    if( !$this->isValid($this->post['pass']) ) {
        RestUtility::sendResponse(400, "Bad Request - pass is required");
    }

    //  Check if credentials match
    $this->checkAuth(
        $this->post['id'], 
        $this->post['pass']
    );

   /**  PHP package for JWT
    *   https://github.com/firebase/php-jwt
    *   
    */
    $this->generateToken();

    $this->response = array(
        "token" => $this->jwt
    );

    //$decoded = JWT::decode($jwt, $key, array('HS256'));
