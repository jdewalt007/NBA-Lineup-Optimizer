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
	<title>TOP SCORE Main Menu</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">
</head>
	
<body>
	<div class="container mw-100 px-5 py-3">
			
		<!-- header section -->
		<?php include('includes/header.php'); ?>
		
		<!-- next row -->	
		<div class= "row mb-5">
			<nav class ="col-sm-2 pl-0">
				<ul class = "nav nav-pills flex-column text-dark" style="max-width:160px;">
					<?php include('includes/nav-main-menu.php'); ?>
				</ul>			
			</nav>
						
			<div class = "col-sm-8 pl-0">
					<h3 class="text-center font-bold mb-3" style="color:black">Main Menu</h3>
					
					<ul class = "nav text-dark justify-content-center">
						<li class="nav-item  mb-2">
							<a class = "nav-link text-dark text-center border border-dark mb-1 h4" 
								style="background:linear-gradient(white, lightblue); "
								href="select-contest.php">Create Daily Lineup</a>
							
							<a class = "nav-link text-dark text-center border border-dark mb-1 h4"
								style="background:linear-gradient(white, lightblue); "
								href="select-lineup-view-delete.php">View/Delete Saved Lineup</a>
							
							<a class = "nav-link text-dark text-center border border-dark mb-1 h4" 
								style="background:linear-gradient(white, lightblue);"
								href="select-lineup-edit.php">Edit Saved Lineup</a>
						</li>						
					</ul>
					
				
				
			</div> <!-- end of middle section of document -->
			
			
			<div class ="col-sm-2">
					<h5 class="text-left  font-bold mb-2"  style="color:darkblue">
						Member:&nbsp; <?php echo $_SESSION['user']; ?></h5>
											
			</div>  <!-- last  column of row -->		

		</div>  <!-- end of row  -->
		
		
		<?php include('includes/footer.php');?>
			
					
	</div> <!-- end container -->
	
	
	<script src="js/jquery.slim.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>   
</body>
</html>