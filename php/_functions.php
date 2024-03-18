<?php

function intBoolToString($intBool,$capitalize=true) {
    if ($intBool == 1 || $intBool == true) {
        if ($capitalize == true) {
            return "True";
        }
        return "true";
    }
    if ($capitalize == true) {
        return "False";
    }
    return "false";
}

function connectDB(array $sqlargs) {
    // Extracting values from the arg array
    list($sql_host, $sql_uname, $sql_password, $sql_database) = $sqlargs;

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
    }
    return array(True,"Successfully connected to database",$mysqli);
} # returning array(bool $success, string $msg, $sqlConnectionInstance)

function validateDBConnection(array $connectionReturn) {
    list($success,$msg,$instance) = $connectionReturn;
    if ($success == 1 || $success == true) {
        return $instance;
    }
    return $msg;
}

function getClientNameFromID($dbInstance,$dbTable, int $clientID) {
    $sqlcmd = "SELECT Username,DispName FROM " . $dbTable . " WHERE ID=?";
    // We also create a stmt (statement) and use the mysqli.prepare method on it to load it as a prepared-statement
    $prepped_statement = $dbInstance->prepare($sqlcmd);

    // We use bind_param to bind our placeholders with their wanted values making sure to use the correct data type:
    // s: String
    // This effectively says to php/sql that the content must be string, making injection less likely.
    $prepped_statement->bind_param("i", $clientID);

    // Execute the prepared statement (Same as our query)
    $prepped_statement->execute();

    // Get the result from the query.
    $result = $prepped_statement->get_result(); // Get Result
    $result = $result->fetch_assoc(); // Fetch from the result (I use same var and overwrites only to compat with my earlier method)

    // Close the statement connection since we don't need this connection to our SQL database.
    $prepped_statement->close();

    // Retrive result
    if (in_array("DispName",array_keys($result)) && $result["DispName"] != "" && $result["DispName"] != null) {
        return $result["DispName"];
    }
    return $result["Username"];
} # returns dispname if set otherwise username

function doesUsernameExist($dbInstance,$dbTable,string $username) {
    $sqlcmd = "SELECT Username FROM " . $dbTable . " WHERE Username=?";
    // We also create a stmt (statement) and use the mysqli.prepare method on it to load it as a prepared-statement
    $prepped_statement = $dbInstance->prepare($sqlcmd);

    // We use bind_param to bind our placeholders with their wanted values making sure to use the correct data type:
    // s: String
    // This effectively says to php/sql that the content must be string, making injection less likely.
    $prepped_statement->bind_param("s", $username);

    // Execute the prepared statement (Same as our query)
    $prepped_statement->execute();

    // Get the result from the query.
    $result = $prepped_statement->get_result(); // Get Result
    $result = $result->fetch_assoc(); // Fetch from the result (I use same var and overwrites only to compat with my earlier method)

    // Close the statement connection since we don't need this connection to our SQL database.
    $prepped_statement->close();

    // Retrive result
    if (empty($result)) {
        return false;
    }
    return true;
}

function doesClientNameExist($dbInstance,$dbTable,string $dispname) {
    $sqlcmd = "SELECT DispName FROM " . $dbTable . " WHERE DispName=?";
    // We also create a stmt (statement) and use the mysqli.prepare method on it to load it as a prepared-statement
    $prepped_statement = $dbInstance->prepare($sqlcmd);

    // We use bind_param to bind our placeholders with their wanted values making sure to use the correct data type:
    // s: String
    // This effectively says to php/sql that the content must be string, making injection less likely.
    $prepped_statement->bind_param("s", $dispname);

    // Execute the prepared statement (Same as our query)
    $prepped_statement->execute();

    // Get the result from the query.
    $result = $prepped_statement->get_result(); // Get Result
    $result = $result->fetch_assoc(); // Fetch from the result (I use same var and overwrites only to compat with my earlier method)

    // Close the statement connection since we don't need this connection to our SQL database.
    $prepped_statement->close();

    // Retrive result
    if (empty($result)) {
        return false;
    }
    return true;
}

function getPosts($dbInstance,$dbTable) {
    $sqlcmd = "SELECT * FROM " . $dbTable;

    // Get the result from the query.
    $result = $dbInstance->query($sqlcmd); // Get Result
    $result = $result->fetch_assoc();

    return $result;
}

function validateClientCredentials($dbInstance,$dbTable,string $username,string $password) {
    $sqlcmd = "SELECT Username,Password FROM " . $dbTable . " WHERE Username=? AND Password=?";
    // We also create a stmt (statement) and use the mysqli.prepare method on it to load it as a prepared-statement
    $prepped_statement = $dbInstance->prepare($sqlcmd);

    // We use bind_param to bind our placeholders with their wanted values making sure to use the correct data type:
    // s: String
    // This effectively says to php/sql that the content must be string, making injection less likely.
    $prepped_statement->bind_param("ss", $username,$password);

    // Execute the prepared statement (Same as our query)
    $prepped_statement->execute();

    // Get the result from the query.
    $result = $prepped_statement->get_result(); // Get Result
    $result = $result->fetch_assoc(); // Fetch from the result (I use same var and overwrites only to compat with my earlier method)

    // Close the statement connection since we don't need this connection to our SQL database.
    $prepped_statement->close();

    // Retrive result
    if (empty($result)) {
        return false;
    }
    return true;
}

