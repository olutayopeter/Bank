<title>Cellulant :: Reporting Portal</title>
<?php 
// call session class 
error_reporting(1);
require_once('classes/session.php');
//echo("Got some results"); die('dying');
require_once('connet/conn2.php');
//require_once('DBConnections.php');
//echo('connecting');
//$cn = mysql_connect("78.110.169.220", "kannel", "secret") or die('unable to connect');
//echo('connected');
# check if session is started, if not; start session
//echo("Got some results"); die('dying');
if(!isset($_SESSION)){
	Session::start();
}
header("Cache-control: private"); //IE 6 Fix 


function clean($str){
		return trim(htmlspecialchars(strip_tags($str)));
	}
function del($name){
        if (isset($_SESSION[$name])){
        	$_SESSION[$name] = NULL; //clear the session varialbles
            unset($_SESSION[$name]);
            return true;
        } else {
            return false;
        }
    }

   $user_name = clean($_POST['user_name']); 
   $password = clean($_POST['password']);
//echo("before query"); die('dying');
//$transaction = new Transaction();
//$transaction->getConnection();
$query ="select * from login where username='$user_name' and password ='$password'";
$result = mysql_query($query,$cn);
$affected_rows = mysql_num_rows($result);
$result_set = mysql_fetch_array($result);
if ($affected_rows == 1) :

      $_SESSION['user_name'] = $result_set['username'];
	  $_SESSION['email'] = $result_set['email'];
	  $_SESSION['tablename'] = $result_set['tablename'];
	  $_SESSION['bank_image'] = $result_set['bank_image'];
	  $_SESSION['database_name'] = $result_set['database_name'];
	  $_SESSION['server_ip'] = $result_set['server_ip'];
      
            header( "Location: reports.php");
      
else :
      session_destroy();
      header("Location: index.php");
      print "not validated";
endif;

?>
