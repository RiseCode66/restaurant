<?php
// info.php

// Chemin vers le fichier de log Symfony
$logFile = '/var/www/html/var/log/prod.log';  // Modifie le chemin si nécessaire

// Vérifie si le fichier existe
if (file_exists($logFile)) {
    // Ouvre le fichier pour le lire ligne par ligne
    $handle = fopen($logFile, 'r');
    if ($handle) {
        echo "<pre>";  // Utilisé pour formater l'affichage
        while (($line = fgets($handle)) !== false) {
            echo htmlspecialchars($line) . "<br>";
        }
        fclose($handle);
        echo "</pre>";
    } else {
        echo "Impossible de lire le fichier de log.";
    }
} else {
    echo "Le fichier de log n'existe pas.";
}
