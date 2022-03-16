<?php
	session_start();
	if(empty($_SESSION['user'])) {		
		header('location: index.php');
		exit();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>SELECT LINEUP (EDIT)</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet"  href="css/main.css">
	<script src="js/jquery.js"></script>

	<style>
	
		.select-lineup a {	
			color: green;
			font-weight: normal;
			text-decoration:none;	
		}

		.select-lineup a:hover {	
			font-weight: bold;	
		}
	
	</style>


	
</head>
	
<body>
	<div class="container mw-100 px-5 py-3">
			
		<!-- header section -->
		<?php include('includes/header.php'); ?>
		
		<!-- next row -->	
		<div class= "row mb-5">
			<nav class ="col-sm-2 pl-0">
				<ul class = "nav nav-pills flex-column text-dark" style="max-width:160px;">
					<!-- reused below nav for this page  logout & main menu options   -->
					<?php include('includes/nav-select-contest.php'); ?>
				</ul>			
			</nav>
						
			<div class = "col-sm-8 pl-0">
					<h3 class="text-center font-bold mb-3" >Select Lineup - Edit</h3>
					<div class = "row table-responsive  ml-0 mr-0  justify-content-center"> <!-- used to center table -->
					<?php
						try{
							
							require('requires/mysqli_connect.php');
							$query = "SELECT c.id , DATE_FORMAT(c.date,'%e-%b-%y') AS c_date, 
									  DATE_FORMAT(c.date,'%l:%i%p') AS c_time, c.name, l.id  
									  FROM lineup l JOIN contest c ON l.contest_id = c.id		
									  WHERE l.user_id = ? AND (CURRENT_TIMESTAMP < c.date)
									  ORDER BY l.id DESC ";
									  									  
							$q = mysqli_stmt_init($dbcon);
							mysqli_stmt_prepare($q,$query);
							mysqli_stmt_bind_param($q, 'i', $_SESSION['user_id']); 
							mysqli_stmt_execute($q);
		 
							$result = mysqli_stmt_get_result($q);
									  
							if($result && (mysqli_num_rows($result) >= 1)) {
								
								echo'<table id="lineups" class="mt-3 table-bordered  border-dark" style="margin: 0px auto;"> 
										<thead>
											<tr class= "text-center">
												<th class= "px-2 h5" scope = "col">#</th>
												<th class= "px-2 h5" scope = "col">Contest Name</th>
												<th class= "px-2 h5" scope = "col">Date</th>
												<th class= "px-2" scope = "col"></th>	
											</tr>									
										</thead>
									<tbody>';		
								while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
											
										
									echo'<tr class="h6">
											<td class= "px-2 text-center">'.$row[4].'</td>
											<td class= "px-2">'.$row[3].'</td>
											<td class= "px-2">'.$row[1].'</td>
											<td class= "px-2 select-lineup">
												<a href="lineup-edit.php?lid=
													'.$row[4].'&cid='.$row[0].'">SELECT</a>
											</td>
										</tr>'; 
								}
								
								echo' </tbody>
									  </table>'; 
														  
								mysqli_close($dbcon);
							
							} else {							
									echo'<p class="text-center h4"> You currently have no lineups for contests not started</p>';					
									include('includes/footer.php');		
									mysqli_close($dbcon);
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
					?>				
					
					
					</div>																													
			</div> <!-- end of middle section of document -->
			
			
			<div class ="col-sm-2">
			
				<h5 class="text-left  font-bold mb-2"  style="color:darkblue">
					Member:&nbsp; <?php echo $_SESSION['user']; ?></h5>
											
			</div>  <!-- last  column of row -->		

		</div>  <!-- end of row  -->
		
		
		<?php include('includes/footer.php');?>
			
					
	</div> <!-- end container -->
	
	
	
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>   
</body>
</html>