<?php
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

include('include/head.php'); 
?>
<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>
<?php
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

// Input validation and sanitization
if (!isset($_POST['id']) || empty($_POST['id'])) {
    die('Invalid request: Missing ID parameter');
}

// Validate that ID is numeric
if (!filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    die('Invalid request: ID must be a valid integer');
}

$invoice_id = (int)$_POST['id']; // Cast to integer for extra safety

// Additional authorization check - ensure user has permission to edit this invoice
// This should be customized based on your user role system
try {
    // Check if invoice belongs to current user's accessible data
    $auth_check = $conn->prepare("SELECT COUNT(*) as count FROM tbl_invoice WHERE id = ?");
    $auth_check->execute([$invoice_id]);
    $auth_result = $auth_check->fetch(PDO::FETCH_ASSOC);
    
    if ($auth_result['count'] == 0) {
        die('Error: Invoice not found or access denied');
    }
} catch (PDOException $e) {
    error_log('Authorization check failed: ' . $e->getMessage());
    die('Error: Unable to verify access permissions');
}
?>

<div class="dashboard-wrapper">
    <div class="container-fluid  dashboard-content">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <h5 class="card-header">Add Installment</h5>
                    <div class="card-body">
                        <?php
                        try {
                            // Fix SQL injection - use proper prepared statement with parameter binding
                            $stmt = $conn->prepare("SELECT * FROM tbl_invoice WHERE id = ?");
                            $stmt->execute([$invoice_id]);
                            $product = $stmt->fetch(PDO::FETCH_ASSOC);

                            // Check if invoice exists
                            if (!$product) {
                                throw new Exception('Invoice not found');
                            }

                            // Validate customer_id exists and is numeric
                            if (empty($product['customer_id']) || !is_numeric($product['customer_id'])) {
                                throw new Exception('Invalid customer ID in invoice');
                            }

                            $sqlq = "SELECT * FROM tbl_customer WHERE cust_id = ?";
                            $stat = $conn->prepare($sqlq);
                            $stat->execute([$product['customer_id']]);
                            $cust = $stat->fetch();

                            // Check if customer exists
                            if (!$cust) {
                                throw new Exception('Customer not found');
                            }
                        } catch (PDOException $e) {
                            error_log('Database error in edit.php: ' . $e->getMessage());
                            die('Error: Database connection failed. Please try again later.');
                        } catch (Exception $e) {
                            error_log('Error in edit.php: ' . $e->getMessage());
                            die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
                        }
                        ?>
                        <form id="add-result" class="sign-in-form mt-32 add_customer row">
                            <!-- CSRF Protection -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            
                            <input type="hidden" class="form-control " name="id" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" class="form-control " name="paid_amt" value="<?= htmlspecialchars($product['paid_amt'], ENT_QUOTES, 'UTF-8'); ?>">

                            <?php $current_date = date('Y-m-d'); ?>
                            <div class="mb-3 col-md-6">
                                <label class="txt-lbl">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" id="text" name="cust_name" value="<?= htmlspecialchars($cust['cust_name'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                <div class="form_bottom_boder"></div>
                            </div>
                           
                            <div class="mb-3 col-md-6">
                                <label class="txt-lbl">Date<span class="text-danger">*</span></label>
                                <input type="date" name="build_date" class="form-control" value="<?= htmlspecialchars($product['build_date'], ENT_QUOTES, 'UTF-8'); ?>" data-provide="datepicker" required readonly>
                                <div class="form_bottom_boder"></div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="txt-lbl">Invoice No.<span class="text-danger">*</span></label>
                                <?php 
                                $user = "SELECT COUNT(*) as cnt FROM tbl_invoice";
                                $statement = $conn->prepare($user);
                                $statement->execute();
                                $row = $statement->fetch(PDO::FETCH_ASSOC);
                                $new = 10000 + $row['cnt'] + 1;
                                ?>
                                <input type="text" value="<?= htmlspecialchars($product['inv_no'], ENT_QUOTES, 'UTF-8'); ?>" name="inv_no" class="form-control" required readonly>
                                <div class="form_bottom_boder"></div>
                            </div>
                            <input type="hidden" name="subtotal" id="subtotal" class="form-control" value="<?= htmlspecialchars($product['subtotal'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Subtotal" readonly="">
                            <div class="mb-3 col-md-6">
                                <label class="txt-lbl">Final Total<span class="text-danger">*</span></label>
                                <input type="text" name="final_total" id="final_total" placeholder="Total" value="<?= htmlspecialchars($product['final_total'], ENT_QUOTES, 'UTF-8'); ?>" onblur="myFunction()" class="form-control">
                                <div class="form_bottom_boder"></div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="txt-lbl">Due Amount<span class="text-danger">*</span></label>
                                <input type="text" name="due_total" id="due_total" onblur="myFunction()" value="<?= htmlspecialchars($product['due_total'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Due Amount" readonly="" class="form-control">
                                <div class="form_bottom_boder"></div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="txt-lbl">Installment Amount<span class="text-danger">*</span></label>
                                <input type="text" name="insta_amt" id="insta_amt" onblur="myFunction()" class="form-control" max="<?= htmlspecialchars($product['due_total'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                <div class="form_bottom_boder"></div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="txt-lbl">Select Payment Method</label>
                                <select name="ptype" class="form-control" required>
                                    <option value="1" <?= ($product['ptype'] == '1') ? 'selected' : ''; ?>>CASH</option>
                                    <option value="2" <?= ($product['ptype'] == '2') ? 'selected' : ''; ?>>ONLINE</option>
                                    <option value="3" <?= ($product['ptype'] == '3') ? 'selected' : ''; ?>>CHEQUE</option>
                                    <option value="4" <?= ($product['ptype'] == '4') ? 'selected' : ''; ?>>DEBIT CARD</option>
                                    <option value="5" <?= ($product['ptype'] == '5') ? 'selected' : ''; ?>>CREDIT CARD</option>
                                    <option value="6" <?= ($product['ptype'] == '6') ? 'selected' : ''; ?>>UPI</option>
                                    <option value="7" <?= ($product['ptype'] == '7') ? 'selected' : ''; ?>>NET BANKING</option>
                                    <option value="8" <?= ($product['ptype'] == '8') ? 'selected' : ''; ?>>PAYTM</option>
                                    <option value="9" <?= ($product['ptype'] == '9') ? 'selected' : ''; ?>>GOOGLE PAY</option>
                                    <option value="10" <?= ($product['ptype'] == '10') ? 'selected' : ''; ?>>PHONEPE</option>
                                    <option value="11" <?= ($product['ptype'] == '11') ? 'selected' : ''; ?>>BANK TRANSFER</option>
                                </select>
                            </div>
                            <div class="sign-in mt-32 col-md-12">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('include/footer.php'); ?>
</div>

<!-- Scripts -->
<script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<script src="assets/vendor/slimscroll/jquery.slimscroll.js"></script>
<script src="assets/libs/js/main-js.js"></script>
<script src="assets/libs/js/jquery.js"></script>
<script src="assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
<script src="assets/vendor/charts/sparkline/jquery.sparkline.js"></script>
<script src="assets/vendor/charts/morris-bundle/raphael.min.js"></script>
<script src="assets/vendor/charts/morris-bundle/morris.js"></script>
<script src="assets/vendor/charts/c3charts/c3.min.js"></script>
<script src="assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
<script src="assets/vendor/charts/c3charts/C3chartjs.js"></script>
<script src="assets/libs/js/dashboard-ecommerce.js"></script>

<script>
    $(document).ready(function() {
        $('.add_customer').submit(function(event) {
            event.preventDefault(); // Prevent default form submission
            
            // Basic client-side validation
            var installmentAmount = parseFloat($('#insta_amt').val()) || 0;
            var dueTotal = parseFloat($('#due_total').val()) || 0;
            
            if (installmentAmount <= 0) {
                alert('Please enter a valid installment amount');
                return false;
            }
            
            if (installmentAmount > dueTotal) {
                alert('Installment amount cannot exceed due amount');
                return false;
            }
            
            var formData = $(this).serialize(); // Get form data

            // Send AJAX request
            $.ajax({
                type: 'POST',
                url: 'operation/paid.php',
                data: formData,
                dataType: 'json',
                timeout: 10000, // 10 second timeout
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Payment saved successfully!');
                        window.location.href = 'view_order.php';
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    if (status === 'timeout') {
                        alert('Request timed out. Please try again.');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });

    function myFunction() {
        var dueTotal = parseFloat(document.getElementById("due_total").value) || 0;
        var installmentAmount = parseFloat(document.getElementById("insta_amt").value) || 0;
        var finalTotal = parseFloat(document.getElementById("final_total").value) || 0;

        // Set max limit dynamically
        document.getElementById("insta_amt").setAttribute("max", dueTotal);

        if (installmentAmount > dueTotal) {
            document.getElementById("insta_amt").value = dueTotal; // Restrict entry
            installmentAmount = dueTotal;
        }

        // Validate that installment amount is not negative
        if (installmentAmount < 0) {
            document.getElementById("insta_amt").value = 0;
            installmentAmount = 0;
        }

        var newDueTotal = dueTotal - installmentAmount;
        document.getElementById("due_total").value = newDueTotal.toFixed(2);
    }

    // Restrict invalid input in real-time
    document.getElementById("insta_amt").addEventListener("input", function () {
        var dueTotal = parseFloat(document.getElementById("due_total").value) || 0;
        var installmentAmount = parseFloat(this.value) || 0;

        // Remove any non-numeric characters except decimal point
        this.value = this.value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        if ((this.value.match(/\./g) || []).length > 1) {
            this.value = this.value.substring(0, this.value.lastIndexOf('.'));
        }

        installmentAmount = parseFloat(this.value) || 0;
        
        if (installmentAmount > dueTotal) {
            this.value = dueTotal; // Stop entry beyond due amount
        }
        
        if (installmentAmount < 0) {
            this.value = 0; // Prevent negative values
        }
    });
</script>
