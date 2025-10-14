<?php
session_start();

require_once('../../assets/constants/config.php');

// Security headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $response = array();

    try {
        // CSRF Protection
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid CSRF token';
            echo json_encode($response);
            exit;
        }

        // Input validation
        $required_fields = ['inv_no', 'insta_amt', 'due_total', 'ptype', 'id', 'paid_amt'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                $response['status'] = 'error';
                $response['message'] = "Missing required field: $field";
                echo json_encode($response);
                exit;
            }
        }

        // Validate numeric fields
        if (!is_numeric($_POST['insta_amt']) || $_POST['insta_amt'] < 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid installment amount';
            echo json_encode($response);
            exit;
        }

        if (!is_numeric($_POST['due_total']) || $_POST['due_total'] < 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid due total amount';
            echo json_encode($response);
            exit;
        }

        if (!filter_var($_POST['id'], FILTER_VALIDATE_INT) || $_POST['id'] <= 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid invoice ID';
            echo json_encode($response);
            exit;
        }

        if (!is_numeric($_POST['paid_amt']) || $_POST['paid_amt'] < 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid paid amount';
            echo json_encode($response);
            exit;
        }

        // Validate payment type
        $valid_payment_types = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11'];
        if (!in_array($_POST['ptype'], $valid_payment_types)) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid payment type';
            echo json_encode($response);
            exit;
        }

        // Sanitize and cast values
        $inv_no = filter_var($_POST['inv_no'], FILTER_SANITIZE_STRING);
        $insta_amt = (float)$_POST['insta_amt'];
        $due_total = (float)$_POST['due_total'];
        $ptype = (int)$_POST['ptype'];
        $id = (int)$_POST['id'];
        $paid_amt = (float)$_POST['paid_amt'];

        // Establish database connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start transaction for data consistency
        $conn->beginTransaction();

        // Prepare SQL statement for inserting data - FIXED: Use proper parameter binding
        $added_date = date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO `tbl_installement`(`inv_no`, `added_date`, `insta_amt`, `due_total`, `ptype`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$inv_no, $added_date, $insta_amt, $due_total, $ptype]);

        $paid_total = $paid_amt + $insta_amt;

        // FIXED: Use proper parameter binding for UPDATE
        $stmt = $conn->prepare("UPDATE `tbl_invoice` SET due_total = ?, paid_amt = ? WHERE id = ?");
        $execute = $stmt->execute([$due_total, $paid_total, $id]);

        // Commit transaction
        $conn->commit();

        $_SESSION['success'] = "Record Updated Successfully";
        
        // If the execution reaches here, the operation was successful
        $response['status'] = 'success';
        $response['message'] = 'Payment saved successfully';
        
    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($conn && $conn->inTransaction()) {
            $conn->rollback();
        }
        
        // Log the error for debugging (don't expose in production)
        error_log('Database error in paid.php: ' . $e->getMessage());
        
        $response['status'] = 'error';
        $response['message'] = 'A database error occurred. Please try again.';
    } catch (Exception $e) {
        // Handle any other exceptions
        error_log('General error in paid.php: ' . $e->getMessage());
        
        $response['status'] = 'error';
        $response['message'] = 'An error occurred. Please try again.';
    }

    // Return JSON response to the client-side JavaScript
    echo json_encode($response);
} else {
    // Invalid request method
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
}
