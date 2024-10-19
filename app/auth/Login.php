<?php
// received user input
$username = $_POST["username"];
$password = $_POST["password"];

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $database = "ecommerce";
    $dbusername = "root";
    $dbpassword = "";

    // Define the Data Source Name (DSN)
    $dsn = "mysql:host=$host;dbname=$database;";
    
    try {
        // Create a new PDO connection
        $conn = new PDO($dsn, $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prepare the SQL statement
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = :p_username');
        $stmt->bindParam(':p_username', $username);
        $stmt->execute();
        $users = $stmt->fetchAll();
        if($users){
            if(password_verify($password,$users[0]["password"])){
                header("location: /index.php");
                $_SESSION["fullname"] = $users[0]["fullname"] ;
                exit;
            } else {
                header("location: /Login.php");
                $_SESSION["error"] = "password did not match";
            exit;
            }
        
        } else {
            header("location: /Login.php");
            $_SESSION["error"] = "user not found";
            exit;
        }
    } catch (Exception $e) {
        echo "Connection Failed: " . $e->getMessage();
    }
}
?>
