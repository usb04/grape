<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'db.php';
include __DIR__ . "/include/logincheck.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['newpseudo']) AND $_POST['newpseudo'] != $u['pseudo']) {
      $newpseudo = htmlspecialchars($_POST['newpseudo']);
      if (strlen($newpseudo) <= 255) {
        $insertpseudo = $bdd->prepare("UPDATE membres SET pseudo = ? WHERE id = ?");
        $insertpseudo->execute(array($newpseudo, $u['id']));
      }
      else {
        $msg = "Your username is too long";
      }
    }

    if (!empty($_POST['newmail']) AND $_POST['newmail'] != $u['email']) {
        $newmail = $_POST['newmail'];
        if (filter_var($newmail, FILTER_VALIDATE_EMAIL)) {
            $reqmail = $bdd->prepare("SELECT * FROM membres WHERE email = ?");
            $reqmail->execute(array($newmail));
            $mailexist = $reqmail->rowCount();
            if($mailexist == 0) {
                $insertmail = $bdd->prepare("UPDATE membres SET email = ? WHERE id = ?");
                $insertmail->execute(array($newmail, $u['id']));
            }
            else {
                $msg = "This email is already used";
            }
        }
        else {
            $msg = "This does not look like an email address";
        }

    }

    if(!empty($_POST['newmdp1']) AND !empty($_POST['newmdp2'])) {
        $mdp = $_POST['newmdp1'];
        $mdp2 = $_POST['newmdp2'];
        if ($mdp == $mdp2) {
            if ($u["version"] == 1) {
                $newmdp = password_hash($mdp, PASSWORD_DEFAULT);
            }
            else {
                $newmdp = sha1($mdp);
            }
            $insertmdp = $bdd->prepare("UPDATE membres SET motdepasse = ? WHERE id = ?");
            $insertmdp->execute(array($newmdp, $u['id']));
        } else {
            $msg = "The passwords doesn't match";
        }
    }

    if(isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name'])) {
        if ($u["version"] == 1) {
            $msg = "changing the profile picture is not yet supported on accounts created on April 20th, 2022 and after";
        }
        else {
            $tailleMax = 2097152;
            $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
            if($_FILES['avatar']['size'] <= $tailleMax) {
               $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
               if(in_array($extensionUpload, $extensionsValides)) {
                  $chemin = "usercontent/profilepic_".$_COOKIE['user_identification'].".".$extensionUpload;
                  $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
                  if($resultat) {
                     $updateavatar = $bdd->prepare('UPDATE membres SET avatar = :avatar WHERE id = :id');
                     $updateavatar->execute(array(
                        'avatar' => $_COOKIE['user_identification'].".".$extensionUpload,
                        'id' => $u['id']
                        ));
                  } else {
                     $msg = "something went wrong";
                  }
               } else {
                  $msg = "jpg, jpeg, gif, png only please";
               }
            } else {
               $msg = "too large (max 2Mo)";
            }
        }
    }

    if (empty($msg)) {
        header('Location: app');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width,initial-scale=1'>

        <title>Grape</title>

        <link rel='icon' type='image/png' href='/favicon.png'>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet">

        <link rel='stylesheet' href='/css/global.css'>
        <link rel='stylesheet' href='/css/menu.css'>
        <link rel='stylesheet' href='/css/content.css'>
        <link rel='stylesheet' href='/css/content/post.css'>
    </head>

    <body>
        <?php
        $current_page = "USER";
        include 'template/menu.php';
        ?>

        <content>
            <div class="edit">
                <h1>Edit my profile</h1>
                <small>fill only the fields you want to change</small>

                <form method=POST action="" enctype="multipart/form-data">
                    <input type=text name=newpseudo placeholder="username"><br/>
                    <input type=email name=newmail placeholder="e-mail"><br/>
                    <input type=password name=newmdp1 placeholder="password"><br/>
                    <input type=password name=newmdp2 placeholder="password (again)" class=endA><br/>
                    <label for=avatar>your profile picture</label><br/>
                    <input type=file name=avatar placeholder="your profile picture" class=endB><br/>

                    <?php if(isset($msg)) {echo $msg;} ?>
                    
                    <button>save</button>
                </form>
            </div>
            <a href="https://forms.gle/ruCRf8j7S4BxLjDQ8"><h5>apply for certification here</a>
        </content>
    </body>
</html>