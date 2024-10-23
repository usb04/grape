<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
 
include 'db.php';

if (isset($_COOKIE['user_authentification']) || isset($_COOKIE['user_identification'])) {
    //$getid = $_COOKIE['user_identification'];
    //$version = 0;
    http_response_code(400);
    echo "Invalid session. Please logout and log back in.";
    exit();
}
else if(isset($_SESSION['id'])) {
    $getid = $_SESSION["id"];
    $version = 1;
    $requser = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
    $requser->execute(array($getid));
    if ($requser->rowCount() > 0) {
        $u = $requser->fetch();
    }
    else {
        http_response_code(400);
        echo "Invalid session. Please logout and log back in.";
        exit();
    }
}
else {
    http_response_code(403);
    echo "Unauthorized - Missing login cookies.";
    exit();
}

if(isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    $message = str_replace('&lt;br&gt;', PHP_EOL, $message);
    if (isset($_GET["hashtag"])) {
        $hashtag = explode(" ", htmlspecialchars($_GET['hashtag']))[0];
    }
    else {
        $hashtag = "grape";
    }
                
    $time = date("Y-m-d H:i:s");

    $checkhashtags = $bdd->prepare("SELECT * FROM blacklisted_hashtags WHERE hashtag = ?");
    $checkhashtags->execute(array($hashtag));
    $blockedList = $checkhashtags->rowCount();
    if($blockedList >= 1) {
        $hashtag = "grape";
    }

    $insert = $bdd->prepare("INSERT INTO posts(postid, userid, message, hashtag, uploadtime, version) VALUES(?, ?, ?, ?, ?, ?)");
    if ($version == 1) {
        $insert->execute(array(uniqid(), $_SESSION["id"], $message, $hashtag, $time, 1));
    }
    else if ($version == 0) {
        $insert->execute(array(uniqid(), $_COOKIE["user_identification"], $message, $hashtag, $time, 0));
    }
}

?>