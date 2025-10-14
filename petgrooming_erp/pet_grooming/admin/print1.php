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

// Input validation for GET parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invalid request: Missing ID parameter');
}

if (!filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die('Invalid request: ID must be a valid integer');
}

$invoice_id = (int)$_GET['id'];
?>

<?php 
// Fix SQL injection - use proper prepared statement with parameter binding
try {
    $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        throw new Exception('Admin profile not found');
    }
} catch (PDOException $e) {
    error_log('Database error in print1.php admin query: ' . $e->getMessage());
    die('Error: Unable to load admin data');
}
?>
 
<?php 
try {
    // Fix SQL injection - use proper prepared statement with parameter binding
    $sql = "SELECT * FROM tbl_invoice WHERE id = ?";
    $statement = $conn->prepare($sql);
    $statement->execute([$invoice_id]);
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);
    
    if (!$invoice) {
        throw new Exception('Invoice not found');
    }
} catch (PDOException $e) {
    error_log('Database error in print1.php invoice query: ' . $e->getMessage());
    die('Error: Unable to load invoice data');
}
?>
 
 
 <?php include('include/head.php');?>

            <?php include('include/header.php');?>
                        <?php include('include/sidebar.php');?>
        <!-- ============================================================== -->
        <!-- end left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
        <div class="dashboard-wrapper">
            <div class="dashboard-ecommerce">
                <div class="container-fluid dashboard-content ">
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
<!--                                     <a class="pt-2 d-inline-block" href="index.html">Concept</a>
-->                                   
                                    <div class="float-right"> <h3 class="mb-0">Invoice #<?= htmlspecialchars($invoice['inv_no'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    Date:  <?= htmlspecialchars($invoice['build_date'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-sm-6">
                                            <h5 class="mb-3">From:</h5>                                            
                                            <h3 class="text-dark mb-1"><?= htmlspecialchars($result['fname'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;<?= htmlspecialchars($result['lname'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                         
                                            <div><?= htmlspecialchars($result['address'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div>Email: <?= htmlspecialchars($result['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div>Phone: <?= htmlspecialchars($result['contact'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h5 class="mb-3">To:</h5>
                                            <h3 class="text-dark mb-1"><?= htmlspecialchars($invoice['customer_id'], ENT_QUOTES, 'UTF-8'); ?></h3>                                            
                                            <div><?= htmlspecialchars($invoice['c_address'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div>Email: <?= htmlspecialchars($invoice['c_email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div>Phone: <?= htmlspecialchars($invoice['customer_no'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                    <div class="table-responsive-sm">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="center">#</th>
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th class="right">Unit Cost</th>
                                                    <th class="center">Qty</th>
                                                    <th class="right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                try {
                                                    // Fix SQL injection - use proper prepared statement with parameter binding
                                                    $sql2 = "SELECT * FROM tbl_quot_inv_items WHERE inv_id = ?";
                                                    $statement2 = $conn->prepare($sql2);
                                                    $statement2->execute([$invoice_id]);
                                                    
                                                    $no = 0; // Initialize counter
                                                    
                                                    while($row2 = $statement2->fetch(PDO::FETCH_ASSOC)) {
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
                                                        
                                                        $no += 1;
                                                ?>
                                                <tr>
                                                    <td class="center"><?= htmlspecialchars($no, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="left strong"><?= htmlspecialchars($row1['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="left"><?= htmlspecialchars($row1['details'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="right"><?= htmlspecialchars($row2['rate'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="center"><?= htmlspecialchars($row2['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="right"><?= htmlspecialchars($row2['total'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                                <?php 
                                                    }
                                                } catch (PDOException $e) {
                                                    error_log('Database error in print1.php products query: ' . $e->getMessage());
                                                    echo '<tr><td colspan="6">Error loading products</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-5">
                                        </div>
                                        <div class="col-lg-4 col-sm-5 ml-auto">
                                            <table class="table table-clear">
                                                <tbody>
                                                    <tr>
                                                        <td class="left">
                                                            <strong class="text-dark">Subtotal</strong>
                                                        </td>
                                                        <td class="right"><?= htmlspecialchars($invoice['subtotal'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left">
                                                            <strong class="text-dark">Discount (<?= htmlspecialchars($invoice['discount'], ENT_QUOTES, 'UTF-8'); ?>%)</strong>
                                                        </td>
                                                        <td class="right"><?php
                                                        $discount = $invoice['subtotal'] * ($invoice['discount'] / 100);
                                                        echo htmlspecialchars($discount, ENT_QUOTES, 'UTF-8');
                                                        ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left">
                                                            <strong class="text-dark">GST (<?= htmlspecialchars($invoice['gst_rate'], ENT_QUOTES, 'UTF-8'); ?>%)</strong>
                                                        </td>
                                                        <td class="right"><?php
                                                        $gst_rate = ($invoice['subtotal'] - $discount) * ($invoice['gst_rate'] / 100);
                                                        echo htmlspecialchars(number_format($gst_rate, 2), ENT_QUOTES, 'UTF-8');
                                                        ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left">
                                                            <strong class="text-dark">Total</strong>
                                                        </td>
                                                        <td class="right">
                                                            <strong class="text-dark"><?= htmlspecialchars($invoice['final_total'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <p class="mb-0">2983 Glenview Drive Corpus Christi, TX 78476</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- footer -->
                <!-- ============================================================== -->
                <?php include('include/footer.php');?>
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
</body>
 
</html>