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

?>