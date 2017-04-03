<?php 
class Session{

    /**
    * Rweb E.O.R (rancell2002ng@yahoo.com) 
    * @tell: 2348055447489
    * @You may copy and use but make sure you don't 
    * @remove this notice. all right reserved
    * @access public
    */

    function start () {
    	//initialize the session if session is not started
		if (!isset($_SESSION)) {
		  session_start();
		}
        
    }

       //function to clean string but leaving any space 
	function clean($str){
		return trim(htmlspecialchars(strip_tags($str)));
	}


    /**
    * Deletes a session variable
    * @param string name of variable
    * @return boolean
    * @access public
    */
    function del ($name) {
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


}
?>