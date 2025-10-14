<?php
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Enable proper error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../assets/constants/config.php');
require_once('../assets/constants/check-login.php');
require_once('../assets/constants/fetch-my-info.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Secure file upload function
function validateAndUploadFile($file, $target_dir, $old_file = '') {
    $upload_errors = [];
    
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error: ' . $file['error']];
    }
    
    // File size validation (limit to 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File size exceeds 5MB limit'];
    }
    
    // Get file info
    $original_name = basename($file['name']);
    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $temp_file = $file['tmp_name'];
    
    // Allow-list filtering: Only permit specific, benign extensions
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.'];
    }
    
    // Deny-list filtering: Reject known dangerous file extensions
    $dangerous_extensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'pht', 'phps',
        'exe', 'bat', 'cmd', 'com', 'scr', 'vbs', 'js', 'jar',
        'pl', 'py', 'sh', 'asp', 'aspx', 'jsp', 'jspx'
    ];
    if (in_array($file_extension, $dangerous_extensions)) {
        return ['success' => false, 'error' => 'Dangerous file type detected'];
    }
    
    // Content validation: Verify MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $temp_file);
    finfo_close($finfo);
    
    $allowed_mime_types = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/gif'
    ];
    if (!in_array($mime_type, $allowed_mime_types)) {
        return ['success' => false, 'error' => 'Invalid file content. File does not match expected image format.'];
    }
    
    // Magic number validation to ensure file is what its extension claims to be
    $file_content = file_get_contents($temp_file, false, null, 0, 20);
    $magic_numbers = [
        'jpg' => ["\xFF\xD8\xFF"],
        'jpeg' => ["\xFF\xD8\xFF"],
        'png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'gif' => ["\x47\x49\x46\x38\x37\x61", "\x47\x49\x46\x38\x39\x61"]
    ];
    
    $valid_magic = false;
    if (isset($magic_numbers[$file_extension])) {
        foreach ($magic_numbers[$file_extension] as $magic) {
            if (strpos($file_content, $magic) === 0) {
                $valid_magic = true;
                break;
            }
        }
    }
    
    if (!$valid_magic) {
        return ['success' => false, 'error' => 'File content does not match the file extension'];
    }
    
    // Generate secure filename to prevent path traversal
    $secure_filename = uniqid('profile_', true) . '.' . $file_extension;
    
    // Ensure target directory is safe (prevent path traversal)
    $safe_target_dir = realpath($target_dir);
    if ($safe_target_dir === false || strpos($safe_target_dir, realpath('../assets/uploadImage/Profile/')) !== 0) {
        return ['success' => false, 'error' => 'Invalid upload directory'];
    }
    
    $target_file = $safe_target_dir . DIRECTORY_SEPARATOR . $secure_filename;
    
    // Move uploaded file
    if (move_uploaded_file($temp_file, $target_file)) {
        // Delete old file if it exists and is different
        if (!empty($old_file) && $old_file !== $secure_filename) {
            // Prevent path traversal by using basename and validating file location
            $old_file_basename = basename($old_file);
            $old_file_path = $safe_target_dir . DIRECTORY_SEPARATOR . $old_file_basename;
            if (
                file_exists($old_file_path) &&
                is_file($old_file_path) &&
                strpos(realpath($old_file_path), $safe_target_dir) === 0
            ) {
                @unlink($old_file_path);
            }
        }
        
        return ['success' => true, 'filename' => $secure_filename];
    } else {
        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }
}

?>

