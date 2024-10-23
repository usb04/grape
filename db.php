<?php
$bdd = new PDO('mysql:host=127.0.0.1;dbname=grape', 'root', 'password');

$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);