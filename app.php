<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include 'db.php';
include __DIR__ . "/include/logincheck.php";
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
        $current_page = "APP";
        include 'template/menu.php';
        ?>

        <content>
            <input class="search" id="search" type="text" placeholder="search for anything">

            <div class="post newP">
                <div class="post-top">
                    <div class="info">
                        <p class="name">new grape</p>
                        <p class="space">
                            #
                            <input class="space_inner" placeholder="grape" id="hashtag">
                        </p>
                        <button onclick="send_Post()" class="p_button">PUBLISH</button>
                    </div>
                </div>
                <div class="post-content">
                    <textarea placeholder="write anything..." class="post-text" id="newPost"></textarea>
                </div>
            </div>

            <div id="post_list">
                <?php include 'load.php'; ?>
            </div>

        </content>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>       
            function getGrapeUrlLetter(letter) {
                return './assets/grape_' + letter + '.svg';
            }
        
            function getLetterJump(order) {
                return 50 * order;
            }

            const search = document.getElementById("search");

            const textarea = document.getElementById("newPost");

            const hashtag = document.getElementById("hashtag");

            search.addEventListener("keydown", function(event) {
                if (event.key === "Enter") {
                    load_Search(search.value);
                }
            });

            textarea.addEventListener("input", function (e) {
                this.style.height = "auto";
                this.style.height = this.scrollHeight + "px";
            });

            function onlySpaces(str) {
                return str.trim().length === 0;
            }

            function send_Post() {
                let noHashtag = false;

                if (hashtag.value == "") {
                    hashtag.value = "grape";
                    noHashtag = true;
                }

                if (onlySpaces(textarea.value)) {
                    alert("you can't post nothing");
                    return;
                }
                else if (onlySpaces(hashtag.value)) {
                    alert("you can't post with no hashtags");
                    return;
                }
                else {
                    function nl2br(str){
                        return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
                    }
                    let msgSend = nl2br(textarea.value);
                    $.get(`post?message=${msgSend}&hashtag=${hashtag.value}`, function( data ) {
                        if(noHashtag) {
                            
                        }
                        else {
                            load_Search(hashtag.value);
                        }
                        textarea.value = "";
                        hashtag.value = "";
                    });
                }
            }

            function load_Posts() {
                if (search.value == "") {
                    $( "#post_list" ).load( "load" );
                }
                else {
                    load_Search(search.value);
                }
            }

            function load_Search(searv) {
                $( "#post_list" ).load( `search?q=${searv}` );
                hashtag.value = searv;
            }

            function load_LastPost() {
                $.get( "load.php?filter=LAST", function( data ) {
                    $( "#post_list" ).prepend( data );
                });
            }

            let lastData = "";

            function prepareLast() {
                $.get( "load.php?filter=LAST", function( data ) {
                    lastData = data;
                });

                setInterval(() => {
                    reloadLast();
                }, 3000);
            }

            function reloadLast() {
                if(search.value == "") {
                    $.get( "load.php?filter=LAST", function( data ) {
                        if (data != lastData) {
                            $( "#post_list" ).prepend( data );
                            lastData = data;
                        }
                    });
                }
            }

            var waitForLoad = function () {
                if (typeof jQuery != "undefined") {                   
                    //load_Posts();    
                    prepareLast();
                } else {
                    window.setTimeout(waitForLoad, 500);
                }
            };

            waitForLoad();   
        </script>
    </body>
</html>