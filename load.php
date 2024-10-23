<?php
include 'db.php';

$loop = 0;

if (!isset($u)) {
    session_start();
    if (isset($_COOKIE['user_authentification']) || isset($_COOKIE['user_identification'])) {
        http_response_code(400);
        echo "Invalid session. Please logout and log back in.";
        exit();
    }
    else if(isset($_SESSION['id'])) {
        $getid = $_SESSION["id"];
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
}

if (isset($_GET["filter"])) {
    $posts_filter = $_GET["filter"];
}

if (isset($posts_filter)) {
    switch ($posts_filter) {
        case "USER_MENTIONED":
            $sql = 'SELECT * FROM posts WHERE message LIKE "%@'.$u['pseudo'].'%" ORDER BY id DESC';
            break;
        case "LAST":
            $sql = 'SELECT * FROM posts ORDER BY id DESC LIMIT 1';
            break;
        default:
            $sql = 'SELECT * FROM posts ORDER BY id DESC';
            break;
    }
}
else {
    $sql = 'SELECT * FROM posts ORDER BY id DESC';
}

// fetch all posts
$req = $bdd->query($sql);
while($post = $req->fetch()) {
    // The userids in the v1 posts refer to the id column of a user while in v0 they refer to the userid column of the user
    if ($post["version"] == 1) {
        $postuser = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
    }
    else {
        $postuser = $bdd->prepare('SELECT * FROM membres WHERE userid = ?');
    }
    $postuser->execute(array($post['userid']));
    $pu = $postuser->fetch();

    // H:i:s

    $date = date("d/m/Y", strtotime($post['uploadtime']));

    if(date("Y-m-d H:i", strtotime($post['uploadtime'])) === date("Y-m-d H:i")) {
        $date = "just now";
    }
    else if(date("Y-m-d", strtotime($post['uploadtime'])) === date("Y-m-d")) {
        $date = date("H:i", strtotime($post['uploadtime']));
    }

    $loop++;

    $msg = nl2br($post['message']);
    //$msg = preg_replace('/@'.$_COOKIE['user_name'].'/', '<span class="mention you">@'.$_COOKIE['user_name'].'</span>', $post['message']); 
    $msg = preg_replace('/@'.$u["pseudo"].'/', '<span class="mention you">@'.$u["pseudo"].'</span>', $post['message']);

    $youtube_regex = "^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$";
    $videoid_regex = "#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#";

    if (preg_match('/'.$youtube_regex.'/', $msg)) {
        preg_match($videoid_regex, $msg, $videoid);
        $msg = preg_replace('/'.$youtube_regex.'/', '<iframe class="yte" src="https://youtube.com/embed/'.$videoid[0].'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $msg);
    }
?>

<div class="post" style="--loopMS:<?php echo $loop * 50 ?>ms;">
<div class="post-top">
    <img src="./usercontent/profilepic_<?= $pu['avatar'] ?>" alt="" class="pic">
    <div class="info">
                <?php 
                    $mark = "";
                    if ($pu['verified'] == 1) {
                        $mark = "<img src=assets/certification_mark.svg class=verified>";
                    }
                    if ($pu['dev'] == 1) {
                        $mark = "<img src=assets/dev_mark.svg class=verified>";
                    }
                    if ($pu['partner'] == 1) {
                        $mark = "<img src=assets/partner_mark.svg class=verified>";
                    }
                ?>
        <p class="name"><?= $pu['pseudo'] . $mark ?></p>
        <p class="space">
            <a href="#" onclick="load_Search('<?= $post['hashtag'] ?>')" class="space_Name">#<?= $post['hashtag'] ?></a>
            – <?= $date ?>
        </p>
    </div>
</div>
<div class="post-content">
    <p class="post-text"><?= $msg ?></p>

    <div class="reactions">
        <div class="reaction" onclick="alert('This is coming soon!');">
            <p class="reacName">ok</p>
            <p class="reacNb"><?= $post['ok'] ?></p>
        </div>
        <div class="reaction" onclick="alert('This is coming soon!');">
            <p class="reacName">no</p>
            <p class="reacNb"><?= $post['no'] ?></p>
        </div>
        <div class="reaction" onclick="alert('This is coming soon!');">
            <p class="reacName">reply</p>
        </div>
    </div>
</div>
</div>

<?php
}
?>