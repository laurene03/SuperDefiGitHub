<?php
$host = 'localhost'; //Hébergement local
$dbname = 'groupe1';
$username = 'root';
$password = ''; 

try { 
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", 
    $username, 
    $password 
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}