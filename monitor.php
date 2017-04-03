<?php
require_once('classes/session.php');
require_once('connet/conn.php');
# check if session is started, if not; start session
if(!isset($_SESSION)){
	Session::start();
}

if (!isset($_SESSION['user_name'])){
header("Location: logout.php");
exit;
}
$tabname=$_SESSION['tablename'];
$monitor_call = "SELECT * FROM $tabname ORDER BY mid DESC LIMIT 20";
$monitor_call_res = mysql_query($monitor_call);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cellulant :: Monitor Traffic</title>
<STYLE type=text/css>
body {
	background-color:snow;
	background-repeat: repeat-x;
}

.clrdivs2 {
line-height:1px;
font-size:1px;
clear:both;
}
#colotext { color:#FFF;
font-family:Arial, Helvetica, sans-serif; 
}
.Normal-Text {color:#FFF; font-weight:bold;}
a {color:#00F;
	text-decoration:none;}
a:link{	text-decoration:none;
font-size:12px;
	}	
a:hover{ color:#fff;
	cursor:pointer;	
	}	
#main {
	   width:800px;
	   padding:0px;
	   margin:0px;
	   text-align:center;
	   background-color:#FFFAFA;
	   }
#content  {
	   width:100%;
	   padding:0px;
	   margin:0px;
	   text-align:center;
	   background-color:#fff;
	   color:#222;
	   font-size:12px;
	   font-family:Arial, Helvetica, sans-serif;
	   border:thin;
	   
	   }  
td { border-top:thin;  border-right: thin;}

</STYLE>
<script type="text/JavaScript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>
</head>
<body topmargin="0px" bottommargin="0px" onload="JavaScript:timedRefresh(5000);">
<center>
<div id="main">

<div id="content">
<TABLE cellSpacing="0" cellPadding="0" width="100%" 
            background="Images/content_r2_c8.jpg"
            border="0">
              <TBODY>
              <TR>
                <TD width="40%" rowSpan=5 background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg">
                  <DIV align="left" class="Normal-Text">&nbsp;&nbsp;TRAFFIC MONITORING PAGE <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="reports.php" title="Back To Report Page">Previous Page</a></DIV></TD>
                <TD width="39%" background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg"><DIV align=right>Cellulant Nigeria - Reporting Portal</DIV></TD>
                <TD width="21%" rowSpan=6>
                  <DIV align="right"><IMG height="133" alt="<?php echo $_SESSION['bank_image'] ;?>" src="Images/<?php echo $_SESSION['bank_image'].'.jpg'; ?>" width="160"></DIV></TD></TR>
              <TR>
                <TD background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg"><DIV align="right" class="Normal-Text"><?php $today = date("F j, Y, g:i a"); echo $today ; ?></DIV></TD>
                </TR>
              <TR>
                <TD class="big" background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg"><DIV align="right" class="Normal-Text"><?php echo "<br>WELCOME: " .$_SESSION['user_name']; ?>&nbsp;</DIV></TD>
                </TR>
              <TR>
                <TD background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg">&nbsp;</TD>
                </TR>
              <TR>
                <TD>&nbsp;</TD>
              </TR>
              <TR>
                <TD colSpan=2>
                  <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR>
                      <TD width="16%"><IMG height=23 alt=home src="Images/banner-bl-rancell.jpg" 
                        width=96></TD>
                      <TD width="2%"><IMG height=23 alt=- 
                        src="Images/menu-div.jpg" 
                        width=12></TD>
                      <TD vAlign=center noWrap align=middle width="17%"><A 
                        href="http://sms.cellulant.com.ng/sms/manageList.php"></A></TD>
                      <TD vAlign=center noWrap align=middle width="2%"><IMG 
                        height=23 alt=- 
                        src="Images/menu-div.jpg" 
                        width=12></TD>
                      <TD vAlign=center noWrap align=middle width="21%"><A 
                        href="http://sms.cellulant.com.ng/sms/messages.php"></A></TD>
                      <TD vAlign=center noWrap align=middle width="2%"><IMG 
                        height=23 alt=- 
                        src="Images/menu-div.jpg" 
                        width=12></TD>
                      <TD vAlign=center noWrap align=middle width="15%"><A 
                        href="http://sms.cellulant.com.ng/sms/users.php"></A></TD>
                      <TD vAlign=center noWrap align=middle width="2%"><IMG 
                        height=23 alt=- 
                        src="Images/menu-div.jpg" 
                        width=12></TD>
                      <TD vAlign=center noWrap align=middle width="21%"><A 
                        href="http://sms.cellulant.com.ng/sms/reports.php"></A></TD>
                <TD width="2%"><IMG height=23 alt=- 
                        src="Images/menu-div.jpg" 
                        width=12></TD>
                    </TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
<table width="800" border="1" bordercolor="#ffffff" cellpadding ="1" cellspacing="1" bgcolor="#CCCCCC">
<tr>
<td height="20" align="center" valign="middle" bgcolor="#FF6600" class="Normal-Text" width="700px"><strong>DATE</strong></td>
<td height="20" align="center" valign="middle" bgcolor="#FF6600" class="Normal-Text"  width="200px"><strong>FROM</strong></td>
<td height="20" align="center" valign="middle" bgcolor="#FF6600" class="Normal-Text"  width="1800px"><strong>MESSAGE</strong></td>
<td height="20" align="center" valign="middle" bgcolor="#FF6600" class="Normal-Text"  width="150px"><strong>RECIPIENT</strong></td>
<td height="20" align="center" valign="middle" bgcolor="#FF6600" class="Normal-Text"  width="100px"><strong>STATUS</strong></td></tr>
<?php

if (mysql_num_rows($monitor_call_res) > 0){
while ($info = mysql_fetch_assoc($monitor_call_res)) {
 $sta = $info['status'] ;
 if ($sta == 0)
 {
 $rsta = 'Submitted To Network';
 }
 elseif ($sta == 1)
 {
 $rsta = 'Delivered';
 }
 elseif ($sta == 2)
 {
 $rsta = 'Pending';
 }
 elseif ($sta == 3)
 {
 $rsta = 'Failed';
 }
 else
 {
 $rsta = 'Unknown';
 }
 
?>

<tr><td height="25"><?php echo $info['rdate']; ?></td>
<td height="25"><?php echo $info['rfrom']; ?></td>
<td height="25"><?php echo $info['message']; ?></td>
<td height="25"><?php echo $info['rto']; ?></td>
<td height="25"><?php echo $rsta; ?></td></tr>
<?php
   }
}
?> 
<tr><td colspan="5" align="center"><a href="http://www.cellulant.com.ng/">© Cellulant 2007-2011</a>
</table>
</div>
</div>

</center>
</body>
</html>
