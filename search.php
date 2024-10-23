<?php
include 'db.php';
session_start();

$loop = 0;

// fetch all posts
$req = $bdd->query('SELECT * FROM posts WHERE CONCAT(message, hashtag) LIKE "%'.$_GET['q'].'%" ORDER BY id DESC');
while($post = $req->fetch()) {
    $postuser = $bdd->prepare('SELECT * FROM membres WHERE userid = ?');
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
                ?>
        <p class="name"><?= $pu['pseudo'] . $mark ?></p>
        <p class="space">
            <a href="#" onclick="load_Search('<?= $post['hashtag'] ?>')" class="space_Name">#<?= $post['hashtag'] ?></a>
            – <?= $date ?>
        </p>
    </div>
</div>
<div class="post-content">
    <p class="post-text"><?= $post['message'] ?></p>

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