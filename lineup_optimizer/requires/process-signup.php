<?php
	
	
 // checks if form has been submitted and inserts the new account into users table
  $errors = array();
  
  $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
  if(empty($user_name))
	  $errors[] = "You forgot to enter your user name.";
  
  
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  if((empty($email)) || (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
	  $error_alert = "You forgot to enter your e-mail address";
	  $error_alert.= " or the e-mail format is incorrect.";
	  $errors[] = $error_alert;
	 
  }
  
  
  $password = trim($_POST['password']);
  
  $of_age = false;
  
  $birth_date = $_POST['birth_date'];
  
  date_default_timezone_set('America/Los_Angeles');
  
  $bday = new DateTime($birth_date);  
  $bday->add(new DateInterval("P18Y")); //adds time interval of 18 years to bday  
  //compare the added years to the current date  
  
  
  if($bday < new DateTime()){   
    $of_age = true;
  }
  else {  
	$errors[] = "You must be 18 or older to signup.";	
  }
  
  if (empty($errors)) {
	  
	  try{
		 // add a user to database 
		 $encrypted_password = SHA1($password);
		 require('requires/mysqli_connect.php');
	     $query = "INSERT INTO user(id, user_name, email, password, birth_date)";
		 $query.=" VALUES ('', ?, ?, ?, ?)";
		 $q= mysqli_stmt_init($dbcon);
		 mysqli_stmt_prepare($q,$query);
		 mysqli_stmt_bind_param($q, 'ssss', $user_name, $email, $encrypted_password, $birth_date);
		 
		 //execute query
		 mysqli_stmt_execute($q);
		 $result = mysqli_stmt_get_result($q);		 
		 if (mysqli_stmt_affected_rows($q) == 1) {	
			
			 mysqli_free_result($result);
			 $query = "SELECT LAST_INSERT_ID()";
			 $result = mysqli_query($dbcon, $query);
			 $row = mysqli_fetch_array($result,MYSQLI_NUM); 
			 $user_id = $row[0];
			 mysqli_close($dbcon);
			 session_start();			 
			 $_SESSION['user_id'] = $user_id;
			 $_SESSION['user']=  $user_name;
			 $_SESSION['message'] = "Your account has been successfully created. <br>" ;
			 $_SESSION['message'].= "Click on the main menu to start creating your lineups!";
			 header('location: new-account-message.php'); 
			 exit();
		 }
		 else {
			 
					 
			$errorstring = "The user_name you entered is already taken or a system error has occurred.";
			$errorstring.= " We apologize for any inconvenience. </p>";
			echo "<p class='text-center col-sm-8' style='color:red'>$errorstring</p>";
			
			$right_column = "<p class='col-sm-2 text-center' style='color:red'>"; // fill final 2 columns in row
			echo $right_column;
						
			mysqli_close($dbcon);
			
			include('includes/footer.php');
						
			exit();
					 
		 }
		 
	  }
	  catch(Exception $e) {
		  
		  // "An Exception occurred. Message: " .$e->getMessage();
		  print "The system is busy. Please try again later";
		  
	  }
	  
	  catch (Error $e) {
		  
		  // "An Error occurred. Message: " .$e->getMessage();
		  print "The system is busy. Please try again later";
		  
	  }  
  }
  else {
	    
	  $errorstring = "Error! The following error(s) occurred: <br>";
	  foreach ($errors as $msg) {
		  $errorstring.= " -$msg<br>";
	  }
	  // errorstring echoed from signup.php file 	  
  } 
  
?>