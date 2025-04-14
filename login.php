<?php
// Start session
session_start();

// Connect to database
$conn = new mysqli("localhost", "root", "", "your_database_name");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($_POST["action"] === "register") {
        // Signup logic
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);
        if ($stmt->execute()) {
            echo "✅ Registered successfully!";
        } else {
            echo "❌ Username may already exist.";
        }
    } elseif ($_POST["action"] === "login") {
        // Login logic
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            if (password_verify($password, $hashedPassword)) {
                $_SESSION["user"] = $username;
                echo "✅ Logged in successfully!";
            } else {
                echo "❌ Incorrect password.";
            }
        } else {
            echo "❌ User not found.";
        }
    }
}
?>

<!-- HTML FORM -->
<h2>Login / Sign Up</h2>
<form method="post">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>

    <button type="submit" name="action" value="login">Log In</button>
    <button type="submit" name="action" value="register">Sign Up</button>
</form>
