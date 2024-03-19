<?php
# Link requirements
require("php/_functions.php");
# Define sql-arguments
$sqlargs = array("localhost","root","","pagehost");
# Create connectionInstances
$conRet = validateDBConnection( connectDB($sqlargs) );
if (is_string($conRet)) { echo $conRet;$dbInstance=null; } else { $dbInstance = $conRet; }

session_start();
$clientID = $_SESSION["clientID"];

$postID = null;
if (isset($_POST["postsel_id"]) && !empty($_POST["postsel_id"])) {
    $postID = $_POST["postsel_id"];
}

if ($postID != null) {
    $postData = getPostData($dbInstance,"posts", $postID);
}

# Check for internal-call meaning we have actions to do.
if (isset($_POST["internal-call"]) && $_POST["internal-call"] == "true") {
    # Handle sending-mode login
    if (isset($_POST["sendingmode"]) && $_POST["sendingmode"] == "save" && isset($_POST["header"]) && isset($_POST["content"])) {
        $header = $_POST["header"];
        $content = $_POST["content"];
        setPostData($dbInstance,"posts",$postID,$header,$content);
        $postData["Content"] = $content;
        $postData["Header"] = $header;
    }
    # Handle sending-mode create-new
    if (isset($_POST["sendingmode"]) && $_POST["sendingmode"] == "create-new" && isset($_POST["submit-create-new"])) {
        $postID = addPost($dbInstance,"posts","New post","Write content here...",$clientID,0);
        $postData = getPostData($dbInstance,"posts",$postID);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PageHost</title>
    <link href="./images/favicon.png" type="image/png">
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/ui.css">
    <link rel="stylesheet" href="./css/postview_and_edit.css">
</head>
<body>
    <header>
        <div id="nav-bar" class="flex-horiz flex-maxwidth">
            <div id="nav-titling" class="flex-horiz">
                <div id="nav-logo-wrapper">
                    <img src="./images/favicon.png" alt="Logo">
                </div>
                <div id="nav-pagename">
                    <h1>PageHost</h1>
                </div>
            </div>
            <div id="button-wrapper">
                <div id="nav-home">
                    <div class="ui-button ui-size-button-big">
                        <a href="./index.php">Home</a>
                    </div>
                </div>
                <div id="nav-login">
                    <div class="ui-button ui-size-button-big">
                        <a href="./customerpage.php">Your page</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="flex-horiz">
        <div id="content-wrapper">
            <div id="editor-content" class="page-content">
                <?php
                if ($postID != null) {
                    # Text-ContentType
                    if ($postData["ContentType"] == 0) {
                        # getdata
                        $postAuthorData = getClientData($dbInstance,"users",$postData["AuthorID"]);
                        $choosenName = getClientNameFromID($dbInstance,"users",$postData["AuthorID"]);
                        # main content
                        echo '<div id="content-main">';
                        echo '<form method="POST" action="editor.php">';
                        echo '<div id="content-main-innerwrapper">';
                        echo '<input type="hidden" name="internal-call" value="true">';
                        echo '<input type="hidden" name="sendingmode" value="save">';
                        echo '<input type="hidden" name="postsel_id" value="'.$postID.'">';
                        echo '<textarea class="editor-edit-header" name="header" rows=1>'.$postData["Header"].'</textarea><br>';
                        echo '<textarea class="editor-edit-content" name="content">'.$postData["Content"].'</textarea><br>';
                        echo '</div>';
                        echo '<input type="submit" name="editor-submit-save" value="Save">';
                        echo '</form>';
                        echo '<form method="POST" action="customerpage.php">';
                        echo '<input type="hidden" name="internal-call" value="true">';
                        echo '<input type="hidden" name="sendingmode" value="delete-post">';
                        echo '<input type="hidden" name="postID" value="'.$postID.'">';
                        echo '<input type="submit" name="editor-submit-remove" value="Delete post">';
                        echo '</form>';
                        echo '</div>';
                        # author panel
                        echo '<div class="sized-divider-midtext"></div><div id="content-author" class="flex-horiz"><h3 id="content-author-info-preline">Written by: </h3><div id="content-author-innerwrapper" class="flex-horiz">';
                        echo '<div id="content-author-img-wrapper">';
                        if (!empty($postAuthorData["ProfPic"])) {
                            $base64_image = base64_encode($postAuthorData["ProfPic"]);
                            $mime_type = 'image/png';
                            echo '<img src="data:' . $mime_type . ';base64,' . $base64_image . '" alt="User Profile Image">';
                        }
                        echo '</div>';
                        echo '<div id="content-author-info"><p>'.$choosenName.'</p></div>';
                        echo '</div></div>';
                    }
                }
                ?>
            </div>
        </div>
        <aside id="editor-sidebar" class="page-sidebar">
            <div id="comment-field">
                <div id="comment-field-innerwrapper">
                    <h2>Comments:</h2>
                    <div class="ui-smal-hdiv"></div>
                    <?php
                    function printComment($dbInstance,$commentTable,array $comment,int $startingIndent=0,int $indentBy=5) {
                        echo '<div class="comment-wrapper" style="margin-left:'.$startingIndent.'px;">';
                        $commentAuthorName = getClientNameFromID($dbInstance,$commentTable,$comment["AuthorID"]);
                        $commentAuthorData = getClientData($dbInstance,$commentTable,$comment["AuthorID"]);
                        echo '<div class="flex-vertic">';
                        echo '    <div class="comment-author-wrapper flex-horiz">';
                        if (!empty($commentAuthorData["ProfPic"])) {
                            $base64_image = base64_encode($commentAuthorData["ProfPic"]);
                            $mime_type = 'image/png';
                            echo '<img src="data:' . $mime_type . ';base64,' . $base64_image . '" alt="User Profile Image">';
                        }
                        echo '        <b class="comment-author">'.$commentAuthorName.'</b>';
                        echo '    </div>';
                        echo '    <p class="comment-content">'.str_replace("\n","<br>",$comment["Content"]).'</p>';
                        echo '</div>';
                        if (isset($comment["subComments"]) && !empty($comment["subComments"])) {
                            echo '<div class="subcomment-wrapper">';
                            foreach($comment["subComments"] as $subcomment) {
                                printComment($dbInstance,$commentTable,$subcomment,$startingIndent+$indentBy,$indentBy);
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                    $commentTree = getCommentTree($dbInstance,"comments", $postID);
                    if (count($commentTree) > 0 ) {
                        foreach ($commentTree as $comment) {
                            printComment($dbInstance,"users",$comment,0,15);
                        }
                    } else {
                        echo '<div id="no-comments-msg"><p>No comments yet!</p></div>';
                    }
                    ?>
                </div>
            </div>
        </aside>
    </main>
</body>
</html>