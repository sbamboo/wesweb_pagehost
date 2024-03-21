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

function functionExSQL($dbInstance,string $sqlcmd,bool $hasMultipleReturn=false,bool $prepare=false,string $bindTypesString="",array $prepareStatements=array(),bool $retExecuteRet=false,bool $yieldRetID=false) {
    if ($prepare == true) {
        // We Create a stmt (statement) and use the mysqli.prepare method on it to load it as a prepared-statement
        $prepped_statement = $dbInstance->prepare($sqlcmd);
        // Make bindParams array
        $bindParams = array_merge(array($bindTypesString),$prepareStatements);
        // We use bind_param to bind our placeholders with their wanted values making sure to use the correct data type.
        // This effectively says to php/sql that the content must be string, making injection less likely.
        $prepped_statement->bind_param(...$bindParams);
        // Execute the prepared statement (Same as query)
        $executeReturn = $prepped_statement->execute();
        $insertedId = $prepped_statement->insert_id;
        // If enabled return the value now as boolean
        if ($retExecuteRet == true) {
            $toRet = false;
            if ($executeReturn === TRUE) {
                if ($yieldRetID == true) {
                    $toRet = $insertedId;
                } else {
                    $toRet = true;
                }
            }
            // Close the statement connection since we don't need this connection to our SQL database.
            $prepped_statement->close();
            return $toRet;
        }
        // Otherwise get the result
        $resultF = $prepped_statement->get_result();
        // Close the statement connection since we don't need this connection to our SQL database.
        $prepped_statement->close();
    } else {
        $resultF = $dbInstance->query($sqlcmd);
    }
    // Grab multiple results if asked
    if ($hasMultipleReturn == true) {
        $results = array();
        while ($res = $resultF->fetch_assoc()) {
            $results[] = $res;
        }
        return $results;
    }
    // Otherwise return the first one
    return $resultF->fetch_assoc();
}

function getClientNameFromID($dbInstance,$userTable, int $clientID) {
    $sqlcmd = "SELECT Username,DispName FROM " . $userTable . " WHERE ID=?";
    
    $result = functionExSQL($dbInstance,$sqlcmd,False,True,"i",array($clientID));

    // Retrive result
    if (in_array("DispName",array_keys($result)) && $result["DispName"] != "" && $result["DispName"] != null) {
        return $result["DispName"];
    }
    return $result["Username"];
} # returns dispname if set otherwise username

function getClientIDFromName($dbInstance,$userTable, string $username) {
    $sqlcmd = "SELECT ID FROM " . $userTable . " WHERE Username=?";
    
    $result = functionExSQL($dbInstance,$sqlcmd,False,True,"s",array($username));

    // Retrive result
    return $result["ID"];
} # returns dispname if set otherwise username


function doesUsernameExist($dbInstance,$userTable,string $username) {
    $sqlcmd = "SELECT Username FROM " . $userTable . " WHERE Username=?";
    
    $result = functionExSQL($dbInstance,$sqlcmd,False,True,"s",array($username));

    // Retrive result
    if (empty($result)) {
        return false;
    }
    return true;
}

function doesClientNameExist($dbInstance,$userTable,string $dispname) {
    $sqlcmd = "SELECT DispName FROM " . $userTable . " WHERE DispName=?";
    
    $result = functionExSQL($dbInstance,$sqlcmd,False,True,"s",array($dispname));

    // Retrive result
    if (empty($result)) {
        return false;
    }
    return true;
}

function getPosts($dbInstance,$postTable) {
    $sqlcmd = "SELECT * FROM " . $postTable;

    return functionExSQL($dbInstance,$sqlcmd,True);
}

function getPostSelData($dbInstance,$postTable) {
    $sqlcmd = "SELECT ID,Header,AuthorID FROM " . $postTable;

    return functionExSQL($dbInstance,$sqlcmd,True);
}

