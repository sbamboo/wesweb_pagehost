<?php

function intBoolToString($intBool,$capitalize=true) {
    if ($intBool == 1 || $intBool == true) {
        if ($capitalize == true) {
            return "True";
        } else {
            return "true";
        }
    } else {
        if ($capitalize == true) {
            return "False";
        } else {
            return "false";
        }
    }
}

function connectDB(array $sqlargs) {
    // Extracting values from the arg array
    list($sql_host, $sql_uname, $sql_password, $sql_database, $sql_table) = $sqlargs;

    // Connect to SQL server UTF8
    try {
        $mysqli = new mysqli($sql_host, $sql_uname, $sql_password, $sql_database);
        $mysqli->set_charset("utf8");
    } catch (Exception $e) {
        // Handle exceptions and return message
        return array(False,"Failed to connect to SQL database (" . $e->getMessage() . ")",NULL);
    }

    // Verify connection to database
    if ($mysqli->connect_error) {
        return array(False,"Failed to connect to SQL database (" . $mysqli->connect_error . ")",NULL);
    } else {
        return array(True,"Successfully connected to database",$mysqli);
    }
} # returning array(bool $success, string $msg, $sqlConnectionInstance)

function validateDBConnection(array $connectionReturn) {
    list($success,$msg,$instance) = $connectionReturn;
    if ($success == 1 || $success == true) {
        return $instance;
    } else {
        return $msg;
    }
}

function getClientNameFromID($dbInstance,int $clientID) {} # returns dispname if set otherwise username

function doesUsernameExist($dbInstance,string $username) {}

function doesClientNameExist($dbInstance,string $dispname) {}

function getPosts($dbInstance) {}

function validateClientCredentials($dbInstance,string $username,string $password) {}

function getClientData($dbInstance,int $clientID) {}

function setClientData($dbInstance,int $clientID,string $username=null, string $password=null, $dispname=null, $profpic=null) {}

function getPostsByClient($dbInstance,int $clientID) {} # returning array of postids

function getPostsWhereClientIsAccessee($dbInstance,int $clientID) {} # returning array of postids

function getPostData($dbInstance,int $postID) {}

function setPostData($dbInstance,int $postID,string $header=null,string $content=null,int $AuthorID=null,int $contentType=null) {}

function getPostAccessees($dbInstance,int $postID) {}

function addPostAccessee($dbInstance,int $postID,int $clientID) {}

function remPostAccessee($dbInstance,int $postID,int $clientID) {}

function getCommentsForPost($dbInstance,int $postID) {}

function addComment($dbInstance,int $parentID, int $authorID,string $content,bool $isForPost) {}

function remComment($dbInstance,int $commentID) {}

?>