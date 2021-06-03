<?php
    if(!defined("CONST_KEY") || CONST_KEY !== "035416f4-e65b-4fc6-a8db-301604ff31c5"){
        header("Location: ../404.html", true, 404);
        echo file_get_contents('../404.html');
        die;
    }

    $messageBody = 
<<<EOT

Jemand hat Sie über das Kontaktformular auf der Website kontaktiert:

{$post->getAnrede()}
{$post->getLastname()}, {$post->getFirstname()} 

Email: {$post->getEmail()} 

Telefon: {$post->getPhone()} 

Adress:
{$post->getAddress()} 

Bitte kontaktieren Sie mich per
{$post->getPreferredContact()}

Nachricht:
{$post->getMessage()}


EOT


?>