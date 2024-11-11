<?php
session_start();

require_once(__DIR__."/../config/Directories.php"); 
include(__DIR__."/../config/DatabaseConnect.php"); 

    $db = new DatabaseConnect();
    $conn = $db->connectDB();

    $productList = [];
    if($_SERVER["REQUEST_METHOD"] == "POST"){ 
        $productId = $_POST["id"];

    try {

        $sql = "DELETE FROM `products` WHERE products.id = :p_id"; //delete query here
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':p_id', $productId);
        $stmt->execute();

        $_SESSION["success"] = "Product has been deleted";
        header("location: ".BASE_URL."views/admin/products/index.php");
        exit;
        
       

    } catch (PDOException $e){
        echo "Connection Failed: " . $e->getMessage();
        $db = null;
    }
}