function getClientData($dbInstance,$dbTable,int $clientID) {
    $sqlcmd = "SELECT * FROM " . $dbTable . " WHERE ID=?";
    // We also create a stmt (statement) and use the mysqli.prepare method on it to load it as a prepared-statement
    $prepped_statement = $dbInstance->prepare($sqlcmd);

    // We use bind_param to bind our placeholders with their wanted values making sure to use the correct data type:
    // s: String
    // This effectively says to php/sql that the content must be string, making injection less likely.
    $prepped_statement->bind_param("i", $clientID);

    // Execute the prepared statement (Same as our query)
    $prepped_statement->execute();

    // Get the result from the query.
    $result = $prepped_statement->get_result(); // Get Result
    $result = $result->fetch_assoc(); // Fetch from the result (I use same var and overwrites only to compat with my earlier method)
    
    // Close the statement connection since we don't need this connection to our SQL database.
    $prepped_statement->close();

    // Retrive result
    return $result;
}

function setClientData($dbInstance,$dbTable,int $clientID,string $username=null, string $password=null, $dispname=null, $profpic=null) {
    // Update query with prepared statement
    $sql = "UPDATE " . $dbTable . " SET ";
    $params = array();
    if ($username !== null) {
        $sql .= "username=?, ";
        $params[] = $username;
    }
    if ($password !== null) {
        $sql .= "password=?, ";
        $params[] = $password;
    }
    if ($dispname !== null) {
        $sql .= "dispname=?, ";
        $params[] = $dispname;
    }
    if ($profpic !== null) {
        $sql .= "profpic=?, ";
        $params[] = $profpic;
    }
    $sql = rtrim($sql, ", ") . " WHERE id=?";
    $stmt = $dbInstance->prepare($sql);
    
    // Dynamically bind parameters based on the provided values
    $bind_types = str_repeat("s", count($params)) . "i";
    $bind_params = array_merge(array($bind_types), $params, array($clientID));
    print_r($sql);
    $stmt->bind_param(...$bind_params);

    $toRet = false;
    if ($stmt->execute() === TRUE) {
        $toRet = true;
    }

    // Close statement and return
    $stmt->close();
    return $toRet;
}

function getPostsByClient($dbInstance,$userTable,$postTable,int $clientID) {
    $sqlcmd = "SELECT posts.ID AS ID,posts.Header AS Header,posts.Content AS Content,posts.AuthorID AS AuthorID,posts.ContentType as ContentType FROM " . $postTable . " JOIN " . $userTable . " WHERE " . $postTable . ".AuthorID = " . $userTable . ".ID";
    
    // Get the result from the query.
    $result = $dbInstance->query($sqlcmd); // Get Result
    $result = $result->fetch_assoc();

    return $result;
} # returning array of postids

function getPostsWhereClientIsAccessee($dbInstance,$postTable,$accesseesTable,int $clientID) {
    $sqlcmd = "SELECT posts.ID AS ID,posts.Header AS Header,posts.Content AS Content,posts.AuthorID AS AuthorID,posts.ContentType as ContentType FROM " . $accesseesTable . " JOIN " . $postTable . " WHERE " . $postTable . ".ID = " . $accesseesTable . ".PostID AND " . $accesseesTable . ".UserID = ?;";
    
    // We also create a stmt (statement) and use the mysqli.prepare method on it to load it as a prepared-statement
    $prepped_statement = $dbInstance->prepare($sqlcmd);

    // We use bind_param to bind our placeholders with their wanted values making sure to use the correct data type:
    // s: String
    // This effectively says to php/sql that the content must be string, making injection less likely.
    $prepped_statement->bind_param("i", $clientID);

    // Execute the prepared statement (Same as our query)
    $prepped_statement->execute();

    // Get the result from the query.
    $result = $prepped_statement->get_result(); // Get Result
    $result = $result->fetch_assoc(); // Fetch from the result (I use same var and overwrites only to compat with my earlier method)
    
    // Close the statement connection since we don't need this connection to our SQL database.
    $prepped_statement->close();

    // Retrive result
    return $result;
} # returning array of postids

function getPostData($dbInstance,$dbTable,int $postID) {
    $sqlcmd = "SELECT * FROM " . $dbTable . " WHERE ID=?";

    // We also create a stmt (statement) and use the mysqli.prepare method on it to load it as a prepared-statement
    $prepped_statement = $dbInstance->prepare($sqlcmd);

    // We use bind_param to bind our placeholders with their wanted values making sure to use the correct data type:
    // s: String
    // This effectively says to php/sql that the content must be string, making injection less likely.
    $prepped_statement->bind_param("i", $postID);

    // Execute the prepared statement (Same as our query)
    $prepped_statement->execute();

    // Get the result from the query.
    $result = $prepped_statement->get_result(); // Get Result
    $result = $result->fetch_assoc(); // Fetch from the result (I use same var and overwrites only to compat with my earlier method)
    
    // Close the statement connection since we don't need this connection to our SQL database.
    $prepped_statement->close();

    // Return
    return $result;
}

function setPostData($dbInstance,int $postID,string $header=null,string $content=null,int $AuthorID=null,int $contentType=null) {}

function getPostAccessees($dbInstance,int $postID) {}

function addPostAccessee($dbInstance,int $postID,int $clientID) {}

function remPostAccessee($dbInstance,int $postID,int $clientID) {}

function getCommentsForPost($dbInstance,int $postID) {}

function addComment($dbInstance,int $parentID, int $authorID,string $content,bool $isForPost) {}

function remComment($dbInstance,int $commentID) {}

?>