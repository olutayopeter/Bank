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

$uname=strtolower($_SESSION['user_name']);
if($_SESSION['tablename'] =="unionbank_tb"){
$_SESSION['uonly']="<a href='union/index.php' class='Normal-Text-BlackSmall'>Transaction Summary</a>";
}
function run_sql($str){
	
	return mysql_query($str);
	
}

function get_rows($_res_id){
	
	return mysql_num_rows($_res_id);	
	
}


function get_records($_res_id){
	
	return mysql_fetch_array($_res_id);	
	
}

# remove empty space 
function clean_numb($input,$rwebs='') {
     $string = eregi_replace("[^0-9,x".$rwebs."]", '', $input);
     return preg_replace('/\s+/','', $string);
   }   

#initialize sql stmt
	$_sql2 ="SELECT * FROM country_operator_code ORDER BY id ASC";
	#run the sql and return a value
	$_res_id2 = run_sql($_sql2);
	#get the no of rows fetched
	$_nrows2 = get_rows($_res_id2);
	#if the rows fetch is greater than 0 fetch all records
	if ($_nrows2 > 0) {
		$_rows_rs = get_records($_res_id2);	
	}

   $table_name = $_SESSION['tablename'];
   $getuser = $_SESSION['user_name'];
	$gname = stripslashes($_GET['msgcon']);
	$_SESSION['msg'] = $gname;
        $dstatus =  stripslashes($_GET['status']);
	$_SESSION['status'] = $dstatus;
	$country_code = stripslashes($_GET['country_name']);
  // echo $country_code;
    $code_array = explode('^', $country_code);
    $c_name = $code_array[0];
    $c_prefix_c =clean_numb($code_array[1]);
 // echo $c_prefix_c;
    $c_prefix_code=trim($c_prefix_c);
	$prefixlen = strlen($c_prefix_c);
	$c_prefix= "'".$c_prefix_code."'";
    
	$_SESSION['country_name'] =$c_prefix;
	$_SESSION['prefixlen'] =$prefixlen;
    $c_op_img = $code_array[2];
	$logo=explode(",",$c_op_img);
    $logo2=$logo;
 if (empty($logo2))
	{$Oper=""; $logo2="NULL";}
	else
	{
	 $res_logo=count($logo2);
	if ($res_logo <= 1){$Oper="Operator :";} else {$Oper="Operators :";}
	} 
	
    $rfrom = stripslashes($_GET['textfield2']);
	$_SESSION['sender'] = $rfrom;
	$senderid = $_SESSION['sender'];
	$receipent = stripslashes($_GET['textfield']);
	//$receipent = stripslashes($receipent);
	$_SESSION['receipent'] = $receipent;	
     $sdate = $_GET['txtfrom'];
	$_SESSION['fdate'] = $sdate;
     $edate = $_GET['txtto'];
	$_SESSION['tdate'] = $edate;

