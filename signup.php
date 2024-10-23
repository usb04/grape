<?php
error_reporting(E_ALL);

include 'db.php';

function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

//checks if it's sign up is disabled or not
$d_check = $bdd->prepare('SELECT * FROM config WHERE code = ?');
$d_check->execute(array('signup_disabled'));
$d_status = $d_check->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $mail = $_POST['mail']; 
    $mdp = $_POST['mdp'];
    $mdp2 = $_POST['mdp2'];

    if(!empty($_POST['pseudo']) AND !empty($_POST['mail']) AND !empty($_POST['mdp']) AND !empty($_POST['mdp2'])) {
        if(strlen($pseudo) <= 255) {
            if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                if($mdp2 == $mdp) {
                    $reqmail = $bdd->prepare("SELECT * FROM membres WHERE email = ?");
                    $reqmail->execute(array($mail));
                    $mailexist = $reqmail->rowCount();
                    if($mailexist == 0) {
                        $mdphash = password_hash($mdp, PASSWORD_DEFAULT);
                        $insertmbr = $bdd->prepare("INSERT INTO membres(pseudo, motdepasse, email, version) VALUES(?, ?, ?, ?)");
                        // $authid = guidv4() . guidv4() . guidv4();
                        // $insertmbr->execute(array(uniqid(), $authid, $pseudo, $mdp, $mail));
                        if (!$insertmbr->execute(array($pseudo, $mdphash, $mail, 1))) {
                            //print($insertmbr->errorInfo()[2]);
                            $erreur = "server error, please try again later";
                        }
                        else {
                            header("Location: login");
                            exit();
                        }
                    } else {
                        $erreur = "This email is already used";
                    }
                } else {
                 $erreur = "The passwords don't match";
             }
            } else {
                $erreur = "This does not look like an email address";
            }
        } else {
            $erreur = "Your username is too long";
        }
    } else {
        $erreur = "All fields are required";
    }
}


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login – Grape</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login">
        <img src="assets/logo.svg" class="logo">
        <hr>
        <?php

        if($d_status['status'] == 1) {
           echo $d_status['notice'];
       } else {?>
        <form id="login" method="POST" action="">
            <label for="pseudo">Choose a username</label><br>
            <input name="pseudo" type="text" required="" placeholder="patrick"><br>

            <label for="mail">E-mail address</label><br>
            <input name="mail" type="email" required="" placeholder="testy04@example.com"><br>

            <label for="mdp">Password</label><br>
            <input name="mdp" type="password" required="" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">

            <label for="mdp2">Password (again)</label><br>
            <input name="mdp2" type="password" required="" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
        </form>

        <a href="login.php"><button class="sec">I already have an account</button></a>
        <button class="prim" onclick="next();">Next</button>
        <?php if(isset($erreur)) {echo $erreur;} ?>
    </div>

    <script>
        function next() {
            document.getElementById("login").submit();
        }
    </script>

<?php } ?>
</body>
</html>