function validateClientCredentials($dbInstance,$userTable,string $username,string $password) {
    $sqlcmd = "SELECT Username,Password FROM " . $userTable . " WHERE Username=? AND Password=?";
    
    $result = functionExSQL($dbInstance,$sqlcmd,False,True,"ss",array($username,$password));

    // Retrive result
    if (empty($result)) {
        return false;
    }
    return true;
}

function getClientData($dbInstance,$userTable,int $clientID) {
    $sqlcmd = "SELECT * FROM " . $userTable . " WHERE ID=?";

    return functionExSQL($dbInstance,$sqlcmd,False,True,"i",array($clientID));
}

function setClientData($dbInstance,$userTable,int $clientID,string $username=null, string $password=null, $dispname=null, $profpic=null) {
    // Update query with prepared statement
    $sql = "UPDATE " . $userTable . " SET ";
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
    
    // Dynamically bind parameters based on the provided values
    $bind_types = str_repeat("s", count($params)) . "i";
    $bind_params = array_merge($params, array($clientID));

    $result = functionExSQL($dbInstance,$sql,False,True,$bind_types,$bind_params,True);

    return $result;
}

function checkIfUsernameExists($dbInstance,$userTable,$username) {
    $sqlcmd = "SELECT * FROM " . $userTable . " WHERE Username=?";
    $result = functionExSQL($dbInstance,$sqlcmd,False,True,"s",array($username));
    if ($result && $result != "" && $result != null) {
        return true;
    } else {
        return false;
    }
}

function addClient($dbInstance,$userTable,string $username,string $password,string $dispname=null,$profpic=null) {
    $exi = checkIfUsernameExists($dbInstance,$userTable,$username);
    if ($exi) {
        return false;
    } else {
        // Update query with prepared statement
        $sql = "INSERT INTO " . $userTable . " (Username,Password";
        $esql = ") VALUES (?,?";
        $bindTypes = "ss";
        $params = array($username,$password);
        if ($dispname != null) {
            $sql .= ",DispName";
            $esql .= ",?";
            $bindTypes .= "s";
            $params[] = $dispname;
        }
        if ($profpic != null) {
            $sql .= ",ProfPic";
            $bindTypes .= "b";
            $esql .= ",?";
            $params[] = $profpic;
        }

        $sqlcmd = $sql . $esql . ")";

        $result = functionExSQL($dbInstance,$sqlcmd,False,True,$bindTypes,$params,True);

        return $result;
    }
}

function remClient($dbInstance,$userTable,int $clientID) {
    $sqlcmd = "DELETE FROM " . $userTable . " WHERE ID=?";
    return functionExSQL($dbInstance,$sqlcmd,False,True,"i",array($clientID),True);
}

function getPostsByClient($dbInstance,$postTable,int $clientID) {
    $sqlcmd = "SELECT * FROM " . $postTable . " WHERE " . $postTable . ".AuthorID=?";
    
    return functionExSQL($dbInstance,$sqlcmd,True,True,"i",array($clientID));
} # returning array of postids

function getPostsWhereClientIsAccessee($dbInstance,$postTable,$accesseeTable,int $clientID) {
    $sqlcmd = "SELECT " . $postTable . ".ID AS ID," . $postTable . ".Header AS Header," . $postTable . ".Content AS Content," . $postTable . ".AuthorID AS AuthorID," . $postTable . ".ContentType as ContentType FROM " . $accesseeTable . " JOIN " . $postTable . " WHERE " . $postTable . ".ID = " . $accesseeTable . ".PostID AND " . $accesseeTable . ".UserID = ?;";
    
    return functionExSQL($dbInstance,$sqlcmd,True,True,"i",array($clientID));
} # returning array of postids

function getPostData($dbInstance,$postTable,int $postID) {
    $sqlcmd = "SELECT * FROM " . $postTable . " WHERE ID=?";

    return functionExSQL($dbInstance,$sqlcmd,False,True,"i",array($postID));
}

