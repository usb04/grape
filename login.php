<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'db.php';
 
if(isset($_POST['mailconnect'])) {
   $mailconnect = htmlspecialchars($_POST['mailconnect']);
   $mdpconnect = $_POST['mdpconnect'];
   if(!empty($mailconnect) AND !empty($mdpconnect)) {
      $requser = $bdd->prepare("SELECT id, userid, authid, pseudo, email, motdepasse, version FROM membres WHERE email = ?");
      $requser->execute(array($mailconnect));
      $userexist = $requser->rowCount();
      if($userexist == 1) {
         $userinfo = $requser->fetch();
        if ($userinfo[('version')] == 1) {
            $mdphash = $userinfo['motdepasse'];

            if (password_verify($mdpconnect, $mdphash)) {
                $_SESSION['id'] = $userinfo['id'];

                header("Location: app");
                exit();
            }
            else {
                $erreur = "wrong credentials";
            }
        }
        else {
            if (sha1($mdpconnect) == $userinfo["motdepasse"]) {
                $_SESSION['id'] = $userinfo['id'];
                //$_SESSION['pseudo'] = $userinfo['pseudo'];
                //$_SESSION['email'] = $userinfo['email'];

                //setcookie('user_identification', $userinfo['userid'], time() + 365*24*3600, null, null, false, true);
                //setcookie('user_authentification', $userinfo['authid'], time() + 365*24*3600, null, null, false, true);
                //setcookie('user_name', $userinfo['pseudo'], time() + 365*24*3600, null, null, false, true);

                header("Location: app");
                exit();
            }
            else {
                $erreur = "wrong credentials";
            }
        }
      } else {
         $erreur = "wrong credentials";
      }
   } else {
      $erreur = "all fields are required";
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
            <form id="login" method="POST" action="">
                <label for="mail">E-mail address</label><br>
                <input name="mailconnect" type="email" placeholder="testy04@example.com"><br>

                <label for="pass">Password</label><br>
                <input name="mdpconnect" type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
            </form>

            <a href="signup.php"><button class="sec">Create an account</button></a>
            <button class="prim" onclick="next();">Next</button>
            <?php if(isset($erreur)) {echo $erreur;} ?>
        </div>

        <script>
            function next() {
                document.getElementById("login").submit();
            }
        </script>
    </body>
</html>