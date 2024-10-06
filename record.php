<?php include "head.php" ?>

<?php 

if (!isset($_SESSION["username"]))
{
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="with=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="<?php if (isset($_SESSION["csrf_token"])) { echo $_SESSION["csrf_token"]; } ?>">
</head>


<body>
    <?php include "header.php" ?>

    <h2>My record</h2>
    <p>Here is your personal calorie data</p>

    <button id="leftWeekButton"><<</button>
    <span id="weekTitle">Week starting: DD/MM/YYYY</span>
    <button id="rightWeekButton">>></button>
    <table id="weekTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Calories</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script src="table.js"></script>
</body>

</html>