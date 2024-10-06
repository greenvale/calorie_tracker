<?php include "head.php" ?>

<?php
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION["username"]))
{
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    if ($stmt->execute())
    {
        $stmt->store_result();

        if ($stmt->num_rows == 0)
        {
            // could not retrieve data for that username
            header("Location: login.php?error=1");
        }
        else
        {   
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
        
            if (password_verify($password, $hashed_password))
            {
                // passwords match so set session username parameter to indicate logged in
                $_SESSION["username"] = $username;
                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
                header("Location: login.php");
            }
            else
            {
                // passwords did not match
                header("Location: login.php?error=2");
            }
        }
    }
    else
    {
        // database error
        header("Location: login.php?error=0");
    }
    $stmt->close();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="with=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include "header.php" ?>

    <?php if (isset($_SESSION["username"])): ?>
        <p>Logged in</p>
    <?php else: ?>            
        <h2>Login</h2>

        <?php 
            if (isset($_GET["error"]))
            {
                if ((int)$_GET["error"] == 0)
                {
                    echo "Database interaction error";
                }
                else if ((int)$_GET["error"] == 1)
                {
                    echo "Username not found";
                }
                else if ((int)$_GET["error"] == 2)
                {
                    echo "Password is incorrect";
                }
                else
                {
                    echo "Error";
                }
            }
        ?>

        <form action="" method="POST">
            <input name="username" type="text" placeholder="Username">
            <br>
            <input name="password" type="password" placeholder="Password">
            <br>
            <button type="submit">Login</button>
        </form>
    <?php endif; ?>

</body>

</html>