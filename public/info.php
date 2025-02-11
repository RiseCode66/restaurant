<?php
// info.php

// Chemin vers le fichier de log Symfony
$logFile = '/var/log/faillog';  // Modifie le chemin si nécessaire

// Vérifie si le fichier existe
if (file_exists($logFile)) {
    // Lis le contenu du fichier
    $logContents = file_get_contents($logFile);

    // Affiche les logs dans le navigateur
    echo "<pre>$logContents</pre>";
} else {
    echo "Le fichier de log n'existe pas.";
}
