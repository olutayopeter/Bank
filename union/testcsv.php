<?php
require_once('../connet/conn.php');
require_once('../classes/session.php');
if(!isset($_SESSION)){
	Session::start();
}

if (!isset($_SESSION['user_name']) && ($_SESSION['tablename'] !="unionbank_tb")){
session_destroy();
header("Location: ../logout.php");
exit;
}

// file name for csv download
  $filename = "Union_Transaction_Report_" . date('Ymd') . ".csv";
  
  // Get user's input 
 $fd=session::clean($_GET['xx']); 
$fd2=session::clean($_GET['xy']); 
 //$fd='2010-01-01 00:00:00';
//$fd2='2010-12-29 23:59:59';
$delfds = Session::del('fds');
$delfd2s = Session::del('fd2s');

 header("Content-Disposition: attachment; filename=\"$filename\"");
 header("Content-Type: application/vnd.ms-excel");

  $flag = false;
   // Check if search parameter is empty?
  if (!empty($fd2) && !empty($fd)){
   $result = mysql_query("SELECT AccountNo,rto,COUNT(*) FROM uniontemp WHERE rdate BETWEEN '$fd' AND '$fd2' GROUP BY AccountNo, rto") or die(mysql_error());

  echo '"' ."Account_No:" . "\",";
    echo '"' . "Dest_Address:" . "\",";
	  echo '"' ."Count:". "\"\n";
  while($row = mysql_fetch_row($result)) {
        print '"' . stripslashes(implode('","',$row)) . "\"\n";
    }
  }
   // End of pushcsv
   
?>
