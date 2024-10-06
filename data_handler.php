<?php include "head.php" ?>

<?php

$dsn = "mysql:host=".$host.";dbname=".$db.";charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] === "GET") 
    {
        if (isset($_SESSION["username"]) && isset($_SESSION["csrf_token"]) && isset($_GET["token"]) && $_GET["token"] == $_SESSION["csrf_token"] 
            && isset($_GET["date"]) && isValidDate($_GET["date"]))
        {            
            $stmt = $pdo->prepare("SELECT * FROM `nutrition` WHERE `date` = :dateValue AND `username` = :username");
            
            $stmt->bindParam(":dateValue", $_GET["date"]);
            $stmt->bindParam(":username", $_SESSION["username"]);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row)
            {
                echo json_encode(["success" => true, "calories" => $row["calories"]]);
            }
            else
            {
                echo json_encode(["success" => false, "error" => "No rows returned"]);
            }
        }
        else
        {
            echo json_encode(["success" => false, "error" => "Issue with token, login or data provided"]);
        }
    }
    elseif ($_SERVER["REQUEST_METHOD"] === "POST") 
    {
        if (isset($_SESSION["username"]) && isset($_SESSION["csrf_token"]) && isset($_POST["token"]) && $_POST["token"] == $_SESSION["csrf_token"] 
            && isset($_POST["date"]) && isset($_POST["calories"]) && isValidDate($_POST["date"]))
        {

            if (empty($_POST["calories"]))
            {                
                $stmt = $pdo->prepare("DELETE FROM nutrition WHERE `date` = :date AND `username` = :username");

                $stmt->bindParam(":date", $_POST["date"]);
                $stmt->bindParam(":username", $_SESSION["username"]);

                $stmt->execute();
            }
            else
            {
                $stmt = $pdo->prepare("INSERT INTO nutrition (`date`, `calories`, `username`) VALUES (:date, :calories, :username)
                    ON DUPLICATE KEY UPDATE calories = :calories;");

                $stmt->bindParam(":date", $_POST["date"]);
                $stmt->bindParam(":calories", $_POST["calories"]);
                $stmt->bindParam(":username", $_SESSION["username"]);

                $stmt->execute();
            }
            echo json_encode(["success" => true]);
        } 
        else 
        {
            echo json_encode(["success" => false, "error" => "Issue with token, login or data provided"]);
        }
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>
