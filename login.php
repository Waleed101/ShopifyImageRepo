<?php
    include('config.php');
    var_dump($_SESSION['uID']);
    if(!is_null($_GET['user'])) {
        echo "A";
        $user = $_GET['user'];
        $pass = $_GET['pass'];
        $sql = "SELECT * FROM users WHERE uName = '$user'";
        echo $sql;
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "B";
                echo ($row['pass'] == $pass);
                if($row['pass'] == $pass) {
                    $login = true;
                    $name = $row['fName'];
                    $uID = $row['UserID'];
                }
                else
                    $login = false;
            }
        }
    }
    
    if($login || !(is_null($_SESSION['uID']))) {
        echo "C";
        $_SESSION['uID'] = $uID;
        $_SESSION['user'] = $user;
        $_SESSION['name'] = $name;
        header('Location: index.php');
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.79.0">
    <title>Login · Image Repo</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    

    <!-- Bootstrap core CSS -->
<link href="http://mydrip.ca/shopify/assets/dist/css/bootstrap.min.css" rel="stylesheet">   

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="http://mydrip.ca/shopify/assets/custom/signin.css" rel="stylesheet">
  </head>
  <body class="text-center">
    
<main class="form-signin">
  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET">
    <img class="mb-4" src="https://hackwestern.com/static/media/hw-logo.83b60e38.png" alt="" width=50%>
    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
    <label for="inputUsername" class="visually-hidden">Username</label>
    <input type="text" name="user" id="inputUsername" class="form-control" placeholder="Username" required autofocus>
    <label for="inputPassword" class="visually-hidden">Password</label>
    <input type="password" name="pass" id="inputPassword" class="form-control" placeholder="Password" required>
    <input class="w-100 btn btn-lg btn-primary" type="submit" value="Submit" name=ÈSubmit>
    <p class="mt-5 mb-3 text-muted">Made with  <i class="fa fa-heart" style="color:red;"></i> by <a href="https://github.com/Waleed101" target="_blank">Waleed Sawan</a></p>
  </form>
</main>


    
  </body>
</html>
