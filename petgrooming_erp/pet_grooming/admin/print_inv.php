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

// Input validation for POST parameter
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
    // Fix SQL injection - use proper prepared statement with parameter binding
    $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        throw new Exception('Admin profile not found');
    }
} catch (PDOException $e) {
    error_log('Database error in print_inv.php admin query: ' . $e->getMessage());
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
    error_log('Database error in print_inv.php: ' . $e->getMessage());
    die('Error: Unable to load invoice data');
} catch (Exception $e) {
    error_log('Error in print_inv.php: ' . $e->getMessage());
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>
   
   
   function number_format1($number, $decimal = 2) {
       // Format the number to fixed decimal places
       $number = number_format((float)$number, $decimal, '.', '');
   
       // Split integer and decimal parts
       $decimalPart = '';
       if (strpos($number, '.') !== false) {
           list($number, $decimalPart) = explode('.', $number);
       }
   
       // Indian number formatting
       $last3 = substr($number, -3);
       $restUnits = substr($number, 0, -3);
   
       if ($restUnits != '') {
           $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
           $formatted = $restUnits . "," . $last3;
       } else {
           $formatted = $last3;
       }
   
       return $decimal > 0 ? $formatted . "." . $decimalPart : $formatted;
   }
   
   
   ?>
<!DOCTYPE html>
<html>
   <head>
      <title>invoice</title>
      <style >
         body{
         font-family: system-ui;
         }
         table{
         width: 90%;
         margin: 0 auto;
         border: 1px solid;
         border-collapse: collapse;
         }
         td,th{
         padding: 10px;
         border: 1px solid;
         border-collapse: collapse;
         /*text-align: center;*/
         }
         @media print{
         table{
         width: 100%;
         }
         .trbg{
         color: #000 !important;
         }
         td,th{
         padding: 1px;
         }
         button{
         display: none;
         }
         }
         .td td{
         border-top: none !important;
         border-bottom:none !important;
         }
         .border-bottom td{
         border-bottom:none !important;
         }
         .th th{
             font-size: 13px;
             padding: 10px 0px;
                 background: #ead1f8;
                 -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
         }
         .bg{
             background: #ead1f8;
             -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
         }
      </style>
   </head>
   <body>
      <table>
         <tr>
            <div></div>
            <td rowspan="2" colspan="3">
               <h4 style="margin: 0px"><?= htmlspecialchars($result['fname'], ENT_QUOTES, 'UTF-8'); ?>&nbsp;<?= htmlspecialchars($result['lname'], ENT_QUOTES, 'UTF-8'); ?></h4>
               <?= htmlspecialchars($result['address'], ENT_QUOTES, 'UTF-8'); ?><br>
               <strong>GSTIN :</strong> <?= htmlspecialchars($result['gstin'], ENT_QUOTES, 'UTF-8'); ?><br>
               <strong>Mobile :</strong> <?= htmlspecialchars($result['contact'], ENT_QUOTES, 'UTF-8'); ?><br>
            </td>
            <td colspan="4">
               <div style="display: flex;justify-content: space-between;">
                  <p><strong>Invoice No</strong><br>#<?= htmlspecialchars($invoice['inv_no'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <p><strong>Invoice Date</strong><br><?= htmlspecialchars(date("d-m-Y", strtotime($invoice['build_date'])), ENT_QUOTES, 'UTF-8'); ?></p>
                  <p><strong>Due Date</strong><br><?= htmlspecialchars(date("d-m-Y", strtotime($invoice['due_date'])), ENT_QUOTES, 'UTF-8'); ?></p>
               </div>
            </td>
         </tr>
         <!--<tr>-->
         <!--   <td colspan="4">-->
         <!--      <div style="display: flex;text-align: center;">-->
         <!--         <p><strong>Salesman</strong><br>Arjun S</p>-->
         <!--         <p style="padding-left:35px"><strong>Challan No</strong><br>1234567890</p>-->
         <!--      </div>-->
         <!--   </td>-->
         <!--</tr>-->
         <tr>
            <td colspan="3">
               BILLTO<br>
               <strong><?= htmlspecialchars($cust['cust_name'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
               Mobile: <?= htmlspecialchars($cust['cust_mob'], ENT_QUOTES, 'UTF-8'); ?><br>
               Email: <?= htmlspecialchars($cust['cust_email'], ENT_QUOTES, 'UTF-8'); ?><br>
            </td>
            <td colspan="4">
               <strong>SHIP TO <br><?= htmlspecialchars($cust['cust_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </td>
         </tr>
         <?php
         try {
            // Fix SQL injection - use proper prepared statement with parameter binding
            $sql2 = "SELECT * FROM tbl_quot_inv_items WHERE inv_id = ?";
            $statement2 = $conn->prepare($sql2);
            $statement2->execute([$invoice_id]);
            $res2 = $statement2->fetchAll();
            
            if(count($res2) > 0){
            ?>
         <tr class="th">
            <th>S.NO</th>
            <th>Product/Services</th>
            <th>HSN</th>
            <th>WARRANTY EXPIRY DATE</th>
            <th>QTY</th>
            <th>RATE</th>
            <th>AMOUNT</th>
         </tr>
         <?php
            $fstot = 0;
            $ftax = 0;
            $fqty = 0;
            $no = 0; // Initialize counter
            
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
                    
                    // Validate unit ID is numeric before using it
                    if (is_numeric($row1['unit'])) {
                        $stmt3 = $conn->prepare("SELECT * FROM tbl_unit_grp WHERE id = ? AND delete_status = '0'");
                        $stmt3->execute([$row1['unit']]);
                        $key3 = $stmt3->fetch();
                    }
                    
                    $no += 1;
                    
                    $stot = $row2['quantity'] * $row1['unit_price'];
                    $fstot += $stot;
                    $fqty += $row2['quantity'];
                    ?>    
         <tr class="border-bottom">
            <td><?= htmlspecialchars($no, ENT_QUOTES, 'UTF-8'); ?></td>
            <td>
               <strong><?= htmlspecialchars($row1['name'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
            </td>
            <td><?= htmlspecialchars($row1['hsn'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php 
                if($row1['exp']=='0'){
                    echo htmlspecialchars(date("d-m-Y", strtotime($row1['exp_date'])), ENT_QUOTES, 'UTF-8');
                } else { 
                    echo 'No Expiry'; 
                }
            ?></td>
            <td><?= htmlspecialchars($row2['quantity'], ENT_QUOTES, 'UTF-8'); ?>PCS</td>
            <td><?= htmlspecialchars($web['currency_symbol'] . " " . number_format($row1['unit_price'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($web['currency_symbol'] . " " . number_format(($row1['unit_price']*$row2['quantity']), 2), ENT_QUOTES, 'UTF-8'); ?></td>
         </tr>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
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
                         $ftax += $cgst_amt;
                         ?>
         <tr class="td">
            <td></td>
            <td><strong><?= htmlspecialchars($tax['name'], ENT_QUOTES, 'UTF-8'); ?>@<?= htmlspecialchars($cgst, ENT_QUOTES, 'UTF-8'); ?>%</strong></td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td><?= htmlspecialchars($web['currency_symbol'], ENT_QUOTES, 'UTF-8'); ?><?= htmlspecialchars(number_format($cgst_amt, 2), ENT_QUOTES, 'UTF-8'); ?></td>
         </tr>
         <?php 
                     }
                 }
             }
         }
         ?>
         <?php 
                } catch (PDOException $e) {
                    error_log('Database error in print_inv.php product loop: ' . $e->getMessage());
                    continue; // Skip this product and continue with the next one
                }
            }
        } catch (PDOException $e) {
            error_log('Database error in print_inv.php main query: ' . $e->getMessage());
            echo '<tr><td colspan="7">Error loading invoice items</td></tr>';
        }
         ?>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
         <tr class="td">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
         <tr>
            <td class="bg"></td>
            <td class="bg"><strong>Total</strong></td>
            <td class="bg">-</td>
            <td class="bg">-</td>
            <td class="bg"><?= htmlspecialchars(number_format($fqty, 2), ENT_QUOTES, 'UTF-8'); ?></td>
            <td class="bg">-</td>
            <td class="bg"><?= htmlspecialchars($web['currency_symbol'], ENT_QUOTES, 'UTF-8'); ?><?= htmlspecialchars(number_format(($fstot + $ftax), 2), ENT_QUOTES, 'UTF-8'); ?></td>
         </tr>
      </table>
      <?php  
         foreach($res2 as $row2){
         
                                                $sql1 = "SELECT * FROM tbl_product where id='" . $row2['product_id'] . "'";
         
         
                                                $statement1 = $conn->prepare($sql1);
                                                $statement1->execute();
                                                $row1 = $statement1->fetch(PDO::FETCH_ASSOC);
         
         
                                                $stmt3 = $conn->prepare("SELECT * FROM tbl_unit_grp where id='" . $row1['unit'] . "' AND delete_status = '0'");
                                                $stmt3->execute();
                                                $key3 = $stmt3->fetch();
         
                                                $no += 1;
                                                
                                                $stot=$row2['quantity']*$row1['unit_price'];
                                                $fstot+=$stot;
                                                $fqty+=$row2['quantity'];
                                            ?>    
      <table style="margin-top:10px;">
         <thead>
            <tr>
               <th rowspan="2" class="bg">HSN/SAC</th>
               <th class="bg" rowspan="2">Taxable Value</th>
               <?php  
                  $ftaxtot=0;
                   $product=$row1['gst']; 
                  $productArray = explode(',', $product);
                  foreach($productArray as $pro_med){
                  $stax = $conn->prepare("SELECT * FROM tbl_tax where id='" . $pro_med . "' AND delete_status = '0'");
                                                $stax->execute();
                                                $tax = $stax->fetch();   ?>
               <th colspan="2" class="bg">   <?php    echo  $tax['name']; ?></th>
               <?php   
                  }
                  ?>
               <th rowspan="2" class="bg">Total Tax Amount</th>
            </tr>
            <tr>
              
               <th>Rate</th>
               <th>Amount</th>
               <th>Rate</th>
               <th>Amount</th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td><?= $row1['hsn'] ?></td>
               <td style="text-align:end"><?php echo $web['currency_symbol']; echo number_format1(($row2['quantity']*$row1['unit_price']),2); ?></td>
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
                               ?>
               <td style="text-align:end"><?= htmlspecialchars($tax['percentage'], ENT_QUOTES, 'UTF-8'); ?> %</td>
               <td style="text-align:end"><?= htmlspecialchars($web['currency_symbol'], ENT_QUOTES, 'UTF-8'); ?><?= htmlspecialchars(number_format($cgst_amt, 2), ENT_QUOTES, 'UTF-8'); ?></td>
               <?php   
                               $ftaxtot += $cgst_amt;
                           }
                       }
                   }
               }
               ?>
               <td style="text-align:end"><?= htmlspecialchars($web['currency_symbol'], ENT_QUOTES, 'UTF-8'); ?><?= htmlspecialchars(number_format($ftaxtot, 2), ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr class="total">
               <td style="text-align:end">Total</td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td style="text-align:end"><?= htmlspecialchars($web['currency_symbol'], ENT_QUOTES, 'UTF-8'); ?><?= htmlspecialchars(number_format($ftaxtot + ($row2['quantity'] * $row1['unit_price']), 2), ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
         </tbody>
      </table>
      <?php } ?>
      <?php } ?>
      <table>
         <tr>
            <td>
               <h3>Bank Details</h3>
               <p><strong>Name:</strong> <?= htmlspecialchars($web['title'], ENT_QUOTES, 'UTF-8'); ?></p>
               <p><strong>IFSC Code:</strong> <?= htmlspecialchars($web['ifsc'], ENT_QUOTES, 'UTF-8'); ?></p>
               <p><strong>Account No:</strong> <?= htmlspecialchars($web['acc_no'], ENT_QUOTES, 'UTF-8'); ?></p>
               <p><strong>Bank:</strong> <?= htmlspecialchars($web['branch'], ENT_QUOTES, 'UTF-8'); ?>,<?= htmlspecialchars($web['badd'], ENT_QUOTES, 'UTF-8'); ?></p>
            </td>
            <td class="qr-code" style="display:flex;justify-content:space-between;height:35vh;align-items:center">
                <div>
               <h3>Payment QR Code</h3>
               <p>Google Pay | Paytm | UPI</p>
               </div>
               <img src="../assets/uploadImage/Logo/<?= htmlspecialchars($web['qr'], ENT_QUOTES, 'UTF-8'); ?>" alt="QR Code" width="80px" height="80px">
            </td>
         </tr>
         <tr>
            <td >
               <?= htmlspecialchars(html_entity_decode($web['term']), ENT_QUOTES, 'UTF-8'); ?>
            </td>
            <td class="qr-code" style="text-align:center">
               <p><img src="../assets/uploadImage/Logo/<?= htmlspecialchars($web['sign'], ENT_QUOTES, 'UTF-8'); ?>" alt="Signature" width="80px" height="80px"></p>
               <p><strong>Authorized Signatory</strong></p>
            </td>
         </tr>
         <tr>
            
         </tr>
      </table>
      <div style="text-align: center">
         <button onclick="window.print()">Print</button>
      </div>
   </body>
</html>