<?php
if(empty($_SESSION['user'])) {		
		header('location: index.php');
		exit();		
	}

	$user_id =    $_SESSION['user_id'];
	$contest_id = $_SESSION['contest_id'];
	$lineup_id  = $_SESSION['lineup_id'];
	
	//sal_post_avg only needed for placement in page table after submit
	//will always be $0 after a lineup is completed
	$sal_remain = $_POST['sal_post_remain'];
	$fpts_total = $_POST['fpts_post_total'];
	
	$pg_id =      $_POST['pg_post_id'];
	$pg_name =    $_POST['pg_post_name'];
	$pg_opp =     $_POST['pg_post_opp'];
	$pg_fppg =    $_POST['pg_post_fppg'];
	$pg_salary =  $_POST['pg_post_salary'];
	
	$sg_id =      $_POST['sg_post_id'];
	$sg_name =    $_POST['sg_post_name'];
	$sg_opp =     $_POST['sg_post_opp'];
	$sg_fppg =    $_POST['sg_post_fppg'];
	$sg_salary =  $_POST['sg_post_salary'];
	
	$sf_id =      $_POST['sf_post_id'];
	$sf_name =    $_POST['sf_post_name'];
	$sf_opp =     $_POST['sf_post_opp'];
	$sf_fppg =    $_POST['sf_post_fppg'];
	$sf_salary =  $_POST['sf_post_salary'];
	
	$pf_id =      $_POST['pf_post_id'];
	$pf_name =    $_POST['pf_post_name'];
	$pf_opp =     $_POST['pf_post_opp'];
	$pf_fppg =    $_POST['pf_post_fppg'];
	$pf_salary =  $_POST['pf_post_salary'];
	
	$c_id =      $_POST['c_post_id'];
	$c_name =    $_POST['c_post_name'];
	$c_opp =     $_POST['c_post_opp'];
	$c_fppg =    $_POST['c_post_fppg'];
	$c_salary =  $_POST['c_post_salary'];
	
	$g_id =      $_POST['g_post_id'];
	$g_name =    $_POST['g_post_name'];
	$g_opp =     $_POST['g_post_opp'];
	$g_fppg =    $_POST['g_post_fppg'];
	$g_salary =  $_POST['g_post_salary'];
	
	$f_id =      $_POST['f_post_id'];
	$f_name =    $_POST['f_post_name'];
	$f_opp =     $_POST['f_post_opp'];
	$f_fppg =    $_POST['f_post_fppg'];
	$f_salary =  $_POST['f_post_salary'];
	
	$util_id =      $_POST['util_post_id'];
	$util_name =    $_POST['util_post_name'];
	$util_opp =     $_POST['util_post_opp'];
	$util_fppg =    $_POST['util_post_fppg'];
	$util_salary =  $_POST['util_post_salary'];
	
	
	
	try {
		
		//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
		require('requires/mysqli_connect.php');
		$query = "UPDATE lineup SET remain_salary = ?, fpts_total = ? WHERE id = ?";			
		$q= mysqli_stmt_init($dbcon);
		mysqli_stmt_prepare($q,$query);
		mysqli_stmt_bind_param($q,'sdi',$sal_remain, $fpts_total, $lineup_id);
		mysqli_stmt_execute($q);
		if(mysqli_stmt_affected_rows($q) == 1) {
			
			$query = "INSERT INTO lineup_position (lineup_id, p_position, p_id, p_name, opponent, p_fppg, p_salary)
			            VALUES (?,'PG',?,?,?,?,?), (?,'SG',?,?,?,?,?), (?,'SF',?,?,?,?,?), 
						(?,'PF',?,?,?,?,?), (?,'C',?,?,?,?,?), (?,'G',?,?,?,?,?), (?,'F',?,?,?,?,?), 
						(?,'UTIL',?,?,?,?,?)
											
						ON DUPLICATE KEY UPDATE lineup_id = VALUES(lineup_id), p_position = VALUES(p_position),
						p_id = VALUES(p_id), p_name = VALUES(p_name), opponent = VALUES(opponent), 
						p_fppg = VALUES(p_fppg), p_salary = VALUES(p_salary)";
						
			mysqli_stmt_prepare($q,$query);
			mysqli_stmt_bind_param($q,'iissssiissssiissssiissssiissssiissssiissssiissss',
									$lineup_id, $pg_id, $pg_name, $pg_opp, $pg_fppg, $pg_salary,
									$lineup_id, $sg_id, $sg_name, $sg_opp, $sg_fppg, $sg_salary,
									$lineup_id, $sf_id, $sf_name, $sf_opp, $sf_fppg, $sf_salary,
									$lineup_id, $pf_id, $pf_name, $pf_opp, $pf_fppg, $pf_salary,
									$lineup_id, $c_id, $c_name, $c_opp, $c_fppg, $c_salary,
									$lineup_id, $g_id, $g_name, $g_opp, $g_fppg, $g_salary,
									$lineup_id, $f_id, $f_name, $f_opp, $f_fppg, $f_salary,
									$lineup_id, $util_id, $util_name, $util_opp, $util_fppg, $util_salary);	
									
			mysqli_stmt_execute($q);  
			$affected = mysqli_stmt_affected_rows($q);
			echo'<script>console.log('.$affected.');    </script>';
			if (mysqli_stmt_affected_rows($q) >= 1) { 
			  
			  echo ' <script>
						var lineup_alert = new CustomAlert();
						lineup_alert.render("Lineup updated successfully.");	
					 </script> ';	
					 mysqli_close($dbcon);	
					 include('includes/footer.php');
					 exit();
					 
			} else {
				 echo ' <script>
							var lineup_alert = new CustomAlert();
							lineup_alert.render("Lineup was not updated. Please try again.");	
						 </script> ';			 
			}						

		} else {
			
			 echo ' <script>
					var cust_alert = new CustomAlert();
					cust_alert.render("The system is busy. Please try updating your lineup again later.");	
				</script>     ';			
			mysqli_close($dbcon);			
		}
	}
	catch(Exception $e) {
				  
		 // echo "An Exception occurred. Message: " .$e->getMessage();
		 // print "The system is busy. Please try again later";
		   echo ' <script>
					var cust_alert = new CustomAlert();
					cust_alert.render("The system is busy. Please try updating your lineup again later.");	
				</script>     ';	
  
	}
	catch (Error $e) {
	  
		  //echo"An Error occurred. Message: " .$e->getMessage();
		  //print "The system is busy. Please try again later";
		  echo ' <script>
					var cust_alert = new CustomAlert();
					cust_alert.render("The system is busy. Please try updating your lineup again later.");	
				</script>     ';		  
	}  							
















	
?>