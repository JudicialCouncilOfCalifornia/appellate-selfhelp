<?php
echo "sample page";
$servername = "ehelp-dca-wp-mysql.mysql.database.azure.com:3306";
//$servername = "127.0.0.1:3306";
//$servername = "localhost:3306";
$username = "mysqldba@ehelp-dca-wp-mysql";
$password = "@dm1n4@MySQL";
$database = "ehelpdcawp";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
//echo "<pre>";
//print_r($conn);
//echo "</pre>";
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);

$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);

$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
$sql = "SELECT * FROM wp_posts";
$result = $conn->query($sql);
$sql1 = "SELECT * FROM wp_postmeta";
$result1 = $conn->query($sql1);
echo "Connected successfully";
?>
