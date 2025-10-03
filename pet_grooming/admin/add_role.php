<?php include('include/head.php');?>
<?php include('include/header.php');?>
<?php include('include/sidebar.php');?>
<?php
   error_reporting(0);
   require_once('../assets/constants/config.php');
   require_once('../assets/constants/check-login.php');
   require_once('../assets/constants/fetch-my-info.php');
   
   
   
   
   
   $stmt = $conn->prepare("SELECT * FROM tbl_permissions WHERE main='0'");
   $stmt->execute();
   $result = $stmt->fetchAll(); 
   ?>
<div class="dashboard-wrapper">
   <div class="container-fluid  dashboard-content">
      <!-- ============================================================== -->
      <!-- pageheader -->
      <!-- ============================================================== -->
      <!-- <div class="row">
         <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
             <div class="page-header">
                 <h2 class="pageheader-title">Candidate </h2>
                 <p class="pageheader-text">Candidate</p>
                 <div class="page-breadcrumb">
                     <nav aria-label="breadcrumb">
                         <ol class="breadcrumb">
                             <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                             <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Forms</a></li>
                             <li class="breadcrumb-item active" aria-current="page">Form Validations</li>
                         </ol>
                     </nav>
                 </div>
             </div>
         </div>
         </div>
         --><!-- ============================================================== -->
      <!-- end pageheader -->
      <!-- ============================================================== -->
      <div class="row">
         <!-- ============================================================== -->
         <!-- validation form -->
         <!-- ============================================================== -->
         <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
               <h5 class="card-header">Add Role</h5>
               <div class="card-body">
                  <form class="form-horizontal" action="operation/role.php" method="post" enctype="multipart/form-data" id="add_brand">
                     <div class="form-row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                           <label for="exampleInputEmail1">Name<span class="text-danger">*</span></label>
                           <input type="text" class="form-control" placeholder="Enter Name" name="assign_name" required autocomplete="off" >
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-2">
                           <label for="exampleInputEmail1">Description<span class="text-danger">*</span></label>
                           <input type="text" class="form-control" placeholder="Enter  Description" name="description" required autocomplete="off">
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                           <u>
                              <h3 style="margin-left: 3%;">Permissions</h3>
                           </u>
                           <h5 style="color:red;">( While selecting any sub roles like add,edit,delete you must require to select Main roles named with Manage Name. )</h5>
                           <br><br>  
                        </div>
                     </div>
                     <!-- sidebar content -->
                     <div class="row">
                        <?php 
                           foreach ($result as $row) {
                            $id = $row["id"]; 
                           ?>
                        <div class="checkbox col-md-12 mt-4">
                           <input type="checkbox" id="checkItem" name="checkItem[]" value="<?php echo $id; ?>"> <span class="font-weight-bold text-dark h6"><?php echo $row["display_name"]; ?></span>
                        </div>
                        <?php    $stmt1 = $conn->prepare("SELECT * FROM tbl_permissions WHERE main=? ");
                           $stmt1->execute([$id]);
                           $result1 = $stmt1->fetchAll(); 
                           foreach ($result1 as $row1) {
                           ?>
                        <div class="checkbox col-md-3">
                           <input type="checkbox" id="checkItem" name="checkItem[]" value="<?php echo $row1['id']; ?>"> <?php echo $row1["display_name"]; ?>
                        </div>
                        <?php } ?>
                        <br>
                        <?php } ?>
                        <br>
                     </div>
                     <br>
                     <center>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                           <button class="btn btn-primary" type="submit" name="btn_save" onclick="addBrand()">Submit</button>
                        </div>
                     </center>
               </div>
               </form>
            </div>
         </div>
         <!-- ============================================================== -->
         <!-- end validation form -->
         <!-- ============================================================== -->
      </div>
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
<script type="text/javascript">1
   $('#class_id').change(function(){
      $("#subject_id").val('');
      $("#subject_id").children('option').hide();
      var class_id=$(this).val();
      $("#subject_id").children("option[data-id="+class_id+ "]").show();
      
    });
</script>
</body>
<style>.error {
   color: red !important;
   }
</style>
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
   
   $.validator.addMethod("noDigits", function(value, element) {
    return this.optional(element) || !/\d/.test(value);
   }, "Please enter a description without digits.");
   
   
   
      jQuery.validator.addMethod("noSpacesOnly", function (value, element) {
    // Check if the input contains only spaces
    return value.trim() !== '';
   }, "Please enter a non-empty value");
   
   $('#add_brand').validate({
    rules: {
        assign_name: {
        required: true,
        noSpacesOnly: true,
        alphanumeric: true,
        noDigits: true 
       
         },
      description: {
        required: true,
        noSpacesOnly: true,
        noDigits: true ,
        alphanumeric: true
      }
   
    },
    messages: {
      assign_name: {
        required: "Please enter a assign_name.",
        pattern: "Only alphanumeric characters are allowed.",
        alphanumeric: "Only alphanumeric characters are allowed."
      },
      description: {
         required: "Please enter description.",
         noDigits: "Description should not contain digits",
         alphanumeric: "Only alphanumeric characters are allowed."
      }
    },
   });
   };
</script>
</body>
</html>