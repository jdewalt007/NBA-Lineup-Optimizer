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
	<title>Account Created</title>
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
				<ul class = "nav nav-pills flex-column text-white" style="max-width:160px;">
					<?php include('includes/nav-new-account-message.php'); ?>
				</ul>			
			</nav>
						
			<div class = "col-sm-8 pl-0 ">
				<h6 class="text-center  font-bold text-dark mb-3">You are currently logged in as: &nbsp;
				<?php echo $_SESSION['user'] ?> </h5>
				<h3 class="text-center  font-bold text-dark mb-2"><?php echo $_SESSION['message'] ?></h3>
								
			</div> <!-- end of middle section of document -->
			
			<!-- fill space to right -->
			<div class ="col-sm-2">
				
						
			</div>  <!-- last  column of row -->	
			
		</div>  <!-- end of row  -->
		
		
		<?php include('includes/footer.php');?>
					
				
	</div> <!-- end container -->
	
	
	
	
	
	
	<script src="js/jquery.slim.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>   
</body>
</html>