function setPostData($dbInstance,$postTable,int $postID,string $header=null,string $content=null,int $AuthorID=null,int $contentType=null) {
    // Update query with prepared statement
    $sql = "UPDATE " . $postTable . " SET ";
    $paramsS = array();
    $paramsI = array();
    if ($header !== null) {
        $sql .= "Header=?, ";
        $paramsS[] = $header;
    }
    if ($content !== null) {
        $sql .= "Content=?, ";
        $paramsS[] = $content;
    }
    if ($AuthorID !== null) {
        $sql .= "AuthorID=?, ";
        $paramsI[] = $AuthorID;
    }
    if ($contentType !== null) {
        $sql .= "contentType=?, ";
        $paramsI[] = $contentType;
    }
    $sql = rtrim($sql, ", ") . " WHERE id=?";
    
    // Dynamically bind parameters based on the provided values
    $bindTypes = str_repeat("s", count($paramsS)) . str_repeat("i", count($paramsI)) . "i";
    $bindParams = array_merge($paramsS, $paramsI, array($postID));

    $result = functionExSQL($dbInstance,$sql,False,True,$bindTypes,$bindParams,True);

    return $result;
}

function addPost($dbInstance,$postTable,string $header,string $content,int $authorID,int $contentType) {
    $sqlcmd = "INSERT INTO " . $postTable . " (Header,Content,AuthorID,ContentType) VALUES (?,?,?,?)";
    return functionExSQL($dbInstance,$sqlcmd,False,True,"ssii",array($header,$content,$authorID,$contentType),True,True);
}

function remPost($dbInstance,$postTable,int $postID) {
    $sqlcmd = "DELETE FROM " . $postTable . " WHERE ID=?";
    return functionExSQL($dbInstance,$sqlcmd,False,True,"i",array($postID),True);
}

function getPostAccessees($dbInstance,$postTable,$accesseeTable,int $postID) {
    $sqlcmd = "SELECT " . $accesseeTable . ".UserID AS ID FROM " . $accesseeTable . " JOIN " . $postTable . " WHERE " . $postTable . ".ID=?";
    
    // Get the result from the query.
    $results = functionExSQL($dbInstance,$sqlcmd,True,True,"i",array($postID));

    $idlist = array();
    foreach ($results as $result) {
        $idlist[] = $result["ID"];
    }

    return $idlist;
}

function checkIfClientIsAccesseeByID($dbInstance,$accesseeTable,int $postID, int $clientID) {
    $sqlcmd = "SELECT * FROM " . $accesseeTable . " WHERE PostID=? AND UserID=?";
    
    // Get the result from the query.
    $results = functionExSQL($dbInstance,$sqlcmd,True,True,"ii",array($postID,$clientID));

    if (count($results) >= 1) {
        return true;
    }
    return false;
}

function addPostAccessee($dbInstance,$accesseeTable,int $postID,int $clientID) {
    // Only add if not existing so check first
    if (checkIfClientIsAccesseeByID($dbInstance,$accesseeTable,$postID,$clientID) == true) {
        return false;
    } else {
        $sqlcmd = "INSERT INTO " . $accesseeTable . " (PostID,UserID) VALUES (?,?)";
        return functionExSQL($dbInstance,$sqlcmd,False,True,"ii",array($postID,$clientID),True);
    }
}

function remPostAccessee($dbInstance,$accesseeTable,int $postID,int $clientID) {
    // Only add if not existing so check first
    if (checkIfClientIsAccesseeByID($dbInstance,$accesseeTable,$postID,$clientID) == true) {
        $sqlcmd = "DELETE FROM " . $accesseeTable . " WHERE PostID=? AND UserID=?";
        return functionExSQL($dbInstance,$sqlcmd,False,True,"ii",array($postID,$clientID),True);
    } else {
        return false;
    }
}

