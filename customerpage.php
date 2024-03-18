<?php
# Link requirements
require("php/_functions.php");
# Define sql-arguments
$sqlargs = array("localhost","root","","pagehost");
# Create connectionInstances
$conRet = validateDBConnection( connectDB($sqlargs) );
if (is_string($conRet)) { echo $conRet;$dbInstance=null; } else { $dbInstance = $conRet; }

session_start();

if (isset($_SESSION["clientID"])) {
    $clientID = $_SESSION["clientID"];
    $clientName = getClientNameFromID($dbInstance,"users",$clientID);
    $clientData = getClientData($dbInstance,"users",$clientID);
} else {
    header("Location:signin.php");
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
    <link rel="stylesheet" href="./css/customerpage.css">
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
            </div>
        </div>
    </header>
    <main class="flex-horiz">
        <div id="content-wrapper">
            <section id="customerpage-content" class="page-content">
                <h2>Your posts</h2>
                <div class="ui-smal-hdiv"></div>
                <?php
                # Get posts
                $posts = getPostsByClient($dbInstance,"posts",$clientID);
                foreach ($posts as $post) {
                    $postHeader = $post["Header"];
                    $postID = $post["ID"];
                    $postAuthor = getClientNameFromID($dbInstance,"users",$post["AuthorID"]);
                    echo <<<EOT
                    <div class="post-link">
                        <form  action="editor.php" method="POST">
                            <input type="hidden" name="postsel_id" value="$postID">
                            <input class="styled-form-btn-to-a" type="submit" name="postsel_submit" value="$postHeader">
                            <label>by $postAuthor</label>
                        </form>
                    </div>
                    EOT;
                }
                ?>
                <h2>Others posts you have access to:</h2>
                <div class="ui-smal-hdiv"></div>
                <?php
                # Get posts
                $posts = getPostsWhereClientIsAccessee($dbInstance,"posts","accessees",$clientID);
                foreach ($posts as $post) {
                    $postHeader = $post["Header"];
                    $postID = $post["ID"];
                    $postAuthor = getClientNameFromID($dbInstance,"users",$post["AuthorID"]);
                    echo <<<EOT
                    <div class="post-link">
                        <form  action="editor.php" method="POST">
                            <input type="hidden" name="postsel_id" value="$postID">
                            <input class="styled-form-btn-to-a" type="submit" name="postsel_submit" value="$postHeader">
                            <label>by $postAuthor</label>
                        </form>
                    </div>
                    EOT;
                }
                ?>
            </section>
        </div>
        <aside id="customerpage-sidebar" class="page-sidebar">
            <section id="logout-field">
                <?php
                echo '<h2>Logged in as: '.$clientName.'</h2>';
                ?>
                <form method="POST" action="signin.php">
                    <input type="hidden" name="internal-call" value="true">
                    <input type="hidden" name="sendingmode" value="logout">
                    <input type="submit" name="logout-client" value="LogOut">
                </form>
            </section>
            <section id="account-field">
                <h2>Account Options:</h2>
                <div class="ui-smal-hdiv"></div>
                <form method="POST" action="customerpage.php">
                    <?php
                    echo '<div id="account-id"><h3>Account ID: '.$clientID.'</h3></div>';
                    if (!empty($clientData["ProfPic"])) {
                        $base64_image = base64_encode($clientData["ProfPic"]);
                        $mime_type = 'image/png';
                        echo '<img class="account-opts-profpic" src="data:' . $mime_type . ';base64,' . $base64_image . '" alt="User Profile Image"><br>';
                    }
                    echo '<input type="hidden" name="internal-call" value="true">';
                    echo '<input type="hidden" name="sendingmode" value="update">';
                    echo '<label>Username:</label><input type="text" name="username" value="'.$clientData["Username"].'"><br>';
                    echo '<label>Password:</label><input type="text" name="password" value="'.$clientData["Password"].'"><br>';
                    echo '<label>Display Name:</label><input type="text" name="dispname" value="'.$clientData["DispName"].'"><br>';
                    ?>
                    <input type="submit" name="update-clientdata" value="Save">
                </form>
            </section>
        </aside>
    </main>
</body>
</html>