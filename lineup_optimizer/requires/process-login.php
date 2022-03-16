<?php

	$errors = array();
  	
	try{
				
		$user_name = trim($_POST['user_name']);				
		$password = trim($_POST['password']);
					
		require('requires/mysqli_connect.php');		
		$query = "SELECT id, password FROM user WHERE user_name =?";
		$q=mysqli_stmt_init($dbcon);
		mysqli_stmt_prepare($q,$query);
		mysqli_stmt_bind_param($q,'s',$user_name);
		mysqli_stmt_execute($q);
		$result = mysqli_stmt_get_result($q);
		$row = mysqli_fetch_array($result, MYSQLI_NUM);
		
		if(mysqli_num_rows($result) == 1) {
			
			$password = SHA1($password);  // encrypt with SHA1 to do accurate comparison
			if ($password === $row[1]) {
				
				$user_id = $row[0];
				mysqli_close($dbcon);
				session_start();	
				$_SESSION['user_id'] =  $user_id;	
				$_SESSION['user'] =   $user_name;				
				// next to statements for later use once user_level implemented
				//$_SESSION['user_level']= intval($row[3]);
				//$url = ($_SESSION['user_level'] === 1) ? 'admin-page.php':'members-page.php'; 					
				header('location: main-menu.php');	
				exit();					
			}
			else {  //no password match. more secure to state message below, then to give this specific info
				
				$errors[]="The User Name/Password entered does not match our records.";
									
			}				
			
		}
		else {  // no user_name match was made
			
			$errors[]="The User Name/Password entered does not match our records.";
			
		}		
		
		if(!empty($errors)) {
			
			$errorstring = "Error! The following error(s) occurred: <br>";
			foreach ($errors as $msg) {
				$errorstring.= " -$msg<br>";
			}
			$errorstring.= "Please try again. <br>";
			// errorstring echoed from login.php file	
			
		}
				
		mysqli_close($dbcon);				
	}
	catch(Exception $e) {
		  
		  // "An Exception occurred. Message: " .$e->getMessage();
		  print "The system is busy. Please try again later";
		  
	}
	  
	catch (Error $e) {
	  
		  // "An Error occurred. Message: " .$e->getMessage();
		  print "The system is busy. Please try again later";
		  
	}  

?>