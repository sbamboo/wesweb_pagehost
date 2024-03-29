<?php
# Link requirements
require("php/_functions.php");
# Define sql-arguments
$sqlargs = array("localhost","root","","pagehost");
# Create connectionInstances
$conRet = validateDBConnection( connectDB($sqlargs) );
if (is_string($conRet)) { echo $conRet;$dbInstance=null; } else { $dbInstance = $conRet; }

session_start();
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
                <div id="nav-login">
                    <div class="ui-button ui-size-button-big">
                        <?php
                            if (isset($_SESSION["clientID"])) {
                                echo'<a href="./signin.php?login">Personal Page</a>';
                            } else {
                                echo'<a href="./signin.php?login">Login</a>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div id="page-content">
            <section id="public-posts">
                <h2>Public-Posts</h2>
                <div class="ui-smal-hdiv"></div>
                <?php
                # Get posts
                $posts = getPostSelData($dbInstance,"posts");
                foreach ($posts as $post) {
                    $postHeader = $post["Header"];
                    $postID = $post["ID"];
                    $postAuthor = getClientNameFromID($dbInstance,"users",$post["AuthorID"]);
                    echo <<<EOT
                    <div class="post-link">
                        <form  action="viewer.php" method="POST">
                            <input type="hidden" name="postsel_id" value="$postID">
                            <input class="styled-form-btn-to-a" type="submit" name="postsel_submit" value="$postHeader">
                            <label>by $postAuthor</label>
                        </form>
                    </div>
                    EOT;
                }
                ?>
            </section>
            <section id="private-posts">
                <h2>Private-Posts</h2>
                <div class="ui-smal-hdiv"></div>
                <?php
                    if (isset($_SESSION["clientID"])) {
                        # Get posts
                        $posts = getPostsByClient($dbInstance,"posts",$_SESSION["clientID"]);
                        if (count($posts) < 1) {
                            echo <<<EOT
                            <form method="POST" action="editor.php">
                                <input type="hidden" name="internal-call" value="true">
                                <input type="hidden" name="sendingmode" value="create-new">
                                <label>Oh looks like you haven't posted anything yet? </label><input type="submit" name="submit-create-new" value="Create new">
                            </form>
                            EOT;
                        } else {
                            foreach ($posts as $post) {
                                $postHeader = $post["Header"];
                                $postID = $post["ID"];
                                $postAuthor = getClientNameFromID($dbInstance,"users",$post["AuthorID"]);
                                echo <<<EOT
                                <div class="post-link">
                                    <form  action="viewer.php" method="POST">
                                        <input type="hidden" name="postsel_id" value="$postID">
                                        <input class="styled-form-btn-to-a" type="submit" name="postsel_submit" value="$postHeader">
                                        <label>by $postAuthor</label>
                                    </form>
                                </div>
                                EOT;
                            }
                        }
                    } else {
                        echo '<p>To view private posts please login!</p>';
                    }
                ?>
            </section>
        </div>
    </main>
</body>
</html>