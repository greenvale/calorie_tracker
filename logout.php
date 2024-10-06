<?php include "head.php" ?>

<?php
    session_unset();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="with=device-width, initial-scale=1.0">
    <title>Log out</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include "header.php" ?>

    <p>Logged out</p>
</body>

</html>