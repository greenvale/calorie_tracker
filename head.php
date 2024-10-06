<?php

session_start();

$host = "localhost";
$db = "my_database";
$user = "root";
$pass = "";

// returns true if a date string is in format YYYY-MM-DD
function isValidDate($dateStr)
{
    $d = DateTime::createFromFormat("Y-m-d", $dateStr);
    return $d && $d->format("Y-m-d") === $dateStr;
}

?>