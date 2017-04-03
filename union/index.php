<?php
require_once('../classes/session.php');
set_time_limit(3600000); //setting the timeout to 1 hour
# start session if not set
if(!isset($_SESSION)){
	Session::start();
}
# Confirm user is logged in
if (!isset($_SESSION['user_name']) && ($_SESSION['tablename'] !="unionbank_tb")){
session_destroy();
header("Location: ../logout.php");
exit;
}
require_once('../connet/conn.php');

if($_POST["submit"]){	
$d=explode(" ",$_POST["demo2"]);
$f=explode("-",$d[0]);
$_SESSION['fds']=$fd=$f[2]."-".$f[1]."-".$f[0]." ".$d[1];
$d1=explode(" ",$_POST["demo1"]);
$f1=explode("-",$d1[0]);
$_SESSION['fd2s']= $fd2=$f1[2]."-".$f1[1]."-".$f1[0]." ".$d1[1];
//echo $fd.'<br />';
//echo $fd1;

$sqla="SELECT AccountNo,rto,COUNT(*) FROM uniontemp WHERE rdate BETWEEN '$fd' AND '$fd2' GROUP BY AccountNo, rto LIMIT 500";
//echo $sqla;
$result = mysql_query($sqla);
$show="<table border='1' width='500px'><br><br>";
$show.="<tr><td class=\"echoed_head\">Account Number</td><td class=\"echoed_head\">Destination Address</td><td class=\"echoed_head\">Count</td></tr>";

while($row = mysql_fetch_row($result)) {
    // $row is an associative array of columns in this iteration's row
    $var = $row[0];
    $var2 = $row[1];
    $var3 = $row[2];
$show.="<tr><td colspan=\"3\" class=\"linediv\">&nbsp;</td></tr>";
$show.="<tr><td class=\"result\">".$var."</td><td class=\"result\">".$var2."</td><td class=\"result\">".$var3."</td></tr>";
}
$show.="</table>";

} // end of if submit

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<script type="text/javascript" src="datetimepicker_css.js"></script>
<link href="rfnet.css" rel="stylesheet" type="text/css"/>
<link type="text/css" rel="stylesheet" href="datepickercontrol.css"/>
<link rel="stylesheet" type="text/css" href="style.css" />
<link href="tablelll.css" rel="stylesheet" type="text/css"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cellulant | Union Transaction Report</title>
<style media="all" type="text/css">
@import "menu/menu_style.css";
.mid { text-align:center; border-top:thin; border-top-color:#999;}
.blue { color:#03F; font-family:Verdana, Geneva, sans-serif; font-size:12px; font-weight:normal;}
.blue2 { color:#FFFFFF; background-color:#0CF; cursor:pointer; font-family:Verdana, Geneva, sans-serif; font-size:12px;}
.linediv {line-height:1px; background-color:#9CF;}
.result { color:#000; font-family:Arial, Helvetica, sans-serif; font-size:10px; text-align:center; font-weight:normal;}
.echoed_head { color:#F60; font-family:Arial, Helvetica, sans-serif; font-size:12px; text-align:center; font-weight:normal;}
td {
	text-align:center;
	border-bottom:thin;
	border-top:thin;
	border-left:thin;
	border-right:thin;
	color:#036;
	font-size:12px;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:normal;
}
</style>
<body bgcolor="orange" style="background-image:url(images/tbheader.jpg); background-repeat:repeat-x">
<table bgcolor="white" width="80%">
<tr>
<td>
<hr/>
<img src="images/CellulantLogo.gif" alt="Cellulant Logo" align="left"/>
<br/>
<br/>
<br/>
<table align="right">
<tr>
<td><div>
  <h2 style="color:#F60; font-size: 2em">Union Transaction Report</h2></div></td>
</tr>
</table>
<br/>
<br/>
<hr/>
<table width="100%" style="height: 20px; text-align:left; border: 2px solid #c3daf9">
<tr style="background-image:url(images/tbheader.jpg);">
<td align="right"><a href="../reports.php" title="Sent Report Page" class="blue">Previous Page</a> &nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="../logout.php" title="Logout" class="blue">Logout</a></td>
</tr>
</table>
<br/>
<br/>
<table width="90%">
<tr>
<td>
<form method="post" action="" enctype="multipart/form-data">
<table width="90%" style="border: 1px solid #c3daf9; font-weight:normal;">
<tr style="background-image:url(images/tbheader.jpg);">
<td colspan="2">
Select Transaction Date Range Below:
</td>
</tr>
<tr>
<td class="result">
<span class="blue">From</span> : <input id="demo2" name="demo2" size="25" value="<?php echo $_SESSION['fds'] ;?>" type="text"/><a href="javascript: NewCssCal('demo2','ddmmyyyy','arrow',true,24,false)">
<img src="images/cal.gif" width="16" height="16" alt="Pick a date"/></a></td>
<td class="result">
<span class="blue">To</span> : <input id="demo1" name="demo1" size="25" value="<?php echo $_SESSION['fd2s'] ;?>" type="text"/><a href="javascript: NewCssCal('demo1','ddmmyyyy','arrow',true,24,false)">
<img src="images/cal.gif" width="16" height="16" alt="Pick a date"/></a></td></tr>
<tr>
  <td colspan="2" class="mid">
    <input name="submit" id="submit" class="blue2" value="Query DataBase" type="submit"/></td>
 </tr>
<tr>
  <td colspan="2">&nbsp;</td></tr>
<tr>
  <td colspan="2" class="mid"><span class="blue">>></span>To Download CSV file, Click On -->> <a href="testcsv.php?xx=<?php echo $_SESSION['fds'] ;?>&xy=<?php echo $_SESSION['fd2s'] ;?>" title="Click Here To Download CSV File">"DOWNLOAD"</a> After You Have Queried The Database</td>
</tr>
</table>
</form>
</td>
</tr>
<tr>
<td>
</td>
</tr>
<tr>
<td><?php print $show; ?>
</td>
</tr>
</table>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>

<center>
<table>
<tr><td align="center">
<a href="http://www.cellulant.com.ng">Copyright 2012 by Cellulant Nigeria</a>
</td></tr>
</table>
</center>
</td>
</tr>
</table>
</body>
</html>