function checkIfCommentIsForPost($dbInstance,$commentTable,int $commentID) {
    $sqlcmd = "SELECT IsForPost FROM " . $commentTable . " WHERE ID=?";
    $result = functionExSQL($dbInstance,$sqlcmd,False,True,"i",array($commentID));
    if ($result == 1) {
        return true;
    }
    return false;
}

function getCommentsForPost($dbInstance,$commentTable,int $postID) {
    $sqlcmd = "SELECT * FROM " . $commentTable . " WHERE ParentID=? AND IsForPost=1";
    return functionExSQL($dbInstance,$sqlcmd,True,True,"i",array($postID));
}

function getCommentsForComment($dbInstance,$commentTable,int $commentID) {
    $sqlcmd = "SELECT * FROM " . $commentTable . " WHERE ParentID=? AND IsForPost=0";
    return functionExSQL($dbInstance,$sqlcmd,True,True,"i",array($commentID));
}

function addComment($dbInstance,$commentTable,int $parentID, int $authorID,string $content,bool $isForPost) {
    $sqlcmd = "INSERT INTO " . $commentTable . " (Content,AuthorID,ParentID,IsForPost) VALUES (????)";
    // Make isforPost into tinyint
    $isForPostINT = 1;
    if ($isForPost == false) {
        $isForPostINT = 0;
    }
    return functionExSQL($dbInstance,$sqlcmd,False,True,"siii",array($content,$authorID,$parentID,$isForPostINT),True);
}

function setComment($dbInstance,$commentTable,int $commentID,string $content=null,int $authorID=null,int $parentID=null,bool $isForPost=null) {
    // Make isForPost int
    $isForPostINT = null;
    if ($isForPost == false) {
        $isForPostINT = 0;
    } elseif ($isForPost == true) {
        $isForPostINT = 1;
    }
    // Update query with prepared statement
    $sql = "UPDATE " . $commentTable . " SET ";
    $paramsS = array();
    $paramsI = array();
    if ($content !== null) {
        $sql .= "Content=?, ";
        $paramsS[] = $content;
    }
    if ($authorID !== null) {
        $sql .= "AuthorID=?, ";
        $paramsI[] = $authorID;
    }
    if ($parentID !== null) {
        $sql .= "ParentID=?, ";
        $paramsI[] = $parentID;
    }
    if ($isForPostINT !== null) {
        $sql .= "IsForPost=?, ";
        $paramsI[] = $isForPostINT;
    }
    $sql = rtrim($sql, ", ") . " WHERE id=?";
    
    // Dynamically bind parameters based on the provided values
    $bindTypes = str_repeat("s", count($paramsS)) . str_repeat("i", count($paramsI)) . "i";
    $bindParams = array_merge($paramsS, $paramsI, array($commentID));

    $result = functionExSQL($dbInstance,$sql,False,True,$bindTypes,$bindParams,True);

    return $result;
}

function remComment($dbInstance,$commentTable,int $commentID) {
    $sqlcmd = "DELETE FROM " . $commentTable . " WHERE ID=?";
    return functionExSQL($dbInstance,$sqlcmd,False,True,"i",array($commentID),True);
}

function recursivelyGetComments($dbInstance,$commentTable,array $rootComment) {
    $tree = array();
    $subcomments = getCommentsForComment($dbInstance,$commentTable,$rootComment["ID"]);
    foreach($subcomments as $subcomment) {
        $tree[] = recursivelyGetComments($dbInstance,$commentTable,$subcomment);
    }
    $rootComment["subComments"] = $tree;
    return $rootComment;
}

function getCommentTree($dbInstance,$commentTable,int $originalParentPostID) {
    $tree = array();
    $comments = getCommentsForPost($dbInstance,$commentTable,$originalParentPostID);
    foreach ($comments as $comment) {
        $tree[] = recursivelyGetComments($dbInstance,$commentTable,$comment);
    }
    return $tree;
}