<?php
//session_start();
//error_reporting(0);
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
//$hostname_cn = $_SESSION['server_ip'];
$hostname_cn = "78.110.169.220";
#$hostname_cn = "localhost";
//echo 'the server ip'.$hostname_cn;
//$database_cn = $_SESSION['database_name'];
$database_cn = "smppDB";
//echo 'the database name'.$database_cn;
//$username_cn = "root";
//$password_cn = "";
$username_cn = "kannel";
$password_cn = "secret";
$cn = mysql_connect($hostname_cn, $username_cn, $password_cn) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_cn);
?>
