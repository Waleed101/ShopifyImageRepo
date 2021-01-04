<?php
    $servername = "localhost";
    $username = "drip_jobs";
    $password = "waleed123-";
    $dbname = "drip_shopify";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    
    $sql="SELECT UUID() `uuid`";
    $result=mysqli_query($conn,$sql);
    $data = mysqli_fetch_assoc($result);
    
    $sql = "INSERT INTO image (imageID, userID)
    VALUES ('" . $data["uuid"] . "', 123)";
    
    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
?>
