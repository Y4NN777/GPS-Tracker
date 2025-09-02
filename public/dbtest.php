<?php

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=gpstracker", "root", "");
    echo "Connexion OK ✅";
} catch (PDOException $e) {
    echo "Erreur ❌ : " . $e->getMessage();
}

