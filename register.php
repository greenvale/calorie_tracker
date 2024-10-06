<?php include "head.php" ?>

<?php

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = $_POST["username"];
    $password = $_POST["password"];
    $firstname = $_POST["firstname"];
    $surname = $_POST["surname"];
    $dob = $_POST["dob"];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    if ($stmt->execute())
    {
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0)
        {
            // username already exists
            header("Location: register.php?error=1");
        }
        else
        {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password, firstname, surname, dob) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $hashed_password, $firstname, $surname, $dob);
            if ($stmt->execute())
            {
                // data insertion successful
                header("Location: register.php?success=1");
            }
            else
            {
                // data insertation failed
                header("Location: register.php?error=0");
            }
        }
    }
    else
    {
        // username count failed
        header("Location: register.php?error=0");
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
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include "header.php" ?>
        
    <h2>Register</h2>

    <?php if (isset($_GET["success"]) && (int)$_GET["success"] == 1): ?>
        <p>"Successfully registered"</p>
    <?php else: ?>
        <?php 
            if (isset($_GET["error"]))
            {
                if ($_GET["error"] == 0)
                {
                    echo "<p>Database interaction error</p>";
                }
                else if ($_GET["error"] == 1)
                {
                    echo "<p>Username already taken</p>";
                }

            }
        ?>
        <form action="" method="POST">

            <label for="username">Username</label><br>
            <input name="username" type="text"><br>

            <label for="firstname">First name</label><br>
            <input name="firstname" type="text"><br>

            <label for="surname">Surname</label><br>
            <input name="surname" type="text"><br>

            <label for="dob">Date of birth</label><br>
            <input type="date" id="dob" name="dob"><br>

            <label for="password">Password</label><br>
            <input name="password" type="password"><br>

            <label for="password">Confirm password</label><br>
            <input name="confirm_password" type="password"><br>

            <button type="submit">Create account</button>
        </form>

    <?php endif; ?>



</body>

</html>