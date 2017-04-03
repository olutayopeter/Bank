<html>
<head>
<title>Stock Purchases :: Cellulant Nigeria</title>
<link rel="stylesheet" type="text/css" href="stockpurchases.css"/>
</head>

<body>
<div id='stockpurchase'>
<div id="header">
<div id='topHeader'><div id='tleft'></div><div id='trepeat'></div><div id ='tright'></div></div>
<div id='bottomHeader'><div id='brepeat'></div><div id ='bright'></div></div>
</div>
<div id='menulink'>
<div id='menu'>
<ul>
<li class='menuclass'>Menu</li>
<li><a href="logout.php" title=reports class="Normal-Text-BlackSmall">Logout</a></li>
<li><a href="stockpurchase.php" title=reports class="Normal-Text-BlackSmall">Bulk SMS Purchases</a></li>
<li><a href="monitor.php" title="Monitor Traffic" class="Normal-Text-BlackSmall">Monitor Traffic</a></li>
</ul>

</div>
</div>
<div id='stk'>
<h3>STOCK PURCHASE</h3>
<table>
<thead>
<tr><th>PURCHASE DATE</th><th>UNITS BOUGHT</th></tr>
</thead>
<?php
SESSION_START();
	if (!isset($_SESSION['user_name'])) :
     // echo "error: not loged in or session expired.";
      //echo "<br>". session_id();
	  session_destroy();
      header( "Location:index.php");
	  exit;
	else:
      //echo "sucessfuly verified login on another page";
	  $username = $_SESSION['user_name'];
      //echo "<br>". session_id();
	endif;

$db_host = '94.229.79.114';
$db_user = 'kannel';
$db_pwd = 'secret';

$database = 'smppDB';
$table = 'stockpurchases';
$total = 0;

if (!mysql_connect($db_host, $db_user, $db_pwd))
    die("Can't connect to database");

if (!mysql_select_db($database))
    die("Can't select database");

// sending query
$result = mysql_query("SELECT purchase_date, units_bought FROM {$table} where username = '{$username}'");
if (!$result) {
    die("Query to show fields from table failed");
}

while($row = mysql_fetch_row($result))
{
$_status = $row[0];
 $_count = $row[1];
 $total = $total + $_count;
    echo "<tr>";
	
        echo "<td>$_status</td>";
		echo "<td>".number_format($_count,2)."</td>";

    echo "</tr>\n";
}
mysql_free_result($result);
?>
</table>
<div style='padding:5px'><b> Total Purchases: </b><?php echo number_format($total,2); ?></div>
</div>


</div>
<div id='footer'><a href='http://www.cellulant.com.ng'>&copy; Cellulant 2007-2011</a></div>
</body>
</html>