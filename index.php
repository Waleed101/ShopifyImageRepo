<?php
    include('config.php');
    session_start();
    if($_GET['logout'])
        session_destroy();
    else {
       $uID = $_SESSION['uID'];
       $name = $_SESSION['name'];
       $user = $_SESSION['user'];
    }
//   var_dump($uID);

   if(isset($_POST['login'])) {
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $sql = "SELECT * FROM users WHERE uName = '$user'";
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
        $_SESSION['uID'] = $uID;
        $_SESSION['user'] = $user;
        $_SESSION['name'] = $name;
    }
    
    if(isset($_POST['submit']) && isset($_SESSION['uID'])) {
        // Count total files
        $countfiles = count($_FILES['file']['name']);
       
        // Looping all files
        for($i=0;$i<$countfiles;$i++) {
            $sql="SELECT UUID() `uuid`";
            $result=mysqli_query($conn,$sql);
            $data = mysqli_fetch_assoc($result);
            $ext = pathinfo($filename=$_FILES["file"]["name"][$i], PATHINFO_EXTENSION);
            
            $sql = "INSERT INTO image (imageID, userID)
            VALUES ('" . $data["uuid"] . $ext . "', $uID)";
            
            if ($conn->query($sql) === TRUE) {
              echo "New record created successfully";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }
            
            move_uploaded_file( $_FILES['file']['tmp_name'][$i],'upload/'.  $data["uuid"] . $ext);
            // echo $data["uuid"] . "." . $filenameExtension;
        }
   } 
   
//   echo $_SESSION['uID'];
//   echo $_SESSION['user'];
//   echo $_SESSION['name'];
   
   function humanTiming ($time)
    {
        
        $time = time() - strtotime($time); // to get the time since that moment
        $time = ($time<1)? 1 : $time;
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
    
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    
    }

