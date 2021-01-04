<?php
   include('config.php'); // Including credentials
   session_start(); // Starting local session
   if($_GET['logout']) // If user wants to logout, destroy the session
       session_destroy();
   else { // If not, pull the session details
      $uID = $_SESSION['uID'];
      $name = $_SESSION['name'];
      $user = $_SESSION['user'];
   }
   
   if(isset($_POST['idsToDelete'])) { // If the user has submitted the ids to delete, process them
       // Error messaging 
       $error = false;
       $success = true;
       $errorMsg = "";
       $successMsg = "";
       $imagesToDelete = explode(",", $_POST['idsToDelete']); // Get array version of the IDs
       foreach($imagesToDelete as &$indivImageId) { // Cycle through them
            $sql = "SELECT UserID, privacy FROM image WHERE imageID='$indivImageId'"; // Retrieve image details
            $result = $conn->query($sql);
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    if($row['UserID'] != $uID) { // If user isn't owner of the picture, they can't remove it
                        $error = true;
                        $errorMsg = "Some of the pictures weren't removed as you weren't authorized to remove those pictures!";
                    } else { // If they are owner, don't quite delete it - but change the privacy settings
                        $updatePrivacySQL = "UPDATE image SET privacy = 2 WHERE imageID='$indivImageId'";
                        if($conn->query($updatePrivacySQL) === TRUE) {
                            $successMsg = "Successfully deleted the image(s)!";
                        }
                        else {
                            $success = false;
                            $errorMsg = "Uh oh! Faced an error while trying to delete the image. " . $conn->error;  
                        }
                    }
                }
            }
            else { // If user tries to fake the submission of invalid hashes
                $error = true;
                $success = false;
                $errorMsg = "None of those pictures were found, please ensure that you're not tampering :)!";
            }
       }
   }
   
   if(isset($_POST['idsToChangePrivacy'])) { // If the user has submitted the ids to change privacy, process them
       // Error messaging
       $error = false;
       $success = false;
       $errorMsg = "";
       $successMsg = "";
       $imagesToPrivate = explode(",", $_POST['idsToChangePrivacy']); // Get array version of the ids
       foreach($imagesToPrivate as &$indivImageId)  {
            $sql = "SELECT UserID, privacy FROM image WHERE imageID='$indivImageId'"; // Retrieve data
            $result = $conn->query($sql);
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    if($row['UserID'] != $uID) { // If they aren't the owner, they can't change the privacy
                        $error = true;
                        $errorMsg = "Some of the pictures weren't changed as you weren't authorized to change them pictures!";
                    } else { // If they are, change the privacy
                        $currentPrivacy = $row['privacy'];
                        $newPrivacy = ($currentPrivacy == 1) ? 0 : 1; // Invert privacy
                        $updatePrivacySQL = "UPDATE image SET privacy = $newPrivacy WHERE imageID='$indivImageId'";
                        echo $updatePrivacySQL;
                        if($conn->query($updatePrivacySQL) === TRUE) {
                            $successMsg = "Successfully changed the image privacy(s)!";
                        }
                        else {
                            $success = false;
                            $errorMsg = "Uh oh! Faced an error while trying to change the images. " . $conn->error;  
                        }
                    }
                }
            }
            else { // If user tries to fake the submission of invalid hashes
                $error = true;
                $success = false;
                $errorMsg = "None of those pictures were found, please ensure that you're not tampering :)!";
            }
       }
   }
   
   if(isset($_POST['login'])) { // If they've logged in, process the user name
       $user = $_POST['user'];
       $pass = $_POST['pass'];
       $sql = "SELECT * FROM users WHERE uName = '$user'";
       $result = $conn->query($sql);
       if($result->num_rows > 0) {
           while($row = $result->fetch_assoc()) {
               if($row['pass'] == $pass) { // Successful login
                   $success = true;
                   $error = false;
                   $successMsg = "Successfully logged in, welcome back!";
                   $login = true;
                   $name = $row['fName'];
                   $uID = $row['UserID'];
               }
               else { // Incorrect credentials
                   $success = false;
                   $error = true;
                   $errorMsg = "Uh oh, that password was incorrect!";
                   $login = false;
               }
           }
       }
   }
   
   if($login || !(is_null($_SESSION['uID']))) { // If they have just logged, set the session variables
       $_SESSION['uID'] = $uID;
       $_SESSION['user'] = $user;
       $_SESSION['name'] = $name;
   }
   
   if(isset($_POST['submit']) && isset($_SESSION['uID'])) { // If they just uploaded new images, process them
       // Count total files
       $countfiles = count($_FILES['file']['name']);
     $success = true;
     $error = false;
      
       // Looping all files
       for($i=0;$i<$countfiles;$i++) {
           $sql="SELECT UUID() `uuid`";
           $result=mysqli_query($conn,$sql);
           $data = mysqli_fetch_assoc($result);
           $ext = pathinfo($filename=$_FILES["file"]["name"][$i], PATHINFO_EXTENSION); // Get the extension
           
           $sql = "INSERT INTO image (imageID, userID)
           VALUES ('" . $data["uuid"] . "." . $ext . "', $uID)"; // Upload to the database
           
           if ($conn->query($sql) === TRUE) { // Error handelling
             $successMsg = "Successfully uploaded the image(s)!";
           } else {
             $success = false;
             $error = true;
             $errorMsg = "Error uploading the image(s), " . $conn->error;
           }
           move_uploaded_file( $_FILES['file']['tmp_name'][$i],'upload/'.  $data["uuid"] .  '.' . $ext); // Actually uploading the image, rename to random 128-bit hash
       }
   } 
   
   function humanTiming ($time) // Get appropriate timing with proper phrasing/grammer
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
      <meta name="author" content="">
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
                <?php if(!$error && $success) { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <?php echo $successMsg;?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } else if($error && !$success) {?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <?php echo $errorMsg;?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
               <center>
                  <div class="btn-group">
                     <button type="button" class="btn btn-sm btn-outline-danger" id="groupRemoval" disabled onclick="deleteSelected()">Remove Selected</button>
                     <button type="button" class="btn btn-sm btn-outline-primary" id="groupPrivacy" disabled onclick="privateSelected()">Change Privacy Selected</button>
                  </div>
               </center>
               <br>
               <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                  <?php
                    // Retrieve all images that are either public or that the user owns
                     $sql = "SELECT * FROM image WHERE privacy = 0 OR UserID='$uID' ORDER BY timestamp DESC";
                     $result = $conn->query($sql);
                     $userOwner = false;
                      if($result->num_rows > 0) {
                          while($row = $result->fetch_assoc()) {
                              $imageHash = $row['imageID']; 
                              if($row['privacy'] == 2) // If they're deleted, skip them
                                continue;
                              if($row['UserID'] != $uID) { // If the user doesn't own them, get the name of the user that does
                                  $userOwner = false;
                                  $image_uID = $row['UserID'];
                                  $sql_newImage = "SELECT * FROM users WHERE UserID=$image_uID";
                                  $result_newImage = $conn->query($sql_newImage);
                                  if($result_newImage->num_rows > 0) {
                                      while($row_newImage = $result_newImage->fetch_assoc()) {
                                          $image_firstName = $row_newImage['fName'];
                                          $image_userName = $row_newImage['uName']; 
                                      }
                                  }
                              }
                              else { // If they do, thats good as well
                                  $userOwner = true;
                                  $image_firstName = $name;
                                  $image_userName = $user;
                              }
                     ?>
                  <div class="col" id="<?php echo $imageHash;?>">
                     <div class="card shadow-sm">
                        <img width="100%" src="/shopify/upload/<?php echo $imageHash;?>">
                        <div class="card-body">
                           <h6>
                              <i class="fa fa-user"></i> <?php echo $image_firstName;?> (<?php echo $image_userName;?>)
                              <?php if($row['privacy'] == 0) { ?>
                                <i class="fa fa-eye" alt="Public"></i> 
                              <?php } else if($row['privacy'] == 1) { ?>
                                <i class="fa fa-eye-slash"></i> 
                              <?php } else {?>
                                <i class="fa fa-trash"></i> 
                              <?php }?>
                           </h6>
                           <div class="d-flex justify-content-between align-items-center">
                              <div class="btn-group">
                                 <!--<button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>-->
                              </div>
                              <small class="text-muted" style="text-align:right"><em><br><?php echo ('Last Changed ' . humanTiming($row['timestamp']) . ' ago');?></em></small>
                           </div>
                           <br>
                           <?php if($userOwner) {?>
                           <div class="btn-group">
                              <input type="checkbox" class="form-check-input" id="<?php echo $imageHash;?>-check" style="margin-right:10px;" onclick="addToList('<?php echo $imageHash;?>')">
                              <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSelected('<?php echo $imageHash;?>')">Remove</button>
                              <button type="button" class="btn btn-sm btn-outline-primary" onclick="privateSelected('<?php echo $imageHash;?>')">Change Privacy</button>
                           </div>
                           <?php }?>
                        </div>
                     </div>
                  </div>
                  <?php }} ?>
               </div>
            </div>
         </div>
         <script>
             var commitAction = []; // Array to hold the selected keys
             function addToList(myID) { // Called whenever a checkbox changes
                if(document.getElementById(myID + "-check").checked) { // Add if its checked
                    commitAction.push(myID);
                } 
                else if(commitAction.includes(myID)) { // If its unchecked, remove from the array
                    const index = commitAction.indexOf(myID);
                    if(commitAction.length > 1) {
                        array.splice(index, 1);
                    } else {
                        commitAction = [];
                    }
                }
                
                if(commitAction.length > 0) { // Change the top buttons to undisabled, if anything is selected
                    document.getElementById("groupRemoval").disabled = false;
                    document.getElementById("groupPrivacy").disabled = false;
                } else { // Otherwise, disable them
                    document.getElementById("groupRemoval").disabled = true;
                    document.getElementById("groupPrivacy").disabled = true;
                }
             }
             
             function deleteSelected(indivID) { // Function to submit the deletion of images
                let strVersionOfAction = "";
                if(!(commitAction.length > 0) && indivID == undefined)
                    return;
                else if(indivID == undefined)
                    strVersionOfAction = commitAction.toString();
                else
                    strVersionOfAction = indivID;
                document.getElementById("holdDeleteIds").value = strVersionOfAction;
                document.getElementById("toDelete").submit();
             }
             
             function privateSelected(indivID) { // Function to change the privacy of images
                let strVersionOfAction = "";
                if(!(commitAction.length > 0) && indivID == undefined)
                    return;
                else if(indivID == undefined)
                    strVersionOfAction = commitAction.toString();
                else
                    strVersionOfAction = indivID;
                document.getElementById("holdPrivateIds").value = strVersionOfAction;
                document.getElementById("toPrivate").submit();
             }
         </script>
         <form id="toDelete" action="index.php" method="POST">
             <input type="text" hidden id="holdDeleteIds" name="idsToDelete">
         </form>
         <form id="toPrivate" action="index.php" method="POST">
             <input type="text" hidden id="holdPrivateIds" name="idsToChangePrivacy">
         </form>
         <!-- Modal -->
         <div class="modal fade" id="upload-modal" tabindex="-1" aria-labelledby="uploadTitle" aria-hidden="true">
            <div class="modal-dialog modal-md">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="uploa2dTitle">Upload</h5>
                  </div>
                  <div class="modal-body">
                     <form method="POST" action="" enctype='multipart/form-data'>
                        <center>
                           <em>
                              <h6>Uploading as <?php echo $_SESSION['name'] . " (" . $_SESSION['user'] . ")";?></h6>
                              <a href="index.php?logout=1">Click Here to Change That</a>
                           </em>
                        </center>
                        <br>
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
                        </div>
                        </br>
                        <div class="form-group">
                           <label for="exampleInputPassword1">Password</label>
                           <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="pass">
                        </div>
                        </br>
                        <div class="col-12">
                           <center><input type="submit" value="Login" name="login" class="btn btn-primary btn-block"></center>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </main>
      <footer class="text-muted py-5">
         <div class="container">
            <p>Made with <i class="fa fa-heart" style="color:red"></i> by Waleed Sawan.</p>   
            <p>Using Bootstrap publicly available CSS libraries and templates.</p> 
         </div>
         <script src="http://mydrip.ca/shopify/assets/dist/js/bootstrap.bundle.min.js"></script><script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
      </footer>
   </body>
</html>
