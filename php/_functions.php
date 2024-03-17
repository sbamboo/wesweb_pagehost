<?php

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
}

function getClientNameFromID() {}

function getPosts() {}

function validateClientCredentials() {}

function getClientData() {}

function setClientData() {}

function getPostsByClient() {}

function getPostsWhereClientIsAccessee() {}

function getPostData() {}

function setPostData() {}

function getPostAccessees() {}

function addPostAccessee() {}

function remPostAccessee() {}

function getCommentsForPost() {}

function addComment() {}

function remComment() {}

?>