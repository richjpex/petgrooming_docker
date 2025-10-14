<?php
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Enable proper error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../assets/constants/config.php');
require_once('../assets/constants/check-login.php');
require_once('../assets/constants/fetch-my-info.php');

// Input validation for POST data
if (!isset($_POST['id']) || empty($_POST['id'])) {
    die('Invalid request: Missing ID parameter');
}

if (!filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    die('Invalid request: ID must be a valid integer');
}

$invoice_id = (int)$_POST['id'];
?>

<?php
try {
    // Fix SQL injection - use proper prepared statement
    $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        throw new Exception('Admin profile not found');
    }
} catch (PDOException $e) {
    error_log('Database error in print.php admin query: ' . $e->getMessage());
    die('Error: Unable to load admin data');
}
?>

<?php
try {
    // Fix SQL injection - use proper prepared statement
    $sql = "SELECT * FROM tbl_invoice WHERE id = ?";
    $statement = $conn->prepare($sql);
    $statement->execute([$invoice_id]);
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);
    
    if (!$invoice) {
        throw new Exception('Invoice not found');
    }

    $sql = "SELECT * FROM tbl_manage_website";
    $statement = $conn->prepare($sql);
    $statement->execute();
    $web = $statement->fetch(PDO::FETCH_ASSOC);

    // Validate customer_id exists and is numeric
    if (empty($invoice['customer_id']) || !is_numeric($invoice['customer_id'])) {
        throw new Exception('Invalid customer ID in invoice');
    }

    $sqlq = "SELECT * FROM tbl_customer WHERE cust_id = ?";
    $stat = $conn->prepare($sqlq);
    $stat->execute([$invoice['customer_id']]);
    $cust = $stat->fetch();
    
    if (!$cust) {
        throw new Exception('Customer not found');
    }
    
} catch (PDOException $e) {
    error_log('Database error in print.php: ' . $e->getMessage());
    die('Error: Unable to load invoice data');
} catch (Exception $e) {
    error_log('Error in print.php: ' . $e->getMessage());
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>


<?php include('include/head.php'); ?>

<?php include('include/header.php'); ?>

<!-- ============================================================== -->
<!-- end left sidebar -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- wrapper  -->
<!-- ============================================================== -->
<div class="">
    <div class="dashboard-ecommerce">
        <div class="container-fluid dashboard-content" style="background-color: #ffffff;">
            <!-- ============================================================== -->
            <!-- pageheader  -->
            <!-- ============================================================== -->
            <!--<div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header">
                                <h2 class="pageheader-title"> Product Invoice </h2>
                                <p class="pageheader-text">Nulla euismod urna eros, sit amet scelerisque torton lectus vel mauris facilisis faucibus at enim quis massa lobortis rutrum.</p>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">E-coommerce</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">E-Commerce Product Invoice</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    --><!-- ============================================================== -->
            <!-- end pageheader  -->
            <!-- ============================================================== -->
            <div class="row">
                <div class="offset-xl-2 col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header p-4">
                            <div class="float-left">
                                <img src="../assets/uploadImage/Logo/<?= htmlspecialchars($web['logo'], ENT_QUOTES, 'UTF-8'); ?>" class="img-responsive" style="width: 300px;" alt="Company Logo">
                            </div>
                            <div class="mr-5 text-center">
                                <h3><?= htmlspecialchars($web['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            </div>
                            <div class="float-right">
                                <h3 class="mb-0">Invoice #<?= htmlspecialchars($invoice['inv_no'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                Date:<?= htmlspecialchars(date("d-m-Y", strtotime($invoice['build_date'])), ENT_QUOTES, 'UTF-8'); ?>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <h5 class="mb-3">From:</h5>
                                   
                                    <h2 class="text-dark mb-1"><?= htmlspecialchars($result['fname'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;<?= htmlspecialchars($result['lname'], ENT_QUOTES, 'UTF-8'); ?></h2>

                                    <div><?= htmlspecialchars($result['address'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>Email: <?= htmlspecialchars($result['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>Phone: <?= htmlspecialchars($result['contact'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>GSTIN: <?= htmlspecialchars($result['gstin'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <h5 class="mb-3">To:</h5>
                                    <h3 class="text-dark mb-1"><?= htmlspecialchars($cust['cust_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <div><?= htmlspecialchars($cust['cust_address'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>Email: <?= htmlspecialchars($cust['cust_email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>Phone: <?= htmlspecialchars($cust['cust_mob'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div>GSTIN: <?= htmlspecialchars($invoice['gstin'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                            </div>
<?php
try {
    // Fix SQL injection - use proper prepared statement with parameter binding
    $sql2 = "SELECT * FROM tbl_quot_inv_items WHERE inv_id = ?";
    $statement2 = $conn->prepare($sql2);
    $statement2->execute([$invoice_id]); // Use the validated invoice_id instead of $_POST['id']
    $res2 = $statement2->fetchAll();
    
    if(count($res2) > 0){
} catch (PDOException $e) {
    error_log('Database error in print.php invoice items query: ' . $e->getMessage());
    die('Error: Unable to load invoice items');
}
?>
                            <label>Product</label>
                            <div class="table-responsive-sm">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="center">#</th>
                                            <th>Product/Service</th>
                                            <th>Description</th>
                                            <!--<th class="right">Unit</th>-->
                                            <th class="center">Qty</th>
                                            <th class="right">Rate</th>

 <th class="right">Tax %</th>

                                            <th class="right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
$fstot=0;
$ftax=0;
                                         $cg1=0;
                                        $sg1=0;
                                        $ig1=0;
                                       
                                        foreach($res2 as $row2){
                                            try {
                                                // Validate product_id is numeric
                                                if (!is_numeric($row2['product_id'])) {
                                                    continue; // Skip invalid product IDs
                                                }

                                                // Fix SQL injection - use proper prepared statement with parameter binding
                                                $sql1 = "SELECT * FROM tbl_product WHERE id = ?";
                                                $statement1 = $conn->prepare($sql1);
                                                $statement1->execute([$row2['product_id']]);
                                                $row1 = $statement1->fetch(PDO::FETCH_ASSOC);
                                                
                                                if (!$row1) {
                                                    continue; // Skip if product not found
                                                }

                                                // Fix SQL injection in unit group query - validate unit ID is numeric
                                                $key3 = null;
                                                if (!empty($row1['unit']) && is_numeric($row1['unit'])) {
                                                    $stmt3 = $conn->prepare("SELECT * FROM tbl_unit_grp WHERE id = ? AND delete_status = '0'");
                                                    $stmt3->execute([$row1['unit']]);
                                                    $key3 = $stmt3->fetch();
                                                }

                                                $no += 1;
                                                
                                                $stot = $row2['quantity'] * $row1['unit_price'];
                                                $fstot += $stot;
                                        ?>
                                            <tr>
                                                <td class="center"><?= htmlspecialchars($no, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="left strong"><?php if($row1['exp']==0){ echo 'Product- ';}else if($row1['exp']==1){ echo 'Service- ';} ?><?= htmlspecialchars($row1['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="left"><?= htmlspecialchars($row1['details'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="center"><?= htmlspecialchars($row2['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="right">
    <?= htmlspecialchars($web['currency_symbol'] . " " . number_format($row1['unit_price'], 2), ENT_QUOTES, 'UTF-8'); ?>
</td>


    <td class="right">
    <?php 
    if (!empty($row1['gst'])) {
        $product = $row1['gst']; 
        $productArray = explode(',', $product);
        foreach($productArray as $pro_med){
            // Validate that pro_med is numeric before using it in query
            if (is_numeric(trim($pro_med))) {
                // Fix SQL injection - use proper prepared statement with parameter binding
                $stax = $conn->prepare("SELECT * FROM tbl_tax WHERE id = ? AND delete_status = '0'");
                $stax->execute([trim($pro_med)]);
                $tax = $stax->fetch();  
                
                if ($tax) {
                    $cgst = $tax['percentage'];
                    $cgst_amt = ($row2['quantity'] * $row1['unit_price'] * $cgst) / 100;
                    
                    echo htmlspecialchars($cgst, ENT_QUOTES, 'UTF-8');
                    echo '%('; 
                    echo htmlspecialchars($web['currency_symbol'], ENT_QUOTES, 'UTF-8'); 
                    echo htmlspecialchars(number_format($cgst_amt, 2), ENT_QUOTES, 'UTF-8'); 
                    echo ')';
                    echo '<br>';
                    $ftax += $cgst_amt;
                }
            }
        }
    }
    ?></td>
    
                                                <td class="right">
    <?= htmlspecialchars($web['currency_symbol'] . " " . number_format($row2['total'], 2), ENT_QUOTES, 'UTF-8'); ?>-/
</td>

                                            </tr>
                                        <?php 
                                            } catch (PDOException $e) {
                                                error_log('Database error in print.php product loop: ' . $e->getMessage());
                                                continue; // Skip this product and continue with the next one
                                            }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
<?php } ?>
                            <br>
                          

                            <div class="row align-items-center  ">
                                <div class="col-lg-4 col-sm-5">
                                    <table>
                                        <tr>
                                            <td class="left">
                                                <strong class="text-dark">Account Details </strong>
                                            </td>
                                            <td class="right">&nbsp;&nbsp;&nbsp; <?= htmlspecialchars($web['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong class="text-dark"> A/c No. </strong>
                                            </td>
                                            <td class="right">&nbsp;&nbsp;&nbsp; <?= htmlspecialchars($web['acc_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong class="text-dark"> IFSC</strong>
                                            </td>
                                            <td class="right">&nbsp;&nbsp;&nbsp; <?= htmlspecialchars($web['ifsc'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <strong class="text-dark"><?= htmlspecialchars($web['branch'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                            </td>
                                            <td class="right">&nbsp;&nbsp;&nbsp; <?= htmlspecialchars($web['badd'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    </table>

                                </div>
                                <div class="col-lg-4 col-sm-5 ml-auto">
                                    <table class="table table-clear">
                                        <tbody>

                                            <tr>
                                                <td class="left">
                                                    <strong class="text-dark">Subtotal</strong>
                                                </td>
                                               <td class="right">
    <?= htmlspecialchars($web['currency_symbol'] . " " . number_format($fstot, 2), ENT_QUOTES, 'UTF-8'); ?>-/
</td>

                                            </tr>
                                            <tr>
                                                <td class="left">
                                                    <strong class="text-dark">Tax</strong>
                                                </td>
                                               <td class="right">
    <?= htmlspecialchars($web['currency_symbol'] . " " . number_format($ftax, 2), ENT_QUOTES, 'UTF-8'); ?>
</td>

                                            </tr>
                                          
                                            <tr>
                                                <td class="left">
                                                    <strong class="text-dark">Total</strong>
                                                </td>
                                                <td class="right">
                                               <strong class="text-dark">
    <?= htmlspecialchars($web['currency_symbol'] . " " . number_format($invoice['final_total'], 2), ENT_QUOTES, 'UTF-8'); ?>-/
</strong>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <p class="mb-0">Terms and conditions:</p><br>
                            <p>
                            
                              <?php echo html_entity_decode($web['term']) ;?>
                            </p>
                        </div>


                        <div class="card-footer bg-white">
                            <p class="mb-0 text-right">Signtaure</p><br>
                            <p class="text-right">
                            
                            <img src="../assets/uploadImage/Logo/<?= htmlspecialchars($web['sign'], ENT_QUOTES, 'UTF-8'); ?>" class="img-responsive" style="width: 150px;" alt="Signature">
                            </p>
                        </div>
                    </div>
                    <input id="printbtn" type="button" value="Print Invoice" onclick="window.print();">
                    <input id="printbtn" type="button" value="Go Back" onclick="goBack()">
                    <!--<button onclick="window.print()">Print </button>-->
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- end footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- end wrapper  -->
    <!-- ============================================================== -->
</div>
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
<script>
    function goBack() {
        window.history.back();
    }
</script>

 
 <!--// preloader-->
 
  <script>
     

function onReady(callback) {
    var intervalID = window.setInterval(checkReady, 1000);
    function checkReady() {
        if (document.getElementsByTagName('body')[0] !== undefined) {
            window.clearInterval(intervalID);
            callback.call(this);
        }
    }
}

function show(id, value) {
    document.getElementById(id).style.display = value ? 'block' : 'none';
}

onReady(function () {
    show('page', true);
    show('loading', false);
});

 </script>
</body>

</html>