<link rel="stylesheet" href="popup_style.css">

<?php
error_reporting(0);
session_start();
if (isset($_SESSION['logged']) && $_SESSION['logged'] == "1" && $_SESSION['role'] == "admin") {

  require_once('../../assets/constants/config.php');

  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


   
if (isset($_POST['add_stock'])) {
    // Form se values le rahe hain
    $id = $_POST['id'];
    $new_stock = $_POST['openning_stock'];
    $old_stock = $_POST['old_stock'];

    // Naya stock update karna
    $updated_stock = $new_stock + $old_stock;

    try {
        // PDO query execute karna
        $query = "UPDATE tbl_product SET openning_stock = :updated_stock WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':updated_stock', $updated_stock, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Stock Updated Succesfully";
      header('location:../productdisplay.php');
      exit;
        } else {
            echo "<script>alert('Stock update failed!');</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}



    if (isset($_POST['btn_save'])) {
        
      
    $id=$_SESSION['id'];
      $openning_stock = 0;
      
      
    $gst = $_POST['gst'] ?? [];

    // Validate and sanitize numeric inputs
    $purchase_price = filter_var($_POST['purchase_price'], FILTER_VALIDATE_FLOAT);
    $unit_price = filter_var($_POST['unit_price'], FILTER_VALIDATE_FLOAT);
    if ($purchase_price === false || $purchase_price < 0) {
      $_SESSION['error'] = "Invalid purchase price.";
      header('location:../productdisplay.php');
      exit;
    }
    if ($unit_price === false || $unit_price < 0) {
      $_SESSION['error'] = "Invalid unit price.";
      header('location:../productdisplay.php');
      exit;
    }

    // Validate GST ids and compute total GST percentage from trusted DB values
    $gst_ids = array_map('intval', $gst);
    $totalGST = 0.0;
    if (!empty($gst_ids)) {
      $taxStmt = $conn->prepare("SELECT id, percentage FROM tbl_tax WHERE delete_status='0' AND id = :id LIMIT 1");
      foreach ($gst_ids as $tid) {
        if ($tid <= 0) {
          $_SESSION['error'] = "Invalid tax selection.";
          header('location:../productdisplay.php');
          exit;
        }
        $taxStmt->bindValue(':id', $tid, PDO::PARAM_INT);
        $taxStmt->execute();
        $trow = $taxStmt->fetch(PDO::FETCH_ASSOC);
        if (!$trow) {
          $_SESSION['error'] = "Selected tax (id: $tid) not found.";
          header('location:../productdisplay.php');
          exit;
        }
        $percentage = floatval($trow['percentage']);
        // Basic sanity check on percentage
        if ($percentage < 0 || $percentage > 100) {
          $_SESSION['error'] = "Invalid tax percentage configured on server.";
          header('location:../productdisplay.php');
          exit;
        }
        $totalGST += $percentage;
      }
    }

    $expl = implode(',', $gst_ids);

    // Compute server-side purchase_gst and selling_gst values (do NOT trust client-supplied fields)
    $purchase_gst_value = $purchase_price + ($purchase_price * $totalGST / 100.0);
    // If product type is service (exp == 1), selling cost is unit_price (no GST addition in UI logic)
    $selling_cost = '';
    if (isset($_POST['exp']) && $_POST['exp'] == '1') {
      $selling_cost = $unit_price;
      $selling_gst_value = $unit_price;
    } else {
      $selling_gst_value = $unit_price + ($unit_price * $totalGST / 100.0);
      $selling_cost = $selling_gst_value;
    }

    $stmt = $conn->prepare("INSERT INTO tbl_product (pid, name,unit_price,purchase_price,openning_stock,currentdate,group_id,details,user_id, gst,purchase_gst, selling_gst, exp,exp_date,hsn) VALUES (:pid,:name,:unit_price,:purchase_price,:openning_stock, :created_date,:group_id,:details,:user_id, :gst, :purchase_gst, :selling_gst,:exp,:exp_date,:hsn)");
    $stmt->bindValue(':pid', htmlspecialchars($_POST['pid']), PDO::PARAM_STR);
    $stmt->bindValue(':name', htmlspecialchars($_POST['name']), PDO::PARAM_STR);
    $stmt->bindParam(':unit_price', $selling_cost);

    $stmt->bindParam(':purchase_price', $purchase_price);
    $stmt->bindValue(':openning_stock', htmlspecialchars($openning_stock), PDO::PARAM_INT);
    $stmt->bindValue(':created_date', htmlspecialchars(date('Y-m-d')), PDO::PARAM_STR);
    $stmt->bindValue(':group_id', htmlspecialchars($_POST['group_id']), PDO::PARAM_STR);
    $stmt->bindValue(':details', htmlspecialchars($_POST['details']), PDO::PARAM_STR);
  
     $stmt->bindParam(':user_id', $id);
    $stmt->bindValue(':gst', $expl, PDO::PARAM_STR);
    $stmt->bindValue(':purchase_gst', number_format($purchase_gst_value, 2, '.', ''), PDO::PARAM_STR);
    $stmt->bindValue(':selling_gst', number_format($selling_gst_value, 2, '.', ''), PDO::PARAM_STR);
     $stmt->bindValue(':exp', htmlspecialchars($_POST['exp']), PDO::PARAM_STR);
      $stmt->bindValue(':exp_date', htmlspecialchars($_POST['exp_date']), PDO::PARAM_STR);
          
      $stmt->bindValue(':hsn', htmlspecialchars($_POST['hsn']), PDO::PARAM_STR);
    //   $stmt->bindParam(':image', htmlspecialchars($img));

      $stmt->execute();

      //echo "<script>alert(' Record Successfully Added');</script>";
      $last_inserted_id = htmlspecialchars($conn->lastInsertId());

     $_SESSION['success'] = "Product Added Succesfully";
      header('location:../productdisplay.php');
      exit;

    }
    
    // print_r($_POST);
   if (isset($_POST['btn_edit'])) {

    $id = $_POST['id'];
    $pid = htmlspecialchars($_POST['pid']);
    $name = htmlspecialchars($_POST['name']);
    $hsn = htmlspecialchars($_POST['hsn']);
    $group_id = $_POST['group_id'];
    $purchase_price = $_POST['purchase_price'];
    $unit_price = $_POST['unit_price'];
    $details = htmlspecialchars($_POST['details']);
    $exp = $_POST['exp'];
    $exp_date = !empty($_POST['exp_date']) ? $_POST['exp_date'] : null;
  
  $gst_array = $_POST['gst'] ?? [];
  $gst_ids = array_map('intval', $gst_array);
  $gst = implode(',', $gst_ids); // convert array to comma-separated string

  // Validate numeric inputs
  $purchase_price = filter_var($purchase_price, FILTER_VALIDATE_FLOAT);
  $unit_price = filter_var($unit_price, FILTER_VALIDATE_FLOAT);
  if ($purchase_price === false || $purchase_price < 0) {
    $_SESSION['error'] = "Invalid purchase price.";
    header('location:../update_product.php?id=' . urlencode($id));
    exit;
  }
  if ($unit_price === false || $unit_price < 0) {
    $_SESSION['error'] = "Invalid unit price.";
    header('location:../update_product.php?id=' . urlencode($id));
    exit;
  }

  // Recompute GST percentages from server-side trusted source
  $totalGST = 0.0;
  if (!empty($gst_ids)) {
    $taxStmt = $conn->prepare("SELECT id, percentage FROM tbl_tax WHERE delete_status='0' AND id = :id LIMIT 1");
    foreach ($gst_ids as $tid) {
      if ($tid <= 0) {
        $_SESSION['error'] = "Invalid tax selection.";
        header('location:../update_product.php?id=' . urlencode($id));
        exit;
      }
      $taxStmt->bindValue(':id', $tid, PDO::PARAM_INT);
      $taxStmt->execute();
      $trow = $taxStmt->fetch(PDO::FETCH_ASSOC);
      if (!$trow) {
        $_SESSION['error'] = "Selected tax (id: $tid) not found.";
        header('location:../update_product.php?id=' . urlencode($id));
        exit;
      }
      $percentage = floatval($trow['percentage']);
      if ($percentage < 0 || $percentage > 100) {
        $_SESSION['error'] = "Invalid tax percentage configured on server.";
        header('location:../update_product.php?id=' . urlencode($id));
        exit;
      }
      $totalGST += $percentage;
    }
  }

  // Compute server-side purchase_gst and selling_gst
  $computed_purchase_gst = $purchase_price + ($purchase_price * $totalGST / 100.0);
  if ($exp == '1') {
    $computed_selling_gst = $unit_price;
    $final_selling_cost = $unit_price;
  } else {
    $computed_selling_gst = $unit_price + ($unit_price * $totalGST / 100.0);
    $final_selling_cost = $computed_selling_gst;
  }

  try {
        $stmt = $conn->prepare("UPDATE tbl_product SET 
            pid = :pid,
            name = :name,
            hsn = :hsn,
            group_id = :group_id,
            purchase_price = :purchase_price,
            unit_price = :unit_price,
            details = :details,
            exp = :exp,
            exp_date = :exp_date,
          
            gst = :gst,
            purchase_gst = :purchase_gst,
            selling_gst = :selling_gst
        WHERE id = :id");

        $stmt->bindParam(':pid', $pid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':hsn', $hsn);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->bindParam(':purchase_price', $purchase_price);
        $stmt->bindParam(':unit_price', $final_selling_cost);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':exp', $exp);
        $stmt->bindParam(':exp_date', $exp_date);
       
  $stmt->bindValue(':gst', $gst, PDO::PARAM_STR);
  $stmt->bindValue(':purchase_gst', number_format($computed_purchase_gst, 2, '.', ''), PDO::PARAM_STR);
  $stmt->bindValue(':selling_gst', number_format($computed_selling_gst, 2, '.', ''), PDO::PARAM_STR);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product updated successfully.";
            header("Location: ../productdisplay.php"); 
            exit();

        } else {
            $_SESSION['error'] = "Something went wrong. Try again.";
            header("Location: ../update_product.php?id=" . $id);
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "DB Error: " . $e->getMessage();
        header("Location: ../update_product.php?id=" . $id);
        exit();
    }
}

    if (isset($_POST['del_id'])) {
      //$stmt = $conn->prepare("DELETE FROM customers WHERE id = :id");
  $stmt = $conn->prepare("UPDATE tbl_product SET delete_status=1 WHERE id=:id");
  $stmt->bindValue(':id', intval($_POST['del_id']), PDO::PARAM_INT);
      $stmt->execute();

       $_SESSION['success'] = "Product Deleted Succesfully";
      header('location:../productdisplay.php');
      exit;
    }
  } catch (PDOException $e) {
    echo "Connection failed: " . htmlspecialchars($e->getMessage());
  }
} else {

  header("location:../");
}

?>