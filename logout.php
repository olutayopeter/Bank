<?php
require_once('classes/session.php');
# check if session is started, if not; start session
if(!isset($_SESSION)){
	Session::start();
}

if (!isset($_SESSION['user_name'])){ 
header('location: index.php');
exit;
}

function del($name) {
        if ( isset ( $_SESSION[$name] ) ) {
        	$_SESSION[$name] = NULL; //clear the session varialbles
            unset ( $_SESSION[$name] );
            return true;
        } else {
            return false;
        }
    }

function destroy() {
        $_SESSION = array();
        session_destroy();
    }

$deltable = del('tablename');
$delsender = del('sender');
$delmsg = del('msg');
$delreceipent = del('receipent');
$delfdate = del('fdate');
$deltdate = del('tdate');
$delemail = del('email');
$delbank_image = del('bank_image');
$delcountry_name = del('country_name');
$delprefixlen = del('prefixlen');
$delfds = Session::del('fds');// to clear the session set on union bank page
$delfd2s = Session::del('fd2s');// to clear the session set on union bank page
$deluonly = Session::del('uonly');// to clear the session set on union bank page

destroy();
header("location: index.php");
exit;
?>