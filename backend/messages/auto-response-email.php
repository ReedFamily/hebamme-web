<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    $messageBody = 
<<<EOT

Hallo {$post->getAnrede()} {$post->getLastname()},

Dies ist eine automatische Antwort der Hebammenpraxis Altenbeck und Kollegin in Moosburg.

Jemand wird Ihre Nachricht prüfen und sich so schnell wie möglich mit Ihnen in Verbindung setzen.

EOT

?>