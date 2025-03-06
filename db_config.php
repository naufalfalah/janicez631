<?php

$dsn = "mysql:host=localhost;dbname=dbs17gptfgbqba";
$username = "u4hhoiy7gab9q";
$password = "22g&@1{ofje@";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}


function pretty_print($data, $die = true){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if($die) die;
}

?>