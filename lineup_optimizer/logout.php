<?php
    // access current session
	session_start();
	
	// if logged in start steps to logout
	if(isset($_SESSION['user'])) {
		
		session_unset();
		session_destroy();
		session_write_close();
		
		// reference via session.name PHPSESSID property in PHP.INI file
		setcookie(session_name(), '', 0, '/');
		
		session_regenerate_id(true);
		
	
		
	}
	
	header('location: index.php');
	exit();
	

?>