<?php
    function actifp($page) {
        global $current_page;
        if ($current_page == $page) {
            echo "selected";
        }
    }
?>
<menu>
    <div class="logo">
        <img class="logo_inner icon" src="assets/grape_icon.svg" alt="Grape">
        <img class="logo_inner text" src="assets/grape_G.svg" style="--jump: 50ms" alt="" aria-hidden="true">
        <img class="logo_inner text" src="assets/grape_R.svg" style="--jump: 100ms" alt="" aria-hidden="true">
        <img class="logo_inner text" src="assets/grape_A.svg" style="--jump: 150ms" alt="" aria-hidden="true">
        <img class="logo_inner text" src="assets/grape_P.svg" style="--jump: 200ms" alt="" aria-hidden="true">
        <img class="logo_inner text" src="assets/grape_E.svg" style="--jump: 250ms" alt="" aria-hidden="true">
    </div>
    
    <a href="app" class="tab <?= actifp('APP') ?>" style="--let: 50ms">
        <span class="material-icons">
            home
        </span>
        <p>latest posts</p>
    </a>
    
    <a href="mentions" class="tab <?= actifp('MENTIONS') ?>" style="--let: 100ms">
        <span class="material-icons">
            notifications
        </span>
        <p>mentions</p>
    </a>
    
    <a href="user" class="tab <?= actifp('USER') ?>" style="--let: 150ms">
        <img src="./usercontent/profilepic_<?= $u['avatar'] ?>" alt="" class="pic2">
        <?php 
        $mark = "";
        if ($u['verified'] == 1) {
            $mark = "<img src=assets/certification_mark.svg class=verified2>";
        }
        ?>
        <p class="close2"><?= $u['pseudo'] . $mark ?></p>
    </a>

    <a href="#" onclick="load_Posts();" class="tab" style="--let: 200ms">
        <span class="material-icons">
            autorenew
        </span>
        <p>reload posts</p>
    </a>
    
    <a href="logout" class="tab" style="--let: 200ms">
        <span class="material-icons">
            meeting_room
        </span>
        <p>logout</p>
    </a>
</menu>