?>
<!doctype html>
<html lang="en">
   <head>
      <meta charset="gb18030">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
      <meta name="generator" content="Hugo 0.79.0">
      <title>Shopify Images - Waleed Sawan</title>
      <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/album/">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <!-- Bootstrap core CSS -->
      <link href="http://mydrip.ca/shopify/assets/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
   </head>
   <body>
      <header>
         <div class="collapse bg-dark" id="navbarHeader">
            <div class="container">
               <div class="row">
                  <div class="col-sm-8 col-md-7 py-4">
                     <h4 class="text-white">About</h4>
                     <p class="text-muted">Add some information about the album below, the author, or any other background context. Make it a few sentences long so folks can pick up some informative tidbits. Then, link them off to some social networking sites or contact information.</p>
                  </div>
                  <div class="col-sm-4 offset-md-1 py-4">
                     <h4 class="text-white">Contact</h4>
                     <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Follow on Twitter</a></li>
                        <li><a href="#" class="text-white">Like on Facebook</a></li>
                        <li><a href="#" class="text-white">Email me</a></li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <div class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container">
               <a href="#" class="navbar-brand d-flex align-items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" aria-hidden="true" class="me-2" viewBox="0 0 24 24">
                     <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                     <circle cx="12" cy="13" r="4"/>
                  </svg>
                  <strong>Shopify Img Repo</strong>
               </a>
               <!--<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">-->
               <!--  <span class="navbar-toggler-icon"></span>-->
               <!--</button>-->
               <?php
                    if(!isset($_SESSION['uID'])) {
                ?>
                     <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#login-modal">
                        <i class="fa fa-user"></i> Login
                     </button>
                <?php }
                    else {
                        ?>
                        
                     <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload-modal">
                        <i class="fa fa-upload"></i> Upload
                     </button>
                        
                        <?php
                    } ?>
            </div>
         </div>
      </header>
      <main>
         <section class="py-5 text-center container">
            <div class="row py-lg-5">
               <div class="col-lg-6 col-md-8 mx-auto">
                  <h1 class="fw-light">Welcome to the Shopify Img Repo!</h1>
                  <p class="lead text-muted">This is Waleed's take on the Shopify Developer challenge, where we were tasked with creating an Image repository for the listing and sale of users images.</p>
                  <p>
                     <!-- Button trigger modal -->
                     <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#login-modal">-->
                     <!--<i class="fa fa-upload"></i> Click Here to Upload Your Own-->
                     <!--</button>-->
                     <!--<a href="#" class="btn btn-secondary my-2">Secondary action</a>-->
                  </p>
               </div>
            </div>
         </section>
         <div class="album py-5 bg-light">
            <div class="container">
                <center><div class="btn-group">
                  <button type="button" class="btn btn-sm btn-outline-danger">Remove</button>
                  <button type="button" class="btn btn-sm btn-outline-primary">Change Privacy</button>
                </div></center><br>
               <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                  <?php
                   $sql = "SELECT * FROM image WHERE privacy = 0 OR UserID='$uID'";
                   $result = $conn->query($sql);
                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            if($row['UserID'] != $uID) {
                                echo "HELLO";
                                $image_uID = $row['UserID'];
                                $sql_newImage = "SELECT * FROM users WHERE UserID='$image_uID";
                                $result_newImage = $conn->query($sql_newImage);
                                if($result_newImage->num_rows > 0) {
                                    while($row_newImage = $result_newImage->fetch_assoc()) {
                                        $image_firstName = $row_newImage['fName'];
                                        $image_userName = $row_newImage['uName']; 
                                    }
                                }
                            }
                            else {
                                $image_firstName = $name;
                                $image_userName = $user;
                            }
                  ?>
                  <div class="col">
                     <div class="card shadow-sm">
                        <img width="100%" src="/shopify/upload/<?php echo $row['imageID'];?>">
                        <div class="card-body">
                           <h6>
                              <i class="fa fa-user"></i> <?php echo $image_firstName;?> (<?php echo $image_userName;?>)
                           </h6>
                           <div class="d-flex justify-content-between align-items-center">
                              <div class="btn-group">
                                 <!--<button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>-->
                              </div>
                              <small class="text-muted" style="text-align:right"><em><br><?php echo ('Posted ' . humanTiming($row['timestamp']) . ' ago');?></em></small>
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php }} ?>
               </div>
            </div>
         </div>
         <!-- Modal -->
         <div class="modal fade" id="upload-modal" tabindex="-1" aria-labelledby="uploadTitle" aria-hidden="true">
            <div class="modal-dialog modal-md">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="uploa2dTitle">Upload</h5>
                  </div>
                  <div class="modal-body">
                     <form method="POST" action="" enctype='multipart/form-data'>
                         <center><em>
                             <h6>Uploading as <?php echo $_SESSION['name'] . " (" . $_SESSION['user'] . ")";?></h6>
                             <a href="index.php?logout=1">Click Here to Change That</a>
                         </em></center><br>
                        <div class="custom-file mb-3">
                           <center>
                               <input type="file" class="custom-file-input" id="customFile" name="file[]" multiple>
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </center>
                        </div>
                  </div>
                  <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                  <input type="submit" name="submit" value="Upload" class="btn btn-success">
                  </form>
                  </div>
               </div>
            </div>
         </div>
         <!-- Login Modal -->
         <div class="modal fade" id="login-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                  </div>
                  <div class="modal-body">
                    <form method="POST" action="index.php">
                        <div class="alert alert-primary" role="alert">
                          <center>Please check the README for the default username/password.</center>
                        </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Username:</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter username" name="user">
                      </div></br>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="pass">
                      </div></br>
                      <div class="col-12">
                      <center><input type="submit" value="Login" name="login" class="btn btn-primary btn-block"></center></div>
                    </form>
                  </div>
               </div>
            </div>
         </div>
      </main>
      <footer class="text-muted py-5">
         <div class="container">
            <p class="float-end mb-1">
               <a href="#">Back to top</a>
            </p>
            <p class="mb-1">Album example is &copy; Bootstrap, but please download and customize it for yourself!</p>
            <p class="mb-0">New to Bootstrap? <a href="/">Visit the homepage</a> or read our <a href="../getting-started/introduction/">getting started guide</a>.</p>
         </div>
         <script src="http://mydrip.ca/shopify/assets/dist/js/bootstrap.bundle.min.js"></script><script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
      </footer>
   </body>
</html>