<?php 
// Fix SQL injection - use proper prepared statement with parameter binding
try {
    $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        die('Error: Admin profile not found');
    }
} catch (PDOException $e) {
    error_log('Database error in profile.php: ' . $e->getMessage());
    die('Error: Unable to load profile data');
}
?>
<?php
if(isset($_POST['update'])){
    try {
        // CSRF Protection
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception('Invalid CSRF token');
        }
        
        // Input validation and sanitization
        $required_fields = ['fname', 'lname', 'email', 'username', 'gender', 'contact', 'address'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Sanitize inputs
        $fname = filter_var(trim($_POST['fname']), FILTER_SANITIZE_STRING);
        $lname = filter_var(trim($_POST['lname']), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        $gender = filter_var(trim($_POST['gender']), FILTER_SANITIZE_STRING);
        $contact = filter_var(trim($_POST['contact']), FILTER_SANITIZE_STRING);
        $address = filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        // Validate names (only letters and spaces)
        if (!preg_match('/^[a-zA-Z\s]+$/', $fname)) {
            throw new Exception('First name should contain only letters and spaces');
        }
        if (!preg_match('/^[a-zA-Z\s]+$/', $lname)) {
            throw new Exception('Last name should contain only letters and spaces');
        }
        
        // Validate username (alphanumeric and underscore only)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new Exception('Username should contain only letters, numbers, and underscores');
        }
        
        // Validate gender
        $valid_genders = ['Male', 'Female'];
        if (!in_array($gender, $valid_genders)) {
            throw new Exception('Invalid gender selection');
        }
        
        // Validate contact number (10 digits)
        if (!preg_match('/^\d{10}$/', $contact)) {
            throw new Exception('Contact number should be exactly 10 digits');
        }
        
        // Validate address length
        if (strlen($address) < 10 || strlen($address) > 500) {
            throw new Exception('Address should be between 10 and 500 characters');
        }
        
        // Handle file upload securely
        $website_logo = $_POST['old_website_image']; // Default to old image
        
        if (!empty($_FILES["website_image"]["name"])) {
            $target_dir = "../assets/uploadImage/Profile/";
            $upload_result = validateAndUploadFile($_FILES["website_image"], $target_dir, $_POST['old_website_image']);
            
            if (!$upload_result['success']) {
                throw new Exception('File upload error: ' . $upload_result['error']);
            }
            
            $website_logo = $upload_result['filename'];
        }
        
        // Update database with proper prepared statement
        $sql = "UPDATE tbl_admin SET fname=?, lname=?, email=?, username=?, gender=?, contact=?, address=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $execute = $stmt->execute([
            $fname, 
            $lname, 
            $email, 
            $username, 
            $gender, 
            $contact, 
            $address, 
            $website_logo, 
            $_SESSION['id']
        ]);
        
        if ($execute === TRUE) {
            $_SESSION['success'] = 'Profile Successfully Updated';
            // Refresh the result data
            $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE id = ?");
            $stmt->execute([$_SESSION['id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new Exception('Database update failed');
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        error_log('Profile update error: ' . $e->getMessage());
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error occurred';
        error_log('Database error in profile update: ' . $e->getMessage());
    }
}
?>



            <?php include('include/head.php');?>
            <link rel="stylesheet" href="operation/popup_style.css">

            <?php include('include/header.php');?>
                        <?php include('include/sidebar.php');?>
<div class="dashboard-wrapper">
            <div class="container-fluid  dashboard-content">
                <!-- ============================================================== -->
                <!-- pageheader -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Profile </h2>
                            <p class="pageheader-text">Profile</p>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                       <!--  <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Forms</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Form Validations</li>
                                  -->   </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader -->
                <!-- ============================================================== -->
             
                   <div class="row">
                        <!-- ============================================================== -->
                        <!-- validation form -->
                        <!-- ============================================================== -->
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Profile</h5>
                                <div class="card-body">
                                    <?php
                                    // Display success/error messages
                                    if (isset($_SESSION['success'])) {
                                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                                        echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8');
                                        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                                        echo '<span aria-hidden="true">&times;</span></button></div>';
                                        unset($_SESSION['success']);
                                    }
                                    
                                    if (isset($_SESSION['error'])) {
                                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                                        echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                                        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                                        echo '<span aria-hidden="true">&times;</span></button></div>';
                                        unset($_SESSION['error']);
                                    }
                                    ?>
                                   <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" id="add_brand">
                                       <!-- CSRF Protection -->
                                       <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                                                         <div class="row">
                                                    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-12">
                                          <div class="text-center">
                                              <img src="../assets/uploadImage/Profile/<?=$result['image']?>" alt="User Avatar" class="rounded-circle user-avatar-xxl">
                                              </div>
                                          </div>
                                          <div class="col-xl-10 col-lg-8 col-md-8 col-sm-8 col-12">
                                              <div class="user-avatar-info">
                                                  <div class="m-b-20">
                                                      <div class="user-avatar-name">
                                                          <h2 class="mb-1"><?= htmlspecialchars($result['fname'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;<?= htmlspecialchars($result['lname'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                                      </div>
                                                       </div>
                                                  <!--  <div class="float-right"><a href="#" class="user-avatar-email text-secondary">www.henrybarbara.com</a></div> -->
                                                  <div class="user-avatar-address">
                                                      <div class="mt-3">
                                                        <!-- <image class="profile-img" src="../assets/uploadImage/Profile/<?=$result['image']?>" style="height:35%;width:25%;">
                          <input type="hidden" value="<?= htmlspecialchars($result['image'], ENT_QUOTES, 'UTF-8'); ?>" name="old_website_image">
                          <input type="file" class="form-control" name="website_image" accept="image/jpeg,image/jpg,image/png,image/gif" >

                                                              </div>
                                                  </div>
                                              </div>
                                        <br/>
                                           <div class="form-row">
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                                <label for="validationCustom03">First Name<span class="text-danger">*</span></label>
                                                 <input type="text" class="form-control " name="fname" value="<?= htmlspecialchars($result['fname'], ENT_QUOTES, 'UTF-8'); ?>" required pattern="^[a-z A-Z]+$">
                                               <div class="invalid-feedback">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                                <label for="validationCustom04">Last Name<span class="text-danger">*</span></label>
                                                 <input type="text" class="form-control" name="lname" value="<?= htmlspecialchars($result['lname'], ENT_QUOTES, 'UTF-8'); ?>" required pattern="^[a-z A-Z]+$">
                                                <div class="invalid-feedback">
                                                </div>
                                            </div>
                                            


                                           
                                             <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                               <label for="validationCustom02">Email<span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($result['email'], ENT_QUOTES, 'UTF-8'); ?>" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
                                                 <div class="valid-feedback">
                                                </div>
                                            </div>
                                            
                                             <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                               <label for="validationCustom01">Gender<span class="text-danger">*</span></label>
                                                <select type="text" class="form-control" placeholder="" name="gender"  required="">
                                                     <option value="Male" <?= ($result['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                                      <option value="Female" <?= ($result['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                                       </select>
                                                  <div class="valid-feedback">
                                                </div>
                                            </div>
                                             <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                               <label for="validationCustom02">Mob No<span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control" name="contact" value="<?= htmlspecialchars($result['contact'], ENT_QUOTES, 'UTF-8'); ?>" required pattern="^\d{10}$" maxlength="10">
                                                <div class="valid-feedback">
                                                </div>
                                            </div>
                                            
                                            
                                             <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                               <label for="validationCustomUsername">Username<span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                    </div>
                                                    <input type="text" class="form-control " name="username" value="<?= htmlspecialchars($result['username'], ENT_QUOTES, 'UTF-8'); ?>" required pattern="^[a-zA-Z0-9_]+$">
                                                 <div class="invalid-feedback">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                                <label for="validationCustom02">Address<span class="text-danger">*</span></label>
                                                <textarea type="text" class="form-control " name="address" required maxlength="500"><?= htmlspecialchars($result['address'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                     <div class="valid-feedback">
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                                <!-- <label for="validationCustom02">Upload Profile</label>
                                                <image class="profile-img" src="../assets/uploadImage/Profile/<?=$result['image']?>" style="height:35%;width:25%;">
                  <input type="hidden" value="<?=$result['image']?>" name="old_website_image">
                          <input type="file" class="form-control" name="website_image" accept="image/jpeg/png" > -->
                                 <div class="valid-feedback" >
                                                </div>
                                            </div>
                                            

                                        </div>
                                        <br>
                                      </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                                                             <center>

                                                <button class="btn btn-primary" type="submit" name="update" onclick="addBrand()">Update </button>
                                              </center>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- ============================================================== -->
                        <!-- end validation form -->
                        <!-- ============================================================== -->
                    </div>
                    
        

        <!-- ============================================================== -->
        <!-- end navbar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- end left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
                       </div>

           <?php include('include/footer.php');?>
         </div>
        <!-- ============================================================== -->
        <!-- end wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper  -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <!-- jquery 3.3.1 -->
    <script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <!-- bootstap bundle js -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <!-- slimscroll js -->
    <script src="assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <!-- main js -->
    <script src="assets/libs/js/main-js.js"></script>
<script src="assets/libs/js/jquery.js"></script>
    <!-- chart chartist js -->
    <script src="assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
    <!-- sparkline js -->
    <script src="assets/vendor/charts/sparkline/jquery.sparkline.js"></script>
    <!-- morris js -->
    <script src="assets/vendor/charts/morris-bundle/raphael.min.js"></script>
    <script src="assets/vendor/charts/morris-bundle/morris.js"></script>
    <!-- chart c3 js -->
    <script src="assets/vendor/charts/c3charts/c3.min.js"></script>
    <script src="assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="assets/vendor/charts/c3charts/C3chartjs.js"></script>
    <script src="assets/libs/js/dashboard-ecommerce.js"></script>


 <style>.error {
    color: red !important;
    
}</style>
<script src="../assets/js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js" integrity="sha512-WMEKGZ7L5LWgaPeJtw9MBM4i5w5OSBlSjTjCtSnvFJGSVD26gE5+Td12qN5pvWXhuWaWcVwF++F7aqu9cvqP0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- ... (your existing HTML code) ... -->

<script>
   function addBrand(){
     jQuery.validator.addMethod("alphanumeric", function (value, element) {
        // Check if the value is empty
        if (value.trim() === "") {
            return false;
        }
        // Check if the value contains at least one alphabet character
        if (!/[a-zA-Z]/.test(value)) {
            return false;
        }
        // Check if the value contains only alphanumeric characters, spaces, and allowed special characters
        return /^[a-zA-Z0-9\s!@#$%^&*()_-]+$/.test(value);
    }, "Please enter alphanumeric characters with at least one alphabet character.");
     
      jQuery.validator.addMethod("lettersonly", function(value, element) {
   // Check if the value is empty
   if (value.trim() === "") {
    return false;
   }
   return /^[a-zA-Z\s]*$/.test(value);
   }, "Please enter alphabet characters only");

      jQuery.validator.addMethod("noSpacesOnly", function (value, element) {
    // Check if the input contains only spaces
    return value.trim() !== '';
}, "Please enter a non-empty value");

   $('#add_brand').validate({
    rules: {
        fname: {
        required: true
     
         },
         lname: {
        required: true,
        alphanumeric: true,
        noSpacesOnly: true
         },
         email: {
        required: true,
        noSpacesOnly: true   
        
       
         },
         contact: {
          required: true,
          digits: true,
          minlength: 10,
          maxlength: 10
        },
      username: {
        required: true
      },
      contact: {
        required: true
      },
      address: {
        required: true
      }
    },
    messages: {
      fname: {
        required: "Please enter a fname.",
        pattern: "Only alphanumeric characters are allowed."
      },
      lname: {
         required: "Please enter status."
      },
      email: {
         required: "Please enter status."
      },
      contact: {
         required: "Please enter contact."
      },
      username: {
         required: "Please enter username."
      },
      address: {
         required: "Please enter address."
      }
      

    },
   });
   };
</script>
</body>
 
</html>