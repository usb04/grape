<?php

if (isset($_COOKIE['user_authentification']) || isset($_COOKIE['user_identification'])) {
    header("Location: logout");
    exit();
}
else if (isset($_SESSION['id'])) {
    $getid = $_SESSION["id"];
    $requser = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
    $requser->execute(array($getid));
    if ($requser->rowCount() > 0) {
        $u = $requser->fetch();
    }
    else {
        header("Location: logout");
        exit();
    }
}
else {
    header("Location: login");
    exit(); 
}

?>