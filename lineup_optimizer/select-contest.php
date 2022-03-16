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
	<title>SELECT CONTEST</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet"  href="css/main.css">
	<script src="js/jquery.js"></script>		
</head>
	
<body>
	<div class="container mw-100 px-5 py-3">
			
		<!-- header section -->
		<?php include('includes/header.php'); ?>
		
		<!-- next row -->	
		<div class= "row mb-5">
			<nav class ="col-sm-2 pl-0">
				<ul class = "nav nav-pills flex-column text-dark" style="max-width:160px;">
					<?php include('includes/nav-select-contest.php'); ?>
				</ul>			
			</nav>
						
			<div class = "col-sm-8 pl-0">
					<h3 class="text-center font-bold mb-3" >Select Contest</h3>
					<div class = "row table-responsive  ml-0 mr-0  justify-content-center"> <!-- used to center table -->
					<?php
						try{
							date_default_timezone_set('America/Los_Angeles');
							require('requires/mysqli_connect.php');
							$query = "SELECT id, name, DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s') AS start_time ";
							$query.= "FROM contest WHERE (CURRENT_TIMESTAMP < date)";
							$result = mysqli_query($dbcon,$query);		
									
							if($result && (mysqli_num_rows($result) >= 1)) {
								
								echo'<table id="myTable" class="table-bordered  border-dark" style="margin: 0px auto;"> 
										<thead>
											<tr>
												<th class= "px-2 h5" scope = "col">Contest Groups</th>
												<th class= "px-2 h5" scope = "col">Style</th>
												<th class= "px-2 h5 style="width:50px;" " scope = "col">Starts In</th>	
												<th class= "px-2" scope = "col"></th>	
											</tr>									
										</thead>
									<tbody>';		

								$ct_array = array();	
								while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
											
										$ct = Date($row["start_time"]);
										$contest_time =  strtotime($ct) * 1000;	
										$ct_array[] = $contest_time;
										
										//var_dump($contest_time);
										
									echo'<tr class="h6">
											<td class= "px-2">'.$row["name"].'</td>
											<td class= "px-2">Classic</td>
											<td class= "px-2"></td>	
											<td class= "px-2 select-contest">
												<a href="create-lineup.php?contest_id=
													'.$row["id"].'">SELECT</a>
											</td>
										</tr>'; 
																			
								}
					?>	

					<script>
						$(document).ready(function(){
						
							var table = document.getElementById("myTable");
							var contest_time = <?php echo json_encode($ct_array); ?>;	
							
								// Update the count down every 1 second
								var x = setInterval(function() {
									
									for (var i = 1, row; row = table.rows[i]; i++) {
										
										// Set the date we're counting down to	
										
										var countDownDate = new Date(contest_time[i-1]).getTime();
											
										  // Get today's date and time			 
										  var now = new Date().getTime();
										  
										
										  // Find the distance between now and the count down date
										  var distance = countDownDate - now;
										  

										  // Time calculations for days, hours, minutes and seconds
										  //var days = Math.floor(distance / (1000 * 60 * 60 * 24));
										  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
										  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
										  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
										  
										  var timer_cell = row.cells[2];
										  // Display the result in the respective td element row & column
										  
										  if (seconds < 10)
											  seconds = "0" + seconds;
										  
										  if (minutes < 10)
											  minutes = "0" + minutes;
										  
										  if (hours < 10)
											  hours = "0" + hours;
										  
											  timer_cell.innerHTML = hours + "h:"
											  + minutes + "m:" + seconds + "s";
										  
										  var status_cell = row.cells[3];
											  											
										  // If the count down is finished, implement the following below
										  if (distance < 0) {
											clearInterval(x);
											
											timer_cell.innerHTML = "CONTEST STARTED";
											status_cell.innerHTML = "EXPIRED";
											status_cell.href = "#" ;
											status_cell.style.color = "red";
											status_cell.style.font.weight = "normal";
											setTimeout(function(){location.reload();}, 2000);
										  }
									}
								}, 1000);
							
						});
					</script>								
					<?php										
								echo' </tbody>
									  </table>'; 
								
								
							  
								mysqli_free_result($result);
							
							} else {							
									echo'<p class="text-center h4"> No Contests are available at this time.</p>';					
									include('includes/footer.php');											
									exit();
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