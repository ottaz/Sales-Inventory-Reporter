<?php

/**
 * code to maintain our session based on the idle time of 15 mins,
 * i.e. if we do not send a request to the server within 15 mins of
 * our most recent request, we start a new session.
 */


function checksession(){


    if(!isset($_SESSION))
    {
        session_start();
    }

	// are we starting a session for the very first time?
	if ($_SESSION['idle']) {
		
		
		if (time() >= $_SESSION['idle']){
			$_SESSION['expired'] = true;
		}
		else {
			$_SESSION['idle'] = time()+15*60;
		}
	

		if ($_SESSION['expired'] == true ){
			$_SESSION = array();
			session_destroy();
			session_start();
			$_SESSION['idle'] = time()+15*60;
			$_SESSION['expired'] = false;
			$_SESSION['cookies'] = array();
		} 
	}
	
	// set idle and expired time since this is the very first session	
	else {
		$_SESSION['idle'] = time()+15*60;
		$_SESSION['expired'] = false;
		$_SESSION['cookies'] = array();
	}
	
}

?>