<!DOCTYPE html>
<html lang="en">
<head>
	<title>TOP SCORE HOMEPAGE</title>
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
		<div class= "row pb-4">
		
			<nav class ="col-sm-2 pl-0">
				<ul class = "nav nav-pills flex-column text-dark" style="max-width:160px;">
					<?php include('includes/nav-home.php'); ?>
				</ul>			
			</nav>
					
			<div class = "col-sm-8 pl-0 text-center">
				
				<img id="homepage-pic" src="images/nba-team-logos.png" >				
			</div>
												
			<div class ="col-sm-2">			
				<?php
				
					//$timezone = date_default_timezone_get();
					date_default_timezone_set('America/Los_Angeles');
					
				?>		
			</div>  <!-- last  column of row -->	
			
		</div>  <!-- end of row  -->
		
		<?php include('includes/footer.php'); ?>
		
		
		
		
		
		
		
				
	</div> <!-- end container -->
	
 
</body>
</html>