<link rel="stylesheet" href="popup_style.css">


<?php
// Configure session cookie parameters to improve security: HttpOnly, Secure (when using HTTPS), and SameSite=Lax
// These must be set before session_start().
// Detect if the current request is over HTTPS (including common proxy headers).
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
  || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

// Preserve existing cookie params where possible
$existing = session_get_cookie_params();

$cookieParams = [
  'lifetime' => $existing['lifetime'],
  'path' => $existing['path'],
  'domain' => $existing['domain'],
  'secure' => (bool)$isSecure,
  'httponly' => true,
];

// Use array form for PHP >= 7.3 to set SameSite as well
if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
  $cookieParams['samesite'] = 'Lax';
  session_set_cookie_params($cookieParams);
} else {
  // For older PHP versions, add SameSite via path (best-effort). Some environments may not honor it.
  $path = $cookieParams['path'] ?? '/';
  if (!empty($cookieParams['samesite'])) {
    $path .= '; samesite=' . $cookieParams['samesite'];
  } else {
    $path .= '; samesite=Lax';
  }
  session_set_cookie_params(
    $cookieParams['lifetime'],
    $path,
    $cookieParams['domain'],
    $cookieParams['secure'],
    $cookieParams['httponly']
  );
}

// Start the session after configuring cookie parameters
session_start();

// Server-side session binding: if ip/user-agent were stored previously, validate them on each request.
// If they differ, invalidate the session to reduce session portability risks.
if (isset($_SESSION['ip_address']) && isset($_SESSION['user_agent'])) {
  $current_ip = $_SERVER['REMOTE_ADDR'] ?? '';
  $current_ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
  if ($_SESSION['ip_address'] !== $current_ip || $_SESSION['user_agent'] !== $current_ua) {
    // Clear session data and cookie, then destroy session
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
      );
    }
    session_destroy();
    // Redirect to login page (or stop processing). Using relative path consistent with app.
    header('Location: ../../index.php');
    exit;
  }
}
//configuration file
require_once('../constants/config.php');

$email_address = $_POST['email'];
$passw = hash('sha256', $_POST['password']);

$salt = getenv('APP_SECURITY_SALT');
$pass = hash('sha256', $salt . $passw);
//
try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("SELECT * FROM tbl_admin where email=:email AND  password=:password AND delete_status='0'  ");
  $stmt->bindParam(':email', $email_address);
  $stmt->bindParam(':password', $pass);
  $stmt->execute();
  $result = $stmt->fetchAll();

  //getting number of records found
  $rec = count($result);

  if ($rec > 0) {
    foreach ($result as $row) {


      $role = $row['role'];
      $avator = $row['image'];
      $_SESSION['id'] = $row['id'];
    }
//print_r($row['password']); exit;
    switch ($role) {
      case 'admin':
        //echo$pass;
        //echo$row['password'];exit;

        # verifying password
        if ($pass == $row['password']) {

          admin_login();
        } else {
          //echo "<script>alert('Invalid Login');</script>";
?>
          <div class="popup popup--icon -error js_error-popup popup--visible">
            <div class="popup__background"></div>
            <div class="popup__content">
              <h3 class="popup__content__title">
                Error
                </h1>
                <p>Invalid Email or Password</p>
                <p>
                  <a href="../../index.php"><button class="button button--error" data-for="js_error-popup">Close</button></a>
                </p>
            </div>
          </div>
  <?php
          /* echo "<script>document.location='../../index.php'
</script>";
*/  /*$_SESSION['reply'] = "001";
    header("location:../../");
*/
        }
        break;

      case 'users':

        if ($pass == $row['password']) {

          student_login();
        } else {

          $_SESSION['reply'] = "001";
          header("location:../../");
        }
        break;
    }
  } else {

    $_SESSION['reply'] = "001";
    header("location:../../");
  }
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

function admin_login()
{

  $_SESSION['logged'] = "1";
  $_SESSION['role'] = "admin";
  $_SESSION['email'] = $GLOBALS['email_address'];
  $_SESSION['avator'] = $GLOBALS['avator'];
  // Bind this session to the current client (IP and User-Agent) to reduce session portability.
  $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
  $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
  // Regenerate session ID after privilege change/login to prevent session fixation
  session_regenerate_id(true);
  /* echo "<script>alert('  Login Successfully');</script>";
 echo "<script>document.location='../../admin'
</script>";*/ ?>
  <div class="popup popup--icon -success js_success-popup popup--visible">
    <div class="popup__background"></div>
    <div class="popup__content">
      <h3 class="popup__content__title">
        Success
        </h1>
        <p>Login Successfully</p>
        <p>

          <?php echo "<script>setTimeout(\"location.href = '../../admin';\",1500);</script>"; ?>
        </p>
    </div>
  </div>
  </div>
<?php }

function student_login()
{

  $_SESSION['logged'] = "1";
  //$_SESSION['role'] = "users";
  // For student/user login set role and store identifying info
  $_SESSION['role'] = 'users';
  $_SESSION['email'] = $_POST['email_address'];
  $_SESSION['avator'] = $GLOBALS['avator'] ?? '';
  // Bind and harden session
  $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
  $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
  session_regenerate_id(true);
}
?>