# if submitted, then process
if ($_GET['Submit']){

if (isset($_GET['checkbox']) && ($_GET['checkbox'] == "csv")){
    $gname = $_GET['msgcon'];
    $_SESSION['msg'] = $gname;
    	$dstatus = stripslashes($_GET['status']);
	$_SESSION['status'] = $dstatus;
	$country_code = stripslashes($_GET['country_name']);
	$code_array = explode('^', $country_code);
    $c_name = $code_array[0];
    $c_prefix_c =clean_numb($code_array[1]);
    $c_prefix_code=trim($c_prefix_c);
	$prefixlen = strlen($c_prefix_c);
	$c_prefix= "'".$c_prefix_code."'";
    
	$_SESSION['country_name'] =$c_prefix;
	$_SESSION['prefixlen'] =$prefixlen;
	
$rfrom = $_GET['textfield2'];
$_SESSION['sender'] = $rfrom; 
$receipent = $_GET['textfield'];
$_SESSION['receipent'] = $receipent;
$sdate = $_GET['txtfrom'];
$_SESSION['fdate'] = $sdate;
$edate = $_GET['txtto'];
$_SESSION['tdate'] = $edate;
header("Location: pushcsv.php");
}

//==========================================


//==================================================
$countQuery = "SELECT count(*) as numb from  $table_name WHERE rdate >= '$sdate' and rdate <= '$edate'";

$dQuery = "SELECT CASE 
WHEN timelag < 1 THEN 'Below One Minute' 
WHEN timelag BETWEEN 1 and 5 THEN 'Between 1 and 5 Mins' 
WHEN timelag BETWEEN 5 and 15 THEN 'Between 5 and 15 Mins' 
WHEN timelag BETWEEN 15 and 30 THEN 'Between 15 and 30 Mins' 
WHEN timelag BETWEEN 30 and 60 THEN 'Between 30 and 60 Mins' 
WHEN timelag BETWEEN 60 and 120 THEN 'Between 60 and 120 Mins' 
WHEN timelag BETWEEN 120 and 360 THEN 'Between 120 and 360 Mins' 
WHEN timelag > 360  THEN 'Greater Than 360 Mins' 
WHEN timelag IS NULL THEN 'Unavailable yet'
END as Time_Range, count(*) as Count  
FROM (select TIME_TO_SEC(timediff(deliverytime,rdate)/ 60) as timelag from $table_name WHERE rdate >= '$sdate' and rdate <= '$edate'";


if (strlen(trim($senderid)) > 0){
$dQuery= $dQuery." and rfrom = '".$senderid."'";
$countQuery= $countQuery." and rfrom = '".$senderid."'";
}

if (strlen(trim($receipent)) > 0){
$dQuery= $dQuery." and rto = '".$receipent."'" ;
$countQuery = $countQuery." and rto = '".$receipent."'" ;
}

if (strlen(trim($gname)) > 0){
$dQuery= $dQuery." and message LIKE '%".$gname."%'";
$countQuery = $countQuery." and message LIKE '%".$gname."%'";
}

if ((substr($c_prefix,0,1) != "x") && (strlen(trim($c_prefix)) > 0)){
	 $dQuery= $dQuery." and substring(`rto`,1,$prefixlen) = $c_prefix";
	 $countQuery= $countQuery." and substring(`rto`,1,$prefixlen) = $c_prefix";
}
     if (strlen(trim($dstatus)) > 0 && (trim($dstatus) !='others')){
	
	 $dQuery= $dQuery." and status = ".$dstatus;
	 $countQuery= $countQuery." and status = ".$dstatus;
	
	}else if(trim($dstatus) == 'others'){
		
	$dQuery= $dQuery." and status not in (0,1,2,3)";
	$countQuery= $countQuery." and status not in (0,1,2,3)";
	}
$dQuery = $dQuery.") as derived GROUP BY Time_Range";
$dQuery;
$result = mysql_query($dQuery,$cn);
$countresult = mysql_query($countQuery,$cn);

//************************************************************************************
	
}
mysql_close($cn);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<!-- saved from url=(0043)http://sms.cellulant.com.ng/sms/reports.php -->
<HTML xmlns="http://www.w3.org/1999/xhtml"><HEAD><TITLE>Cellulant :: Reporting Portal </TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<LINK href="Cellulant%20Bulk-SMS%20Accounts_files/bulksms.css" type=text/css rel=stylesheet>
<LINK title=win2k-1 media=all href="Cellulant%20Bulk-SMS%20Accounts_files/calendar-win2k-1.css" type=text/css rel=stylesheet>
<SCRIPT src="Cellulant%20Bulk-SMS%20Accounts_files/calendar.js" 
type=text/javascript></SCRIPT>
<script language="javascript" src="calendar.js"></script>
<SCRIPT src="Cellulant%20Bulk-SMS%20Accounts_files/calendar-en.js" 
type=text/javascript></SCRIPT>

<SCRIPT src="Cellulant%20Bulk-SMS%20Accounts_files/calendar-setup.js" 
type=text/javascript></SCRIPT>

<SCRIPT language=JavaScript 
src="Cellulant%20Bulk-SMS%20Accounts_files/jsFuncs.js" 
type=text/JavaScript></SCRIPT>
<link href="style.css" rel="stylesheet" text = "text/css">
<STYLE type=text/css>
.forms {
	BORDER-RIGHT: #0066cc 1px solid; BORDER-TOP: #0066cc 1px solid; FONT-WEIGHT: normal; FONT-SIZE: 10px; BORDER-LEFT: #0066cc 1px solid; COLOR: #383687; BORDER-BOTTOM: #0066cc 1px solid; FONT-STYLE: normal; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif; BACKGROUND-COLOR: #f1f1f8; TEXT-DECORATION: none
}
#operator_logo { float:left; max-width:500px; }
.formsBlue {
	BORDER-RIGHT: #44618f 1px solid; PADDING-RIGHT: 2px; BORDER-TOP: #44618f 1px solid; PADDING-LEFT: 2px; FONT-WEIGHT: bold; FONT-SIZE: 11px; PADDING-BOTTOM: 2px; BORDER-LEFT: #44618f 1px solid; COLOR: #ffffff; PADDING-TOP: 2px; BORDER-BOTTOM: #44618f 1px solid; FONT-STYLE: normal; FONT-FAMILY: Tahoma; BACKGROUND-COLOR: #0447b7; TEXT-DECORATION: none
}
.big { color:#FFF; font-size:19px; font-weight:normal; font-family:Arial, Helvetica, sans-serif;}
#logo_out { text-align:center; background-color:#FFFAFA; font-weight:normal; font-size:15px;}
.clrdivs2 {
line-height:1px;
font-size:1px;
clear:both;
}
#name_country { font-weight:normal; text-align:center; font-size:15px; background-color:#FFFAFA; }
.cntcolor { color: #F60;} 
</STYLE>

<META content="MSHTML 6.00.6000.20815" name=GENERATOR>
<link href="calendar-win2k-1.css" rel="stylesheet" type="text/css">
<link href="bulksms.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/calendar-en.js"></script>
<script type="text/javascript" src="js/calendar-setup.js"></script>
<script language="JavaScript" type="text/JavaScript" src="js/jsFuncs.js"></script>
<script language="JavaScript" type="text/JavaScript" src="js/time_zones.js"></script>
<script language="JavaScript" type="text/JavaScript">
	var staticMessage = '';
	var utext1 ="";
	var smslimit=612;
	var targetentries=0;
	var credits=0;
	var serverTime="2011 01 14 12 24 29";
	var serverTimeZone="+0100";
	var timeZoneID=19;


function smsLength(charcount)
{
	var n=charcount;
	var c = 0;
	if(n <= 160)
	{
		c=1;
	}
	else
	{
		c= Math.ceil(n /153);
	}
	return c;
}

function createmessage(vtxt1){
	return vtxt1;
}

function creditsConsumed()
{
	var n = messageLength(utext1);
	var c = smsLength(n);
	var units=targetentries * c;

	document.getElementById('CreditCounter').innerHTML =  units + " C";
}

function isValidSOA(evt)
{
		var validChars = "1234567890";
				validChars +="abcdefghijklmnopqrstuvwxyz+,.-_ ABCDEFGHIJKLMNOPQRSTUVWXYZ";
				var keynum = (evt.which) ? evt.which : event.keyCode;
		if( keynum == 8)
			return true;
		var keychar = String.fromCharCode(keynum)
		var x = validChars.indexOf(keychar);
		//alert("char " + keychar + "["+keynum+"] found at "+ x);

		if(x >= 0)
			return true;
		return false;
}

function messageLength(message)
{
	var m_len = 0,c='',bad_char=" [\\]^`{|}~Â£Â¦â‚¤â‚¬",x=0;
	if(message.length>0)
	{
		do
		{
			c = message.substring(0,1);
			message = message.substring(1,message.length);
			x=bad_char.indexOf(c);

			if( x> 0)
				m_len++;
			m_len++;

		}while(message.length > 0);
	}
	return m_len;
}

function keepLimit(utn){
	var tex1 = document.formMessage.taMessage

	var jointmsg = createmessage(tex1.value);
	var n=messageLength(jointmsg);

	if(n > smslimit){
		tex1.value = utext1;
	}else{
		utext1 = tex1.value;
	}
	jointmsg = createmessage(tex1.value);
	n = messageLength(jointmsg);
	var c = smsLength(n);

	document.getElementById('MessageCounter').innerHTML = n + "[" + c + " sms]";
	creditsConsumed();

	return true;
}

function keepCount(utn){
	var tex1 = document.formMessage.taMessage

	var jointmsg = createmessage(tex1.value);
	var n = messageLength(jointmsg)

	if(n > smslimit){
		tex1.value = utext1;
		alert("Your have reached the maximum length for an SMS in your account, This is " +smslimit+ " characters");
		jointmsg = createmessage(tex1.value);
	}else{
		utext1 = tex1.value;
	}
	jointmsg = createmessage(tex1.value);
	n = messageLength(jointmsg);
	var c = smsLength(n);

	document.getElementById('MessageCounter').innerHTML = n + "[" + c + " sms]";
	creditsConsumed();

	return true;
}

function markCheck(checkbox,entries)
{
	//var entries = checkbox.value
	//eval("document.getElementById(\"TR" + checkbox.value + "\")");
	if (checkbox.checked) {
		var something=targetentries+entries;
		targetentries = something;
	}
	else
	{
		targetentries = (targetentries-entries);
	}

	if(targetentries < 0)
		targetentries = 0;

	document.getElementById('targetsdiv').innerHTML = "Targets ["+targetentries+"] selected";
	creditsConsumed();
	return true;
}

function canSend(button)
{
	var tex1 = document.formMessage.taMessage
	var tex2 = document.formMessage.txsourceNumber
	var rds = document.formMessage.rdStatus[0] // option for schedule.

	//alert(rds.checked);

	var jointmsg = createmessage(tex1.value);
	var n = messageLength(jointmsg);
	var c = smsLength(n);

	document.getElementById('MessageCounter').innerHTML = n + "[" + c + " sms]";
	creditsConsumed();

	if(n > smslimit){
		tex1.value = utext1;
		alert("Your have reached the maximum length for an SMS in your account, This is " +smslimit+ " characters");
		jointmsg = createmessage(tex1.value);
		return false;
	}else{
		utext1 = tex1.value;
		var units=targetentries*c;
		if(rds.checked)
		{
			if(units > credits)
			{
				alert("Your have insufficient credits to send this message to the selected targets, You have "+ credits +" units and you need "+ units +" to send this message Please review your message or schedule message as suspended and load more credits before you reschedule.");
				return false;
			}else if(units==0){
				alert("You message may either be too short or their are no targets selected, please review to confirm.");
				return false;
			}else if(tex2.value.length<4){
				alert("You source address is too short or not a valid source address.");
				return false;
			}else{
				//alert("This is a good message credits are " + units+ " n is "+n+" c is "+c+" targetentries is "+targetentries);
				button.value="OK";
				document.formMessage.submit();
				return true;
			}
		}
		else
		{
			alert("This message has been scheduled as suspended, please understand that it is NOT scheduled to be sent.");
			button.value="Suspended";
			document.formMessage.submit();
			return true;
		}
	}
}

function getLocalTime()
{
	var d = new Date();
	var dy = d.getFullYear();
	var dm = "" + (1 + d.getMonth());
	var dd = "" + d.getDate();

	var dh = "" + d.getHours();
	var di = "" + d.getMinutes();
	var ds = "00";

	// fix leading 0 error
	if(dm.length == 1){ dm = "0" + dm;}
	if(dd.length == 1){ dd = "0" + dd;}
	if(dh.length == 1){ dh = "0" + dh;}
	if(di.length == 1){ di = "0" + di;}

	return dy + " " + dm + " " + dd + " " + dh + " " + di + " " + ds;
	// we use spaces cause we will use this value again later
}

function start()
{
	document.getElementById('targetsdiv').innerHTML = "Targets ["+targetentries+"] selected";
	keepLimit();

	var localTime = getLocalTime().split(" ");//local client time
//set date time fields
    var tzn = document.formMessage.slTimeZone;
	loadTimeZones_select(tzn, timeZoneID);
// this is the preselected time zone this is Africa/Nairobi
}

</script>
</HEAD>
<BODY onLoad="start();" topmargin="0px" bottommargin="0px" rightmargin="0px" leftmargin="0px">
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TBODY>
  <TR>
    <TD><TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#f5f4e0 
        border=0><TBODY>
        <TR>
          <TD height="28">
            <TABLE cellSpacing="0" cellPadding="0" width="100%" 
            background="Cellulant%20Bulk-SMS%20Accounts_files/content_r1_c4_rancell.jpg" 
            border="0">
              <TBODY>
              <TR>
                <TD width="20%"><IMG height=28 alt="" 
                  src="Images/content_r1_c1_rancell.jpg" 
                  width=195 border=0 name=content_r1_c1></TD>
                <TD class=bodytext width="78%">&nbsp;</TD>
                <TD width="2%">
                  <DIV align=right><IMG height=28 alt="" 
                  src="Images/content_r1_c10.jpg" 
                  width="12" border="0" 
            name="content_r1_c10"></DIV></TD></TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD height="133">
            <TABLE cellSpacing="0" cellPadding="0" width="100%" 
            background="Images/content_r2_c8.jpg" 
            border="0">
              <TBODY>
              <TR>
                <TD width="40%" rowSpan=5 background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg">
                  <DIV align=left>&nbsp;</DIV></TD>
                <TD width="39%" background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg"><DIV align=right>Cellulant Nigeria - Reporting Portal</DIV></TD>
                <TD width="21%" rowSpan=6>
                  <DIV align="right"><IMG height="133" alt="<?php echo $_SESSION['bank_image'] ;?>" src="Images/<?php echo $_SESSION['bank_image'].'.jpg'; ?>" width="160"></DIV></TD></TR>
              <TR>
                <TD background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg"><DIV align=right><?php $today = date("F j, Y, g:i a"); echo $today ; ?></DIV></TD>
                </TR>
              <TR>
                <TD class="big" background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg"><DIV align=right><?php echo "<br>WELCOME: " .$_SESSION['user_name']; ?>&nbsp;</DIV></TD>
                </TR>
              <TR>
                <TD background="Cellulant%20Bulk-SMS%20Manage%20List_files/content_r2_c8.jpg">&nbsp;</TD>
                </TR>
              <TR>
                <TD style="text-align:right; margin-left:2px; color:#FFF; font-weight:bold;">		<?php
//$ransms =file_get_contents("http://94.229.79.115/GetSMSAcctBalanceForClient.php?USERNAME=$uname");
				# Retrieve balance from database
////$unme=$_SESSION['user_name'];
//$get_bal="SELECT credit FROM prepaid_smpp_account WHERE username ='$unme'";
//$get_bal_res = mysql_query($get_bal,$cn) or die(mysql_error());
//if (mysql_num_rows($get_bal_res) > 0) {

//}
//  else
//  { $ransms=0;}

echo "<span style=\"color:#000;\">SMS Units:</span> ".$ransms; ?>
</TD>
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
                    </TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD height=19>
            <TABLE height=17 cellSpacing=0 cellPadding=0 width="100%" 
            background=Cellulant%20Bulk-SMS%20Accounts_files/content_r4_c1.jpg 
            border=0>
              <TBODY>
              <TR>
                <TD>&nbsp;&nbsp;</TD>
                <TD width="37%">
                  <DIV align="right"></DIV></TD></TR>
              <TR>
                <TD colSpan="2">
                  <DIV align="center"><SPAN 
              class="style12"></SPAN></DIV></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></TR>
  <TR>
    <TD bgColor="#f5f4e0">
      <TABLE cellSpacing="5" cellPadding="1" width="95%" border="0">
        <TBODY>
        <TR>
          <TD width="147" height="247" valign="top"><TABLE class=work-area cellSpacing=1 cellPadding="1" width="149" border="0">
            <TBODY>
              <TR>
                <TH scope="col" width="9"> <DIV align="left"><IMG height=21 alt=l 
                  src="Images/tbl-tp-l.gif" 
                  width=6></DIV></TH>
                <TH scope=col width=121>Menu</TH>
                <TH scope=col width=17>&nbsp;</TH>
              </TR>
              <TR>
                <TD>&nbsp;</TD>
                <TD height="16" colSpan=2 background="accbal.php" class=sideLink><a 
                  href="logout.php" title="logout" class="Normal-Text-BlackSmall">Logout</a></TD>
              </TR>
              <TR>
                <TD>&nbsp;</TD>
                <TD height="16" colSpan=2 background="accbal.php" class=sideLink><a
                  href="stockpurchases.php" title="Bulk SMS Purchases" class="Normal-Text-BlackSmall">Bulk SMS Purchases</a></TD>
              </TR>
			  <TR>
            <TD>&nbsp;</TD>
                <TD height="16" colSpan=2 background="accbal.php" class=sideLink><a
                  href="statistics.php" title="Delivery Statistics" class="Normal-Text-BlackSmall">Delivery Statistics</a></TD>
              </TR>
              <TR>
            <TD>&nbsp;</TD>
                <TD height="16" colSpan=2 background="accbal.php" class=sideLink><a
                  href="monitor.php" title="Monitor Traffic" class="Normal-Text-BlackSmall">Monitor Traffic</a></TD>
              </TR>
 <TR>
                <TD>&nbsp;</TD>
                <TD height="16" colSpan="2" class="sideLink"><?php echo $_SESSION['uonly']; ?></TD>
              </TR>
            </TBODY>
          </TABLE></TD>
          <TD width="872" valign="top" bgColor="#f5f4e0"><table width="100%" border="0" align="center">
  <tr>
    <td height="21" colspan="3" bgcolor="#FF6600" class="Normal-Text"><strong>Sent Report</strong></td>
    </tr>
  <tr>
    <td colspan="3"><form id="form1" name="form1" method="get" action="">
      <table width="125%" border="0" align="center">
            <tr>
              <td height="47" colspan="2" align="left" valign="top" class="Normal-Text-Black"><strong>Help<BR>
              </strong>
Select   a Date Range To Retrieve Record. Additionally, You Can 
Search Using   SenderID or The Recipients.  
Check &quot;Download&quot; To Get 
The Search As An Excel CSV Document. </td>
              <td width="54%" rowspan="10" valign="top">
              <div id="operator_logo">
              <div id="name_country">Country Search: &nbsp;<span class="cntcolor">
               <?php     $con_name=trim($c_name);
			   		    if (!empty($con_name)){
				        $disp=$con_name;
					    echo $disp;
					    } 
				    else {
						 $disp="Non Selected";
					     echo $disp; 
						}
			   ?>
              </span></div>
              <div id="clrdivs2">&nbsp;</div>
              <div id="logo_out"><?php echo $Oper ;?> &nbsp;
                <?php
			 if ($logo2 !="NULL"){
                 foreach ($logo2 as $value) {
                 echo " <img src=\"Images/".$value.".png\" width=\"\" height=\"40px\" alt=\" \" border=\"0\">";
		                 }
			     }
				 else 
				    {echo " &nbsp;";}
              ?>
              </div>
              </div></td>
            </tr>
            <tr>
          <td align="right" valign="middle" class="Normal-Text-Black"><strong>From</strong></td>
          <td><input name="txtfrom" type="text" class="formsBr" id="txSheduleDate" size="24" maxlength="30" />
                      &nbsp;<a href="#"><img border="0" width="20" height="20" src="images/Calendar.gif" id="cxStartDate" alt="Date" /></a>
                        <script type="text/javascript">
								Calendar.setup(
								{
									inputField 	   : "txSheduleDate", // ID of the input field
									ifFormat 	   : "%Y-%m-%d %H:%M:00", // the date format
									showsTime      : true,
									timeFormat     : "24",
									button 		   : "cxStartDate" // ID of the button
								}
							);
							</script>
                        &nbsp;<a href="#" ></a>
                        <script type="text/javascript">
								Calendar.setup(
								{
									inputField 	   : "txSheduleDate", // ID of the input field
									ifFormat 	   : "%Y-%m-%d %H:%M:00", // the date format
									showsTime      : true,
									timeFormat     : "24",
									button 		   : "cxStartDate" // ID of the button
								}
							);
							</script></td>
          </tr>
        <tr>
          <td align="right" valign="middle" class="Normal-Text-Black"><strong>To</strong></td>
          <td><input name="txtto" type="text" class="formsBr" id="txSheduleDate1" size="24" maxlength="30" />
                      &nbsp;<a href="#"><img border="0" width="20" height="20" src="images/Calendar.gif" id="cxStartDate1" alt="Date" /></a>
                        <script type="text/javascript">
								Calendar.setup(
								{
									inputField 	   : "txSheduleDate1", // ID of the input field
									ifFormat 	   : "%Y-%m-%d %H:%M:00", // the date format
									showsTime      : true,
									timeFormat     : "24",
									button 		   : "cxStartDate1" // ID of the button
								}
							);
							</script>
                        &nbsp;<a href="#" ></a>
                        <script type="text/javascript">
								Calendar.setup(
								{
									inputField 	   : "txSheduleDate1", // ID of the input field
									ifFormat 	   : "%Y-%m-%d %H:%M:00", // the date format
									showsTime      : true,
									timeFormat     : "24",
									button 		   : "cxStartDate1" // ID of the button
								}
							);
							</script></td>
        </tr>
                <tr>
          <td width="22%" align="right" valign="middle" class="Normal-Text-Black"><strong>Country/Operators</strong></td>
          <td width="24%">
                          <select name="country_name" id="country_name">
                          <option selected="selected" value="x">All Countries</option>
                          <?php  				  
				  
						  do{  
						       ?>
                          <option class="blac" value="<?php echo $_rows_rs['country'].'^'.$_rows_rs['prefix'].'^'.$_rows_rs['op_image'];  ?>"><span class="blac"><?php echo $_rows_rs['country'];  ?> </span></option>
                          <?php  } while($_rows_rs = get_records($_res_id2)); ?>
                        </select>
            </td>
          </tr>
          <tr>
          <td width="22%" align="right" valign="middle" class="Normal-Text-Black"><strong>Status</strong></td>
          <td width="24%"><select name="status" class="blue">
            <option selected value="">All Status</option>
            <option value="1">Delivered</option>
            <option value="2">Undelivered</option>
            <option value="3">Failed</option>
            <option value="0">Submitted To Network</option>
            <option value="others">Unknown</option>
          </select></td>
          </tr>
        <tr>
          <td width="22%" align="right" valign="middle" class="Normal-Text-Black"><strong>Sender ID </strong></td>
          <td width="24%">
            <input type="text" name="textfield2" id="textfield2">
          </td>
          </tr>
        <tr>
          <td align="right" valign="middle" class="Normal-Text-Black"><strong>Recipient</strong></td>
          <td>
            <input type="text" name="textfield" />
          </td>
          </tr>
        <tr>
          <td align="right" valign="middle" class="Normal-Text-Black"><strong>Msg Contains </strong></td>
          <td><label><input name="msgcon" type="text" size="40" maxlength="78" /></label></td>
          </tr>
        <tr>
          <td><input type="checkbox" name="checkbox" value="csv">
             Download (CSV) 
               <label></label></td>
          <td><input name="Submit" type="submit" class="button1" id="Submit" value="Search for Record" /></td>
        </tr>
        <tr>
          <td colspan="2"><label></label></td>
          </tr>
         <td height="14" colspan="2">&nbsp;</td>
          </tr>

      </table>
      
      
      <!---Rancell -->
	  
      
    </form></td>
  </tr>
<tr><td >
<b>Country :</b> <?PHP if( strlen($c_prefix) < 3) echo "All"; else echo $c_prefix; ?><br>
<b>start date:</b> <?PHP echo $sdate; ?><br>
<b>End date:  </b> <?PHP echo $edate; ?>
</td></tr>
  <tr>
    <td>   
     
<table width="400" border="1" bordercolor="#ffffff" cellpadding ="1" cellspacing="1" bgcolor="#CCCCCC">
<tr>
<td height="20" align="center" valign="middle" bgcolor="#FF6600" class="Normal-Text" ><strong>TIME-RANGE</strong></td>
<td height="20" align="center" valign="middle" bgcolor="#FF6600" class="Normal-Text" ><strong>COUNT</strong></td>
<td height="20" align="center" valign="middle" bgcolor="#FF6600" class="Normal-Text" ><strong>PERCENTAGE</strong></td></tr>
<?php

$totalMessg = 0;
$msg_count = 1;
while($info = mysql_fetch_array($countresult)) {
$totalMessg = $info['numb'];
}
while($info = mysql_fetch_array($result)) { 
 
 
 ?>

<tr><td height="25" ><?php echo $msg_count.'. '.$info['Time_Range']; ?></td>
<td height="25" ><?php echo $info['Count']; ?></td>
<td height="25" ><?php echo round((($info['Count']/$totalMessg)*100),2).'%'; ?></td>
</tr>
<?php
$msg_count++;
}

?> 
<tr><td height="25" ><b>Total:<b/></td>
<td height="25" ><b><?php echo $totalMessg; ?></b></td>
<td height="25" >100%</td>
</tr>
</table>
  </td>
  </tr>
  <tr><td colspan="3"><?php  echo $pagination; ?></td></tr>
  <tr>
    <td colspan="3">&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table></TD>
        </TR>
        <TR>
          <TD colSpan=3>
            <TABLE cellSpacing=0 cellPadding=1 width="100%" bgColor=#f5f4e0 
            border=0>
              <TBODY>
              <TR>
                <TD>
                  <HR width="100%" SIZE=1>                </TD></TR>
              <TR>
                <TD>
                  <DIV style="COLOR: #999999" align=center></DIV></TD></TR>
              <TR>
                <TD>
                  <DIV align=center><A href="http://www.cellulant.com.ng/"> © Cellulant 2007-2012</A></DIV></TD></TR>
                  </TBODY>
                  </TABLE></TD></TR>
                  </TBODY>
                  </TABLE>
  </TR></TBODY></TABLE>
  </BODY>
  </HTML>
