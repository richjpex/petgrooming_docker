<?php
//error_reporting(0);
require_once('../assets/constants/config.php');
require_once('../assets/constants/check-login.php');
require_once('../assets/constants/fetch-my-info.php');

?>

<?php



// Validate session id as integer
$admin_id = isset($_SESSION['id']) && is_numeric($_SESSION['id']) ? intval($_SESSION['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE id = :id");
$stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<?php
if (isset($_POST['update'])) {
    $target_dir = "../assets/uploadImage/Profile/";
    $website_logo = $_POST['old_website_image'];
    $allowed_types = ['image/jpeg', 'image/png'];
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $deny_extensions = ['php', 'phtml', 'exe', 'js', 'html', 'htm', 'sh', 'bat', 'pl', 'py', 'asp', 'aspx', 'cgi'];
    $max_file_size = 2 * 1024 * 1024; // 2MB

    // Validate and sanitize inputs
    $fname = isset($_POST['fname']) ? trim($_POST['fname']) : '';
    $lname = isset($_POST['lname']) ? trim($_POST['lname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';

    // Basic input validation
    if (!preg_match('/^[a-zA-Z ]+$/', $fname)) {
        echo "<script>alert('Invalid first name.');window.history.back();</script>";
        exit;
    }
    if (!preg_match('/^[a-zA-Z ]+$/', $lname)) {
        echo "<script>alert('Invalid last name.');window.history.back();</script>";
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address.');window.history.back();</script>";
        exit;
    }
    // Accepts numbers with optional leading +, blocks symbols for XSS
    if (!preg_match('/^\+?[0-9]{10,15}$/', $contact)) {
        echo "<script>alert('Invalid contact number.');window.history.back();</script>";
        exit;
    }
    if (!in_array($gender, ['Male', 'Female'])) {
        echo "<script>alert('Invalid gender.');window.history.back();</script>";
        exit;
    }
    // Username: block script tags and dangerous symbols
    if (preg_match('/<script|>|"|\'|`|\\|\{|\}|\[|\]|\(|\)/i', $username)) {
        echo "<script>alert('Invalid username.');window.history.back();</script>";
        exit;
    }
    // Address: block script tags and dangerous symbols
    if (preg_match('/<script|>|"|\'|`|\\|\{|\}|\[|\]|\(|\)/i', $address)) {
        echo "<script>alert('Invalid address.');window.history.back();</script>";
        exit;
    }

    // File upload validation
    if (isset($_FILES["website_image"]) && $_FILES["website_image"]["tmp_name"] != '') {
        $file_name = $_FILES["website_image"]["name"];
        $file_tmp = $_FILES["website_image"]["tmp_name"];
        $file_size = $_FILES["website_image"]["size"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_type = mime_content_type($file_tmp);

        // Deny-list filtering
        if (in_array($file_ext, $deny_extensions)) {
            echo "<script>alert('Dangerous file type not allowed.');window.history.back();</script>";
            exit;
        }
        // Allow-list filtering
        if (!in_array($file_ext, $allowed_extensions)) {
            echo "<script>alert('Only JPG and PNG files are allowed.');window.history.back();</script>";
            exit;
        }
        // MIME type check
        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Invalid file type. Only JPG and PNG files are allowed.');window.history.back();</script>";
            exit;
        }
        // Magic number validation
        $magic_valid = false;
        $fh = fopen($file_tmp, 'rb');
        $bytes = fread($fh, 8);
        fclose($fh);
        // JPEG: FF D8 FF
        if ($file_ext === 'jpg' || $file_ext === 'jpeg') {
            if (substr($bytes, 0, 3) === "\xFF\xD8\xFF") $magic_valid = true;
        }
        // PNG: 89 50 4E 47 0D 0A 1A 0A
        elseif ($file_ext === 'png') {
            if ($bytes === "\x89PNG\x0D\x0A\x1A\x0A") $magic_valid = true;
        }
        if (!$magic_valid) {
            echo "<script>alert('File content does not match its extension.');window.history.back();</script>";
            exit;
        }
        if ($file_size > $max_file_size) {
            echo "<script>alert('File size exceeds 2MB limit.');window.history.back();</script>";
            exit;
        }
        $new_filename = uniqid('profile_', true) . '.' . $file_ext;
        $image = $target_dir . $new_filename;
        if (move_uploaded_file($file_tmp, $image)) {
            // Remove old image if exists and not default
            if (!empty($_POST['old_website_image']) && $_POST['old_website_image'] != $new_filename) {
                // Sanitize filename to prevent path traversal
                $old_image_filename = basename($_POST['old_website_image']);
                // Only allow valid image extensions
                $valid_exts = ['jpg', 'jpeg', 'png'];
                $old_ext = strtolower(pathinfo($old_image_filename, PATHINFO_EXTENSION));
                if (in_array($old_ext, $valid_exts)) {
                    $old_image_path = $target_dir . $old_image_filename;
                    if (file_exists($old_image_path)) {
                        @unlink($old_image_path);
                    }
                }
            }
            $website_logo = $new_filename;
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');window.history.back();</script>";
            exit;
        }
    }

    $sql = "UPDATE tbl_admin SET fname=:fname, lname=:lname, email=:email, username=:username, gender=:gender, dob=:dob, contact=:contact, address=:address, image=:website_logo WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':dob', $dob);
    $stmt->bindParam(':contact', $contact);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':website_logo', $website_logo);
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);

    $execute = $stmt->execute();
    if ($execute === TRUE) {
        $_SESSION['success'] = 'Record Successfully Updated';
?>
        <?php

        } else {

            //$_SESSION['error']='Something Went Wrong';
        }
    }
            ?>



<?php include('include/head.php'); ?>
<link rel="stylesheet" href="operation/popup_style.css">

<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>
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
                                  -->
                            </ol>
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
                        <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" id="add_brand">
                            <div class="row">
                                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="text-center">
                                        <img src="../assets/uploadImage/Profile/<?= $result['image'] ?>" alt="User Avatar" class="rounded-circle user-avatar-xxl">
                                    </div>
                                </div>
                                <div class="col-xl-10 col-lg-8 col-md-8 col-sm-8 col-12">
                                    <div class="user-avatar-info">
                                        <div class="m-b-20">
                                            <div class="user-avatar-name">
                                                <h2 class="mb-1"><?= $result['fname']; ?>&nbsp;<?= $result['lname']; ?></h2>
                                            </div>
                                        </div>
                                        <!--  <div class="float-right"><a href="#" class="user-avatar-email text-secondary">www.henrybarbara.com</a></div> -->
                                        <div class="user-avatar-address">
                                            <div class="mt-3">
                                                <!-- <image class="profile-img" src="../assets/uploadImage/Profile/<?= $result['image'] ?>" style="height:35%;width:25%;">
                 --> <input type="hidden" value="<?= $result['image'] ?>" name="old_website_image">
                                                <input type="file" class="form-control" name="website_image" accept="image/jpeg/png">

                                            </div>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="form-row">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                            <label for="validationCustom03">First Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control " name="fname" value="<?= $result['fname'] ?>" required pattern="^[a-z A-Z]+$">
                                            <div class="invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                            <label for="validationCustom04">Last Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="lname" value="<?= $result['lname'] ?>" required pattern="^[a-z A-Z]+$">
                                            <div class="invalid-feedback">
                                            </div>
                                        </div>




                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                            <label for="validationCustom02">Email<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="email" value="<?= $result['email'] ?>" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
                                            <div class="valid-feedback">
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                            <label for="validationCustom01">Gender<span class="text-danger">*</span></label>
                                            <select type="text" class="form-control" placeholder="" name="gender" required="" value="<?= $result['dob'] ?>">

                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                            <div class="valid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                            <label for="validationCustom02">Mob No<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="contact" value="<?= $result['contact'] ?>" required>
                                            <div class="valid-feedback">
                                            </div>
                                        </div>


                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                                            <label for="validationCustomUsername">Username<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                </div>
                                                <input type="text" class="form-control " name="username" value="<?= $result['username'] ?>" required>
                                                <div class="invalid-feedback">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                            <label for="validationCustom02">Address<span class="text-danger">*</span></label>
                                            <textarea type="text" class="form-control " name="address" required><?= $result['address'] ?>
                                                      </textarea>
                                            <div class="valid-feedback">
                                            </div>
                                        </div>
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                            <!-- <label for="validationCustom02">Upload Profile</label>
                                                <image class="profile-img" src="../assets/uploadImage/Profile/<?= $result['image'] ?>" style="height:35%;width:25%;">
                  <input type="hidden" value="<?= $result['image'] ?>" name="old_website_image">
                          <input type="file" class="form-control" name="website_image" accept="image/jpeg/png" > -->
                                            <div class="valid-feedback">
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

    <?php include('include/footer.php'); ?>
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


<style>
    .error {
        color: red !important;

    }
</style>
<script src="../assets/js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js" integrity="sha512-WMEKGZ7L5LWgaPeJtw9MBM4i5w5OSBlSjTjCtSnvFJGSVD26gE5+Td12qN5pvWXhuWaWcVwF++F7aqu9cvqP0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- ... (your existing HTML code) ... -->

<script>
    function addBrand() {
        jQuery.validator.addMethod("alphanumeric", function(value, element) {
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

        jQuery.validator.addMethod("noSpacesOnly", function(value, element) {
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