<?php
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
        $current_page = "MENTIONS";
        include 'template/menu.php';
        ?>

        <content>
            <div id="post_list">
                <?php
                $posts_filter = "USER_MENTIONED";
                include 'load.php';
                ?>
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

            search.addEventListener("keydown", function(event) {
                if (event.key === "Enter") {
                    load_Search(search.value);
                }
            });

            const textarea = document.getElementById("newPost");

            const hashtag = document.getElementById("hashtag");

            textarea.addEventListener("input", function (e) {
                this.style.height = "auto";
                this.style.height = this.scrollHeight + "px";
            });

            function send_Post() {
                if (hashtag.value == "") {
                    hashtag.value = "grape";
                }

                $.get(`post?message=${textarea.value}&hashtag=${hashtag.value}`, function( data ) {
                    load_LastPost();
                    textarea.value = "";
                    hashtag.value = "";
                });
            }

            function load_Posts() {
                $( "#post_list" ).load( "load" );
            }

            function load_Search(searv) {
                $( "#post_list" ).load( `search?q=${searv}` );
            }

            function load_LastPost() {
                $.get( "last", function( data ) {
                    $( "#post_list" ).prepend( data );
                });
            }


            var waitForLoad = function () {
                if (typeof jQuery != "undefined") {                   
                    load_Posts();    
                } else {
                    window.setTimeout(waitForLoad, 500);
                }
            };

            window.setTimeout(waitForLoad, 500);   
        </script>
    </body>
</html>