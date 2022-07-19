<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    $messageBody = 
<<<EOT

Es wurde eine Anfrage zum Zurücksetzen Ihres Passworts gestellt. 
Wenn Sie diese Anfrage gestellt haben, klicken Sie bitte auf den unten stehenden Link. 
Wenn Sie kein Zurücksetzen des Kennworts angefordert haben, ignorieren Sie diese Meldung. 
Der Link ist ab dem Zeitpunkt der Anfrage nur eine Stunde lang gültig.

{$linkBuilder->getRecoveryLink()}


EOT
?>