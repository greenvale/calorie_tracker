<!-- Header -->
<header>
    <div class="navbar">
        <div class="navbar-left">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>  
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION["username"])): ?>
                <!-- Logged-in -->
                <?php
                    $conn = new mysqli($host, $user, $pass, $db);
                    if ($conn->connect_error) 
                    {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $stmt = $conn->prepare("SELECT firstname, surname FROM users WHERE username = ?");
                    $stmt->bind_param("s", $_SESSION["username"]); // may need to check that this is still a valid username
                    
                    if ($stmt->execute())
                    {
                        $stmt->store_result();
                        
                        if ($stmt->num_rows == 1)
                        {
                            $stmt->bind_result($firstname, $surname);
                            $stmt->fetch();
                            echo "<span><strong>" . htmlspecialchars($firstname) . " " . htmlspecialchars($surname) . "</strong></span>";
                        }
                        else
                        {
                            die("Username not found or multiple username entries found");
                        }
                    }
                    else
                    {
                        die("Username search failed");
                    }
                    $stmt->close();
                    $conn->close();
                ?>
                <a href="record.php">My record</a>
                <a href="logout.php">Logout</a> 
            <?php else: ?>
                <!-- Not logged-in -->
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>