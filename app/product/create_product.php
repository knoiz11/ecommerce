<?php
if(!isset($_SESSION)){
    session_start();
}
require_once(__DIR__."/../config/Directories.php"); 
include(__DIR__."/../config/DatabaseConnect.php"); 
$db = new DatabaseConnect(); 


if($_SERVER["REQUEST_METHOD"] == "POST"){ 

    if (!isset($_FILES['productImage']) || $_FILES['productImage']['error'] !== 0) {
        $_SESSION["error"] = "No image attached";
       
       header("location: ".BASE_URL."views/admin/products/add.php");
        exit;
       }
       

    $productName    = htmlspecialchars($_POST["productName"]);
    $productDesc    = htmlspecialchars($_POST["description"]);
    $category       = htmlspecialchars($_POST["category"]);
    $basePrice      = htmlspecialchars($_POST["basePrice"]);
    $numberOfStocks = htmlspecialchars($_POST["numberofStocks"]);
    $unitPrice      = htmlspecialchars($_POST["unitPrice"]);
    $totalPrice     = htmlspecialchars($_POST["totalPrice"]);

   

    try {
        // Insert record
        $conn = $db->connectDB();
        $sql = "INSERT INTO products (product_name, product_description, category_id, base_price, stocks, unit_price, total_price, created_at, updated_at) 
                VALUES (:p_product_name, :p_product_description, :p_category_id, :p_base_price, :p_stocks, :p_unit_price, :p_total_price, NOW(), NOW())";
        
        $stmt = $conn->prepare($sql);  
        $data = [
            ':p_product_name'        => $productName,
            ':p_product_description' => $productDesc,
            ':p_category_id'         => $category,
            ':p_base_price'          => $basePrice,
            ':p_stocks'              => $numberOfStocks,
            ':p_unit_price'          => $unitPrice,
            ':p_total_price'         => $totalPrice
        ];

        if(!$stmt->execute($data)){ 
            $_SESSION["error"] = "Failed to insert record";
            header("location: ".BASE_URL."views/admin/products/index.php");
            exit;
        }

        $lastId = $conn->lastInsertId();

        $error = processImage($lastId);
        if($error){
            $_SESSION["error"] = $error;
            header("location: ".BASE_URL."views/admin/products/add.php");
            exit;
           }
        

        $_SESSION["success"] = "Product added successfully";
        header("location: ".BASE_URL."views/admin/products/index.php");
        exit;
    } catch (PDOException $e) {
        echo "Connection Failed: " . $e->getMessage();
        $db = null;
    }

}

function processImage($id){
    global $db;
    //retrieve $_FILES
    $path       = $_FILES['productImage']['tmp_name']; //actual file on tmp path
    $fileName   = $_FILES['productImage']['name']; //file name
    $fileType   = mime_content_type($path); //get file type

    //check if file type is not an image
    if($fileType != 'image/jpeg' && $fileType != 'image/png'){
        return "File is not a jpg/png file";
    }

    //rename image uploaded
    $newFileName = md5(uniqid($fileName, true));
    $fileExt     = pathinfo($fileName, PATHINFO_EXTENSION);
    $hashedName  = $newFileName . '.' . $fileExt;
    
    //move the image to project folder
    $destination = ROOT_DIR . 'public/uploads/products/' . $hashedName;
    if(!move_uploaded_file($path, $destination)){
        return "Transeferring of image returns an error";
    }

    //update the image_url field in products table
    $imageUrl = 'public/uploads/products/' .$hashedName;

    $conn   = $db->connectDB();
    $sql    = "UPDATE products SET image_url = :p_image_url WHERE id = :p_id";
    $stmt   = $conn->prepare($sql);
    $stmt->bindParam(':p_image_url',$imageUrl);
    $stmt->bindParam(':p_id',$id);

    //execute the query then throw an error if failed
    if(!$stmt->execute()){
        return "Failed to upload the image url field";
    };
    



    //return null if no error
    return null;
}

