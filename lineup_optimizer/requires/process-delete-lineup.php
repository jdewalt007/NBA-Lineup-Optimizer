<?php
	if(empty($_SESSION['user'])) {		
		header('location: index.php');
		exit();		
	}
	
	try {
		$query = "DELETE FROM lineup WHERE id = ?";						 			 
		require('requires/mysqli_connect.php');	
		$q= mysqli_stmt_init($dbcon);
		mysqli_stmt_prepare($q,$query);
		mysqli_stmt_bind_param($q,'i', $_SESSION['lineup_id']); 
		mysqli_stmt_execute($q);
		if(mysqli_stmt_affected_rows($q) == 1) {
			
			echo'	
				<script>									
					var delete_alert = new CustomAlert();
					delete_alert.render("The lineup was successfully deleted."); 
				 </script> ';													 
			mysqli_close($dbcon);	
			exit();
		}
		else {
			
			echo'
			  <script>
				  var delete_alert2 = new CustomAlert();
				  var message1 = "The selected lineup was unable to be deleted.";
				  var message2 = "We apologize for any inconvenience.";
				  delete_alert2.render2(message1, message2);	
			  </script>';
			  mysqli_close($dbcon);	
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
?>