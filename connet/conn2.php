<?php
 error_reporting(1);
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_cn = "78.110.169.220";
$database_cn = "smppDB";
$username_cn = "kannel";
$password_cn = "secret";
//$cn = mysql_connect($hostname_cn, $username_cn, $password_cn) or die(mysql_error());
$cn = mysql_connect($hostname_cn, $username_cn, $password_cn) or  trigger_error(mysql_error(),E_USER_ERROR);
  // echo('Error connecting');
//trigger_error(mysql_error(),E_USER_ERROR);
//echo('Eror');
mysql_select_db($database_cn) or die(mysql_error());

?>
