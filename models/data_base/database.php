<?php
    try{
        $conn = new PDO("mysql:host=localhost;dbname=gericare_connect", "root");
    } catch(Exception $e) {
        return $e;
    }
?>
