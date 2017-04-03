<?PHP
 SESSION_START();
if (!isset($_SESSION['user_name'])) :
     // echo "error: not loged in or session expired.";
      //echo "<br>". session_id();
	  session_destroy();
      header( "Location:index.php");
	  exit;
else:
      //echo "sucessfuly verified ligin on another page";
      //echo "<br>". session_id();
endif;
  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.

  function cleanData(&$str)
  {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  // file name for download
  $filename = "website_data_" . date('Ymd') . ".csv";
   //$filename = "website_data_" . date('Ymd') . ".doc";
// Get the user's input from the form 
   $user_name = $_POST['user_name']; 
   $password = $_POST['password'];
   //$password= md5($password);
   
   $getuser = $_SESSION['user_name'];
   $gname = $_SESSION['msg'];
    $senderid = $_SESSION['sender'];
	
	$receipent = $_SESSION['receipent'];
    $sdate = $_SESSION['fdate'];
    $edate = $_SESSION['tdate'];
   
$db_user ="root";
$db_pass ="";

$connection = mysql_connect( 'localhost', $db_user, $db_pass );
mysql_select_db('message', $connection);
  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: application/vnd.ms-excel");

  $flag = false;
  
  //$result = mysql_query("SELECT * FROM cellmessage WHERE username = '$getuser' and rdate >= '$sdate' and rdate <= '$edate' ORDER BY mid") 
   //or die(mysql_error());
  $result = mysql_query("SELECT rfrom,rto,rdate,status,username,message,receipent,senderid FROM cellmessage WHERE username = '$getuser'ORDER BY mid") or die('Query failed!');
 if ((empty($senderid )) && (empty($receipent )) && (empty($gname))):
   $result = mysql_query("SELECT rfrom,rto,rdate,status,username,message,receipent,senderid FROM cellmessage WHERE username = '$getuser' and rdate >= '$sdate' and rdate <= '$edate' ORDER BY mid") 
   or die(mysql_error());
 //echo "The field was empty";  
 elseif ((empty($senderid )) && (empty($receipent ))):
  $result = mysql_query("SELECT rfrom,rto,rdate,status,username,message,receipent,senderid FROM cellmessage WHERE username = '$getuser' and rdate >= '$sdate' and rdate <= '$edate' and message LIKE '%$gname%' ORDER BY mid") 
    or die(mysql_error());
  
 elseif ((empty($gname)) && (empty($receipent ))):
   $result = mysql_query("SELECT rfrom,rto,rdate,status,username,message,receipent,senderid FROM cellmessage WHERE username = '$getuser' and rdate >= '$sdate' and rdate <= '$edate' and senderid = '$senderid'     ORDER BY mid") or die(mysql_error());
 elseif ((empty($gname )) && (empty($senderid ))):
   $result = mysql_query("SELECT rfrom,rto,rdate,status,username,message,receipent,senderid FROM cellmessage WHERE username = '$getuser' and rdate >= '$sdate' and rdate <= '$edate'  and  receipent = '$receipent ' ORDER BY mid") or die(mysql_error());
 endif;
  while(false !== ($row = mysql_fetch_row($result))) {
    if(!$flag) {
      // display field/column names as first row
    
	  echo 'FROM'."\t".'TO'."\t".'DATE'."\t".'STATUS'."\t".'USERNAME'."\t".'MESSAGE'."\t".'RECEIPIENT'."\t".'SENDER ID'."\n";
	 //echo implode("\t", ($row)) . "\n";
      $flag = true;
    }
    array_walk($row, 'cleanData');
    echo implode("\t", ($row)) . "\n";
  }


?>